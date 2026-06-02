<?php
require_once '../auth.php';
require_once '../middleware.php';
requireRole('instructor');

$instructorId = (int)$_SESSION['user_id'];

// Get reviews for courses taught by this instructor
$reviews = fetchAllSecure("
    SELECT r.*, u.full_name AS student_name, c.title AS course_title
    FROM reviews r
    JOIN courses c ON r.course_id = c.id
    JOIN users u ON r.user_id = u.id
    WHERE c.instructor_id = ?
    ORDER BY r.created_at DESC
", [$instructorId]);

$pageTitle = 'My Reviews - Instructor Portal';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <!-- Navigation links for portal -->
    <div style="display:flex; gap:1rem; border-bottom:1px solid var(--border-default); padding-bottom:1rem; margin-bottom:2rem;">
        <a href="dashboard.php" class="btn btn-sm btn-outline">Courses List</a>
        <a href="students.php" class="btn btn-sm btn-outline">My Students</a>
        <a href="analytics.php" class="btn btn-sm btn-outline">Analytics</a>
        <a href="earnings.php" class="btn btn-sm btn-outline">Earnings Report</a>
        <a href="messages.php" class="btn btn-sm btn-outline">QA Messages</a>
        <a href="reviews.php" class="btn btn-sm btn-primary">Course Reviews</a>
    </div>

    <h2 style="margin-bottom: 1.5rem;">Student Feedbacks</h2>
    <div class="card reveal" style="padding: 2rem;">
        <?php if (!empty($reviews)): ?>
            <div style="display:flex; flex-direction:column; gap:1.5rem;">
                <?php foreach ($reviews as $rev): 
                    $stars = str_repeat('<i class="fa-solid fa-star" style="color:#d97706;"></i>', $rev['rating']) . str_repeat('<i class="fa-regular fa-star" style="color:var(--text-muted);"></i>', 5 - $rev['rating']);
                    ?>
                    <div style="border-bottom:1px solid var(--border-default); padding-bottom:1.5rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                            <strong><?php echo htmlspecialchars($rev['student_name']); ?></strong>
                            <span style="font-size:0.875rem; color:var(--text-tertiary);"><?php echo date('d M Y', strtotime($rev['created_at'])); ?></span>
                        </div>
                        <div style="margin-bottom:0.75rem;">
                            <span><?php echo $stars; ?></span>
                            <span style="margin-left:0.5rem; font-size:0.875rem; color:var(--text-secondary);">on <em><?php echo htmlspecialchars($rev['course_title']); ?></em></span>
                        </div>
                        <p style="font-style:italic;">"<?php echo htmlspecialchars($rev['review']); ?>"</p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No course reviews submitted by students yet.</p>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
