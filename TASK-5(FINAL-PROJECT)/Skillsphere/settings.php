<?php

// =========================================
// SKILLSPHERE SETTINGS PAGE
// settings.php
// =========================================

$pageTitle = "Settings";

require_once 'auth.php';

include 'includes/header.php';

include 'includes/navbar.php';

require_once 'db.php'; // loads functions.php (uploadImage, deleteImage)

// =========================================
// USER DATA
// =========================================

$user_id = intval($_SESSION['user_id']);

$user = fetchSingleSecure("SELECT * FROM users WHERE id = ? LIMIT 1", [$user_id]);

// =========================================
// MESSAGE
// =========================================

$message = '';

// =========================================
// UPDATE PROFILE
// =========================================

if(isset($_POST['update_profile'])){

    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $message = "CSRF token mismatch. Please try again.";
    } else {
        $name =
        sanitize($_POST['name']);

        $email =
        sanitize($_POST['email']);

        $profileImage =
        $user['profile_image'];

        // IMAGE UPLOAD using central helper

        if(
            isset($_FILES['profile_image']) &&
            $_FILES['profile_image']['error'] === 0
        ){
            $uploaded = uploadImage($_FILES['profile_image'], PROFILE_UPLOAD);
            if ($uploaded !== false) {
                // Delete old custom profile image
                if ($user['profile_image'] !== 'default.png') {
                    deleteImage(PROFILE_UPLOAD . $user['profile_image']);
                }
                $profileImage = $uploaded;
            }
        }

        // UPDATE QUERY - uses full_name instead of name

        $update = dbQuery(
            "UPDATE users SET full_name = ?, email = ?, profile_image = ? WHERE id = ?",
            [$name, $email, $profileImage, $user_id]
        );

        if($update){

            $_SESSION['user_name'] =
            $name;

            $_SESSION['profile_image'] =
            $profileImage;

            $message =
            "Profile updated successfully";

            // REFRESH USER DATA

            $user = fetchSingleSecure("SELECT * FROM users WHERE id = ? LIMIT 1", [$user_id]);

        }

        else{

            $message =
            "Failed to update profile";

        }
    }

}

// =========================================
// CHANGE PASSWORD
// =========================================

if(isset($_POST['change_password'])){

    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $message = "CSRF token mismatch. Please try again.";
    } else {
        $currentPassword =
        $_POST['current_password'];

        $newPassword =
        $_POST['new_password'];

        $confirmPassword =
        $_POST['confirm_password'];

        // VERIFY CURRENT PASSWORD

        if(

            password_verify(

                $currentPassword,

                $user['password']

            )

        ){

            if($newPassword === $confirmPassword){

                if(strlen($newPassword) >= 6){

                    $hashedPassword =

                    password_hash(

                        $newPassword,

                        PASSWORD_DEFAULT

                    );

                    dbQuery(
                        "UPDATE users SET password = ? WHERE id = ?",
                        [$hashedPassword, $user_id]
                    );

                    $message =
                    "Password changed successfully";

                }

                else{

                    $message =
                    "Password must be at least 6 characters";

                }

            }

            else{

                $message =
                "Passwords do not match";

            }

        }

        else{

            $message =
            "Current password is incorrect";

        }
    }

}

?>

<!-- =========================================
     PAGE HEADER
========================================= -->

<section class="page-header">

    <div class="container">

        <h1 class="fade">

            Account Settings

        </h1>

        <p class="fade">

            Manage your profile,
            security,
            and account preferences.

        </p>

    </div>

</section>

<!-- =========================================
     SETTINGS SECTION
========================================= -->

<section class="about-section">

    <div class="container">

        <?php if($message != ''): ?>

            <div

                class="alert <?php

                echo strpos(

                    strtolower($message),

                    'success'

                ) !== false

                ?

                'alert-success'

                :

                'alert-danger';

                ?>"

                style="
                    margin-bottom:30px;
                "

            >

                <?php echo $message; ?>

            </div>

        <?php endif; ?>

        <div class="about-grid">

            <!-- PROFILE SETTINGS -->

            <div class="card reveal">

                <h2
                    style="
                        margin-bottom:30px;
                    "
                >

                    Profile Settings

                </h2>

                <!-- IMAGE -->

                <div
                    style="
                        text-align:center;
                        margin-bottom:30px;
                    "
                >

                    <img

                        src="uploads/profiles/<?php

                        echo htmlspecialchars($user['profile_image'] ?: 'default.png');

                        ?><?php
                        $profileFile = $user['profile_image'] ?: 'default.png';
                        $profilePath = __DIR__ . '/uploads/profiles/' . $profileFile;
                        if (is_file($profilePath)) {
                            echo '?v=' . (int) filemtime($profilePath);
                        }
                        ?>"

                        id="preview"

                        alt="Profile"

                        style="
                            width:120px;
                            height:120px;
                            border-radius:50%;
                            object-fit:cover;
                            border:4px solid #4f46e5;
                            margin:auto;
                        "

                    >

                </div>

                <!-- FORM -->

                <form
                    method="POST"
                    enctype="multipart/form-data"
                    id="profileForm"
                    onsubmit="
                        return validateForm(
                            'profileForm'
                        );
                    "
                >

                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <!-- NAME -->

                    <div class="form-group">

                        <label>

                            Full Name

                        </label>

                        <input

                            type="text"

                            name="name"

                            class="form-control"

                            value="<?php echo $user['full_name']; ?>"

                            required

                        >

                    </div>

                    <!-- EMAIL -->

                    <div class="form-group">

                        <label>

                            Email Address

                        </label>

                        <input

                            type="email"

                            name="email"

                            class="form-control"

                            value="<?php echo $user['email']; ?>"

                            required

                        >

                    </div>

                    <!-- IMAGE -->

                    <div class="form-group">

                        <label>

                            Profile Image

                        </label>

                        <input

                            type="file"

                            name="profile_image"

                            class="form-control"

                            accept="image/*"

                            onchange="
                                previewImage(
                                    this,
                                    'preview'
                                )
                            "

                        >

                    </div>

                    <!-- BUTTON -->

                    <button

                        type="submit"

                        name="update_profile"

                        class="btn btn-block"

                    >

                        Update Profile

                    </button>

                </form>

            </div>

            <!-- PASSWORD SETTINGS -->

            <div class="card reveal">

                <h2
                    style="
                        margin-bottom:30px;
                    "
                >

                    Security Settings

                </h2>

                <form
                    method="POST"
                    id="passwordForm"
                    onsubmit="
                        return validateForm(
                            'passwordForm'
                        );
                    "
                >

                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <!-- CURRENT -->

                    <div class="form-group">

                        <label>

                            Current Password

                        </label>

                        <input

                            type="password"

                            name="current_password"

                            class="form-control"

                            required

                        >

                    </div>

                    <!-- NEW -->

                    <div class="form-group">

                        <label>

                            New Password

                        </label>

                        <input

                            type="password"

                            name="new_password"

                            class="form-control"

                            required

                        >

                    </div>

                    <!-- CONFIRM -->

                    <div class="form-group">

                        <label>

                            Confirm Password

                        </label>

                        <input

                            type="password"

                            name="confirm_password"

                            class="form-control"

                            required

                        >

                    </div>

                    <!-- BUTTON -->

                    <button

                        type="submit"

                        name="change_password"

                        class="btn btn-block"

                    >

                        Change Password

                    </button>

                </form>

            </div>

        </div>

    </div>

</section>

<?php

include 'includes/footer.php';

?>
