<?php
require_once '../auth.php';
requireAdmin();

$message = '';
if (isset($_POST['save_settings'])) {
    // Simulate settings saving
    $message = 'System configuration updated successfully!';
}

$adminTitle = 'System Settings';
$adminPage = 'settings';
$adminHeading = 'System settings';
$adminSubheading = 'Configure baseline parameters';
$adminIllustration = 'assets/images/admin-dashboard.svg';
$adminHeroTitle = 'Configuration';
$adminHeroText = 'Edit system metadata, maintenance states, payment gateways, and caching policies.';

include 'includes/head.php';
include 'includes/sidebar.php';
?>
<div class="admin-main">
<?php include 'includes/topbar.php'; ?>

<div class="admin-panel reveal" style="max-width:700px; margin:0 auto;">
    <div class="admin-panel__head"><h3>Global Preferences</h3></div>
    <div class="admin-panel__body">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" class="admin-form">
            <div class="form-group">
                <label for="site_name">Platform Name</label>
                <input type="text" name="site_name" id="site_name" class="form-control" value="SkillSphere">
            </div>
            
            <div class="form-group">
                <label for="tagline">Tagline</label>
                <input type="text" name="tagline" id="tagline" class="form-control" value="Modern Online Learning Platform">
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label for="smtp_host">SMTP Host</label>
                    <input type="text" name="smtp_host" id="smtp_host" class="form-control" value="smtp.skillsphere.com">
                </div>
                <div class="form-group">
                    <label for="smtp_port">SMTP Port</label>
                    <input type="number" name="smtp_port" id="smtp_port" class="form-control" value="587">
                </div>
            </div>

            <div class="form-group">
                <label class="form-check" style="margin-top:1rem;">
                    <input type="checkbox" name="maintenance" value="1">
                    Enable Platform Maintenance Mode (Redirects public visits to 503 error)
                </label>
            </div>

            <div style="margin-top:1.5rem;">
                <button type="submit" name="save_settings" class="admin-btn admin-btn--primary">Save Preferences</button>
            </div>
        </form>
    </div>
</div>

</div>
<?php include 'includes/footer.php'; ?>
