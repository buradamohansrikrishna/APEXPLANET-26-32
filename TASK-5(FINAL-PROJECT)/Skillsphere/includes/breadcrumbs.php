<!-- =========================================
     SKILLSPHERE BREADCRUMBS COMPONENT
     includes/breadcrumbs.php
========================================= -->
<nav class="breadcrumbs" aria-label="Breadcrumb">
    <div class="container">
        <ul class="breadcrumbs__list">
            <li class="breadcrumbs__item">
                <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
            </li>
            <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                <?php foreach ($breadcrumbs as $name => $url): ?>
                    <li class="breadcrumbs__separator"><i class="fa-solid fa-chevron-right"></i></li>
                    <li class="breadcrumbs__item">
                        <?php if ($url): ?>
                            <a href="<?php echo htmlspecialchars($url); ?>"><?php echo htmlspecialchars($name); ?></a>
                        <?php else: ?>
                            <span aria-current="page"><?php echo htmlspecialchars($name); ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</nav>
