<?php
require_once 'admin_session.php';            // Checks $_SESSION['admin_id'], redirects if not set
require_once '../config/db.php';
require_once '../includes/functions.php';

$admin_name = $_SESSION['admin_name'] ?? 'Administrator';

/* ──────────────────────────────────────────
   STATS QUERIES
─────────────────────────────────────────── */
function fetchSingleValue(mysqli $conn, string $sql, array $params = []): mixed {
    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
    } else {
        $res = $conn->query($sql);
    }
    $row = $res->fetch_row();
    return $row[0] ?? 0;
}

$stat_users        = (int) fetchSingleValue($conn, "SELECT COUNT(*) FROM users");
$stat_orders       = (int) fetchSingleValue($conn, "SELECT COUNT(*) FROM orders");
$stat_foods        = (int) fetchSingleValue($conn, "SELECT COUNT(*) FROM foods");
$stat_restaurants  = (int) fetchSingleValue($conn, "SELECT COUNT(*) FROM restaurants");
$stat_revenue      = (float) fetchSingleValue($conn, "SELECT COALESCE(SUM(total_price),0) FROM orders WHERE order_status='Delivered'");
$stat_today_orders = (int) fetchSingleValue($conn, "SELECT COUNT(*) FROM orders WHERE DATE(created_at)=CURDATE()");
$stat_pending      = (int) fetchSingleValue($conn, "SELECT COUNT(*) FROM orders WHERE order_status='Pending'");
$stat_delivered    = (int) fetchSingleValue($conn, "SELECT COUNT(*) FROM orders WHERE order_status='Delivered'");
$stat_app_rating   = (float) fetchSingleValue($conn, "SELECT COALESCE(AVG(rating),0) FROM app_reviews");

/* ──────────────────────────────────────────
   LAST 7 DAYS — REVENUE & ORDERS
─────────────────────────────────────────── */
$revenue_labels = [];
$revenue_data   = [];
$orders_data    = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('M j', strtotime("-$i days"));

    $rev_q = $conn->prepare("SELECT COALESCE(SUM(total_price),0) FROM orders WHERE order_status='Delivered' AND DATE(created_at)=?");
    $rev_q->bind_param('s', $date);
    $rev_q->execute();
    $rev_val = (float) $rev_q->get_result()->fetch_row()[0];
    $rev_q->close();

    $ord_q = $conn->prepare("SELECT COUNT(*) FROM orders WHERE DATE(created_at)=?");
    $ord_q->bind_param('s', $date);
    $ord_q->execute();
    $ord_val = (int) $ord_q->get_result()->fetch_row()[0];
    $ord_q->close();

    $revenue_labels[] = $label;
    $revenue_data[]   = round($rev_val, 2);
    $orders_data[]    = $ord_val;
}

/* ──────────────────────────────────────────
   ORDER STATUS BREAKDOWN
─────────────────────────────────────────── */
$status_result = $conn->query("SELECT order_status, COUNT(*) as cnt FROM orders GROUP BY order_status");
$status_labels = [];
$status_data   = [];
while ($row = $status_result->fetch_assoc()) {
    $status_labels[] = $row['order_status'];
    $status_data[]   = (int) $row['cnt'];
}

/* ──────────────────────────────────────────
   USER GROWTH — LAST 6 MONTHS
─────────────────────────────────────────── */
$user_growth_labels = [];
$user_growth_data   = [];

for ($i = 5; $i >= 0; $i--) {
    $year  = date('Y', strtotime("-$i months"));
    $month = date('m', strtotime("-$i months"));
    $label = date('M Y', strtotime("-$i months"));

    $ug_q = $conn->prepare("SELECT COUNT(*) FROM users WHERE YEAR(created_at)=? AND MONTH(created_at)=?");
    $ug_q->bind_param('ss', $year, $month);
    $ug_q->execute();
    $ug_val = (int) $ug_q->get_result()->fetch_row()[0];
    $ug_q->close();

    $user_growth_labels[] = $label;
    $user_growth_data[]   = $ug_val;
}

/* ──────────────────────────────────────────
   TOP 5 FOODS BY ORDER COUNT
─────────────────────────────────────────── */
$food_res = $conn->query(
    "SELECT f.food_name AS name, COUNT(oi.id) as total_orders
     FROM order_items oi
     JOIN foods f ON oi.food_id = f.id
     GROUP BY oi.food_id
     ORDER BY total_orders DESC
     LIMIT 5"
);
$food_labels = [];
$food_data   = [];
while ($row = $food_res->fetch_assoc()) {
    $food_labels[] = $row['name'];
    $food_data[]   = (int) $row['total_orders'];
}

/* ──────────────────────────────────────────
   RECENT 5 ORDERS
─────────────────────────────────────────── */
$recent_orders_res = $conn->query(
    "SELECT o.id, u.name AS user_name, o.total_price, o.order_status, o.created_at
     FROM orders o
     LEFT JOIN users u ON o.user_id = u.id
     ORDER BY o.id DESC
     LIMIT 5"
);
$recent_orders = [];
while ($row = $recent_orders_res->fetch_assoc()) {
    $recent_orders[] = $row;
}

// JSON encode for charts
$revenue_labels_json  = json_encode($revenue_labels);
$revenue_data_json    = json_encode($revenue_data);
$orders_data_json     = json_encode($orders_data);
$status_labels_json   = json_encode($status_labels);
$status_data_json     = json_encode($status_data);
$ug_labels_json       = json_encode($user_growth_labels);
$ug_data_json         = json_encode($user_growth_data);
$food_labels_json     = json_encode($food_labels);
$food_data_json       = json_encode($food_data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="QuickBite Admin Dashboard — Platform analytics and management.">
    <title>Dashboard — QuickBite Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <style>
        :root {
            --neon-cyan: #FF5A00;
            --bg-dark: #F8FAFC;
            --bg-secondary: #FFFFFF;
            --bg-card: rgba(255, 255, 255, 0.04);
            --text-primary: #0F172A;
            --text-secondary: #475569;
            --grad-primary: linear-gradient(135deg, #FF4747, #3A86FF);
            --border-glass: rgba(255, 255, 255, 0.08);
            --sidebar-w: 260px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: #0F172A;
            display: flex;
        }

        /* ── SIDEBAR ── */
        #adminSidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: rgba(11,16,32,0.95);
            border-right: 1px solid var(--border-glass);
            backdrop-filter: blur(20px);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 24px 20px 16px;
            border-bottom: 1px solid var(--border-glass);
        }

        .sidebar-logo {
            display: flex; align-items: center; gap: 12px;
        }

        .sidebar-logo-icon {
            width: 42px; height: 42px;
            background: var(--grad-primary);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            box-shadow: 0 0 20px rgba(255,71,71,0.3);
        }

        .sidebar-logo-text {
            font-size: 20px; font-weight: 800;
            background: var(--grad-primary);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .sidebar-logo-version { font-size: 10px; color: var(--text-secondary); }

        .sidebar-profile {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-glass);
            display: flex; align-items: center; gap: 12px;
        }

        .sidebar-avatar {
            width: 40px; height: 40px;
            background: var(--grad-primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 700; color: #F8FAFC;
            flex-shrink: 0;
        }

        .sidebar-profile-info { min-width: 0; }
        .sidebar-profile-name { font-size: 13.5px; font-weight: 600; truncate: ellipsis; overflow: hidden; white-space: nowrap; }
        .sidebar-profile-role { font-size: 11px; color: var(--neon-cyan); font-weight: 500; }

        .sidebar-nav { flex: 1; padding: 12px 12px; overflow-y: auto; }

        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 16px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 13.5px; font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 4px;
            position: relative;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.06);
            color: #0F172A;
        }

        .nav-item.active {
            background: rgba(255,71,71,0.1);
            color: var(--neon-cyan);
            border: 1px solid rgba(255,71,71,0.2);
        }

        .nav-item.active::before {
            content: '';
            position: absolute; left: 0; top: 20%; height: 60%;
            width: 3px; background: var(--neon-cyan); border-radius: 0 2px 2px 0;
        }

        .nav-item-icon { font-size: 18px; width: 22px; text-align: center; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border-glass);
        }

        .nav-logout {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 16px;
            border-radius: 10px;
            text-decoration: none;
            color: #FF4D6D;
            font-size: 13.5px; font-weight: 500;
            transition: background 0.2s;
        }

        .nav-logout:hover { background: rgba(255,77,109,0.1); }

        /* ── ADMIN MAIN ── */
        #adminMain {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        /* ── TOPBAR ── */
        .admin-topbar {
            height: 64px;
            background: rgba(11,16,32,0.9);
            border-bottom: 1px solid var(--border-glass);
            backdrop-filter: blur(20px);
            display: flex; align-items: center; gap: 16px;
            padding: 0 24px;
            position: sticky; top: 0; z-index: 50;
        }

        #sidebarToggle {
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border-glass);
            border-radius: 8px;
            padding: 8px 10px;
            color: #0F172A;
            cursor: pointer; font-size: 18px;
            display: none;
            transition: background 0.2s;
        }

        #sidebarToggle:hover { background: rgba(255,71,71,0.1); }

        .topbar-title { font-size: 17px; font-weight: 700; flex: 1; }

        .admin-search-wrapper {
            position: relative;
        }

        #adminSearchInput {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border-glass);
            border-radius: 10px;
            padding: 8px 16px 8px 36px;
            color: #0F172A;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            width: 220px;
            outline: none;
            transition: border-color 0.2s;
        }

        #adminSearchInput:focus { border-color: var(--neon-cyan); }
        #adminSearchInput::placeholder { color: rgba(148,163,184,0.4); }

        .search-icon {
            position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
            color: var(--text-secondary); font-size: 14px; pointer-events: none;
        }

        .topbar-clock { text-align: right; }
        #adminClock { font-size: 18px; font-weight: 700; color: var(--neon-cyan); font-variant-numeric: tabular-nums; }
        #adminDate { font-size: 11px; color: var(--text-secondary); }

        /* Notification */
        .notif-wrapper { position: relative; }

        .notif-btn {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border-glass);
            border-radius: 10px;
            padding: 8px 12px;
            color: #0F172A;
            cursor: pointer; font-size: 18px;
            position: relative;
            transition: background 0.2s;
        }

        .notif-btn:hover { background: rgba(255,71,71,0.08); }

        .notif-badge {
            position: absolute; top: -4px; right: -4px;
            width: 16px; height: 16px;
            background: #FF4D6D;
            border-radius: 50%;
            font-size: 9px; font-weight: 700; color: #fff;
            display: flex; align-items: center; justify-content: center;
        }

        .notif-dropdown {
            position: absolute; right: 0; top: calc(100% + 10px);
            width: 300px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-glass);
            border-radius: 14px;
            padding: 16px;
            display: none;
            z-index: 200;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }

        .notif-dropdown.open { display: block; animation: fadeIn 0.2s ease; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }

        .notif-header { font-size: 13px; font-weight: 600; margin-bottom: 12px; color: var(--text-secondary); }

        .notif-item {
            display: flex; gap: 10px; padding: 10px 0;
            border-bottom: 1px solid var(--border-glass);
        }

        .notif-item:last-child { border-bottom: none; padding-bottom: 0; }
        .notif-icon { font-size: 20px; }
        .notif-text { font-size: 12.5px; line-height: 1.5; }
        .notif-time { font-size: 11px; color: var(--text-secondary); margin-top: 2px; }

        /* ── CONTENT ── */
        .admin-content { flex: 1; padding: 24px; overflow-y: auto; }

        /* Welcome */
        .welcome-widget {
            background: linear-gradient(135deg, rgba(255,71,71,0.08), rgba(58,134,255,0.08));
            border: 1px solid rgba(255,71,71,0.15);
            border-radius: 16px; padding: 20px 24px;
            margin-bottom: 24px;
            display: flex; align-items: center; justify-content: space-between;
        }

        .welcome-title { font-size: 20px; font-weight: 700; }
        .welcome-date { font-size: 13px; color: var(--text-secondary); margin-top: 4px; }
        .welcome-badge { background: var(--grad-primary); color: #F8FAFC; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 20px; }

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 16px; padding: 20px;
            backdrop-filter: blur(10px);
            transition: transform 0.2s, border-color 0.2s;
            position: relative; overflow: hidden;
        }

        .stat-card:hover { transform: translateY(-3px); border-color: rgba(255,71,71,0.2); }

        .stat-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 2px;
        }

        .stat-card.c1::before { background: linear-gradient(90deg,#FF4747,#3A86FF); }
        .stat-card.c2::before { background: linear-gradient(90deg,#7C3AED,#DB2777); }
        .stat-card.c3::before { background: linear-gradient(90deg,#F59E0B,#EF4444); }
        .stat-card.c4::before { background: linear-gradient(90deg,#10B981,#059669); }
        .stat-card.c5::before { background: linear-gradient(90deg,#6366F1,#8B5CF6); }
        .stat-card.c6::before { background: linear-gradient(90deg,#06B6D4,#0891B2); }
        .stat-card.c7::before { background: linear-gradient(90deg,#F97316,#EA580C); }
        .stat-card.c8::before { background: linear-gradient(90deg,#22C55E,#16A34A); }

        .stat-icon { font-size: 32px; margin-bottom: 12px; }
        .stat-value { font-size: 28px; font-weight: 800; line-height: 1; margin-bottom: 4px; }
        .stat-label { font-size: 12px; color: var(--text-secondary); font-weight: 500; }

        /* Charts grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .chart-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 16px; padding: 20px;
            backdrop-filter: blur(10px);
        }

        .chart-title {
            font-size: 14px; font-weight: 600; margin-bottom: 16px;
            display: flex; align-items: center; gap: 8px;
        }

        .chart-title .dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--neon-cyan); box-shadow: 0 0 6px var(--neon-cyan);
        }

        .chart-wrapper { position: relative; height: 200px; }
        .chart-wrapper canvas { max-height: 200px; }

        /* Bottom row */
        .bottom-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* Activity feed */
        .activity-feed { display: flex; flex-direction: column; gap: 10px; }

        .activity-item {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px;
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            transition: background 0.2s;
        }

        .activity-item:hover { background: rgba(255,255,255,0.05); }

        .activity-avatar {
            width: 36px; height: 36px;
            background: var(--grad-primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #F8FAFC;
            flex-shrink: 0;
        }

        .activity-info { flex: 1; min-width: 0; }
        .activity-name { font-size: 13px; font-weight: 600; }
        .activity-meta { font-size: 11px; color: var(--text-secondary); margin-top: 2px; }

        .status-badge {
            padding: 4px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }
        .badge-delivered { background: rgba(0,217,126,0.15); color: #00D97E; }
        .badge-pending   { background: rgba(249,115,22,0.15); color: #F97316; }
        .badge-cancelled { background: rgba(255,77,109,0.15); color: #FF4D6D; }
        .badge-processing{ background: rgba(255,71,71,0.15); color: var(--neon-cyan); }
        .badge-default   { background: rgba(255,255,255,0.06); color: var(--text-secondary); }

        .order-price { font-size: 14px; font-weight: 700; color: var(--neon-cyan); }

        /* Sidebar collapsed */
        .sidebar-collapsed #adminSidebar { transform: translateX(-100%); }
        .sidebar-collapsed #adminMain { margin-left: 0; }

        @media(max-width:1200px) { .stats-grid { grid-template-columns: repeat(2,1fr); } }
        @media(max-width:900px) {
            #sidebarToggle { display: flex; }
            #adminSidebar { transform: translateX(-100%); }
            #adminMain { margin-left: 0; }
            .sidebar-open #adminSidebar { transform: translateX(0); }
            .charts-grid, .bottom-row { grid-template-columns: 1fr; }
        }
        @media(max-width:600px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .admin-content { padding: 16px; }
        }
    </style>
</head>
<body class="admin-layout">

<!-- ── SIDEBAR ── -->
<aside id="adminSidebar" role="navigation" aria-label="Admin navigation">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">🍔</div>
            <div>
                <div class="sidebar-logo-text">QuickBite</div>
                <div class="sidebar-logo-version">Admin Panel v2.0</div>
            </div>
        </div>
    </div>

    <div class="sidebar-profile">
        <div class="sidebar-avatar"><?= strtoupper(substr($admin_name, 0, 1)) ?></div>
        <div class="sidebar-profile-info">
            <div class="sidebar-profile-name"><?= htmlspecialchars($admin_name) ?></div>
            <div class="sidebar-profile-role">⚡ Super Admin</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item active" id="nav-dashboard">
            <span class="nav-item-icon">📊</span>
            <span>Dashboard</span>
        </a>
        <a href="restaurants.php" class="nav-item" id="nav-restaurants">
            <span class="nav-item-icon">🏪</span>
            <span>Restaurants</span>
        </a>
        <a href="foods.php" class="nav-item" id="nav-foods">
            <span class="nav-item-icon">🍽️</span>
            <span>Foods</span>
        </a>
        <a href="orders.php" class="nav-item" id="nav-orders">
            <span class="nav-item-icon">📦</span>
            <span>Orders</span>
        </a>
        <a href="users.php" class="nav-item" id="nav-users">
            <span class="nav-item-icon">👥</span>
            <span>Users</span>
        </a>
        <a href="coupons.php" class="nav-item" id="nav-coupons">
            <span class="nav-item-icon">🎟️</span>
            <span>Coupons</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="../auth/logout.php" class="nav-logout">
            <span class="nav-item-icon">🚪</span>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- ── ADMIN MAIN ── -->
<div id="adminMain">

    <!-- TOPBAR -->
    <header class="admin-topbar">
        <button id="sidebarToggle" aria-label="Toggle sidebar">☰</button>
        <span class="topbar-title">Dashboard</span>

        <div class="admin-search-wrapper">
            <span class="search-icon">🔍</span>
            <input type="search" id="adminSearchInput" placeholder="Search anything…" aria-label="Search admin panel">
        </div>

        <div class="topbar-clock" aria-live="polite">
            <div id="adminClock">00:00:00</div>
            <div id="adminDate">Loading…</div>
        </div>

        <div class="notif-wrapper">
            <button class="notif-btn" id="notifBtn" aria-label="Notifications" aria-expanded="false">
                🔔
                <span class="notif-badge" aria-label="3 notifications">3</span>
            </button>
            <div class="notif-dropdown" id="notifDropdown" role="dialog" aria-label="Notifications">
                <div class="notif-header">🔔 Recent Notifications</div>
                <div class="notif-item">
                    <span class="notif-icon">📦</span>
                    <div>
                        <div class="notif-text">New order #<?= $stat_orders ?> placed successfully</div>
                        <div class="notif-time">Just now</div>
                    </div>
                </div>
                <div class="notif-item">
                    <span class="notif-icon">👤</span>
                    <div>
                        <div class="notif-text"><?= $stat_today_orders ?> new users registered today</div>
                        <div class="notif-time">2 hours ago</div>
                    </div>
                </div>
                <div class="notif-item">
                    <span class="notif-icon">⚠️</span>
                    <div>
                        <div class="notif-text"><?= $stat_pending ?> orders are still pending review</div>
                        <div class="notif-time">5 hours ago</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="admin-content">

        <!-- Welcome widget -->
        <div class="welcome-widget">
            <div>
                <div class="welcome-title">Welcome back, <?= htmlspecialchars($admin_name) ?>! 👋</div>
                <div class="welcome-date"><?= date('l, F j, Y') ?></div>
            </div>
            <div class="welcome-badge">Admin Panel</div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card c1">
                <div class="stat-icon">👥</div>
                <div class="stat-value" data-target="<?= $stat_users ?>" id="ctr-users">0</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card c2">
                <div class="stat-icon">📦</div>
                <div class="stat-value" data-target="<?= $stat_orders ?>" id="ctr-orders">0</div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card c3">
                <div class="stat-icon">🍽️</div>
                <div class="stat-value" data-target="<?= $stat_foods ?>" id="ctr-foods">0</div>
                <div class="stat-label">Total Foods</div>
            </div>
            <div class="stat-card c4">
                <div class="stat-icon">🏪</div>
                <div class="stat-value" data-target="<?= $stat_restaurants ?>" id="ctr-restaurants">0</div>
                <div class="stat-label">Total Restaurants</div>
            </div>
            <div class="stat-card c5">
                <div class="stat-icon">💰</div>
                <div class="stat-value" id="ctr-revenue">₹0</div>
                <div class="stat-label">Revenue (₹)</div>
            </div>
            <div class="stat-card c6">
                <div class="stat-icon">📅</div>
                <div class="stat-value" data-target="<?= $stat_today_orders ?>" id="ctr-today">0</div>
                <div class="stat-label">Today's Orders</div>
            </div>
            <div class="stat-card c7">
                <div class="stat-icon">⏳</div>
                <div class="stat-value" data-target="<?= $stat_pending ?>" id="ctr-pending">0</div>
                <div class="stat-label">Pending Orders</div>
            </div>
            <div class="stat-card c8">
                <div class="stat-icon">✅</div>
                <div class="stat-value" data-target="<?= $stat_delivered ?>" id="ctr-delivered">0</div>
                <div class="stat-label">Delivered Orders</div>
            </div>
            <div class="stat-card c9" style="border-color: rgba(255,184,0,0.4);">
                <div class="stat-icon">⭐</div>
                <div class="stat-value"><?= number_format($stat_app_rating, 1) ?></div>
                <div class="stat-label">App Rating (Out of 5)</div>
            </div>
        </div>


        <!-- Charts Grid -->
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-title"><span class="dot" style="background:#FF4747;box-shadow:0 0 6px #FF4747;"></span> Revenue (Last 7 Days)</div>
                <div class="chart-wrapper"><canvas id="revenueChart"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-title"><span class="dot" style="background:#3A86FF;box-shadow:0 0 6px #3A86FF;"></span> Orders (Last 7 Days)</div>
                <div class="chart-wrapper"><canvas id="ordersChart"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-title"><span class="dot" style="background:#7C3AED;box-shadow:0 0 6px #7C3AED;"></span> Order Status Breakdown</div>
                <div class="chart-wrapper"><canvas id="statusChart"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-title"><span class="dot" style="background:#10B981;box-shadow:0 0 6px #10B981;"></span> User Growth (6 Months)</div>
                <div class="chart-wrapper"><canvas id="userGrowthChart"></canvas></div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="bottom-row">
            <!-- Food Popularity -->
            <div class="chart-card">
                <div class="chart-title"><span class="dot" style="background:#F59E0B;box-shadow:0 0 6px #F59E0B;"></span> Food Popularity (Top 5)</div>
                <div class="chart-wrapper"><canvas id="foodPopularityChart"></canvas></div>
            </div>

            <!-- Recent Activity -->
            <div class="chart-card">
                <div class="chart-title"><span class="dot" style="background:#DB2777;box-shadow:0 0 6px #DB2777;"></span> Recent Orders</div>
                <div class="activity-feed">
                    <?php if (empty($recent_orders)): ?>
                        <p style="color:var(--text-secondary);font-size:13px;text-align:center;padding:24px;">No recent orders found.</p>
                    <?php else: ?>
                        <?php foreach ($recent_orders as $order): ?>
                            <?php
                            $initials   = strtoupper(substr($order['user_name'] ?? 'U', 0, 1));
                            $status_cls = match($order['order_status']) {
                                'Delivered'  => 'badge-delivered',
                                'Pending'    => 'badge-pending',
                                'Cancelled'  => 'badge-cancelled',
                                'Processing' => 'badge-processing',
                                default      => 'badge-default',
                            };
                            ?>
                            <div class="activity-item">
                                <div class="activity-avatar"><?= $initials ?></div>
                                <div class="activity-info">
                                    <div class="activity-name"><?= htmlspecialchars($order['user_name'] ?? 'Unknown') ?></div>
                                    <div class="activity-meta">Order #<?= $order['id'] ?> · <?= date('M j, H:i', strtotime($order['created_at'])) ?></div>
                                </div>
                                <span class="status-badge <?= $status_cls ?>"><?= htmlspecialchars($order['order_status']) ?></span>
                                <span class="order-price">₹<?= number_format($order['total_price'], 0) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>
</div><!-- end adminMain -->

<script src="../assets/js/main.js"></script>
<script src="../assets/js/dashboard.js"></script>
<script>
    // ── CLOCK ──
    function updateClock() {
        const now  = new Date();
        const time = now.toLocaleTimeString('en-IN', { hour12: false });
        const date = now.toLocaleDateString('en-IN', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        document.getElementById('adminClock').textContent = time;
        document.getElementById('adminDate').textContent  = date;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── SIDEBAR TOGGLE ──
    const sidebarToggle = document.getElementById('sidebarToggle');
    sidebarToggle && sidebarToggle.addEventListener('click', () => {
        document.body.classList.toggle('sidebar-open');
    });

    // ── NOTIFICATION DROPDOWN ──
    const notifBtn      = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    notifBtn && notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = notifDropdown.classList.toggle('open');
        notifBtn.setAttribute('aria-expanded', isOpen);
    });
    document.addEventListener('click', () => {
        notifDropdown && notifDropdown.classList.remove('open');
        notifBtn && notifBtn.setAttribute('aria-expanded', 'false');
    });

    // ── ANIMATED COUNTERS ──
    function animateCounter(el, target, prefix = '', suffix = '') {
        let start = 0;
        const duration = 1500;
        const step = target / (duration / 16);
        const timer = setInterval(() => {
            start += step;
            if (start >= target) { start = target; clearInterval(timer); }
            el.textContent = prefix + Math.floor(start).toLocaleString('en-IN') + suffix;
        }, 16);
    }

    document.querySelectorAll('.stat-value[data-target]').forEach(el => {
        animateCounter(el, parseInt(el.dataset.target));
    });
    // Revenue counter
    const revEl = document.getElementById('ctr-revenue');
    if (revEl) animateCounter(revEl, <?= $stat_revenue ?>, '₹');

    // ── CHART DEFAULTS ──
    const chartDefaults = {
        color: '#94A3B8',
        grid: { color: 'rgba(255,255,255,0.04)' },
        plugins: { legend: { labels: { color: '#94A3B8', font: { family: 'Inter', size: 12 }, padding: 16 } } }
    };

    // ── REVENUE CHART ──
    function initRevenueChart(labels, data) {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue (₹)',
                    data,
                    borderColor: '#FF4747',
                    backgroundColor: 'rgba(255,71,71,0.08)',
                    borderWidth: 2,
                    pointBackgroundColor: '#FF4747',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#94A3B8', font: { family: 'Inter' } }, grid: chartDefaults.grid },
                    y: { ticks: { color: '#94A3B8', font: { family: 'Inter' } }, grid: chartDefaults.grid }
                }
            }
        });
    }

    // ── ORDERS CHART ──
    function initOrdersChart(labels, data) {
        const ctx = document.getElementById('ordersChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Orders',
                    data,
                    backgroundColor: 'rgba(58,134,255,0.6)',
                    borderColor: '#3A86FF',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#94A3B8', font: { family: 'Inter' } }, grid: chartDefaults.grid },
                    y: { ticks: { color: '#94A3B8', font: { family: 'Inter' } }, grid: chartDefaults.grid }
                }
            }
        });
    }

    // ── STATUS DONUT CHART ──
    function initStatusChart(labels, data) {
        const ctx = document.getElementById('statusChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: ['#00D97E','#F97316','#FF4D6D','#FF4747','#7C3AED'],
                    borderColor: '#F8FAFC',
                    borderWidth: 3,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#94A3B8', font: { family: 'Inter', size: 11 }, padding: 12 } }
                },
                cutout: '65%',
            }
        });
    }

    // ── USER GROWTH CHART ──
    function initUserGrowthChart(labels, data) {
        const ctx = document.getElementById('userGrowthChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'New Users',
                    data,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16,185,129,0.08)',
                    borderWidth: 2,
                    pointBackgroundColor: '#10B981',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#94A3B8', font: { family: 'Inter' } }, grid: chartDefaults.grid },
                    y: { ticks: { color: '#94A3B8', font: { family: 'Inter' } }, grid: chartDefaults.grid }
                }
            }
        });
    }

    // ── FOOD POPULARITY CHART ──
    function initFoodChart(labels, data) {
        const ctx = document.getElementById('foodPopularityChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Orders',
                    data,
                    backgroundColor: [
                        'rgba(245,158,11,0.7)',
                        'rgba(255,71,71,0.7)',
                        'rgba(58,134,255,0.7)',
                        'rgba(124,58,237,0.7)',
                        'rgba(16,185,129,0.7)',
                    ],
                    borderColor: ['#F59E0B','#FF4747','#3A86FF','#7C3AED','#10B981'],
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#94A3B8', font: { family: 'Inter' } }, grid: chartDefaults.grid },
                    y: { ticks: { color: '#94A3B8', font: { family: 'Inter' } }, grid: chartDefaults.grid }
                }
            }
        });
    }

    // ── INIT ALL CHARTS after Chart.js loads ──
    window.addEventListener('load', function () {
        initRevenueChart(<?= $revenue_labels_json ?>, <?= $revenue_data_json ?>);
        initOrdersChart(<?= $revenue_labels_json ?>, <?= $orders_data_json ?>);
        initStatusChart(<?= $status_labels_json ?>, <?= $status_data_json ?>);
        initUserGrowthChart(<?= $ug_labels_json ?>, <?= $ug_data_json ?>);
        initFoodChart(<?= $food_labels_json ?>, <?= $food_data_json ?>);
    });
</script>
</body>
</html>