<?php

include '../config/db.php';

$message = "";

if(isset($_POST['register'])){

    $name = $_POST['name'];

    $email = $_POST['email'];

    $password =
    password_hash(
        $_POST['password'],
        PASSWORD_DEFAULT
    );

    /* CHECK EMAIL EXISTS */

    $checkEmail =
    mysqli_query(
        $conn,
        "SELECT * FROM users
         WHERE email='$email'"
    );

    if(mysqli_num_rows($checkEmail) > 0){

        $message =
        "Email Already Exists";

    }else{

        $query =
        "INSERT INTO users(
            name,
            email,
            password
        )

        VALUES(
            '$name',
            '$email',
            '$password'
        )";

        $result =
        mysqli_query($conn, $query);

        if($result){

            header("Location: login.php");

            exit();

        }else{

            $message =
            "Registration Failed";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width,
          initial-scale=1.0">

    <title>Create Account</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body>

<div class="form-container">

    <form method="POST"
          class="modern-form fade-in">

        <h2>
            Create Account 🚀
        </h2>

        <p style="
            text-align:center;
            color:#6b7280;
            margin-bottom:25px;
        ">

            Join QuickBite and start
            ordering delicious food.

        </p>

        <?php
        if($message != ""){
        ?>

        <div style="
            background:#fee2e2;
            color:#991b1b;
            padding:14px;
            border-radius:12px;
            margin-bottom:20px;
            text-align:center;
        ">

            <?php echo $message; ?>

        </div>

        <?php } ?>

        <input
            type="text"
            name="name"
            placeholder="Full Name"
            required
        >

        <input
            type="email"
            name="email"
            placeholder="Enter Email"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Create Password"
            required
        >

        <button
            type="submit"
            name="register"
            class="primary-btn"
        >

            Create Account

        </button>

        <p style="
            text-align:center;
            margin-top:25px;
            color:#6b7280;
        ">

            Already have an account?

            <a href="login.php"
               style="
               color:#ff6b35;
               font-weight:600;
               text-decoration:none;
               ">

               Login

            </a>

        </p>

    </form>

</div>

<script src="../assets/js/main.js"></script>

</body>
</html>