<?php
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$jsonFile = 'storage/cache/blog.json';
$post = null;
if (file_exists($jsonFile)) {
    $posts = json_decode(file_get_contents($jsonFile), true);
    foreach ($posts as $p) {
        if ($p['id'] === $id) {
            $post = $p;
            break;
        }
    }
}

if (!$post) {
    header("Location: blog.php");
    exit();
}

$pageTitle = $post['title'];
include 'includes/header.php';
include 'includes/navbar.php';
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="badge badge-primary">Technical Guides</span>
        <h1 class="fade"><?php echo htmlspecialchars($post['title']); ?></h1>
        <p class="fade">Published on <?php echo date('d M Y', strtotime($post['date'])); ?> by <?php echo htmlspecialchars($post['author']); ?></p>
    </div>
</section>

<div class="container container-narrow" style="margin-top:4rem; margin-bottom:6rem;">
    <div class="card reveal" style="padding: 3rem; line-height: 1.8; font-size:1.125rem;">
        <?php echo $post['content']; ?>
        <div style="margin-top: 3rem; border-top: 1px solid var(--border-default); padding-top: 2rem;">
            <a href="blog.php" class="btn btn-outline">Back to Blog</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
