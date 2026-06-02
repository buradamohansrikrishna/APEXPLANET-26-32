<?php
$pageTitle = 'Course Categories';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';

$categories = fetchAllSecure("
    SELECT cat.*, COUNT(c.id) AS courses_count
    FROM categories cat
    LEFT JOIN courses c ON cat.id = c.category_id AND c.status = 'published'
    GROUP BY cat.id
    ORDER BY category_name ASC
");
?>

<section class="page-header">
    <div class="container">
        <span class="badge badge-primary">Taxonomy Browse</span>
        <h1 class="fade">Browse Course Categories</h1>
        <p class="fade">Select a technical domain below to explore expert-led structured paths.</p>
    </div>
</section>

<div class="container" style="margin-top:4rem; margin-bottom:6rem;">
    <div class="grid grid-4">
        <?php foreach ($categories as $cat): 
            $slug = htmlspecialchars($cat['slug']);
            ?>
            <div class="card reveal text-center" style="padding: 2rem; display:flex; flex-direction:column; justify-content:space-between; min-height:220px;">
                <div>
                    <div class="course-thumb-art course-thumb-art--<?php echo $slug; ?>" style="height:60px; border-radius:8px; margin-bottom:1.5rem;"></div>
                    <h3><?php echo htmlspecialchars($cat['category_name']); ?></h3>
                </div>
                <div style="margin-top: 1.5rem;">
                    <span style="font-size:0.875rem; color:var(--text-tertiary); display:block; margin-bottom:1rem;"><?php echo $cat['courses_count']; ?> courses</span>
                    <a href="courses.php?category=<?php echo $cat['id']; ?>" class="btn btn-sm btn-outline" style="width:100%; text-align:center;">Explore Domain</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
