<?php

// =========================================
// SKILLSPHERE FORGOT PASSWORD
// forgot-password.php
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

// =========================================
// FORM SUBMIT
// =========================================

if(isset($_POST['reset_password'])){

    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $message = "CSRF token mismatch. Please try again.";
    } else {
        $email =
        sanitize($_POST['email']);

        // CHECK USER

        $user = fetchSingleSecure(
            "SELECT * FROM users WHERE email = ? LIMIT 1",
            [$email]
        );

        if($user){

            // GENERATE TOKEN

            $token =
            md5(

                uniqid(rand(),true)

            );

            // SAVE TOKEN (with 1 hour expiry)

            dbQuery(
                "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))",
                [$email, $token]
            );

            // RESET LINK

            $resetLink =

            BASE_URL . "reset-password.php?token=$token";

            $message =

            "Password reset link generated successfully.";

        }

        else{

            $message =
            "Email address not found.";

        }
    }

}

$pageTitle = "Forgot Password";

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

                Forgot Password

            </h2>

            <p class="auth-subtitle">

                Enter your email address
                to reset your password.

            </p>

            <!-- MESSAGE -->

            <?php if($message != ''): ?>

                <div

                    class="alert <?php

                    echo $message ==
                    'Email address not found.'

                    ?

                    'alert-danger'

                    :

                    'alert-success';

                    ?>"

                >

                    <?php echo $message; ?>

                </div>

            <?php endif; ?>

            <!-- FORM -->

            <form
                method="POST"
                id="forgotForm"
                onsubmit="
                    return validateForm(
                        'forgotForm'
                    );
                "
            >

                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <!-- EMAIL -->

                <div class="form-group">

                    <label>

                        Email Address

                    </label>

                    <input

                        type="email"

                        name="email"

                        class="form-control"

                        placeholder="Enter your email"

                        required

                    >

                </div>

                <!-- BUTTON -->

                <button

                    type="submit"

                    name="reset_password"

                    class="auth-btn"

                >

                    Send Reset Link

                </button>

            </form>

            <!-- LINKS -->

            <div class="auth-links">

                <a href="login.php">

                    Back To Login

                </a>

            </div>

            <!-- RESET LINK PREVIEW -->

            <?php if(isset($resetLink)): ?>

                <div
                    style="
                        margin-top:25px;
                        word-break:break-all;
                    "
                >

                    <small
                        style="
                            color:#94a3b8;
                        "
                    >

                        Reset Link:

                    </small>

                    <br><br>

                    <a

                        href="<?php echo $resetLink; ?>"

                        style="
                            color:#818cf8;
                            font-size:14px;
                        "

                    >

                        <?php echo $resetLink; ?>

                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

</section>

<?php

include 'includes/footer.php';

?>
