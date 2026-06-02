<?php
$pageTitle = 'Success Testimonials';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';

$jsonFile = 'storage/cache/testimonials.json';
$list = [];
if (file_exists($jsonFile)) {
    $list = json_decode(file_get_contents($jsonFile), true);
}
?>

<section class="page-header">
    <div class="container">
        <span class="badge badge-primary">Success Stories</span>
        <h1 class="fade">Loved by ambitious developers</h1>
        <p class="fade">Here is what engineers and engineering managers say about learning and upskilling with SkillSphere.</p>
    </div>
</section>

<div class="container" style="margin-top:4rem; margin-bottom:6rem;">
    <div class="grid grid-3">
        <?php if (!empty($list)): ?>
            <?php foreach ($list as $t): ?>
                <div class="card reveal" style="padding: 2rem;">
                    <div style="font-size:1.75rem; color:var(--brand-500); margin-bottom:1rem;"><i class="fa-solid fa-quote-left"></i></div>
                    <p style="font-style:italic; line-height:1.6; min-height:80px;">"<?php echo htmlspecialchars($t['quote']); ?>"</p>
                    <div style="display:flex; align-items:center; gap:0.75rem; margin-top:2rem; border-top:1px solid var(--border-default); padding-top:1rem;">
                        <div class="testimonial-card__avatar"><?php echo substr($t['name'], 0, 2); ?></div>
                        <div>
                            <strong><?php echo htmlspecialchars($t['name']); ?></strong><br>
                            <small style="color:var(--text-tertiary);"><?php echo htmlspecialchars($t['role']); ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No testimonials configured yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
