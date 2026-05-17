<?php

session_start();

include '../config/db.php';

/* =========================
   RESTAURANT ID
========================= */

if(!isset($_GET['id'])){

    header("Location: restaurants.php");

    exit();
}

$restaurant_id = $_GET['id'];

/* =========================
   FETCH RESTAURANT
========================= */

$restaurantQuery =
mysqli_query(
    $conn,
    "SELECT * FROM restaurants
     WHERE id='$restaurant_id'"
);

$restaurant =
mysqli_fetch_assoc($restaurantQuery);

/* =========================
   FETCH FOODS
========================= */

$query =
"SELECT * FROM foods
 WHERE restaurant_id='$restaurant_id'";

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

    <title>
        <?php
        echo $restaurant['restaurant_name'];
        ?> Menu
    </title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <style>

        body{

            background:#fdf8f4;
        }

        .menu-hero{

            width:90%;

            margin:120px auto 50px;

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            border-radius:35px;

            padding:60px;

            color:white;

            position:relative;

            overflow:hidden;
        }

        .menu-hero::before{

            content:'';

            position:absolute;

            width:320px;
            height:320px;

            background:
            rgba(255,255,255,0.15);

            border-radius:50%;

            top:-100px;
            right:-100px;
        }

        .menu-hero h1{

            font-size:3.5rem;

            margin-bottom:15px;

            position:relative;
            z-index:2;
        }

        .menu-hero p{

            line-height:1.8;

            max-width:700px;

            position:relative;
            z-index:2;

            font-size:1.05rem;
        }

        .search-box{

            width:90%;

            margin:0 auto 40px;
        }

        .search-box input{

            width:100%;

            padding:18px 22px;

            border:none;

            border-radius:18px;

            background:white;

            box-shadow:
            0 10px 25px rgba(0,0,0,0.05);

            font-size:1rem;

            transition:0.3s;
        }

        .search-box input:focus{

            outline:none;

            border:2px solid #ff6b35;
        }

        .menu-grid{

            width:90%;

            margin:auto auto 100px;

            display:grid;

            grid-template-columns:
            repeat(auto-fit,minmax(340px,1fr));

            gap:35px;
        }

        .food-card{

            background:white;

            border-radius:30px;

            overflow:hidden;

            box-shadow:
            0 10px 30px rgba(0,0,0,0.08);

            transition:0.4s;

            position:relative;

            border:1px solid rgba(255,255,255,0.4);

            backdrop-filter:blur(10px);
        }

        .food-card:hover{

            transform:
            translateY(-12px)
            scale(1.02);

            box-shadow:
            0 20px 40px rgba(255,107,53,0.15);
        }

        .food-card img{

            width:100%;

            height:240px;

            object-fit:cover;

            transition:0.5s;
        }

        .food-card:hover img{

            transform:scale(1.08);
        }

        .food-content{

            padding:25px;
        }

        .food-top{

            display:flex;

            justify-content:space-between;

            align-items:center;

            margin-bottom:15px;
        }

        .food-top h3{

            font-size:1.5rem;

            color:#111827;
        }

        .food-price{

            color:#ff6b35;

            font-size:1.3rem;

            font-weight:700;
        }

        .food-category{

            display:inline-block;

            background:#fff1e8;

            color:#ff6b35;

            padding:8px 16px;

            border-radius:30px;

            font-size:0.9rem;

            margin-bottom:18px;

            font-weight:600;
        }

        .food-meta{

            display:flex;

            justify-content:space-between;

            margin-bottom:18px;

            color:#6b7280;

            font-size:0.95rem;
        }

        .food-content p{

            color:#6b7280;

            line-height:1.8;

            margin-bottom:22px;
        }

        .cart-form{

            display:flex;

            gap:12px;

            align-items:center;
        }

        .cart-form input{

            width:80px;

            padding:14px;

            border:2px solid #f1f5f9;

            border-radius:14px;

            text-align:center;

            background:#fffaf5;

            font-weight:600;
        }

        .primary-btn{

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            color:white;

            border:none;

            padding:14px 24px;

            border-radius:14px;

            cursor:pointer;

            font-weight:600;

            text-decoration:none;

            transition:0.3s;
        }

        .primary-btn:hover{

            transform:
            translateY(-3px);
        }

        .empty-menu{

            text-align:center;

            padding:100px 20px;
        }

        .empty-menu img{

            width:180px;

            margin-bottom:25px;
        }

        .empty-menu h2{

            margin-bottom:15px;

            color:#111827;
        }

        .empty-menu p{

            color:#6b7280;

            margin-bottom:30px;
        }

        @media(max-width:768px){

            .menu-hero{

                padding:40px 25px;
            }

            .menu-hero h1{

                font-size:2.5rem;
            }

            .menu-grid{

                width:95%;
            }

            .cart-form{

                flex-direction:column;

                align-items:stretch;
            }

            .cart-form input{

                width:100%;
            }
        }

    </style>

</head>

<body>

<?php include '../includes/navbar.php'; ?>

<div class="menu-hero">

    <h1>

        <?php
        echo $restaurant['restaurant_name'];
        ?>

    </h1>

    <p>

        Explore delicious meals freshly
        prepared with premium ingredients.

    </p>

</div>

<div class="search-box">

    <input
        type="text"
        id="searchInput"
        placeholder="Search delicious foods..."
    >

</div>

<div class="menu-grid" id="menuGrid">

<?php

if(mysqli_num_rows($result) > 0){

while($food = mysqli_fetch_assoc($result)){

?>

<div class="food-card">

    <img
    src="../assets/images/foods/<?php echo $food['image']; ?>"
    alt="<?php echo $food['food_name']; ?>"
    loading="lazy"
    >

    <div class="food-content">

        <div class="food-top">

            <h3>
                <?php
                echo $food['food_name'];
                ?>
            </h3>

            <div class="food-price">

                ₹ <?php
                echo $food['price'];
                ?>

            </div>

        </div>

        <div class="food-category">

            <?php
            echo $food['category'];
            ?>

        </div>

        <div class="food-meta">

            <span>⭐ 4.5 Rating</span>

            <span>⏱ 20-30 mins</span>

        </div>

        <p>

            <?php
            echo $food['description'];
            ?>

        </p>

        <form action="cart.php"
              method="POST"
              class="cart-form">

            <input
                type="hidden"
                name="food_id"
                value="<?php
                echo $food['id'];
                ?>"
            >

            <input
                type="hidden"
                name="price"
                value="<?php
                echo $food['price'];
                ?>"
            >

            <input
                type="number"
                name="quantity"
                value="1"
                min="1"
            >

            <button
                type="submit"
                name="add_cart"
                class="primary-btn"
            >

                Add To Cart

            </button>

        </form>

    </div>

</div>

<?php
}
}else{
?>

<div class="empty-menu">

    <img
    src="../assets/images/icons/empty-cart.png"
    alt="Empty Menu"
    >

    <h2>
        No Foods Available
    </h2>

    <p>

        This restaurant has not added
        food items yet.

    </p>

</div>

<?php } ?>

</div>

<?php include '../includes/footer.php'; ?>

<script>

/* =========================
   SEARCH FOOD
========================= */

const searchInput =
document.getElementById('searchInput');

searchInput.addEventListener('keyup', () => {

    const filter =
    searchInput.value.toLowerCase();

    const cards =
    document.querySelectorAll('.food-card');

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