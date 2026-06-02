<?php
$pageTitle = 'Frequently Asked Questions';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';

$jsonFile = 'storage/cache/faq.json';
$list = [];
if (file_exists($jsonFile)) {
    $list = json_decode(file_get_contents($jsonFile), true);
}
?>

<section class="page-header">
    <div class="container">
        <span class="badge badge-primary">Help & FAQ</span>
        <h1 class="fade">Frequently Asked Questions</h1>
        <p class="fade">Find quick answers to common queries regarding registration, pricing plans, lessons, and certificates.</p>
    </div>
</section>

<div class="container container-narrow" style="margin-top:4rem; margin-bottom:6rem;">
    <div class="faq-list" style="display:flex; flex-direction:column; gap:1.25rem;">
        <?php if (!empty($list)): ?>
            <?php foreach ($list as $f): ?>
                <div class="faq-item reveal">
                    <button type="button" class="faq-question" aria-expanded="false">
                        <?php echo htmlspecialchars($f['question']); ?>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <?php echo htmlspecialchars($f['answer']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No FAQs available.</p>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/app.js"></script>
<?php include 'includes/footer.php'; ?>
