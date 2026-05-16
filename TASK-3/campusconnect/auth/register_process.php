<?php

// DATABASE CONNECTION

include '../config/db.php';

/* =========================
   CHECK REQUEST METHOD
========================= */

if($_SERVER["REQUEST_METHOD"] != "POST"){

    header("Location: ../register.php");
    exit();
}

/* =========================
   GET FORM DATA
========================= */

$name = trim($_POST['name']);

$email = trim($_POST['email']);

$password = trim($_POST['password']);

$role = trim($_POST['role']);

/* =========================
   VALIDATION
========================= */

if(
    empty($name) ||
    empty($email) ||
    empty($password) ||
    empty($role)
){

    die("All fields are required.");
}

/* =========================
   VALID EMAIL
========================= */

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

    die("Invalid Email Format");
}

/* =========================
   PASSWORD LENGTH
========================= */

if(strlen($password) < 5){

    die("Password must be at least 5 characters");
}

/* =========================
   CHECK DUPLICATE EMAIL
========================= */

$checkEmail = $conn->prepare(
    "SELECT id FROM users WHERE email = ?"
);

$checkEmail->bind_param("s", $email);

$checkEmail->execute();

$checkResult = $checkEmail->get_result();

if($checkResult->num_rows > 0){

    echo "
    <script>
        alert('Email already exists');
        window.location.href='../register.php';
    </script>
    ";

    exit();
}

/* =========================
   HASH PASSWORD
========================= */

$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);

/* =========================
   INSERT USER
========================= */

$stmt = $conn->prepare(

    "INSERT INTO users
    (name,email,password,role)

    VALUES(?,?,?,?)"
);

$stmt->bind_param(

    "ssss",

    $name,
    $email,
    $hashedPassword,
    $role
);

/* =========================
   EXECUTE QUERY
========================= */

if($stmt->execute()){

    echo "
    <script>
        alert('Registration Successful');
        window.location.href='../login.php';
    </script>
    ";

}else{

    echo "
    <script>
        alert('Registration Failed');
        window.location.href='../register.php';
    </script>
    ";
}

/* CLOSE STATEMENTS */

$stmt->close();

$checkEmail->close();

?>