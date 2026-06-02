<?php

// =========================================
// SKILLSPHERE LOGOUT
// logout.php
// =========================================

// =========================================
// SESSION START
// =========================================

if(session_status() === PHP_SESSION_NONE){

    session_start();

}

// =========================================
// CLEAR SESSION VARIABLES
// =========================================

$_SESSION = [];

// =========================================
// REMOVE SESSION COOKIE
// =========================================

if(ini_get("session.use_cookies")){

    $params =
    session_get_cookie_params();

    setcookie(

        session_name(),

        '',

        time() - 42000,

        $params["path"],

        $params["domain"],

        $params["secure"],

        $params["httponly"]

    );

}

// =========================================
// DESTROY SESSION
// =========================================

session_destroy();

// =========================================
// START NEW SESSION FOR FLASH MESSAGE
// =========================================

session_start();

$_SESSION['flash_type'] =
'success';

$_SESSION['flash_message'] =
'Logged out successfully';

// =========================================
// REDIRECT
// =========================================

header(
    'Location: login.php'
);

exit();

?>