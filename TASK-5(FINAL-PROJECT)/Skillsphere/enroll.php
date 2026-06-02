<?php
require_once 'db.php';
require_once 'session.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (!in_array($id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $id;
    }
    $_SESSION['success'] = "Course added to cart!";
}
header("Location: cart.php");
exit();
?>
