<?php
$pageTitle = 'Pricing Plans';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <span class="badge badge-primary">Pricing Plans</span>
        <h1 class="fade">Flexible plans for any path</h1>
        <p class="fade">Unlock full access to industry-aligned courses, dynamic quizzes, and verified credentials.</p>
    </div>
</section>

<div class="container" style="margin-top:4rem; margin-bottom:6rem;">
    <div class="grid grid-3" style="align-items: stretch;">
        <!-- Basic Plan -->
        <div class="card reveal" style="padding:2.5rem; display:flex; flex-direction:column;">
            <h3>Single Purchase</h3>
            <div style="font-size:2.5rem; font-weight:bold; margin-top:1rem; margin-bottom:1rem;">Individual Price</div>
            <p style="color:var(--text-secondary); margin-bottom:2rem; flex-grow:1;">Pay once per course. Keep lifetime access to videos, assignments, and future updates.</p>
            <ul style="list-style:none; padding:0; display:flex; flex-direction:column; gap:0.75rem; margin-bottom:2rem; color:var(--text-secondary);">
                <li><i class="fa-solid fa-check" style="color:var(--success);"></i> Lifetime course access</li>
                <li><i class="fa-solid fa-check" style="color:var(--success);"></i> QA discussion sections</li>
                <li><i class="fa-solid fa-check" style="color:var(--success);"></i> Verifiable certificate</li>
            </ul>
            <a href="courses.php" class="btn btn-outline" style="text-align:center;">Explore Catalog</a>
        </div>

        <!-- Premium Plan -->
        <div class="card reveal" style="padding:2.5rem; display:flex; flex-direction:column; border:2px solid var(--brand-500); position:relative;">
            <div style="position:absolute; top:-15px; left:50%; transform:translateX(-50%); background:var(--brand-500); color:white; padding:0.25rem 1rem; border-radius:12px; font-size:0.75rem; font-weight:bold; text-transform:uppercase;">Most Popular</div>
            <h3>Monthly Membership</h3>
            <div style="font-size:2.5rem; font-weight:bold; margin-top:1rem; margin-bottom:1rem;">₹999 <small style="font-size:1rem; color:var(--text-muted);">/ month</small></div>
            <p style="color:var(--text-secondary); margin-bottom:2rem; flex-grow:1;">Unlimited access to all courses, projects, paths, and AI learning trackers. Cancel anytime.</p>
            <ul style="list-style:none; padding:0; display:flex; flex-direction:column; gap:0.75rem; margin-bottom:2rem; color:var(--text-secondary);">
                <li><i class="fa-solid fa-check" style="color:var(--success);"></i> Access to 100+ premium tracks</li>
                <li><i class="fa-solid fa-check" style="color:var(--success);"></i> AI chat assistant study guide</li>
                <li><i class="fa-solid fa-check" style="color:var(--success);"></i> Unlimited verified certificates</li>
                <li><i class="fa-solid fa-check" style="color:var(--success);"></i> Gamified leaderboard ranks</li>
            </ul>
            <a href="register.php" class="btn btn-primary" style="text-align:center;">Start 7-Day Free Trial</a>
        </div>

        <!-- Enterprise Plan -->
        <div class="card reveal" style="padding:2.5rem; display:flex; flex-direction:column;">
            <h3>SkillSphere Business</h3>
            <div style="font-size:2.5rem; font-weight:bold; margin-top:1rem; margin-bottom:1rem;">Custom Quote</div>
            <p style="color:var(--text-secondary); margin-bottom:2rem; flex-grow:1;">For teams and enterprise organizations. Central billing, progress reporting, and custom paths.</p>
            <ul style="list-style:none; padding:0; display:flex; flex-direction:column; gap:0.75rem; margin-bottom:2rem; color:var(--text-secondary);">
                <li><i class="fa-solid fa-check" style="color:var(--success);"></i> Centralized manager dashboard</li>
                <li><i class="fa-solid fa-check" style="color:var(--success);"></i> Seat management metrics</li>
                <li><i class="fa-solid fa-check" style="color:var(--success);"></i> Custom corporate curricula</li>
            </ul>
            <a href="contact.php" class="btn btn-outline" style="text-align:center;">Contact Enterprise Sales</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
