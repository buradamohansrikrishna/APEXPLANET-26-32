<?php
// =========================================
// SKILLSPHERE DYNAMIC ROUTING HELPERS
// routes.php
// =========================================

require_once __DIR__ . '/config.php';

if (!function_exists('route')) {
    function route($path) {
        return BASE_URL . ltrim($path, '/');
    }
}

if (!function_exists('getRouteParams')) {
    function getRouteParams() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base = parse_url(BASE_URL, PHP_URL_PATH);
        
        // Strip base path
        if (strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
        }
        
        return explode('/', trim($uri, '/'));
    }
}
?>
