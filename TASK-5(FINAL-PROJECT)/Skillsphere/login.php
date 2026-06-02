<?php
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';

$message = '';

if (isset($_POST['login'])) {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $message = 'CSRF token mismatch. Please try again.';
    } else {
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $user = fetchSingleSecure('SELECT * FROM users WHERE email = ? LIMIT 1', [$email]);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'] ?? 'student';
            $_SESSION['profile_image'] = $user['profile_image'] ?? 'default.png';

            if (isset($user['role']) && $user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: profile.php');
            }
            exit();
        }
        $message = $user ? 'Incorrect password' : 'User not found';
    }
}

$pageTitle = 'Login';
include 'includes/header.php';
?>

<div class="auth-page">
    <aside class="auth-page__brand">
        <div class="auth-page__brand-content">
            <h1>SkillSphere</h1>
            <p>Continue your learning journey on a platform built for modern engineers and creators.</p>
            <img class="auth-page__illustration" src="assets/images/illustrations/auth-panel.png" alt="" width="400" height="320">
            <div class="auth-page__features">
                <div class="auth-page__feature"><i class="fa-solid fa-shield-halved"></i> Secure authentication</div>
                <div class="auth-page__feature"><i class="fa-solid fa-chart-line"></i> Track your progress</div>
                <div class="auth-page__feature"><i class="fa-solid fa-certificate"></i> Earn certificates</div>
            </div>
        </div>
    </aside>

    <main class="auth-page__form-wrap">
        <div class="auth-card">
            <div class="auth-card__logo">Skill<span>Sphere</span></div>
            <h2 class="auth-card__title">Welcome back</h2>
            <p class="auth-card__subtitle">Sign in to access your courses and dashboard.</p>

            <?php if (!empty($message)): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="POST" id="loginForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <div class="form-floating">
                    <input type="email" name="email" id="email" class="form-control" placeholder=" " required autocomplete="email">
                    <label for="email">Email address</label>
                </div>

                <div class="form-floating input-icon-wrap">
                    <input type="password" name="password" id="password" class="form-control" placeholder=" " required autocomplete="current-password">
                    <label for="password">Password</label>
                    <button type="button" class="input-icon-btn toggle-password" data-target="password" aria-label="Show password">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>

                <div class="flex justify-between items-center" style="margin-bottom: 1.25rem;">
                    <label class="form-check">
                        <input type="checkbox" name="remember">
                        Remember me
                    </label>
                    <div class="auth-forgot" style="margin: 0;">
                        <a href="forgot-password.php">Forgot password?</a>
                    </div>
                </div>

                <button type="submit" name="login" class="auth-btn">Sign in</button>
            </form>

            <div class="auth-divider"><span>or continue with</span></div>

            <div class="social-login">
                <button type="button" class="social-btn"><i class="fa-brands fa-google"></i> Google</button>
                <button type="button" class="social-btn"><i class="fa-brands fa-github"></i> GitHub</button>
            </div>

            <p class="auth-card__footer">
                Don't have an account? <a href="register.php">Create one</a>
            </p>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>

