<?php
require_once '../../auth.php';
requireAdmin();

$jsonFile = '../../storage/cache/announcements.json';
if (!file_exists($jsonFile)) {
    $defaults = [
        [
            'id' => 1,
            'title' => 'SkillSphere v2.0 Platform Upgrade',
            'body' => 'Welcome to the complete enterprise project rebuild. Interactive dashboards and AI learning features are now live.',
            'date' => '2026-05-24'
        ]
    ];
    file_put_contents($jsonFile, json_encode($defaults, JSON_PRETTY_PRINT));
}

$announcements = json_decode(file_get_contents($jsonFile), true);

if (isset($_POST['add'])) {
    $title = sanitize($_POST['title']);
    $body = sanitize($_POST['body']);
    
    $new = [
        'id' => count($announcements) > 0 ? max(array_column($announcements, 'id')) + 1 : 1,
        'title' => $title,
        'body' => $body,
        'date' => date('Y-m-d')
    ];
    $announcements[] = $new;
    file_put_contents($jsonFile, json_encode($announcements, JSON_PRETTY_PRINT));
    $_SESSION['success'] = 'Announcement posted!';
    header('Location: announcements.php');
    exit();
}

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $announcements = array_filter($announcements, fn($a) => $a['id'] !== $delId);
    file_put_contents($jsonFile, json_encode(array_values($announcements), JSON_PRETTY_PRINT));
    $_SESSION['success'] = 'Announcement deleted!';
    header('Location: announcements.php');
    exit();
}

$adminTitle = 'Announcements';
$adminPage = 'announcements';
$adminHeading = 'System announcements';
$adminSubheading = 'Manage dashboard alerts';
$adminIllustration = '../assets/images/admin-courses.svg';
$adminHeroTitle = 'Global alerts';
$adminHeroText = 'Publish platform bulletins, system updates, and discounts directly to student dashboards.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="grid grid-3">
    <div class="admin-panel reveal" style="grid-column: span 1;">
        <div class="admin-panel__head"><h3>Create Alert</h3></div>
        <div class="admin-panel__body">
            <form method="POST">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="body">Alert Body</label>
                    <textarea name="body" id="body" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" name="add" class="admin-btn admin-btn--primary" style="margin-top:1rem; width:100%;">Broadcast Announcement</button>
            </form>
        </div>
    </div>

    <div class="admin-panel reveal" style="grid-column: span 2;">
        <div class="admin-panel__head"><h3>Active Broadcasts</h3></div>
        <div class="admin-panel__body">
            <div class="admin-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Broadcast</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($announcements as $a): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($a['title']); ?></strong><br>
                                <small><?php echo htmlspecialchars($a['body']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($a['date']); ?></td>
                            <td>
                                <a href="announcements.php?delete=<?php echo $a['id']; ?>" class="delete-btn" onclick="return confirm('Delete announcement?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
