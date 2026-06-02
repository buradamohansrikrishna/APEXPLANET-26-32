<?php
require_once '../auth.php';
require_once '../middleware.php';
requireRole('instructor');

$instructorId = (int)$_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$categories = fetchAllSecure('SELECT * FROM categories ORDER BY category_name ASC');

$message = '';
$errors = [];

// Handle course add
if (isset($_POST['add_course'])) {
    $category_id = (int)$_POST['category_id'];
    $title = sanitize($_POST['title']);
    $short_desc = sanitize($_POST['short_description']);
    $description = cleanHtml($_POST['description']);
    $level = sanitize($_POST['level']);
    $duration = sanitize($_POST['duration']);
    $price = (float)$_POST['price'];
    
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    if (empty($title)) $errors[] = 'Title is required';

    if (empty($errors)) {
        // Simple insert with draft status (needs admin approval)
        $res = dbQuery(
            "INSERT INTO courses (category_id, instructor_id, title, slug, short_description, description, level, duration, price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft')",
            [$category_id, $instructorId, $title, $slug, $short_desc, $description, $level, $duration, $price]
        );
        if ($res) {
            $_SESSION['success'] = 'Course submitted as Draft. Awaiting administrator approval.';
            header('Location: dashboard.php');
            exit();
        } else {
            $message = 'Database error adding course';
        }
    } else {
        $message = implode('<br>', $errors);
    }
}

// Handle course edit
if (isset($_POST['edit_course'])) {
    $category_id = (int)$_POST['category_id'];
    $title = sanitize($_POST['title']);
    $short_desc = sanitize($_POST['short_description']);
    $description = cleanHtml($_POST['description']);
    $level = sanitize($_POST['level']);
    $duration = sanitize($_POST['duration']);
    $price = (float)$_POST['price'];

    if (empty($title)) $errors[] = 'Title is required';

    if (empty($errors)) {
        $res = dbQuery(
            "UPDATE courses SET category_id = ?, title = ?, short_description = ?, description = ?, level = ?, duration = ?, price = ? WHERE id = ? AND instructor_id = ?",
            [$category_id, $title, $short_desc, $description, $level, $duration, $price, $id, $instructorId]
        );
        if ($res) {
            $_SESSION['success'] = 'Course details updated successfully';
            header('Location: dashboard.php');
            exit();
        } else {
            $message = 'Database error updating course';
        }
    } else {
        $message = implode('<br>', $errors);
    }
}

$course = null;
if ($action === 'edit' && $id > 0) {
    $course = fetchSingleSecure("SELECT * FROM courses WHERE id = ? AND instructor_id = ? LIMIT 1", [$id, $instructorId]);
    if (!$course) {
        $_SESSION['error'] = 'Course not found or unauthorized';
        header('Location: dashboard.php');
        exit();
    }
}

$pageTitle = 'Manage Course - Instructor Portal';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <!-- Navigation links for portal -->
    <div style="display:flex; gap:1rem; border-bottom:1px solid var(--border-default); padding-bottom:1rem; margin-bottom:2rem;">
        <a href="dashboard.php" class="btn btn-sm btn-outline">Courses List</a>
        <a href="students.php" class="btn btn-sm btn-outline">My Students</a>
        <a href="analytics.php" class="btn btn-sm btn-outline">Analytics</a>
        <a href="earnings.php" class="btn btn-sm btn-outline">Earnings Report</a>
        <a href="messages.php" class="btn btn-sm btn-outline">QA Messages</a>
        <a href="reviews.php" class="btn btn-sm btn-outline">Course Reviews</a>
    </div>

    <?php if ($action === 'add'): ?>
        <div class="card reveal" style="padding: 2rem;">
            <h2>Create New Course Draft</h2>
            <p style="color:var(--text-tertiary); margin-bottom: 2rem;">Drafts must be reviewed and approved by administrators before publication.</p>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label for="title">Course Title</label>
                    <input type="text" name="title" id="title" class="form-control" required placeholder="e.g. Master React 19">
                </div>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Course Price (₹)</label>
                        <input type="number" name="price" id="price" class="form-control" step="0.01" value="0.00">
                    </div>
                </div>
                <div class="form-group">
                    <label for="short_description">Short Summary</label>
                    <textarea name="short_description" id="short_description" class="form-control" rows="2" placeholder="Brief tagline or card introduction..."></textarea>
                </div>
                <div class="form-group">
                    <label for="description">Full Description Syllabus (HTML allowed)</label>
                    <textarea name="description" id="description" class="form-control" rows="5"></textarea>
                </div>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="level">Difficulty Level</label>
                        <select name="level" id="level" class="form-control">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="duration">Duration (e.g. 12 hours)</label>
                        <input type="text" name="duration" id="duration" class="form-control" required placeholder="e.g. 10 hours">
                    </div>
                </div>
                <button type="submit" name="add_course" class="btn btn-primary" style="margin-top: 1.5rem;">Submit Draft for Approval</button>
                <a href="dashboard.php" class="btn btn-outline" style="margin-top: 1.5rem; margin-left: 0.5rem;">Cancel</a>
            </form>
        </div>
    <?php elseif ($action === 'edit' && $course): ?>
        <div class="card reveal" style="padding: 2rem;">
            <h2>Edit Course Details</h2>
            <p style="color:var(--text-tertiary); margin-bottom: 2rem;">Modify details for <strong><?php echo htmlspecialchars($course['title']); ?></strong></p>

            <?php if (!empty($message)): ?>
                <div class="alert alert-danger"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label for="title">Course Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($course['title']); ?>" required>
                </div>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $course['category_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Course Price (₹)</label>
                        <input type="number" name="price" id="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($course['price']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="short_description">Short Summary</label>
                    <textarea name="short_description" id="short_description" class="form-control" rows="2"><?php echo htmlspecialchars($course['short_description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="description">Full Description Syllabus</label>
                    <textarea name="description" id="description" class="form-control" rows="5"><?php echo htmlspecialchars($course['description'] ?? ''); ?></textarea>
                </div>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="level">Difficulty Level</label>
                        <select name="level" id="level" class="form-control">
                            <option value="beginner" <?php echo $course['level'] === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                            <option value="intermediate" <?php echo $course['level'] === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="advanced" <?php echo $course['level'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="duration">Duration</label>
                        <input type="text" name="duration" id="duration" class="form-control" value="<?php echo htmlspecialchars($course['duration'] ?? ''); ?>" required>
                    </div>
                </div>
                <button type="submit" name="edit_course" class="btn btn-primary" style="margin-top: 1.5rem;">Save Changes</button>
                <a href="dashboard.php" class="btn btn-outline" style="margin-top: 1.5rem; margin-left: 0.5rem;">Cancel</a>
            </form>
        </div>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
