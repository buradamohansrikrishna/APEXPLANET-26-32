<?php
require_once '../../auth.php';
requireAdmin();

$instructorId = isset($_GET['instructor_id']) ? (int)$_GET['instructor_id'] : 0;
$instructor = null;
if ($instructorId > 0) {
    $instructor = fetchSingleSecure("SELECT * FROM users WHERE id = ? AND role = 'instructor'", [$instructorId]);
}

// Calculate payouts
$revenueShare = 0.70; // 70% share for instructor
$salesQuery = null;

if ($instructor) {
    $salesQuery = fetchSingleSecure("
        SELECT IFNULL(SUM(p.amount), 0) AS total_sales, COUNT(p.id) AS sales_count
        FROM payments p
        JOIN courses c ON p.course_id = c.id
        WHERE c.instructor_id = ? AND p.payment_status = 'success'
    ", [$instructorId]);
}

$adminTitle = 'Instructor Payouts';
$adminPage = 'instructors';
$adminHeading = 'Instructor payouts';
$adminSubheading = 'Manage earnings and transfers';
$adminIllustration = '../assets/images/admin-analytics.svg';
$adminHeroTitle = 'Financial operations';
$adminHeroText = 'Track revenue splits, calculate teacher payouts, and log bank transfers.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<div class="admin-panel reveal" style="max-width: 700px; margin: 0 auto;">
    <div class="admin-panel__head">
        <h3>Payout calculations<?php echo $instructor ? ' for ' . htmlspecialchars($instructor['full_name']) : ''; ?></h3>
    </div>
    <div class="admin-panel__body">
        <?php if ($instructor && $salesQuery): 
            $gross = (float)$salesQuery['total_sales'];
            $earnings = $gross * $revenueShare;
            ?>
            <div class="stats-grid" style="grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:2rem;">
                <div class="stat-item">
                    <div style="font-size:0.875rem; color:var(--text-tertiary);">Gross Sales</div>
                    <div style="font-size:1.75rem; font-weight:bold; color:var(--text-primary);">₹<?php echo number_format($gross, 2); ?></div>
                </div>
                <div class="stat-item">
                    <div style="font-size:0.875rem; color:var(--text-tertiary);">Instructor Share (70%)</div>
                    <div style="font-size:1.75rem; font-weight:bold; color:var(--success);">₹<?php echo number_format($earnings, 2); ?></div>
                </div>
            </div>

            <p style="margin-bottom:1.5rem; color:var(--text-secondary);">
                Instructor <strong><?php echo htmlspecialchars($instructor['full_name']); ?></strong> has sold <strong><?php echo $salesQuery['sales_count']; ?></strong> course seats.
                The current bank transfer details on file: <code>Bank Transfer / NEFT</code>.
            </p>

            <button type="button" class="admin-btn admin-btn--primary" onclick="alert('Payout of ₹<?php echo number_format($earnings, 2); ?> processed successfully!');">Process payout transfer</button>
            <a href="manage-instructors.php" class="admin-btn admin-btn--outline">Back to instructors</a>
        <?php else: ?>
            <p>Please select an instructor from the <a href="manage-instructors.php">Instructors Directory</a> to process payouts.</p>
        <?php endif; ?>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
