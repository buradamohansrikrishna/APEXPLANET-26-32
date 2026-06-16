<?php
// ================================================
// QUICKBITE — AJAX: Get Reviews for a Food Item
// GET: food_id, page (optional, default 1)
// ================================================
session_start();
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../includes/security.php';

$food_id = (int)($_GET['food_id'] ?? 0);
$page    = max(1, (int)($_GET['page'] ?? 1));
$per_page = 5;
$offset   = ($page - 1) * $per_page;

if ($food_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid food ID.']);
    exit;
}

// Total count
$total = db_count($conn, 'reviews', 'food_id = ?', 'i', [$food_id]);

// Fetch reviews with user name
$reviews_raw = db_fetch_all(
    $conn,
    "SELECT r.id, r.rating, r.title, r.comment, r.is_verified, r.created_at,
            u.name AS user_name
     FROM reviews r
     JOIN users u ON r.user_id = u.id
     WHERE r.food_id = ?
     ORDER BY r.created_at DESC
     LIMIT ? OFFSET ?",
    'iii', [$food_id, $per_page, $offset]
);

// Rating breakdown
$breakdown_raw = db_fetch_all(
    $conn,
    "SELECT rating, COUNT(*) AS cnt FROM reviews WHERE food_id = ? GROUP BY rating ORDER BY rating DESC",
    'i', [$food_id]
);

$breakdown = array_fill(1, 5, 0);
foreach ($breakdown_raw as $b) {
    $breakdown[(int)$b['rating']] = (int)$b['cnt'];
}

// Average
$avg_row = db_fetch($conn, "SELECT AVG(rating) as avg_r FROM reviews WHERE food_id = ?", 'i', [$food_id]);
$avg = round((float)($avg_row['avg_r'] ?? 0), 1);

// Current user's review (if logged in)
$my_review = null;
if (isset($_SESSION['user_id'])) {
    $my_review = db_fetch(
        $conn,
        "SELECT * FROM reviews WHERE user_id = ? AND food_id = ?",
        'ii', [$_SESSION['user_id'], $food_id]
    );
}

// Format reviews
$reviews = array_map(function($r) {
    $initials = strtoupper(substr($r['user_name'] ?? 'U', 0, 1));
    $name_parts = explode(' ', trim($r['user_name'] ?? ''));
    $display = count($name_parts) > 1
        ? $name_parts[0] . ' ' . strtoupper(substr($name_parts[1], 0, 1)) . '.'
        : $r['user_name'];
    return [
        'id'          => $r['id'],
        'user_name'   => htmlspecialchars($display),
        'initials'    => $initials,
        'rating'      => (int)$r['rating'],
        'title'       => htmlspecialchars($r['title'] ?? ''),
        'comment'     => htmlspecialchars($r['comment'] ?? ''),
        'is_verified' => (bool)$r['is_verified'],
        'created_at'  => date('d M Y', strtotime($r['created_at'])),
    ];
}, $reviews_raw);

echo json_encode([
    'success'    => true,
    'reviews'    => $reviews,
    'total'      => $total,
    'pages'      => ceil($total / $per_page),
    'page'       => $page,
    'avg_rating' => $avg,
    'breakdown'  => $breakdown,
    'my_review'  => $my_review ? [
        'rating'  => (int)$my_review['rating'],
        'title'   => htmlspecialchars($my_review['title'] ?? ''),
        'comment' => htmlspecialchars($my_review['comment'] ?? ''),
    ] : null,
]);
