<?php
// ================================================
// QUICKBITE 2.0 — DATABASE CONFIGURATION
// ================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'quickbite');
define('DB_VERSION', '2.0');
define('SITE_NAME', 'QuickBite');
define('SITE_URL', 'http://localhost/TASK-4/QUICKBITE');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'gif']);

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database Connection Failed: ' . $conn->connect_error]));
}

// Set charset
$conn->set_charset('utf8mb4');

// ================================================
// PREPARED STATEMENT HELPER
// ================================================
function db_query($conn, $sql, $types = '', $params = []) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}

// ================================================
// FETCH ONE ROW
// ================================================
function db_fetch($conn, $sql, $types = '', $params = []) {
    $stmt = db_query($conn, $sql, $types, $params);
    if (!$stmt) return null;
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// ================================================
// FETCH ALL ROWS
// ================================================
function db_fetch_all($conn, $sql, $types = '', $params = []) {
    $stmt = db_query($conn, $sql, $types, $params);
    if (!$stmt) return [];
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// ================================================
// COUNT ROWS
// ================================================
function db_count($conn, $table, $where = '1=1', $types = '', $params = []) {
    $sql = "SELECT COUNT(*) as cnt FROM `$table` WHERE $where";
    $row = db_fetch($conn, $sql, $types, $params);
    return $row ? (int)$row['cnt'] : 0;
}

// ================================================
// SAFE OUTPUT ESCAPING
// ================================================
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}