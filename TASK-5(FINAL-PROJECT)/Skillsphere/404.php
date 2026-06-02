<?php
$pageTitle = 'Page Not Found';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container" style="margin-top:8rem; margin-bottom:10rem; text-align:center;">
    <div class="reveal" style="max-width:500px; margin: 0 auto;">
        <span class="text-gradient" style="font-size:8rem; font-weight:bold; font-family:var(--font-display); line-height:1;">404</span>
        <h2 style="font-size:2rem; margin-top:1.5rem; margin-bottom:1rem;">Lost in the Sphere?</h2>
        <p style="color:var(--text-secondary); margin-bottom:2.5rem; line-height:1.6;">
            The page you are looking for has been moved, deleted, or does not exist.
        </p>
        <a href="index.php" class="btn btn-primary btn-lg">Back to Home</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
