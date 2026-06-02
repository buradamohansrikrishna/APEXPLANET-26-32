<?php
require_once '../../auth.php';
requireAdmin();

$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$student = null;
$progressData = [];

if ($studentId > 0) {
    $student = fetchSingleSecure("SELECT * FROM users WHERE id = ? AND role = 'student'", [$studentId]);
    if ($student) {
        $progressData = fetchAllSecure("
            SELECT e.*, c.title AS course_title,
                   (SELECT COUNT(l.id) FROM lessons l WHERE l.course_id = e.course_id) AS total_lessons,
                   (SELECT COUNT(lp.id) FROM lesson_progress lp JOIN lessons l ON lp.lesson_id = l.id WHERE lp.user_id = e.user_id AND l.course_id = e.course_id AND lp.completed = 1) AS completed_lessons
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            WHERE e.user_id = ?
        ", [$studentId]);
    }
}

$adminTitle = 'Student Progress';
$adminPage = 'students';
$adminHeading = 'Student progress';
$adminSubheading = 'Track course completions';
$adminIllustration = '../assets/images/admin-analytics.svg';
$adminHeroTitle = 'Progress metrics';
$adminHeroText = 'Track enrollments, lecture views, completion percentages, and study achievements.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<div class="admin-panel reveal">
    <div class="admin-panel__head">
        <h3><?php echo $student ? 'Course progress for ' . htmlspecialchars($student['full_name']) : 'Select a student to view progress'; ?></h3>
    </div>
    <div class="admin-panel__body">
        <?php if ($student): ?>
            <?php if (!empty($progressData)): ?>
                <div class="admin-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Course Title</th>
                                <th>Lessons Completed</th>
                                <th>Completion Rate</th>
                                <th>Enrolled On</th>
                                <th>Status</th>
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
                                    <td><?php echo $completed; ?> / <?php echo $total; ?></td>
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
                                        <?php if ($pct === 100): ?>
                                            <span class="badge badge-success">Completed</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">In Progress</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>This student is not enrolled in any courses yet.</p>
            <?php endif; ?>
            <div style="margin-top:1.5rem;">
                <a href="manage-students.php" class="admin-btn admin-btn--outline">Back to directory</a>
            </div>
        <?php else: ?>
            <p>Please select a student from the <a href="manage-students.php">Students Directory</a>.</p>
        <?php endif; ?>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
