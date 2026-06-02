<?php
session_start();

// Destroy all session data
$_SESSION = [];

// Destroy the session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

// Clear any remember-me cookie
setcookie('remember_token', '', time() - 3600, '/');

// Redirect to homepage
header('Location: ../index.php');
exit();