<?php
// ajax/rate-app.php
require_once '../../config/db.php';
require_once '../../includes/security.php';

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid request token']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $rating  = (int)($_POST['rating'] ?? 0);
    $comment = sanitize_string($_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Please select a valid rating between 1 and 5']);
        exit;
    }

    // Check if user already rated
    $stmt = $conn->prepare("SELECT id FROM app_reviews WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existing) {
        // Update existing rating
        $stmt = $conn->prepare("UPDATE app_reviews SET rating = ?, comment = ?, created_at = NOW() WHERE user_id = ?");
        $stmt->bind_param("isi", $rating, $comment, $user_id);
    } else {
        // Insert new rating
        $stmt = $conn->prepare("INSERT INTO app_reviews (user_id, rating, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $rating, $comment);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Thank you for rating QuickBite!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save rating.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
}
