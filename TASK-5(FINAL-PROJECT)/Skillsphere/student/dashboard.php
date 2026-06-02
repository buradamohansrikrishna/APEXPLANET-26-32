<?php
require_once '../auth.php';
require_once '../middleware.php';
requireRole('student');

// Fetch student progress stats
$userId = (int)$_SESSION['user_id'];

$enrollments = fetchAllSecure("
    SELECT e.*, c.title AS course_title, c.thumbnail, cat.category_name
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    LEFT JOIN categories cat ON c.category_id = cat.id
    WHERE e.user_id = ?
", [$userId]);

$enrolledCount = count($enrollments);

$completedCount = fetchSingleSecure("
    SELECT COUNT(*) AS c FROM enrollments WHERE user_id = ? AND completed_at IS NOT NULL
", [$userId])['c'] ?? 0;

$certificatesCount = fetchSingleSecure("
    SELECT COUNT(*) AS c FROM certificates WHERE user_id = ?
", [$userId])['c'] ?? 0;

$pageTitle = 'Student Dashboard';
$studentPage = 'dashboard';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <span class="badge badge-primary">Student Portal</span>
            <h1 class="text-gradient">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        </div>
        <a href="../courses.php" class="btn btn-primary">Find a new course</a>
    </div>

    <!-- Student Stats -->
    <div class="dashboard-cards grid-3" style="margin-bottom: 2rem;">
        <div class="stat-card dashboard-card reveal">
            <p class="stat-card__label">Enrolled Courses</p>
            <p class="stat-card__value"><?php echo $enrolledCount; ?></p>
        </div>
        <div class="stat-card dashboard-card reveal stagger-1">
            <p class="stat-card__label">Completed Courses</p>
            <p class="stat-card__value"><?php echo $completedCount; ?></p>
        </div>
        <div class="stat-card dashboard-card reveal stagger-2">
            <p class="stat-card__label">Verifiable Certificates</p>
            <p class="stat-card__value"><?php echo $certificatesCount; ?></p>
        </div>
    </div>

    <!-- Navigation links for portal -->
    <div style="display:flex; gap:1rem; border-bottom:1px solid var(--border-default); padding-bottom:1rem; margin-bottom:2rem;">
        <a href="dashboard.php" class="btn btn-sm btn-primary">Active Courses</a>
        <a href="progress.php" class="btn btn-sm btn-outline">Study Progress</a>
        <a href="certificates.php" class="btn btn-sm btn-outline">Certificates</a>
        <a href="achievements.php" class="btn btn-sm btn-outline">Achievements</a>
        <a href="leaderboard.php" class="btn btn-sm btn-outline">Leaderboard</a>
        <a href="wishlist.php" class="btn btn-sm btn-outline">Wishlist</a>
    </div>

    <!-- Active Courses Grid -->
    <h2 style="margin-bottom: 1.5rem;">Your Active Courses</h2>
    <div class="course-grid">
        <?php if (!empty($enrollments)): ?>
            <?php foreach ($enrollments as $e): 
                $course_id = $e['course_id'];
                // Get progress percent
                $totalLessons = fetchSingleSecure("SELECT COUNT(*) AS c FROM lessons WHERE course_id = ?", [$course_id])['c'] ?? 0;
                $completedLessons = fetchSingleSecure("SELECT COUNT(*) AS c FROM lesson_progress lp JOIN lessons l ON lp.lesson_id = l.id WHERE lp.user_id = ? AND l.course_id = ? AND lp.completed = 1", [$userId, $course_id])['c'] ?? 0;
                $pct = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
                ?>
                <article class="course-card reveal">
                    <div class="course-card__media">
                        <?php 
                        $thumbPath = !empty($e['thumbnail']) ? '../uploads/thumbnails/' . htmlspecialchars($e['thumbnail']) : '';
                        if ($thumbPath): ?>
                            <img src="<?php echo $thumbPath; ?>" alt="" onerror="this.style.display='none';">
                        <?php endif; ?>
                        <div class="course-card__thumb course-thumb-art"></div>
                    </div>
                    <div class="course-card__body">
                        <span class="course-card__category"><?php echo htmlspecialchars($e['category_name'] ?? 'Web Development'); ?></span>
                        <h3 class="course-card__title">
                            <a href="../my-progress.php?course_id=<?php echo $course_id; ?>">
                                <?php echo htmlspecialchars($e['course_title']); ?>
                            </a>
                        </h3>
                        <div style="margin-top: 1rem; margin-bottom: 1rem;">
                            <div style="display:flex; justify-content:space-between; font-size:0.875rem; margin-bottom:0.25rem;">
                                <span>Progress</span>
                                <span><?php echo $pct; ?>%</span>
                            </div>
                            <div style="width:100%; height:6px; background:var(--bg-muted); border-radius:3px; overflow:hidden;">
                                <div style="width:<?php echo $pct; ?>%; height:100%; background:var(--brand-500);"></div>
                            </div>
                        </div>
                        <a href="../my-progress.php?course_id=<?php echo $course_id; ?>" class="btn btn-sm btn-primary" style="width:100%; text-align:center;">Resume Course</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card text-center" style="grid-column: 1 / -1; padding: 3rem;">
                <h3>No course registrations yet</h3>
                <p style="margin-top: 1rem; margin-bottom:1.5rem;">Explore our catalog to find your first technical course.</p>
                <a href="../courses.php" class="btn btn-primary">Browse Courses</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
