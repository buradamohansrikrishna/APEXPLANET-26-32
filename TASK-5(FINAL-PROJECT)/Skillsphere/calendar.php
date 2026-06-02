<?php
$pageTitle = 'Study Calendar';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem; max-width: 800px;">
    <h1>Study Calendar</h1>
    <p style="color:var(--text-secondary); margin-bottom:2rem;">Track live QA reviews, module submission deadlines, and upcoming quizzes.</p>

    <div class="card reveal" style="padding:2.5rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <h2>May 2026</h2>
        </div>
        <div style="display:grid; grid-template-columns: repeat(7, 1fr); gap:0.5rem; text-align:center; font-weight:bold; margin-bottom:1rem; border-bottom:1px solid var(--border-default); padding-bottom:0.5rem;">
            <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
        </div>
        <div style="display:grid; grid-template-columns: repeat(7, 1fr); gap:0.5rem; min-height:300px;">
            <?php 
            // Render 31 days
            for ($i = 1; $i <= 31; $i++) {
                $hasEvent = ($i === 24 || $i === 28 || $i === 30);
                echo '<div style="background:var(--bg-subtle); border-radius:8px; padding:0.5rem; min-height:60px; text-align:left; border:1px solid ' . ($hasEvent ? 'var(--brand-100)' : 'transparent') . '; ' . ($hasEvent ? 'background:var(--brand-50);' : '') . '">';
                echo '<strong>' . $i . '</strong>';
                if ($i === 24) {
                    echo '<div style="font-size:0.65rem; margin-top:0.25rem; color:var(--brand-700); font-weight:bold;"><i class="fa-solid fa-bell"></i> Live QA (3 PM)</div>';
                }
                if ($i === 28) {
                    echo '<div style="font-size:0.65rem; margin-top:0.25rem; color:var(--success); font-weight:bold;"><i class="fa-solid fa-code"></i> Project due</div>';
                }
                echo '</div>';
            }
            ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
