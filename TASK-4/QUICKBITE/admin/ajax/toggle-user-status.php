<?php
// Admin AJAX: Toggle user ban status
require_once '../../admin/admin_session.php';
require_once '../../config/db.php';
require_once '../../includes/security.php';
header('Content-Type: application/json');

if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid token.']); exit;
}

$user_id   = (int)($_POST['user_id'] ?? 0);
$new_status = sanitize_string($_POST['status'] ?? '');
if (!in_array($new_status, ['active','banned']) || $user_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid data.']); exit;
}

$stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
$stmt->bind_param('si', $new_status, $user_id);
$ok = $stmt->execute();
$stmt->close();
echo json_encode(['success' => $ok]);
