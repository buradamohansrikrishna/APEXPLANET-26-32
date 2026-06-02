<?php
// =========================================
// SKILLSPHERE SECURITY FUNCTIONS
// security.php
// =========================================

require_once __DIR__ . '/session.php';

// Set standard security headers
if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header("Content-Security-Policy: default-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: http://localhost; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com; frame-src 'self'; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com;");
}

if (!function_exists('cleanInput')) {
    function cleanInput($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = cleanInput($value);
            }
            return $data;
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('cleanHtml')) {
    function cleanHtml($html) {
        // Strip script tags and dangerous HTML attributes
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
        $html = preg_replace('/on\w+\s*=\s*"/i', 'data-js-removed="', $html);
        $html = preg_replace('/on\w+\s*=\s*\'/i', 'data-js-removed=\'', $html);
        return $html;
    }
}
?>
