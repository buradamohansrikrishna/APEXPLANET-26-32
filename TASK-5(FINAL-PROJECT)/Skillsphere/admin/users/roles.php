<?php
require_once '../../auth.php';
requireAdmin();

$roleCounts = fetchAllSecure("SELECT role, COUNT(*) as count FROM users GROUP BY role");

$adminTitle = 'User Roles';
$adminPage = 'users';
$adminHeading = 'User roles & permissions';
$adminSubheading = 'Overview of administrative authority';
$adminIllustration = '../assets/images/admin-users.svg';
$adminHeroTitle = 'Access roles';
$adminHeroText = 'Manage roles distribution and baseline privileges for administrative control.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<div class="admin-panel reveal" style="max-width: 600px; margin: 0 auto;">
    <div class="admin-panel__head"><h3>Platform Roles Summary</h3></div>
    <div class="admin-panel__body">
        <ul class="role-list" style="display:flex; flex-direction:column; gap:1.5rem; list-style:none; padding:0;">
            <?php foreach ($roleCounts as $rc): ?>
                <li style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-default); padding-bottom:1rem;">
                    <div>
                        <strong style="font-size:1.25rem;"><?php echo htmlspecialchars(ucfirst($rc['role'])); ?></strong>
                        <p style="color:var(--text-tertiary); margin-top:0.25rem;">
                            <?php if ($rc['role'] === 'admin') echo 'Full administrative credentials, database operations, content moderation.'; ?>
                            <?php if ($rc['role'] === 'instructor') echo 'Create courses, record modules, view student progression, track earnings.'; ?>
                            <?php if ($rc['role'] === 'student') echo 'Register/browse courses, complete lessons, receive certificates.'; ?>
                        </p>
                    </div>
                    <span class="admin-pill" style="font-size:1.125rem; font-weight:bold;"><?php echo $rc['count']; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <div style="margin-top:2rem; text-align:center;">
            <a href="manage-users.php" class="admin-btn admin-btn--outline">Back to users list</a>
        </div>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
