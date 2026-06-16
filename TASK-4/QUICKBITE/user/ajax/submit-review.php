<?php
// ================================================
// QUICKBITE — AJAX: Submit Food Review
// POST: food_id, rating, title, comment, csrf_token
// ================================================
session_start();
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../includes/security.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Please log in to leave a review.']);
    exit;
}

// CSRF check
if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid request token.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$food_id = (int)($_POST['food_id'] ?? 0);
$rating  = (int)($_POST['rating']  ?? 0);
$title   = sanitize_string($_POST['title']   ?? '');
$comment = sanitize_string($_POST['comment'] ?? '');

// Validate
if ($food_id <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'error' => 'Invalid rating or food.']);
    exit;
}

// Check if food exists
$food = db_fetch($conn, "SELECT id, food_name FROM foods WHERE id = ?", 'i', [$food_id]);
if (!$food) {
    echo json_encode(['success' => false, 'error' => 'Food item not found.']);
    exit;
}

// Check if user has ordered this food (verified badge)
$ordered = db_fetch(
    $conn,
    "SELECT o.id FROM orders o
     JOIN order_items oi ON oi.order_id = o.id
     WHERE o.user_id = ? AND oi.food_id = ? AND o.order_status = 'Delivered'
     LIMIT 1",
    'ii', [$user_id, $food_id]
);
// Fallback: check old flat orders schema
if (!$ordered) {
    $ordered = db_fetch(
        $conn,
        "SELECT id FROM orders WHERE user_id = ? AND food_id = ? AND order_status = 'Delivered' LIMIT 1",
        'ii', [$user_id, $food_id]
    );
}
$is_verified = $ordered ? 1 : 0;

// Upsert review (one review per user per food)
$existing = db_fetch(
    $conn,
    "SELECT id FROM reviews WHERE user_id = ? AND food_id = ?",
    'ii', [$user_id, $food_id]
);

if ($existing) {
    // Update existing review
    $stmt = $conn->prepare(
        "UPDATE reviews SET rating=?, title=?, comment=?, is_verified=?, created_at=NOW() WHERE id=?"
    );
    $stmt->bind_param('issii', $rating, $title, $comment, $is_verified, $existing['id']);
    $ok = $stmt->execute();
    $stmt->close();
} else {
    // Insert new review
    $stmt = $conn->prepare(
        "INSERT INTO reviews (user_id, food_id, rating, title, comment, is_verified) VALUES (?,?,?,?,?,?)"
    );
    $stmt->bind_param('iiissi', $user_id, $food_id, $rating, $title, $comment, $is_verified);
    $ok = $stmt->execute();
    $stmt->close();
}

if (!$ok) {
    echo json_encode(['success' => false, 'error' => 'Could not save review. Please try again.']);
    exit;
}

// Recalculate food average rating
$avg_row = db_fetch($conn, "SELECT AVG(rating) as avg_r, COUNT(*) as cnt FROM reviews WHERE food_id = ?", 'i', [$food_id]);
$new_avg  = round((float)($avg_row['avg_r'] ?? $rating), 1);
$cnt      = (int)($avg_row['cnt'] ?? 1);

$conn->query("UPDATE foods SET rating = $new_avg WHERE id = $food_id");

echo json_encode([
    'success'    => true,
    'message'    => $existing ? 'Review updated!' : 'Review posted!',
    'new_avg'    => $new_avg,
    'count'      => $cnt,
    'verified'   => (bool)$is_verified,
    'user_name'  => $_SESSION['user_name'] ?? 'You',
    'rating'     => $rating,
    'title'      => htmlspecialchars($title),
    'comment'    => htmlspecialchars($comment),
    'created_at' => date('d M Y'),
]);
