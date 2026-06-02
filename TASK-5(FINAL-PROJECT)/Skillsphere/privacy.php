<?php
$pageTitle = 'Privacy Policy';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<section class="page-header">
    <div class="container container-narrow">
        <h1>Privacy Policy</h1>
        <p>Last updated: May 24, 2026</p>
    </div>
</section>

<div class="container container-narrow" style="margin-top:3rem; margin-bottom:6rem;">
    <div class="card reveal" style="padding:3rem; line-height:1.7; color:var(--text-secondary);">
        <h2>1. Data Collection</h2>
        <p style="margin-bottom:1.5rem;">We collect your registration credentials, course enrollments, progress markers, and payment tokens to provide a unified LMS experience.</p>
        
        <h2>2. Data Usage</h2>
        <p style="margin-bottom:1.5rem;">Your stats and progress checks are analyzed by our AI engines to provide personalized study guides and recommendations.</p>
        
        <h2>3. Third Parties</h2>
        <p style="margin-bottom:1.5rem;">We do not share your private database logs or billing records with unverified third parties.</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
