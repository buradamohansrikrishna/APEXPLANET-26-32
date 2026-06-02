<?php

// =========================================
// SKILLSPHERE FUNCTIONS FILE
// includes/functions.php
// =========================================

// =========================================
// START SESSION
// =========================================

if(session_status() === PHP_SESSION_NONE){

    session_start();

}

// =========================================
// SANITIZE INPUT
// =========================================

if (!function_exists('sanitize')) {
    function sanitize($data){

        global $conn;

        $data = trim($data);

        $data = htmlspecialchars($data);

        if(isset($conn)){

            $data = mysqli_real_escape_string(
                $conn,
                $data
            );

        }

        return $data;

    }
}

// =========================================
// CHECK INSTRUCTOR
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
// FORMAT DATE
// =========================================

if (!function_exists('formatDate')) {
    function formatDate($date){

        if(empty($date)){

            return '';

        }

        return date(
            'd M Y',
            strtotime($date)
        );

    }
}

// =========================================
// TIME AGO FUNCTION
// =========================================

if (!function_exists('timeAgo')) {
    function timeAgo($datetime){

        if(empty($datetime)){

            return '';

        }

        $time = time() - strtotime($datetime);

        if($time < 60){

            return $time . ' seconds ago';

        }

        elseif($time < 3600){

            return floor($time / 60)
            . ' minutes ago';

        }

        elseif($time < 86400){

            return floor($time / 3600)
            . ' hours ago';

        }

        else{

            return floor($time / 86400)
            . ' days ago';

        }

    }
}

// =========================================
// DISPLAY FLASH MESSAGE
// =========================================

function displayFlashMessage(){

    if(isset($_SESSION['flash_message'])){

        $type =
        $_SESSION['flash_type'] ?? 'success';

        echo '

        <div class="alert alert-'
        .
        htmlspecialchars($type)
        .
        '">

        '
        .
        htmlspecialchars($_SESSION['flash_message'])
        .
        '

        </div>

        ';

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);

    }

}

// =========================================
// GENERATE SLUG
// =========================================

function generateSlug($string){

    return strtolower(

        trim(

            preg_replace(

                '/[^A-Za-z0-9-]+/',

                '-',

                $string

            )

        )

    );

}

// =========================================
// COURSE THUMBNAIL RESOLUTION
// Prefers uploaded webp/jpg/png over stale SVG entries
// =========================================

if (!function_exists('resolveCourseThumbnail')) {
    function resolveCourseThumbnail($storedFilename = null, $uploadDir = null) {
        $default = defined('DEFAULT_COURSE') ? DEFAULT_COURSE : 'default-course.jpg';
        $uploadDir = $uploadDir ?? (__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, THUMBNAIL_UPLOAD));

        if (!is_dir($uploadDir)) {
            return $default;
        }

        $uploadDir = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!empty($storedFilename) && is_file($uploadDir . $storedFilename)) {
            return $storedFilename;
        }

        $base = pathinfo((string) $storedFilename, PATHINFO_FILENAME);
        if ($base === '' && !empty($storedFilename)) {
            $base = preg_replace('/\.[^.]+$/', '', (string) $storedFilename);
        }

        if ($base !== '') {
            foreach (['webp', 'jpg', 'jpeg', 'png', 'svg'] as $ext) {
                $candidate = $base . '.' . $ext;
                if (is_file($uploadDir . $candidate)) {
                    return $candidate;
                }
            }
        }

        return is_file($uploadDir . $default) ? $default : $storedFilename;
    }
}

if (!function_exists('courseThumbnailUrl')) {
    function courseThumbnailUrl($storedFilename = null, $urlPrefix = '') {
        $file = resolveCourseThumbnail($storedFilename);
        return $urlPrefix . THUMBNAIL_UPLOAD . $file;
    }
}

if (!function_exists('syncCourseThumbnailsInDb')) {
  function syncCourseThumbnailsInDb() {
    global $conn;
    if (!isset($conn)) {
      return 0;
    }

    $result = mysqli_query($conn, 'SELECT id, slug, thumbnail FROM courses');
    if (!$result) {
      return 0;
    }

    $updated = 0;
    while ($row = mysqli_fetch_assoc($result)) {
      $resolved = resolveCourseThumbnail($row['thumbnail'] ?: $row['slug']);
      if ($resolved && $resolved !== ($row['thumbnail'] ?? '')) {
        $stmt = mysqli_prepare($conn, 'UPDATE courses SET thumbnail = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'si', $resolved, $row['id']);
        if (mysqli_stmt_execute($stmt)) {
          $updated++;
        }
      }
    }

    return $updated;
  }
}

// =========================================
// UPLOAD IMAGE
// =========================================

function uploadImage($file,$folder){

    if(

        isset($file) &&
        $file['error'] === 0

    ){

        $extension = strtolower(

            pathinfo(

                $file['name'],

                PATHINFO_EXTENSION

            )

        );

        $allowed = [

            'jpg',
            'jpeg',
            'png',
            'webp'

        ];

        if(in_array($extension,$allowed)){

            if(!is_dir($folder)){

                mkdir($folder,0777,true);

            }

            $filename =

            time()
            .
            '_'
            .
            rand(1000,9999)
            .
            '.'
            .
            $extension;

            $destination =

            rtrim($folder,'/')
            .
            '/'
            .
            $filename;

            if(

                move_uploaded_file(

                    $file['tmp_name'],

                    $destination

                )

            ){

                return $filename;

            }

        }

    }

    return false;

}

// =========================================
// DELETE IMAGE
// =========================================

function deleteImage($path){

    if(

        !empty($path) &&
        file_exists($path)

    ){

        unlink($path);

    }

}

// =========================================
// COURSE COUNT
// =========================================

function getCourseCount(){

    global $conn;

    if(!isset($conn)){

        return 0;

    }

    $query = mysqli_query(

        $conn,

        "SELECT COUNT(*) AS total
         FROM courses"

    );

    $result = mysqli_fetch_assoc($query);

    return $result['total'] ?? 0;

}

// =========================================
// USER COUNT
// =========================================

function getUserCount(){

    global $conn;

    if(!isset($conn)){

        return 0;

    }

    $query = mysqli_query(

        $conn,

        "SELECT COUNT(*) AS total
         FROM users"

    );

    $result = mysqli_fetch_assoc($query);

    return $result['total'] ?? 0;

}

// =========================================
// ENROLLMENT COUNT
// =========================================

function getEnrollmentCount(){

    global $conn;

    if(!isset($conn)){

        return 0;

    }

    $query = mysqli_query(

        $conn,

        "SELECT COUNT(*) AS total
         FROM enrollments"

    );

    $result = mysqli_fetch_assoc($query);

    return $result['total'] ?? 0;

}

// =========================================
// LIMIT TEXT
// =========================================

if (!function_exists('limitText')) {
    function limitText($text,$limit = 100){

        $text = strip_tags($text);

        if(strlen($text) > $limit){

            return substr(
                $text,
                0,
                $limit
            ) . '...';

        }

        return $text;

    }
}

// =========================================
// ACTIVE PAGE
// =========================================

if (!function_exists('activePage')) {
    function activePage($page){

        return basename($_SERVER['PHP_SELF'])
        === $page
        ?
        'active'
        :
        '';

    }
}

?>