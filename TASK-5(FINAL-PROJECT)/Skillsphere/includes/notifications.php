<!-- =========================================
     SKILLSPHERE NOTIFICATIONS DROPDOWN / SECTION
     includes/notifications.php
========================================= -->
<?php
require_once __DIR__ . '/../db.php';
$notificationsList = [];
if (isset($_SESSION['user_id'])) {
    $notificationsList = fetchAllSecure(
        "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5",
        [$_SESSION['user_id']]
    );
}
?>
<div class="notifications-panel" id="notificationsDropdown" style="display: none;">
    <div class="notifications-panel__header">
        <h3>Notifications</h3>
        <?php if (!empty($notificationsList)): ?>
            <button class="btn-text" onclick="markAllNotificationsRead()">Mark all as read</button>
        <?php endif; ?>
    </div>
    <div class="notifications-panel__body">
        <?php if (!empty($notificationsList)): ?>
            <?php foreach ($notificationsList as $notif): ?>
                <div class="notification-item <?php echo $notif['is_read'] ? 'read' : 'unread'; ?>">
                    <div class="notification-item__icon">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div class="notification-item__content">
                        <h4><?php echo htmlspecialchars($notif['title']); ?></h4>
                        <p><?php echo htmlspecialchars($notif['message']); ?></p>
                        <time><?php echo date('d M Y, h:i A', strtotime($notif['created_at'])); ?></time>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="notification-empty">
                <i class="fa-regular fa-bell-slash"></i>
                <p>No new notifications</p>
            </div>
        <?php endif; ?>
    </div>
    <div class="notifications-panel__footer">
        <a href="notifications.php">See all notifications</a>
    </div>
</div>
