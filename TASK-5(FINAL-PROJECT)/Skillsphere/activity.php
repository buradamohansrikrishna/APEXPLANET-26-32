<?php
$pageTitle = 'Study Activity';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
require_once 'middleware.php';

requireLogin();

$userId = (int)$_SESSION['user_id'];
$activities = fetchAllSecure("
    SELECT lp.*, l.title AS lesson_title, c.title AS course_title
    FROM lesson_progress lp
    JOIN lessons l ON lp.lesson_id = l.id
    JOIN courses c ON l.course_id = c.id
    WHERE lp.user_id = ?
    ORDER BY lp.completed_at DESC
    LIMIT 10
", [$userId]);
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem; max-width: 700px;">
    <h1>Study Activity Log</h1>
    <p style="color:var(--text-secondary); margin-bottom:2rem;">Auditing review sessions, lesson completions, and quiz scores.</p>

    <div class="card reveal" style="padding: 2rem;">
        <?php if (!empty($activities)): ?>
            <div style="display:flex; flex-direction:column; gap:1.25rem;">
                <?php foreach ($activities as $act): ?>
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-default); padding-bottom:1rem;">
                        <div>
                            <span style="font-size:0.75rem; color:var(--text-tertiary); display:block;"><?php echo htmlspecialchars($act['course_title']); ?></span>
                            <strong style="font-size:1.1rem;"><?php echo htmlspecialchars($act['lesson_title']); ?></strong>
                        </div>
                        <div style="text-align:right;">
                            <span class="badge badge-success">Completed</span>
                            <time style="font-size:0.75rem; color:var(--text-tertiary); display:block; margin-top:0.25rem;"><?php echo date('d M Y, h:i A', strtotime($act['completed_at'])); ?></time>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center" style="padding: 2rem;">
                <p>No study activity logged yet. Start watching lectures to track progress!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
