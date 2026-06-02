<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (file_exists(__DIR__ . '/functions.php')) {
    require_once __DIR__ . '/functions.php';
}
if (!function_exists('activePage')) {
    function activePage($page) {
        return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
    }
}
$rootPrefix = $rootPrefix ?? '';
?>
<header class="site-header header" id="siteHeader">
    <div class="container">
        <a href="<?php echo $rootPrefix; ?>index.php" class="logo" aria-label="SkillSphere Home">
            <span class="logo-mark">S</span>
            Skill<span>Sphere</span>
        </a>

        <nav class="nav-main nav-links" id="navLinks" aria-label="Main navigation">
            <a href="<?php echo $rootPrefix; ?>index.php" class="<?php echo activePage('index.php'); ?>">Home</a>
            <a href="<?php echo $rootPrefix; ?>courses.php" class="<?php echo activePage('courses.php'); ?>">Courses</a>
            <a href="<?php echo $rootPrefix; ?>about.php" class="<?php echo activePage('about.php'); ?>">About</a>
            <a href="<?php echo $rootPrefix; ?>contact.php" class="<?php echo activePage('contact.php'); ?>">Contact</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $rootPrefix; ?>profile.php" class="<?php echo activePage('profile.php'); ?>">Profile</a>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="<?php echo $rootPrefix; ?>admin/dashboard.php">Admin</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>

        <div class="nav-actions">
            <button type="button" class="theme-toggle" data-theme-toggle aria-label="Toggle dark mode">
                <i class="fa-solid fa-sun theme-toggle__icon theme-toggle__icon--sun" aria-hidden="true"></i>
                <i class="fa-solid fa-moon theme-toggle__icon theme-toggle__icon--moon" aria-hidden="true"></i>
            </button>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $rootPrefix; ?>logout.php" class="btn btn-sm btn-outline">Logout</a>
            <?php else: ?>
                <a href="<?php echo $rootPrefix; ?>login.php" class="btn btn-ghost btn-sm <?php echo activePage('login.php'); ?>">Log in</a>
                <a href="<?php echo $rootPrefix; ?>register.php" class="btn btn-sm nav-btn">Get Started</a>
            <?php endif; ?>

            <button type="button" class="menu-toggle" id="menuToggle" aria-label="Open menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>
