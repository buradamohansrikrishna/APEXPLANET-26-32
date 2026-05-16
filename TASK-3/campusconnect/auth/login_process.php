<?php

// START SESSION

session_start();

// DATABASE CONNECTION

include '../config/db.php';

/* =========================
   CHECK REQUEST METHOD
========================= */

if($_SERVER["REQUEST_METHOD"] != "POST"){

    header("Location: ../login.php");
    exit();
}

/* =========================
   GET FORM DATA
========================= */

$email = trim($_POST['email']);
$password = trim($_POST['password']);

/* =========================
   VALIDATION
========================= */

if(empty($email) || empty($password)){

    die("All fields are required.");
}

/* =========================
   PREPARED STATEMENT
========================= */

$stmt = $conn->prepare(
    "SELECT * FROM users WHERE email = ?"
);

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

/* =========================
   CHECK USER EXISTS
========================= */

if($result->num_rows > 0){

    $row = $result->fetch_assoc();

    /* =========================
       VERIFY PASSWORD
    ========================= */

    if(password_verify($password, $row['password'])){

        /* =========================
           SECURE SESSION
        ========================= */

        session_regenerate_id(true);

        $_SESSION['user_id'] = $row['id'];

        $_SESSION['name'] = $row['name'];

        $_SESSION['role'] = $row['role'];

        $_SESSION['logged_in'] = true;

        $_SESSION['last_activity'] = time();

        /* =========================
           REDIRECT USER
        ========================= */

        header("Location: /campusconnect/dashboard.php");

        exit();

    }else{

        echo "
        <script>
            alert('Invalid Password');
            window.location.href='../login.php';
        </script>
        ";
    }

}else{

    echo "
    <script>
        alert('User Not Found');
        window.location.href='../login.php';
    </script>
    ";
}

/* CLOSE STATEMENT */

$stmt->close();

?>