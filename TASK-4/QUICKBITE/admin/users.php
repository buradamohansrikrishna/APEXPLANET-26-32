<?php
require_once 'admin_session.php';
require_once '../config/db.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

$search = sanitize_string($_GET['search'] ?? '');
$filter = sanitize_string($_GET['filter'] ?? 'all'); // all, active, banned
$page   = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset   = ($page - 1) * $per_page;

$where  = '1=1';
$params = [];
$types  = '';

if ($filter === 'active') { $where .= " AND u.status = 'active'"; }
if ($filter === 'banned')  { $where .= " AND u.status = 'banned'"; }
if ($search) {
    $where   .= " AND (u.name LIKE ? OR u.email LIKE ?)";
    $s        = "%$search%";
    $params[] = $s; $params[] = $s;
    $types   .= 'ss';
}

$sql = "SELECT u.id, u.name, u.email, u.phone, u.reward_points, u.status, u.created_at,
               COUNT(DISTINCT o.id) AS order_count,
               COALESCE(SUM(o.total_price), 0) AS total_spent
        FROM users u
        LEFT JOIN orders o ON o.user_id = u.id
        WHERE $where
        GROUP BY u.id
        ORDER BY u.id DESC
        LIMIT ? OFFSET ?";
$params[] = $per_page; $params[] = $offset;
$types   .= 'ii';

$users = db_fetch_all($conn, $sql, $types ?: null, $params ?: []);

$count_sql = "SELECT COUNT(*) AS cnt FROM users u WHERE $where";
$cp = array_slice($params, 0, -2);
$ct = rtrim(rtrim($types, 'i'), 'i');
$total_users = (int)(db_fetch($conn, $count_sql, $ct ?: null, $cp ?: [])['cnt'] ?? 0);
$total_pages = ceil($total_users / $per_page);

$total_all    = db_count($conn, 'users');
$total_active = db_count($conn, 'users', "status = 'active'");
$total_banned = db_count($conn, 'users', "status = 'banned'");

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management — QuickBite Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        :root{--neon-cyan:#FF5A00;--bg-dark:#F8FAFC;--bg-secondary:#FFFFFF;--bg-card:#FFFFFF;--text-primary:#0F172A;--text-secondary:#475569;--border-glass:#E2E8F0;--green:#00D084;--red:#FF4545;}
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

        /* STAT CARDS */
        .stat-row{display:flex;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;}
        .stat-mini{background:var(--bg-card);border:1px solid var(--border-glass);border-radius:14px;padding:1.2rem 1.5rem;flex:1;min-width:150px;}
        .stat-mini-label{font-size:0.75rem;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.4rem;}
        .stat-mini-val{font-size:1.8rem;font-weight:800;}

        .stat-pills{display:flex;gap:0.6rem;flex-wrap:wrap;margin-bottom:1.5rem;}
        .stat-pill{padding:0.45rem 1rem;border-radius:20px;border:1px solid var(--border-glass);background:rgba(255,255,255,0.03);font-size:0.82rem;font-weight:600;cursor:pointer;transition:all 0.25s;text-decoration:none;color:var(--text-secondary);}
        .stat-pill:hover,.stat-pill.active{background:rgba(255,71,71,0.1);border-color:var(--neon-cyan);color:var(--neon-cyan);}

        .toolbar{display:flex;gap:0.8rem;margin-bottom:1.5rem;flex-wrap:wrap;}
        .search-box{flex:1;min-width:200px;padding:0.65rem 1rem;background:rgba(255,255,255,0.05);border:1px solid var(--border-glass);border-radius:10px;color:var(--text-primary);font-size:0.88rem;outline:none;transition:border-color 0.25s;font-family:'Inter',sans-serif;}
        .search-box:focus{border-color:var(--neon-cyan);}
        .search-box::placeholder{color:rgba(148,163,184,0.5);}

        .table-wrap{background:var(--bg-card);border:1px solid var(--border-glass);border-radius:18px;overflow:hidden;}
        .users-table{width:100%;border-collapse:collapse;}
        .users-table thead tr{background:rgba(255,71,71,0.05);}
        .users-table th{text-align:left;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);padding:1rem 1.2rem;font-weight:600;}
        .users-table td{padding:0.85rem 1.2rem;border-top:1px solid var(--border-glass);font-size:0.88rem;vertical-align:middle;}
        .users-table tbody tr:hover{background:rgba(255,71,71,0.02);}

        .user-avatar{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#FF4747,#3A86FF);display:flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:700;color:#0F172A;flex-shrink:0;}
        .user-cell{display:flex;align-items:center;gap:0.7rem;}
        .u-name{font-weight:600;font-size:0.9rem;}
        .u-email{font-size:0.75rem;color:var(--text-secondary);}

        .badge-active{display:inline-block;padding:0.25rem 0.7rem;border-radius:20px;font-size:0.72rem;font-weight:700;background:rgba(0,208,132,0.15);color:var(--green);border:1px solid rgba(0,208,132,0.3);}
        .badge-banned{display:inline-block;padding:0.25rem 0.7rem;border-radius:20px;font-size:0.72rem;font-weight:700;background:rgba(239,68,68,0.15);color:#EF4444;border:1px solid rgba(239,68,68,0.3);}

        .btn-toggle{padding:0.4rem 0.8rem;border-radius:8px;font-size:0.78rem;font-weight:700;cursor:pointer;border:1px solid;transition:all 0.25s;font-family:'Inter',sans-serif;}
        .btn-ban{background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.3);color:#EF4444;}
        .btn-ban:hover{background:rgba(239,68,68,0.2);}
        .btn-unban{background:rgba(0,208,132,0.1);border-color:rgba(0,208,132,0.3);color:var(--green);}
        .btn-unban:hover{background:rgba(0,208,132,0.2);}

        .pagination{display:flex;gap:0.5rem;justify-content:center;margin-top:1.5rem;flex-wrap:wrap;}
        .page-btn{padding:0.45rem 0.9rem;border-radius:8px;border:1px solid var(--border-glass);background:rgba(255,255,255,0.03);color:var(--text-secondary);font-size:0.82rem;font-weight:600;cursor:pointer;text-decoration:none;transition:all 0.2s;}
        .page-btn:hover,.page-btn.active{background:rgba(255,71,71,0.12);border-color:var(--neon-cyan);color:var(--neon-cyan);}

        #toast{position:fixed;bottom:2rem;right:2rem;padding:0.8rem 1.4rem;border-radius:12px;font-size:0.88rem;font-weight:600;z-index:9999;display:none;animation:slideUp 0.35s ease;}
        #toast.success{background:rgba(0,208,132,0.2);border:1px solid rgba(0,208,132,0.4);color:var(--green);}
        #toast.error{background:rgba(239,68,68,0.2);border:1px solid rgba(239,68,68,0.4);color:#EF4444;}
        @keyframes slideUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
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
        <a href="users.php"       class="nav-item active"><span class="nav-icon">👥</span> Users</a>
        <a href="coupons.php"     class="nav-item"><span class="nav-icon">🎟️</span> Coupons</a>
    </nav>
    <div class="sidebar-footer"><a href="../auth/logout.php" style="color:var(--red);font-size:0.85rem;font-weight:600;text-decoration:none;">🚪 Logout</a></div>
</aside>

<main class="main-content">
    <div class="page-title">👥 User Management</div>
    <div class="page-sub"><?= number_format($total_users) ?> users found</div>

    <div class="stat-row">
        <div class="stat-mini"><div class="stat-mini-label">Total Users</div><div class="stat-mini-val" style="color:var(--neon-cyan);"><?= number_format($total_all) ?></div></div>
        <div class="stat-mini"><div class="stat-mini-label">Active</div><div class="stat-mini-val" style="color:var(--green);"><?= number_format($total_active) ?></div></div>
        <div class="stat-mini"><div class="stat-mini-label">Banned</div><div class="stat-mini-val" style="color:var(--red);"><?= number_format($total_banned) ?></div></div>
    </div>

    <div class="stat-pills">
        <a href="users.php" class="stat-pill <?= $filter==='all'?'active':'' ?>">All <?= $total_all ?></a>
        <a href="users.php?filter=active" class="stat-pill <?= $filter==='active'?'active':'' ?>">✅ Active <?= $total_active ?></a>
        <a href="users.php?filter=banned" class="stat-pill <?= $filter==='banned'?'active':'' ?>">🚫 Banned <?= $total_banned ?></a>
    </div>

    <form method="GET" class="toolbar">
        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
        <input type="text" class="search-box" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="🔍 Search name or email…">
        <button type="submit" style="padding:0.65rem 1.2rem;background:rgba(255,71,71,0.12);border:1px solid rgba(255,71,71,0.3);border-radius:10px;color:var(--neon-cyan);font-weight:700;font-size:0.88rem;cursor:pointer;font-family:'Inter',sans-serif;">Search</button>
    </form>

    <input type="hidden" id="csrf-token" value="<?= $csrf_token ?>">
    <div class="table-wrap">
        <table class="users-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Phone</th>
                    <th>Orders</th>
                    <th>Spent</th>
                    <th>Points</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($users)): ?>
            <tr><td colspan="8" style="text-align:center;color:var(--text-secondary);padding:3rem;">No users found.</td></tr>
            <?php else: ?>
            <?php foreach ($users as $u): ?>
            <?php $initials = get_initials($u['name']); $status = $u['status'] ?? 'active'; ?>
            <tr id="urow-<?= $u['id'] ?>">
                <td>
                    <div class="user-cell">
                        <div class="user-avatar"><?= htmlspecialchars($initials) ?></div>
                        <div>
                            <div class="u-name"><?= htmlspecialchars($u['name']) ?></div>
                            <div class="u-email"><?= htmlspecialchars($u['email']) ?></div>
                        </div>
                    </div>
                </td>
                <td style="color:var(--text-secondary);"><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
                <td style="font-weight:600;"><?= (int)$u['order_count'] ?></td>
                <td style="font-weight:700;color:var(--neon-cyan);">₹<?= number_format($u['total_spent'], 0) ?></td>
                <td><?= (int)$u['reward_points'] ?> pts</td>
                <td style="font-size:0.8rem;color:var(--text-secondary);"><?= date('d M Y', strtotime($u['created_at'] ?? 'now')) ?></td>
                <td>
                    <span id="ubadge-<?= $u['id'] ?>" class="<?= $status==='banned'?'badge-banned':'badge-active' ?>">
                        <?= $status === 'banned' ? '🚫 Banned' : '✅ Active' ?>
                    </span>
                </td>
                <td>
                    <button id="ubtn-<?= $u['id'] ?>"
                            class="btn-toggle <?= $status==='banned'?'btn-unban':'btn-ban' ?>"
                            onclick="toggleUser(<?= $u['id'] ?>, '<?= $status ?>')">
                        <?= $status === 'banned' ? 'Unban' : 'Ban' ?>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

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

function toggleUser(userId, currentStatus) {
    const newStatus = currentStatus === 'banned' ? 'active' : 'banned';
    fetch('ajax/toggle-user-status.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'user_id='+userId+'&status='+encodeURIComponent(newStatus)+'&csrf_token='+CSRF
    })
    .then(r=>r.json())
    .then(data=>{
        if(data.success){
            const badge = document.getElementById('ubadge-'+userId);
            const btn   = document.getElementById('ubtn-'+userId);
            if(newStatus==='banned'){
                badge.className='badge-banned'; badge.textContent='🚫 Banned';
                btn.className='btn-toggle btn-unban'; btn.textContent='Unban';
            } else {
                badge.className='badge-active'; badge.textContent='✅ Active';
                btn.className='btn-toggle btn-ban'; btn.textContent='Ban';
            }
            btn.setAttribute('onclick','toggleUser('+userId+',\''+newStatus+'\')');
            showToast('✅ User '+newStatus,'success');
        } else { showToast('❌ '+(data.error||'Failed'),'error'); }
    }).catch(()=>showToast('❌ Network error','error'));
}

function showToast(msg,type){
    const t=document.getElementById('toast');
    t.textContent=msg; t.className=type; t.style.display='block';
    setTimeout(()=>{t.style.display='none';},3000);
}
</script>
</body>
</html>
