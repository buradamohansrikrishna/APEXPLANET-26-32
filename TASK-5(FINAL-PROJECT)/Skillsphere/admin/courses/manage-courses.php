<?php
require_once '../../auth.php';
requireAdmin();

$courseQuery = dbQuery(
    "SELECT c.*, cat.category_name, u.full_name AS instructor_name
     FROM courses c
     LEFT JOIN categories cat ON c.category_id = cat.id
     LEFT JOIN users u ON c.instructor_id = u.id
     ORDER BY c.created_at DESC"
);

$adminTitle = 'Manage Courses';
$adminPage = 'courses';
$adminHeading = 'Manage courses';
$adminSubheading = 'Edit, review, and publish learning programs';
$adminIllustration = '../assets/images/admin-courses.svg';
$adminHeroTitle = 'Content operations';
$adminHeroText = 'Oversee the full course catalog, instructors, and pricing from one table.';
$adminTopbarActions = '<a href="add-course.php" class="admin-btn admin-btn--primary"><i class="fa-solid fa-plus"></i> Add course</a>';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="admin-panel reveal">
    <div class="admin-panel__head">
        <h3>All courses</h3>
        <span class="admin-pill"><?php echo $courseQuery ? mysqli_num_rows($courseQuery) : 0; ?> total</span>
    </div>
    <div class="admin-table-wrap">
        <?php if ($courseQuery && mysqli_num_rows($courseQuery) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Preview</th>
                    <th>Title</th>
                    <th>Instructor</th>
                    <th>Category</th>
                    <th>Level</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($course = mysqli_fetch_assoc($courseQuery)): ?>
                <tr>
                    <td><?php echo (int) $course['id']; ?></td>
                    <td>
                        <?php $thumbPrefix = '../../'; include '../includes/course-thumb.php'; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($course['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($course['instructor_name'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($course['category_name'] ?? '—'); ?></td>
                    <td><span class="badge <?php echo strtolower($course['level'] ?? 'beginner'); ?>"><?php echo htmlspecialchars(ucfirst($course['level'])); ?></span></td>
                    <td><strong>₹<?php echo number_format((float) $course['price'], 0); ?></strong></td>
                    <td><?php echo htmlspecialchars($course['duration']); ?></td>
                    <td><?php echo date('d M Y', strtotime($course['created_at'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="edit-course.php?id=<?php echo (int) $course['id']; ?>" class="edit-btn">Edit</a>
                            <a href="delete-course.php?id=<?php echo (int) $course['id']; ?>" class="delete-btn" onclick="return confirm('Delete this course permanently?');">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty">No courses found. <a href="add-course.php">Add your first course</a>.</div>
        <?php endif; ?>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
