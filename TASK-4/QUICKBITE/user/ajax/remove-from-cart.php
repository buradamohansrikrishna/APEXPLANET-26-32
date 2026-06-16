<?php
// ================================================
// QUICKBITE — AJAX: Remove from Cart
// POST: cart_id, csrf_token
// ================================================
session_start();
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../includes/security.php';
require_once '../../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Login required.']);
    exit;
}

if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$cart_id = (int)($_POST['cart_id'] ?? 0);

if ($cart_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid cart item.']);
    exit;
}

// Check if item belongs to user
$stmt = $conn->prepare("SELECT id FROM cart WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$item) {
    echo json_encode(['success' => false, 'error' => 'Cart item not found.']);
    exit;
}

// Delete item
$stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$stmt->close();

// Recalculate totals
$cart_count = get_cart_count($user_id, $conn);
$subtotal = get_cart_total($user_id, $conn);
$delivery_fee = $subtotal > 0 ? 40.00 : 0;
$tax_rate = 0.05;
$tax = $subtotal * $tax_rate;
$discount = 0;

if (!empty($_SESSION['applied_coupon'])) {
    $code = $_SESSION['applied_coupon']['code'] ?? '';
    // Validate coupon against new subtotal
    $val = validate_coupon($conn, $code, $subtotal);
    if ($val['valid']) {
        $_SESSION['applied_coupon']['discount'] = $val['discount'];
        $discount = $val['discount'];
    } else {
        // Remove coupon if no longer valid
        unset($_SESSION['applied_coupon']);
    }
}

$total = $subtotal + $delivery_fee + $tax - $discount;

echo json_encode([
    'success' => true,
    'cart_count' => $cart_count,
    'subtotal' => $subtotal,
    'delivery_fee' => $delivery_fee,
    'tax' => $tax,
    'discount' => $discount,
    'total' => $total
]);
