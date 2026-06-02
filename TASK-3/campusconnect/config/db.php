<?php

/* =========================
   DATABASE CONFIGURATION
========================= */

$host = "localhost";

$username = "root";

$password = "";

$database = "campusconnect";

/* =========================
   CREATE CONNECTION
========================= */

$conn = mysqli_connect(

    $host,
    $username,
    $password,
    $database
);

/* =========================
   CHECK CONNECTION
========================= */

if(!$conn){

    die(

        "Database Connection Failed : "

        . mysqli_connect_error()
    );
}

/* =========================
   CHARACTER SET
========================= */

mysqli_set_charset($conn, "utf8mb4");

/* =========================
   TIMEZONE
========================= */

date_default_timezone_set("Asia/Kolkata");

?>