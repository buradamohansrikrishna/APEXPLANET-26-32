<?php
// ================================================
// QUICKBITE — AJAX: Get Order Status (Polling)
// GET: order_id
// ================================================
session_start();
header('Content-Type: application/json');

require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$order_id = (int)($_GET['order_id'] ?? 0);
$user_id  = (int)$_SESSION['user_id'];

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid order']);
    exit;
}

// Fetch order — must belong to this user
$stmt = $conn->prepare(
    "SELECT o.id, o.order_number, o.order_status, o.total_price, o.delivery_fee,
            o.tax, o.discount, o.payment_method, o.delivery_address,
            o.estimated_delivery, o.created_at, o.coupon_code,
            p.status as pay_status, p.transaction_id
     FROM orders o
     LEFT JOIN payments p ON p.order_id = o.id
     WHERE o.id = ? AND o.user_id = ?
     LIMIT 1"
);
$stmt->bind_param('ii', $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    echo json_encode(['success' => false, 'error' => 'Order not found']);
    exit;
}

// Map status to step index (0-based, 0-3)
$status_steps = [
    'Pending'          => 0,
    'Accepted'         => 1,
    'Preparing'        => 1,
    'Ready'            => 2,
    'Out For Delivery' => 2,
    'Delivered'        => 3,
    'Cancelled'        => -1,
];
$step = $status_steps[$order['order_status']] ?? 0;

// Fetch order items
$items_stmt = $conn->prepare(
    "SELECT oi.quantity, oi.unit_price, f.food_name, f.image
     FROM order_items oi
     JOIN foods f ON oi.food_id = f.id
     WHERE oi.order_id = ?"
);
$items_stmt->bind_param('i', $order_id);
$items_stmt->execute();
$items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$items_stmt->close();

// Format items
$items = array_map(fn($i) => [
    'food_name'  => htmlspecialchars($i['food_name']),
    'quantity'   => (int)$i['quantity'],
    'unit_price' => (float)$i['unit_price'],
    'image'      => htmlspecialchars($i['image'] ?? ''),
], $items);

echo json_encode([
    'success'      => true,
    'order_number' => $order['order_number'],
    'status'       => $order['order_status'],
    'step'         => $step,
    'total_price'  => number_format($order['total_price'], 2),
    'delivery_fee' => number_format($order['delivery_fee'] ?? 40, 2),
    'tax'          => number_format($order['tax'] ?? 0, 2),
    'discount'     => number_format($order['discount'] ?? 0, 2),
    'payment_method' => $order['payment_method'],
    'pay_status'   => $order['pay_status'] ?? 'pending',
    'txn_id'       => $order['transaction_id'] ?? '',
    'address'      => htmlspecialchars($order['delivery_address'] ?? ''),
    'est_delivery' => $order['estimated_delivery'] ? date('h:i A', strtotime($order['estimated_delivery'])) : '--',
    'created_at'   => date('d M Y, h:i A', strtotime($order['created_at'])),
    'items'        => $items,
    'is_cancelled' => $order['order_status'] === 'Cancelled',
]);
