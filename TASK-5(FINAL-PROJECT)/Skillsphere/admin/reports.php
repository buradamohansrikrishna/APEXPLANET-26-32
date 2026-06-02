<?php
require_once '../auth.php';
requireAdmin();

$reportStats = [
    'users_count' => fetchSingleSecure("SELECT COUNT(*) AS c FROM users")['c'],
    'courses_count' => fetchSingleSecure("SELECT COUNT(*) AS c FROM courses")['c'],
    'payments_count' => fetchSingleSecure("SELECT COUNT(*) AS c FROM payments WHERE payment_status = 'success'")['c'],
    'revenue_sum' => fetchSingleSecure("SELECT SUM(amount) AS c FROM payments WHERE payment_status = 'success'")['c']
];

$adminTitle = 'System Reports';
$adminPage = 'reports';
$adminHeading = 'System reports';
$adminSubheading = 'Generate operations summaries';
$adminIllustration = 'assets/images/admin-analytics.svg';
$adminHeroTitle = 'Platform reports';
$adminHeroText = 'Export aggregated database data, user counts, course catalogs, and financial statements.';

include 'includes/head.php';
include 'includes/sidebar.php';
?>
<div class="admin-main">
<?php include 'includes/topbar.php'; ?>

<div class="admin-panel reveal" style="max-width:600px; margin:0 auto;">
    <div class="admin-panel__head"><h3>Operations Reports</h3></div>
    <div class="admin-panel__body">
        <ul style="list-style:none; padding:0; display:flex; flex-direction:column; gap:1rem; margin-bottom:2rem;">
            <li style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-default); padding-bottom:0.5rem;">
                <span>Total Active Users:</span>
                <strong><?php echo $reportStats['users_count']; ?></strong>
            </li>
            <li style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-default); padding-bottom:0.5rem;">
                <span>Published Courses:</span>
                <strong><?php echo $reportStats['courses_count']; ?></strong>
            </li>
            <li style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-default); padding-bottom:0.5rem;">
                <span>Paid Enrollments:</span>
                <strong><?php echo $reportStats['payments_count']; ?></strong>
            </li>
            <li style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-default); padding-bottom:0.5rem;">
                <span>Total Collected Revenue:</span>
                <strong>₹<?php echo number_format($reportStats['revenue_sum'], 2); ?></strong>
            </li>
        </ul>
        <button type="button" class="admin-btn admin-btn--primary" onclick="alert('Exporting system summary data to CSV...');">Export report to CSV</button>
    </div>
</div>

</div>
<?php include 'includes/footer.php'; ?>
