<?php
// =========================================
// SKILLSPHERE SECURE SESSION MANAGEMENT
// session.php
// =========================================

// Configure secure session settings before starting session
if (session_status() === PHP_SESSION_NONE) {
    // 30-minute session duration
    $lifetime = 1800; 
    
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_trans_sid', 0);
    ini_set('session.cookie_httponly', 1);
    
    // Check if HTTPS is used
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    
    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    session_start();
}

// Session expiration check
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    // Session expired
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['created_time'])) {
    $_SESSION['created_time'] = time();
} elseif (time() - $_SESSION['created_time'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created_time'] = time();
}
?>
