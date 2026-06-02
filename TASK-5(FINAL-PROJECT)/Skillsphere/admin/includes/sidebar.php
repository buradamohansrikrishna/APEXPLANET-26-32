<?php
$adminPage = $adminPage ?? '';
if (!isset($pathPrefix)) {
    $pathPrefix = '';
    $currSelf = $_SERVER['PHP_SELF'];
    if (strpos($currSelf, '/users/') !== false || strpos($currSelf, '/courses/') !== false || strpos($currSelf, '/instructors/') !== false || strpos($currSelf, '/students/') !== false || strpos($currSelf, '/content/') !== false) {
        $pathPrefix = '../';
    }
}
$profileImg = $pathPrefix . '../uploads/profiles/' . ($_SESSION['profile_image'] ?? 'default.png');
$adminName = htmlspecialchars($_SESSION['user_name'] ?? 'Administrator');
?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar__brand">
        <a href="<?php echo $pathPrefix; ?>dashboard.php" class="admin-sidebar__logo">
            <span class="admin-sidebar__mark">S</span>
            <span>Skill<span class="accent">Sphere</span></span>
        </a>
        <span class="admin-sidebar__badge">Admin</span>
    </div>

    <div class="admin-sidebar__user">
        <a href="<?php echo $pathPrefix; ?>../settings.php" style="display:flex; gap:12px; align-items:center; text-decoration:none; color:inherit;">
            <img src="<?php echo htmlspecialchars($profileImg); ?>" alt="" class="admin-sidebar__avatar" onerror="this.src='<?php echo $pathPrefix; ?>../uploads/profiles/default.png'">
            <div>
                <strong><?php echo $adminName; ?></strong>
                <small style="display:block;">Control center <i class="fa-solid fa-gear" style="font-size:0.75rem; margin-left:2px; opacity:0.7;"></i></small>
            </div>
        </a>
    </div>

    <nav class="admin-nav" aria-label="Admin navigation" style="overflow-y: auto; max-height: calc(100vh - 200px); padding-bottom: 2rem;">
        <a href="<?php echo $pathPrefix; ?>dashboard.php" class="admin-nav__link <?php echo $adminPage === 'dashboard' ? 'is-active' : ''; ?>">
            <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
        </a>
        <a href="<?php echo $pathPrefix; ?>analytics.php" class="admin-nav__link <?php echo $adminPage === 'analytics' ? 'is-active' : ''; ?>">
            <i class="fa-solid fa-chart-line"></i><span>Analytics</span>
        </a>
        <a href="<?php echo $pathPrefix; ?>revenue.php" class="admin-nav__link <?php echo $adminPage === 'revenue' ? 'is-active' : ''; ?>">
            <i class="fa-solid fa-indian-rupee-sign"></i><span>Revenue</span>
        </a>
        <a href="<?php echo $pathPrefix; ?>users/manage-users.php" class="admin-nav__link <?php echo $adminPage === 'users' ? 'is-active' : ''; ?>">
            <i class="fa-solid fa-users"></i><span>Users</span>
        </a>
        <a href="<?php echo $pathPrefix; ?>courses/manage-courses.php" class="admin-nav__link <?php echo $adminPage === 'courses' ? 'is-active' : ''; ?>">
            <i class="fa-solid fa-book-open"></i><span>Courses</span>
        </a>
        <a href="<?php echo $pathPrefix; ?>courses/approvals.php" class="admin-nav__link <?php echo $adminPage === 'approvals' ? 'is-active' : ''; ?>">
            <i class="fa-solid fa-square-check"></i><span>Course Approvals</span>
        </a>
        <a href="<?php echo $pathPrefix; ?>courses/categories.php" class="admin-nav__link <?php echo $adminPage === 'categories' ? 'is-active' : ''; ?>">
            <i class="fa-solid fa-tags"></i><span>Categories</span>
        </a>
        <a href="<?php echo $pathPrefix; ?>instructors/manage-instructors.php" class="admin-nav__link <?php echo $adminPage === 'instructors' ? 'is-active' : ''; ?>">
            <i class="fa-solid fa-chalkboard-user"></i><span>Instructors</span>
        </a>
        <a href="<?php echo $pathPrefix; ?>students/manage-students.php" class="admin-nav__link <?php echo $adminPage === 'students' ? 'is-active' : ''; ?>">
            <i class="fa-solid fa-user-graduate"></i><span>Students</span>
        </a>
        <a href="<?php echo $pathPrefix; ?>content/manage-blog.php" class="admin-nav__link <?php echo $adminPage === 'content' ? 'is-active' : ''; ?>">
            <i class="fa-solid fa-pen-nib"></i><span>Blog</span>
        </a>
        <a href="<?php echo $pathPrefix; ?>content/testimonials.php" class="admin-nav__link <?php echo $adminPage === 'testimonials' ? 'is-active' : ''; ?>">
            <i class="fa-solid fa-quote-left"></i><span>Testimonials</span>
        </a>
        <a href="<?php echo $pathPrefix; ?>content/faq.php" class="admin-nav__link <?php echo $adminPage === 'faq' ? 'is-active' : ''; ?>">
            <i class="fa-solid fa-circle-question"></i><span>FAQs</span>
        </a>
        <a href="<?php echo $pathPrefix; ?>content/announcements.php" class="admin-nav__link <?php echo $adminPage === 'announcements' ? 'is-active' : ''; ?>">
            <i class="fa-solid fa-bullhorn"></i><span>Announcements</span>
        </a>
    </nav>

    <div class="admin-sidebar__footer">
        <a href="<?php echo $pathPrefix; ?>../index.php" class="admin-nav__link admin-nav__link--muted" target="_blank" rel="noopener">
            <i class="fa-solid fa-arrow-up-right-from-square"></i><span>View site</span>
        </a>
        <a href="<?php echo $pathPrefix; ?>../logout.php" class="admin-nav__link admin-nav__link--danger">
            <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
        </a>
    </div>
</aside>

<button type="button" class="admin-sidebar-toggle" id="adminSidebarToggle" aria-label="Toggle menu">
    <i class="fa-solid fa-bars"></i>
</button>
