<?php
require_once '../auth.php';
requireAdmin();

$adminTitle = 'System Security';
$adminPage = 'security';
$adminHeading = 'System security';
$adminSubheading = 'Configure baseline parameters';
$adminIllustration = 'assets/images/admin-dashboard.svg';
$adminHeroTitle = 'System shielding';
$adminHeroText = 'Manage CSP rules, brute-force limits, active sessions, and SSL parameters.';

include 'includes/head.php';
include 'includes/sidebar.php';
?>
<div class="admin-main">
<?php include 'includes/topbar.php'; ?>

<div class="admin-panel reveal" style="max-width:700px; margin:0 auto;">
    <div class="admin-panel__head"><h3>Security Settings</h3></div>
    <div class="admin-panel__body">
        <form method="POST" class="admin-form" onsubmit="event.preventDefault(); alert('Security rules updated!');">
            <div class="form-group">
                <label for="lockout">Brute-Force Login Lockout Limit</label>
                <select id="lockout" class="form-control">
                    <option value="5">5 Failed attempts</option>
                    <option value="10" selected>10 Failed attempts</option>
                    <option value="0">Disabled</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-check" style="margin-top:1rem;">
                    <input type="checkbox" checked> Force HTTPS connections (SSL Redirection)
                </label>
            </div>

            <div class="form-group">
                <label class="form-check" style="margin-top:0.5rem;">
                    <input type="checkbox" checked> Enable strict Content Security Policy (CSP) headers
                </label>
            </div>

            <button type="submit" class="admin-btn admin-btn--primary" style="margin-top:1.5rem;">Update rules</button>
        </form>
    </div>
</div>

</div>
<?php include 'includes/footer.php'; ?>
