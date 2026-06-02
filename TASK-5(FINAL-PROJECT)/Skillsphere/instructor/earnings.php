<?php
require_once '../auth.php';
require_once '../middleware.php';
requireRole('instructor');

$instructorId = (int)$_SESSION['user_id'];

// Fetch payments log
$earnings = fetchAllSecure("
    SELECT p.*, u.full_name AS student_name, c.title AS course_title
    FROM payments p
    JOIN courses c ON p.course_id = c.id
    JOIN users u ON p.user_id = u.id
    WHERE c.instructor_id = ? AND p.payment_status = 'success'
    ORDER BY p.paid_at DESC
", [$instructorId]);

$revenueShare = 0.70; // 70% share

$totals = fetchSingleSecure("
    SELECT IFNULL(SUM(p.amount), 0) AS total_gross
    FROM payments p
    JOIN courses c ON p.course_id = c.id
    WHERE c.instructor_id = ? AND p.payment_status = 'success'
", [$instructorId]);

$gross = (float)$totals['total_gross'];
$netEarnings = $gross * $revenueShare;

$pageTitle = 'Earnings Report - Instructor Portal';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <!-- Navigation links for portal -->
    <div style="display:flex; gap:1rem; border-bottom:1px solid var(--border-default); padding-bottom:1rem; margin-bottom:2rem;">
        <a href="dashboard.php" class="btn btn-sm btn-outline">Courses List</a>
        <a href="students.php" class="btn btn-sm btn-outline">My Students</a>
        <a href="analytics.php" class="btn btn-sm btn-outline">Analytics</a>
        <a href="earnings.php" class="btn btn-sm btn-primary">Earnings Report</a>
        <a href="messages.php" class="btn btn-sm btn-outline">QA Messages</a>
        <a href="reviews.php" class="btn btn-sm btn-outline">Course Reviews</a>
    </div>

    <!-- Summary -->
    <div class="dashboard-cards grid-2" style="margin-bottom: 2rem;">
        <div class="stat-card dashboard-card reveal">
            <p class="stat-card__label">Gross Platform Collections</p>
            <p class="stat-card__value">₹<?php echo number_format($gross, 2); ?></p>
        </div>
        <div class="stat-card dashboard-card reveal stagger-1">
            <p class="stat-card__label">Net Earnings Share (70%)</p>
            <p class="stat-card__value" style="color:var(--success);">₹<?php echo number_format($netEarnings, 2); ?></p>
        </div>
    </div>

    <!-- Table -->
    <h2 style="margin-bottom: 1.5rem;">Recent Course Sales</h2>
    <div class="card reveal" style="padding: 2rem;">
        <div class="admin-table-wrap">
            <?php if (!empty($earnings)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Amount Collected</th>
                            <th>Your Earnings (70%)</th>
                            <th>Paid At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($earnings as $e): 
                            $val = (float)$e['amount'];
                            $share = $val * $revenueShare;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($e['student_name']); ?></td>
                                <td><strong><?php echo htmlspecialchars($e['course_title']); ?></strong></td>
                                <td>₹<?php echo number_format($val, 2); ?></td>
                                <td style="color:var(--success); font-weight:bold;">₹<?php echo number_format($share, 2); ?></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($e['paid_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No sales transactions have been logged.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
