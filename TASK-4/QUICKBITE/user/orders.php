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

$user_id = $_SESSION['user_id'];

/* =========================
   FETCH ORDERS
========================= */

$query =
"SELECT orders.*, foods.food_name,
foods.image

FROM orders

JOIN foods
ON orders.food_id = foods.id

WHERE user_id='$user_id'

ORDER BY orders.id DESC";

$result =
mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width,
          initial-scale=1.0">

    <title>My Orders</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <style>

        .orders-page{

            width:90%;

            margin:120px auto;
        }

        .orders-header{

            text-align:center;

            margin-bottom:50px;
        }

        .orders-header h1{

            font-size:3rem;

            margin-bottom:15px;

            color:#111827;
        }

        .orders-header p{

            color:#6b7280;

            font-size:1.05rem;
        }

        .search-box{

            margin-bottom:40px;
        }

        .search-box input{

            width:100%;

            padding:18px;

            border:none;

            border-radius:18px;

            background:white;

            box-shadow:
            0 8px 25px rgba(0,0,0,0.05);

            font-size:1rem;
        }

        .orders-grid{

            display:grid;

            grid-template-columns:
            repeat(auto-fit,minmax(340px,1fr));

            gap:35px;
        }

        .order-card{

            background:white;

            border-radius:30px;

            overflow:hidden;

            box-shadow:
            0 10px 30px rgba(0,0,0,0.08);

            transition:0.4s;
        }

        .order-card:hover{

            transform:
            translateY(-10px);
        }

        .order-card img{

            width:100%;

            height:220px;

            object-fit:cover;
        }

        .order-content{

            padding:25px;
        }

        .order-top{

            display:flex;

            justify-content:space-between;

            align-items:center;

            margin-bottom:20px;
        }

        .order-top h3{

            font-size:1.5rem;

            color:#111827;
        }

        .status{

            padding:10px 16px;

            border-radius:30px;

            font-size:0.9rem;

            font-weight:600;
        }

        .pending{

            background:#fef3c7;

            color:#92400e;
        }

        .completed{

            background:#dcfce7;

            color:#166534;
        }

        .cancelled{

            background:#fee2e2;

            color:#991b1b;
        }

        .order-details{

            margin-top:15px;
        }

        .order-details p{

            color:#6b7280;

            margin-bottom:12px;

            line-height:1.7;
        }

        .order-details strong{
            color:#111827;
        }

        .empty-orders{

            text-align:center;

            padding:100px 20px;
        }

        .empty-orders h2{

            margin:20px 0 10px;
        }

        .empty-orders p{

            color:#6b7280;

            margin-bottom:30px;
        }

        @media(max-width:768px){

            .orders-page{
                width:95%;
            }

            .orders-header h1{
                font-size:2.3rem;
            }
        }

    </style>

</head>

<body>

<div class="orders-page">

    <div class="orders-header">

        <h1>
            My Orders 🍔
        </h1>

        <p>

            Track all your delicious
            food orders here.

        </p>

    </div>

    <div class="search-box">

        <input
            type="text"
            id="searchInput"
            placeholder="Search your orders..."
        >

    </div>

<?php

if(mysqli_num_rows($result) > 0){
?>

<div class="orders-grid" id="ordersGrid">

<?php
while($order = mysqli_fetch_assoc($result)){

$status =
strtolower($order['order_status']);
?>

<div class="order-card">

    <img
    src="../<?php
    echo htmlspecialchars($order['image']);
    ?>"
    >

    <div class="order-content">

        <div class="order-top">

            <h3>

                <?php
                echo $order['food_name'];
                ?>

            </h3>

            <span class="status <?php echo $status; ?>">

                <?php
                echo $order['order_status'];
                ?>

            </span>

        </div>

        <div class="order-details">

            <p>

                <strong>Quantity:</strong>

                <?php
                echo $order['quantity'];
                ?>

            </p>

            <p>

                <strong>Total Price:</strong>

                ₹ <?php
                echo number_format(
                    $order['total_price'],
                    2
                );
                ?>

            </p>

            <p>

                <strong>Ordered On:</strong>

                <?php
                echo date(
                    "d M Y, h:i A",
                    strtotime(
                        $order['order_date']
                    )
                );
                ?>

            </p>

        </div>

    </div>

</div>

<?php } ?>

</div>

<?php } else { ?>

<div class="empty-orders">

    <img
    src="../assets/images/empty-cart.png"
    width="250"
    >

    <h2>
        No Orders Yet
    </h2>

    <p>

        Start ordering delicious
        food from restaurants.

    </p>

    <a href="restaurants.php"
       class="primary-btn">

       Browse Foods
    </a>

</div>

<?php } ?>

</div>

<script>

/* =========================
   SEARCH ORDERS
========================= */

const searchInput =
document.getElementById('searchInput');

searchInput.addEventListener('keyup', () => {

    const filter =
    searchInput.value.toLowerCase();

    const cards =
    document.querySelectorAll('.order-card');

    cards.forEach(card => {

        const text =
        card.innerText.toLowerCase();

        card.style.display =
        text.includes(filter)
        ? 'block'
        : 'none';
    });
});

</script>

</body>
</html>