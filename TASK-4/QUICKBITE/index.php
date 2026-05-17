<?php include 'includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>QuickBite - Food Delivery</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

    <style>

        .hero{

            width:100%;
            min-height:100vh;

            display:flex;
            justify-content:center;
            align-items:center;
            gap:60px;

            padding:140px 8% 80px;

            background:
            linear-gradient(
                to right,
                #fff7ed,
                #ffffff
            );

            position:relative;

            overflow:hidden;
        }

        .hero::before{

            content:'';

            position:absolute;

            width:500px;
            height:500px;

            background:
            rgba(255,107,53,0.10);

            border-radius:50%;

            top:-150px;
            right:-120px;

            filter:blur(80px);
        }

        .hero::after{

            content:'';

            position:absolute;

            width:350px;
            height:350px;

            background:
            rgba(255,159,28,0.10);

            border-radius:50%;

            bottom:-120px;
            left:-120px;

            filter:blur(70px);
        }

        .hero-content{

            flex:1;

            z-index:2;
        }

        .hero-badge{

            display:inline-block;

            background:#fff1e6;

            color:#ff6b35;

            padding:10px 20px;

            border-radius:50px;

            font-weight:600;

            margin-bottom:25px;
        }

        .hero-content h1{

            font-size:5rem;

            line-height:1.1;

            margin-bottom:25px;

            color:#0f172a;
        }

        .hero-content span{
            color:#ff6b35;
        }

        .hero-content p{

            color:#6b7280;

            font-size:1.15rem;

            max-width:600px;

            line-height:1.9;

            margin-bottom:40px;
        }

        .hero-buttons{

            display:flex;

            gap:20px;

            flex-wrap:wrap;
        }

        .primary-btn{

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            color:white;

            padding:16px 34px;

            border-radius:16px;

            text-decoration:none;

            font-weight:600;

            box-shadow:
            0 10px 25px rgba(255,107,53,0.25);

            transition:0.4s;
        }

        .primary-btn:hover{

            transform:
            translateY(-5px);
        }

        .secondary-btn{

            background:white;

            color:#111827;

            padding:16px 34px;

            border-radius:16px;

            text-decoration:none;

            font-weight:600;

            border:1px solid #f1f5f9;

            transition:0.4s;
        }

        .secondary-btn:hover{

            background:#fff7ed;
        }

        .hero-image{

            flex:1;

            text-align:center;

            z-index:2;
        }

        .hero-image img{

            width:100%;
            max-width:620px;

            animation:
            floatAnimation 4s ease-in-out infinite;
        }

        @keyframes floatAnimation{

            0%{
                transform:translateY(0px);
            }

            50%{
                transform:translateY(-15px);
            }

            100%{
                transform:translateY(0px);
            }
        }

        .section{

            width:90%;

            margin:100px auto;
        }

        .section-title{

            text-align:center;

            font-size:3rem;

            margin-bottom:20px;

            color:#111827;
        }

        .section-subtitle{

            text-align:center;

            color:#6b7280;

            max-width:700px;

            margin:auto auto 60px;

            line-height:1.8;
        }

        .restaurant-grid{

            display:grid;

            grid-template-columns:
            repeat(auto-fit,minmax(320px,1fr));

            gap:35px;
        }

        .restaurant-card{

            background:white;

            border-radius:30px;

            overflow:hidden;

            box-shadow:
            0 10px 35px rgba(0,0,0,0.08);

            transition:0.4s;

            position:relative;
        }

        .restaurant-card:hover{

            transform:
            translateY(-12px);
        }

        .restaurant-card img{

            width:100%;

            height:260px;

            object-fit:cover;
        }

        .restaurant-content{

            padding:28px;
        }

        .restaurant-content h3{

            font-size:1.7rem;

            margin-bottom:12px;

            color:#111827;
        }

        .restaurant-content p{

            color:#6b7280;

            margin-bottom:25px;

            line-height:1.7;
        }

        .restaurant-info{

            display:flex;

            justify-content:space-between;

            margin-bottom:20px;

            color:#6b7280;

            font-size:0.95rem;
        }

        .rating{

            background:#fff7ed;

            color:#ff6b35;

            padding:8px 14px;

            border-radius:30px;

            font-weight:600;

            display:inline-block;

            margin-bottom:20px;
        }

        .features-grid{

            display:grid;

            grid-template-columns:
            repeat(auto-fit,minmax(250px,1fr));

            gap:35px;
        }

        .feature-card{

            background:white;

            padding:40px;

            border-radius:30px;

            text-align:center;

            box-shadow:
            0 10px 30px rgba(0,0,0,0.06);

            transition:0.4s;
        }

        .feature-card:hover{

            transform:
            translateY(-10px);
        }

        .feature-icon{

            width:95px;
            height:95px;

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

        .feature-card h3{

            margin-bottom:15px;

            font-size:1.4rem;
        }

        .feature-card p{

            color:#6b7280;

            line-height:1.8;
        }

        .stats-grid{

            display:grid;

            grid-template-columns:
            repeat(auto-fit,minmax(220px,1fr));

            gap:30px;
        }

        .stats-card{

            background:white;

            padding:40px;

            border-radius:30px;

            text-align:center;

            box-shadow:
            0 10px 25px rgba(0,0,0,0.06);
        }

        .stats-card h2{

            font-size:3.2rem;

            color:#ff6b35;

            margin-bottom:10px;
        }

        .stats-card p{

            color:#6b7280;

            font-size:1rem;
        }

        .cta-section{

            width:90%;

            margin:100px auto;

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            border-radius:40px;

            padding:80px 50px;

            text-align:center;

            color:white;

            position:relative;

            overflow:hidden;
        }

        .cta-section::before{

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

        .cta-section h2{

            font-size:3rem;

            margin-bottom:20px;

            position:relative;
            z-index:2;
        }

        .cta-section p{

            max-width:750px;

            margin:auto auto 35px;

            line-height:1.9;

            position:relative;
            z-index:2;
        }

        .cta-btn{

            background:white;

            color:#ff6b35;

            padding:18px 36px;

            border-radius:16px;

            text-decoration:none;

            font-weight:700;

            display:inline-block;

            position:relative;
            z-index:2;
        }

        @media(max-width:992px){

            .hero{

                flex-direction:column;

                text-align:center;
            }

            .hero-content h1{

                font-size:3.5rem;
            }

            .hero-content p{

                margin:auto auto 35px;
            }

            .hero-buttons{

                justify-content:center;
            }
        }

        @media(max-width:768px){

            .hero{

                padding-top:150px;
            }

            .hero-content h1{

                font-size:2.7rem;
            }

            .section-title,
            .cta-section h2{

                font-size:2.2rem;
            }

            .cta-section{

                padding:50px 25px;
            }
        }

    </style>

</head>

<body>

<section class="hero">

    <div class="hero-content">

        <div class="hero-badge">
            🍔 Fastest Food Delivery Platform
        </div>

        <h1>

            Delicious Food
            <span>Delivered Fast</span>

        </h1>

        <p>

            Order delicious meals from top restaurants near your campus.
            Experience premium food ordering with fast delivery and modern design.

        </p>

        <div class="hero-buttons">

            <a href="user/restaurants.php"
               class="primary-btn">

               Explore Restaurants

            </a>

            <a href="auth/register.php"
               class="secondary-btn">

               Get Started

            </a>

        </div>

    </div>

    <div class="hero-image">

        <img
        src="assets/images/banners/hero-food.jpg"
        alt="Food Delivery">

    </div>

</section>

<section class="section">

    <h2 class="section-title">
        Popular Restaurants 🍔
    </h2>

    <p class="section-subtitle">

        Explore premium restaurants with delicious meals,
        fast delivery, and modern dining experiences.

    </p>

    <div class="restaurant-grid">

        <div class="restaurant-card">

            <img
            src="assets/images/restaurants/rest1.jpg"
            alt="Burger King">

            <div class="restaurant-content">

                <div class="rating">
                    ⭐ 4.8 Rating
                </div>

                <h3>Burger King</h3>

                <p>
                    Delicious burgers, crispy fries,
                    and premium fast food meals.
                </p>

                <div class="restaurant-info">

                    <span>📍 Hyderabad</span>

                    <span>⏱ 20-30 mins</span>

                </div>

                <a href="user/restaurants.php"
                   class="primary-btn">

                   View Menu

                </a>

            </div>

        </div>

        <div class="restaurant-card">

            <img
            src="assets/images/restaurants/rest2.jpg"
            alt="Pizza Hub">

            <div class="restaurant-content">

                <div class="rating">
                    ⭐ 4.7 Rating
                </div>

                <h3>Pizza Hub</h3>

                <p>
                    Hot cheesy pizzas loaded
                    with premium toppings.
                </p>

                <div class="restaurant-info">

                    <span>📍 Vijayawada</span>

                    <span>⏱ 25-35 mins</span>

                </div>

                <a href="user/restaurants.php"
                   class="primary-btn">

                   View Menu

                </a>

            </div>

        </div>

        <div class="restaurant-card">

            <img
            src="assets/images/restaurants/rest3.jpg"
            alt="Biryani House">

            <div class="restaurant-content">

                <div class="rating">
                    ⭐ 4.9 Rating
                </div>

                <h3>Biryani House</h3>

                <p>
                    Authentic spicy biryani
                    with rich traditional flavors.
                </p>

                <div class="restaurant-info">

                    <span>📍 Guntur</span>

                    <span>⏱ 30-40 mins</span>

                </div>

                <a href="user/restaurants.php"
                   class="primary-btn">

                   View Menu

                </a>

            </div>

        </div>

    </div>

</section>

<section class="section">

    <h2 class="section-title">
        Why Choose QuickBite?
    </h2>

    <p class="section-subtitle">

        QuickBite provides modern online food ordering
        experiences with speed, quality, and convenience.

    </p>

    <div class="features-grid">

        <div class="feature-card">

            <div class="feature-icon">
                ⚡
            </div>

            <h3>Fast Delivery</h3>

            <p>
                Get your favorite meals delivered quickly and fresh.
            </p>

        </div>

        <div class="feature-card">

            <div class="feature-icon">
                🍔
            </div>

            <h3>Delicious Food</h3>

            <p>
                Explore restaurants with premium quality meals.
            </p>

        </div>

        <div class="feature-card">

            <div class="feature-icon">
                🛒
            </div>

            <h3>Easy Ordering</h3>

            <p>
                Add items to cart and order seamlessly.
            </p>

        </div>

        <div class="feature-card">

            <div class="feature-icon">
                📱
            </div>

            <h3>Responsive Design</h3>

            <p>
                Works perfectly on desktop, tablet, and mobile devices.
            </p>

        </div>

    </div>

</section>

<section class="section">

    <div class="stats-grid">

        <div class="stats-card">

            <h2>50+</h2>

            <p>Food Items</p>

        </div>

        <div class="stats-card">

            <h2>10+</h2>

            <p>Restaurants</p>

        </div>

        <div class="stats-card">

            <h2>100+</h2>

            <p>Orders Served</p>

        </div>

        <div class="stats-card">

            <h2>24/7</h2>

            <p>Availability</p>

        </div>

    </div>

</section>

<section class="cta-section">

    <h2>
        Ready To Order Delicious Food?
    </h2>

    <p>

        Join QuickBite today and enjoy
        fast, modern, and seamless
        food ordering experiences.

    </p>

    <a href="auth/register.php"
       class="cta-btn">

       Get Started

    </a>

</section>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>

</body>
</html>