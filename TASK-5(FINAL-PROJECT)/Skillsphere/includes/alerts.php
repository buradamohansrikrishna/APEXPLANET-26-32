<!-- =========================================
     SKILLSPHERE ALERTS COMPONENT
     includes/alerts.php
========================================= -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible reveal" role="alert">
        <i class="fa-solid fa-circle-check"></i>
        <span><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
        <button type="button" class="alert-close" onclick="this.parentElement.remove();" aria-label="Close">&times;</button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible reveal" role="alert">
        <i class="fa-solid fa-circle-xmark"></i>
        <span><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
        <button type="button" class="alert-close" onclick="this.parentElement.remove();" aria-label="Close">&times;</button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['info'])): ?>
    <div class="alert alert-info alert-dismissible reveal" role="alert">
        <i class="fa-solid fa-circle-info"></i>
        <span><?php echo htmlspecialchars($_SESSION['info']); unset($_SESSION['info']); ?></span>
        <button type="button" class="alert-close" onclick="this.parentElement.remove();" aria-label="Close">&times;</button>
    </div>
<?php endif; ?>
