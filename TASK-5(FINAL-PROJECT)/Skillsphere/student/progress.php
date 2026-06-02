<?php
require_once '../auth.php';
require_once '../middleware.php';
requireRole('student');

$userId = (int)$_SESSION['user_id'];
$progressData = fetchAllSecure("
    SELECT e.*, c.title AS course_title,
           (SELECT COUNT(l.id) FROM lessons l WHERE l.course_id = e.course_id) AS total_lessons,
           (SELECT COUNT(lp.id) FROM lesson_progress lp JOIN lessons l ON lp.lesson_id = l.id WHERE lp.user_id = e.user_id AND l.course_id = e.course_id AND lp.completed = 1) AS completed_lessons
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.user_id = ?
", [$userId]);

$pageTitle = 'Study Progress';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <span class="badge badge-primary">Student Portal</span>
            <h1 class="text-gradient">Detailed Progress Breakdown</h1>
        </div>
    </div>

    <!-- Navigation links for portal -->
    <div style="display:flex; gap:1rem; border-bottom:1px solid var(--border-default); padding-bottom:1rem; margin-bottom:2rem;">
        <a href="dashboard.php" class="btn btn-sm btn-outline">Active Courses</a>
        <a href="progress.php" class="btn btn-sm btn-primary">Study Progress</a>
        <a href="certificates.php" class="btn btn-sm btn-outline">Certificates</a>
        <a href="achievements.php" class="btn btn-sm btn-outline">Achievements</a>
        <a href="leaderboard.php" class="btn btn-sm btn-outline">Leaderboard</a>
        <a href="wishlist.php" class="btn btn-sm btn-outline">Wishlist</a>
    </div>

    <div class="card reveal" style="padding: 2rem;">
        <?php if (!empty($progressData)): ?>
            <div class="admin-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Course Title</th>
                            <th>Lessons Completed</th>
                            <th>Completion Rate</th>
                            <th>Enrolled On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($progressData as $prog): 
                            $total = (int)$prog['total_lessons'];
                            $completed = (int)$prog['completed_lessons'];
                            $pct = $total > 0 ? round(($completed / $total) * 100) : 0;
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($prog['course_title']); ?></strong></td>
                                <td><?php echo $completed; ?> / <?php echo $total; ?> lectures</td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:0.5rem;">
                                        <div style="width:100px; height:8px; background:var(--bg-muted); border-radius:4px; overflow:hidden;">
                                            <div style="width:<?php echo $pct; ?>%; height:100%; background:var(--brand-500);"></div>
                                        </div>
                                        <span><?php echo $pct; ?>%</span>
                                    </div>
                                </td>
                                <td><?php echo date('d M Y', strtotime($prog['enrolled_at'])); ?></td>
                                <td>
                                    <a href="../my-progress.php?course_id=<?php echo $prog['course_id']; ?>" class="btn btn-sm btn-primary">Resume</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>You have not registered for any courses yet. Explore courses to begin your study tracking!</p>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
