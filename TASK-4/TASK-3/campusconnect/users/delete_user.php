<?php
include '../config/db.php';

$id = $_GET['id'];

$sql = "DELETE FROM users WHERE id='$id'";

mysqli_query($conn, $sql);

header("Location: view_users.php");
?>