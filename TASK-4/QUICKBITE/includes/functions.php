<?php
// ================================================
// QUICKBITE 2.0 — UTILITY FUNCTIONS
// ================================================

// ─── CART HELPERS ─────────────────────────────
function get_cart_count($user_id, $conn) {
    if (!$user_id) return 0;
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int)($row['total'] ?? 0);
}

function get_cart_items($user_id, $conn) {
    $stmt = $conn->prepare("
        SELECT c.*, f.food_name, f.price, f.image, f.category, r.restaurant_name
        FROM cart c
        JOIN foods f ON c.food_id = f.id
        JOIN restaurants r ON f.restaurant_id = r.id
        WHERE c.user_id = ? AND c.saved_for_later = 0
        ORDER BY c.added_at DESC
    ");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function get_cart_total($user_id, $conn) {
    $stmt = $conn->prepare("
        SELECT SUM(c.quantity * f.price) as total
        FROM cart c JOIN foods f ON c.food_id = f.id
        WHERE c.user_id = ? AND c.saved_for_later = 0
    ");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (float)($row['total'] ?? 0);
}

// ─── NOTIFICATIONS ────────────────────────────
function get_notifications($user_id, $conn, $limit = 10) {
    $stmt = $conn->prepare("
        SELECT * FROM notifications 
        WHERE user_id = ? OR user_id IS NULL
        ORDER BY created_at DESC LIMIT ?
    ");
    $stmt->bind_param('ii', $user_id, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function get_unread_count($user_id, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM notifications WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int)($row['cnt'] ?? 0);
}

function add_notification($conn, $user_id, $title, $message, $type = 'system') {
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('isss', $user_id, $title, $message, $type);
    return $stmt->execute();
}

// ─── PRICE FORMATTING ─────────────────────────
function format_price($amount) {
    return '₹' . number_format((float)$amount, 2);
}

// ─── TIME HELPERS ─────────────────────────────
function time_ago($timestamp) {
    $diff = time() - strtotime($timestamp);
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' mins ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hrs ago';
    if ($diff < 2592000) return floor($diff / 86400) . ' days ago';
    return date('d M Y', strtotime($timestamp));
}

function greeting() {
    $h = (int)date('G');
    if ($h < 12) return '🌅 Good Morning';
    if ($h < 17) return '☀️ Good Afternoon';
    if ($h < 21) return '🌆 Good Evening';
    return '🌙 Good Night';
}

// ─── ORDER HELPERS ────────────────────────────
function generate_order_number() {
    return 'QB' . strtoupper(date('ymd')) . rand(1000, 9999);
}

function get_order_status_color($status) {
    $colors = [
        'Pending'          => '#F59E0B',
        'Accepted'         => '#3B82F6',
        'Preparing'        => '#8B5CF6',
        'Ready'            => '#06B6D4',
        'Out For Delivery' => '#F97316',
        'Delivered'        => '#10B981',
        'Cancelled'        => '#EF4444',
    ];
    return $colors[$status] ?? '#6B7280';
}

function get_order_status_icon($status) {
    $icons = [
        'Pending'          => '⏳',
        'Accepted'         => '✅',
        'Preparing'        => '👨‍🍳',
        'Ready'            => '📦',
        'Out For Delivery' => '🛵',
        'Delivered'        => '🎉',
        'Cancelled'        => '❌',
    ];
    return $icons[$status] ?? '📋';
}

// ─── COUPON VALIDATION ────────────────────────
function validate_coupon($conn, $code, $order_total) {
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND expiry_date >= CURDATE() AND used_count < max_uses");
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $coupon = $stmt->get_result()->fetch_assoc();

    if (!$coupon) return ['valid' => false, 'error' => 'Invalid or expired coupon'];
    if ($order_total < $coupon['min_order_value']) {
        return ['valid' => false, 'error' => 'Minimum order ' . format_price($coupon['min_order_value']) . ' required'];
    }

    $discount = 0;
    if ($coupon['discount_type'] === 'percent') {
        $discount = ($order_total * $coupon['discount_value']) / 100;
        if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
            $discount = $coupon['max_discount'];
        }
    } else {
        $discount = $coupon['discount_value'];
    }

    return ['valid' => true, 'coupon' => $coupon, 'discount' => round($discount, 2)];
}

// ─── AVATAR INITIAL GENERATOR ─────────────────
function get_initials($name) {
    $words = explode(' ', trim($name));
    $initials = '';
    foreach (array_slice($words, 0, 2) as $word) {
        $initials .= strtoupper(substr($word, 0, 1));
    }
    return $initials ?: 'U';
}

// ─── ADMIN STATS ──────────────────────────────
function get_admin_stats($conn) {
    return [
        'users'       => db_count($conn, 'users'),
        'orders'      => db_count($conn, 'orders'),
        'foods'       => db_count($conn, 'foods'),
        'restaurants' => db_count($conn, 'restaurants'),
        'revenue'     => (float)(db_fetch($conn, "SELECT SUM(total_price) as r FROM orders WHERE order_status='Delivered'")['r'] ?? 0),
        'today_orders'=> db_count($conn, 'orders', "DATE(order_date) = CURDATE()"),
        'pending'     => db_count($conn, 'orders', "order_status='Pending'"),
        'delivered'   => db_count($conn, 'orders', "order_status='Delivered'"),
    ];
}
