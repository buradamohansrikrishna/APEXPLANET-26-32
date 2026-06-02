<?php
require_once '../auth.php';
requireAdmin();
header("Location: dashboard.php");
exit();
?>
