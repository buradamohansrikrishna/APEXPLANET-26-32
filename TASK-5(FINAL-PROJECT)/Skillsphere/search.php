<?php
$pageTitle = 'Search Courses';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';

$query = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$level = isset($_GET['level']) ? sanitize($_GET['level']) : '';
$catId = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$sql = "SELECT c.*, cat.category_name, u.full_name AS instructor_name
        FROM courses c
        LEFT JOIN categories cat ON c.category_id = cat.id
        LEFT JOIN users u ON c.instructor_id = u.id
        WHERE 1=1";
$params = [];
$types = "";

if (!empty($query)) {
    $sql .= " AND (c.title LIKE ? OR c.description LIKE ?)";
    $params[] = "%$query%";
    $params[] = "%$query%";
    $types .= "ss";
}

if (!empty($level)) {
    $sql .= " AND c.level = ?";
    $params[] = $level;
    $types .= "s";
}

if ($catId > 0) {
    $sql .= " AND c.category_id = ?";
    $params[] = $catId;
    $types .= "i";
}

$sql .= " ORDER BY c.created_at DESC";
$courses = fetchAllSecure($sql, $params, $types);
$categories = fetchAllSecure("SELECT * FROM categories ORDER BY category_name ASC");
?>

<section class="page-header">
    <div class="container">
        <h1 class="fade">Search Results</h1>
        <p class="fade">Found <?php echo count($courses); ?> courses matching your search criteria.</p>
    </div>
</section>

<div class="container" style="margin-top:2rem; margin-bottom:4rem;">
    <div class="grid grid-4">
        <!-- Sidebar Filters -->
        <aside style="grid-column: span 1;">
            <div class="card" style="padding: 1.5rem;">
                <h3>Filters</h3>
                <form method="GET" style="margin-top: 1rem; display:flex; flex-direction:column; gap:1.25rem;">
                    <div class="form-group">
                        <label for="q">Keywords</label>
                        <input type="text" name="q" id="q" class="form-control" value="<?php echo htmlspecialchars($query); ?>" placeholder="Search...">
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select name="category" id="category" class="form-control">
                            <option value="0">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] === $catId ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="level">Level</label>
                        <select name="level" id="level" class="form-control">
                            <option value="">All Levels</option>
                            <option value="beginner" <?php echo $level === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                            <option value="intermediate" <?php echo $level === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="advanced" <?php echo $level === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Apply Filters</button>
                </form>
            </div>
        </aside>

        <!-- Course Listings -->
        <main style="grid-column: span 3;">
            <div class="course-grid" style="grid-template-columns: repeat(2, 1fr);">
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <?php include 'includes/course-card.php'; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card text-center" style="grid-column: span 2; padding:3rem;">
                        <h3>No matching courses found</h3>
                        <p style="margin-top: 0.5rem;">Try modifying your keyword search or adjusting filters.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
