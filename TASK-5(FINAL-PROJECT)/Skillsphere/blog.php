<?php
$pageTitle = 'SkillSphere Blog';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';

$jsonFile = 'storage/cache/blog.json';
$posts = [];
if (file_exists($jsonFile)) {
    $posts = json_decode(file_get_contents($jsonFile), true);
}
?>

<section class="page-header">
    <div class="container">
        <span class="badge badge-primary">Technical Guides</span>
        <h1 class="fade">The SkillSphere Blog</h1>
        <p class="fade">Read engineering walkthroughs, architectural writeups, and language guides written by our mentors.</p>
    </div>
</section>

<div class="container" style="margin-top:4rem; margin-bottom:6rem;">
    <div class="grid grid-3">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <div class="card reveal" style="padding: 2rem; display:flex; flex-direction:column; justify-content:space-between;">
                    <div>
                        <span style="font-size:0.75rem; color:var(--text-tertiary);"><?php echo date('d M Y', strtotime($post['date'])); ?></span>
                        <h3 style="margin-top:0.5rem; margin-bottom:1rem;"><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p style="color:var(--text-secondary); font-size:0.875rem; line-height:1.6; margin-bottom:1.5rem;">
                            <?php echo htmlspecialchars($post['excerpt']); ?>
                        </p>
                    </div>
                    <div style="border-top:1px solid var(--border-default); padding-top:1rem; display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:0.8125rem; color:var(--text-tertiary);">By <?php echo htmlspecialchars($post['author']); ?></span>
                        <a href="blog-details.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary">Read Article</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No blog posts published yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
