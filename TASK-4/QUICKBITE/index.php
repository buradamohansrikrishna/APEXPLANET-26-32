<?php
session_start();
include 'config/db.php';
include 'includes/security.php';
include 'includes/functions.php';

// Fetch featured restaurants
$restaurants = db_fetch_all($conn, "SELECT * FROM restaurants WHERE status='active' ORDER BY rating DESC LIMIT 6");

// Fetch trending foods
$trendingFoods = db_fetch_all($conn, "SELECT f.*, r.restaurant_name FROM foods f JOIN restaurants r ON f.restaurant_id=r.id WHERE f.availability='available' ORDER BY f.total_orders DESC, f.rating DESC LIMIT 8");

// Stats
$stats = [
    'restaurants' => db_count($conn, 'restaurants'),
    'foods'       => db_count($conn, 'foods'),
    'users'       => db_count($conn, 'users'),
    'orders'      => db_count($conn, 'orders'),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="QuickBite — Order delicious food from the best restaurants near you. Fast delivery, premium experience.">
    <title>QuickBite — Delicious Food Delivered Fast 🍔</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="manifest" href="manifest.json">
    <style>
    /* ── HERO ── */
    .hero-section {
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: calc(var(--navbar-h) + 40px) 5% 80px;
        position: relative;
        overflow: hidden;
        background: radial-gradient(ellipse 80% 60% at 50% -20%, rgba(255,71,71,0.12) 0%, transparent 60%),
                    radial-gradient(ellipse 60% 50% at 80% 80%, rgba(157,78,221,0.08) 0%, transparent 60%);
    }
    .hero-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        width: 100%;
        max-width: 1280px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }
    .hero-title {
        font-family: var(--font-heading);
        font-size: clamp(2.5rem, 5vw, 4.5rem);
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 24px;
    }
    .hero-title .line1 { color: #0F172A; }
    .hero-title .line2 {
        color: #FF4747;
        background: linear-gradient(90deg, #FF4747, #3A86FF, #9D4EDD);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .hero-desc {
        color: var(--text-secondary);
        font-size: 1.1rem;
        line-height: 1.8;
        max-width: 520px;
        margin-bottom: 36px;
    }
    .hero-buttons { display: flex; gap: 16px; flex-wrap: wrap; }
    .hero-pills {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 40px;
    }
    .hero-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-glass);
        border: 1px solid var(--bg-glass-border);
        border-radius: var(--radius-full);
        padding: 8px 16px;
        font-size: 0.85rem;
        color: var(--text-secondary);
    }
    .hero-pill span:first-child { font-size: 1rem; }

    /* Hero visual */
    .hero-visual {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .hero-ring {
        width: 460px;
        height: 460px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,71,71,0.06) 0%, transparent 70%);
        border: 1px solid rgba(255,71,71,0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        animation: float-bob 6s ease-in-out infinite;
    }
    .hero-ring-inner {
        width: 360px;
        height: 360px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(58,134,255,0.08) 0%, transparent 70%);
        border: 1px solid rgba(58,134,255,0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8rem;
        animation: float-bob 4s ease-in-out infinite reverse;
    }
    .hero-float-card {
        position: absolute;
        background: var(--bg-secondary);
        border: 1px solid var(--bg-glass-border);
        border-radius: var(--radius-md);
        padding: 12px 16px;
        font-size: 0.82rem;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        box-shadow: var(--shadow-md);
        animation: float-bob 5s ease-in-out infinite;
    }
    .hero-float-card:nth-child(1) { top: 30px; left: -20px; animation-delay: 0s; }
    .hero-float-card:nth-child(2) { bottom: 60px; right: -10px; animation-delay: 1.5s; }
    .hero-float-card:nth-child(3) { top: 50%; right: -30px; animation-delay: 0.8s; }
    .float-card-icon { font-size: 1.4rem; }
    .float-card-label { color: var(--text-muted); font-size: 0.72rem; }
    .float-card-value { font-weight: 700; color: #0F172A; }

    /* ── STATS SECTION ── */
    .stats-section { padding: 0 5% 80px; }
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        max-width: 1280px;
        margin: 0 auto;
    }
    .stats-item {
        text-align: center;
        background: var(--bg-card);
        border: 1px solid var(--bg-glass-border);
        border-radius: var(--radius-lg);
        padding: 32px 20px;
        transition: var(--transition-base);
    }
    .stats-item:hover { border-color: rgba(255,71,71,0.3); transform: translateY(-4px); }
    .stats-num {
        font-family: var(--font-heading);
        font-size: 2.8rem;
        font-weight: 800;
        color: #FF5A00;
        background: var(--grad-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 6px;
    }
    .stats-label { color: var(--text-secondary); font-size: 0.9rem; }

    /* ── CATEGORIES ── */
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px,1fr));
        gap: 16px;
        max-width: 1280px;
        margin: 0 auto;
    }
    .category-card {
        background: var(--bg-card);
        border: 1px solid var(--bg-glass-border);
        border-radius: var(--radius-lg);
        padding: 24px 16px;
        text-align: center;
        cursor: pointer;
        transition: var(--transition-base);
    }
    .category-card:hover {
        border-color: rgba(255,71,71,0.4);
        background: rgba(255,71,71,0.04);
        transform: translateY(-6px);
        box-shadow: var(--glow-cyan);
    }
    .category-card .cat-icon { font-size: 2.2rem; margin-bottom: 8px; }
    .category-card .cat-name { font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); }

    /* ── RESTAURANT CARDS ── */
    .rest-card {
        background: var(--bg-card);
        border: 1px solid var(--bg-glass-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: var(--transition-base);
    }
    .rest-card:hover {
        border-color: rgba(255,71,71,0.25);
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(255,71,71,0.06);
    }
    .rest-card img {
        width: 100%; height: 200px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .rest-card:hover img { transform: scale(1.06); }
    .rest-card-body { padding: 20px; }
    .rest-card-name { font-family: var(--font-heading); font-weight: 700; font-size: 1.15rem; margin-bottom: 6px; }
    .rest-card-meta { display: flex; gap: 12px; align-items: center; margin-bottom: 14px; flex-wrap: wrap; }
    .rest-card-desc { color: var(--text-secondary); font-size: 0.88rem; margin-bottom: 16px; line-height: 1.6; }

    /* ── HOW IT WORKS ── */
    .hiw-grid {
        display: grid;
        grid-template-columns: repeat(3,1fr);
        gap: 32px;
        max-width: 1280px;
        margin: 0 auto;
        position: relative;
    }
    .hiw-grid::before {
        content: '';
        position: absolute;
        top: 40px; left: 16.6%; right: 16.6%;
        height: 2px;
        background: linear-gradient(90deg, var(--neon-cyan), var(--electric-blue), var(--neon-purple));
        opacity: 0.3;
    }
    .hiw-card {
        text-align: center;
        padding: 32px 24px;
        background: var(--bg-card);
        border: 1px solid var(--bg-glass-border);
        border-radius: var(--radius-xl);
        position: relative;
        transition: var(--transition-base);
    }
    .hiw-card:hover { border-color: rgba(255,71,71,0.3); transform: translateY(-6px); }
    .hiw-num {
        position: absolute;
        top: -16px; left: 50%; transform: translateX(-50%);
        width: 32px; height: 32px;
        background: var(--grad-primary);
        color: var(--bg-dark);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem; font-weight: 800;
    }
    .hiw-icon { font-size: 3rem; margin-bottom: 16px; display: block; }
    .hiw-title { font-family: var(--font-heading); font-weight: 700; font-size: 1.1rem; margin-bottom: 10px; }
    .hiw-desc { color: var(--text-secondary); font-size: 0.88rem; line-height: 1.7; }

    /* ── TESTIMONIALS ── */
    .testimonials-wrap { position: relative; overflow: hidden; max-width: 1280px; margin: 0 auto; }
    .carousel-track { display: flex; transition: transform 0.5s cubic-bezier(0.4,0,0.2,1); }
    .carousel-slide { min-width: 100%; }
    .testi-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 24px; }
    .testi-card {
        background: var(--bg-card);
        border: 1px solid var(--bg-glass-border);
        border-radius: var(--radius-lg);
        padding: 28px;
        transition: var(--transition-base);
    }
    .testi-card:hover { border-color: rgba(255,71,71,0.2); }
    .testi-stars { color: #F59E0B; font-size: 0.95rem; margin-bottom: 14px; }
    .testi-text { color: var(--text-secondary); font-size: 0.9rem; line-height: 1.8; margin-bottom: 20px; font-style: italic; }
    .testi-author { display: flex; align-items: center; gap: 12px; }
    .testi-avatar {
        width: 44px; height: 44px;
        border-radius: 50%;
        background: var(--grad-primary);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; font-weight: 700;
        color: var(--bg-dark); flex-shrink: 0;
    }
    .testi-name { font-weight: 600; font-size: 0.9rem; }
    .testi-loc { color: var(--text-muted); font-size: 0.78rem; }

    /* ── CTA ── */
    .cta-section {
        max-width: 1280px;
        margin: 0 auto 80px;
        background: linear-gradient(135deg, rgba(255,71,71,0.08), rgba(157,78,221,0.08));
        border: 1px solid rgba(255,71,71,0.2);
        border-radius: var(--radius-xl);
        padding: 80px 60px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .cta-section::before {
        content: '';
        position: absolute; top: -100px; right: -100px;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(255,71,71,0.15) 0%, transparent 70%);
        pointer-events: none;
    }
    .cta-section::after {
        content: '';
        position: absolute; bottom: -80px; left: -80px;
        width: 250px; height: 250px;
        background: radial-gradient(circle, rgba(157,78,221,0.15) 0%, transparent 70%);
        pointer-events: none;
    }
    .cta-title {
        font-family: var(--font-heading);
        font-size: clamp(2rem,4vw,3rem);
        font-weight: 800;
        margin-bottom: 16px;
    }
    .cta-desc { color: var(--text-secondary); font-size: 1.05rem; max-width: 600px; margin: 0 auto 32px; }
    .cta-buttons { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; position: relative; z-index: 2; }

    @media(max-width:768px) {
        .hero-grid { grid-template-columns:1fr; gap:40px; }
        .hero-visual { display:none; }
        .stats-row { grid-template-columns:repeat(2,1fr); }
        .hiw-grid { grid-template-columns:1fr; }
        .hiw-grid::before { display:none; }
        .testi-grid { grid-template-columns:1fr; }
        .cta-section { padding:50px 30px; }
    }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- ════════════════════════════════════════════
     HERO
════════════════════════════════════════════ -->
<section class="hero-section">
    <div class="particles-bg"></div>

    <div class="hero-grid">
        <div class="hero-content" data-reveal="up">
            <div class="section-badge">🚀 #1 Food Delivery Platform</div>
            <h1 class="hero-title">
                <span class="line1">Delicious Food</span><br>
                <span class="line2">Delivered Fast ⚡</span>
            </h1>
            <p class="hero-desc">
                Order from the best restaurants near you. Lightning-fast delivery, 
                real-time tracking, and a premium dining experience — all in one place.
            </p>
            <div class="hero-buttons">
                <a href="user/restaurants.php" class="btn btn-primary btn-lg">
                    🍽️ Explore Restaurants
                </a>
                <a href="auth/register.php" class="btn btn-secondary btn-lg">
                    ✨ Get Started Free
                </a>
            </div>
            <div class="hero-pills">
                <div class="hero-pill"><span>⚡</span><span>30-min Delivery</span></div>
                <div class="hero-pill"><span>🔒</span><span>Secure Payments</span></div>
                <div class="hero-pill"><span>⭐</span><span>4.9 Rating</span></div>
            </div>
        </div>

        <div class="hero-visual" data-reveal="right" data-delay="0.2s">
            <div class="hero-ring">
                <div class="hero-ring-inner">🍔</div>
            </div>
            <div class="hero-float-card animate-fade-up delay-300">
                <span class="float-card-icon">📦</span>
                <div>
                    <div class="float-card-label">Live Order</div>
                    <div class="float-card-value">Out for Delivery 🛵</div>
                </div>
            </div>
            <div class="hero-float-card animate-fade-up delay-500">
                <span class="float-card-icon">⭐</span>
                <div>
                    <div class="float-card-label">Average Rating</div>
                    <div class="float-card-value">4.9 / 5.0</div>
                </div>
            </div>
            <div class="hero-float-card animate-fade-up delay-400">
                <span class="float-card-icon">🏃</span>
                <div>
                    <div class="float-card-label">Delivery Time</div>
                    <div class="float-card-value">≈ 28 Minutes</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════
     STATS
════════════════════════════════════════════ -->
<section class="stats-section">
    <div class="stats-row">
        <div class="stats-item" data-reveal="up">
            <div class="stats-num" data-counter data-target="<?= max($stats['restaurants'], 50) ?>" data-suffix="+">0</div>
            <div class="stats-label">🏪 Restaurants</div>
        </div>
        <div class="stats-item" data-reveal="up" data-delay="0.1s">
            <div class="stats-num" data-counter data-target="<?= max($stats['foods'], 200) ?>" data-suffix="+">0</div>
            <div class="stats-label">🍔 Menu Items</div>
        </div>
        <div class="stats-item" data-reveal="up" data-delay="0.2s">
            <div class="stats-num" data-counter data-target="<?= max($stats['orders'], 5000) ?>" data-suffix="+">0</div>
            <div class="stats-label">📦 Orders Delivered</div>
        </div>
        <div class="stats-item" data-reveal="up" data-delay="0.3s">
            <div class="stats-num" data-counter data-target="<?= max($stats['users'], 1000) ?>" data-suffix="+">0</div>
            <div class="stats-label">😊 Happy Customers</div>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════
     CATEGORIES
════════════════════════════════════════════ -->
<section class="section" style="padding-top:60px">
    <div class="container">
        <div class="section-header" data-reveal="up">
            <div class="section-badge">🍽️ Browse By Type</div>
            <h2 class="section-title">Popular <span class="highlight">Categories</span></h2>
            <p class="section-subtitle">Find your favorite cuisine from our wide selection of food categories</p>
        </div>
        <div class="categories-grid">
            <?php
            $cats = [
                ['🍔','Burgers'],['🍕','Pizza'],['🍛','Biryani'],['🌮','Wraps'],
                ['🍜','Noodles'],['🥗','Salads'],['🍦','Desserts'],['🧃','Beverages'],
                ['🐔','Chicken'],['🌿','Vegan'],['🥞','Breakfast'],['🦐','Seafood'],
            ];
            foreach ($cats as $i => $cat):
            ?>
            <a href="user/restaurants.php?category=<?= urlencode($cat[1]) ?>"
               class="category-card" data-reveal="scale" data-delay="<?= $i * 0.04 ?>s">
                <div class="cat-icon"><?= $cat[0] ?></div>
                <div class="cat-name"><?= $cat[1] ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════
     FEATURED RESTAURANTS
════════════════════════════════════════════ -->
<section class="section">
    <div class="container">
        <div class="section-header" data-reveal="up">
            <div class="section-badge">🏪 Top Picks</div>
            <h2 class="section-title">Featured <span class="highlight">Restaurants</span></h2>
            <p class="section-subtitle">Explore our handpicked selection of top-rated restaurants delivering right to your door</p>
        </div>
        <div class="auto-grid-md">
            <?php if ($restaurants): foreach ($restaurants as $i => $r): ?>
            <div class="rest-card" data-reveal="up" data-delay="<?= $i * 0.1 ?>s">
                <div style="overflow:hidden">
                    <img src="assets/images/restaurants/<?= e($r['image'] ?? 'rest1.jpg') ?>"
                         alt="<?= e($r['restaurant_name']) ?>"
                         onerror="this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&q=80'">
                </div>
                <div class="rest-card-body">
                    <h3 class="rest-card-name"><?= e($r['restaurant_name']) ?></h3>
                    <div class="rest-card-meta">
                        <span class="rating">⭐ <?= e($r['rating']) ?></span>
                        <span class="badge <?= $r['status']==='active'?'badge-active':'badge-inactive' ?>">
                            <?= $r['status']==='active' ? '🟢 Open' : '🔴 Closed' ?>
                        </span>
                        <span style="color:var(--text-muted);font-size:0.82rem">⏱ <?= e($r['delivery_time']) ?></span>
                    </div>
                    <p class="rest-card-desc"><?= e(substr($r['description'] ?? 'Delicious meals prepared with fresh ingredients.',0,90)) ?>...</p>
                    <a href="user/menu.php?id=<?= $r['id'] ?>" class="btn btn-primary btn-sm" style="width:100%">
                        View Menu →
                    </a>
                </div>
            </div>
            <?php endforeach; else: ?>
            <?php
            $defaults = [
                ['Burger King','🍔','Fast Food','4.8','20-30 mins','Flame-grilled burgers with premium toppings and crispy fries.'],
                ['Pizza Hub','🍕','Italian','4.7','25-35 mins','Authentic Italian-style pizzas with fresh handmade dough.'],
                ['Biryani House','🍛','Indian','4.9','30-40 mins','Hyderabadi Dum Biryani — the real deal since 1965.'],
                ['Sushi Palace','🍱','Japanese','4.6','25-30 mins','Fresh sushi rolls and authentic Japanese cuisine.'],
                ['Taco Town','🌮','Mexican','4.5','20-25 mins','Crispy tacos, burritos and authentic Mexican flavors.'],
                ['Noodle House','🍜','Chinese','4.7','15-25 mins','Hand-pulled noodles in rich broths, dumplings and dim sum.'],
            ];
            foreach ($defaults as $i => $d): ?>
            <div class="rest-card" data-reveal="up" data-delay="<?= $i * 0.1 ?>s">
                <div style="overflow:hidden;height:200px;display:flex;align-items:center;justify-content:center;background:var(--bg-secondary)">
                    <span style="font-size:5rem"><?= $d[1] ?></span>
                </div>
                <div class="rest-card-body">
                    <h3 class="rest-card-name"><?= $d[0] ?></h3>
                    <div class="rest-card-meta">
                        <span class="rating">⭐ <?= $d[3] ?></span>
                        <span class="badge badge-active">🟢 Open</span>
                        <span style="color:var(--text-muted);font-size:0.82rem">⏱ <?= $d[4] ?></span>
                    </div>
                    <p class="rest-card-desc"><?= $d[5] ?></p>
                    <a href="user/restaurants.php" class="btn btn-primary btn-sm" style="width:100%">View Menu →</a>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
        <div style="text-align:center;margin-top:40px" data-reveal="up">
            <a href="user/restaurants.php" class="btn btn-secondary btn-lg">View All Restaurants →</a>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════
     HOW IT WORKS
════════════════════════════════════════════ -->
<section class="section" style="background:radial-gradient(ellipse 80% 50% at 50% 50%, rgba(58,134,255,0.04) 0%, transparent 70%)">
    <div class="container">
        <div class="section-header" data-reveal="up">
            <div class="section-badge">⚡ Simple & Fast</div>
            <h2 class="section-title">How It <span class="highlight">Works</span></h2>
            <p class="section-subtitle">Get your favorite food in 3 simple steps</p>
        </div>
        <div class="hiw-grid">
            <div class="hiw-card" data-reveal="up" data-delay="0s">
                <div class="hiw-num">1</div>
                <span class="hiw-icon">🔍</span>
                <div class="hiw-title">Choose Restaurant</div>
                <p class="hiw-desc">Browse from 500+ top-rated restaurants near you. Filter by cuisine, rating, or delivery time.</p>
            </div>
            <div class="hiw-card" data-reveal="up" data-delay="0.15s">
                <div class="hiw-num">2</div>
                <span class="hiw-icon">🛒</span>
                <div class="hiw-title">Select Your Food</div>
                <p class="hiw-desc">Pick your favourite dishes, customize your order, and add everything to your cart with one click.</p>
            </div>
            <div class="hiw-card" data-reveal="up" data-delay="0.3s">
                <div class="hiw-num">3</div>
                <span class="hiw-icon">🛵</span>
                <div class="hiw-title">Fast Delivery</div>
                <p class="hiw-desc">Your food is prepared fresh and delivered hot to your door in 30 minutes or less. Track in real-time!</p>
            </div>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════
     TESTIMONIALS
════════════════════════════════════════════ -->
<section class="section">
    <div class="container">
        <div class="section-header" data-reveal="up">
            <div class="section-badge">💬 Reviews</div>
            <h2 class="section-title">What Our <span class="highlight">Customers Say</span></h2>
            <p class="section-subtitle">Don't just take our word for it — hear from our happy customers</p>
        </div>
        <div class="testi-grid">
            <?php
            $testis = [
                ['A','Arjun Sharma','Hyderabad','5','Absolutely love QuickBite! The food arrives hot and on time every single time. The interface is beautiful and so easy to use.'],
                ['P','Priya Reddy','Vijayawada','5','Best food delivery app I have used! Wide variety of restaurants, great prices, and lightning-fast delivery. Highly recommend!'],
                ['R','Rahul Kumar','Guntur','4','The order tracking feature is amazing. I can see exactly where my food is at all times. QuickBite is a game-changer!'],
                ['S','Sneha Patel','Hyderabad','5','I love the personalized recommendations. QuickBite always suggests exactly what I am in the mood for. The app is stunning!'],
                ['K','Kiran Naidu','Visakhapatnam','5','The coupon system is fantastic! I saved ₹200 on my first order. The food quality is consistently excellent.'],
                ['A','Ananya Rao','Hyderabad','4','QuickBite has made my life so much easier. No more cooking on busy weekdays — just order and relax!'],
            ];
            foreach ($testis as $i => $t): ?>
            <div class="testi-card" data-reveal="up" data-delay="<?= $i * 0.08 ?>s">
                <div class="testi-stars"><?= str_repeat('⭐', (int)$t[3]) ?></div>
                <p class="testi-text">"<?= $t[4] ?>"</p>
                <div class="testi-author">
                    <div class="testi-avatar"><?= $t[0] ?></div>
                    <div>
                        <div class="testi-name"><?= $t[1] ?></div>
                        <div class="testi-loc">📍 <?= $t[2] ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════
     CTA
════════════════════════════════════════════ -->
<section style="padding:0 5%">
    <div class="cta-section" data-reveal="up">
        <div class="section-badge" style="margin:0 auto 20px;display:inline-flex">🚀 Join QuickBite Today</div>
        <h2 class="cta-title">
            Ready to <span style="color:#FF5A00; background:var(--grad-primary);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">Order?</span>
        </h2>
        <p class="cta-desc">Join over 1,000+ happy customers. Sign up free and get 20% off your first order with code WELCOME20.</p>
        <div class="cta-buttons">
            <a href="auth/register.php" class="btn btn-primary btn-lg">
                🎉 Get Started — It's Free
            </a>
            <a href="user/restaurants.php" class="btn btn-secondary btn-lg">
                🍔 Browse Restaurants
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>

</body>
</html>