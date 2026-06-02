<?php
require_once '../auth.php';
requireAdmin();

$totalUsers = (fetchSingleSecure('SELECT COUNT(*) AS c FROM users')['c'] ?? 0);
$totalCourses = (fetchSingleSecure('SELECT COUNT(*) AS c FROM courses')['c'] ?? 0);
$totalEnrollments = (fetchSingleSecure('SELECT COUNT(*) AS c FROM enrollments')['c'] ?? 0);
$totalRevenue = (fetchSingleSecure("SELECT SUM(amount) AS c FROM payments WHERE payment_status='success'")['c'] ?? 0);
$recentUsers = dbQuery('SELECT * FROM users ORDER BY created_at DESC LIMIT 5');

$adminTitle = 'Analytics';
$adminPage = 'analytics';
$adminHeading = 'Analytics';
$adminSubheading = 'Growth metrics and platform performance';
$adminIllustration = 'assets/images/admin-analytics.svg';
$adminHeroTitle = 'Data-driven decisions';
$adminHeroText = 'Visualize user growth, enrollments, and revenue trends across SkillSphere.';

include 'includes/head.php';
include 'includes/sidebar.php';
?>
<div class="admin-main">
<?php include 'includes/topbar.php'; ?>

<div class="analytics-grid reveal">
    <div class="analytics-card">
        <h3>Total users</h3>
        <h1><?php echo number_format($totalUsers); ?></h1>
    </div>
    <div class="analytics-card">
        <h3>Total courses</h3>
        <h1><?php echo number_format($totalCourses); ?></h1>
    </div>
    <div class="analytics-card">
        <h3>Enrollments</h3>
        <h1><?php echo number_format($totalEnrollments); ?></h1>
    </div>
    <div class="analytics-card">
        <h3>Revenue</h3>
        <h1>₹<?php echo number_format((float) $totalRevenue, 0); ?></h1>
    </div>
</div>

<div class="chart-container reveal">
    <h2 class="chart-title">Platform overview</h2>
    <canvas id="analyticsChart" height="120"></canvas>
</div>

<div class="admin-panel reveal">
    <div class="admin-panel__head"><h3>Recent registrations</h3></div>
    <div class="admin-table-wrap">
        <table>
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Joined</th></tr>
            </thead>
            <tbody>
                <?php if ($recentUsers && mysqli_num_rows($recentUsers) > 0): ?>
                    <?php while ($user = mysqli_fetch_assoc($recentUsers)): ?>
                    <tr>
                        <td><?php echo (int) $user['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($user['full_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><span class="badge <?php echo strtolower($user['role']); ?>"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="empty">No users</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
  const ctx = document.getElementById('analyticsChart');
  if (!ctx) return;
  const styles = getComputedStyle(document.documentElement);
  const text = styles.getPropertyValue('--admin-text-muted').trim() || '#64748b';
  const grid = styles.getPropertyValue('--admin-border').trim() || 'rgba(0,0,0,0.08)';

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Users', 'Courses', 'Enrollments'],
      datasets: [{
        label: 'Count',
        data: [<?php echo (int) $totalUsers; ?>, <?php echo (int) $totalCourses; ?>, <?php echo (int) $totalEnrollments; ?>],
        backgroundColor: ['#0d9488', '#0891b2', '#0284c7'],
        borderRadius: 10,
        borderSkipped: false
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        x: { ticks: { color: text }, grid: { color: grid } },
        y: { ticks: { color: text }, grid: { color: grid }, beginAtZero: true }
      }
    }
  });
})();
</script>
<?php include 'includes/footer.php'; ?>
