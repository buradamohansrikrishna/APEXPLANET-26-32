<?php
$pageTitle = 'Instructors';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';

$instructors = fetchAllSecure("
    SELECT u.*, COUNT(c.id) AS courses_count
    FROM users u
    LEFT JOIN courses c ON u.id = c.instructor_id
    WHERE u.role = 'instructor'
    GROUP BY u.id
    ORDER BY courses_count DESC
");
?>

<section class="page-header">
    <div class="container">
        <span class="badge badge-primary">SkillSphere Mentors</span>
        <h1 class="fade">Learn from scaling builders</h1>
        <p class="fade">Meet the professional software engineers and AI researchers who design our guided curriculums.</p>
    </div>
</section>

<div class="container" style="margin-top:4rem; margin-bottom:6rem;">
    <div class="grid grid-3">
        <?php foreach ($instructors as $inst): ?>
            <div class="card reveal text-center" style="padding: 2rem;">
                <img src="uploads/profiles/<?php echo htmlspecialchars($inst['profile_image'] ?? 'default.png'); ?>" alt="" onerror="this.src='uploads/profiles/default.png'" style="width:96px; height:96px; border-radius:50%; margin: 0 auto 1.5rem; object-fit:cover;">
                <h3><?php echo htmlspecialchars($inst['full_name']); ?></h3>
                <p style="color:var(--brand-500); font-weight:bold; font-size:0.875rem; margin-top:0.25rem;">Expert Instructor</p>
                <p style="font-size:0.875rem; color:var(--text-secondary); margin-top:1rem; min-height:60px;">
                    <?php echo htmlspecialchars(limitText($inst['bio'] ?? 'Ex-staff engineer. 10+ years shipping production architectures.', 120)); ?>
                </p>
                <div style="border-top:1px solid var(--border-default); margin-top:1.5rem; padding-top:1rem; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:0.875rem; color:var(--text-tertiary);"><strong><?php echo $inst['courses_count']; ?></strong> published courses</span>
                    <a href="courses.php?instructor=<?php echo $inst['id']; ?>" class="btn btn-sm btn-outline">View courses</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
