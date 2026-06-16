<?php
// ================================================
// QUICKBITE — AJAX: Apply Coupon
// POST: code, subtotal, csrf_token
// ================================================
session_start();
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../includes/security.php';
require_once '../../includes/functions.php';

if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['valid' => false, 'error' => 'Invalid request.']);
    exit;
}

$code     = strtoupper(sanitize_string($_POST['code']     ?? ''));
$subtotal = (float)($_POST['subtotal'] ?? 0);

if (!$code) {
    echo json_encode(['valid' => false, 'error' => 'Please enter a coupon code.']);
    exit;
}

$result = validate_coupon($conn, $code, $subtotal);

if ($result['valid']) {
    // Save to session
    $_SESSION['applied_coupon'] = [
        'code'     => $code,
        'discount' => $result['discount'],
    ];

    $coupon = $result['coupon'];
    $desc   = $coupon['description'] ?? '';
    echo json_encode([
        'valid'    => true,
        'message'  => "🎉 {$desc} — You save ₹" . number_format($result['discount'], 2) . "!",
        'discount' => $result['discount'],
        'code'     => $code,
    ]);
} else {
    unset($_SESSION['applied_coupon']);
    echo json_encode([
        'valid' => false,
        'error' => $result['error'],
    ]);
}
