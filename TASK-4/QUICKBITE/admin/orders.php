<?php
require_once 'admin_session.php';
require_once '../config/db.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

// Fetch orders with user info — support both old (food_id) and new (order_items) schemas
$search = sanitize_string($_GET['search'] ?? '');
$filter = sanitize_string($_GET['filter'] ?? 'all');
$page   = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset   = ($page - 1) * $per_page;

$where  = '1=1';
$params = [];
$types  = '';

if ($filter !== 'all') {
    $where   .= " AND o.order_status = ?";
    $params[] = $filter;
    $types   .= 's';
}
if ($search) {
    $where   .= " AND (u.name LIKE ? OR o.order_number LIKE ?)";
    $s        = "%$search%";
    $params[] = $s;
    $params[] = $s;
    $types   .= 'ss';
}

$sql = "SELECT o.id, o.order_number, o.total_price, o.order_status, o.payment_method,
               o.created_at, o.delivery_address,
               u.name AS customer_name, u.email AS customer_email,
               COUNT(DISTINCT oi.id) AS item_count
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON oi.order_id = o.id
        WHERE $where
        GROUP BY o.id
        ORDER BY o.id DESC
        LIMIT ? OFFSET ?";

$params[] = $per_page;
$params[] = $offset;
$types   .= 'ii';

$orders = db_fetch_all($conn, $sql, $types ?: null, $params ?: []);

// Count for pagination
$count_sql = "SELECT COUNT(DISTINCT o.id) as cnt FROM orders o JOIN users u ON o.user_id=u.id WHERE $where";
$count_params = array_slice($params, 0, -2);
$count_types  = $types ? rtrim($types, 'i') : '';
$count_types  = rtrim($count_types, 'i');
$total_orders = (int)(db_fetch($conn, $count_sql, $count_types ?: null, $count_params ?: [])['cnt'] ?? 0);
$total_pages  = ceil($total_orders / $per_page);

// Status breakdown
$statuses = ['Pending','Accepted','Preparing','Ready','Out For Delivery','Delivered','Cancelled'];
$status_counts = [];
foreach ($statuses as $s) {
    $status_counts[$s] = db_count($conn, 'orders', "order_status = ?", 's', [$s]);
}

$csrf_token = generate_csrf_token();

$status_colors = [
    'Pending'          => 'orange',
    'Accepted'         => 'blue',
    'Preparing'        => 'purple',
    'Ready'            => 'cyan',
    'Out For Delivery' => 'cyan',
    'Delivered'        => 'green',
    'Cancelled'        => 'red',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management — QuickBite Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        :root{--neon-cyan:#FF5A00;--bg-dark:#F8FAFC;--bg-secondary:#FFFFFF;--bg-card:#FFFFFF;--text-primary:#0F172A;--text-secondary:#475569;--border-glass:#E2E8F0;--green:#00D084;--orange:#FF8C42;--red:#FF4545;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:var(--bg-dark);color:var(--text-primary);display:flex;min-height:100vh;}

        /* SIDEBAR */
        .sidebar{width:240px;background:var(--bg-secondary);border-right:1px solid var(--border-glass);display:flex;flex-direction:column;position:fixed;height:100vh;z-index:100;}
        .sidebar-logo{padding:1.5rem;font-size:1.3rem;font-weight:800;border-bottom:1px solid var(--border-glass);}
        .sidebar-logo span{color:var(--neon-cyan);}
        .sidebar-nav{flex:1;padding:1rem 0;overflow-y:auto;}
        .nav-item{display:flex;align-items:center;gap:0.8rem;padding:0.75rem 1.5rem;color:var(--text-secondary);text-decoration:none;font-size:0.88rem;font-weight:600;transition:all 0.25s;border-left:3px solid transparent;}
        .nav-item:hover,.nav-item.active{background:rgba(255,71,71,0.06);color:var(--neon-cyan);border-left-color:var(--neon-cyan);}
        .nav-item .nav-icon{font-size:1.1rem;width:20px;text-align:center;}
        .sidebar-footer{padding:1rem 1.5rem;border-top:1px solid var(--border-glass);}

        /* MAIN */
        .main-content{margin-left:240px;flex:1;padding:2rem;}
        .page-title{font-size:1.6rem;font-weight:800;margin-bottom:0.3rem;}
        .page-sub{font-size:0.88rem;color:var(--text-secondary);margin-bottom:2rem;}

        /* STAT PILLS */
        .stat-pills{display:flex;gap:0.6rem;flex-wrap:wrap;margin-bottom:1.5rem;}
        .stat-pill{padding:0.45rem 1rem;border-radius:20px;border:1px solid var(--border-glass);background:rgba(255,255,255,0.03);font-size:0.82rem;font-weight:600;cursor:pointer;transition:all 0.25s;text-decoration:none;color:var(--text-secondary);}
        .stat-pill:hover,.stat-pill.active{background:rgba(255,71,71,0.1);border-color:var(--neon-cyan);color:var(--neon-cyan);}
        .stat-pill .cnt{margin-left:0.3rem;font-size:0.75rem;opacity:0.8;}

        /* TOOLBAR */
        .toolbar{display:flex;gap:0.8rem;margin-bottom:1.5rem;flex-wrap:wrap;}
        .search-box{flex:1;min-width:200px;padding:0.65rem 1rem;background:rgba(255,255,255,0.05);border:1px solid var(--border-glass);border-radius:10px;color:var(--text-primary);font-size:0.88rem;outline:none;transition:border-color 0.25s;font-family:'Inter',sans-serif;}
        .search-box:focus{border-color:var(--neon-cyan);}
        .search-box::placeholder{color:rgba(148,163,184,0.5);}

        /* TABLE */
        .table-wrap{background:var(--bg-card);border:1px solid var(--border-glass);border-radius:18px;overflow:hidden;}
        .orders-table{width:100%;border-collapse:collapse;}
        .orders-table thead tr{background:rgba(255,71,71,0.05);}
        .orders-table th{text-align:left;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);padding:1rem 1.2rem;font-weight:600;}
        .orders-table td{padding:0.9rem 1.2rem;border-top:1px solid var(--border-glass);font-size:0.88rem;vertical-align:middle;}
        .orders-table tbody tr:hover{background:rgba(255,71,71,0.02);}

        .order-num-cell{font-weight:700;color:var(--neon-cyan);}
        .customer-cell .c-name{font-weight:600;font-size:0.9rem;}
        .customer-cell .c-email{font-size:0.75rem;color:var(--text-secondary);}
        .amount-cell{font-weight:800;}

        /* STATUS BADGES */
        .sbadge{display:inline-flex;align-items:center;gap:0.3rem;padding:0.3rem 0.75rem;border-radius:20px;font-size:0.74rem;font-weight:700;}
        .sbadge-orange{background:rgba(245,158,11,0.15);color:#F59E0B;border:1px solid rgba(245,158,11,0.3);}
        .sbadge-blue{background:rgba(59,130,246,0.15);color:#3B82F6;border:1px solid rgba(59,130,246,0.3);}
        .sbadge-purple{background:rgba(139,92,246,0.15);color:#8B5CF6;border:1px solid rgba(139,92,246,0.3);}
        .sbadge-cyan{background:rgba(255,71,71,0.12);color:var(--neon-cyan);border:1px solid rgba(255,71,71,0.3);}
        .sbadge-green{background:rgba(0,208,132,0.15);color:var(--green);border:1px solid rgba(0,208,132,0.3);}
        .sbadge-red{background:rgba(239,68,68,0.15);color:#EF4444;border:1px solid rgba(239,68,68,0.3);}

        /* STATUS DROPDOWN */
        .status-select{
            padding:0.4rem 0.7rem;background:rgba(255,255,255,0.05);
            border:1px solid var(--border-glass);border-radius:8px;
            color:var(--text-primary);font-size:0.8rem;font-family:'Inter',sans-serif;
            cursor:pointer;outline:none;transition:all 0.25s;
        }
        .status-select:focus{border-color:var(--neon-cyan);}

        /* PAGINATION */
        .pagination{display:flex;gap:0.5rem;justify-content:center;margin-top:1.5rem;flex-wrap:wrap;}
        .page-btn{padding:0.45rem 0.9rem;border-radius:8px;border:1px solid var(--border-glass);background:rgba(255,255,255,0.03);color:var(--text-secondary);font-size:0.82rem;font-weight:600;cursor:pointer;text-decoration:none;transition:all 0.2s;}
        .page-btn:hover,.page-btn.active{background:rgba(255,71,71,0.12);border-color:var(--neon-cyan);color:var(--neon-cyan);}

        /* TOAST */
        #toast{position:fixed;bottom:2rem;right:2rem;padding:0.8rem 1.4rem;border-radius:12px;font-size:0.88rem;font-weight:600;z-index:9999;display:none;animation:slideUp 0.35s ease;}
        #toast.success{background:rgba(0,208,132,0.2);border:1px solid rgba(0,208,132,0.4);color:var(--green);}
        #toast.error{background:rgba(239,68,68,0.2);border:1px solid rgba(239,68,68,0.4);color:#EF4444;}
        @keyframes slideUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">Quick<span>Bite</span> <span style="font-size:0.65rem;color:var(--text-secondary);font-weight:400;">Admin</span></div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item"><span class="nav-icon">📊</span> Dashboard</a>
        <a href="orders.php"    class="nav-item active"><span class="nav-icon">📦</span> Orders</a>
        <a href="foods.php"     class="nav-item"><span class="nav-icon">🍔</span> Foods</a>
        <a href="restaurants.php" class="nav-item"><span class="nav-icon">🏪</span> Restaurants</a>
        <a href="users.php"     class="nav-item"><span class="nav-icon">👥</span> Users</a>
        <a href="coupons.php"   class="nav-item"><span class="nav-icon">🎟️</span> Coupons</a>
    </nav>
    <div class="sidebar-footer">
        <a href="../auth/logout.php" style="color:var(--red);font-size:0.85rem;font-weight:600;text-decoration:none;">🚪 Logout</a>
    </div>
</aside>

<main class="main-content">
    <div class="page-title">📦 Order Management</div>
    <div class="page-sub"><?= number_format($total_orders) ?> total orders · Page <?= $page ?> of <?= max(1,$total_pages) ?></div>

    <!-- STATUS PILLS -->
    <div class="stat-pills">
        <a href="orders.php" class="stat-pill <?= $filter==='all'?'active':'' ?>">All <span class="cnt"><?= array_sum($status_counts) ?></span></a>
        <?php foreach ($statuses as $s): ?>
        <a href="orders.php?filter=<?= urlencode($s) ?>&search=<?= urlencode($search) ?>"
           class="stat-pill <?= $filter===$s?'active':'' ?>">
            <?= get_order_status_icon($s) ?> <?= $s ?> <span class="cnt"><?= $status_counts[$s] ?></span>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- TOOLBAR -->
    <form method="GET" class="toolbar" style="margin-bottom:1.5rem;">
        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
        <input type="text" class="search-box" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="🔍 Search customer, order number…">
        <button type="submit" style="padding:0.65rem 1.2rem;background:rgba(255,71,71,0.12);border:1px solid rgba(255,71,71,0.3);border-radius:10px;color:var(--neon-cyan);font-weight:700;font-size:0.88rem;cursor:pointer;font-family:'Inter',sans-serif;">Search</button>
    </form>

    <!-- ORDERS TABLE -->
    <input type="hidden" id="csrf-token" value="<?= $csrf_token ?>">
    <div class="table-wrap">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($orders)): ?>
            <tr><td colspan="8" style="text-align:center;color:var(--text-secondary);padding:3rem;">No orders found.</td></tr>
            <?php else: ?>
            <?php foreach ($orders as $order): ?>
            <?php
                $slug = $status_colors[$order['order_status']] ?? 'orange';
                $icon = get_order_status_icon($order['order_status']);
            ?>
            <tr id="row-<?= $order['id'] ?>">
                <td class="order-num-cell"><?= htmlspecialchars($order['order_number'] ?? '#'.$order['id']) ?></td>
                <td class="customer-cell">
                    <div class="c-name"><?= htmlspecialchars($order['customer_name']) ?></div>
                    <div class="c-email"><?= htmlspecialchars($order['customer_email']) ?></div>
                </td>
                <td><?= (int)($order['item_count'] ?: 1) ?> item<?= $order['item_count'] != 1 ? 's' : '' ?></td>
                <td class="amount-cell">₹<?= number_format($order['total_price'], 2) ?></td>
                <td><?= htmlspecialchars($order['payment_method'] ?? 'COD') ?></td>
                <td style="color:var(--text-secondary);font-size:0.8rem;"><?= date('d M Y, h:i A', strtotime($order['created_at'] ?? 'now')) ?></td>
                <td>
                    <span class="sbadge sbadge-<?= $slug ?>" id="badge-<?= $order['id'] ?>">
                        <?= $icon ?> <?= htmlspecialchars($order['order_status']) ?>
                    </span>
                </td>
                <td>
                    <select class="status-select" id="sel-<?= $order['id'] ?>"
                            onchange="updateStatus(<?= $order['id'] ?>, this.value)">
                        <?php foreach (['Pending','Accepted','Preparing','Ready','Out For Delivery','Delivered','Cancelled'] as $st): ?>
                        <option value="<?= $st ?>" <?= $order['order_status']===$st?'selected':'' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
        <a href="?page=<?= $p ?>&filter=<?= urlencode($filter) ?>&search=<?= urlencode($search) ?>"
           class="page-btn <?= $p===$page?'active':'' ?>"><?= $p ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</main>

<div id="toast"></div>

<script>
const CSRF = document.getElementById('csrf-token').value;

const badgeColors = {
    'Pending':'sbadge-orange','Accepted':'sbadge-blue','Preparing':'sbadge-purple',
    'Ready':'sbadge-cyan','Out For Delivery':'sbadge-cyan','Delivered':'sbadge-green','Cancelled':'sbadge-red'
};
const icons = {
    'Pending':'⏳','Accepted':'✅','Preparing':'👨‍🍳','Ready':'📦',
    'Out For Delivery':'🛵','Delivered':'🎉','Cancelled':'❌'
};

function updateStatus(orderId, newStatus) {
    fetch('ajax/update-order-status.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'order_id=' + orderId + '&status=' + encodeURIComponent(newStatus) + '&csrf_token=' + CSRF
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById('badge-' + orderId);
            badge.className = 'sbadge ' + (badgeColors[newStatus] || 'sbadge-orange');
            badge.innerHTML = (icons[newStatus] || '📋') + ' ' + newStatus;
            showToast('✅ Order #' + orderId + ' updated to ' + newStatus, 'success');
        } else {
            showToast('❌ ' + (data.error || 'Update failed'), 'error');
        }
    })
    .catch(() => showToast('❌ Network error', 'error'));
}

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = type;
    t.style.display = 'block';
    setTimeout(() => { t.style.display = 'none'; }, 3000);
}
</script>
</body>
</html>