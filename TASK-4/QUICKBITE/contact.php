<?php include 'includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width,
          initial-scale=1.0">

    <title>Contact QuickBite</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

    <style>

        .contact-hero{

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

        .contact-hero::before{

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

        .contact-hero h1{

            font-size:4rem;

            margin-bottom:20px;

            position:relative;
            z-index:2;
        }

        .contact-hero p{

            max-width:700px;

            line-height:1.9;

            font-size:1.1rem;

            position:relative;
            z-index:2;
        }

        .contact-main{

            width:90%;

            margin:auto;
        }

        .contact-grid{

            display:grid;

            grid-template-columns:
            1fr 1fr;

            gap:60px;

            align-items:start;
        }

        .contact-info h2{

            font-size:3rem;

            margin-bottom:25px;

            color:#111827;
        }

        .contact-info span{
            color:#ff6b35;
        }

        .contact-info p{

            color:#6b7280;

            line-height:1.9;

            margin-bottom:25px;
        }

        .info-card{

            background:white;

            padding:25px;

            border-radius:25px;

            margin-bottom:20px;

            box-shadow:
            0 10px 25px rgba(0,0,0,0.06);

            transition:0.4s;
        }

        .info-card:hover{

            transform:
            translateY(-8px);
        }

        .info-card h3{

            margin-bottom:12px;

            color:#ff6b35;
        }

        .info-card p{

            margin:0;

            color:#6b7280;
        }

        .contact-form{

            background:white;

            padding:45px;

            border-radius:35px;

            box-shadow:
            0 15px 40px rgba(0,0,0,0.08);
        }

        .contact-form h2{

            margin-bottom:25px;

            color:#111827;
        }

        .contact-form input,
        .contact-form textarea{

            width:100%;

            padding:16px;

            margin-bottom:20px;

            border:2px solid #f1f5f9;

            border-radius:14px;

            font-size:1rem;
        }

        .contact-form input:focus,
        .contact-form textarea:focus{

            border-color:#ff6b35;

            outline:none;
        }

        .contact-form textarea{

            resize:none;

            height:140px;
        }

        .social-section{

            margin-top:70px;

            text-align:center;
        }

        .social-section h2{

            font-size:2.5rem;

            margin-bottom:35px;
        }

        .social-icons{

            display:flex;

            justify-content:center;

            gap:25px;

            flex-wrap:wrap;
        }

        .social-icons a{

            width:70px;
            height:70px;

            display:flex;
            justify-content:center;
            align-items:center;

            background:white;

            border-radius:50%;

            text-decoration:none;

            font-size:2rem;

            box-shadow:
            0 10px 25px rgba(0,0,0,0.08);

            transition:0.4s;
        }

        .social-icons a:hover{

            transform:
            translateY(-10px);

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            color:white;
        }

        .faq-section{

            margin-top:90px;
        }

        .faq-section h2{

            text-align:center;

            font-size:3rem;

            margin-bottom:50px;
        }

        .faq-grid{

            display:grid;

            grid-template-columns:
            repeat(auto-fit,minmax(320px,1fr));

            gap:30px;
        }

        .faq-card{

            background:white;

            padding:35px;

            border-radius:30px;

            box-shadow:
            0 10px 25px rgba(0,0,0,0.06);
        }

        .faq-card h3{

            margin-bottom:15px;

            color:#111827;
        }

        .faq-card p{

            color:#6b7280;

            line-height:1.8;
        }

        @media(max-width:768px){

            .contact-hero{

                padding:45px 25px;
            }

            .contact-hero h1{

                font-size:2.5rem;
            }

            .contact-grid{

                grid-template-columns:1fr;
            }

            .contact-info h2,
            .faq-section h2{

                font-size:2.3rem;
            }

            .contact-form{

                padding:35px 25px;
            }
        }

    </style>

</head>

<body>

<div class="contact-hero">

    <h1>
        Contact QuickBite 📞
    </h1>

    <p>

        Have questions, suggestions,
        or feedback? Our QuickBite team
        is always ready to help you.

    </p>

</div>

<div class="contact-main">

    <div class="contact-grid">

        <div class="contact-info">

            <h2>

                Get In
                <span>Touch</span>

            </h2>

            <p>

                Reach out to us anytime
                for support, collaborations,
                or project-related queries.

            </p>

            <div class="info-card">

                <h3>
                    📧 Email
                </h3>

                <p>
                    support@quickbite.com
                </p>

            </div>

            <div class="info-card">

                <h3>
                    📞 Phone
                </h3>

                <p>
                    +91 9876543210
                </p>

            </div>

            <div class="info-card">

                <h3>
                    📍 Location
                </h3>

                <p>
                    Hyderabad, India
                </p>

            </div>

        </div>

        <div class="contact-form">

            <h2>
                Send Message
            </h2>

            <form>

                <input
                    type="text"
                    placeholder="Your Name"
                    required
                >

                <input
                    type="email"
                    placeholder="Your Email"
                    required
                >

                <input
                    type="text"
                    placeholder="Subject"
                    required
                >

                <textarea
                    placeholder="Write your message..."
                    required
                ></textarea>

                <button
                    type="submit"
                    class="primary-btn"
                >

                    Send Message

                </button>

            </form>

        </div>

    </div>

    <div class="social-section">

        <h2>
            Follow Us
        </h2>

        <div class="social-icons">

            <a href="#">📘</a>

            <a href="#">📸</a>

            <a href="#">🐦</a>

            <a href="#">💼</a>

        </div>

    </div>

    <div class="faq-section">

        <h2>
            Frequently Asked Questions
        </h2>

        <div class="faq-grid">

            <div class="faq-card">

                <h3>
                    How do I place orders?
                </h3>

                <p>

                    Browse restaurants,
                    select food items,
                    add them to cart,
                    and checkout easily.

                </p>

            </div>

            <div class="faq-card">

                <h3>
                    Is QuickBite responsive?
                </h3>

                <p>

                    Yes, QuickBite works
                    perfectly on desktop,
                    tablet, and mobile devices.

                </p>

            </div>

            <div class="faq-card">

                <h3>
                    Which technologies are used?
                </h3>

                <p>

                    QuickBite is built using
                    HTML, CSS, JavaScript,
                    PHP, and MySQL.

                </p>

            </div>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>