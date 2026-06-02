<?php
require_once '../../auth.php';
requireAdmin();

$instructors = fetchAllSecure("
    SELECT u.*, COUNT(c.id) AS courses_count, IFNULL(SUM(c.total_students), 0) AS total_students
    FROM users u
    LEFT JOIN courses c ON u.id = c.instructor_id
    WHERE u.role = 'instructor'
    GROUP BY u.id
    ORDER BY u.created_at DESC
");

$adminTitle = 'Manage Instructors';
$adminPage = 'instructors';
$adminHeading = 'Platform instructors';
$adminSubheading = 'Overview of professional mentors';
$adminIllustration = '../assets/images/admin-users.svg';
$adminHeroTitle = 'Instructor accounts';
$adminHeroText = 'Oversee instructors, published courses, cumulative student bases, and system permissions.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<div class="admin-panel reveal">
    <div class="admin-panel__head">
        <h3>Instructors Directory</h3>
        <span class="admin-pill"><?php echo count($instructors); ?> mentors</span>
    </div>
    <div class="admin-panel__body">
        <div class="admin-table-wrap">
            <?php if (!empty($instructors)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Instructor</th>
                            <th>Email</th>
                            <th>Courses</th>
                            <th>Total Students</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($instructors as $inst): ?>
                            <tr>
                                <td>
                                    <div class="admin-profile-cell">
                                        <img src="../../uploads/profiles/<?php echo htmlspecialchars($inst['profile_image'] ?? 'default.png'); ?>" alt="" onerror="this.src='../../uploads/profiles/default.png'">
                                        <div>
                                            <strong><?php echo htmlspecialchars($inst['full_name']); ?></strong><br>
                                            <small>Joined <?php echo date('M Y', strtotime($inst['created_at'])); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($inst['email']); ?></td>
                                <td><strong><?php echo $inst['courses_count']; ?></strong> published</td>
                                <td><strong><?php echo number_format($inst['total_students']); ?></strong> learners</td>
                                <td><span class="status <?php echo strtolower($inst['status']); ?>"><?php echo htmlspecialchars(ucfirst($inst['status'])); ?></span></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="../users/edit-user.php?id=<?php echo $inst['id']; ?>" class="edit-btn">Edit Profile</a>
                                        <a href="payouts.php?instructor_id=<?php echo $inst['id']; ?>" class="edit-btn" style="background:#10b981;">Payouts</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No instructors registered on the platform.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
