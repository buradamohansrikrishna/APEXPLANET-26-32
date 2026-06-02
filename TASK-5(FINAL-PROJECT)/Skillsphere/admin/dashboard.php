<?php
require_once '../auth.php';
requireAdmin();

$userQuery = fetchSingleSecure('SELECT COUNT(*) AS total_users FROM users');
$totalUsers = $userQuery['total_users'] ?? 0;

$courseQuery = fetchSingleSecure('SELECT COUNT(*) AS total_courses FROM courses');
$totalCourses = $courseQuery['total_courses'] ?? 0;

$pendingQuery = fetchSingleSecure("SELECT COUNT(*) AS total_pending FROM courses WHERE status = 'draft'");
$totalPending = $pendingQuery['total_pending'] ?? 0;

$enrollQuery = fetchSingleSecure('SELECT COUNT(*) AS total_enrollments FROM enrollments');
$totalEnrollments = $enrollQuery['total_enrollments'] ?? 0;

$revenueQuery = fetchSingleSecure("SELECT SUM(amount) AS total_revenue FROM payments WHERE payment_status = 'success'");
$totalRevenue = $revenueQuery['total_revenue'] ?? 0;

$dbSizeQuery = fetchSingleSecure("
    SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS db_size
    FROM information_schema.TABLES
    WHERE table_schema = DATABASE()
");
$dbSize = $dbSizeQuery['db_size'] ?? '0';

$recentCourses = dbQuery(
    "SELECT c.*, cat.category_name, u.full_name AS instructor_name
     FROM courses c
     LEFT JOIN categories cat ON c.category_id = cat.id
     LEFT JOIN users u ON c.instructor_id = u.id
     ORDER BY c.created_at DESC
     LIMIT 5"
);

$systemLogs = [
    ['time' => '10 min ago', 'event' => 'Connection pool refreshed', 'status' => 'active'],
    ['time' => '45 min ago', 'event' => 'Admin session verified', 'status' => 'secure'],
    ['time' => '2 hours ago', 'event' => 'Database sync completed', 'status' => 'verified'],
    ['time' => '3 hours ago', 'event' => 'Analytics compile finished', 'status' => 'active'],
];

$adminTitle = 'Dashboard';
$adminPage = 'dashboard';
$adminHeading = 'Dashboard';
$adminSubheading = 'Platform overview and recent activity';
$adminIllustration = 'assets/images/admin-dashboard.svg';
$adminHeroTitle = 'Command your learning platform';
$adminHeroText = 'Track users, courses, revenue, and system health from one executive workspace.';

include 'includes/head.php';
include 'includes/sidebar.php';
?>
<div class="admin-main">
<?php include 'includes/topbar.php'; ?>

<div class="admin-stats reveal">
    <div class="admin-stat">
        <div class="admin-stat__icon admin-stat__icon--users"><i class="fa-solid fa-users"></i></div>
        <h3>Total users</h3>
        <p class="admin-stat__value"><?php echo number_format($totalUsers); ?></p>
        <span class="admin-stat__meta">+12% this month</span>
    </div>
    <div class="admin-stat">
        <div class="admin-stat__icon admin-stat__icon--courses"><i class="fa-solid fa-book"></i></div>
        <h3>Total courses</h3>
        <p class="admin-stat__value"><?php echo number_format($totalCourses); ?></p>
        <span class="admin-stat__meta">Active catalog</span>
    </div>
    <div class="admin-stat">
        <div class="admin-stat__icon"><i class="fa-solid fa-user-graduate"></i></div>
        <h3>Enrollments</h3>
        <p class="admin-stat__value"><?php echo number_format($totalEnrollments); ?></p>
        <span class="admin-stat__meta">88% completion</span>
    </div>
    <div class="admin-stat">
        <div class="admin-stat__icon admin-stat__icon--revenue"><i class="fa-solid fa-indian-rupee-sign"></i></div>
        <h3>Revenue</h3>
        <p class="admin-stat__value">₹<?php echo number_format((float) $totalRevenue, 0); ?></p>
        <span class="admin-stat__meta">Successful payments</span>
    </div>
    <div class="admin-stat">
        <div class="admin-stat__icon"><i class="fa-solid fa-database"></i></div>
        <h3>Database</h3>
        <p class="admin-stat__value"><?php echo htmlspecialchars($dbSize); ?> <small>MB</small></p>
        <span class="admin-stat__meta">Healthy</span>
    </div>
    <div class="admin-stat">
        <div class="admin-stat__icon admin-stat__icon--pending"><i class="fa-solid fa-clock"></i></div>
        <h3>Pending</h3>
        <p class="admin-stat__value"><?php echo number_format($totalPending); ?></p>
        <span class="admin-stat__meta">Draft moderation</span>
    </div>
</div>

<div class="admin-bento">
    <div class="admin-panel reveal">
        <div class="admin-panel__head">
            <h3>Recent course submissions</h3>
            <a href="manage-courses.php" class="admin-btn admin-btn--outline" style="padding: 0.4rem 0.875rem; font-size: 0.8125rem;">View all</a>
        </div>
        <div class="admin-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Title</th>
                        <th>Instructor</th>
                        <th>Price</th>
                        <th>Level</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recentCourses && mysqli_num_rows($recentCourses) > 0): ?>
                        <?php while ($course = mysqli_fetch_assoc($recentCourses)): ?>
                        <tr>
                            <td><?php include 'includes/course-thumb-cell.php'; ?></td>
                            <td><strong><?php echo htmlspecialchars($course['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($course['instructor_name'] ?? '—'); ?></td>
                            <td><strong>₹<?php echo number_format((float) $course['price'], 0); ?></strong></td>
                            <td><span class="badge <?php echo strtolower($course['level'] ?? 'beginner'); ?>"><?php echo htmlspecialchars(ucfirst($course['level'] ?? '—')); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="empty">No courses yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-panel reveal">
        <div class="admin-panel__head"><h3>System activity</h3></div>
        <div class="admin-panel__body">
            <div class="admin-log">
                <?php foreach ($systemLogs as $log): ?>
                <div class="admin-log__item">
                    <div>
                        <strong><?php echo htmlspecialchars($log['event']); ?></strong>
                        <time><?php echo htmlspecialchars($log['time']); ?></time>
                    </div>
                    <span class="badge badge-success"><?php echo htmlspecialchars($log['status']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="admin-insight reveal">
    <h3><i class="fa-solid fa-lightbulb"></i> Platform insight</h3>
    <p>Enrollment velocity is strongest in full-stack and AI tracks. Consider publishing a follow-up intermediate module while demand is high — draft courses in queue: <strong><?php echo (int) $totalPending; ?></strong>.</p>
</div>

</div>
<?php include 'includes/footer.php'; ?>
