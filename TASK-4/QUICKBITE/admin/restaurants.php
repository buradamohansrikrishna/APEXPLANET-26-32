<?php
require_once 'admin_session.php';
require_once '../config/db.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

$msg = '';
$msg_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $msg = 'Invalid request.'; $msg_type = 'error';
    } else {
        $action = sanitize_string($_POST['action'] ?? '');

        if ($action === 'add' || $action === 'edit') {
            $name       = sanitize_string($_POST['name'] ?? '');
            $address    = sanitize_string($_POST['address'] ?? '');
            $location   = sanitize_string($_POST['location'] ?? '');
            $category   = sanitize_string($_POST['category'] ?? '');
            $open_time  = sanitize_string($_POST['opening_time'] ?? '09:00');
            $close_time = sanitize_string($_POST['closing_time'] ?? '22:00');
            $del_time   = (int)($_POST['delivery_time'] ?? 30);
            $min_order  = (float)($_POST['min_order'] ?? 0);
            $status     = sanitize_string($_POST['status'] ?? 'active');

            if (!$name || !$location) {
                $msg = 'Name and location are required.'; $msg_type = 'error';
            } elseif ($action === 'add') {
                $stmt = $conn->prepare(
                    "INSERT INTO restaurants (restaurant_name, address, location, category, opening_time, closing_time, delivery_time, min_order, status)
                     VALUES (?,?,?,?,?,?,?,?,'active')"
                );
                $stmt->bind_param('ssssssid', $name, $address, $location, $category, $open_time, $close_time, $del_time, $min_order);
                $stmt->execute() ? ($msg = "Restaurant '$name' added!") : ($msg = 'DB error.'); $msg_type = 'error';
                $stmt->close();
            } else {
                $id = (int)($_POST['restaurant_id'] ?? 0);
                $stmt = $conn->prepare(
                    "UPDATE restaurants SET restaurant_name=?, address=?, location=?, category=?, opening_time=?, closing_time=?, delivery_time=?, min_order=?, status=? WHERE id=?"
                );
                $stmt->bind_param('ssssssidsi', $name, $address, $location, $category, $open_time, $close_time, $del_time, $min_order, $status, $id);
                $stmt->execute() ? ($msg = "Restaurant '$name' updated!") : ($msg = 'Update failed.'); $msg_type = 'error';
                $stmt->close();
            }
        } elseif ($action === 'delete') {
            $id = (int)($_POST['restaurant_id'] ?? 0);
            $conn->query("DELETE FROM restaurants WHERE id = $id");
            $msg = 'Restaurant deleted.';
        } elseif ($action === 'toggle') {
            $id = (int)($_POST['restaurant_id'] ?? 0);
            $conn->query("UPDATE restaurants SET status = IF(status='active','inactive','active') WHERE id=$id");
            $msg = 'Status toggled.';
        }
    }
}

$search = sanitize_string($_GET['search'] ?? '');
$where  = '1=1';
if ($search) $where .= " AND (restaurant_name LIKE '%" . $conn->real_escape_string($search) . "%' OR location LIKE '%" . $conn->real_escape_string($search) . "%')";

$restaurants = db_fetch_all($conn, "SELECT r.*, r.restaurant_name AS name, COUNT(f.id) AS food_count FROM restaurants r LEFT JOIN foods f ON f.restaurant_id = r.id WHERE $where GROUP BY r.id ORDER BY r.id DESC");
$csrf_token = generate_csrf_token();

$edit_id = (int)($_GET['edit'] ?? 0);
$edit_rest = $edit_id ? db_fetch($conn, "SELECT *, restaurant_name AS name FROM restaurants WHERE id = ?", 'i', [$edit_id]) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management — QuickBite Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        :root{--neon-cyan:#FF5A00;--bg-dark:#F8FAFC;--bg-secondary:#FFFFFF;--bg-card:#FFFFFF;--text-primary:#0F172A;--text-secondary:#475569;--border-glass:#E2E8F0;--green:#00D084;--red:#FF4545;--orange:#FF8C42;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:var(--bg-dark);color:var(--text-primary);display:flex;min-height:100vh;}
        .sidebar{width:240px;background:var(--bg-secondary);border-right:1px solid var(--border-glass);display:flex;flex-direction:column;position:fixed;height:100vh;z-index:100;}
        .sidebar-logo{padding:1.5rem;font-size:1.3rem;font-weight:800;border-bottom:1px solid var(--border-glass);}
        .sidebar-logo span{color:var(--neon-cyan);}
        .sidebar-nav{flex:1;padding:1rem 0;}
        .nav-item{display:flex;align-items:center;gap:0.8rem;padding:0.75rem 1.5rem;color:var(--text-secondary);text-decoration:none;font-size:0.88rem;font-weight:600;transition:all 0.25s;border-left:3px solid transparent;}
        .nav-item:hover,.nav-item.active{background:rgba(255,71,71,0.06);color:var(--neon-cyan);border-left-color:var(--neon-cyan);}
        .nav-icon{font-size:1.1rem;width:20px;text-align:center;}
        .sidebar-footer{padding:1rem 1.5rem;border-top:1px solid var(--border-glass);}
        .main-content{margin-left:240px;flex:1;padding:2rem;}
        .page-title{font-size:1.6rem;font-weight:800;margin-bottom:0.3rem;}
        .page-sub{font-size:0.88rem;color:var(--text-secondary);margin-bottom:2rem;}
        .alert{padding:0.9rem 1.2rem;border-radius:12px;margin-bottom:1.5rem;font-size:0.88rem;font-weight:600;}
        .alert-success{background:rgba(0,208,132,0.1);border:1px solid rgba(0,208,132,0.3);color:var(--green);}
        .alert-error{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#EF4444;}

        .layout-grid{display:grid;grid-template-columns:360px 1fr;gap:1.5rem;align-items:start;}
        @media(max-width:1100px){.layout-grid{grid-template-columns:1fr;}}

        .form-panel{background:var(--bg-card);border:1px solid var(--border-glass);border-radius:18px;padding:1.8rem;position:sticky;top:2rem;}
        .form-panel-title{font-size:1rem;font-weight:700;margin-bottom:1.4rem;border-bottom:1px solid var(--border-glass);padding-bottom:0.7rem;}
        .form-group{display:flex;flex-direction:column;gap:0.4rem;margin-bottom:0.9rem;}
        .form-label{font-size:0.78rem;font-weight:600;color:var(--text-secondary);}
        .form-input{padding:0.65rem 0.9rem;background:rgba(255,255,255,0.05);border:1px solid var(--border-glass);border-radius:10px;color:var(--text-primary);font-size:0.88rem;font-family:'Inter',sans-serif;outline:none;transition:border-color 0.25s;}
        .form-input:focus{border-color:var(--neon-cyan);}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:0.8rem;}
        .btn-submit{padding:0.75rem;background:linear-gradient(135deg,var(--neon-cyan),#00b8c8);border:none;border-radius:10px;color:#0F172A;font-weight:700;font-size:0.9rem;cursor:pointer;font-family:'Inter',sans-serif;transition:all 0.3s;width:100%;}
        .btn-submit:hover{opacity:0.88;}
        .btn-reset{padding:0.65rem;background:rgba(255,255,255,0.05);border:1px solid var(--border-glass);border-radius:10px;color:var(--text-secondary);font-weight:600;font-size:0.85rem;cursor:pointer;font-family:'Inter',sans-serif;width:100%;margin-top:0.5rem;text-decoration:none;display:block;text-align:center;transition:all 0.25s;}
        .btn-reset:hover{background:rgba(255,255,255,0.1);}

        /* SEARCH + GRID */
        .search-bar{display:flex;gap:0.8rem;margin-bottom:1.5rem;flex-wrap:wrap;}
        .s-input{flex:1;padding:0.65rem 1rem;background:rgba(255,255,255,0.05);border:1px solid var(--border-glass);border-radius:10px;color:var(--text-primary);font-size:0.88rem;outline:none;transition:border-color 0.25s;font-family:'Inter',sans-serif;}
        .s-input:focus{border-color:var(--neon-cyan);}
        .s-btn{padding:0.65rem 1.2rem;background:rgba(255,71,71,0.12);border:1px solid rgba(255,71,71,0.3);border-radius:10px;color:var(--neon-cyan);font-weight:700;font-size:0.88rem;cursor:pointer;font-family:'Inter',sans-serif;}

        .rest-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.2rem;}
        .rest-card{background:var(--bg-card);border:1px solid var(--border-glass);border-radius:16px;overflow:hidden;transition:all 0.3s;}
        .rest-card:hover{border-color:rgba(255,71,71,0.2);transform:translateY(-3px);}
        .rest-img{height:140px;object-fit:cover;width:100%;background:linear-gradient(135deg,#FFFFFF,#0d1a40);}
        .rest-body{padding:1rem;}
        .rest-name{font-size:1rem;font-weight:700;margin-bottom:0.3rem;}
        .rest-meta{font-size:0.78rem;color:var(--text-secondary);margin-bottom:0.6rem;line-height:1.5;}
        .rest-badges{display:flex;gap:0.4rem;flex-wrap:wrap;margin-bottom:0.9rem;}
        .badge-sm{font-size:0.68rem;padding:0.2rem 0.55rem;border-radius:10px;font-weight:600;}
        .badge-cyan{background:rgba(255,71,71,0.1);color:var(--neon-cyan);border:1px solid rgba(255,71,71,0.2);}
        .badge-green{background:rgba(0,208,132,0.1);color:var(--green);border:1px solid rgba(0,208,132,0.2);}
        .badge-grey{background:rgba(255,255,255,0.06);color:var(--text-secondary);border:1px solid var(--border-glass);}
        .rest-actions{display:flex;gap:0.5rem;}
        .btn-act{flex:1;padding:0.45rem 0.5rem;border-radius:8px;font-size:0.75rem;font-weight:700;cursor:pointer;border:1px solid;font-family:'Inter',sans-serif;text-align:center;text-decoration:none;transition:all 0.2s;display:inline-flex;align-items:center;justify-content:center;gap:0.3rem;}
        .btn-edit{background:rgba(255,71,71,0.08);border-color:rgba(255,71,71,0.25);color:var(--neon-cyan);}
        .btn-edit:hover{background:rgba(255,71,71,0.15);}
        .btn-toggle-r{background:rgba(249,115,22,0.08);border-color:rgba(249,115,22,0.25);color:#F97316;}
        .btn-toggle-r:hover{background:rgba(249,115,22,0.15);}
        .btn-del-r{background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.25);color:#EF4444;}
        .btn-del-r:hover{background:rgba(239,68,68,0.15);}
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-logo">Quick<span>Bite</span></div>
    <nav class="sidebar-nav">
        <a href="dashboard.php"   class="nav-item"><span class="nav-icon">📊</span> Dashboard</a>
        <a href="orders.php"      class="nav-item"><span class="nav-icon">📦</span> Orders</a>
        <a href="foods.php"       class="nav-item"><span class="nav-icon">🍔</span> Foods</a>
        <a href="restaurants.php" class="nav-item active"><span class="nav-icon">🏪</span> Restaurants</a>
        <a href="users.php"       class="nav-item"><span class="nav-icon">👥</span> Users</a>
        <a href="coupons.php"     class="nav-item"><span class="nav-icon">🎟️</span> Coupons</a>
    </nav>
    <div class="sidebar-footer"><a href="../auth/logout.php" style="color:var(--red);font-size:0.85rem;font-weight:600;text-decoration:none;">🚪 Logout</a></div>
</aside>

<main class="main-content">
    <div class="page-title">🏪 Restaurant Management</div>
    <div class="page-sub"><?= count($restaurants) ?> restaurant<?= count($restaurants)!=1?'s':'' ?> total</div>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msg_type==='error'?'error':'success' ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="layout-grid">

        <!-- FORM PANEL -->
        <div class="form-panel">
            <div class="form-panel-title"><?= $edit_rest ? '✏️ Edit Restaurant' : '➕ Add Restaurant' ?></div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="action" value="<?= $edit_rest ? 'edit' : 'add' ?>">
                <?php if ($edit_rest): ?>
                <input type="hidden" name="restaurant_id" value="<?= $edit_rest['id'] ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Restaurant Name *</label>
                    <input type="text" class="form-input" name="name" required value="<?= htmlspecialchars($edit_rest['name'] ?? '') ?>" placeholder="e.g. Pizza Hub">
                </div>
                <div class="form-group">
                    <label class="form-label">Full Address</label>
                    <input type="text" class="form-input" name="address" value="<?= htmlspecialchars($edit_rest['address'] ?? '') ?>" placeholder="Street, Area, City">
                </div>
                <div class="form-group">
                    <label class="form-label">Location / Area *</label>
                    <input type="text" class="form-input" name="location" required value="<?= htmlspecialchars($edit_rest['location'] ?? '') ?>" placeholder="Banjara Hills, Hyderabad">
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <input type="text" class="form-input" name="category" value="<?= htmlspecialchars($edit_rest['category'] ?? '') ?>" placeholder="North Indian, Fast Food…">
                </div>
                <div class="form-row">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Opening Time</label>
                        <input type="time" class="form-input" name="opening_time" value="<?= htmlspecialchars($edit_rest['opening_time'] ?? '09:00') ?>">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Closing Time</label>
                        <input type="time" class="form-input" name="closing_time" value="<?= htmlspecialchars($edit_rest['closing_time'] ?? '22:00') ?>">
                    </div>
                </div>
                <div class="form-row" style="margin-top:0.9rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Delivery Time (min)</label>
                        <input type="number" class="form-input" name="delivery_time" value="<?= htmlspecialchars($edit_rest['delivery_time'] ?? '30') ?>" min="5" max="120">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Min Order (₹)</label>
                        <input type="number" class="form-input" name="min_order" value="<?= htmlspecialchars($edit_rest['min_order'] ?? '0') ?>" min="0" step="0.01">
                    </div>
                </div>
                <?php if ($edit_rest): ?>
                <div class="form-group" style="margin-top:0.9rem;">
                    <label class="form-label">Status</label>
                    <select class="form-input" name="status">
                        <option value="active" <?= ($edit_rest['status']??'active')==='active'?'selected':'' ?>>Active</option>
                        <option value="inactive" <?= ($edit_rest['status']??'active')==='inactive'?'selected':'' ?>>Inactive</option>
                    </select>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn-submit" style="margin-top:1rem;"><?= $edit_rest ? '💾 Save Changes' : '➕ Add Restaurant' ?></button>
                <?php if ($edit_rest): ?>
                <a href="restaurants.php" class="btn-reset">✕ Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- RESTAURANT GRID -->
        <div>
            <form method="GET" class="search-bar">
                <input type="text" class="s-input" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="🔍 Search restaurants…">
                <button type="submit" class="s-btn">Search</button>
            </form>

            <?php if (empty($restaurants)): ?>
            <div style="text-align:center;padding:3rem;color:var(--text-secondary);">No restaurants found. Add one!</div>
            <?php else: ?>
            <div class="rest-grid">
                <?php foreach ($restaurants as $r):
                    $is_active = ($r['status'] ?? 'active') === 'active';
                ?>
                <div class="rest-card">
                    <?php if (!empty($r['image'])): ?>
                    <img src="../<?= htmlspecialchars($r['image']) ?>" class="rest-img" alt="<?= htmlspecialchars($r['name']) ?>">
                    <?php else: ?>
                    <div class="rest-img" style="display:flex;align-items:center;justify-content:center;font-size:3rem;">🏪</div>
                    <?php endif; ?>
                    <div class="rest-body">
                        <div class="rest-name"><?= htmlspecialchars($r['name']) ?></div>
                        <div class="rest-meta">
                            📍 <?= htmlspecialchars($r['location'] ?? '') ?><br>
                            ⏱ <?= htmlspecialchars($r['delivery_time'] ?? '30') ?> min · Min ₹<?= number_format($r['min_order'] ?? 0, 0) ?>
                        </div>
                        <div class="rest-badges">
                            <?php if ($r['category']): ?><span class="badge-sm badge-cyan"><?= htmlspecialchars($r['category']) ?></span><?php endif; ?>
                            <span class="badge-sm <?= $is_active ? 'badge-green' : 'badge-grey' ?>"><?= $is_active ? '● Open' : '● Closed' ?></span>
                            <span class="badge-sm badge-grey"><?= (int)$r['food_count'] ?> foods</span>
                        </div>
                        <div class="rest-actions">
                            <a href="restaurants.php?edit=<?= $r['id'] ?>" class="btn-act btn-edit">✏️ Edit</a>
                            <form method="POST" style="flex:1">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="restaurant_id" value="<?= $r['id'] ?>">
                                <button type="submit" class="btn-act btn-toggle-r" style="width:100%;"><?= $is_active ? '⏸ Close' : '▶ Open' ?></button>
                            </form>
                            <form method="POST" style="flex:1" onsubmit="return confirm('Delete <?= htmlspecialchars($r['name']) ?>?');">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="restaurant_id" value="<?= $r['id'] ?>">
                                <button type="submit" class="btn-act btn-del-r" style="width:100%;">🗑</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

    </div>
</main>
</body>
</html>
