<?php

/* =========================
   START SESSION
========================= */

if(session_status() === PHP_SESSION_NONE){

    session_start();
}

/* =========================
   USER AUTH CHECK
========================= */

if(!isset($_SESSION['user_id'])){

    header("Location: ../auth/login.php");

    exit();
}

/* =========================
   SESSION SECURITY
========================= */

session_regenerate_id(true);

?>