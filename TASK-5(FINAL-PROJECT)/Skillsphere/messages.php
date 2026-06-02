<?php
require_once 'auth.php';
$role = $_SESSION['user_role'] ?? 'student';
if ($role === 'instructor') {
    header("Location: instructor/messages.php");
} else {
    header("Location: student/dashboard.php");
}
exit();
?>
