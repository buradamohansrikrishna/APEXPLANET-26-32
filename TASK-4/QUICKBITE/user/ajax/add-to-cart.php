<?php
// ================================================
// QUICKBITE — AJAX: Add to Cart
// POST: food_id, quantity, csrf_token
// ================================================
session_start();
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../includes/security.php';
require_once '../../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'redirect' => '../auth/login.php']);
    exit;
}

if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$food_id = (int)($_POST['food_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);

if ($food_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid food item or quantity.']);
    exit;
}

// Check if food exists
$stmt = $conn->prepare("SELECT food_name FROM foods WHERE id = ?");
$stmt->bind_param("i", $food_id);
$stmt->execute();
$food = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$food) {
    echo json_encode(['success' => false, 'message' => 'Food item not found.']);
    exit;
}

// Check if already in cart
$stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND food_id = ? AND saved_for_later = 0");
$stmt->bind_param("ii", $user_id, $food_id);
$stmt->execute();
$cart_item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($cart_item) {
    $new_qty = $cart_item['quantity'] + $quantity;
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_qty, $cart_item['id']);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare("INSERT INTO cart (user_id, food_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $food_id, $quantity);
    $stmt->execute();
    $stmt->close();
}

$cart_count = get_cart_count($user_id, $conn);

echo json_encode([
    'success' => true,
    'cart_count' => $cart_count,
    'food_name' => $food['food_name']
]);
