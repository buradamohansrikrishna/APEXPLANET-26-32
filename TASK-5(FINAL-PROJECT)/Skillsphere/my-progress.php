<?php
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
require_once 'middleware.php';

requireLogin();

$userId = (int)$_SESSION['user_id'];
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

$course = fetchSingleSecure("SELECT * FROM courses WHERE id = ? LIMIT 1", [$courseId]);
if (!$course) {
    $_SESSION['error'] = "Course not found";
    header("Location: dashboard.php");
    exit();
}

// Check enrollment
$enrolled = fetchSingleSecure("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ? LIMIT 1", [$userId, $courseId]);
if (!$enrolled) {
    $_SESSION['error'] = "You are not enrolled in this course";
    header("Location: course-details.php?id=" . $courseId);
    exit();
}

$lessons = fetchAllSecure("SELECT * FROM lessons WHERE course_id = ? ORDER BY lesson_order ASC", [$courseId]);

// Determine active lesson
$activeLessonId = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;
$activeLesson = null;
if ($activeLessonId > 0) {
    foreach ($lessons as $l) {
        if ((int)$l['id'] === $activeLessonId) {
            $activeLesson = $l;
            break;
        }
    }
}
if (!$activeLesson && !empty($lessons)) {
    $activeLesson = $lessons[0];
    $activeLessonId = (int)$activeLesson['id'];
}

// Handle lesson completion toggle
if (isset($_GET['complete'])) {
    $compLessonId = (int)$_GET['complete'];
    $val = (int)$_GET['val']; // 1 or 0
    
    // Check if progress row exists
    $progCheck = fetchSingleSecure("SELECT * FROM lesson_progress WHERE user_id = ? AND lesson_id = ?", [$userId, $compLessonId]);
    if ($progCheck) {
        dbQuery("UPDATE lesson_progress SET completed = ?, completed_at = NOW() WHERE user_id = ? AND lesson_id = ?", [$val, $userId, $compLessonId]);
    } else {
        dbQuery("INSERT INTO lesson_progress (user_id, lesson_id, completed, completed_at) VALUES (?, ?, ?, NOW())", [$userId, $compLessonId, $val]);
    }
    
    // Check if course completed 100%
    $totalLessons = count($lessons);
    $completedLessons = fetchSingleSecure("
        SELECT COUNT(lp.id) AS c 
        FROM lesson_progress lp 
        JOIN lessons l ON lp.lesson_id = l.id 
        WHERE lp.user_id = ? AND l.course_id = ? AND lp.completed = 1
    ", [$userId, $courseId])['c'] ?? 0;
    
    if ($completedLessons === $totalLessons && $totalLessons > 0) {
        // Mark enrollment completed
        dbQuery("UPDATE enrollments SET completed_at = NOW() WHERE user_id = ? AND course_id = ?", [$userId, $courseId]);
        
        // Generate certificate code if not exists
        $certCheck = fetchSingleSecure("SELECT * FROM certificates WHERE user_id = ? AND course_id = ?", [$userId, $courseId]);
        if (!$certCheck) {
            $certCode = 'CERT-' . strtoupper(bin2hex(random_bytes(4)));
            dbQuery("INSERT INTO certificates (user_id, course_id, certificate_code) VALUES (?, ?, ?)", [$userId, $courseId, $certCode]);
            dbQuery(
                "INSERT INTO notifications (user_id, title, message) VALUES (?, 'Certificate Issued!', ?)",
                [$userId, "Congratulations! You earned a completion certificate for: " . $course['title']]
            );
        }
    }
    
    header("Location: my-progress.php?course_id=$courseId&lesson_id=$compLessonId");
    exit();
}

$pageTitle = $course['title'] . ' - Learning View';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <div style="margin-bottom: 2rem;">
        <span class="badge badge-primary"><a href="dashboard.php" style="color:inherit;">Student Dashboard</a> / Learning View</span>
        <h1 class="text-gradient"><?php echo htmlspecialchars($course['title']); ?></h1>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="grid grid-4" style="align-items: flex-start;">
        <!-- Left Side: Video & Content -->
        <main style="grid-column: span 3;">
            <div class="card reveal" style="padding: 1.5rem; margin-bottom: 2rem;">
                <?php if ($activeLesson): ?>
                    <!-- Mock premium video frame with pulse effect -->
                    <div style="width:100%; height:400px; background:#0f172a; border-radius:12px; display:flex; flex-direction:column; justify-content:center; align-items:center; position:relative; overflow:hidden; margin-bottom:1.5rem; border:2px solid var(--border-default);">
                        <i class="fa-solid fa-play" style="font-size:4rem; color:var(--brand-500); cursor:pointer; filter:drop-shadow(0 0 20px rgba(99,102,241,0.6));"></i>
                        <p style="color:var(--text-inverse); font-family:var(--font-display); font-weight:bold; font-size:1.25rem; margin-top:1.5rem; z-index:10;">
                            Lecture Video: <?php echo htmlspecialchars($activeLesson['title']); ?>
                        </p>
                        <small style="color:var(--text-muted); z-index:10;">Duration: <?php echo htmlspecialchars($activeLesson['duration'] ?? 'Self-paced'); ?></small>
                        <div class="hero__glow hero__glow--1" style="transform: scale(0.6);"></div>
                    </div>

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                        <h2><?php echo htmlspecialchars($activeLesson['title']); ?></h2>
                        <?php 
                        // Check if completed
                        $isCompleted = fetchSingleSecure("SELECT completed FROM lesson_progress WHERE user_id = ? AND lesson_id = ? LIMIT 1", [$userId, $activeLessonId])['completed'] ?? 0;
                        if ($isCompleted): ?>
                            <a href="my-progress.php?course_id=<?php echo $courseId; ?>&lesson_id=<?php echo $activeLessonId; ?>&complete=<?php echo $activeLessonId; ?>&val=0" class="btn btn-sm btn-outline" style="border-color:var(--success); color:var(--success);"><i class="fa-solid fa-circle-check"></i> Completed (Undo)</a>
                        <?php else: ?>
                            <a href="my-progress.php?course_id=<?php echo $courseId; ?>&lesson_id=<?php echo $activeLessonId; ?>&complete=<?php echo $activeLessonId; ?>&val=1" class="btn btn-sm btn-primary"><i class="fa-regular fa-circle-check"></i> Mark Completed</a>
                        <?php endif; ?>
                    </div>

                    <div style="line-height:1.7; color:var(--text-secondary);">
                        <?php echo $activeLesson['lesson_content'] ?: '<p>Review this module lecture. Write notes and practice coding modules offline.</p>'; ?>
                    </div>
                <?php else: ?>
                    <p>No lessons added to this course syllabus yet.</p>
                <?php endif; ?>
            </div>
        </main>

        <!-- Right Side: Syllabus Navigation -->
        <aside style="grid-column: span 1;">
            <div class="card reveal" style="padding: 1.5rem;">
                <h3>Course Content</h3>
                <p style="font-size:0.875rem; color:var(--text-tertiary); margin-bottom:1.25rem;">Select a lesson to load video</p>
                
                <ul style="list-style:none; padding:0; display:flex; flex-direction:column; gap:0.5rem;">
                    <?php 
                    $num = 1;
                    foreach ($lessons as $l): 
                        $isActive = (int)$l['id'] === $activeLessonId;
                        $isComp = fetchSingleSecure("SELECT completed FROM lesson_progress WHERE user_id = ? AND lesson_id = ? LIMIT 1", [$userId, $l['id']])['completed'] ?? 0;
                        ?>
                        <li>
                            <a href="my-progress.php?course_id=<?php echo $courseId; ?>&lesson_id=<?php echo $l['id']; ?>" 
                               style="display:flex; justify-content:space-between; align-items:center; padding:0.75rem 1rem; border-radius:8px; text-decoration:none; 
                                      background:<?php echo $isActive ? 'var(--brand-50)' : 'var(--bg-subtle)'; ?>; 
                                      color:<?php echo $isActive ? 'var(--brand-700)' : 'var(--text-primary)'; ?>;
                                      border: 1px solid <?php echo $isActive ? 'var(--brand-100)' : 'transparent'; ?>;">
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <span style="font-size:0.75rem; color:var(--text-tertiary);"><?php echo $num; ?>.</span>
                                    <span><?php echo htmlspecialchars($l['title']); ?></span>
                                </div>
                                <?php if ($isComp): ?>
                                    <i class="fa-solid fa-circle-check" style="color:var(--success);"></i>
                                <?php else: ?>
                                    <i class="fa-regular fa-circle" style="color:var(--text-muted); font-size:0.875rem;"></i>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php 
                        $num++;
                    endforeach; ?>
                </ul>
            </div>
        </aside>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
