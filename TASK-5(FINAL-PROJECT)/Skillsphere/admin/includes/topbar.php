<?php
$adminHeading = $adminHeading ?? 'Dashboard';
$adminSubheading = $adminSubheading ?? '';
$adminIllustration = $adminIllustration ?? '';
?>
<header class="admin-topbar">
    <div class="admin-topbar__text">
        <h1><?php echo htmlspecialchars($adminHeading); ?></h1>
        <?php if ($adminSubheading): ?>
            <p><?php echo htmlspecialchars($adminSubheading); ?></p>
        <?php endif; ?>
    </div>
    <div class="admin-topbar__actions">
        <button type="button" class="admin-icon-btn" data-theme-toggle aria-label="Toggle theme">
            <i class="fa-solid fa-sun theme-toggle__icon theme-toggle__icon--sun"></i>
            <i class="fa-solid fa-moon theme-toggle__icon theme-toggle__icon--moon"></i>
        </button>
        <?php if (!empty($adminTopbarActions)) echo $adminTopbarActions; ?>
    </div>
</header>

<?php if ($adminIllustration): ?>
<div class="admin-hero reveal">
    <div class="admin-hero__copy">
        <span class="admin-pill"><i class="fa-solid fa-shield-halved"></i> Secure workspace</span>
        <h2><?php echo htmlspecialchars($adminHeroTitle ?? 'Operations overview'); ?></h2>
        <p><?php echo htmlspecialchars($adminHeroText ?? 'Monitor platform health, content, and learner activity in real time.'); ?></p>
    </div>
    <div class="admin-hero__visual">
        <img src="<?php echo htmlspecialchars($adminIllustration); ?>" alt="" width="480" height="360" loading="lazy">
    </div>
</div>
<?php endif; ?>
