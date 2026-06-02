<?php
require_once '../../auth.php';
requireAdmin();

$jsonFile = '../../storage/cache/blog.json';
if (!file_exists($jsonFile)) {
    $defaults = [
        [
            'id' => 1,
            'title' => 'Getting Started with Next.js 15',
            'excerpt' => 'A guide to server components, routing patterns, and data loading in modern Next.js.',
            'content' => '<p>Next.js 15 brings powerful updates to Server Components and caching. Let\'s explore how to configure layouts, routes, and data loading safely in standard SaaS setups.</p>',
            'author' => 'Dr. Sravani Devi',
            'date' => '2026-05-20'
        ],
        [
            'id' => 2,
            'title' => 'Demystifying gRPC in Go microservices',
            'excerpt' => 'Why gRPC is preferred over REST APIs for internal service communication.',
            'content' => '<p>gRPC uses Protocol Buffers over HTTP/2, facilitating streaming RPCs and high performance. Here is how to configure grpc-go endpoints in microservices.</p>',
            'author' => 'Venkata Srinivas',
            'date' => '2026-05-18'
        ]
    ];
    file_put_contents($jsonFile, json_encode($defaults, JSON_PRETTY_PRINT));
}

$posts = json_decode(file_get_contents($jsonFile), true);

if (isset($_POST['add_post'])) {
    $title = sanitize($_POST['title']);
    $excerpt = sanitize($_POST['excerpt']);
    $content = cleanHtml($_POST['content']);
    $author = $_SESSION['user_name'];
    $date = date('Y-m-d');
    
    $newPost = [
        'id' => count($posts) > 0 ? max(array_column($posts, 'id')) + 1 : 1,
        'title' => $title,
        'excerpt' => $excerpt,
        'content' => $content,
        'author' => $author,
        'date' => $date
    ];
    $posts[] = $newPost;
    file_put_contents($jsonFile, json_encode($posts, JSON_PRETTY_PRINT));
    $_SESSION['success'] = 'Blog post created!';
    header('Location: manage-blog.php');
    exit();
}

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $posts = array_filter($posts, fn($p) => $p['id'] !== $delId);
    file_put_contents($jsonFile, json_encode(array_values($posts), JSON_PRETTY_PRINT));
    $_SESSION['success'] = 'Blog post deleted!';
    header('Location: manage-blog.php');
    exit();
}

$adminTitle = 'Manage Blog';
$adminPage = 'content';
$adminHeading = 'Platform Blog';
$adminSubheading = 'Manage educational posts';
$adminIllustration = '../assets/images/admin-courses.svg';
$adminHeroTitle = 'Content creation';
$adminHeroText = 'Publish technology tutorials, engineering write-ups, and product updates.';

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
        <div class="admin-panel__head"><h3>Create Post</h3></div>
        <div class="admin-panel__body">
            <form method="POST">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="excerpt">Excerpt</label>
                    <textarea name="excerpt" id="excerpt" class="form-control" rows="2" required></textarea>
                </div>
                <div class="form-group">
                    <label for="content">Content (HTML)</label>
                    <textarea name="content" id="content" class="form-control" rows="5" required></textarea>
                </div>
                <button type="submit" name="add_post" class="admin-btn admin-btn--primary" style="margin-top:1rem; width:100%;">Publish Post</button>
            </form>
        </div>
    </div>

    <div class="admin-panel reveal" style="grid-column: span 2;">
        <div class="admin-panel__head"><h3>Blog Posts</h3></div>
        <div class="admin-panel__body">
            <div class="admin-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $p): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($p['title']); ?></strong><br>
                                <small><?php echo htmlspecialchars($p['excerpt']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($p['author']); ?></td>
                            <td><?php echo htmlspecialchars($p['date']); ?></td>
                            <td>
                                <a href="manage-blog.php?delete=<?php echo $p['id']; ?>" class="delete-btn" onclick="return confirm('Delete post?');">Delete</a>
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
