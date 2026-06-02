<?php
require_once '../auth.php';
require_once '../middleware.php';
requireRole('instructor');

$instructorId = (int)$_SESSION['user_id'];

// Get aggregate stats
$stats = fetchSingleSecure("
    SELECT COUNT(id) AS courses_count, IFNULL(SUM(total_students), 0) AS total_students
    FROM courses
    WHERE instructor_id = ?
", [$instructorId]);

$pageTitle = 'Analytics Dashboard - Instructor Portal';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <!-- Navigation links for portal -->
    <div style="display:flex; gap:1rem; border-bottom:1px solid var(--border-default); padding-bottom:1rem; margin-bottom:2rem;">
        <a href="dashboard.php" class="btn btn-sm btn-outline">Courses List</a>
        <a href="students.php" class="btn btn-sm btn-outline">My Students</a>
        <a href="analytics.php" class="btn btn-sm btn-primary">Analytics</a>
        <a href="earnings.php" class="btn btn-sm btn-outline">Earnings Report</a>
        <a href="messages.php" class="btn btn-sm btn-outline">QA Messages</a>
        <a href="reviews.php" class="btn btn-sm btn-outline">Course Reviews</a>
    </div>

    <div class="grid grid-2" style="gap:2rem;">
        <div class="card reveal" style="padding:2rem;">
            <h3>Enrolled Learners Growth</h3>
            <canvas id="growthChart" style="margin-top:1.5rem;"></canvas>
        </div>
        <div class="card reveal stagger-1" style="padding:2rem;">
            <h3>Analytics Summary</h3>
            <ul style="list-style:none; padding:0; display:flex; flex-direction:column; gap:1rem; margin-top:1.5rem;">
                <li style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-default); padding-bottom:0.5rem;">
                    <span>Courses Built:</span>
                    <strong><?php echo (int)$stats['courses_count']; ?></strong>
                </li>
                <li style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-default); padding-bottom:0.5rem;">
                    <span>Direct Students:</span>
                    <strong><?php echo number_format($stats['total_students']); ?></strong>
                </li>
            </ul>
        </div>
    </div>
</div>

<script src="../assets/js/charts.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Generate dummy line chart data for student growth
    const growthData = [12, 28, 45, 68, 90, 118, <?php echo (int)$stats['total_students']; ?>];
    const growthLabels = ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May'];
    DashboardChart.drawLineChart('growthChart', growthData, growthLabels, '#6366f1');
});
</script>
<?php include '../includes/footer.php'; ?>
