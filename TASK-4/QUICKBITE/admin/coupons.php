<?php
require_once 'admin_session.php';
require_once '../config/db.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

// Handle Add/Edit/Delete form submissions
$msg = '';
$msg_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $msg = 'Invalid request.'; $msg_type = 'error';
    } else {
        $action = sanitize_string($_POST['action'] ?? '');

        if ($action === 'add') {
            $code        = strtoupper(sanitize_string($_POST['code'] ?? ''));
            $desc        = sanitize_string($_POST['description'] ?? '');
            $type        = sanitize_string($_POST['discount_type'] ?? 'percent');
            $value       = (float)($_POST['discount_value'] ?? 0);
            $min_order   = (float)($_POST['min_order_value'] ?? 0);
            $max_discount= (float)($_POST['max_discount'] ?? 0);
            $max_uses    = (int)($_POST['max_uses'] ?? 100);
            $expiry      = sanitize_string($_POST['expiry_date'] ?? '');

            if (!$code || $value <= 0 || !$expiry) {
                $msg = 'Please fill all required fields.'; $msg_type = 'error';
            } else {
                $stmt = $conn->prepare(
                    "INSERT INTO coupons (code, description, discount_type, discount_value, min_order_value, max_discount, max_uses, expiry_date, status)
                     VALUES (?,?,?,?,?,?,?,?,'active')"
                );
                $stmt->bind_param('sssdddis', $code, $desc, $type, $value, $min_order, $max_discount, $max_uses, $expiry);
                if ($stmt->execute()) $msg = "Coupon '$code' created!";
                else $msg = 'Code already exists or DB error.'; $msg_type = 'error';
                $stmt->close();
            }
        } elseif ($action === 'delete') {
            $id = (int)($_POST['coupon_id'] ?? 0);
            $conn->query("DELETE FROM coupons WHERE id = $id");
            $msg = 'Coupon deleted.';
        } elseif ($action === 'toggle') {
            $id = (int)($_POST['coupon_id'] ?? 0);
            $conn->query("UPDATE coupons SET status = IF(status='active','inactive','active') WHERE id = $id");
            $msg = 'Coupon status toggled.';
        }
    }
}

$coupons = db_fetch_all($conn, "SELECT * FROM coupons ORDER BY id DESC");
$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupon Management — QuickBite Admin</title>
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

        /* ALERT */
        .alert{padding:0.9rem 1.2rem;border-radius:12px;margin-bottom:1.5rem;font-size:0.88rem;font-weight:600;}
        .alert-success{background:rgba(0,208,132,0.1);border:1px solid rgba(0,208,132,0.3);color:var(--green);}
        .alert-error{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#EF4444;}

        /* ADD COUPON FORM */
        .add-panel{background:var(--bg-card);border:1px solid var(--border-glass);border-radius:18px;padding:1.8rem;margin-bottom:2rem;}
        .add-panel-title{font-size:1rem;font-weight:700;margin-bottom:1.4rem;border-bottom:1px solid var(--border-glass);padding-bottom:0.7rem;}
        .form-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;}
        @media(max-width:900px){.form-grid{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:600px){.form-grid{grid-template-columns:1fr;}}
        .form-group{display:flex;flex-direction:column;gap:0.4rem;}
        .form-label{font-size:0.78rem;font-weight:600;color:var(--text-secondary);}
        .form-input{padding:0.65rem 0.9rem;background:rgba(255,255,255,0.05);border:1px solid var(--border-glass);border-radius:10px;color:var(--text-primary);font-size:0.88rem;font-family:'Inter',sans-serif;outline:none;transition:border-color 0.25s;}
        .form-input:focus{border-color:var(--neon-cyan);}
        .btn-add{padding:0.75rem 1.5rem;background:linear-gradient(135deg,var(--neon-cyan),#00b8c8);border:none;border-radius:10px;color:#0F172A;font-weight:700;font-size:0.9rem;cursor:pointer;font-family:'Inter',sans-serif;transition:all 0.3s;margin-top:1rem;}
        .btn-add:hover{opacity:0.88;}

        /* COUPONS TABLE */
        .table-wrap{background:var(--bg-card);border:1px solid var(--border-glass);border-radius:18px;overflow:hidden;}
        .coupons-table{width:100%;border-collapse:collapse;}
        .coupons-table thead tr{background:rgba(255,71,71,0.05);}
        .coupons-table th{text-align:left;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);padding:1rem 1.2rem;font-weight:600;}
        .coupons-table td{padding:0.85rem 1.2rem;border-top:1px solid var(--border-glass);font-size:0.88rem;vertical-align:middle;}
        .coupons-table tbody tr:hover{background:rgba(255,71,71,0.02);}

        .code-pill{display:inline-block;padding:0.25rem 0.8rem;border-radius:8px;font-family:monospace;font-weight:700;font-size:0.9rem;background:rgba(255,71,71,0.1);color:var(--neon-cyan);border:1px solid rgba(255,71,71,0.25);letter-spacing:0.08em;}
        .badge-active{display:inline-block;padding:0.25rem 0.7rem;border-radius:20px;font-size:0.72rem;font-weight:700;background:rgba(0,208,132,0.15);color:var(--green);border:1px solid rgba(0,208,132,0.3);}
        .badge-inactive{display:inline-block;padding:0.25rem 0.7rem;border-radius:20px;font-size:0.72rem;font-weight:700;background:rgba(148,163,184,0.1);color:var(--text-secondary);border:1px solid var(--border-glass);}
        .badge-expired{display:inline-block;padding:0.25rem 0.7rem;border-radius:20px;font-size:0.72rem;font-weight:700;background:rgba(239,68,68,0.12);color:#EF4444;border:1px solid rgba(239,68,68,0.3);}

        .btn-sm{padding:0.35rem 0.75rem;border-radius:7px;font-size:0.75rem;font-weight:700;cursor:pointer;border:1px solid;font-family:'Inter',sans-serif;transition:all 0.2s;}
        .btn-toggle-coupon{background:rgba(255,71,71,0.08);border-color:rgba(255,71,71,0.25);color:var(--neon-cyan);}
        .btn-toggle-coupon:hover{background:rgba(255,71,71,0.15);}
        .btn-del{background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.25);color:#EF4444;}
        .btn-del:hover{background:rgba(239,68,68,0.18);}

        .progress-bar-bg{width:100%;height:6px;background:rgba(255,255,255,0.08);border-radius:3px;overflow:hidden;margin-top:0.3rem;}
        .progress-bar-fill{height:100%;background:var(--neon-cyan);border-radius:3px;}
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-logo">Quick<span>Bite</span></div>
    <nav class="sidebar-nav">
        <a href="dashboard.php"   class="nav-item"><span class="nav-icon">📊</span> Dashboard</a>
        <a href="orders.php"      class="nav-item"><span class="nav-icon">📦</span> Orders</a>
        <a href="foods.php"       class="nav-item"><span class="nav-icon">🍔</span> Foods</a>
        <a href="restaurants.php" class="nav-item"><span class="nav-icon">🏪</span> Restaurants</a>
        <a href="users.php"       class="nav-item"><span class="nav-icon">👥</span> Users</a>
        <a href="coupons.php"     class="nav-item active"><span class="nav-icon">🎟️</span> Coupons</a>
    </nav>
    <div class="sidebar-footer"><a href="../auth/logout.php" style="color:var(--red);font-size:0.85rem;font-weight:600;text-decoration:none;">🚪 Logout</a></div>
</aside>

<main class="main-content">
    <div class="page-title">🎟️ Coupon Management</div>
    <div class="page-sub">Create, activate and monitor discount coupons</div>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msg_type === 'error' ? 'error' : 'success' ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- ADD COUPON FORM -->
    <div class="add-panel">
        <div class="add-panel-title">➕ Create New Coupon</div>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="action" value="add">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Code *</label>
                    <input type="text" class="form-input" name="code" placeholder="SAVE20" required style="text-transform:uppercase;">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" class="form-input" name="description" placeholder="20% off on first order">
                </div>
                <div class="form-group">
                    <label class="form-label">Discount Type *</label>
                    <select class="form-input" name="discount_type">
                        <option value="percent">Percentage (%)</option>
                        <option value="flat">Flat Amount (₹)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Discount Value *</label>
                    <input type="number" class="form-input" name="discount_value" placeholder="20" min="1" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Min Order (₹)</label>
                    <input type="number" class="form-input" name="min_order_value" placeholder="0" min="0" step="0.01" value="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Max Discount Cap (₹, 0=no cap)</label>
                    <input type="number" class="form-input" name="max_discount" placeholder="0" min="0" step="0.01" value="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Max Uses</label>
                    <input type="number" class="form-input" name="max_uses" placeholder="100" min="1" value="100">
                </div>
                <div class="form-group">
                    <label class="form-label">Expiry Date *</label>
                    <input type="date" class="form-input" name="expiry_date" required min="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <button type="submit" class="btn-add">➕ Create Coupon</button>
        </form>
    </div>

    <!-- COUPONS TABLE -->
    <div class="table-wrap">
        <table class="coupons-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Min Order</th>
                    <th>Usage</th>
                    <th>Expiry</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($coupons)): ?>
            <tr><td colspan="7" style="text-align:center;color:var(--text-secondary);padding:3rem;">No coupons yet. Create one above!</td></tr>
            <?php else: ?>
            <?php foreach ($coupons as $c):
                $is_expired = strtotime($c['expiry_date']) < strtotime('today');
                $usage_pct  = $c['max_uses'] > 0 ? min(100, round(($c['used_count'] / $c['max_uses']) * 100)) : 0;
                if ($is_expired) $badge = 'badge-expired';
                elseif ($c['status'] === 'active') $badge = 'badge-active';
                else $badge = 'badge-inactive';
            ?>
            <tr>
                <td><span class="code-pill"><?= htmlspecialchars($c['code']) ?></span>
                    <?php if ($c['description']): ?>
                    <div style="font-size:0.75rem;color:var(--text-secondary);margin-top:0.2rem;"><?= htmlspecialchars($c['description']) ?></div>
                    <?php endif; ?>
                </td>
                <td style="font-weight:700;">
                    <?= $c['discount_type'] === 'percent'
                        ? htmlspecialchars($c['discount_value']) . '%'
                        : '₹' . number_format($c['discount_value'], 0) ?>
                    <?php if ($c['max_discount'] > 0): ?>
                    <div style="font-size:0.72rem;color:var(--text-secondary);">max ₹<?= number_format($c['max_discount'],0) ?></div>
                    <?php endif; ?>
                </td>
                <td>₹<?= number_format($c['min_order_value'], 0) ?></td>
                <td>
                    <span style="font-weight:600;"><?= $c['used_count'] ?></span><span style="color:var(--text-secondary);"> / <?= $c['max_uses'] ?></span>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:<?= $usage_pct ?>%;"></div>
                    </div>
                </td>
                <td style="font-size:0.82rem;<?= $is_expired ? 'color:#EF4444;' : 'color:var(--text-secondary);' ?>">
                    <?= date('d M Y', strtotime($c['expiry_date'])) ?>
                    <?= $is_expired ? ' (Expired)' : '' ?>
                </td>
                <td><span class="<?= $badge ?>"><?= $is_expired ? '⌛ Expired' : ($c['status']==='active' ? '✅ Active' : '⏸ Inactive') ?></span></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="coupon_id" value="<?= $c['id'] ?>">
                        <button type="submit" class="btn-sm btn-toggle-coupon"><?= $c['status']==='active'?'Deactivate':'Activate' ?></button>
                    </form>
                    <form method="POST" style="display:inline;margin-left:0.4rem;" onsubmit="return confirm('Delete coupon <?= htmlspecialchars($c['code']) ?>?');">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="coupon_id" value="<?= $c['id'] ?>">
                        <button type="submit" class="btn-sm btn-del">🗑 Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
