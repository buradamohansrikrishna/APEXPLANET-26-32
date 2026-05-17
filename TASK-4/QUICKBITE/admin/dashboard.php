<?php
include '../config/db.php';

$userCount = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM users")
);

$orderCount = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM orders")
);

$foodCount = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM foods")
);

$restaurantCount = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM restaurants")
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>QuickBite Admin Dashboard</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <style>

        body{

            background:
            linear-gradient(
                to right,
                #fff7ed,
                #ffffff
            );

            font-family:Poppins, sans-serif;
        }

        .dashboard{

            width:90%;

            margin:120px auto 60px;
        }

        .dashboard-hero{

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            padding:60px;

            border-radius:40px;

            color:white;

            position:relative;

            overflow:hidden;

            margin-bottom:50px;
        }

        .dashboard-hero::before{

            content:'';

            position:absolute;

            width:350px;
            height:350px;

            background:
            rgba(255,255,255,0.12);

            border-radius:50%;

            top:-120px;
            right:-120px;
        }

        .dashboard-top{

            display:flex;

            justify-content:space-between;

            align-items:center;

            flex-wrap:wrap;

            gap:20px;

            position:relative;
            z-index:2;
        }

        .dashboard-top h1{

            font-size:3.2rem;

            margin-bottom:12px;
        }

        .dashboard-top p{

            max-width:650px;

            line-height:1.8;

            opacity:0.95;
        }

        .logout-btn{

            background:white;

            color:#ff6b35;

            padding:14px 26px;

            border-radius:16px;

            text-decoration:none;

            font-weight:700;

            transition:0.3s;
        }

        .logout-btn:hover{

            transform:
            translateY(-4px);
        }

        .stats-container{

            display:grid;

            grid-template-columns:
            repeat(auto-fit,minmax(250px,1fr));

            gap:30px;

            margin-bottom:70px;
        }

        .stat-card{

            background:
            rgba(255,255,255,0.85);

            backdrop-filter:blur(12px);

            border-radius:30px;

            padding:35px;

            box-shadow:
            0 10px 30px rgba(0,0,0,0.08);

            transition:0.4s;

            position:relative;

            overflow:hidden;

            border:1px solid rgba(255,255,255,0.4);
        }

        .stat-card:hover{

            transform:
            translateY(-10px)
            scale(1.02);

            box-shadow:
            0 20px 40px rgba(255,107,53,0.12);
        }

        .stat-card::before{

            content:'';

            position:absolute;

            width:100%;
            height:7px;

            top:0;
            left:0;

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );
        }

        .stat-icon{

            width:75px;
            height:75px;

            border-radius:50%;

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            display:flex;
            justify-content:center;
            align-items:center;

            color:white;

            font-size:2rem;

            margin-bottom:20px;
        }

        .stat-card h2{

            color:#6b7280;

            margin-bottom:12px;

            font-size:1.1rem;
        }

        .stat-card p{

            font-size:3rem;

            font-weight:700;

            color:#111827;
        }

        .quick-actions{

            margin-top:30px;
        }

        .quick-actions h2{

            margin-bottom:30px;

            font-size:2.3rem;

            color:#111827;
        }

        .action-grid{

            display:grid;

            grid-template-columns:
            repeat(auto-fit,minmax(250px,1fr));

            gap:30px;
        }

        .action-card{

            background:
            rgba(255,255,255,0.85);

            backdrop-filter:blur(10px);

            padding:35px;

            border-radius:28px;

            text-align:center;

            box-shadow:
            0 10px 25px rgba(0,0,0,0.06);

            transition:0.4s;

            border:1px solid rgba(255,255,255,0.4);
        }

        .action-card:hover{

            transform:
            translateY(-10px);

            box-shadow:
            0 20px 35px rgba(255,107,53,0.12);
        }

        .action-icon{

            width:85px;
            height:85px;

            margin:auto auto 25px;

            border-radius:50%;

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            display:flex;
            justify-content:center;
            align-items:center;

            color:white;

            font-size:2rem;
        }

        .action-card h3{

            margin-bottom:15px;

            color:#111827;

            font-size:1.5rem;
        }

        .action-card p{

            color:#6b7280;

            line-height:1.8;

            margin-bottom:25px;
        }

        .action-btn{

            display:inline-block;

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            color:white;

            padding:14px 24px;

            border-radius:14px;

            text-decoration:none;

            font-weight:600;

            transition:0.3s;
        }

        .action-btn:hover{

            transform:
            translateY(-4px);
        }

        @media(max-width:768px){

            .dashboard-hero{

                padding:40px 25px;
            }

            .dashboard-top{

                flex-direction:column;

                align-items:flex-start;
            }

            .dashboard-top h1{

                font-size:2.4rem;
            }

            .quick-actions h2{

                font-size:2rem;
            }
        }

    </style>

</head>

<body>

<div class="dashboard">

    <div class="dashboard-hero">

        <div class="dashboard-top">

            <div>

                <h1>

                    QuickBite Admin 🚀

                </h1>

                <p>

                    Manage restaurants, foods,
                    customer orders, and platform
                    analytics from one dashboard.

                </p>

            </div>

            <a href="../auth/logout.php"
               class="logout-btn">

               Logout

            </a>

        </div>

    </div>

    <div class="stats-container">

        <div class="stat-card">

            <div class="stat-icon">
                👤
            </div>

            <h2>Total Users</h2>

            <p>

                <?php echo $userCount; ?>

            </p>

        </div>

        <div class="stat-card">

            <div class="stat-icon">
                🛒
            </div>

            <h2>Total Orders</h2>

            <p>

                <?php echo $orderCount; ?>

            </p>

        </div>

        <div class="stat-card">

            <div class="stat-icon">
                🍔
            </div>

            <h2>Total Foods</h2>

            <p>

                <?php echo $foodCount; ?>

            </p>

        </div>

        <div class="stat-card">

            <div class="stat-icon">
                🏪
            </div>

            <h2>Total Restaurants</h2>

            <p>

                <?php echo $restaurantCount; ?>

            </p>

        </div>

    </div>

    <div class="quick-actions">

        <h2>

            Quick Actions

        </h2>

        <div class="action-grid">

            <div class="action-card">

                <div class="action-icon">
                    🍽
                </div>

                <h3>

                    Manage Foods

                </h3>

                <p>

                    Add, edit, and delete
                    food items easily.

                </p>

                <a href="foods.php"
                   class="action-btn">

                   Open

                </a>

            </div>

            <div class="action-card">

                <div class="action-icon">
                    📦
                </div>

                <h3>

                    Orders

                </h3>

                <p>

                    Track and manage
                    customer orders.

                </p>

                <a href="orders.php"
                   class="action-btn">

                   View

                </a>

            </div>

            <div class="action-card">

                <div class="action-icon">
                    ➕
                </div>

                <h3>

                    Add Food

                </h3>

                <p>

                    Create delicious new
                    menu items instantly.

                </p>

                <a href="add-food.php"
                   class="action-btn">

                   Add

                </a>

            </div>

            <div class="action-card">

                <div class="action-icon">
                    👥
                </div>

                <h3>

                    Users

                </h3>

                <p>

                    Manage registered
                    customer accounts.

                </p>

                <a href="#"
                   class="action-btn">

                   Manage

                </a>

            </div>

        </div>

    </div>

</div>

</body>
</html>