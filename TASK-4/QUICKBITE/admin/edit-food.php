<?php
require_once 'admin_session.php';
require_once '../config/db.php';
require_once '../includes/security.php';

$food_id = (int)($_GET['id'] ?? 0);
if ($food_id <= 0) {
    header('Location: foods.php');
    exit;
}

// Fetch food
$stmt = $pdo->prepare("SELECT f.*, r.name AS restaurant_name FROM foods f LEFT JOIN restaurants r ON f.restaurant_id = r.id WHERE f.id = ?");
$stmt->execute([$food_id]);
$food = $stmt->fetch();

if (!$food) {
    set_flash_message('error', 'Food item not found.');
    header('Location: foods.php');
    exit;
}

$errors  = [];
$success = false;

// Fetch restaurants
$restaurants = $pdo->query("SELECT id, name FROM restaurants ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_food') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token.';
    } else {
        $restaurant_id = (int)($_POST['restaurant_id'] ?? 0);
        $food_name     = sanitize_input($_POST['food_name'] ?? '');
        $price         = floatval($_POST['price'] ?? 0);
        $category      = sanitize_input($_POST['category'] ?? '');
        $description   = sanitize_input($_POST['description'] ?? '');
        $ingredients   = sanitize_input($_POST['ingredients'] ?? '');
        $calories      = (int)($_POST['calories'] ?? 0);
        $prep_time     = (int)($_POST['prep_time'] ?? 0);
        $is_veg        = isset($_POST['is_veg']) ? 1 : 0;
        $spice_level   = sanitize_input($_POST['spice_level'] ?? 'mild');
        $availability  = isset($_POST['availability']) ? 1 : 0;

        if ($restaurant_id <= 0) $errors[] = 'Please select a restaurant.';
        if (empty($food_name))   $errors[] = 'Food name is required.';
        if ($price <= 0)         $errors[] = 'Price must be greater than 0.';
        if (empty($category))    $errors[] = 'Category is required.';

        $allowed_spice = ['mild', 'medium', 'hot', 'extra_hot'];
        if (!in_array($spice_level, $allowed_spice)) $spice_level = 'mild';

        // Image upload (optional)
        $image_path = $food['image']; // keep existing
        if (!empty($_FILES['image']['name'])) {
            $upload_result = upload_file($_FILES['image'], '../assets/images/foods/', ['image/jpeg','image/png','image/webp','image/gif'], 5 * 1024 * 1024);
            if ($upload_result['success']) {
                // Remove old image
                if ($image_path && file_exists('../' . $image_path)) {
                    unlink('../' . $image_path);
                }
                $image_path = 'assets/images/foods/' . $upload_result['filename'];
            } else {
                $errors[] = $upload_result['error'];
            }
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("
                UPDATE foods
                SET restaurant_id=?, food_name=?, price=?, category=?, description=?, ingredients=?,
                    calories=?, prep_time=?, is_veg=?, spice_level=?, availability=?, image=?, updated_at=NOW()
                WHERE id=?
            ");
            $stmt->execute([$restaurant_id, $food_name, $price, $category, $description, $ingredients, $calories, $prep_time, $is_veg, $spice_level, $availability, $image_path, $food_id]);

            set_flash_message('success', "'{$food_name}' updated successfully!");
            header('Location: foods.php');
            exit;
        }

        // Keep POST values in $food for re-display
        $food = array_merge($food, [
            'restaurant_id' => $restaurant_id, 'food_name' => $food_name, 'price' => $price,
            'category' => $category, 'description' => $description, 'ingredients' => $ingredients,
            'calories' => $calories, 'prep_time' => $prep_time, 'is_veg' => $is_veg,
            'spice_level' => $spice_level, 'availability' => $availability,
        ]);
    }
}

$csrf_token = generate_csrf_token();
$page_title = 'Edit Food: ' . htmlspecialchars($food['food_name']);

$categories_list = ['Burger', 'Pizza', 'Sushi', 'Pasta', 'Biryani', 'Salad', 'Dessert', 'Drinks', 'Sandwich', 'Chinese', 'South Indian', 'North Indian', 'Street Food', 'Healthy', 'Snacks'];
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
            --bg-primary: #050816; --bg-secondary: #0a0f1e;
            --bg-glass: rgba(255,255,255,0.05); --border-glass: rgba(255,255,255,0.1);
            --neon-orange: #ff6b2b; --neon-blue: #00d4ff; --neon-purple: #7c3aed;
            --text-primary: #f0f0f0; --text-secondary: #9ca3af;
            --success: #10b981; --danger: #ef4444;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); min-height: 100vh; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; flex-shrink: 0; background: rgba(10,15,30,0.95); border-right: 1px solid var(--border-glass); backdrop-filter: blur(20px); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; height: 100vh; z-index: 100; }
        .sidebar-logo { padding: 24px 20px; border-bottom: 1px solid var(--border-glass); display: flex; align-items: center; gap: 10px; }
        .sidebar-logo .logo-icon { width: 38px; height: 38px; background: linear-gradient(135deg, var(--neon-orange), #ff3d00); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .sidebar-logo span { font-size: 1.2rem; font-weight: 700; }
        .sidebar-logo span em { color: var(--neon-orange); font-style: normal; }
        .sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
        .nav-section-label { font-size: 0.65rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.08em; padding: 8px 20px 4px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; color: var(--text-secondary); text-decoration: none; font-size: 0.875rem; font-weight: 500; border-left: 3px solid transparent; transition: all 0.2s; }
        .nav-item:hover { color: var(--text-primary); background: rgba(255,255,255,0.05); }
        .nav-item.active { color: var(--neon-orange); background: rgba(255,107,43,0.08); border-left-color: var(--neon-orange); }
        .nav-item i { width: 18px; text-align: center; }
        .sidebar-footer { padding: 16px 20px; border-top: 1px solid var(--border-glass); }
        .logout-btn { display: flex; align-items: center; gap: 10px; color: var(--text-secondary); text-decoration: none; font-size: 0.85rem; padding: 8px 10px; border-radius: 8px; transition: all 0.2s; }
        .logout-btn:hover { color: var(--danger); }
        .main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; }
        .topbar { height: 64px; display: flex; align-items: center; justify-content: space-between; padding: 0 28px; border-bottom: 1px solid var(--border-glass); background: rgba(10,15,30,0.8); backdrop-filter: blur(20px); position: sticky; top: 0; z-index: 50; }
        .topbar-title { font-size: 1.1rem; font-weight: 600; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .admin-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--neon-orange), var(--neon-purple)); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; }
        .content-area { padding: 28px; flex: 1; max-width: 900px; }
        .page-header { display: flex; align-items: center; gap: 14px; margin-bottom: 28px; flex-wrap: wrap; }
        .page-header a { color: var(--text-secondary); text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 6px; }
        .page-header a:hover { color: var(--text-primary); }
        .page-header h1 { font-size: 1.6rem; font-weight: 700; }
        .page-header h1 span { color: var(--neon-orange); }
        .form-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 18px; padding: 32px; backdrop-filter: blur(20px); }
        .form-section { margin-bottom: 30px; }
        .form-section-title { font-size: 0.85rem; font-weight: 700; color: var(--neon-orange); text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 18px; padding-bottom: 10px; border-bottom: 1px solid var(--border-glass); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .form-grid.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group.full-width { grid-column: 1 / -1; }
        label { font-size: 0.82rem; font-weight: 600; color: var(--text-secondary); }
        .form-control { background: rgba(255,255,255,0.07); border: 1px solid var(--border-glass); color: var(--text-primary); border-radius: 10px; padding: 11px 14px; font-size: 0.875rem; outline: none; transition: border 0.2s, box-shadow 0.2s; font-family: 'Inter', sans-serif; width: 100%; }
        .form-control:focus { border-color: var(--neon-orange); box-shadow: 0 0 0 3px rgba(255,107,43,0.12); }
        .form-control option { background: #0a0f1e; }
        textarea.form-control { resize: vertical; min-height: 90px; }
        .toggle-field { display: flex; align-items: center; gap: 14px; padding: 11px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--border-glass); border-radius: 10px; }
        .toggle-label { font-size: 0.875rem; font-weight: 500; flex: 1; }
        .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; cursor: pointer; inset: 0; background: rgba(255,255,255,0.15); border-radius: 34px; transition: 0.3s; }
        .toggle-slider:before { content: ''; position: absolute; height: 18px; width: 18px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: 0.3s; }
        input:checked + .toggle-slider { background: var(--neon-orange); }
        input:checked + .toggle-slider:before { transform: translateX(20px); }

        /* Current image */
        .current-image-box { background: rgba(255,255,255,0.04); border: 1px solid var(--border-glass); border-radius: 12px; padding: 16px; display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
        .current-image-box img { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; border: 1px solid var(--border-glass); }
        .current-image-box .info { flex: 1; }
        .current-image-box .info p { font-size: 0.85rem; color: var(--text-secondary); }
        .current-image-box .info small { font-size: 0.75rem; color: var(--text-secondary); opacity: 0.6; }

        .drop-zone { border: 2px dashed var(--border-glass); border-radius: 14px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s; position: relative; background: rgba(255,255,255,0.02); }
        .drop-zone.dragover { border-color: var(--neon-orange); background: rgba(255,107,43,0.05); }
        .drop-zone i { font-size: 2rem; color: var(--text-secondary); margin-bottom: 10px; display: block; }
        .drop-zone p { color: var(--text-secondary); font-size: 0.85rem; }
        .drop-zone p strong { color: var(--neon-orange); }
        .drop-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        #dropZonePreview { display: none; }
        #dropZonePreview img { max-height: 160px; border-radius: 10px; object-fit: cover; max-width: 100%; }

        .alert { padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 0.875rem; }
        .alert-danger { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #f87171; }
        .alert ul { margin: 8px 0 0 18px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 24px; border-radius: 10px; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; text-decoration: none; transition: all 0.2s; font-family: 'Inter', sans-serif; }
        .btn-primary { background: linear-gradient(135deg, var(--neon-orange), #ff3d00); color: #fff; box-shadow: 0 4px 15px rgba(255,107,43,0.35); }
        .btn-primary:hover { transform: translateY(-2px); }
        .btn-secondary { background: rgba(255,255,255,0.08); color: var(--text-secondary); border: 1px solid var(--border-glass); }
        .form-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 30px; padding-top: 24px; border-top: 1px solid var(--border-glass); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
            .form-grid { grid-template-columns: 1fr; }
            .form-grid.cols-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
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

    <div class="main-content">
        <div class="topbar">
            <span class="topbar-title">✏️ Edit Food Item</span>
            <div class="topbar-right">
                <span style="color:var(--text-secondary);font-size:0.8rem;"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
                <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?></div>
            </div>
        </div>

        <div class="content-area">
            <div class="page-header">
                <a href="foods.php"><i class="fas fa-arrow-left"></i> Back to Foods</a>
                <h1>Edit <span><?= htmlspecialchars($food['food_name']) ?></span></h1>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <strong><i class="fas fa-exclamation-circle"></i> Please fix:</strong>
                    <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="edit-food.php?id=<?= $food_id ?>" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="action" value="edit_food">

                <div class="form-card">
                    <!-- BASIC INFO -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-info-circle"></i> Basic Information</div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="restaurant_id">Restaurant *</label>
                                <select name="restaurant_id" id="restaurant_id" class="form-control" required>
                                    <option value="">— Select Restaurant —</option>
                                    <?php foreach ($restaurants as $r): ?>
                                        <option value="<?= $r['id'] ?>" <?= $food['restaurant_id'] == $r['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($r['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="food_name">Food Name *</label>
                                <input type="text" name="food_name" id="food_name" class="form-control" value="<?= htmlspecialchars($food['food_name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="price">Price (₹) *</label>
                                <input type="number" name="price" id="price" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($food['price']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="category">Category *</label>
                                <select name="category" id="category" class="form-control" required>
                                    <option value="">— Select Category —</option>
                                    <?php foreach (['Burger','Pizza','Sushi','Pasta','Biryani','Salad','Dessert','Drinks','Sandwich','Chinese','South Indian','North Indian','Street Food','Healthy','Snacks'] as $cat): ?>
                                        <option value="<?= $cat ?>" <?= $food['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div style="margin-top:18px;display:flex;flex-direction:column;gap:18px;">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" class="form-control"><?= htmlspecialchars($food['description'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="ingredients">Ingredients</label>
                                <textarea name="ingredients" id="ingredients" class="form-control"><?= htmlspecialchars($food['ingredients'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- NUTRITION -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-fire"></i> Nutrition & Details</div>
                        <div class="form-grid cols-3">
                            <div class="form-group">
                                <label for="calories">Calories (kcal)</label>
                                <input type="number" name="calories" id="calories" class="form-control" min="0" value="<?= htmlspecialchars($food['calories'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="prep_time">Prep Time (min)</label>
                                <input type="number" name="prep_time" id="prep_time" class="form-control" min="0" value="<?= htmlspecialchars($food['prep_time'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="spice_level">Spice Level</label>
                                <select name="spice_level" id="spice_level" class="form-control">
                                    <?php foreach (['mild'=>'🟡 Mild','medium'=>'🟠 Medium','hot'=>'🔴 Hot','extra_hot'=>'🌶️ Extra Hot'] as $val=>$label): ?>
                                        <option value="<?= $val ?>" <?= ($food['spice_level'] ?? 'mild') === $val ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-grid" style="margin-top:18px;">
                            <div class="form-group">
                                <label>Diet Type</label>
                                <div class="toggle-field">
                                    <span class="toggle-label">🟢 Vegetarian</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="is_veg" <?= $food['is_veg'] ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Availability</label>
                                <div class="toggle-field">
                                    <span class="toggle-label">Available for Order</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="availability" <?= $food['availability'] ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- IMAGE -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-image"></i> Food Image</div>
                        <?php if (!empty($food['image']) && file_exists('../' . $food['image'])): ?>
                        <div class="current-image-box">
                            <img src="../<?= htmlspecialchars($food['image']) ?>" alt="Current image">
                            <div class="info">
                                <p>Current image</p>
                                <small><?= htmlspecialchars($food['image']) ?></small>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="drop-zone" id="dropZone">
                            <input type="file" name="image" id="imageInput" accept="image/*">
                            <div id="dropZoneContent">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p><strong>Click to upload new image</strong> or drag & drop</p>
                                <p style="font-size:0.78rem;margin-top:4px;opacity:0.6;">Leave empty to keep current image. PNG, JPG, WEBP up to 5MB</p>
                            </div>
                            <div id="dropZonePreview">
                                <img id="previewImg" src="" alt="New preview">
                                <p style="font-size:0.8rem;margin-top:8px;color:var(--neon-orange);">New image selected</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="foods.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
const imageInput = document.getElementById('imageInput');
const previewImg = document.getElementById('previewImg');
const previewEl  = document.getElementById('dropZonePreview');
const contentEl  = document.getElementById('dropZoneContent');
const dropZone   = document.getElementById('dropZone');

imageInput.addEventListener('change', () => {
    if (imageInput.files[0]) showPreview(imageInput.files[0]);
});
function showPreview(file) {
    const reader = new FileReader();
    reader.onload = e => { previewImg.src = e.target.result; previewEl.style.display = 'block'; contentEl.style.display = 'none'; };
    reader.readAsDataURL(file);
}
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault(); dropZone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) { const dt = new DataTransfer(); dt.items.add(file); imageInput.files = dt.files; showPreview(file); }
});
</script>
</body>
</html>
