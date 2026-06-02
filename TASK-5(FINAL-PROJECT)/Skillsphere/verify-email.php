<?php
$pageTitle = 'Verify Email';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';

$message = '';
$success = false;

$email = isset($_GET['email']) ? sanitize($_GET['email']) : '';
if (!empty($email)) {
    $res = dbQuery("UPDATE users SET email_verified = 1 WHERE email = ?", [$email]);
    if ($res) {
        $message = "Your email address has been successfully verified! You can now access all learning platform features.";
        $success = true;
    } else {
        $message = "Error verifying email. Please contact support.";
    }
} else {
    $message = "Invalid verification request.";
}
?>

<div class="container" style="margin-top:6rem; margin-bottom:8rem; max-width:600px; text-align:center;">
    <div class="card reveal" style="padding:3rem;">
        <?php if ($success): ?>
            <i class="fa-solid fa-circle-check" style="font-size:3.5rem; color:var(--success); margin-bottom:1.5rem;"></i>
            <h2>Email Verified!</h2>
        <?php else: ?>
            <i class="fa-solid fa-triangle-exclamation" style="font-size:3.5rem; color:var(--danger); margin-bottom:1.5rem;"></i>
            <h2>Verification Failed</h2>
        <?php endif; ?>
        <p style="color:var(--text-secondary); margin-top:1rem; margin-bottom:2rem;"><?php echo htmlspecialchars($message); ?></p>
        <a href="login.php" class="btn btn-primary">Sign In to Dashboard</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
