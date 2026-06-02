<?php
session_start();
require_once '../config/db.php';
require_once '../includes/security.php';

$error   = '';
$success = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../user/dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $error = 'Invalid request. Please refresh and try again.';
    } else {
        $name             = trim($_POST['name'] ?? '');
        $email            = trim($_POST['email'] ?? '');
        $phone            = trim($_POST['phone'] ?? '');
        $password         = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate
        if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
            $error = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
            $error = 'Please enter a valid 10-digit phone number.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
            // Check duplicate email
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = 'An account with this email already exists. Please sign in.';
                $stmt->close();
            } else {
                $stmt->close();

                // Hash password and insert
                $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                $stmt = $conn->prepare(
                    "INSERT INTO users (name, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())"
                );
                $stmt->bind_param('ssss', $name, $email, $phone, $hashed_password);

                if ($stmt->execute()) {
                    $stmt->close();
                    header('Location: login.php?registered=1');
                    exit();
                } else {
                    $error = 'Registration failed. Please try again.';
                    $stmt->close();
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
    <meta name="description" content="Create your QuickBite account — premium food delivery at your fingertips.">
    <title>Register — QuickBite 2.0</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <style>
        :root {
            --neon-cyan: #00F7FF;
            --bg-dark: #050816;
            --bg-secondary: #0B1020;
            --bg-card: rgba(255, 255, 255, 0.04);
            --text-primary: #F0F4FF;
            --text-secondary: #94A3B8;
            --grad-primary: linear-gradient(135deg, #00F7FF, #3A86FF);
            --border-glass: rgba(255, 255, 255, 0.08);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* ── LEFT PANEL (Form side — mirrored) ── */
        .auth-left {
            width: 500px;
            min-width: 440px;
            background: var(--bg-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        .auth-left::after {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 1px; height: 100%;
            background: linear-gradient(to bottom, transparent, var(--neon-cyan), transparent);
            opacity: 0.3;
        }

        .auth-form-wrapper { width: 100%; max-width: 400px; }

        .form-header { margin-bottom: 28px; }
        .form-header h1 { font-size: 26px; font-weight: 700; margin-bottom: 6px; }
        .form-header p { font-size: 14px; color: var(--text-secondary); }

        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 28px 32px;
            backdrop-filter: blur(20px);
        }

        .alert {
            padding: 11px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 18px;
            display: flex; align-items: center; gap: 8px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

        .alert-error { background: rgba(255,77,109,0.12); border: 1px solid rgba(255,77,109,0.3); color: #FF4D6D; }
        .alert-success { background: rgba(0,217,126,0.12); border: 1px solid rgba(0,217,126,0.3); color: #00D97E; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12.5px; font-weight: 500; color: var(--text-secondary); margin-bottom: 6px; }

        .input-wrapper { position: relative; }
        .input-icon { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); font-size: 15px; color: var(--text-secondary); pointer-events: none; }

        .form-input {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border-glass);
            border-radius: 11px;
            padding: 12px 14px 12px 42px;
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .form-input:focus { border-color: var(--neon-cyan); box-shadow: 0 0 0 3px rgba(0,247,255,0.08); }
        .form-input::placeholder { color: rgba(148,163,184,0.45); }
        .form-input.input-error { border-color: #FF4D6D; }
        .form-input.input-success { border-color: #00D97E; }

        .toggle-password {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: var(--text-secondary);
            font-size: 15px; padding: 0; transition: color 0.2s;
        }
        .toggle-password:hover { color: var(--neon-cyan); }

        /* Password strength meter */
        .strength-meter { margin-top: 8px; }
        .strength-bars { display: flex; gap: 4px; margin-bottom: 4px; }
        .strength-bar {
            height: 3px; flex: 1; border-radius: 2px;
            background: rgba(255,255,255,0.1);
            transition: background 0.3s ease;
        }
        .strength-label { font-size: 11px; color: var(--text-secondary); }

        /* Match indicator */
        .match-indicator { font-size: 11.5px; margin-top: 5px; font-weight: 500; }
        .match-ok { color: #00D97E; }
        .match-fail { color: #FF4D6D; }

        .btn-primary {
            width: 100%; padding: 13px;
            background: var(--grad-primary); border: none; border-radius: 12px;
            color: #050816; font-family: 'Inter', sans-serif; font-size: 14.5px; font-weight: 700;
            cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 20px rgba(0,247,255,0.3); letter-spacing: 0.3px;
            margin-top: 4px;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,247,255,0.5); }
        .btn-primary:active { transform: translateY(0); }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .divider { display: flex; align-items: center; gap: 10px; margin: 20px 0; }
        .divider-line { flex: 1; height: 1px; background: var(--border-glass); }
        .divider-text { font-size: 12px; color: var(--text-secondary); }

        .login-link { text-align: center; font-size: 13.5px; color: var(--text-secondary); }
        .login-link a { color: var(--neon-cyan); text-decoration: none; font-weight: 600; }
        .login-link a:hover { opacity: 0.8; }

        /* ── RIGHT PANEL ── */
        .auth-right {
            flex: 1;
            background: linear-gradient(145deg, #050816 0%, #0a1628 50%, #0d1f3c 100%);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 60px 50px;
            position: relative; overflow: hidden;
        }

        .auth-right::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(0,247,255,0.1) 0%, transparent 70%);
            top: -80px; right: -100px; border-radius: 50%;
            animation: pulseGlow 4s ease-in-out infinite;
        }

        .auth-right::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(58,134,255,0.1) 0%, transparent 70%);
            bottom: -60px; left: -60px; border-radius: 50%;
            animation: pulseGlow 4s ease-in-out infinite 2s;
        }

        @keyframes pulseGlow { 0%,100%{transform:scale(1);opacity:.6} 50%{transform:scale(1.1);opacity:1} }

        .brand-logo { display: flex; align-items: center; gap: 14px; margin-bottom: 28px; position: relative; z-index: 1; }
        .brand-logo-icon { width: 56px; height: 56px; background: var(--grad-primary); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: 0 0 30px rgba(0,247,255,0.4); }
        .brand-logo-text { font-size: 32px; font-weight: 800; background: var(--grad-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

        .hero-emoji { font-size: 100px; margin: 28px 0; position: relative; z-index: 1; animation: floatEmoji 3s ease-in-out infinite; filter: drop-shadow(0 0 30px rgba(0,247,255,0.3)); }
        @keyframes floatEmoji { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-16px)} }

        .hero-headline { font-size: 26px; font-weight: 700; text-align: center; line-height: 1.4; margin-bottom: 12px; position: relative; z-index: 1; }
        .hero-subtext { font-size: 14px; color: var(--text-secondary); text-align: center; max-width: 300px; position: relative; z-index: 1; line-height: 1.6; }

        .perks-list { margin-top: 36px; display: flex; flex-direction: column; gap: 14px; position: relative; z-index: 1; }
        .perk-item { display: flex; align-items: center; gap: 12px; background: rgba(255,255,255,0.04); border: 1px solid var(--border-glass); border-radius: 12px; padding: 12px 18px; backdrop-filter: blur(10px); animation: slideUpFade 0.8s ease forwards; opacity: 0; }
        .perk-item:nth-child(1){animation-delay:.2s} .perk-item:nth-child(2){animation-delay:.4s} .perk-item:nth-child(3){animation-delay:.6s}
        @keyframes slideUpFade { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }
        .perk-icon { font-size: 24px; }
        .perk-text h4 { font-size: 14px; font-weight: 600; margin-bottom: 2px; }
        .perk-text p { font-size: 12px; color: var(--text-secondary); }

        @media (max-width:960px) { .auth-right { display: none; } .auth-left { width: 100%; min-width: unset; } }
        @media (max-width:480px) { .auth-left { padding: 24px 16px; } .glass-card { padding: 22px 18px; } .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<!-- LEFT PANEL: Registration Form -->
<div class="auth-left">
    <div class="auth-form-wrapper">
        <div class="form-header">
            <h1>Create account ✨</h1>
            <p>Join QuickBite and start ordering your favourite meals</p>
        </div>

        <div class="glass-card">
            <?php if ($error): ?>
                <div class="alert alert-error" role="alert">
                    <span>⚠️</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="registerForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <div class="input-wrapper">
                            <span class="input-icon">👤</span>
                            <input type="text" id="name" name="name" class="form-input" placeholder="John Doe"
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" autocomplete="name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div class="input-wrapper">
                            <span class="input-icon">📱</span>
                            <input type="tel" id="phone" name="phone" class="form-input" placeholder="9876543210"
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" autocomplete="tel" pattern="[0-9]{10}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon">✉️</span>
                        <input type="email" id="email" name="email" class="form-input" placeholder="you@example.com"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autocomplete="email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" id="password" name="password" class="form-input"
                               placeholder="Min. 8 characters" autocomplete="new-password" required>
                        <button type="button" class="toggle-password" id="togglePass1" aria-label="Toggle password">👁️</button>
                    </div>
                    <!-- Password Strength Meter -->
                    <div class="strength-meter" id="strengthMeter" style="display:none;">
                        <div class="strength-bars">
                            <div class="strength-bar" id="bar1"></div>
                            <div class="strength-bar" id="bar2"></div>
                            <div class="strength-bar" id="bar3"></div>
                            <div class="strength-bar" id="bar4"></div>
                        </div>
                        <span class="strength-label" id="strengthLabel"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔑</span>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input"
                               placeholder="Re-enter your password" autocomplete="new-password" required>
                        <button type="button" class="toggle-password" id="togglePass2" aria-label="Toggle password">👁️</button>
                    </div>
                    <div class="match-indicator" id="matchIndicator"></div>
                </div>

                <button type="submit" class="btn-primary" id="registerBtn">
                    Create My Account →
                </button>
            </form>

            <div class="divider">
                <div class="divider-line"></div>
                <span class="divider-text">Already have an account?</span>
                <div class="divider-line"></div>
            </div>

            <p class="login-link">
                <a href="login.php">← Sign in instead</a>
            </p>
        </div>
    </div>
</div>

<!-- RIGHT PANEL: Branding -->
<div class="auth-right">
    <div class="brand-logo">
        <div class="brand-logo-icon">🍔</div>
        <span class="brand-logo-text">QuickBite</span>
    </div>
    <p style="color:var(--text-secondary);font-size:15px;position:relative;z-index:1;">Premium Food Delivery Platform</p>

    <div class="hero-emoji">🥗</div>

    <h2 class="hero-headline">Your next great meal<br><span style="background:var(--grad-primary);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">is one tap away</span></h2>
    <p class="hero-subtext">Sign up today and unlock exclusive deals, real-time tracking, and 500+ restaurants at your fingertips.</p>

    <div class="perks-list">
        <div class="perk-item">
            <span class="perk-icon">🎁</span>
            <div class="perk-text">
                <h4>Welcome Bonus</h4>
                <p>Get ₹100 off your first order</p>
            </div>
        </div>
        <div class="perk-item">
            <span class="perk-icon">⚡</span>
            <div class="perk-text">
                <h4>Lightning Fast</h4>
                <p>Average delivery in 30 minutes</p>
            </div>
        </div>
        <div class="perk-item">
            <span class="perk-icon">🔒</span>
            <div class="perk-text">
                <h4>Secure & Trusted</h4>
                <p>100% safe transactions, always</p>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/main.js"></script>
<script>
    // ── Toggle password visibility ──
    function setupToggle(btnId, inputId) {
        const btn = document.getElementById(btnId);
        const inp = document.getElementById(inputId);
        if (btn && inp) {
            btn.addEventListener('click', () => {
                const show = inp.type === 'password';
                inp.type = show ? 'text' : 'password';
                btn.textContent = show ? '🙈' : '👁️';
            });
        }
    }
    setupToggle('togglePass1', 'password');
    setupToggle('togglePass2', 'confirm_password');

    // ── Password Strength Meter ──
    const passwordInput = document.getElementById('password');
    const strengthMeter = document.getElementById('strengthMeter');
    const strengthLabel = document.getElementById('strengthLabel');
    const bars = [
        document.getElementById('bar1'),
        document.getElementById('bar2'),
        document.getElementById('bar3'),
        document.getElementById('bar4'),
    ];

    const strengthColors = ['#FF4D6D', '#FF9F0A', '#FFD60A', '#00D97E'];
    const strengthLabels = ['Weak', 'Fair', 'Good', 'Strong'];

    function evaluateStrength(pwd) {
        let score = 0;
        if (pwd.length >= 8) score++;
        if (/[A-Z]/.test(pwd)) score++;
        if (/[0-9]/.test(pwd)) score++;
        if (/[^A-Za-z0-9]/.test(pwd)) score++;
        return score; // 0-4
    }

    passwordInput.addEventListener('input', function () {
        const val = this.value;
        if (!val) {
            strengthMeter.style.display = 'none';
            bars.forEach(b => b.style.background = 'rgba(255,255,255,0.1)');
            return;
        }
        strengthMeter.style.display = 'block';
        const score = evaluateStrength(val); // 1-4
        const idx   = Math.max(0, score - 1);

        bars.forEach((b, i) => {
            b.style.background = i < score ? strengthColors[idx] : 'rgba(255,255,255,0.1)';
        });
        strengthLabel.textContent = strengthLabels[idx];
        strengthLabel.style.color = strengthColors[idx];
    });

    // ── Real-time Password Match ──
    const confirmInput   = document.getElementById('confirm_password');
    const matchIndicator = document.getElementById('matchIndicator');

    confirmInput.addEventListener('input', function () {
        const pass    = passwordInput.value;
        const confirm = this.value;
        if (!confirm) { matchIndicator.textContent = ''; return; }

        if (pass === confirm) {
            matchIndicator.textContent = '✓ Passwords match';
            matchIndicator.className   = 'match-indicator match-ok';
        } else {
            matchIndicator.textContent = '✗ Passwords do not match';
            matchIndicator.className   = 'match-indicator match-fail';
        }
    });

    // ── Loading state on submit ──
    const registerForm = document.getElementById('registerForm');
    const registerBtn  = document.getElementById('registerBtn');
    if (registerForm && registerBtn) {
        registerForm.addEventListener('submit', function () {
            registerBtn.textContent = 'Creating account…';
            registerBtn.disabled = true;
        });
    }
</script>
</body>
</html>