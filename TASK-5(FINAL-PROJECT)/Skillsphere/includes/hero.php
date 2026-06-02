<!-- =========================================
     SKILLSPHERE HERO COMPONENT
     includes/hero.php
========================================= -->
<div class="hero-component reveal">
    <div class="hero-component__overlay"></div>
    <div class="container">
        <div class="hero-component__content">
            <span class="badge badge-primary"><i class="fa-solid fa-graduation-cap"></i> <?php echo htmlspecialchars($heroTag ?? 'SkillSphere'); ?></span>
            <h1 class="hero-component__title text-gradient"><?php echo htmlspecialchars($heroTitle ?? 'Learn Without Limits'); ?></h1>
            <p class="hero-component__text"><?php echo htmlspecialchars($heroDesc ?? 'Discover world-class tech training programs designed to launch your career to new heights.'); ?></p>
            <?php if (isset($heroBtnText) && !empty($heroBtnText)): ?>
                <div class="hero-component__actions">
                    <a href="<?php echo htmlspecialchars($heroBtnUrl ?? '#'); ?>" class="btn btn-lg btn-primary"><?php echo htmlspecialchars($heroBtnText); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
