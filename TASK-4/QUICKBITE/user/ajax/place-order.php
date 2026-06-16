<?php
// ================================================
// QUICKBITE — AJAX: Place Order (Secure)
// POST: delivery_address, payment_method, csrf_token
// ================================================
session_start();
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../includes/security.php';
require_once '../../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Please log in to place an order.']);
    exit;
}

if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit;
}

$user_id         = (int)$_SESSION['user_id'];
$delivery_address = sanitize_string($_POST['delivery_address'] ?? '');
$payment_method  = sanitize_string($_POST['payment_method'] ?? 'COD');
$coupon_code     = sanitize_string($_POST['coupon_code'] ?? '');

if (empty($delivery_address)) {
    echo json_encode(['success' => false, 'error' => 'Delivery address is required.']);
    exit;
}

$allowed_methods = ['COD', 'UPI', 'Card', 'Wallet', 'Netbanking'];
if (!in_array($payment_method, $allowed_methods)) {
    $payment_method = 'COD';
}

// Fetch cart from DB
$cart_items = get_cart_items($user_id, $conn);
if (empty($cart_items)) {
    echo json_encode(['success' => false, 'error' => 'Your cart is empty.']);
    exit;
}

// Calculate totals
$subtotal     = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart_items));
$delivery_fee = 40.00;
$tax          = round($subtotal * 0.05, 2);
$discount     = 0.0;

// Validate coupon
if (!empty($coupon_code)) {
    $coupon_result = validate_coupon($conn, $coupon_code, $subtotal);
    if ($coupon_result['valid']) {
        $discount = $coupon_result['discount'];
        // Increment coupon usage
        $conn->query("UPDATE coupons SET used_count = used_count + 1 WHERE code = '" . $conn->real_escape_string($coupon_code) . "'");
    }
}

$grand_total = $subtotal + $delivery_fee + $tax - $discount;
$order_number = generate_order_number();

// Begin transaction
$conn->begin_transaction();
try {
    // 1. Insert into orders
    $stmt = $conn->prepare(
        "INSERT INTO orders (user_id, total_price, order_status, delivery_fee, tax, coupon_code, discount,
                             delivery_address, payment_method, order_number, estimated_delivery, created_at)
         VALUES (?, ?, 'Pending', ?, ?, ?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 35 MINUTE), NOW())"
    );
    $stmt->bind_param(
        'idsdsssss',
        $user_id, $grand_total, $delivery_fee, $tax,
        $coupon_code, $discount, $delivery_address, $payment_method, $order_number
    );
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();

    // 2. Insert order_items
    $item_stmt = $conn->prepare(
        "INSERT INTO order_items (order_id, food_id, quantity, unit_price) VALUES (?,?,?,?)"
    );
    foreach ($cart_items as $item) {
        $fid = (int)$item['food_id'];
        $qty = (int)$item['quantity'];
        $price = (float)$item['price'];
        $item_stmt->bind_param('iiid', $order_id, $fid, $qty, $price);
        $item_stmt->execute();

        // Increment food total_orders
        $conn->query("UPDATE foods SET total_orders = total_orders + $qty WHERE id = $fid");
    }
    $item_stmt->close();

    // 3. Record payment
    $pay_status = ($payment_method === 'COD') ? 'pending' : 'completed';
    $txn_id     = ($payment_method !== 'COD') ? 'SIM_' . strtoupper(bin2hex(random_bytes(6))) : null;

    $ps = $conn->prepare(
        "INSERT INTO payments (order_id, user_id, amount, method, status, transaction_id, paid_at)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $paid_at = ($payment_method !== 'COD') ? date('Y-m-d H:i:s') : null;
    $ps->bind_param('iidssss', $order_id, $user_id, $grand_total, $payment_method, $pay_status, $txn_id, $paid_at);
    $ps->execute();
    $ps->close();

    // 4. Clear cart
    $del = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $del->bind_param('i', $user_id);
    $del->execute();
    $del->close();

    // 5. Add notification
    add_notification($conn, $user_id, '🎉 Order Placed!',
        "Your order #{$order_number} has been placed successfully. Estimated delivery: 35 minutes.", 'order');

    // Clear applied coupon from session
    unset($_SESSION['applied_coupon']);

    $conn->commit();

    echo json_encode([
        'success'      => true,
        'order_id'     => $order_id,
        'order_number' => $order_number,
        'grand_total'  => number_format($grand_total, 2),
        'payment_method' => $payment_method,
        'txn_id'       => $txn_id,
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => 'Order failed. Please try again.']);
}
