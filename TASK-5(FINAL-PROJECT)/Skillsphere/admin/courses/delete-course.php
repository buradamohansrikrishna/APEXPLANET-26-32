<?php
require_once '../../auth.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $res = dbQuery("DELETE FROM courses WHERE id = ?", [$id]);
    if ($res) {
        $_SESSION['success'] = "Course deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting course";
    }
}
header("Location: manage-courses.php");
exit();
?>
