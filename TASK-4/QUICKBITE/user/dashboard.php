<?php

session_start();

if(!isset($_SESSION['user_id'])){

    header("Location: ../auth/login.php");

    exit();
}

include '../config/db.php';

$user_name =
$_SESSION['user_name'];

$user_id =
$_SESSION['user_id'];

/* =========================
   USER ORDERS COUNT
========================= */

$orderQuery =
mysqli_query(
    $conn,
    "SELECT * FROM orders
     WHERE user_id='$user_id'"
);

$totalOrders =
mysqli_num_rows($orderQuery);

/* =========================
   TOTAL CART ITEMS
========================= */

$cartCount = 0;

if(isset($_SESSION['cart'])){

    $cartCount =
    count($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width,
          initial-scale=1.0">

    <title>User Dashboard</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <style>

        .dashboard{

            width:90%;

            margin:120px auto;
        }

        .welcome-box{

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            color:white;

            padding:60px;

            border-radius:35px;

            margin-bottom:40px;

            position:relative;

            overflow:hidden;
        }

        .welcome-box::before{

            content:'';

            position:absolute;

            width:300px;
            height:300px;

            background:
            rgba(255,255,255,0.15);

            border-radius:50%;

            top:-100px;
            right:-100px;
        }

        .welcome-box h1{

            font-size:3rem;

            margin-bottom:15px;

            position:relative;
            z-index:2;
        }

        .welcome-box p{

            font-size:1.1rem;

            line-height:1.8;

            position:relative;
            z-index:2;
        }

        .dashboard-grid{

            display:grid;

            grid-template-columns:
            repeat(auto-fit,minmax(260px,1fr));

            gap:30px;
        }

        .dashboard-card{

            background:white;

            padding:35px;

            border-radius:30px;

            box-shadow:
            0 10px 30px rgba(0,0,0,0.08);

            transition:0.4s;
        }

        .dashboard-card:hover{

            transform:
            translateY(-10px);
        }

        .dashboard-card h2{

            font-size:1.3rem;

            margin-bottom:15px;

            color:#111827;
        }

        .dashboard-card p{

            color:#6b7280;

            margin-bottom:25px;

            line-height:1.7;
        }

        .dashboard-stats{

            display:grid;

            grid-template-columns:
            repeat(auto-fit,minmax(220px,1fr));

            gap:25px;

            margin-top:50px;
        }

        .stats-card{

            background:white;

            padding:30px;

            border-radius:25px;

            text-align:center;

            box-shadow:
            0 10px 25px rgba(0,0,0,0.06);
        }

        .stats-card h3{

            color:#6b7280;

            margin-bottom:15px;
        }

        .stats-card p{

            font-size:2.5rem;

            font-weight:700;

            color:#ff6b35;
        }

        @media(max-width:768px){

            .welcome-box{

                padding:40px 25px;
            }

            .welcome-box h1{

                font-size:2.2rem;
            }
        }

    </style>

</head>

<body>

<div class="dashboard">

    <div class="welcome-box">

        <h1>

            Welcome,
            <?php echo $user_name; ?> 👋

        </h1>

        <p>

            Explore delicious meals,
            manage your orders,
            and enjoy seamless food ordering
            with QuickBite.

        </p>

    </div>

    <div class="dashboard-grid">

        <div class="dashboard-card">

            <h2>
                Browse Restaurants
            </h2>

            <p>

                Explore restaurants and
                order your favorite meals.

            </p>

            <a href="restaurants.php"
               class="primary-btn">

               Explore
            </a>

        </div>

        <div class="dashboard-card">

            <h2>
                My Orders
            </h2>

            <p>

                Track your food orders
                and order history.

            </p>

            <a href="orders.php"
               class="primary-btn">

               View Orders
            </a>

        </div>

        <div class="dashboard-card">

            <h2>
                My Cart
            </h2>

            <p>

                View items added to
                your shopping cart.

            </p>

            <a href="cart.php"
               class="primary-btn">

               Open Cart
            </a>

        </div>

        <div class="dashboard-card">

            <h2>
                Logout
            </h2>

            <p>

                Securely logout from
                your QuickBite account.

            </p>

            <a href="../auth/logout.php"
               class="primary-btn">

               Logout
            </a>

        </div>

    </div>

    <div class="dashboard-stats">

        <div class="stats-card">

            <h3>Total Orders</h3>

            <p>

                <?php echo $totalOrders; ?>

            </p>

        </div>

        <div class="stats-card">

            <h3>Cart Items</h3>

            <p>

                <?php echo $cartCount; ?>

            </p>

        </div>

    </div>

</div>

</body>
</html>