<?php
$pageTitle = 'Help & Support';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<section class="page-header">
    <div class="container container-narrow">
        <h1>Help & Support Center</h1>
        <p>Get assistance with billing, account access, or database setup.</p>
    </div>
</section>

<div class="container container-narrow" style="margin-top:3rem; margin-bottom:6rem;">
    <div class="card reveal" style="padding:3rem; line-height:1.7; color:var(--text-secondary);">
        <h2>Contact Support</h2>
        <p style="margin-bottom:1.5rem;">For direct technical support, please submit a message through our <a href="contact.php">Contact Page</a>.</p>
        
        <h2>Knowledge Base</h2>
        <p style="margin-bottom:1.5rem;">Review common queries in our collapsible <a href="faq.php">Frequently Asked Questions (FAQ)</a> catalog.</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
