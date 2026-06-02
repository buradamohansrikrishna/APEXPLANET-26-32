<?php
// ================================================
// QUICKBITE 2.0 — SECURE SESSION MANAGER
// ================================================

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 7200,
        'path'     => '/',
        'secure'   => false, // set true in production with HTTPS
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

// Session timeout (2 hours idle)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 7200) {
    session_unset();
    session_destroy();
    header('Location: ' . (strpos($_SERVER['PHP_SELF'], 'admin') !== false ? '../auth/login.php' : '../auth/login.php'));
    exit();
}
$_SESSION['last_activity'] = time();

// User auth check
if (!isset($_SESSION['user_id'])) {
    $redirect = strpos($_SERVER['PHP_SELF'], 'user/') !== false ? '../auth/login.php' : '../../auth/login.php';
    header('Location: ' . $redirect);
    exit();
}