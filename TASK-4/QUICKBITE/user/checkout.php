<?php

session_start();

include '../config/db.php';

/* =========================
   LOGIN CHECK
========================= */

if(!isset($_SESSION['user_id'])){

    header("Location: ../auth/login.php");

    exit();
}

/* =========================
   EMPTY CART CHECK
========================= */

if(
    !isset($_SESSION['cart'])
    ||
    count($_SESSION['cart']) == 0
){

    header("Location: cart.php");

    exit();
}

$user_id = $_SESSION['user_id'];

$orderSuccess = false;

/* =========================
   INSERT ORDERS
========================= */

foreach($_SESSION['cart'] as $item){

    $food_id = $item['food_id'];

    $quantity = $item['quantity'];

    $total =
    $item['price'] * $quantity;

    $query =
    "INSERT INTO orders(

        user_id,
        food_id,
        quantity,
        total_price,
        order_status

    )

    VALUES(

        '$user_id',
        '$food_id',
        '$quantity',
        '$total',
        'Pending'
    )";

    $result =
    mysqli_query($conn, $query);

    if($result){

        $orderSuccess = true;
    }
}

/* =========================
   CLEAR CART
========================= */

unset($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width,
          initial-scale=1.0">

    <title>Order Success</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <style>

        .success-box{

            width:100%;
            min-height:100vh;

            display:flex;
            justify-content:center;
            align-items:center;

            padding:40px;
        }

        .success-card{

            background:white;

            width:500px;

            padding:50px;

            border-radius:35px;

            text-align:center;

            box-shadow:
            0 15px 40px rgba(0,0,0,0.08);

            animation:fadeUp 1s ease;
        }

        .success-icon{

            width:120px;
            height:120px;

            margin:auto;

            border-radius:50%;

            background:
            linear-gradient(
                135deg,
                #22c55e,
                #16a34a
            );

            display:flex;
            justify-content:center;
            align-items:center;

            font-size:4rem;

            color:white;

            margin-bottom:30px;

            animation:pop 0.6s ease;
        }

        .success-card h1{

            font-size:2.5rem;

            margin-bottom:15px;

            color:#111827;
        }

        .success-card p{

            color:#6b7280;

            line-height:1.8;

            margin-bottom:35px;
        }

        .success-buttons{

            display:flex;

            justify-content:center;

            gap:20px;

            flex-wrap:wrap;
        }

        @keyframes fadeUp{

            from{
                opacity:0;
                transform:translateY(40px);
            }

            to{
                opacity:1;
                transform:translateY(0);
            }
        }

        @keyframes pop{

            0%{
                transform:scale(0);
            }

            100%{
                transform:scale(1);
            }
        }

        @media(max-width:600px){

            .success-card{

                width:100%;

                padding:40px 25px;
            }

            .success-card h1{
                font-size:2rem;
            }
        }

    </style>

</head>

<body>

<div class="success-box">

    <div class="success-card">

        <?php if($orderSuccess){ ?>

        <div class="success-icon">
            ✓
        </div>

        <h1>
            Order Confirmed!
        </h1>

        <p>

            Your delicious food is being
            prepared and will arrive soon 🍔

        </p>

        <div class="success-buttons">

            <a href="orders.php"
               class="primary-btn">

               View Orders
            </a>

            <a href="restaurants.php"
               class="primary-btn">

               Order More
            </a>

        </div>

        <?php } else { ?>

        <h1>
            Something Went Wrong
        </h1>

        <p>

            Unable to place your order.
            Please try again.

        </p>

        <a href="cart.php"
           class="primary-btn">

           Back To Cart
        </a>

        <?php } ?>

    </div>

</div>

</body>
</html>