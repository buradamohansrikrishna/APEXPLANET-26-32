<?php

/* =========================
   SESSION SECURITY SETTINGS
========================= */

ini_set('session.cookie_httponly', 1);

ini_set('session.use_only_cookies', 1);

ini_set('session.use_strict_mode', 1);

/* =========================
   START SESSION
========================= */

if(session_status() === PHP_SESSION_NONE){

    session_start();
}

/* =========================
   SESSION TIMEOUT
========================= */

$session_timeout = 1800;

if(isset($_SESSION['LAST_ACTIVITY'])){

    if((time() - $_SESSION['LAST_ACTIVITY']) > $session_timeout){

        session_unset();

        session_destroy();

        header("Location: /campusconnect/login.php");

        exit();
    }
}

/* UPDATE ACTIVITY */

$_SESSION['LAST_ACTIVITY'] = time();

/* =========================
   REGENERATE SESSION
========================= */

if(!isset($_SESSION['CREATED'])){

    $_SESSION['CREATED'] = time();

}else if(time() - $_SESSION['CREATED'] > 1800){

    session_regenerate_id(true);

    $_SESSION['CREATED'] = time();
}

?>