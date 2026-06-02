<?php

// =========================================
// SKILLSPHERE AUTH SYSTEM
// includes/auth.php
// =========================================

// =========================================
// SESSION START
// =========================================

if(session_status() === PHP_SESSION_NONE){

    session_start();

}

// =========================================
// DATABASE CONNECTION
// =========================================

require_once __DIR__ . '/db.php';

// =========================================
// LOGIN CHECK
// =========================================

if(!isset($_SESSION['user_id'])){

    $_SESSION['error'] =
    "Please login to continue";

    header("Location: " . BASE_URL . "login.php");

    exit();

}

// =========================================
// FETCH USER DATA
// =========================================

$user_id =
intval($_SESSION['user_id']);

$currentUser = fetchSingleSecure(
    "SELECT * FROM users WHERE id = ? LIMIT 1",
    [$user_id]
);

if(!$currentUser){

    session_destroy();

    header("Location: " . BASE_URL . "login.php");

    exit();

}

// =========================================
// ACCOUNT STATUS CHECK
// =========================================

if(

    isset($currentUser['status']) &&
    $currentUser['status'] === 'blocked'

){

    session_destroy();

    header("Location: ../login.php");

    exit();

}

// =========================================
// AUTO SESSION VARIABLES
// =========================================

$_SESSION['user_name'] =

$currentUser['full_name']
?? $currentUser['name'];

$_SESSION['user_email'] =

$currentUser['email'];

$_SESSION['user_role'] =

$currentUser['role'];

$_SESSION['profile_image'] =

$currentUser['profile_image']
?? 'default.png';

// =========================================
// ADMIN HELPER
// =========================================

if (!function_exists('isAdmin')) {
    function isAdmin(){

        return (

            isset($_SESSION['user_role']) &&

            $_SESSION['user_role'] === 'admin'

        );

    }
}

// =========================================
// INSTRUCTOR HELPER
// =========================================

if (!function_exists('isInstructor')) {
    function isInstructor(){

        return (

            isset($_SESSION['user_role']) &&

            $_SESSION['user_role'] === 'instructor'

        );

    }
}

// =========================================
// STUDENT HELPER
// =========================================

if (!function_exists('isStudent')) {
    function isStudent(){

        return (

            isset($_SESSION['user_role']) &&

            $_SESSION['user_role'] === 'student'

        );

    }
}

// =========================================
// ADMIN ACCESS CHECK
// =========================================

if (!function_exists('requireAdmin')) {
    function requireAdmin(){

        if(!isAdmin()){

            header("Location: " . BASE_URL . "index.php");

            exit();

        }

    }
}

// =========================================
// SANITIZE FUNCTION
// =========================================

if (!function_exists('sanitize')) {
    function sanitize($data){

        global $conn;

        return mysqli_real_escape_string(

            $conn,

            trim($data)

        );

    }
}

// =========================================
// FLASH MESSAGE HELPER
// =========================================

function setMessage(

    $type,
    $message

){

    $_SESSION['flash_type'] =
    $type;

    $_SESSION['flash_message'] =
    $message;

}

// =========================================
// LOGOUT HELPER
// =========================================

function logoutUser(){

    session_unset();

    session_destroy();

    header("Location: " . BASE_URL . "login.php");

    exit();

}

?>