<?php
require_once '../../auth.php';
requireAdmin();

if (isset($_GET['approve'])) {
    $userId = (int)$_GET['approve'];
    $res = dbQuery("UPDATE users SET status = 'active' WHERE id = ? AND role = 'instructor'", [$userId]);
    if ($res) {
        $_SESSION['success'] = "Instructor account approved successfully!";
    } else {
        $_SESSION['error'] = "Failed to approve instructor account";
    }
    header("Location: approvals.php");
    exit();
}

$pendingInstructors = fetchAllSecure(
    "SELECT * FROM users WHERE role = 'instructor' AND status = 'pending' ORDER BY created_at DESC"
);

$adminTitle = 'Instructor Approvals';
$adminPage = 'instructors';
$adminHeading = 'Pending mentors';
$adminSubheading = 'Review instructor applications';
$adminIllustration = '../assets/images/admin-users.svg';
$adminHeroTitle = 'Onboarding';
$adminHeroText = 'Approve pending applications from teaching candidates before granting catalog access.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<div class="admin-panel reveal">
    <div class="admin-panel__head">
        <h3>Pending Applications</h3>
        <span class="admin-pill"><?php echo count($pendingInstructors); ?> pending</span>
    </div>
    <div class="admin-panel__body">
        <div class="admin-table-wrap">
            <?php if (!empty($pendingInstructors)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Email</th>
                            <th>Applied At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingInstructors as $pi): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($pi['full_name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($pi['bio'] ?? 'No bio provided'); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($pi['email']); ?></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($pi['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="approvals.php?approve=<?php echo $pi['id']; ?>" class="edit-btn" style="background:#10b981;">Approve Application</a>
                                        <a href="../users/edit-user.php?id=<?php echo $pi['id']; ?>" class="edit-btn">Review details</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No instructor applications are pending review.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
