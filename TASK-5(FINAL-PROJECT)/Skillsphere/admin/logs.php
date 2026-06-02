<?php
require_once '../auth.php';
requireAdmin();

$logItems = [
    ['time' => date('Y-m-d H:i:s', time() - 300), 'ip' => '127.0.0.1', 'user' => 'admin@skillsphere.com', 'action' => 'Admin session authenticated', 'severity' => 'info'],
    ['time' => date('Y-m-d H:i:s', time() - 1200), 'ip' => '127.0.0.1', 'user' => 'venkat.s@skillsphere.com', 'action' => 'Draft course submitted: Docker & Kubernetes', 'severity' => 'info'],
    ['time' => date('Y-m-d H:i:s', time() - 3600), 'ip' => '192.168.1.55', 'user' => 'ravi.bhupathi@example.com', 'action' => 'Completed quiz: ES6 Arrays', 'severity' => 'info'],
    ['time' => date('Y-m-d H:i:s', time() - 7200), 'ip' => '127.0.0.1', 'user' => 'system', 'action' => 'Automatic certificate issued for code: CERT-889', 'severity' => 'info']
];

$adminTitle = 'System Logs';
$adminPage = 'logs';
$adminHeading = 'System activity logs';
$adminSubheading = 'Track platform actions and debug events';
$adminIllustration = 'assets/images/admin-dashboard.svg';
$adminHeroTitle = 'System auditing';
$adminHeroText = 'Review historical log entries, user activities, error traces, and configuration audits.';

include 'includes/head.php';
include 'includes/sidebar.php';
?>
<div class="admin-main">
<?php include 'includes/topbar.php'; ?>

<div class="admin-panel reveal">
    <div class="admin-panel__head"><h3>Audit Trails</h3></div>
    <div class="admin-panel__body">
        <div class="admin-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>IP Address</th>
                        <th>User Account</th>
                        <th>Action Performed</th>
                        <th>Severity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logItems as $log): ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($log['time']); ?></code></td>
                            <td><code><?php echo htmlspecialchars($log['ip']); ?></code></td>
                            <td><?php echo htmlspecialchars($log['user']); ?></td>
                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                            <td><span class="badge badge-success"><?php echo htmlspecialchars(ucfirst($log['severity'])); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
<?php include 'includes/footer.php'; ?>
