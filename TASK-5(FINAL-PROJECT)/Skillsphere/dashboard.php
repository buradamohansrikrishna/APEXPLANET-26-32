<?php
require_once 'auth.php';
require_once 'middleware.php';

// Force authentication
requireLogin();

$role = $_SESSION['user_role'] ?? 'student';

if ($role === 'admin') {
    header("Location: admin/dashboard.php");
} elseif ($role === 'instructor') {
    header("Location: instructor/dashboard.php");
} else {
    header("Location: student/dashboard.php");
}
exit();
?>
