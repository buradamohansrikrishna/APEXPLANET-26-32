<?php
session_start();
require_once '../config/db.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

// Validate restaurant ID
$restaurant_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($restaurant_id <= 0) {
    header('Location: restaurants.php');
    exit;
}

// Fetch restaurant
$stmt = $conn->prepare("SELECT * FROM restaurants WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$restaurant = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$restaurant) {
    header('Location: restaurants.php');
    exit;
}

// Fetch all foods for this restaurant
$stmt = $conn->prepare("SELECT * FROM foods WHERE restaurant_id = ? ORDER BY category, name");
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$foods = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Cart count (if logged in)
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $cart_count = get_cart_count($_SESSION['user_id'], $conn);
}

// Get unique categories
$food_categories = array_unique(array_column($foods, 'category'));

// Is restaurant open?
function is_open(string $opening, string $closing): bool {
    $now   = date('H:i:s');
    $open  = date('H:i:s', strtotime($opening));
    $close = date('H:i:s', strtotime($closing));
    if ($close > $open) return $now >= $open && $now <= $close;
    return $now >= $open || $now <= $close;
}

$is_open   = is_open($restaurant['opening_time'] ?? '09:00', $restaurant['closing_time'] ?? '22:00');
$avg_rating = $restaurant['rating'] ?? 4.2;
$csrf_token = generate_csrf_token();

// Spice level helper
function spice_dots(int $level): string {
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $color = $i <= $level ? '#FF4545' : 'rgba(255,255,255,0.15)';
        $out  .= "<span style='display:inline-block;width:8px;height:8px;border-radius:50%;background:{$color};margin-right:3px;'></span>";
    }
    return $out;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($restaurant['name']) ?> – Menu | QuickBite 2.0</title>
    <meta name="description" content="Browse the full menu of <?= htmlspecialchars($restaurant['name']) ?> on QuickBite. Order delicious food online.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <style>
        :root{
            --neon-cyan:#00F7FF;--bg-dark:#050816;--bg-secondary:#0B1020;
            --bg-card:rgba(255,255,255,0.04);--text-primary:#F0F4FF;--text-secondary:#94A3B8;
            --border-glass:rgba(255,255,255,0.08);--neon-glow:0 0 20px rgba(0,247,255,0.3);
            --green:#00D084;--orange:#FF8C42;--pink:#FF6B9D;
        }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:var(--bg-dark);color:var(--text-primary);min-height:100vh;overflow-x:hidden;}

        /* RESTAURANT HERO */
        .rest-hero{
            position:relative;height:320px;overflow:hidden;display:flex;align-items:flex-end;
        }
        .hero-bg{width:100%;height:100%;object-fit:cover;position:absolute;inset:0;}
        .hero-bg-gradient{
            position:absolute;inset:0;
            background:linear-gradient(to bottom, rgba(5,8,22,0.3) 0%, rgba(5,8,22,0.95) 100%);
        }
        .hero-content{position:relative;z-index:2;padding:2rem;max-width:1200px;margin:0 auto;width:100%;}
        .hero-content h1{font-size:2.2rem;font-weight:800;margin-bottom:0.5rem;}
        .hero-badges{display:flex;gap:0.7rem;flex-wrap:wrap;align-items:center;}
        .badge{padding:0.3rem 0.8rem;border-radius:20px;font-size:0.78rem;font-weight:700;}
        .badge-cyan{background:rgba(0,247,255,0.12);color:var(--neon-cyan);border:1px solid rgba(0,247,255,0.3);}
        .badge-green{background:rgba(0,208,132,0.15);color:var(--green);border:1px solid rgba(0,208,132,0.3);}
        .badge-red{background:rgba(255,100,100,0.15);color:#ff6464;border:1px solid rgba(255,100,100,0.3);}
        .badge-orange{background:rgba(255,140,66,0.12);color:var(--orange);border:1px solid rgba(255,140,66,0.3);}
        .badge-star{background:rgba(255,215,0,0.12);color:#FFD700;border:1px solid rgba(255,215,0,0.3);}

        /* FILTER BAR */
        .filter-bar{
            background:var(--bg-secondary);border-bottom:1px solid var(--border-glass);
            padding:1rem 2rem;display:flex;gap:1rem;align-items:center;flex-wrap:wrap;
            position:sticky;top:0;z-index:100;backdrop-filter:blur(20px);
        }
        .search-live{
            padding:0.55rem 1rem;background:rgba(255,255,255,0.05);
            border:1px solid var(--border-glass);border-radius:10px;
            color:var(--text-primary);font-size:0.88rem;outline:none;
            transition:border-color 0.3s;min-width:200px;
        }
        .search-live:focus{border-color:var(--neon-cyan);}
        .search-live::placeholder{color:var(--text-secondary);}
        .toggle-group{display:flex;border:1px solid var(--border-glass);border-radius:10px;overflow:hidden;}
        .toggle-btn{
            padding:0.5rem 1rem;background:transparent;border:none;
            color:var(--text-secondary);font-size:0.82rem;font-weight:600;cursor:pointer;
            transition:all 0.25s;
        }
        .toggle-btn.active{background:rgba(0,247,255,0.12);color:var(--neon-cyan);}
        .cat-tabs{display:flex;gap:0.5rem;flex-wrap:wrap;}
        .cat-tab{
            padding:0.4rem 0.9rem;border-radius:20px;font-size:0.8rem;font-weight:600;
            border:1px solid var(--border-glass);color:var(--text-secondary);
            background:transparent;cursor:pointer;transition:all 0.25s;
        }
        .cat-tab.active,.cat-tab:hover{background:rgba(0,247,255,0.1);border-color:var(--neon-cyan);color:var(--neon-cyan);}

        /* FOOD GRID */
        .menu-wrap{max-width:1200px;margin:2rem auto 6rem;padding:0 2rem;}
        .food-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.6rem;}
        @media(max-width:900px){.food-grid{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:560px){.food-grid{grid-template-columns:1fr;}}
        .food-card{
            background:var(--bg-card);border:1px solid var(--border-glass);
            border-radius:18px;overflow:hidden;position:relative;
            transition:all 0.35s ease;animation:card-in 0.5s ease both;
        }
        @keyframes card-in{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
        .food-card:hover{transform:translateY(-6px);border-color:rgba(0,247,255,0.25);box-shadow:0 12px 35px rgba(0,0,0,0.5);}
        .food-img-wrap{position:relative;height:190px;overflow:hidden;}
        .food-img{width:100%;height:100%;object-fit:cover;transition:transform 0.4s ease;}
        .food-card:hover .food-img{transform:scale(1.06);}
        .food-img-placeholder{width:100%;height:190px;background:linear-gradient(135deg,#0B1020,#0d1a40);display:flex;align-items:center;justify-content:center;font-size:3rem;}
        .fav-btn{
            position:absolute;top:10px;right:10px;
            width:36px;height:36px;border-radius:50%;
            background:rgba(0,0,0,0.6);backdrop-filter:blur(8px);
            border:1px solid rgba(255,255,255,0.15);
            font-size:1.1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;
            transition:all 0.3s;z-index:5;
        }
        .fav-btn:hover,.fav-btn.active{background:rgba(255,107,157,0.3);border-color:var(--pink);}
        .food-body{padding:1.1rem;}
        .food-badges{display:flex;gap:0.4rem;margin-bottom:0.6rem;flex-wrap:wrap;}
        .cat-badge{font-size:0.7rem;padding:0.2rem 0.6rem;border-radius:10px;background:rgba(0,247,255,0.08);color:var(--neon-cyan);border:1px solid rgba(0,247,255,0.2);font-weight:600;}
        .veg-badge{font-size:0.7rem;padding:0.2rem 0.6rem;border-radius:10px;background:rgba(0,208,132,0.1);color:var(--green);border:1px solid rgba(0,208,132,0.2);font-weight:600;}
        .nonveg-badge{font-size:0.7rem;padding:0.2rem 0.6rem;border-radius:10px;background:rgba(255,100,100,0.1);color:#ff6464;border:1px solid rgba(255,100,100,0.2);font-weight:600;}
        .food-name{font-size:1rem;font-weight:700;margin-bottom:0.25rem;}
        .food-desc{font-size:0.78rem;color:var(--text-secondary);margin-bottom:0.5rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
        .spice-row{display:flex;align-items:center;gap:0.4rem;margin-bottom:0.7rem;font-size:0.75rem;color:var(--text-secondary);}
        .food-price{font-size:1.2rem;font-weight:800;background:linear-gradient(90deg,var(--neon-cyan),#9B59B6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:0.9rem;}
        .cart-row{display:flex;align-items:center;gap:0.7rem;}
        .qty-input{
            width:60px;padding:0.45rem 0.5rem;text-align:center;
            background:rgba(255,255,255,0.06);border:1px solid var(--border-glass);
            border-radius:8px;color:var(--text-primary);font-size:0.9rem;font-weight:600;outline:none;
        }
        .btn-add-cart{
            flex:1;padding:0.55rem 0.8rem;
            background:linear-gradient(135deg,var(--neon-cyan),#00b8c8);
            border:none;border-radius:10px;color:#050816;font-weight:700;font-size:0.85rem;
            cursor:pointer;transition:all 0.3s;
        }
        .btn-add-cart:hover{opacity:0.85;}

        /* FLOAT CART */
        .float-cart-btn{
            position:fixed;bottom:2rem;right:2rem;z-index:999;
            background:linear-gradient(135deg,var(--neon-cyan),#00b8c8);
            color:#050816;border:none;border-radius:30px;
            padding:0.9rem 1.5rem;font-size:1rem;font-weight:800;
            cursor:pointer;box-shadow:0 4px 20px rgba(0,247,255,0.4);
            display:flex;align-items:center;gap:0.6rem;
            transition:transform 0.3s,box-shadow 0.3s;text-decoration:none;
        }
        .float-cart-btn:hover{transform:translateY(-3px);box-shadow:0 8px 30px rgba(0,247,255,0.6);}
        .float-cart-count{
            background:#050816;color:var(--neon-cyan);
            border-radius:50%;width:24px;height:24px;
            display:flex;align-items:center;justify-content:center;font-size:0.78rem;font-weight:800;
        }

        /* EMPTY */
        .empty-menu{text-align:center;padding:5rem 2rem;color:var(--text-secondary);}
        .empty-menu .empty-icon{font-size:4rem;margin-bottom:1rem;}
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<!-- RESTAURANT HERO -->
<section class="rest-hero">
    <?php if (!empty($restaurant['image'])): ?>
        <img src="../<?= htmlspecialchars($restaurant['image']) ?>" alt="<?= htmlspecialchars($restaurant['name']) ?>" class="hero-bg">
    <?php else: ?>
        <div style="position:absolute;inset:0;background:linear-gradient(135deg,#050816,#0a1a3e,#050816);"></div>
    <?php endif; ?>
    <div class="hero-bg-gradient"></div>
    <div class="hero-content">
        <h1><?= htmlspecialchars($restaurant['name']) ?></h1>
        <div class="hero-badges">
            <span class="badge badge-star">⭐ <?= number_format($avg_rating, 1) ?></span>
            <span class="badge badge-cyan">🍽️ <?= htmlspecialchars($restaurant['category']) ?></span>
            <span class="badge badge-orange">⏱ <?= htmlspecialchars($restaurant['delivery_time'] ?? '30') ?> min</span>
            <span class="badge <?= $is_open ? 'badge-green' : 'badge-red' ?>">
                <?= $is_open ? '● Open Now' : '● Closed' ?>
            </span>
        </div>
    </div>
</section>

<!-- FILTER BAR -->
<div class="filter-bar">
    <input type="text" id="live-search" class="search-live" placeholder="🔍 Search menu items…" autocomplete="off">
    <div class="toggle-group" role="group" aria-label="Diet filter">
        <button class="toggle-btn active" id="diet-all" data-diet="all">All</button>
        <button class="toggle-btn" id="diet-veg" data-diet="veg">🌿 Veg</button>
        <button class="toggle-btn" id="diet-nonveg" data-diet="nonveg">🍗 Non-Veg</button>
    </div>
    <div class="cat-tabs">
        <button class="cat-tab active" data-cat="all">All</button>
        <?php foreach ($food_categories as $cat): ?>
            <button class="cat-tab" data-cat="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></button>
        <?php endforeach; ?>
    </div>
</div>

<!-- FOOD GRID -->
<div class="menu-wrap">
    <?php if (empty($foods)): ?>
    <div class="empty-menu">
        <div class="empty-icon">🍽️</div>
        <h3 style="color:var(--text-primary);margin-bottom:0.5rem;">Menu Coming Soon</h3>
        <p>This restaurant hasn't added menu items yet.</p>
    </div>
    <?php else: ?>
    <div class="food-grid" id="food-grid">
        <?php foreach ($foods as $i => $food): ?>
        <div class="food-card"
             style="animation-delay:<?= $i * 0.06 ?>s;"
             data-name="<?= htmlspecialchars(strtolower($food['name'])) ?>"
             data-cat="<?= htmlspecialchars($food['category']) ?>"
             data-diet="<?= $food['is_veg'] ? 'veg' : 'nonveg' ?>"
        >
            <div class="food-img-wrap">
                <?php if (!empty($food['image'])): ?>
                    <img src="../<?= htmlspecialchars($food['image']) ?>" alt="<?= htmlspecialchars($food['name']) ?>" class="food-img" loading="lazy">
                <?php else: ?>
                    <div class="food-img-placeholder">🍽️</div>
                <?php endif; ?>
                <button class="fav-btn" data-food-id="<?= (int)$food['id'] ?>" id="fav-<?= (int)$food['id'] ?>"
                        onclick="toggleFavorite(<?= (int)$food['id'] ?>, this)" title="Toggle Favourite">❤️</button>
            </div>
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
                <?php if (!empty($food['description'])): ?>
                    <div class="food-desc"><?= htmlspecialchars($food['description']) ?></div>
                <?php endif; ?>
                <?php if (isset($food['spice_level'])): ?>
                <div class="spice-row">
                    🌶️ <?= spice_dots((int)$food['spice_level']) ?>
                </div>
                <?php endif; ?>
                <div class="food-price">₹<?= number_format($food['price'], 2) ?></div>
                <form class="add-to-cart-form" data-food-id="<?= (int)$food['id'] ?>" action="cart.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="food_id" value="<?= (int)$food['id'] ?>">
                    <div class="cart-row">
                        <input type="number" name="quantity" class="qty-input" value="1" min="1" max="20" id="qty-<?= (int)$food['id'] ?>">
                        <button type="submit" class="btn-add-cart">🛒 Add to Cart</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- FLOATING CART BUTTON -->
<a href="cart.php" class="float-cart-btn" id="float-cart" aria-label="Go to cart">
    🛒 My Cart
    <span class="float-cart-count" id="float-cart-count"><?= (int)$cart_count ?></span>
</a>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/cart.js"></script>
<script>
(function() {
    // Live search filter
    var searchInput = document.getElementById('live-search');
    var cards       = Array.from(document.querySelectorAll('.food-card'));
    var dietBtns    = document.querySelectorAll('.toggle-btn');
    var catBtns     = document.querySelectorAll('.cat-tab');
    var activeDiet  = 'all';
    var activeCat   = 'all';

    function applyFilters() {
        var q = searchInput ? searchInput.value.toLowerCase().trim() : '';
        cards.forEach(function(card) {
            var name  = card.dataset.name  || '';
            var cat   = card.dataset.cat   || '';
            var diet  = card.dataset.diet  || '';
            var matchSearch = !q || name.includes(q);
            var matchDiet   = activeDiet === 'all' || diet === activeDiet;
            var matchCat    = activeCat  === 'all' || cat === activeCat;
            card.style.display = (matchSearch && matchDiet && matchCat) ? '' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);

    dietBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            dietBtns.forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            activeDiet = btn.dataset.diet;
            applyFilters();
        });
    });

    catBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            catBtns.forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            activeCat = btn.dataset.cat;
            applyFilters();
        });
    });
})();

// Favourite toggle (delegated to cart.js or inline)
function toggleFavorite(foodId, btn) {
    <?php if (!isset($_SESSION['user_id'])): ?>
        window.location.href = '../auth/login.php';
        return;
    <?php endif; ?>
    fetch('ajax/toggle-favorite.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'food_id=' + encodeURIComponent(foodId) + '&csrf_token=<?= $csrf_token ?>'
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (data.success) {
            btn.classList.toggle('active', data.is_favorite);
            btn.textContent = data.is_favorite ? '❤️' : '🤍';
        }
    });
}
</script>
</body>
</html>