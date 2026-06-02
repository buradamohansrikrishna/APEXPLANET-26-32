<?php
require_once '../auth.php';
requireAdmin();

$payments = fetchAllSecure("
    SELECT p.*, u.full_name AS student_name, c.title AS course_title
    FROM payments p
    JOIN users u ON p.user_id = u.id
    JOIN courses c ON p.course_id = c.id
    ORDER BY p.paid_at DESC
");

$revenueStats = fetchSingleSecure("
    SELECT IFNULL(SUM(amount), 0) AS total_revenue, COUNT(id) AS tx_count
    FROM payments
    WHERE payment_status = 'success'
");

$adminTitle = 'Revenue Operations';
$adminPage = 'revenue';
$adminHeading = 'Revenue operations';
$adminSubheading = 'Track student purchases and transactions';
$adminIllustration = 'assets/images/admin-revenue.svg';
$adminHeroTitle = 'Financial metrics';
$adminHeroText = 'Track system collections, transactions, and discount allocations.';

include 'includes/head.php';
include 'includes/sidebar.php';
?>
<div class="admin-main">
<?php include 'includes/topbar.php'; ?>

<div class="admin-stats reveal">
    <div class="admin-stat">
        <div class="admin-stat__icon admin-stat__icon--revenue"><i class="fa-solid fa-indian-rupee-sign"></i></div>
        <h3>Gross Collections</h3>
        <p class="admin-stat__value">₹<?php echo number_format($revenueStats['total_revenue'], 2); ?></p>
        <span class="admin-stat__meta">All successful payments</span>
    </div>
    <div class="admin-stat">
        <div class="admin-stat__icon"><i class="fa-solid fa-cart-shopping"></i></div>
        <h3>Total Transactions</h3>
        <p class="admin-stat__value"><?php echo $revenueStats['tx_count']; ?></p>
        <span class="admin-stat__meta">Approved licenses</span>
    </div>
</div>

<div class="admin-panel reveal">
    <div class="admin-panel__head"><h3>Transaction History</h3></div>
    <div class="admin-panel__body">
        <div class="admin-table-wrap">
            <?php if (!empty($payments)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Paid At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $p): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($p['transaction_id'] ?? 'MOCK-TX'); ?></code></td>
                                <td><?php echo htmlspecialchars($p['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($p['course_title']); ?></td>
                                <td><strong>₹<?php echo number_format($p['amount'], 2); ?></strong></td>
                                <td><?php echo htmlspecialchars(ucfirst($p['payment_method'] ?? 'UPI')); ?></td>
                                <td><span class="status <?php echo strtolower($p['payment_status']); ?>"><?php echo htmlspecialchars(ucfirst($p['payment_status'])); ?></span></td>
                                <td><?php echo $p['paid_at'] ? date('d M Y, h:i A', strtotime($p['paid_at'])) : '—'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No transactions logged on the system.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>
<?php include 'includes/footer.php'; ?>
