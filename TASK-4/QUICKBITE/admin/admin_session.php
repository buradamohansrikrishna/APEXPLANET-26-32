<?php
// ================================================
// ADMIN SESSION GUARD
// ================================================

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict']);
    session_start();
}

// Timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 7200) {
    session_unset(); session_destroy();
    header('Location: ../auth/login.php?msg=timeout'); exit();
}
$_SESSION['last_activity'] = time();

// Must be logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login.php'); exit();
}
