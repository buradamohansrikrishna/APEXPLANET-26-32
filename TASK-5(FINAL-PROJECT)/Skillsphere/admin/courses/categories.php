<?php
require_once '../../auth.php';
requireAdmin();

$errors = [];
if (isset($_POST['add_category'])) {
    $name = sanitize($_POST['category_name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    if (empty($name)) {
        $errors[] = 'Category name is required';
    } else {
        $duplicate = fetchSingleSecure("SELECT id FROM categories WHERE category_name = ? OR slug = ? LIMIT 1", [$name, $slug]);
        if ($duplicate) {
            $errors[] = 'Category name or slug already exists';
        } else {
            $res = dbQuery("INSERT INTO categories (category_name, slug) VALUES (?, ?)", [$name, $slug]);
            if ($res) {
                $_SESSION['success'] = "Category added successfully";
                header("Location: categories.php");
                exit();
            } else {
                $errors[] = 'Error saving category';
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $res = dbQuery("DELETE FROM categories WHERE id = ?", [$delId]);
    if ($res) {
        $_SESSION['success'] = "Category deleted successfully";
    } else {
        $_SESSION['error'] = "Failed to delete category";
    }
    header("Location: categories.php");
    exit();
}

$categoriesList = fetchAllSecure("SELECT * FROM categories ORDER BY category_name ASC");

$adminTitle = 'Categories';
$adminPage = 'categories';
$adminHeading = 'Course categories';
$adminSubheading = 'Structure the course catalog';
$adminIllustration = '../assets/images/admin-courses.svg';
$adminHeroTitle = 'Syllabus taxonomies';
$adminHeroText = 'Manage taxonomy categories and URL slugs for courses.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<div class="grid grid-3">
    <div class="admin-panel reveal" style="grid-column: span 1;">
        <div class="admin-panel__head"><h3>Add Category</h3></div>
        <div class="admin-panel__body">
            <?php if(!empty($errors)): ?>
                <div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="category_name">Category Name</label>
                    <input type="text" name="category_name" id="category_name" class="form-control" required placeholder="e.g. Mobile Development">
                </div>
                <button type="submit" name="add_category" class="admin-btn admin-btn--primary" style="margin-top:1rem; width:100%;">Create Category</button>
            </form>
        </div>
    </div>

    <div class="admin-panel reveal" style="grid-column: span 2;">
        <div class="admin-panel__head"><h3>All Categories</h3></div>
        <div class="admin-panel__body">
            <div class="admin-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categoriesList as $c): ?>
                        <tr>
                            <td><?php echo $c['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($c['category_name']); ?></strong></td>
                            <td><code><?php echo htmlspecialchars($c['slug']); ?></code></td>
                            <td>
                                <a href="categories.php?delete=<?php echo $c['id']; ?>" class="delete-btn" onclick="return confirm('Delete category? All courses in this category will set category_id to NULL.');">Delete</a>
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
