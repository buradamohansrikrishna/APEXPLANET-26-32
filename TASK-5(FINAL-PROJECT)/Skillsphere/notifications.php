<?php
$pageTitle = 'My Notifications';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
require_once 'middleware.php';

requireLogin();

$userId = (int)$_SESSION['user_id'];

// Mark all as read
if (isset($_GET['mark_all'])) {
    dbQuery("UPDATE notifications SET is_read = 1 WHERE user_id = ?", [$userId]);
    $_SESSION['success'] = "All notifications marked as read";
    header("Location: notifications.php");
    exit();
}

$notifications = fetchAllSecure("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 5rem; max-width: 700px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h1>Your Notifications</h1>
        <?php if (!empty($notifications)): ?>
            <a href="notifications.php?mark_all=1" class="btn btn-sm btn-outline">Mark all read</a>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card reveal" style="padding: 2rem;">
        <?php if (!empty($notifications)): ?>
            <div style="display:flex; flex-direction:column; gap:1.25rem;">
                <?php foreach ($notifications as $n): ?>
                    <div style="display:flex; gap:1rem; align-items:flex-start; border-bottom:1px solid var(--border-default); padding-bottom:1rem; opacity:<?php echo $n['is_read'] ? '0.6' : '1'; ?>;">
                        <div style="font-size:1.5rem; color:var(--brand-500); margin-top:0.25rem;"><i class="fa-solid fa-bell"></i></div>
                        <div>
                            <h3 style="font-size:1.1rem; margin-bottom:0.25rem;"><?php echo htmlspecialchars($n['title']); ?></h3>
                            <p style="color:var(--text-secondary);"><?php echo htmlspecialchars($n['message']); ?></p>
                            <time style="font-size:0.75rem; color:var(--text-tertiary); display:block; margin-top:0.5rem;"><?php echo timeAgo($n['created_at']); ?></time>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align:center; padding: 2rem;">
                <i class="fa-regular fa-bell-slash" style="font-size:3rem; color:var(--text-muted); margin-bottom:1rem;"></i>
                <p>You have no notifications at this time.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
