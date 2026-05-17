<?php

session_start();

include '../config/db.php';

/* =========================
   CREATE CART SESSION
========================= */

if(!isset($_SESSION['cart'])){

    $_SESSION['cart'] = [];
}

/* =========================
   ADD TO CART
========================= */

if(isset($_POST['add_cart'])){

    $food_id = $_POST['food_id'];

    $price = $_POST['price'];

    $quantity = $_POST['quantity'];

    /* =========================
       CHECK IF ITEM ALREADY EXISTS
    ========================= */

    $found = false;

    foreach($_SESSION['cart'] as &$cart_item){

        if($cart_item['food_id'] == $food_id){

            $cart_item['quantity'] += $quantity;

            $found = true;

            break;
        }
    }

    /* =========================
       ADD NEW ITEM
    ========================= */

    if(!$found){

        $_SESSION['cart'][] = [

            'food_id' => $food_id,

            'price' => $price,

            'quantity' => $quantity
        ];
    }
}

/* =========================
   REMOVE ITEM
========================= */

if(isset($_GET['remove'])){

    $index = $_GET['remove'];

    unset($_SESSION['cart'][$index]);

    $_SESSION['cart'] =
    array_values($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width,
          initial-scale=1.0">

    <title>My Cart</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <style>

        body{

            background:#fffaf5;
        }

        .cart-container{

            width:90%;

            margin:120px auto 80px;
        }

        .cart-card{

            background:white;

            padding:25px;

            border-radius:25px;

            margin-bottom:25px;

            display:flex;

            align-items:center;

            justify-content:space-between;

            gap:25px;

            flex-wrap:wrap;

            box-shadow:
            0 10px 30px rgba(0,0,0,0.06);
        }

        .cart-image img{

            width:120px;
            height:120px;

            object-fit:cover;

            border-radius:20px;

            display:block;
        }

        .cart-details{

            flex:1;
        }

        .cart-details h3{

            font-size:1.5rem;

            color:#111827;

            margin-bottom:10px;
        }

        .cart-details p{

            color:#6b7280;
        }

        .quantity-box{

            display:flex;

            align-items:center;

            gap:12px;
        }

        .minus-btn,
        .plus-btn{

            width:40px;
            height:40px;

            border:none;

            border-radius:10px;

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            color:white;

            font-size:1.2rem;

            cursor:pointer;
        }

        .quantity-input{

            width:70px;

            text-align:center;

            padding:10px;

            border-radius:10px;

            border:1px solid #ddd;

            font-size:1rem;
        }

        .subtotal{

            color:#111827;

            font-size:1.4rem;
        }

        .remove-btn{

            background:#ef4444;

            color:white;

            padding:12px 18px;

            border-radius:12px;

            text-decoration:none;

            font-weight:600;

            transition:0.3s ease;
        }

        .remove-btn:hover{

            background:#dc2626;
        }

        .total-box{

            background:white;

            padding:35px;

            border-radius:25px;

            margin-top:40px;

            text-align:center;

            box-shadow:
            0 10px 30px rgba(0,0,0,0.06);
        }

        .grand-total{

            color:#ff6b35;
        }

        .empty-cart{

            text-align:center;

            background:white;

            padding:60px 30px;

            border-radius:30px;

            box-shadow:
            0 10px 30px rgba(0,0,0,0.06);
        }

        .empty-cart img{

            margin-bottom:25px;
        }

        .empty-cart h2{

            margin-bottom:15px;

            color:#111827;
        }

        .empty-cart p{

            color:#6b7280;

            margin-bottom:30px;
        }

        @media(max-width:768px){

            .cart-card{

                flex-direction:column;

                text-align:center;
            }

            .quantity-box{

                justify-content:center;
            }
        }

    </style>

</head>

<body>

<h2 class="section-title">
    My Cart 🛒
</h2>

<div class="cart-container">

<?php

$total = 0;

/* =========================
   EMPTY CART
========================= */

if(count($_SESSION['cart']) == 0){
?>

<div class="empty-cart">

    <img
    src="../assets/images/banners/hero-food.jpg"
    width="250"
    style="
    border-radius:20px;
    object-fit:cover;
    "
    >

    <h2>Your Cart Is Empty</h2>

    <p>
        Add delicious food items
        to continue ordering.
    </p>

    <a href="restaurants.php"
       class="primary-btn">

       Browse Foods
    </a>

</div>

<?php
}else{

/* =========================
   DISPLAY CART ITEMS
========================= */

foreach($_SESSION['cart']
as $index => $item){

    $food_id = $item['food_id'];

    $query =
    "SELECT * FROM foods
     WHERE id='$food_id'";

    $result =
    mysqli_query($conn, $query);

    $food =
    mysqli_fetch_assoc($result);

    /* =========================
       IMAGE FIX
    ========================= */

    $image_path =
    "../assets/images/foods/" .
    $food['image'];

    $subtotal =
    $item['price'] *
    $item['quantity'];

    $total += $subtotal;
?>

<div class="cart-card">

    <div class="cart-image">

        <img
        src="<?php
        echo $image_path;
        ?>"
        alt="<?php
        echo $food['food_name'];
        ?>"
        >

    </div>

    <div class="cart-details">

        <h3>
            <?php
            echo $food['food_name'];
            ?>
        </h3>

        <p>

            <?php
            echo $food['category'];
            ?>

        </p>

    </div>

    <div class="quantity-box">

        <button class="minus-btn">
            -
        </button>

        <input
            type="number"

            value="<?php
            echo $item['quantity'];
            ?>"

            class="quantity-input"

            min="1"

            data-price="<?php
            echo $item['price'];
            ?>"
        >

        <button class="plus-btn">
            +
        </button>

    </div>

    <div>

        <h3 class="subtotal">

            ₹ <?php
            echo number_format(
                $subtotal,
                2
            );
            ?>

        </h3>

    </div>

    <div>

        <a href="cart.php?remove=<?php
           echo $index;
           ?>"

           class="remove-btn">

           Remove
        </a>

    </div>

</div>

<?php } ?>

<div class="total-box">

    <h2>

        Grand Total:

        <span class="grand-total">

            ₹ <?php
            echo number_format(
                $total,
                2
            );
            ?>

        </span>

    </h2>

    <br>

    <a href="checkout.php"
       class="primary-btn">

       Proceed To Checkout
    </a>

</div>

<?php } ?>

</div>

<script src="../assets/js/cart.js"></script>

</body>
</html>