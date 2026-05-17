<?php

session_start();

include '../config/db.php';

/* =========================
   MESSAGE VARIABLE
========================= */

$message = "";

/* =========================
   LOGIN USER
========================= */

if(isset($_POST['login'])){

    /* =========================
       GET FORM DATA
    ========================= */

    $email =
    mysqli_real_escape_string(
        $conn,
        trim($_POST['email'])
    );

    $password =
    trim($_POST['password']);

    /* =========================
       CHECK USER
    ========================= */

    $query =
    "SELECT * FROM users
     WHERE email='$email'";

    $result =
    mysqli_query($conn, $query);

    /* =========================
       USER FOUND
    ========================= */

    if(mysqli_num_rows($result) > 0){

        $user =
        mysqli_fetch_assoc($result);

        /* =========================
           VERIFY PASSWORD
        ========================= */

        if(password_verify(
            $password,
            $user['password']
        )){

            $_SESSION['user_id'] =
            $user['id'];

            $_SESSION['user_name'] =
            $user['name'];

            $_SESSION['user_email'] =
            $user['email'];

            /* =========================
               REDIRECT USER
            ========================= */

            header(
            "Location: ../user/dashboard.php"
            );

            exit();

        }else{

            $message =
            "Incorrect Password!";
        }

    }else{

        $message =
        "Email Not Registered!";
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

    <title>
        QuickBite Login
    </title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <style>

        body{

            min-height:100vh;

            display:flex;
            justify-content:center;
            align-items:center;

            background:
            linear-gradient(
                to bottom right,
                #fff7ed,
                #ffffff
            );

            padding:30px;
        }

        .form-container{

            width:100%;
            max-width:450px;
        }

        .modern-form{

            background:white;

            padding:45px;

            border-radius:30px;

            box-shadow:
            0 15px 40px rgba(0,0,0,0.08);

            animation:
            fadeIn 0.8s ease;
        }

        .modern-form h2{

            text-align:center;

            font-size:2.3rem;

            margin-bottom:10px;

            color:#111827;
        }

        .login-subtitle{

            text-align:center;

            color:#6b7280;

            margin-bottom:30px;

            line-height:1.7;
        }

        .error-message{

            background:#fee2e2;

            color:#991b1b;

            padding:14px;

            border-radius:12px;

            margin-bottom:20px;

            text-align:center;

            font-weight:500;
        }

        .modern-form input{

            width:100%;

            padding:16px;

            margin-bottom:20px;

            border:2px solid #f1f5f9;

            border-radius:14px;

            font-size:1rem;

            outline:none;

            transition:0.3s ease;
        }

        .modern-form input:focus{

            border-color:#ff6b35;

            box-shadow:
            0 0 0 4px rgba(255,107,53,0.1);
        }

        .extra-options{

            display:flex;

            justify-content:space-between;

            align-items:center;

            margin-bottom:25px;

            font-size:0.95rem;

            gap:10px;
        }

        .remember-box{

            display:flex;

            align-items:center;

            gap:8px;

            color:#6b7280;
        }

        .forgot-link{

            color:#ff6b35;

            text-decoration:none;

            font-weight:500;
        }

        .forgot-link:hover{

            text-decoration:underline;
        }

        .login-btn{

            width:100%;

            border:none;

            padding:16px;

            font-size:1rem;
        }

        .register-text{

            text-align:center;

            margin-top:25px;

            color:#6b7280;
        }

        .register-text a{

            color:#ff6b35;

            font-weight:600;

            text-decoration:none;
        }

        .register-text a:hover{

            text-decoration:underline;
        }

        @keyframes fadeIn{

            from{

                opacity:0;

                transform:
                translateY(20px);
            }

            to{

                opacity:1;

                transform:
                translateY(0px);
            }
        }

        @media(max-width:480px){

            .modern-form{

                padding:35px 25px;
            }

            .modern-form h2{

                font-size:1.9rem;
            }

            .extra-options{

                flex-direction:column;

                align-items:flex-start;
            }
        }

    </style>

</head>

<body>

<div class="form-container">

    <form method="POST"
          class="modern-form">

        <h2>
            Welcome Back 👋
        </h2>

        <p class="login-subtitle">

            Login to continue ordering
            delicious food from QuickBite.

        </p>

        <?php
        if($message != ""){
        ?>

        <div class="error-message">

            <?php
            echo $message;
            ?>

        </div>

        <?php } ?>

        <input
            type="email"
            name="email"
            placeholder="Enter Email"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Enter Password"
            required
        >

        <div class="extra-options">

            <label class="remember-box">

                <input type="checkbox">

                Remember Me

            </label>

            <a href="#"
               class="forgot-link">

               Forgot Password?

            </a>

        </div>

        <button
            type="submit"
            name="login"
            class="primary-btn login-btn"
        >

            Login

        </button>

        <p class="register-text">

            Don't have an account?

            <a href="./register.php">

               Register

            </a>

        </p>

    </form>

</div>

<script src="../assets/js/main.js"></script>

</body>
</html>