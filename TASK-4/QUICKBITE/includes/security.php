<?php
// ================================================
// QUICKBITE 2.0 — SECURITY HELPERS
// ================================================

// ─── CSRF TOKEN ───────────────────────────────
function csrf_token() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf($token) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

// ─── RATE LIMITING ────────────────────────────
function check_rate_limit($key, $max_attempts = 5, $window_seconds = 900) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $now = time();
    $attempts_key = 'rl_' . $key;
    $time_key = 'rl_time_' . $key;

    if (!isset($_SESSION[$attempts_key])) {
        $_SESSION[$attempts_key] = 0;
        $_SESSION[$time_key] = $now;
    }

    // Reset window if expired
    if (($now - $_SESSION[$time_key]) > $window_seconds) {
        $_SESSION[$attempts_key] = 0;
        $_SESSION[$time_key] = $now;
    }

    return $_SESSION[$attempts_key] < $max_attempts;
}

function increment_rate_limit($key) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $attempts_key = 'rl_' . $key;
    if (!isset($_SESSION[$attempts_key])) $_SESSION[$attempts_key] = 0;
    $_SESSION[$attempts_key]++;
}

function reset_rate_limit($key) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    unset($_SESSION['rl_' . $key], $_SESSION['rl_time_' . $key]);
}

// ─── INPUT SANITIZATION ───────────────────────
function sanitize_string($input) {
    return trim(strip_tags($input ?? ''));
}

function sanitize_email($input) {
    return filter_var(trim($input ?? ''), FILTER_SANITIZE_EMAIL);
}

function sanitize_int($input) {
    return (int)($input ?? 0);
}

function sanitize_float($input) {
    return (float)filter_var($input ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

// ─── FILE UPLOAD VALIDATION ──────────────────
function validate_upload($file, $max_size = null) {
    if ($max_size === null) $max_size = UPLOAD_MAX_SIZE ?? 5242880;
    $allowed = ALLOWED_EXTENSIONS ?? ['jpg','jpeg','png','webp'];

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File too large (max 5MB)'];
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowed)];
    }
    // MIME type check
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed_mimes = ['image/jpeg','image/png','image/gif','image/webp'];
    if (!in_array($mime, $allowed_mimes)) {
        return ['success' => false, 'error' => 'Invalid file content'];
    }
    return ['success' => true, 'ext' => $ext];
}

function upload_file($file, $dir, $prefix = 'img') {
    $validation = validate_upload($file);
    if (!$validation['success']) return $validation;

    $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $validation['ext'];
    $target = rtrim($dir, '/') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return ['success' => false, 'error' => 'Failed to move uploaded file'];
    }
    return ['success' => true, 'filename' => $filename];
}

// ─── SECURE HEADERS ───────────────────────────
function set_security_headers() {
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
