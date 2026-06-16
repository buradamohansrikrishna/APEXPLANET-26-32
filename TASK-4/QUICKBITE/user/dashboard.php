<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';
require_once '../includes/functions.php';

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT name, email, reward_points, avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Total orders
$stmt = $conn->prepare("SELECT COUNT(*) as total_orders FROM orders WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_orders = $stmt->get_result()->fetch_assoc()['total_orders'];
$stmt->close();

// Total spent (Delivered orders)
$stmt = $conn->prepare("SELECT COALESCE(SUM(total_price), 0) as total_spent FROM orders WHERE user_id = ? AND order_status = 'Delivered'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_spent = $stmt->get_result()->fetch_assoc()['total_spent'];
$stmt->close();

// Cart count
$cart_count = get_cart_count($user_id, $conn);

// Reward points
$reward_points = $user['reward_points'] ?? 0;

// Last 3 orders
$stmt = $conn->prepare("
    SELECT o.id, o.order_number, o.total_price, o.order_status as status, o.created_at as order_date,
           f.food_name as food_name, f.image as food_image
    FROM orders o
    LEFT JOIN foods f ON o.food_id = f.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
    LIMIT 3
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Favorite foods
$stmt = $conn->prepare("
    SELECT f.id, f.food_name as name, f.price, f.image, f.category, f.is_veg
    FROM favorites fav
    JOIN foods f ON fav.food_id = f.id
    WHERE fav.user_id = ?
    LIMIT 6
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$favorite_foods = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Recommendations (random foods not ordered by user)
$stmt = $conn->prepare("
    SELECT f.id, f.food_name as name, f.price, f.image, f.category, f.is_veg, f.description
    FROM foods f
    WHERE f.id NOT IN (
        SELECT DISTINCT food_id FROM orders WHERE user_id = ?
    )
    AND f.availability = 'available'
    ORDER BY RAND()
    LIMIT 3
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recommendations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$greeting = greeting();
$initials = get_initials($user['name'] ?? 'User');
$user_name = $user['name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – QuickBite 2.0</title>
    <meta name="description" content="Your QuickBite personal dashboard. View orders, favourites, and recommendations.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <style>
        :root {
            --neon-cyan: #FF5A00;
            --bg-dark: #F8FAFC;
            --bg-secondary: #FFFFFF;
            --bg-card: #FFFFFF;
            --text-primary: #0F172A;
            --text-secondary: #475569;
            --border-glass: #E2E8F0;
            --neon-glow: 0 0 20px rgba(255,71,71,0.3);
            --green: #00D084;
            --orange: #FF8C42;
            --purple: #9B59B6;
            --pink: #FF6B9D;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:var(--bg-dark); color:var(--text-primary); min-height:100vh; overflow-x:hidden; }

        /* HERO */
        .dash-hero {
            background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 50%, #F8FAFC 100%);
            padding: 4rem 2rem 3rem;
            position: relative;
            overflow: hidden;
        }
        .dash-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 60%;
            height: 200%;
            background: radial-gradient(ellipse, rgba(255,71,71,0.07) 0%, transparent 70%);
            pointer-events: none;
        }
        .dash-hero::after {
            content: '';
            position: absolute;
            top: 0; right: -10%;
            width: 40%;
            height: 100%;
            background: radial-gradient(ellipse, rgba(155,89,182,0.06) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-content { max-width:1200px; margin:0 auto; display:flex; align-items:center; gap:2rem; position:relative; z-index:1; }
        .avatar-circle {
            width:80px; height:80px; border-radius:50%;
            background: linear-gradient(135deg, var(--neon-cyan), #9B59B6);
            display:flex; align-items:center; justify-content:center;
            font-size:1.8rem; font-weight:800; color:#0F172A;
            flex-shrink:0; box-shadow: var(--neon-glow);
            animation: pulse-avatar 3s ease-in-out infinite;
        }
        @keyframes pulse-avatar {
            0%,100% { box-shadow: 0 0 20px rgba(255,71,71,0.3); }
            50% { box-shadow: 0 0 40px rgba(255,71,71,0.6); }
        }
        .hero-text .greeting { font-size:0.95rem; color:var(--neon-cyan); font-weight:600; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:0.4rem; }
        .hero-text h1 { font-size:2.2rem; font-weight:800; margin-bottom:0.5rem; }
        .hero-text h1 span { background: linear-gradient(90deg, var(--neon-cyan), #9B59B6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
        .hero-text .subtitle { color:var(--text-secondary); font-size:1rem; animation: fade-slide-up 1s ease both 0.3s; }
        @keyframes fade-slide-up { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }

        /* MAIN */
        .dash-main { max-width:1200px; margin:0 auto; padding:2rem; }

        /* STAT CARDS */
        .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1.5rem; margin-bottom:2.5rem; }
        @media(max-width:900px){ .stats-grid { grid-template-columns:repeat(2,1fr); } }
        @media(max-width:480px){ .stats-grid { grid-template-columns:1fr; } }
        .stat-card {
            background:var(--bg-card);
            border:1px solid var(--border-glass);
            backdrop-filter:blur(20px);
            border-radius:16px;
            padding:1.5rem;
            display:flex; align-items:center; gap:1.2rem;
            transition:transform 0.3s ease, box-shadow 0.3s ease;
            animation: card-in 0.6s ease both;
        }
        .stat-card:nth-child(1) { animation-delay:0.1s; }
        .stat-card:nth-child(2) { animation-delay:0.2s; }
        .stat-card:nth-child(3) { animation-delay:0.3s; }
        .stat-card:nth-child(4) { animation-delay:0.4s; }
        @keyframes card-in { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .stat-card:hover { transform:translateY(-4px); box-shadow:0 8px 30px rgba(255,71,71,0.15); }
        .stat-icon {
            width:54px; height:54px; border-radius:14px;
            display:flex; align-items:center; justify-content:center;
            font-size:1.5rem; flex-shrink:0;
        }
        .stat-icon.cyan { background:rgba(255,71,71,0.15); }
        .stat-icon.green { background:rgba(0,208,132,0.15); }
        .stat-icon.purple { background:rgba(155,89,182,0.15); }
        .stat-icon.orange { background:rgba(255,140,66,0.15); }
        .stat-info { flex:1; }
        .stat-label { font-size:0.8rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.3rem; }
        .stat-value { font-size:1.8rem; font-weight:800; color:var(--text-primary); }
        .stat-value.cyan-text { color:var(--neon-cyan); }
        .stat-value.green-text { color:var(--green); }
        .stat-value.purple-text { color:var(--purple); }
        .stat-value.orange-text { color:var(--orange); }

        /* QUICK ACTIONS */
        .section-title { font-size:1.3rem; font-weight:700; margin-bottom:1.2rem; display:flex; align-items:center; gap:0.6rem; }
        .section-title::before { content:''; width:4px; height:1.3em; background:linear-gradient(180deg,var(--neon-cyan),#9B59B6); border-radius:2px; display:inline-block; }
        .actions-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:2.5rem; }
        @media(max-width:700px){ .actions-grid { grid-template-columns:repeat(2,1fr); } }
        .action-card {
            background:var(--bg-card);
            border:1px solid var(--border-glass);
            border-radius:14px;
            padding:1.4rem 1rem;
            text-align:center;
            text-decoration:none;
            color:var(--text-primary);
            transition:all 0.3s ease;
            display:flex; flex-direction:column; align-items:center; gap:0.7rem;
        }
        .action-card:hover { transform:translateY(-4px); border-color:var(--neon-cyan); box-shadow:0 0 20px rgba(255,71,71,0.15); color:var(--neon-cyan); }
        .action-icon { font-size:2rem; }
        .action-label { font-size:0.88rem; font-weight:600; }

        /* RECENT ORDERS */
        .orders-table-wrap { overflow-x:auto; margin-bottom:2.5rem; }
        .orders-table { width:100%; border-collapse:collapse; }
        .orders-table th { text-align:left; font-size:0.78rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-secondary); padding:0.7rem 1rem; border-bottom:1px solid var(--border-glass); }
        .orders-table td { padding:0.9rem 1rem; border-bottom:1px solid rgba(255,255,255,0.03); font-size:0.92rem; }
        .orders-table tr:hover td { background:rgba(255,71,71,0.03); }
        .status-badge { display:inline-block; padding:0.3rem 0.8rem; border-radius:20px; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; }
        .status-badge.delivered { background:rgba(0,208,132,0.15); color:var(--green); border:1px solid rgba(0,208,132,0.3); }
        .status-badge.pending { background:rgba(255,140,66,0.15); color:var(--orange); border:1px solid rgba(255,140,66,0.3); }
        .status-badge.cancelled { background:rgba(255,100,100,0.15); color:#ff6464; border:1px solid rgba(255,100,100,0.3); }
        .status-badge.preparing { background:rgba(255,71,71,0.12); color:var(--neon-cyan); border:1px solid rgba(255,71,71,0.3); }
        .status-badge.accepted { background:rgba(155,89,182,0.15); color:var(--purple); border:1px solid rgba(155,89,182,0.3); }
        .status-badge.ready { background:rgba(255,107,157,0.15); color:var(--pink); border:1px solid rgba(255,107,157,0.3); }
        .status-badge.out-for-delivery { background:rgba(255,71,71,0.1); color:#7dd3fc; border:1px solid rgba(125,211,252,0.3); }

        /* FOOD CARDS */
        .foods-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem; margin-bottom:2.5rem; }
        @media(max-width:900px){ .foods-grid { grid-template-columns:repeat(2,1fr); } }
        @media(max-width:500px){ .foods-grid { grid-template-columns:1fr; } }
        .food-card {
            background:var(--bg-card);
            border:1px solid var(--border-glass);
            border-radius:16px;
            overflow:hidden;
            transition:all 0.35s ease;
        }
        .food-card:hover { transform:translateY(-6px); border-color:rgba(255,71,71,0.25); box-shadow:0 12px 30px rgba(0,0,0,0.4); }
        .food-img { width:100%; height:180px; object-fit:cover; }
        .food-img-placeholder { width:100%; height:180px; background:linear-gradient(135deg,#FFFFFF,#0d1a40); display:flex; align-items:center; justify-content:center; font-size:3rem; }
        .food-body { padding:1.1rem; }
        .food-badges { display:flex; gap:0.5rem; margin-bottom:0.6rem; flex-wrap:wrap; }
        .cat-badge { font-size:0.7rem; padding:0.2rem 0.6rem; border-radius:10px; background:rgba(255,71,71,0.1); color:var(--neon-cyan); border:1px solid rgba(255,71,71,0.2); font-weight:600; }
        .veg-badge { font-size:0.7rem; padding:0.2rem 0.6rem; border-radius:10px; background:rgba(0,208,132,0.1); color:var(--green); border:1px solid rgba(0,208,132,0.2); font-weight:600; }
        .nonveg-badge { font-size:0.7rem; padding:0.2rem 0.6rem; border-radius:10px; background:rgba(255,100,100,0.1); color:#ff6464; border:1px solid rgba(255,100,100,0.2); font-weight:600; }
        .food-name { font-size:1rem; font-weight:700; margin-bottom:0.3rem; }
        .food-price { font-size:1.2rem; font-weight:800; background:linear-gradient(90deg,var(--neon-cyan),#9B59B6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:0.8rem; }
        .btn-add-cart {
            width:100%; padding:0.6rem;
            background:linear-gradient(135deg,var(--neon-cyan),#00b8c8);
            border:none; border-radius:10px;
            color:#0F172A; font-weight:700; font-size:0.88rem;
            cursor:pointer; transition:all 0.3s ease;
        }
        .btn-add-cart:hover { opacity:0.85; transform:scale(1.02); }

        /* EMPTY STATE */
        .empty-state { text-align:center; padding:3rem 1rem; color:var(--text-secondary); }
        .empty-state .empty-icon { font-size:3.5rem; margin-bottom:1rem; }
        .empty-state p { font-size:0.95rem; }

        /* SECTION WRAP */
        .section-card { background:var(--bg-card); border:1px solid var(--border-glass); border-radius:18px; padding:1.5rem; margin-bottom:2.5rem; backdrop-filter:blur(20px); }
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<!-- HERO -->
<section class="dash-hero">
    <div class="hero-content">
        <div class="avatar-circle" aria-label="User avatar"><?= htmlspecialchars($initials) ?></div>
        <div class="hero-text">
            <p class="greeting"><?= htmlspecialchars($greeting) ?></p>
            <h1>Welcome back, <span><?= htmlspecialchars($user_name) ?></span>! 👋</h1>
            <p class="subtitle">Here's what's happening with your food journey today.</p>
        </div>
    </div>
</section>

<!-- MAIN CONTENT -->
<main class="dash-main">

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon cyan">🛍️</div>
            <div class="stat-info">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value cyan-text counter" data-target="<?= (int)$total_orders ?>"><?= (int)$total_orders ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">💰</div>
            <div class="stat-info">
                <div class="stat-label">Total Spent</div>
                <div class="stat-value green-text">₹<?= number_format($total_spent, 2) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">⭐</div>
            <div class="stat-info">
                <div class="stat-label">Reward Points</div>
                <div class="stat-value purple-text counter" data-target="<?= (int)$reward_points ?>"><?= (int)$reward_points ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">🛒</div>
            <div class="stat-info">
                <div class="stat-label">Cart Items</div>
                <div class="stat-value orange-text counter" data-target="<?= (int)$cart_count ?>"><?= (int)$cart_count ?></div>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <p class="section-title">Quick Actions</p>
    <div class="actions-grid">
        <a href="restaurants.php" class="action-card" id="quick-browse">
            <span class="action-icon">🍽️</span>
            <span class="action-label">Browse Restaurants</span>
        </a>
        <a href="orders.php" class="action-card" id="quick-orders">
            <span class="action-icon">📦</span>
            <span class="action-label">My Orders</span>
        </a>
        <a href="cart.php" class="action-card" id="quick-cart">
            <span class="action-icon">🛒</span>
            <span class="action-label">My Cart</span>
        </a>
        <a href="profile.php" class="action-card" id="quick-profile">
            <span class="action-icon">👤</span>
            <span class="action-label">Profile</span>
        </a>
        <a href="#" class="action-card" onclick="openAppRateModal(); return false;" id="quick-rate">
            <span class="action-icon">⭐</span>
            <span class="action-label">Rate App</span>
        </a>
    </div>

    <!-- RECENT ORDERS -->
    <div class="section-card">
        <p class="section-title">Recent Orders</p>
        <?php if (!empty($recent_orders)): ?>
        <div class="orders-table-wrap">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Food</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $order): ?>
                    <tr>
                        <td><strong>#<?= htmlspecialchars($order['order_number']) ?></strong></td>
                        <td><?= htmlspecialchars($order['food_name'] ?? 'N/A') ?></td>
                        <td>₹<?= number_format($order['total_price'], 2) ?></td>
                        <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                        <td>
                            <?php
                            $s = strtolower(str_replace(' ', '-', $order['status']));
                            echo "<span class='status-badge {$s}'>" . htmlspecialchars($order['status']) . "</span>";
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <p>No orders yet. <a href="restaurants.php" style="color:var(--neon-cyan);">Order your first meal!</a></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- FAVORITE FOODS -->
    <?php if (!empty($favorite_foods)): ?>
    <p class="section-title">❤️ Your Favourites</p>
    <div class="foods-grid">
        <?php foreach ($favorite_foods as $food): ?>
        <div class="food-card">
            <?php if (!empty($food['image'])): ?>
                <img src="../<?= htmlspecialchars($food['image']) ?>" alt="<?= htmlspecialchars($food['name']) ?>" class="food-img" loading="lazy">
            <?php else: ?>
                <div class="food-img-placeholder">🍔</div>
            <?php endif; ?>
            <div class="food-body">
                <div class="food-badges">
                    <span class="cat-badge"><?= htmlspecialchars($food['category']) ?></span>
                    <?php if ($food['is_veg']): ?>
                        <span class="veg-badge">🌿 Veg</span>
                    <?php else: ?>
                        <span class="nonveg-badge">🍗 Non-Veg</span>
                    <?php endif; ?>
                </div>
                <div class="food-name"><?= htmlspecialchars($food['name']) ?></div>
                <div class="food-price">₹<?= number_format($food['price'], 2) ?></div>
                <form class="add-to-cart-form" data-food-id="<?= (int)$food['id'] ?>">
                    <button type="submit" class="btn-add-cart">🛒 Add to Cart</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- RECOMMENDATIONS -->
    <?php if (!empty($recommendations)): ?>
    <p class="section-title">✨ You Might Like</p>
    <div class="foods-grid">
        <?php foreach ($recommendations as $food): ?>
        <div class="food-card">
            <?php if (!empty($food['image'])): ?>
                <img src="../<?= htmlspecialchars($food['image']) ?>" alt="<?= htmlspecialchars($food['name']) ?>" class="food-img" loading="lazy">
            <?php else: ?>
                <div class="food-img-placeholder">🍽️</div>
            <?php endif; ?>
            <div class="food-body">
                <div class="food-badges">
                    <span class="cat-badge"><?= htmlspecialchars($food['category']) ?></span>
                    <?php if ($food['is_veg']): ?>
                        <span class="veg-badge">🌿 Veg</span>
                    <?php else: ?>
                        <span class="nonveg-badge">🍗 Non-Veg</span>
                    <?php endif; ?>
                </div>
                <div class="food-name"><?= htmlspecialchars($food['name']) ?></div>
                <div class="food-price">₹<?= number_format($food['price'], 2) ?></div>
                <?php if (!empty($food['description'])): ?>
                    <p style="font-size:0.8rem;color:var(--text-secondary);margin-bottom:0.7rem;"><?= htmlspecialchars(substr($food['description'], 0, 60)) ?>…</p>
                <?php endif; ?>
                <form class="add-to-cart-form" data-food-id="<?= (int)$food['id'] ?>">
                    <button type="submit" class="btn-add-cart">🛒 Add to Cart</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</main>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/cart.js"></script>
<script>
// Animated counters
document.querySelectorAll('.counter').forEach(function(el) {
    var target = parseInt(el.dataset.target, 10) || 0;
    var current = 0;
    var step = Math.max(1, Math.ceil(target / 40));
    var timer = setInterval(function() {
        current = Math.min(current + step, target);
        el.textContent = current;
        if (current >= target) clearInterval(timer);
    }, 30);
});
</script>

<!-- App Rate Modal -->
<div id="appRateModal" class="modal">
    <div class="modal-content">
        <h3>Rate QuickBite</h3>
        <p style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:1rem;">How is your experience with our app?</p>
        <form id="appRateForm">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
            <input type="hidden" name="rating" id="app_rating_input" value="0">
            
            <div style="font-size:2rem;margin-bottom:1rem;display:flex;gap:0.5rem;justify-content:center;">
                <button type="button" class="app-star-btn" data-rating="1" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:2.5rem;">★</button>
                <button type="button" class="app-star-btn" data-rating="2" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:2.5rem;">★</button>
                <button type="button" class="app-star-btn" data-rating="3" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:2.5rem;">★</button>
                <button type="button" class="app-star-btn" data-rating="4" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:2.5rem;">★</button>
                <button type="button" class="app-star-btn" data-rating="5" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:2.5rem;">★</button>
            </div>
            
            <div class="form-group">
                <textarea name="comment" class="form-control" placeholder="Any suggestions for us?" rows="3"></textarea>
            </div>
            
            <div style="display:flex;gap:1rem;margin-top:1rem;">
                <button type="button" onclick="closeAppRateModal()" class="btn-secondary" style="flex:1;padding:0.8rem;border-radius:10px;border:1px solid var(--border-glass);background:var(--bg-card);color:var(--text-primary);cursor:pointer;">Cancel</button>
                <button type="submit" class="btn-primary" style="flex:1;padding:0.8rem;border-radius:10px;border:none;background:var(--neon-cyan);color:#fff;cursor:pointer;font-weight:bold;">Submit</button>
            </div>
        </form>
    </div>
</div>
<style>
.modal { display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.6); backdrop-filter:blur(5px); align-items:center; justify-content:center; }
.modal.show { display:flex; }
.modal-content { background:var(--bg-secondary); padding:2rem; border-radius:16px; width:90%; max-width:400px; text-align:center; box-shadow:var(--shadow-card); }
</style>
<script>
// Rate App Modal Logic
function openAppRateModal() { document.getElementById('appRateModal').classList.add('show'); }
function closeAppRateModal() { document.getElementById('appRateModal').classList.remove('show'); }

document.querySelectorAll('.app-star-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        let rating = this.getAttribute('data-rating');
        document.getElementById('app_rating_input').value = rating;
        
        document.querySelectorAll('.app-star-btn').forEach(b => {
            if (b.getAttribute('data-rating') <= rating) {
                b.style.color = '#FFB800';
            } else {
                b.style.color = 'var(--text-secondary)';
            }
        });
    });
});

document.getElementById('appRateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let rating = document.getElementById('app_rating_input').value;
    if (rating === '0') {
        alert('Please select a star rating.');
        return;
    }

    let formData = new FormData(this);
    fetch('ajax/rate-app.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            closeAppRateModal();
        } else {
            alert(data.message);
        }
    })
    .catch(err => alert('An error occurred.'));
});
</script>
</body>
</html>