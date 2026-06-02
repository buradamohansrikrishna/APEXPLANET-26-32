<?php
require_once '../auth.php';
require_once '../middleware.php';
requireRole('instructor');

$instructorId = (int)$_SESSION['user_id'];

// Get students enrolled in courses taught by this instructor
$students = fetchAllSecure("
    SELECT DISTINCT u.id, u.full_name, u.email, u.profile_image, e.enrolled_at, c.title AS course_title
    FROM enrollments e
    JOIN users u ON e.user_id = u.id
    JOIN courses c ON e.course_id = c.id
    WHERE c.instructor_id = ?
    ORDER BY e.enrolled_at DESC
", [$instructorId]);

$pageTitle = 'My Students - Instructor Portal';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <!-- Navigation links for portal -->
    <div style="display:flex; gap:1rem; border-bottom:1px solid var(--border-default); padding-bottom:1rem; margin-bottom:2rem;">
        <a href="dashboard.php" class="btn btn-sm btn-outline">Courses List</a>
        <a href="students.php" class="btn btn-sm btn-primary">My Students</a>
        <a href="analytics.php" class="btn btn-sm btn-outline">Analytics</a>
        <a href="earnings.php" class="btn btn-sm btn-outline">Earnings Report</a>
        <a href="messages.php" class="btn btn-sm btn-outline">QA Messages</a>
        <a href="reviews.php" class="btn btn-sm btn-outline">Course Reviews</a>
    </div>

    <h2 style="margin-bottom: 1.5rem;">Enrolled Learners</h2>
    <div class="card reveal" style="padding: 2rem;">
        <div class="admin-table-wrap">
            <?php if (!empty($students)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Course Registered</th>
                            <th>Enrolled On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td>
                                    <div class="admin-profile-cell">
                                        <img src="../uploads/profiles/<?php echo htmlspecialchars($s['profile_image'] ?? 'default.png'); ?>" alt="" onerror="this.src='../uploads/profiles/default.png'" style="width:36px; height:36px; border-radius:50%;">
                                        <strong><?php echo htmlspecialchars($s['full_name']); ?></strong>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($s['email']); ?></td>
                                <td><strong><?php echo htmlspecialchars($s['course_title']); ?></strong></td>
                                <td><?php echo date('d M Y', strtotime($s['enrolled_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No students are currently registered in your courses.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
