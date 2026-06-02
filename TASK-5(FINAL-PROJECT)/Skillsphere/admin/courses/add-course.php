<?php
require_once '../../auth.php';
requireAdmin();

$categories = fetchAllSecure('SELECT * FROM categories ORDER BY category_name ASC');
$instructors = fetchAllSecure("SELECT * FROM users WHERE role = 'instructor' ORDER BY full_name ASC");

$message = '';
$errors = [];

if (isset($_POST['add_course'])) {
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
    $thumbnail = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['thumbnail']['tmp_name'];
        $fileName = $_FILES['thumbnail']['name'];
        $fileSize = $_FILES['thumbnail']['size'];
        $fileType = $_FILES['thumbnail']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'svg', 'webp');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = time() . '_' . md5(uniqid()) . '.' . $fileExtension;
            $uploadFileDir = '../../uploads/thumbnails/';
            if(!is_dir($uploadFileDir)){
                mkdir($uploadFileDir, 0777, true);
            }
            $dest_path = $uploadFileDir . $newFileName;
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $thumbnail = $newFileName;
            }
        }
    }

    if (empty($errors)) {
        $res = dbQuery(
            "INSERT INTO courses (category_id, instructor_id, title, slug, short_description, description, thumbnail, level, language, duration, price, discount_price, requirements, learning_outcomes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$category_id, $instructor_id, $title, $slug, $short_desc, $description, $thumbnail, $level, $language, $duration, $price, $discount_price, $requirements, $learning_outcomes, $status]
        );
        if ($res) {
            $_SESSION['success'] = 'Course created successfully';
            header('Location: manage-courses.php');
            exit();
        } else {
            $message = 'Database error creating course';
        }
    } else {
        $message = implode('<br>', $errors);
    }
}

$adminTitle = 'Add Course';
$adminPage = 'courses';
$adminHeading = 'Add course';
$adminSubheading = 'Create a new course entry';
$adminIllustration = '../assets/images/admin-courses.svg';
$adminHeroTitle = 'Provision course';
$adminHeroText = 'Define course title, level, target pricing, resources, and map to an instructor.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<div class="admin-panel reveal">
    <div class="admin-panel__head"><h3>Course Structure Details</h3></div>
    <div class="admin-panel__body">
        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="title">Course Title</label>
                <input type="text" name="title" id="title" class="form-control" required placeholder="e.g. Master React 19 and Next.js 15">
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
                    <label for="instructor_id">Instructor</label>
                    <select name="instructor_id" id="instructor_id" class="form-control">
                        <?php foreach ($instructors as $inst): ?>
                            <option value="<?php echo $inst['id']; ?>"><?php echo htmlspecialchars($inst['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="short_description">Short Summary (Max 500 chars)</label>
                <textarea name="short_description" id="short_description" class="form-control" rows="2"></textarea>
            </div>

            <div class="form-group">
                <label for="description">Full Description HTML</label>
                <textarea name="description" id="description" class="form-control" rows="6"></textarea>
            </div>

            <div class="grid grid-4">
                <div class="form-group">
                    <label for="level">Level</label>
                    <select name="level" id="level" class="form-control">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="language">Language</label>
                    <input type="text" name="language" id="language" class="form-control" value="English">
                </div>
                <div class="form-group">
                    <label for="duration">Duration (e.g. 15 hours)</label>
                    <input type="text" name="duration" id="duration" class="form-control" required placeholder="e.g. 18 hours">
                </div>
                <div class="form-group">
                    <label for="thumbnail">Thumbnail File</label>
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control">
                </div>
            </div>

            <div class="grid grid-3">
                <div class="form-group">
                    <label for="price">Regular Price (₹)</label>
                    <input type="number" name="price" id="price" class="form-control" step="0.01" value="0.00">
                </div>
                <div class="form-group">
                    <label for="discount_price">Discount Price (₹)</label>
                    <input type="number" name="discount_price" id="discount_price" class="form-control" step="0.01" value="0.00">
                </div>
                <div class="form-group">
                    <label for="status">Publication Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label for="requirements">Prerequisites (One per line)</label>
                    <textarea name="requirements" id="requirements" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="learning_outcomes">Learning Outcomes (One per line)</label>
                    <textarea name="learning_outcomes" id="learning_outcomes" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" name="add_course" class="admin-btn admin-btn--primary">Save Course</button>
                <a href="manage-courses.php" class="admin-btn admin-btn--outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
