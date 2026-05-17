<?php include './includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width,
          initial-scale=1.0">

    <title>About QuickBite</title>

    <link rel="stylesheet"
          href="./assets/css/style.css">

    <style>

        .about-hero{

            width:90%;

            margin:120px auto 60px;

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            border-radius:35px;

            padding:70px;

            color:white;

            position:relative;

            overflow:hidden;
        }

        .about-hero::before{

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

        .about-hero h1{

            font-size:4rem;

            margin-bottom:20px;

            position:relative;
            z-index:2;
        }

        .about-hero p{

            max-width:700px;

            line-height:1.9;

            font-size:1.1rem;

            position:relative;
            z-index:2;
        }

        .about-main{

            width:90%;

            margin:auto;
        }

        .about-grid{

            display:grid;

            grid-template-columns:
            1fr 1fr;

            gap:60px;

            align-items:center;

            margin-bottom:80px;
        }

        .about-image img{

            width:100%;

            height:500px;

            object-fit:cover;

            border-radius:35px;

            display:block;

            box-shadow:
            0 15px 40px rgba(0,0,0,0.08);
        }

        .about-text h2{

            font-size:3rem;

            margin-bottom:25px;

            color:#111827;

            line-height:1.3;
        }

        .about-text span{
            color:#ff6b35;
        }

        .about-text p{

            color:#6b7280;

            line-height:1.9;

            margin-bottom:20px;

            font-size:1.05rem;
        }

        .features-section{

            margin-top:40px;
        }

        .features-section h2{

            text-align:center;

            font-size:3rem;

            margin-bottom:50px;
        }

        .features-grid{

            display:grid;

            grid-template-columns:
            repeat(auto-fit,minmax(260px,1fr));

            gap:35px;
        }

        .feature-card{

            background:white;

            padding:40px;

            border-radius:30px;

            text-align:center;

            box-shadow:
            0 10px 30px rgba(0,0,0,0.08);

            transition:0.4s ease;
        }

        .feature-card:hover{

            transform:
            translateY(-12px);

            box-shadow:
            0 20px 45px rgba(255,107,53,0.15);
        }

        .feature-icon{

            width:90px;
            height:90px;

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

            font-size:2rem;

            color:white;
        }

        .feature-card h3{

            margin-bottom:15px;

            color:#111827;

            font-size:1.4rem;
        }

        .feature-card p{

            color:#6b7280;

            line-height:1.8;
        }

        .stats-section{

            margin-top:90px;
        }

        .stats-grid{

            display:grid;

            grid-template-columns:
            repeat(auto-fit,minmax(220px,1fr));

            gap:30px;
        }

        .stats-card{

            background:white;

            padding:35px;

            border-radius:30px;

            text-align:center;

            box-shadow:
            0 10px 25px rgba(0,0,0,0.06);

            transition:0.3s ease;
        }

        .stats-card:hover{

            transform:translateY(-8px);
        }

        .stats-card h2{

            font-size:3rem;

            color:#ff6b35;

            margin-bottom:10px;
        }

        .stats-card p{

            color:#6b7280;
        }

        .tech-section{

            margin-top:90px;

            text-align:center;
        }

        .tech-section h2{

            font-size:3rem;

            margin-bottom:50px;
        }

        .tech-grid{

            display:flex;

            justify-content:center;

            gap:20px;

            flex-wrap:wrap;
        }

        .tech-badge{

            background:white;

            padding:16px 26px;

            border-radius:40px;

            box-shadow:
            0 8px 20px rgba(0,0,0,0.06);

            font-weight:600;

            color:#111827;

            transition:0.3s ease;
        }

        .tech-badge:hover{

            transform:translateY(-5px);

            background:#ff6b35;

            color:white;
        }

        @media(max-width:992px){

            .about-grid{

                grid-template-columns:1fr;

                gap:40px;
            }

            .about-image img{

                height:400px;
            }
        }

        @media(max-width:768px){

            .about-hero{

                padding:45px 25px;
            }

            .about-hero h1{

                font-size:2.6rem;
            }

            .about-text h2,
            .features-section h2,
            .tech-section h2{

                font-size:2.3rem;
            }

            .about-image img{

                height:320px;
            }
        }

        @media(max-width:480px){

            .about-hero h1{

                font-size:2rem;
            }

            .about-text h2,
            .features-section h2,
            .tech-section h2{

                font-size:1.9rem;
            }

            .about-hero{

                padding:35px 20px;
            }

            .about-image img{

                height:250px;
            }
        }

    </style>

</head>

<body>

<div class="about-hero">

    <h1>
        About QuickBite 🚀
    </h1>

    <p>

        QuickBite is a modern food ordering
        and restaurant management platform
        designed to simplify food ordering
        experiences for students and users.

    </p>

</div>

<div class="about-main">

    <div class="about-grid">

        <div class="about-image">

            <img
            src="./assets/images/banners/hero-food.jpg"
            alt="About QuickBite"
            >

        </div>

        <div class="about-text">

            <h2>

                Modern Food
                <span>Ordering Experience</span>

            </h2>

            <p>

                QuickBite allows users to
                explore restaurants, browse
                delicious food items, add
                meals to cart, and place
                orders seamlessly.

            </p>

            <p>

                This project was developed
                using PHP, MySQL, HTML,
                CSS, and JavaScript with
                modern responsive UI design.

            </p>

            <a href="./user/restaurants.php"
               class="primary-btn">

               Explore Restaurants

            </a>

        </div>

    </div>

    <div class="features-section">

        <h2>
            Why Choose QuickBite?
        </h2>

        <div class="features-grid">

            <div class="feature-card">

                <div class="feature-icon">
                    🍔
                </div>

                <h3>
                    Fast Ordering
                </h3>

                <p>

                    Order food quickly
                    with seamless user
                    experience.

                </p>

            </div>

            <div class="feature-card">

                <div class="feature-icon">
                    ⚡
                </div>

                <h3>
                    Modern UI
                </h3>

                <p>

                    Beautiful responsive
                    interface with modern
                    design principles.

                </p>

            </div>

            <div class="feature-card">

                <div class="feature-icon">
                    🛒
                </div>

                <h3>
                    Easy Cart System
                </h3>

                <p>

                    Add items to cart and
                    manage orders easily.

                </p>

            </div>

            <div class="feature-card">

                <div class="feature-icon">
                    🔒
                </div>

                <h3>
                    Secure Login
                </h3>

                <p>

                    User authentication
                    with password security.

                </p>

            </div>

        </div>

    </div>

    <div class="stats-section">

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

    </div>

    <div class="tech-section">

        <h2>
            Technologies Used
        </h2>

        <div class="tech-grid">

            <div class="tech-badge">
                HTML5
            </div>

            <div class="tech-badge">
                CSS3
            </div>

            <div class="tech-badge">
                JavaScript
            </div>

            <div class="tech-badge">
                PHP
            </div>

            <div class="tech-badge">
                MySQL
            </div>

            <div class="tech-badge">
                Responsive Design
            </div>

        </div>

    </div>

</div>

<?php include './includes/footer.php'; ?>

</body>
</html>