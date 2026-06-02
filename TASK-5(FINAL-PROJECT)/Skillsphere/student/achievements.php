<?php
require_once '../auth.php';
require_once '../middleware.php';
requireRole('student');

$userId = (int)$_SESSION['user_id'];
$lessonsCount = fetchSingleSecure("
    SELECT COUNT(*) AS c FROM lesson_progress WHERE user_id = ? AND completed = 1
", [$userId])['c'] ?? 0;

$badges = [
    [
        'name' => 'Fast Starter',
        'desc' => 'Enrolled in your first technical course',
        'icon' => 'fa-bolt',
        'unlocked' => true
    ],
    [
        'name' => 'Consistent Learner',
        'desc' => 'Completed at least 5 study lectures',
        'icon' => 'fa-calendar-check',
        'unlocked' => $lessonsCount >= 5
    ],
    [
        'name' => 'Code Warrior',
        'desc' => 'Completed at least 15 study lectures',
        'icon' => 'fa-code',
        'unlocked' => $lessonsCount >= 15
    ],
    [
        'name' => 'Certification Graduate',
        'desc' => 'Received at least 1 verified certificate',
        'icon' => 'fa-certificate',
        'unlocked' => (fetchSingleSecure("SELECT COUNT(*) AS c FROM certificates WHERE user_id = ?", [$userId])['c'] ?? 0) >= 1
    ]
];

$pageTitle = 'My Achievements';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <span class="badge badge-primary">Student Portal</span>
            <h1 class="text-gradient">Your Learning Achievements</h1>
        </div>
    </div>

    <!-- Navigation links for portal -->
    <div style="display:flex; gap:1rem; border-bottom:1px solid var(--border-default); padding-bottom:1rem; margin-bottom:2rem;">
        <a href="dashboard.php" class="btn btn-sm btn-outline">Active Courses</a>
        <a href="progress.php" class="btn btn-sm btn-outline">Study Progress</a>
        <a href="certificates.php" class="btn btn-sm btn-outline">Certificates</a>
        <a href="achievements.php" class="btn btn-sm btn-primary">Achievements</a>
        <a href="leaderboard.php" class="btn btn-sm btn-outline">Leaderboard</a>
        <a href="wishlist.php" class="btn btn-sm btn-outline">Wishlist</a>
    </div>

    <div class="grid grid-4">
        <?php foreach ($badges as $badge): ?>
            <div class="card reveal text-center" style="padding: 2rem; opacity: <?php echo $badge['unlocked'] ? '1' : '0.5'; ?>;">
                <div style="font-size:3rem; margin-bottom:1rem; color:<?php echo $badge['unlocked'] ? 'var(--brand-500)' : 'var(--text-muted)'; ?>;">
                    <i class="fa-solid <?php echo $badge['icon']; ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($badge['name']); ?></h3>
                <p style="font-size:0.875rem; margin-top:0.5rem; color:var(--text-tertiary);"><?php echo htmlspecialchars($badge['desc']); ?></p>
                <div style="margin-top:1.5rem;">
                    <?php if ($badge['unlocked']): ?>
                        <span class="badge badge-success">Unlocked</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Locked</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
