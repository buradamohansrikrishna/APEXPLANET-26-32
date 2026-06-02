<?php
// =========================================
// SKILLSPHERE GENERAL HELPERS
// helpers.php
// =========================================

if (!function_exists('formatPrice')) {
    function formatPrice($amount) {
        return '₹' . number_format((float) $amount, 0);
    }
}

if (!function_exists('limitText')) {
    function limitText($text, $limit = 100) {
        if (strlen($text) > $limit) {
            return substr($text, 0, $limit) . '...';
        }
        return $text;
    }
}

if (!function_exists('timeAgo')) {
    function timeAgo($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}

if (!function_exists('getLevelBadgeClass')) {
    function getLevelBadgeClass($level) {
        $level = strtolower($level);
        switch ($level) {
            case 'advanced':
                return 'badge-danger';
            case 'intermediate':
                return 'badge-warning';
            default:
                return 'badge-success';
        }
    }
}
?>
