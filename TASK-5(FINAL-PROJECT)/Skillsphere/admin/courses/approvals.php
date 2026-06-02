<?php
require_once '../../auth.php';
requireAdmin();

if (isset($_GET['approve'])) {
    $courseId = (int)$_GET['approve'];
    $res = dbQuery("UPDATE courses SET status = 'published' WHERE id = ?", [$courseId]);
    if ($res) {
        $_SESSION['success'] = "Course approved and published!";
    } else {
        $_SESSION['error'] = "Failed to approve course";
    }
    header("Location: approvals.php");
    exit();
}

$draftCourses = fetchAllSecure(
    "SELECT c.*, cat.category_name, u.full_name AS instructor_name
     FROM courses c
     LEFT JOIN categories cat ON c.category_id = cat.id
     LEFT JOIN users u ON c.instructor_id = u.id
     WHERE c.status = 'draft'
     ORDER BY c.created_at DESC"
);

$adminTitle = 'Course Approvals';
$adminPage = 'approvals';
$adminHeading = 'Course approvals';
$adminSubheading = 'Review drafts submitted by instructors';
$adminIllustration = '../assets/images/admin-courses.svg';
$adminHeroTitle = 'Content moderation';
$adminHeroText = 'Approve new course submissions and updates before publishing them to learners.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<div class="admin-panel reveal">
    <div class="admin-panel__head">
        <h3>Pending Submissions</h3>
        <span class="admin-pill"><?php echo count($draftCourses); ?> pending</span>
    </div>
    <div class="admin-panel__body">
        <div class="admin-table-wrap">
            <?php if (!empty($draftCourses)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Title</th>
                            <th>Instructor</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Level</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($draftCourses as $dc): ?>
                            <tr>
                                <td><?php $course = $dc; $thumbPrefix = '../../'; include '../includes/course-thumb.php'; ?></td>
                                <td><strong><?php echo htmlspecialchars($dc['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($dc['instructor_name'] ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars($dc['category_name'] ?? '—'); ?></td>
                                <td>₹<?php echo number_format((float) $dc['price'], 0); ?></td>
                                <td><span class="badge <?php echo strtolower($dc['level']); ?>"><?php echo htmlspecialchars(ucfirst($dc['level'])); ?></span></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="approvals.php?approve=<?php echo $dc['id']; ?>" class="edit-btn" style="background:#10b981;">Approve</a>
                                        <a href="edit-course.php?id=<?php echo $dc['id']; ?>" class="edit-btn">Review details</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No courses are pending approval at this time.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
