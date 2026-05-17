<?php

include '../config/db.php';

include '../includes/navbar.php';

/* =========================
   FETCH RESTAURANTS
========================= */

$query =
"SELECT * FROM restaurants
 ORDER BY id DESC";

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

    <title>Restaurants</title>

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
        }

        .restaurants-hero{

            width:90%;

            margin:120px auto 50px;

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            border-radius:40px;

            padding:70px;

            color:white;

            position:relative;

            overflow:hidden;
        }

        .restaurants-hero::before{

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

        .restaurants-hero h1{

            font-size:3.8rem;

            margin-bottom:18px;

            position:relative;
            z-index:2;
        }

        .restaurants-hero p{

            max-width:700px;

            line-height:1.9;

            position:relative;
            z-index:2;

            font-size:1.05rem;
        }

        .search-box{

            width:90%;

            margin:0 auto 45px;
        }

        .search-box input{

            width:100%;

            padding:20px 24px;

            border:none;

            border-radius:20px;

            background:white;

            box-shadow:
            0 10px 30px rgba(0,0,0,0.05);

            font-size:1rem;

            transition:0.3s;
        }

        .search-box input:focus{

            outline:none;

            border:2px solid #ff6b35;
        }

        .restaurant-grid{

            width:90%;

            margin:auto auto 100px;

            display:grid;

            grid-template-columns:
            repeat(auto-fit,minmax(340px,1fr));

            gap:35px;
        }

        .restaurant-card{

            background:
            rgba(255,255,255,0.85);

            backdrop-filter:blur(10px);

            border-radius:32px;

            overflow:hidden;

            position:relative;

            box-shadow:
            0 10px 30px rgba(0,0,0,0.08);

            transition:0.4s;

            border:1px solid rgba(255,255,255,0.4);
        }

        .restaurant-card:hover{

            transform:
            translateY(-12px)
            scale(1.02);

            box-shadow:
            0 20px 40px rgba(255,107,53,0.15);
        }

        .restaurant-image{

            position:relative;

            overflow:hidden;
        }

        .restaurant-image img{

            width:100%;

            height:250px;

            object-fit:cover;

            transition:0.5s;
        }

        .restaurant-card:hover img{

            transform:scale(1.08);
        }

        .restaurant-badge{

            position:absolute;

            top:20px;
            left:20px;

            background:white;

            color:#ff6b35;

            padding:8px 16px;

            border-radius:30px;

            font-size:0.9rem;

            font-weight:700;

            box-shadow:
            0 5px 15px rgba(0,0,0,0.08);
        }

        .restaurant-content{

            padding:28px;
        }

        .restaurant-content h3{

            font-size:1.7rem;

            margin-bottom:14px;

            color:#111827;
        }

        .restaurant-location{

            color:#6b7280;

            margin-bottom:22px;

            line-height:1.7;
        }

        .restaurant-info{

            display:flex;

            justify-content:space-between;

            align-items:center;

            margin-bottom:25px;

            flex-wrap:wrap;

            gap:10px;
        }

        .rating{

            background:#fff1e8;

            color:#ff6b35;

            padding:8px 16px;

            border-radius:30px;

            font-size:0.9rem;

            font-weight:700;
        }

        .delivery{

            color:#6b7280;

            font-size:0.95rem;
        }

        .primary-btn{

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

            display:inline-block;

            font-weight:600;

            transition:0.3s;

            box-shadow:
            0 10px 25px rgba(255,107,53,0.25);
        }

        .primary-btn:hover{

            transform:
            translateY(-4px);
        }

        .empty-restaurants{

            text-align:center;

            padding:100px 20px;
        }

        .empty-restaurants img{

            width:220px;

            margin-bottom:25px;
        }

        .empty-restaurants h2{

            margin-bottom:15px;

            color:#111827;
        }

        .empty-restaurants p{

            color:#6b7280;
        }

        @media(max-width:768px){

            .restaurants-hero{

                padding:45px 25px;
            }

            .restaurants-hero h1{

                font-size:2.5rem;
            }

            .restaurant-grid{

                width:95%;
            }
        }

    </style>

</head>

<body>

<div class="restaurants-hero">

    <h1>

        Explore Restaurants 🍔

    </h1>

    <p>

        Discover delicious meals from
        premium restaurants near your campus
        and enjoy seamless online ordering.

    </p>

</div>

<div class="search-box">

    <input
        type="text"
        id="searchInput"
        placeholder="Search restaurants..."
    >

</div>

<?php

if(mysqli_num_rows($result) > 0){
?>

<div class="restaurant-grid" id="restaurantGrid">

<?php
while($row = mysqli_fetch_assoc($result)){
?>

<div class="restaurant-card">

    <div class="restaurant-image">

        <img
        src="../assets/images/restaurants/<?php echo $row['image']; ?>"
        alt="<?php echo $row['restaurant_name']; ?>"
        >

        <div class="restaurant-badge">

            Popular

        </div>

    </div>

    <div class="restaurant-content">

        <h3>

            <?php
            echo $row['restaurant_name'];
            ?>

        </h3>

        <p class="restaurant-location">

            📍 <?php
            echo $row['location'];
            ?>

        </p>

        <div class="restaurant-info">

            <div class="rating">

                ⭐ 4.5 Rating

            </div>

            <div class="delivery">

                ⏱ 20-30 mins

            </div>

        </div>

        <a href="menu.php?id=<?php
           echo $row['id'];
           ?>"
           class="primary-btn">

           View Menu

        </a>

    </div>

</div>

<?php } ?>

</div>

<?php } else { ?>

<div class="empty-restaurants">

    <img
    src="../assets/images/icons/empty-cart.png"
    alt="No Restaurants"
    >

    <h2>

        No Restaurants Found

    </h2>

    <p>

        Restaurants will appear here
        once added by admin.

    </p>

</div>

<?php } ?>

<?php include '../includes/footer.php'; ?>

<script>

/* =========================
   SEARCH RESTAURANTS
========================= */

const searchInput =
document.getElementById('searchInput');

searchInput.addEventListener('keyup', () => {

    const filter =
    searchInput.value.toLowerCase();

    const cards =
    document.querySelectorAll('.restaurant-card');

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