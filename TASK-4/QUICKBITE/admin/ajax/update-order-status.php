<?php
// ================================================
// QUICKBITE — ADMIN AJAX: Update Order Status
// POST: order_id, status, csrf_token
// ================================================
require_once '../../admin/admin_session.php';
require_once '../../config/db.php';
require_once '../../includes/security.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid token.']);
    exit;
}

$order_id  = (int)($_POST['order_id'] ?? 0);
$new_status = sanitize_string($_POST['status'] ?? '');

$allowed = ['Pending','Accepted','Preparing','Ready','Out For Delivery','Delivered','Cancelled'];
if (!in_array($new_status, $allowed) || $order_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid data.']);
    exit;
}

// Get order + user_id for notification
$order = db_fetch($conn, "SELECT id, user_id, order_number FROM orders WHERE id = ?", 'i', [$order_id]);
if (!$order) {
    echo json_encode(['success' => false, 'error' => 'Order not found.']);
    exit;
}

// Update status
$stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
$stmt->bind_param('si', $new_status, $order_id);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
    echo json_encode(['success' => false, 'error' => 'DB error.']);
    exit;
}

// If delivered, update payment status
if ($new_status === 'Delivered') {
    $conn->query("UPDATE payments SET status='completed', paid_at=NOW() WHERE order_id=$order_id AND method='COD' AND status='pending'");
}

// Notify user
$icons = get_order_status_icon($new_status);
add_notification($conn, $order['user_id'],
    "$icons Order #{$order['order_number']} — $new_status",
    "Your order status has been updated to: $new_status.",
    'order'
);

// Update admin last_login
$conn->query("UPDATE admins SET last_login=NOW() WHERE id=" . (int)$_SESSION['admin_id']);

echo json_encode(['success' => true, 'new_status' => $new_status, 'order_id' => $order_id]);
