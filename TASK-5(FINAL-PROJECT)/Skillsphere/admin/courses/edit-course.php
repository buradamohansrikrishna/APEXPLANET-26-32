<?php
require_once '../../auth.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$course = fetchSingleSecure('SELECT * FROM courses WHERE id = ? LIMIT 1', [$id]);

if (!$course) {
    $_SESSION['error'] = 'Course not found';
    header('Location: manage-courses.php');
    exit();
}

$categories = fetchAllSecure('SELECT * FROM categories ORDER BY category_name ASC');
$instructors = fetchAllSecure("SELECT * FROM users WHERE role = 'instructor' ORDER BY full_name ASC");

$message = '';
$errors = [];

if (isset($_POST['edit_course'])) {
    $category_id = (int)$_POST['category_id'];
    $instructor_id = (int)$_POST['instructor_id'];
    $title = sanitize($_POST['title']);
    $short_desc = sanitize($_POST['short_description']);
    $description = cleanHtml($_POST['description']);
    $level = sanitize($_POST['level']);
    $language = sanitize($_POST['language']);
    $duration = sanitize($_POST['duration']);
    $price = (float)$_POST['price'];
    $discount_price = (float)$_POST['discount_price'];
    $requirements = sanitize($_POST['requirements']);
    $learning_outcomes = sanitize($_POST['learning_outcomes']);
    $status = sanitize($_POST['status']);

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    if (empty($title)) $errors[] = 'Course title is required';

    // File Upload handling
    $thumbnail = $course['thumbnail'];
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['thumbnail']['tmp_name'];
        $fileName = $_FILES['thumbnail']['name'];
        $fileExtension = strtolower(end(explode(".", $fileName)));
        
        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'svg', 'webp');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = time() . '_' . md5(uniqid()) . '.' . $fileExtension;
            $uploadFileDir = '../../uploads/thumbnails/';
            $dest_path = $uploadFileDir . $newFileName;
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $thumbnail = $newFileName;
            }
        }
    }

    if (empty($errors)) {
        $res = dbQuery(
            "UPDATE courses SET category_id = ?, instructor_id = ?, title = ?, slug = ?, short_description = ?, description = ?, thumbnail = ?, level = ?, language = ?, duration = ?, price = ?, discount_price = ?, requirements = ?, learning_outcomes = ?, status = ? WHERE id = ?",
            [$category_id, $instructor_id, $title, $slug, $short_desc, $description, $thumbnail, $level, $language, $duration, $price, $discount_price, $requirements, $learning_outcomes, $status, $id]
        );
        if ($res) {
            $_SESSION['success'] = 'Course updated successfully';
            header('Location: manage-courses.php');
            exit();
        } else {
            $message = 'Database error updating course';
        }
    } else {
        $message = implode('<br>', $errors);
    }
}

$adminTitle = 'Edit Course';
$adminPage = 'courses';
$adminHeading = 'Edit course';
$adminSubheading = 'Modify course data fields';
$adminIllustration = '../assets/images/admin-courses.svg';
$adminHeroTitle = 'Modify syllabus';
$adminHeroText = 'Edit curriculum structures, prerequisites, description texts, and pricing.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<div class="admin-panel reveal">
    <div class="admin-panel__head"><h3>Edit info for <?php echo htmlspecialchars($course['title']); ?></h3></div>
    <div class="admin-panel__body">
        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
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
                    <label for="instructor_id">Instructor</label>
                    <select name="instructor_id" id="instructor_id" class="form-control">
                        <?php foreach ($instructors as $inst): ?>
                            <option value="<?php echo $inst['id']; ?>" <?php echo $inst['id'] == $course['instructor_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($inst['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="short_description">Short Summary (Max 500 chars)</label>
                <textarea name="short_description" id="short_description" class="form-control" rows="2"><?php echo htmlspecialchars($course['short_description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="description">Full Description HTML</label>
                <textarea name="description" id="description" class="form-control" rows="6"><?php echo htmlspecialchars($course['description'] ?? ''); ?></textarea>
            </div>

            <div class="grid grid-4">
                <div class="form-group">
                    <label for="level">Level</label>
                    <select name="level" id="level" class="form-control">
                        <option value="beginner" <?php echo $course['level'] === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                        <option value="intermediate" <?php echo $course['level'] === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                        <option value="advanced" <?php echo $course['level'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="language">Language</label>
                    <input type="text" name="language" id="language" class="form-control" value="<?php echo htmlspecialchars($course['language'] ?? 'English'); ?>">
                </div>
                <div class="form-group">
                    <label for="duration">Duration</label>
                    <input type="text" name="duration" id="duration" class="form-control" value="<?php echo htmlspecialchars($course['duration'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="thumbnail">Thumbnail File <small>(Leave empty to keep current)</small></label>
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control">
                </div>
            </div>

            <div class="grid grid-3">
                <div class="form-group">
                    <label for="price">Regular Price (₹)</label>
                    <input type="number" name="price" id="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($course['price']); ?>">
                </div>
                <div class="form-group">
                    <label for="discount_price">Discount Price (₹)</label>
                    <input type="number" name="discount_price" id="discount_price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($course['discount_price']); ?>">
                </div>
                <div class="form-group">
                    <label for="status">Publication Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="draft" <?php echo $course['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo $course['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                        <option value="archived" <?php echo $course['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label for="requirements">Prerequisites (One per line)</label>
                    <textarea name="requirements" id="requirements" class="form-control" rows="3"><?php echo htmlspecialchars($course['requirements'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="learning_outcomes">Learning Outcomes (One per line)</label>
                    <textarea name="learning_outcomes" id="learning_outcomes" class="form-control" rows="3"><?php echo htmlspecialchars($course['learning_outcomes'] ?? ''); ?></textarea>
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" name="edit_course" class="admin-btn admin-btn--primary">Save Changes</button>
                <a href="manage-courses.php" class="admin-btn admin-btn--outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
