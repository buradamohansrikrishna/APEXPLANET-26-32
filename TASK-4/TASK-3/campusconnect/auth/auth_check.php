<?php

// START SESSION

if(session_status() === PHP_SESSION_NONE){

    session_start();
}

/* =========================
   CHECK USER LOGIN
========================= */

if(!isset($_SESSION['user_id'])){

    header("Location: /campusconnect/login.php");

    exit();
}

/* =========================
   OPTIONAL SECURITY
========================= */

// Prevent session hijacking

if(isset($_SESSION['user_agent'])){

    if($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']){

        session_unset();

        session_destroy();

        header("Location: /campusconnect/login.php");

        exit();
    }

}else{

    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
}

/* =========================
   SESSION TIMEOUT
========================= */

$timeout_duration = 1800; // 30 Minutes

if(isset($_SESSION['last_activity'])){

    if((time() - $_SESSION['last_activity']) > $timeout_duration){

        session_unset();

        session_destroy();

        header("Location: /campusconnect/login.php");

        exit();
    }
}

/* UPDATE LAST ACTIVITY */

$_SESSION['last_activity'] = time();

?>