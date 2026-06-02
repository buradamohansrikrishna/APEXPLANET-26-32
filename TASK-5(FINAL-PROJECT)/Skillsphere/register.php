<?php
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';

$message = '';

if (isset($_POST['register'])) {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $message = 'CSRF token mismatch. Please try again.';
    } else {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        if (empty($name) || empty($email) || empty($password)) {
            $message = 'All fields are required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Invalid email address';
        } elseif (strlen($password) < 6) {
            $message = 'Password must be at least 6 characters';
        } elseif ($password !== $confirmPassword) {
            $message = 'Passwords do not match';
        } else {
            $check = fetchSingleSecure('SELECT id FROM users WHERE email = ? LIMIT 1', [$email]);
            if ($check) {
                $message = 'Email already registered';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $insertQuery = dbQuery(
                    "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'student')",
                    [$name, $email, $hashedPassword]
                );
                if ($insertQuery) {
                    $_SESSION['flash_type'] = 'success';
                    $_SESSION['flash_message'] = 'Registration successful. Please login.';
                    header('Location: login.php');
                    exit();
                }
                $message = 'Registration failed';
            }
        }
    }
}

$pageTitle = 'Register';
include 'includes/header.php';
?>

<div class="auth-page">
    <aside class="auth-page__brand">
        <div class="auth-page__brand-content">
            <h1>Join SkillSphere</h1>
            <p>Start learning with structured paths, real projects, and a premium experience from day one.</p>
            <img class="auth-page__illustration" src="assets/images/illustrations/auth-panel.png" alt="" width="400" height="320">
            <div class="auth-page__features">
                <div class="auth-page__feature"><i class="fa-solid fa-rocket"></i> Free to get started</div>
                <div class="auth-page__feature"><i class="fa-solid fa-book-open"></i> 250+ courses</div>
                <div class="auth-page__feature"><i class="fa-solid fa-users"></i> Expert community</div>
            </div>
        </div>
    </aside>

    <main class="auth-page__form-wrap">
        <div class="auth-card">
            <div class="auth-card__logo">Skill<span>Sphere</span></div>
            <h2 class="auth-card__title">Create your account</h2>
            <p class="auth-card__subtitle">Join thousands of learners upgrading their careers.</p>

            <?php if (!empty($message)): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="POST" id="registerForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <div class="form-floating">
                    <input type="text" name="name" id="name" class="form-control" placeholder=" " required autocomplete="name">
                    <label for="name">Full name</label>
                </div>

                <div class="form-floating">
                    <input type="email" name="email" id="reg-email" class="form-control" placeholder=" " required autocomplete="email">
                    <label for="reg-email">Email address</label>
                </div>

                <div class="form-floating input-icon-wrap">
                    <input type="password" name="password" id="reg-password" class="form-control" placeholder=" " required autocomplete="new-password" minlength="6">
                    <label for="reg-password">Password</label>
                    <button type="button" class="input-icon-btn toggle-password" data-target="reg-password" aria-label="Show password">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>

                <div class="form-floating input-icon-wrap">
                    <input type="password" name="confirm_password" id="confirm-password" class="form-control" placeholder=" " required autocomplete="new-password">
                    <label for="confirm-password">Confirm password</label>
                    <button type="button" class="input-icon-btn toggle-password" data-target="confirm-password" aria-label="Show password">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>

                <button type="submit" name="register" class="auth-btn">Create account</button>
            </form>

            <div class="auth-divider"><span>or continue with</span></div>

            <div class="social-login">
                <button type="button" class="social-btn"><i class="fa-brands fa-google"></i> Google</button>
                <button type="button" class="social-btn"><i class="fa-brands fa-github"></i> GitHub</button>
            </div>

            <p class="auth-card__footer">
                Already have an account? <a href="login.php">Sign in</a>
            </p>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>

