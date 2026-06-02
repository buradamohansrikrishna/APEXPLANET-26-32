<?php

// =========================================
// SKILLSPHERE RESET PASSWORD
// reset-password.php
// =========================================

// =========================================
// REQUIRED FILES
// =========================================

require_once 'db.php';

require_once 'functions.php';
require_once 'helpers.php';

// =========================================
// MESSAGE
// =========================================

$message = '';

$validToken = false;

// =========================================
// TOKEN CHECK
// =========================================

if(isset($_GET['token'])){

    $token =
    sanitize($_GET['token']);

    $resetData = fetchSingleSecure(
        "SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1",
        [$token]
    );

    if($resetData){

        $validToken = true;

    }

    else{

        $message =
        "Invalid or expired reset link";

    }

}

// =========================================
// RESET PASSWORD
// =========================================

if(isset($_POST['update_password'])){

    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $message = "CSRF token mismatch. Please try again.";
    } else {
        $token =
        sanitize($_POST['token']);

        $password =
        $_POST['password'];

        $confirmPassword =
        $_POST['confirm_password'];

        // PASSWORD CHECK

        if(strlen($password) < 6){

            $message =
            "Password must be at least 6 characters";

        }

        elseif(

            $password !== $confirmPassword

        ){

            $message =
            "Passwords do not match";

        }

        else{

            // CHECK TOKEN

            $resetData = fetchSingleSecure(
                "SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1",
                [$token]
            );

            if($resetData){

                $email =
                $resetData['email'];

                // HASH PASSWORD

                $hashedPassword =

                password_hash(

                    $password,

                    PASSWORD_DEFAULT

                );

                // UPDATE USER PASSWORD

                dbQuery(
                    "UPDATE users SET password = ? WHERE email = ?",
                    [$hashedPassword, $email]
                );

                // DELETE TOKEN

                dbQuery(
                    "DELETE FROM password_resets WHERE token = ?",
                    [$token]
                );

                $_SESSION['flash_type'] =
                'success';

                $_SESSION['flash_message'] =

                'Password updated successfully';

                header(
                    'Location: login.php'
                );

                exit();

            }

            else{

                $message =
                "Invalid reset request";

            }

        }
    }

}

$pageTitle = "Reset Password";

include 'includes/header.php';

include 'includes/navbar.php';

?>

<!-- =========================================
     AUTH SECTION
========================================= -->

<section class="auth-layout">

    <div class="auth-container">

        <div class="auth-box fade">

            <!-- LOGO -->

            <div class="auth-logo">

                <h1>

                    Skill<span>Sphere</span>

                </h1>

            </div>

            <!-- TITLE -->

            <h2 class="auth-title">

                Reset Password

            </h2>

            <p class="auth-subtitle">

                Create a new secure password
                for your account.

            </p>

            <!-- MESSAGE -->

            <?php if($message != ''): ?>

                <div

                    class="alert <?php

                    echo $validToken

                    ?

                    'alert-danger'

                    :

                    'alert-warning';

                    ?>"

                >

                    <?php echo $message; ?>

                </div>

            <?php endif; ?>

            <!-- FORM -->

            <?php if($validToken): ?>

                <form
                    method="POST"
                    id="resetForm"
                    onsubmit="
                        return validateForm(
                            'resetForm'
                        );
                    "
                >

                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <!-- TOKEN -->

                    <input

                        type="hidden"

                        name="token"

                        value="<?php echo $token; ?>"

                    >

                    <!-- PASSWORD -->

                    <div class="form-group">

                        <label>

                            New Password

                        </label>

                        <div
                            style="
                                position:relative;
                            "
                        >

                            <input

                                type="password"

                                name="password"

                                id="password"

                                class="form-control"

                                placeholder="Enter new password"

                                required

                            >

                            <span

                                onclick="
                                    togglePassword(
                                        'password'
                                    )
                                "

                                style="
                                    position:absolute;
                                    right:18px;
                                    top:50%;
                                    transform:translateY(-50%);
                                    cursor:pointer;
                                    color:#94a3b8;
                                "

                            >

                                👁️

                            </span>

                        </div>

                    </div>

                    <!-- CONFIRM -->

                    <div class="form-group">

                        <label>

                            Confirm Password

                        </label>

                        <div
                            style="
                                position:relative;
                            "
                        >

                            <input

                                type="password"

                                name="confirm_password"

                                id="confirmPassword"

                                class="form-control"

                                placeholder="Confirm password"

                                required

                            >

                            <span

                                onclick="
                                    togglePassword(
                                        'confirmPassword'
                                    )
                                "

                                style="
                                    position:absolute;
                                    right:18px;
                                    top:50%;
                                    transform:translateY(-50%);
                                    cursor:pointer;
                                    color:#94a3b8;
                                "

                            >

                                👁️

                            </span>

                        </div>

                    </div>

                    <!-- BUTTON -->

                    <button

                        type="submit"

                        name="update_password"

                        class="auth-btn"

                    >

                        Update Password

                    </button>

                </form>

            <?php endif; ?>

            <!-- LOGIN LINK -->

            <div class="auth-links">

                <a href="login.php">

                    Back To Login

                </a>

            </div>

        </div>

    </div>

</section>

<?php

include 'includes/footer.php';

?>
