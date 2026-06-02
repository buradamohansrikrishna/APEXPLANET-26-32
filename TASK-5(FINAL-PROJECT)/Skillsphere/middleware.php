<?php
// =========================================
// SKILLSPHERE MIDDLEWARE
// middleware.php
// =========================================

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/csrf.php';

if (!function_exists('requireLogin')) {
    function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please log in to access this page";
            header("Location: " . BASE_URL . "login.php");
            exit();
        }
    }
}

if (!function_exists('requireRole')) {
    function requireRole($role) {
        requireLogin();
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
            $_SESSION['error'] = "Unauthorized access";
            header("Location: " . BASE_URL . "index.php");
            exit();
        }
    }
}

if (!function_exists('requireAdmin')) {
    function requireAdmin() {
        requireRole('admin');
    }
}

if (!function_exists('requireInstructor')) {
    function requireInstructor() {
        requireRole('instructor');
    }
}

if (!function_exists('apiCsrfCheck')) {
    function apiCsrfCheck() {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!verifyCsrfToken($token)) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'CSRF verification failed']);
            exit();
        }
    }
}
?>
