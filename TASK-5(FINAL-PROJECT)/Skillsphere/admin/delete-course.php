<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
header("Location: courses/delete-course.php?id=" . $id);
exit();
?>