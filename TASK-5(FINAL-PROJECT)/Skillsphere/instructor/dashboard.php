<?php
require_once '../auth.php';
require_once '../middleware.php';
requireRole('instructor');

$instructorId = (int)$_SESSION['user_id'];

// Get instructor stats
$courses = fetchAllSecure("
    SELECT c.*, cat.category_name
    FROM courses c
    LEFT JOIN categories cat ON c.category_id = cat.id
    WHERE c.instructor_id = ?
    ORDER BY c.created_at DESC
", [$instructorId]);

$coursesCount = count($courses);

$totalStudents = fetchSingleSecure("
    SELECT IFNULL(SUM(total_students), 0) AS c FROM courses WHERE instructor_id = ?
", [$instructorId])['c'] ?? 0;

$earningsShare = fetchSingleSecure("
    SELECT IFNULL(SUM(p.amount) * 0.70, 0) AS c
    FROM payments p
    JOIN courses c ON p.course_id = c.id
    WHERE c.instructor_id = ? AND p.payment_status = 'success'
", [$instructorId])['c'] ?? 0;

$pageTitle = 'Instructor Dashboard';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <span class="badge badge-primary">Instructor Portal</span>
            <h1 class="text-gradient">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        </div>
        <a href="courses.php?action=add" class="btn btn-primary">Create New Course</a>
    </div>

    <!-- Stats -->
    <div class="dashboard-cards grid-3" style="margin-bottom: 2rem;">
        <div class="stat-card dashboard-card reveal">
            <p class="stat-card__label">Active Courses</p>
            <p class="stat-card__value"><?php echo $coursesCount; ?></p>
        </div>
        <div class="stat-card dashboard-card reveal stagger-1">
            <p class="stat-card__label">Total Enrolled Learners</p>
            <p class="stat-card__value"><?php echo number_format($totalStudents); ?></p>
        </div>
        <div class="stat-card dashboard-card reveal stagger-2">
            <p class="stat-card__label">Cumulative Earnings (70%)</p>
            <p class="stat-card__value">₹<?php echo number_format($earningsShare, 0); ?></p>
        </div>
    </div>

    <!-- Nav menu -->
    <div style="display:flex; gap:1rem; border-bottom:1px solid var(--border-default); padding-bottom:1rem; margin-bottom:2rem;">
        <a href="dashboard.php" class="btn btn-sm btn-primary">Courses List</a>
        <a href="students.php" class="btn btn-sm btn-outline">My Students</a>
        <a href="analytics.php" class="btn btn-sm btn-outline">Analytics</a>
        <a href="earnings.php" class="btn btn-sm btn-outline">Earnings Report</a>
        <a href="messages.php" class="btn btn-sm btn-outline">QA Messages</a>
        <a href="reviews.php" class="btn btn-sm btn-outline">Course Reviews</a>
    </div>

    <!-- Courses Table -->
    <h2 style="margin-bottom: 1.5rem;">Your Published Courses</h2>
    <div class="card reveal" style="padding: 2rem;">
        <div class="admin-table-wrap">
            <?php if (!empty($courses)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Level</th>
                            <th>Price</th>
                            <th>Students Enrolled</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $c): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($c['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($c['category_name'] ?? 'General'); ?></td>
                                <td><span class="badge <?php echo strtolower($c['level']); ?>"><?php echo htmlspecialchars(ucfirst($c['level'])); ?></span></td>
                                <td>₹<?php echo number_format($c['price'], 0); ?></td>
                                <td><strong><?php echo number_format($c['total_students']); ?></strong> students</td>
                                <td><span class="status <?php echo strtolower($c['status']); ?>"><?php echo htmlspecialchars(ucfirst($c['status'])); ?></span></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="courses.php?action=edit&id=<?php echo $c['id']; ?>" class="edit-btn">Edit Syllabus</a>
                                        <a href="../course-details.php?id=<?php echo $c['id']; ?>" target="_blank" class="edit-btn">Preview view</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center" style="padding: 2rem;">
                    <p>You haven't built any courses yet. Share your expertise today!</p>
                    <a href="courses.php?action=add" class="btn btn-primary" style="margin-top:1rem;">Add your first course</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
