<?php
require_once '../../auth.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    // Check not deleting self
    if ($id === (int)$_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete your own admin account!";
    } else {
        $res = dbQuery("DELETE FROM users WHERE id = ?", [$id]);
        if ($res) {
            $_SESSION['success'] = "User deleted successfully";
        } else {
            $_SESSION['error'] = "Error deleting user account";
        }
    }
}
header("Location: manage-users.php");
exit();
?>
