<?php

/* =========================
   DATABASE CONFIGURATION
========================= */

$host = "localhost";

$user = "root";

$password = "";

$database = "quickbite";

/* =========================
   CREATE CONNECTION
========================= */

$conn = mysqli_connect(
    $host,
    $user,
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
   SET CHARACTER ENCODING
========================= */

mysqli_set_charset($conn, "utf8");

?>