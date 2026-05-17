<?php

session_start();

/* REMOVE ALL SESSION VARIABLES */

$_SESSION = array();

/* DESTROY SESSION */

session_destroy();

/* REDIRECT TO LOGIN */

header("Location: login.php?logout=success");

exit();

?>