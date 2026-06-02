<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
header("Location: courses/edit-course.php?id=" . $id);
exit();
?>
