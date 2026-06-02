<?php

// =========================================
// SKILLSPHERE DATABASE CONNECTION
// includes/db.php
// =========================================

// =========================================
// LOAD CONFIG
// =========================================

require_once __DIR__ . '/config.php';

// =========================================
// MYSQLI ERROR REPORTING
// =========================================

mysqli_report(

    MYSQLI_REPORT_ERROR |
    MYSQLI_REPORT_STRICT

);

// =========================================
// CREATE DATABASE CONNECTION
// =========================================

try{

    $conn = mysqli_connect(

        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME

    );

}catch(Exception $e){

    die(

        "Database Connection Failed"

    );

}

// =========================================
// UTF-8 SUPPORT
// =========================================

mysqli_set_charset(

    $conn,
    "utf8mb4"

);

// =========================================
// TIMEZONE
// =========================================

date_default_timezone_set(
    'Asia/Kolkata'
);

// =========================================
// DATABASE HELPER FUNCTIONS
// =========================================

function dbEscape($value){

    global $conn;

    return mysqli_real_escape_string(

        $conn,

        trim($value)

    );

}

// =========================================
// FETCH SINGLE ROW
// =========================================

function fetchSingle($query){

    global $conn;

    $result =
    mysqli_query($conn,$query);

    return mysqli_fetch_assoc(
        $result
    );

}

// =========================================
// FETCH MULTIPLE ROWS
// =========================================

function fetchAll($query){

    global $conn;

    $result =
    mysqli_query($conn,$query);

    $rows = [];

    while(

        $row =
        mysqli_fetch_assoc($result)

    ){

        $rows[] = $row;

    }

    return $rows;

}

// =========================================
// INSERT ID HELPER
// =========================================

function lastInsertId(){

    global $conn;

    return mysqli_insert_id(
        $conn
    );

}

// =========================================
// PARAMETERIZED PREPARED STATEMENT HELPER
// =========================================

function dbQuery($query, $params = [], $types = "") {
    global $conn;
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return false;
    }
    if (!empty($params)) {
        if (empty($types)) {
            $types = str_repeat("s", count($params));
        }
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    $execute = mysqli_stmt_execute($stmt);
    if (!$execute) {
        return false;
    }
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        return $stmt;
    }
    return $result;
}

function fetchSingleSecure($query, $params = [], $types = "") {
    $result = dbQuery($query, $params, $types);
    if (!$result) return null;
    return mysqli_fetch_assoc($result);
}

function fetchAllSecure($query, $params = [], $types = "") {
    $result = dbQuery($query, $params, $types);
    if (!$result) return [];
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// =========================================
// SHARED HELPERS (upload, thumbnails, etc.)
// =========================================

require_once __DIR__ . '/functions.php';

?>
