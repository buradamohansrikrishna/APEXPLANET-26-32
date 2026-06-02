<?php

// =========================================
// SKILLSPHERE CONFIG FILE
// includes/config.php
// =========================================

// =========================================
// ERROR REPORTING
// =========================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

// =========================================
// START SESSION
// =========================================

if(session_status() === PHP_SESSION_NONE){

    session_start();

}

// =========================================
// SITE SETTINGS
// =========================================

define('SITE_NAME', 'SkillSphere');

define(
    'SITE_TAGLINE',
    'Modern Online Learning Platform'
);

// =========================================
// IMPORTANT
// YOUR FOLDER NAME = Skillsphere
// =========================================

define(
    'BASE_URL',
    'http://localhost/Skillsphere/'
);

// =========================================
// DATABASE SETTINGS
// =========================================

define('DB_HOST', 'localhost');

define('DB_USER', 'root');

define('DB_PASS', '');

define('DB_NAME', 'skillsphere');

// =========================================
// UPLOAD PATHS
// =========================================

define(
    'PROFILE_UPLOAD',
    'uploads/profiles/'
);

define(
    'THUMBNAIL_UPLOAD',
    'uploads/thumbnails/'
);

// =========================================
// DEFAULT IMAGES
// =========================================

define(
    'DEFAULT_PROFILE',
    'default.png'
);

define(
    'DEFAULT_COURSE',
    'default-course.jpg'
);

// =========================================
// MAIL SETTINGS
// =========================================

define(
    'MAIL_FROM',
    'noreply@skillsphere.com'
);

// =========================================
// PASSWORD SETTINGS
// =========================================

define(
    'PASSWORD_MIN_LENGTH',
    6
);

// =========================================
// TIMEZONE
// =========================================

date_default_timezone_set(
    'Asia/Kolkata'
);

// =========================================
// REDIRECT FUNCTION
// =========================================

if (!function_exists('redirect')) {
    function redirect($path){

        header(
            "Location: " . BASE_URL . $path
        );

        exit();

    }
}

// =========================================
// LOGIN CHECK
// =========================================

if (!function_exists('isLoggedIn')) {
    function isLoggedIn(){

        return isset($_SESSION['user_id']);

    }
}

// =========================================
// ADMIN CHECK
// =========================================

if (!function_exists('isAdmin')) {
    function isAdmin(){

        return (

            isset($_SESSION['user_role'])

            &&

            $_SESSION['user_role'] === 'admin'

        );

    }
}

// =========================================
// USER ROLE
// =========================================

function getUserRole(){

    return $_SESSION['user_role']
    ?? null;

}

// =========================================
// FLASH MESSAGE
// =========================================

function setFlashMessage($type, $message){

    $_SESSION['flash_type'] = $type;

    $_SESSION['flash_message'] = $message;

}

// =========================================
// SHOW FLASH MESSAGE
// =========================================

function showFlashMessage(){

    if(isset($_SESSION['flash_message'])){

        echo '

        <div class="alert alert-'

        .

        htmlspecialchars($_SESSION['flash_type'])

        .

        '">

        '

        .

        htmlspecialchars($_SESSION['flash_message'])

        .

        '

        </div>

        ';

        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);

    }

}

// =========================================
// CSRF PROTECTION HELPERS
// =========================================

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// =========================================
// SECURITY HEADERS
// =========================================

if(!headers_sent()){

    header(
        'X-Frame-Options: SAMEORIGIN'
    );

    header(
        'X-Content-Type-Options: nosniff'
    );

}

?>