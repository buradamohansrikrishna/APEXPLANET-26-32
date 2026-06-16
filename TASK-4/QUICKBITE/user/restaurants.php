<?php
session_start();
require_once '../config/db.php';

$search   = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Build query dynamically
$where_parts = ["r.status = 'active'"];
$params      = [];
$types       = '';

if ($search !== '') {
    $where_parts[] = "(r.restaurant_name LIKE ? OR r.description LIKE ?)";
    $like = "%{$search}%";
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}

if ($category !== '' && $category !== 'All') {
    $where_parts[] = "r.category = ?";
    $params[] = $category;
    $types   .= 's';
}

$where_sql = implode(' AND ', $where_parts);
$sql = "SELECT r.id, r.restaurant_name as name, r.location, r.image, r.description, r.address, 
               r.rating as avg_rating, r.category, r.opening_time, r.closing_time, r.delivery_time, 
               r.min_order, r.status, r.cover_image, r.created_at,
               (SELECT COUNT(*) FROM reviews rv JOIN foods f ON rv.food_id = f.id WHERE f.restaurant_id = r.id) as review_count
        FROM restaurants r
        WHERE {$where_sql}
        ORDER BY avg_rating DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$restaurants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Categories for filter pills
$categories = ['All', 'Fast Food', 'Indian', 'Italian', 'Chinese', 'Mexican'];

// Helper: is restaurant open right now?
function is_open(string $opening, string $closing): bool {
    $now     = date('H:i:s');
    $open    = date('H:i:s', strtotime($opening));
    $close   = date('H:i:s', strtotime($closing));
    if ($close > $open) {
        return $now >= $open && $now <= $close;
    }
    // Overnight
    return $now >= $open || $now <= $close;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Restaurants – QuickBite 2.0</title>
    <meta name="description" content="Browse and discover top restaurants near you on QuickBite. Filter by cuisine and search by name.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <style>
        :root {
            --neon-cyan:#FF5A00; --bg-dark:#F8FAFC; --bg-secondary:#FFFFFF;
            --bg-card:#FFFFFF; --text-primary:#0F172A; --text-secondary:#475569;
            --border-glass:#E2E8F0; --neon-glow:0 0 20px rgba(255,71,71,0.3);
            --green:#00D084; --orange:#FF8C42;
        }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:var(--bg-dark);color:var(--text-primary);min-height:100vh;overflow-x:hidden;}

        /* HERO */
        .page-hero{
            background:linear-gradient(135deg,#F8FAFC 0%,#F1F5F9 60%,#F8FAFC 100%);
            padding:4rem 2rem 3rem; text-align:center; position:relative; overflow:hidden;
        }
        .page-hero::before{
            content:'';position:absolute;inset:0;
            background:radial-gradient(ellipse 80% 60% at 50% 100%,rgba(255,71,71,0.06),transparent);
            pointer-events:none;
        }
        .page-hero h1{font-size:2.6rem;font-weight:800;margin-bottom:0.6rem;}
        .page-hero h1 span{background:linear-gradient(90deg,var(--neon-cyan),#9B59B6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
        .page-hero p{color:var(--text-secondary);margin-bottom:2rem;font-size:1.05rem;}

        /* SEARCH FORM */
        .search-form{display:flex;gap:0.8rem;max-width:560px;margin:0 auto;position:relative;z-index:1;}
        .search-input{
            flex:1;padding:0.85rem 1.2rem;
            background:rgba(255,255,255,0.06);border:1px solid var(--border-glass);
            border-radius:12px;color:var(--text-primary);font-size:0.95rem;
            backdrop-filter:blur(10px);outline:none;transition:border-color 0.3s;
        }
        .search-input:focus{border-color:var(--neon-cyan);box-shadow:0 0 0 3px rgba(255,71,71,0.1);}
        .search-input::placeholder{color:var(--text-secondary);}
        .btn-search{
            padding:0.85rem 1.6rem;
            background:linear-gradient(135deg,var(--neon-cyan),#00b8c8);
            border:none;border-radius:12px;color:#0F172A;font-weight:700;
            cursor:pointer;transition:opacity 0.3s;white-space:nowrap;
        }
        .btn-search:hover{opacity:0.85;}

        /* FILTER PILLS */
        .filters-wrap{max-width:1200px;margin:0 auto;padding:1.5rem 2rem 0;display:flex;gap:0.7rem;flex-wrap:wrap;}
        .filter-pill{
            padding:0.5rem 1.2rem;border-radius:30px;font-size:0.85rem;font-weight:600;
            text-decoration:none;border:1px solid var(--border-glass);
            color:var(--text-secondary);background:var(--bg-card);
            transition:all 0.25s ease;white-space:nowrap;
        }
        .filter-pill:hover,.filter-pill.active{
            background:rgba(255,71,71,0.12);border-color:var(--neon-cyan);
            color:var(--neon-cyan);box-shadow:0 0 12px rgba(255,71,71,0.2);
        }

        /* RESULTS META */
        .results-meta{max-width:1200px;margin:1.5rem auto 0;padding:0 2rem;color:var(--text-secondary);font-size:0.88rem;}

        /* RESTAURANT GRID */
        .restaurants-grid{max-width:1200px;margin:1.5rem auto 4rem;padding:0 2rem;display:grid;grid-template-columns:repeat(3,1fr);gap:1.8rem;}
        @media(max-width:900px){.restaurants-grid{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:560px){.restaurants-grid{grid-template-columns:1fr;}}

        /* RESTAURANT CARD */
        .rest-card{
            background:var(--bg-card);border:1px solid var(--border-glass);
            border-radius:18px;overflow:hidden;
            transition:all 0.35s ease;animation:card-in 0.5s ease both;
        }
        @keyframes card-in{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
        .rest-card:hover{transform:translateY(-6px);border-color:rgba(255,71,71,0.25);box-shadow:0 12px 30px rgba(0,0,0,0.5);}
        .rest-img-wrap{position:relative;height:190px;overflow:hidden;}
        .rest-img{width:100%;height:100%;object-fit:cover;transition:transform 0.4s ease;}
        .rest-card:hover .rest-img{transform:scale(1.05);}
        .rest-img-placeholder{
            width:100%;height:190px;
            background:linear-gradient(135deg,#FFFFFF,#0d1a40);
            display:flex;align-items:center;justify-content:center;font-size:3.5rem;
        }
        .rating-badge{
            position:absolute;top:12px;right:12px;
            background:rgba(0,0,0,0.7);backdrop-filter:blur(8px);
            border:1px solid rgba(255,215,0,0.4);border-radius:20px;
            padding:0.3rem 0.7rem;font-size:0.8rem;font-weight:700;color:#FFD700;
        }
        .open-badge{
            position:absolute;top:12px;left:12px;
            padding:0.3rem 0.7rem;border-radius:20px;font-size:0.75rem;font-weight:700;
        }
        .open-badge.open{background:rgba(0,208,132,0.2);border:1px solid rgba(0,208,132,0.5);color:var(--green);}
        .open-badge.closed{background:rgba(255,100,100,0.2);border:1px solid rgba(255,100,100,0.4);color:#ff6464;}
        .rest-body{padding:1.2rem;}
        .rest-meta{display:flex;gap:0.6rem;margin-bottom:0.6rem;flex-wrap:wrap;}
        .rest-cat{font-size:0.72rem;padding:0.2rem 0.6rem;border-radius:10px;background:rgba(255,71,71,0.08);color:var(--neon-cyan);border:1px solid rgba(255,71,71,0.2);font-weight:600;}
        .rest-time{font-size:0.72rem;padding:0.2rem 0.6rem;border-radius:10px;background:rgba(255,140,66,0.1);color:var(--orange);border:1px solid rgba(255,140,66,0.2);font-weight:600;}
        .rest-name{font-size:1.1rem;font-weight:700;margin-bottom:0.4rem;}
        .rest-desc{font-size:0.82rem;color:var(--text-secondary);margin-bottom:1rem;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
        .btn-menu{
            display:block;width:100%;text-align:center;
            padding:0.65rem;border-radius:10px;
            background:linear-gradient(135deg,var(--neon-cyan),#00b8c8);
            color:#0F172A;font-weight:700;font-size:0.88rem;
            text-decoration:none;transition:opacity 0.3s;
        }
        .btn-menu:hover{opacity:0.85;}

        /* EMPTY STATE */
        .empty-state{text-align:center;padding:5rem 2rem;color:var(--text-secondary);grid-column:1/-1;}
        .empty-state .empty-icon{font-size:4rem;margin-bottom:1rem;}
        .empty-state h3{font-size:1.3rem;color:var(--text-primary);margin-bottom:0.5rem;}
        .empty-state p{font-size:0.9rem;}
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<!-- HERO -->
<section class="page-hero">
    <h1>Explore <span>Restaurants</span> 🍴</h1>
    <p>Discover the best food spots near you</p>
    <form class="search-form" method="GET" action="restaurants.php" id="restaurant-search-form">
        <?php if ($category): ?>
            <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
        <?php endif; ?>
        <input
            type="text"
            name="search"
            id="search-input"
            class="search-input"
            placeholder="Search restaurants, cuisines…"
            value="<?= htmlspecialchars($search) ?>"
            autocomplete="off"
        >
        <button type="submit" class="btn-search" id="search-btn">🔍 Search</button>
    </form>
</section>

<!-- FILTER PILLS -->
<div class="filters-wrap">
    <?php foreach ($categories as $cat): ?>
        <?php
        $is_active = ($category === $cat) || ($cat === 'All' && $category === '');
        $href = 'restaurants.php?' . ($search ? 'search=' . urlencode($search) . '&' : '') . ($cat !== 'All' ? 'category=' . urlencode($cat) : '');
        ?>
        <a href="<?= $href ?>" class="filter-pill <?= $is_active ? 'active' : '' ?>" id="filter-<?= strtolower(str_replace(' ','-',$cat)) ?>">
            <?= htmlspecialchars($cat) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- RESULTS META -->
<div class="results-meta">
    <?php $count = count($restaurants); ?>
    Showing <strong><?= $count ?></strong> restaurant<?= $count !== 1 ? 's' : '' ?>
    <?= $search ? ' for "<strong>' . htmlspecialchars($search) . '</strong>"' : '' ?>
    <?= ($category && $category !== 'All') ? ' in <strong>' . htmlspecialchars($category) . '</strong>' : '' ?>
</div>

<!-- GRID -->
<div class="restaurants-grid">
    <?php if (empty($restaurants)): ?>
    <div class="empty-state">
        <div class="empty-icon">🔍</div>
        <h3>No restaurants found</h3>
        <p>Try adjusting your search or filters.</p>
    </div>
    <?php else: ?>
        <?php foreach ($restaurants as $i => $r): ?>
        <?php $open = is_open($r['opening_time'] ?? '09:00', $r['closing_time'] ?? '22:00'); ?>
        <div class="rest-card" style="animation-delay:<?= $i * 0.07 ?>s;">
            <div class="rest-img-wrap">
                <?php if (!empty($r['image'])): ?>
                    <img src="../<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['name']) ?>" class="rest-img" loading="lazy">
                <?php else: ?>
                    <div class="rest-img-placeholder">🏪</div>
                <?php endif; ?>
                <span class="rating-badge">⭐ <?= number_format($r['avg_rating'], 1) ?> (<?= (int)$r['review_count'] ?>)</span>
                <span class="open-badge <?= $open ? 'open' : 'closed' ?>"><?= $open ? '● Open' : '● Closed' ?></span>
            </div>
            <div class="rest-body">
                <div class="rest-meta">
                    <span class="rest-cat"><?= htmlspecialchars($r['category']) ?></span>
                    <span class="rest-time">⏱ <?= htmlspecialchars($r['delivery_time'] ?? '30') ?> min</span>
                </div>
                <div class="rest-name"><?= htmlspecialchars($r['name']) ?></div>
                <div class="rest-desc"><?= htmlspecialchars($r['description'] ?? '') ?></div>
                <a href="menu.php?id=<?= (int)$r['id'] ?>" class="btn-menu" id="view-menu-<?= (int)$r['id'] ?>">View Menu →</a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/cart.js"></script>
</body>
</html>