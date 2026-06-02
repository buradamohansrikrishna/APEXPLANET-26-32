<?php
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
require_once 'middleware.php';

requireLogin();

$userId = (int)$_SESSION['user_id'];
$user = fetchSingleSecure("SELECT * FROM users WHERE id = ? LIMIT 1", [$userId]);

$message = '';
if (isset($_POST['update_profile'])) {
    $name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $bio = sanitize($_POST['bio']);
    
    // File Upload handling
    $image = $user['profile_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name'];
        $fileExtension = strtolower(end(explode(".", $fileName)));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = time() . '_' . md5(uniqid()) . '.' . $fileExtension;
            $uploadDir = 'uploads/profiles/';
            if (move_uploaded_file($fileTmpPath, $uploadDir . $newFileName)) {
                $image = $newFileName;
                $_SESSION['profile_image'] = $image;
            }
        }
    }

    $res = dbQuery(
        "UPDATE users SET full_name = ?, phone = ?, bio = ?, profile_image = ? WHERE id = ?",
        [$name, $phone, $bio, $image, $userId]
    );

    if ($res) {
        $_SESSION['user_name'] = $name;
        $_SESSION['success'] = "Profile details updated successfully!";
        header("Location: profile.php");
        exit();
    } else {
        $message = "Database error updating profile settings";
    }
}

$pageTitle = 'Edit Profile';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container" style="margin-top:3rem; margin-bottom:6rem; max-width:600px;">
    <div class="card reveal" style="padding:2.5rem;">
        <h2>Edit Profile</h2>
        <p style="color:var(--text-tertiary); margin-bottom:2rem;">Modify your profile details and settings</p>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea name="bio" id="bio" class="form-control" rows="3"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="profile_image">Profile Picture</label>
                <input type="file" name="profile_image" id="profile_image" class="form-control">
            </div>

            <div style="margin-top:1.5rem;">
                <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                <a href="profile.php" class="btn btn-outline" style="margin-left:0.5rem;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
