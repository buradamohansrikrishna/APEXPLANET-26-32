<?php
require_once '../../auth.php';
requireAdmin();

$students = fetchAllSecure("
    SELECT u.*, COUNT(e.id) AS enrollments_count
    FROM users u
    LEFT JOIN enrollments e ON u.id = e.user_id
    WHERE u.role = 'student'
    GROUP BY u.id
    ORDER BY u.created_at DESC
");

$adminTitle = 'Manage Students';
$adminPage = 'students';
$adminHeading = 'Platform students';
$adminSubheading = 'Overview of registered learners';
$adminIllustration = '../assets/images/admin-users.svg';
$adminHeroTitle = 'Student accounts';
$adminHeroText = 'Track active student profiles, course registrations, lesson progression, and certificates.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<div class="admin-panel reveal">
    <div class="admin-panel__head">
        <h3>Students Directory</h3>
        <span class="admin-pill"><?php echo count($students); ?> learners</span>
    </div>
    <div class="admin-panel__body">
        <div class="admin-table-wrap">
            <?php if (!empty($students)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Enrollments</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $stu): ?>
                            <tr>
                                <td>
                                    <div class="admin-profile-cell">
                                        <img src="../../uploads/profiles/<?php echo htmlspecialchars($stu['profile_image'] ?? 'default.png'); ?>" alt="" onerror="this.src='../../uploads/profiles/default.png'">
                                        <div>
                                            <strong><?php echo htmlspecialchars($stu['full_name']); ?></strong><br>
                                            <small>Joined <?php echo date('M Y', strtotime($stu['created_at'])); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($stu['email']); ?></td>
                                <td><strong><?php echo $stu['enrollments_count']; ?></strong> courses</td>
                                <td><span class="status <?php echo strtolower($stu['status']); ?>"><?php echo htmlspecialchars(ucfirst($stu['status'])); ?></span></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="progress.php?student_id=<?php echo $stu['id']; ?>" class="edit-btn">View Progress</a>
                                        <a href="certificates.php?student_id=<?php echo $stu['id']; ?>" class="edit-btn" style="background:#10b981;">Certificates</a>
                                        <a href="../users/edit-user.php?id=<?php echo $stu['id']; ?>" class="edit-btn">Edit Profile</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No students registered on the platform.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
