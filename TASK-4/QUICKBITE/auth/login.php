<?php
session_start();
require_once '../config/db.php';
require_once '../includes/security.php';

$error = '';
$success = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../user/dashboard.php');
    exit();
}
if (isset($_SESSION['admin_id'])) {
    header('Location: ../admin/dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Rate limiting: 5 attempts per 15 minutes
        $rate_key = 'login_' . md5($_SERVER['REMOTE_ADDR'] . $email);
        if (!check_rate_limit($rate_key, 5, 900)) {
            $error = 'Too many login attempts. Please wait 15 minutes before trying again.';
        } elseif (empty($email) || empty($password)) {
            $error = 'Please fill in all fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } else {
            // Check admins table first
            $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();
            $stmt->close();

            if ($admin && password_verify($password, $admin['password'])) {
                // Admin login
                session_regenerate_id(true);
                $_SESSION['admin_id']   = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_email']= $admin['email'];
                increment_rate_limit($rate_key); // optional: clear on success you may skip
                header('Location: ../admin/dashboard.php');
                exit();
            } else {
                // Check users table
                $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();

                if ($user && password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']    = $user['id'];
                    $_SESSION['user_name']  = $user['name'];
                    $_SESSION['user_email'] = $user['email'];

                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, time() + 86400 * 30, '/', '', false, true);
                    }

                    header('Location: ../user/dashboard.php');
                    exit();
                } else {
                    increment_rate_limit($rate_key);
                    $error = 'Invalid email or password. Please check your credentials.';
                }
            }
        }
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign in to QuickBite — your premium food delivery platform.">
    <title>Login — QuickBite 2.0</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <style>
        :root {
            --neon-cyan: #FF5A00;
            --bg-dark: #F8FAFC;
            --bg-secondary: #FFFFFF;
            --bg-card: #FFFFFF;
            --text-primary: #0F172A;
            --text-secondary: #475569;
            --grad-primary: linear-gradient(135deg, #FF5A00, #FF8C42);
            --border-glass: #E2E8F0;
            --error-red: #FF4D6D;
            --success-green: #00D97E;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: #0F172A;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* ── LEFT PANEL ── */
        .auth-left {
            flex: 1;
            background: linear-gradient(145deg, #F8FAFC 0%, #0a1628 40%, #0d1f3c 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 50px;
            position: relative;
            overflow: hidden;
            color: #FFFFFF;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(255,71,71,0.12) 0%, transparent 70%);
            top: -100px; left: -100px;
            border-radius: 50%;
            animation: pulseGlow 4s ease-in-out infinite;
        }

        .auth-left::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(58,134,255,0.1) 0%, transparent 70%);
            bottom: -80px; right: -80px;
            border-radius: 50%;
            animation: pulseGlow 4s ease-in-out infinite 2s;
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.1); opacity: 1; }
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 32px;
            position: relative;
            z-index: 1;
        }

        .brand-logo-icon {
            width: 56px; height: 56px;
            background: var(--grad-primary);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px;
            box-shadow: 0 0 30px rgba(255,90,0,0.4);
        }

        .brand-logo-text {
            font-size: 32px;
            font-weight: 800;
            background: var(--grad-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-tagline {
            font-size: 16px;
            color: #E2E8F0;
            font-weight: 400;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 1;
        }

        .hero-emoji {
            font-size: 110px;
            margin: 36px 0;
            position: relative;
            z-index: 1;
            animation: floatEmoji 3s ease-in-out infinite;
            filter: drop-shadow(0 0 30px rgba(255,90,0,0.3));
        }

        @keyframes floatEmoji {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-16px); }
        }

        .hero-headline {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            line-height: 1.35;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
            color: #FFFFFF;
        }

        .hero-subtext {
            font-size: 15px;
            color: #CBD5E1;
            text-align: center;
            max-width: 320px;
            position: relative;
            z-index: 1;
        }

        .floating-stats {
            display: flex;
            gap: 20px;
            margin-top: 44px;
            position: relative;
            z-index: 1;
        }

        .stat-pill {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border-glass);
            backdrop-filter: blur(12px);
            padding: 12px 20px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            animation: slideUpFade 0.8s ease forwards;
            opacity: 0;
        }

        .stat-pill:nth-child(1) { animation-delay: 0.2s; }
        .stat-pill:nth-child(2) { animation-delay: 0.4s; }
        .stat-pill:nth-child(3) { animation-delay: 0.6s; }

        @keyframes slideUpFade {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .stat-pill .stat-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--neon-cyan);
            box-shadow: 0 0 8px var(--neon-cyan);
        }

        /* ── RIGHT PANEL ── */
        .auth-right {
            width: 480px;
            min-width: 420px;
            background: var(--bg-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        .auth-right::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 1px; height: 100%;
            background: linear-gradient(to bottom, transparent, var(--neon-cyan), transparent);
            opacity: 0.3;
        }

        .auth-form-wrapper {
            width: 100%;
            max-width: 380px;
        }

        .form-header {
            margin-bottom: 36px;
        }

        .form-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-header p {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

        .alert-error {
            background: rgba(255, 77, 109, 0.12);
            border: 1px solid rgba(255, 77, 109, 0.3);
            color: #FF4D6D;
        }

        .alert-success {
            background: rgba(0, 217, 126, 0.12);
            border: 1px solid rgba(0, 217, 126, 0.3);
            color: #00D97E;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            color: var(--text-secondary);
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            background: #F8FAFC;
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            padding: 13px 16px 13px 44px;
            color: #0F172A;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--neon-cyan);
            box-shadow: 0 0 0 3px rgba(255,90,0,0.15);
        }

        .form-input::placeholder { color: rgba(148,163,184,0.7); }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 16px;
            padding: 0;
            transition: color 0.2s;
        }

        .toggle-password:hover { color: var(--neon-cyan); }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--neon-cyan);
            cursor: pointer;
        }

        .checkbox-wrapper span {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .forgot-link {
            font-size: 13px;
            color: var(--neon-cyan);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
        }

        .forgot-link:hover { opacity: 0.8; }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: var(--grad-primary);
            border: none;
            border-radius: 12px;
            color: #FFFFFF;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
            box-shadow: 0 4px 20px rgba(255,90,0,0.3);
            letter-spacing: 0.3px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(255,90,0,0.5);
        }

        .btn-primary:active { transform: translateY(0); }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
        }

        .divider-line { flex: 1; height: 1px; background: var(--border-glass); }
        .divider-text { font-size: 12px; color: var(--text-secondary); white-space: nowrap; }

        .register-link {
            text-align: center;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .register-link a {
            color: var(--neon-cyan);
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .register-link a:hover { opacity: 0.8; }

        /* Responsive */
        @media (max-width: 900px) {
            .auth-left { display: none; }
            .auth-right { width: 100%; min-width: unset; }
        }

        @media (max-width: 480px) {
            .auth-right { padding: 24px 16px; }
            .glass-card { padding: 24px 20px; }
        }
    </style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="auth-left">
    <div class="brand-logo">
        <div class="brand-logo-icon">🍔</div>
        <span class="brand-logo-text">QuickBite</span>
    </div>
    <p class="brand-tagline">Premium Food Delivery Platform</p>

    <div class="hero-emoji">🍕</div>

    <h2 class="hero-headline">Crave it. Order it.<br><span style="background:var(--grad-primary);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Devour it.</span></h2>
    <p class="hero-subtext">Join thousands of food lovers enjoying restaurant-quality meals delivered to their door.</p>

    <div class="floating-stats">
        <div class="stat-pill">
            <span class="stat-dot"></span>
            <span>500+ Restaurants</span>
        </div>
        <div class="stat-pill">
            <span class="stat-dot" style="background:#3A86FF;box-shadow:0 0 8px #3A86FF;"></span>
            <span>30 Min Avg</span>
        </div>
        <div class="stat-pill">
            <span class="stat-dot" style="background:#7C3AED;box-shadow:0 0 8px #7C3AED;"></span>
            <span>50k+ Orders</span>
        </div>
    </div>
</div>

<!-- RIGHT PANEL -->
<div class="auth-right">
    <div class="auth-form-wrapper">
        <div class="form-header">
            <h1>Welcome back 👋</h1>
            <p>Sign in to your QuickBite account to continue ordering</p>
        </div>

        <div class="glass-card">
            <?php if ($error): ?>
                <div class="alert alert-error" role="alert">
                    <span>⚠️</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success" role="alert">
                    <span>✅</span>
                    <span>Account created! Please sign in.</span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon">✉️</span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="you@example.com"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            autocomplete="email"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="toggle-password" id="togglePassword" aria-label="Show/hide password">
                            👁️
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="remember" id="remember" <?= isset($_POST['remember']) ? 'checked' : '' ?>>
                        <span>Remember me</span>
                    </label>
                    <a href="forgot_password.php" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-primary" id="loginBtn">
                    Sign In to QuickBite
                </button>
            </form>

            <div class="divider">
                <div class="divider-line"></div>
                <span class="divider-text">New to QuickBite?</span>
                <div class="divider-line"></div>
            </div>

            <p class="register-link">
                Don't have an account? <a href="register.php">Create one free →</a>
            </p>
        </div>
    </div>
</div>

<script src="../assets/js/main.js"></script>
<script>
    // Toggle password visibility
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function() {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            toggleBtn.textContent = isPassword ? '🙈' : '👁️';
        });
    }

    // Loading state on submit
    const loginForm = document.getElementById('loginForm');
    const loginBtn  = document.getElementById('loginBtn');
    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function() {
            loginBtn.textContent = 'Signing in…';
            loginBtn.disabled = true;
            loginBtn.style.opacity = '0.75';
        });
    }
</script>
</body>
</html>