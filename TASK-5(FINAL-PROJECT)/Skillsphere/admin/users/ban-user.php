<?php
require_once '../../auth.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($id > 0) {
    if ($id === (int)$_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot ban your own admin account!";
    } else {
        $status = $action === 'unban' ? 'active' : 'blocked';
        $res = dbQuery("UPDATE users SET status = ? WHERE id = ?", [$status, $id]);
        if ($res) {
            $_SESSION['success'] = "User account has been " . ($action === 'unban' ? 'unbanned' : 'blocked');
        } else {
            $_SESSION['error'] = "Failed to update user status";
        }
    }
}
header("Location: manage-users.php");
exit();
?>
