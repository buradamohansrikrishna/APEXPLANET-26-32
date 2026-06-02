<?php
require_once 'admin_session.php';
require_once '../config/db.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid security token.');
        header('Location: foods.php');
        exit;
    }

    $action = sanitize_input($_POST['action'] ?? '');

    // Delete food
    if ($action === 'delete') {
        $food_id = (int)($_POST['food_id'] ?? 0);
        if ($food_id > 0) {
            // Delete image file if exists
            $stmt = $pdo->prepare("SELECT image FROM foods WHERE id = ?");
            $stmt->execute([$food_id]);
            $food = $stmt->fetch();
            if ($food && $food['image'] && file_exists('../' . $food['image'])) {
                unlink('../' . $food['image']);
            }
            $stmt = $pdo->prepare("DELETE FROM foods WHERE id = ?");
            $stmt->execute([$food_id]);
            set_flash_message('success', 'Food item deleted successfully.');
        }
        header('Location: foods.php');
        exit;
    }

    // Toggle availability
    if ($action === 'toggle_availability') {
        $food_id   = (int)($_POST['food_id'] ?? 0);
        $new_value = (int)($_POST['availability'] ?? 0);
        if ($food_id > 0) {
            $stmt = $pdo->prepare("UPDATE foods SET availability = ? WHERE id = ?");
            $stmt->execute([$new_value, $food_id]);
        }
        header('Location: foods.php' . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
        exit;
    }
}

// Filters
$search       = sanitize_input($_GET['search']     ?? '');
$cat_filter   = sanitize_input($_GET['category']   ?? '');
$rest_filter  = (int)($_GET['restaurant']          ?? 0);

// Build query
$where  = [];
$params = [];

if ($search !== '') {
    $where[]  = '(f.food_name LIKE ? OR f.description LIKE ?)';
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
if ($cat_filter !== '') {
    $where[]  = 'f.category = ?';
    $params[] = $cat_filter;
}
if ($rest_filter > 0) {
    $where[]  = 'f.restaurant_id = ?';
    $params[] = $rest_filter;
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT f.*, r.name AS restaurant_name
        FROM foods f
        LEFT JOIN restaurants r ON f.restaurant_id = r.id
        {$where_sql}
        ORDER BY f.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$foods = $stmt->fetchAll();

// Fetch categories for filter
$categories = $pdo->query("SELECT DISTINCT category FROM foods WHERE category IS NOT NULL AND category != '' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

// Fetch restaurants for filter
$restaurants = $pdo->query("SELECT id, name FROM restaurants ORDER BY name")->fetchAll();

$csrf_token  = generate_csrf_token();
$flash       = get_flash_message();
$page_title  = 'Manage Foods';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> – QuickBite Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #050816;
            --bg-secondary: #0a0f1e;
            --bg-glass: rgba(255,255,255,0.05);
            --border-glass: rgba(255,255,255,0.1);
            --neon-orange: #ff6b2b;
            --neon-blue: #00d4ff;
            --neon-purple: #7c3aed;
            --text-primary: #f0f0f0;
            --text-secondary: #9ca3af;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); min-height: 100vh; }

        /* SIDEBAR */
        .admin-wrapper { display: flex; min-height: 100vh; }
        .sidebar {
            width: 260px; flex-shrink: 0; background: rgba(10,15,30,0.95);
            border-right: 1px solid var(--border-glass);
            backdrop-filter: blur(20px);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; height: 100vh; z-index: 100;
            transition: transform 0.3s;
        }
        .sidebar-logo {
            padding: 24px 20px;
            border-bottom: 1px solid var(--border-glass);
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-logo .logo-icon {
            width: 38px; height: 38px; background: linear-gradient(135deg, var(--neon-orange), #ff3d00);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .sidebar-logo span { font-size: 1.2rem; font-weight: 700; color: var(--text-primary); }
        .sidebar-logo span em { color: var(--neon-orange); font-style: normal; }
        .sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
        .nav-section-label {
            font-size: 0.65rem; font-weight: 600; color: var(--text-secondary);
            text-transform: uppercase; letter-spacing: 0.08em;
            padding: 8px 20px 4px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 20px; color: var(--text-secondary);
            text-decoration: none; font-size: 0.875rem; font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .nav-item:hover { color: var(--text-primary); background: rgba(255,255,255,0.05); }
        .nav-item.active {
            color: var(--neon-orange); background: rgba(255,107,43,0.08);
            border-left-color: var(--neon-orange);
        }
        .nav-item i { width: 18px; text-align: center; font-size: 0.95rem; }
        .sidebar-footer {
            padding: 16px 20px; border-top: 1px solid var(--border-glass);
        }
        .logout-btn {
            display: flex; align-items: center; gap: 10px;
            color: var(--text-secondary); text-decoration: none;
            font-size: 0.85rem; padding: 8px 10px; border-radius: 8px;
            transition: all 0.2s;
        }
        .logout-btn:hover { color: var(--danger); background: rgba(239,68,68,0.08); }

        /* MAIN CONTENT */
        .main-content { margin-left: 260px; flex: 1; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar {
            height: 64px; display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; border-bottom: 1px solid var(--border-glass);
            background: rgba(10,15,30,0.8); backdrop-filter: blur(20px);
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-size: 1.1rem; font-weight: 600; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .admin-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, var(--neon-orange), var(--neon-purple));
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.85rem;
        }
        .content-area { padding: 28px; flex: 1; }

        /* PAGE HEADER */
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }
        .page-header h1 { font-size: 1.6rem; font-weight: 700; }
        .page-header h1 span { color: var(--neon-orange); }

        /* BUTTON */
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 10px; font-size: 0.875rem; font-weight: 600;
            border: none; cursor: pointer; text-decoration: none; transition: all 0.2s;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--neon-orange), #ff3d00);
            color: #fff; box-shadow: 0 4px 15px rgba(255,107,43,0.35);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,107,43,0.5); }
        .btn-danger { background: rgba(239,68,68,0.15); color: var(--danger); border: 1px solid rgba(239,68,68,0.3); padding: 6px 14px; }
        .btn-danger:hover { background: rgba(239,68,68,0.25); }
        .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
        .btn-edit { background: rgba(0,212,255,0.1); color: var(--neon-blue); border: 1px solid rgba(0,212,255,0.25); }
        .btn-edit:hover { background: rgba(0,212,255,0.2); }

        /* FLASH */
        .flash { padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 0.875rem; font-weight: 500; display: flex; align-items: center; gap: 10px; }
        .flash-success { background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: #34d399; }
        .flash-error   { background: rgba(239,68,68,0.12);  border: 1px solid rgba(239,68,68,0.3);  color: #f87171; }

        /* FILTER ROW */
        .filter-row {
            display: flex; gap: 12px; flex-wrap: wrap;
            background: var(--bg-glass); border: 1px solid var(--border-glass);
            border-radius: 14px; padding: 16px; margin-bottom: 20px;
            backdrop-filter: blur(20px);
        }
        .filter-row input, .filter-row select {
            background: rgba(255,255,255,0.07); border: 1px solid var(--border-glass);
            color: var(--text-primary); border-radius: 9px; padding: 9px 14px;
            font-size: 0.875rem; outline: none; transition: border 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .filter-row input:focus, .filter-row select:focus { border-color: var(--neon-orange); }
        .filter-row input { flex: 1; min-width: 180px; }
        .filter-row select option { background: #0a0f1e; }
        .filter-row .btn { padding: 9px 18px; }

        /* GLASS CARD */
        .glass-card {
            background: var(--bg-glass); border: 1px solid var(--border-glass);
            border-radius: 16px; backdrop-filter: blur(20px); overflow: hidden;
        }

        /* TABLE */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        thead tr { border-bottom: 1px solid var(--border-glass); }
        thead th {
            padding: 14px 16px; text-align: left; font-weight: 600;
            color: var(--text-secondary); font-size: 0.78rem; text-transform: uppercase;
            letter-spacing: 0.06em; white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.15s; }
        tbody tr:hover { background: rgba(255,255,255,0.03); }
        tbody tr:last-child { border-bottom: none; }
        td { padding: 12px 16px; vertical-align: middle; }

        /* TABLE IMAGE */
        .table-img {
            width: 56px; height: 56px; border-radius: 10px; object-fit: cover;
            border: 1px solid var(--border-glass);
        }
        .table-img-placeholder {
            width: 56px; height: 56px; border-radius: 10px;
            background: rgba(255,255,255,0.07); display: flex; align-items: center;
            justify-content: center; color: var(--text-secondary); font-size: 1.2rem;
            border: 1px solid var(--border-glass);
        }

        /* BADGES */
        .badge {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: 0.72rem; font-weight: 600; letter-spacing: 0.03em;
        }
        .badge-veg    { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
        .badge-nonveg { background: rgba(239,68,68,0.15);  color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
        .badge-category { background: rgba(124,58,237,0.15); color: #a78bfa; border: 1px solid rgba(124,58,237,0.3); }

        /* TOGGLE SWITCH */
        .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; cursor: pointer; inset: 0;
            background: rgba(255,255,255,0.15); border-radius: 34px;
            transition: 0.3s;
        }
        .toggle-slider:before {
            content: ''; position: absolute;
            height: 18px; width: 18px; left: 3px; bottom: 3px;
            background: #fff; border-radius: 50%; transition: 0.3s;
        }
        input:checked + .toggle-slider { background: var(--neon-orange); }
        input:checked + .toggle-slider:before { transform: translateX(20px); }

        /* ACTIONS */
        .action-group { display: flex; gap: 6px; align-items: center; }

        /* EMPTY STATE */
        .empty-state {
            text-align: center; padding: 60px 20px; color: var(--text-secondary);
        }
        .empty-state i { font-size: 3rem; margin-bottom: 16px; opacity: 0.4; }
        .empty-state p { font-size: 1rem; }

        /* MODAL */
        .modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 999;
            background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);
            align-items: center; justify-content: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: #0d1526; border: 1px solid var(--border-glass);
            border-radius: 18px; padding: 32px; max-width: 440px; width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .modal-box h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 10px; }
        .modal-box p { color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 24px; }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
        .btn-cancel {
            background: rgba(255,255,255,0.08); color: var(--text-secondary);
            border: 1px solid var(--border-glass); padding: 9px 20px;
            border-radius: 9px; cursor: pointer; font-size: 0.875rem;
        }
        .btn-cancel:hover { background: rgba(255,255,255,0.12); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">🍔</div>
            <span>Quick<em>Bite</em> <sup style="font-size:0.55rem;color:var(--neon-orange);vertical-align:super;">ADMIN</sup></span>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-label">Main</div>
            <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="orders.php"    class="nav-item"><i class="fas fa-receipt"></i> Orders</a>

            <div class="nav-section-label">Catalog</div>
            <a href="foods.php"       class="nav-item active"><i class="fas fa-utensils"></i> Foods</a>
            <a href="restaurants.php" class="nav-item"><i class="fas fa-store"></i> Restaurants</a>
            <a href="categories.php"  class="nav-item"><i class="fas fa-tags"></i> Categories</a>

            <div class="nav-section-label">Users</div>
            <a href="users.php"   class="nav-item"><i class="fas fa-users"></i> Users</a>
            <a href="coupons.php" class="nav-item"><i class="fas fa-ticket"></i> Coupons</a>

            <div class="nav-section-label">System</div>
            <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i> Settings</a>
        </nav>
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="main-content">
        <div class="topbar">
            <span class="topbar-title">🍔 Food Management</span>
            <div class="topbar-right">
                <span style="color:var(--text-secondary);font-size:0.8rem;"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
                <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?></div>
            </div>
        </div>

        <div class="content-area">

            <?php if ($flash): ?>
                <div class="flash flash-<?= $flash['type'] ?>">
                    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <div class="page-header">
                <h1>Manage <span>Foods</span></h1>
                <a href="add-food.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Food
                </a>
            </div>

            <!-- FILTER ROW -->
            <form method="GET" action="foods.php">
                <div class="filter-row">
                    <input type="text" name="search" placeholder="🔍 Search foods..." value="<?= htmlspecialchars($search) ?>">
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $cat_filter === $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="restaurant">
                        <option value="">All Restaurants</option>
                        <?php foreach ($restaurants as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= $rest_filter === $r['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                    <a href="foods.php" class="btn" style="background:rgba(255,255,255,0.08);color:var(--text-secondary);border:1px solid var(--border-glass);">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>

            <!-- TABLE -->
            <div class="glass-card">
                <div class="table-wrapper">
                    <table id="adminDataTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Restaurant</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Available</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($foods)): ?>
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-state">
                                            <i class="fas fa-utensils"></i>
                                            <p>No food items found. <a href="add-food.php" style="color:var(--neon-orange);">Add the first one!</a></p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($foods as $food): ?>
                                <tr>
                                    <td style="color:var(--text-secondary);font-size:0.8rem;">#<?= $food['id'] ?></td>
                                    <td>
                                        <?php if (!empty($food['image']) && file_exists('../' . $food['image'])): ?>
                                            <img src="../<?= htmlspecialchars($food['image']) ?>" alt="<?= htmlspecialchars($food['food_name']) ?>" class="table-img">
                                        <?php else: ?>
                                            <div class="table-img-placeholder"><i class="fas fa-image"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;"><?= htmlspecialchars($food['food_name']) ?></div>
                                        <?php if (!empty($food['calories'])): ?>
                                            <div style="font-size:0.75rem;color:var(--text-secondary);"><?= $food['calories'] ?> kcal</div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color:var(--text-secondary);font-size:0.85rem;"><?= htmlspecialchars($food['restaurant_name'] ?? '—') ?></td>
                                    <td style="color:var(--neon-orange);font-weight:600;">₹<?= number_format($food['price'], 2) ?></td>
                                    <td><?php if ($food['category']): ?><span class="badge badge-category"><?= htmlspecialchars($food['category']) ?></span><?php else: ?>—<?php endif; ?></td>
                                    <td>
                                        <span class="badge <?= $food['is_veg'] ? 'badge-veg' : 'badge-nonveg' ?>">
                                            <?= $food['is_veg'] ? '🟢 Veg' : '🔴 Non-Veg' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="foods.php" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <input type="hidden" name="action" value="toggle_availability">
                                            <input type="hidden" name="food_id" value="<?= $food['id'] ?>">
                                            <input type="hidden" name="availability" value="<?= $food['availability'] ? 0 : 1 ?>">
                                            <label class="toggle-switch" title="<?= $food['availability'] ? 'Available' : 'Unavailable' ?>">
                                                <input type="checkbox" <?= $food['availability'] ? 'checked' : '' ?> onchange="this.closest('form').submit()">
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <a href="edit-food.php?id=<?= $food['id'] ?>" class="btn btn-edit btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm" onclick="openDeleteModal(<?= $food['id'] ?>, '<?= htmlspecialchars(addslashes($food['food_name'])) ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /content-area -->
    </div><!-- /main-content -->
</div><!-- /admin-wrapper -->

<!-- DELETE CONFIRM MODAL -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <h3><i class="fas fa-exclamation-triangle" style="color:var(--danger);margin-right:8px;"></i> Delete Food Item</h3>
        <p id="deleteModalText">Are you sure you want to delete this food item? This action cannot be undone.</p>
        <form method="POST" action="foods.php" id="deleteForm">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="food_id" id="deleteFoodId" value="">
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/main.js" defer></script>
<script>
function openDeleteModal(id, name) {
    document.getElementById('deleteFoodId').value = id;
    document.getElementById('deleteModalText').textContent = `Are you sure you want to delete "${name}"? This action cannot be undone.`;
    document.getElementById('deleteModal').classList.add('active');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
</body>
</html>