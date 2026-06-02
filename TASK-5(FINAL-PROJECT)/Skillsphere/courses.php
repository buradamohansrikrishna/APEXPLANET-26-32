<?php
$pageTitle = 'Courses';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';

$query = dbQuery(
    "SELECT c.*, cat.category_name, u.full_name AS instructor_name
     FROM courses c
     LEFT JOIN categories cat ON c.category_id = cat.id
     LEFT JOIN users u ON c.instructor_id = u.id
     ORDER BY c.created_at DESC"
);
?>

<section class="page-header">
    <div class="container">
        <span class="badge badge-primary"><i class="fa-solid fa-book"></i> Course catalog</span>
        <h1 class="fade">Learn modern skills</h1>
        <p class="fade">Explore premium technology courses, build real projects, and advance your career with SkillSphere.</p>
    </div>
</section>

<section class="section-sm" style="padding-bottom: 0;">
    <div class="container">
        <div class="search-bar">
            <i class="fa-solid fa-magnifying-glass search-bar__icon"></i>
            <input type="text" id="searchInput" class="form-control" placeholder="Search courses, topics, instructors…" autocomplete="off">
            <div id="searchResults" class="search-results" hidden></div>
        </div>
    </div>
</section>

<section class="section courses-section">
    <div class="container">
        <div class="course-grid">
            <?php if ($query && mysqli_num_rows($query) > 0): ?>
                <?php while ($course = mysqli_fetch_assoc($query)): ?>
                    <?php include 'includes/course-card.php'; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="card text-center" style="grid-column: 1 / -1; padding: 3rem;">
                    <h3>No courses available</h3>
                    <p style="margin-top: 1rem;">New courses will appear here soon.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

