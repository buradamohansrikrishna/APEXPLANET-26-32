<?php
// ================================================
// QUICKBITE — AJAX: Toggle Favorite Food
// POST: food_id, csrf_token
// ================================================
session_start();
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../includes/security.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Login required.']);
    exit;
}

if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$food_id = (int)($_POST['food_id'] ?? 0);

if ($food_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid food.']);
    exit;
}

// Check if already favorited
$existing = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND food_id = ?");
$existing->bind_param('ii', $user_id, $food_id);
$existing->execute();
$row = $existing->get_result()->fetch_assoc();
$existing->close();

if ($row) {
    // Remove favorite
    $del = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND food_id = ?");
    $del->bind_param('ii', $user_id, $food_id);
    $del->execute();
    $del->close();
    echo json_encode(['success' => true, 'is_favorite' => false, 'message' => 'Removed from favorites']);
} else {
    // Add favorite
    $ins = $conn->prepare("INSERT INTO favorites (user_id, food_id) VALUES (?, ?)");
    $ins->bind_param('ii', $user_id, $food_id);
    $ins->execute();
    $ins->close();
    echo json_encode(['success' => true, 'is_favorite' => true, 'message' => 'Added to favorites']);
}
