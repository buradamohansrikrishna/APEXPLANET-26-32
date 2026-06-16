<?php
// Determine base path dynamically
$depth = substr_count($_SERVER['PHP_SELF'], '/') - 2;
$base  = str_repeat('../', max($depth, 0));
if (strpos($_SERVER['PHP_SELF'], 'QUICKBITE') !== false) {
    $parts = explode('QUICKBITE', $_SERVER['PHP_SELF']);
    $depth = substr_count($parts[1], '/') - 1;
    $base  = $depth > 0 ? str_repeat('../', $depth) : '';
}

if (session_status() === PHP_SESSION_NONE) session_start();
$loggedIn   = isset($_SESSION['user_id']);
$userName   = $_SESSION['user_name'] ?? '';
$userId     = $_SESSION['user_id']   ?? 0;

// Cart count (DB-backed when logged in)
$cartCount = 0;
if ($loggedIn && isset($conn)) {
    $cs = $conn->prepare("SELECT SUM(quantity) as t FROM cart WHERE user_id = ?");
    $cs->bind_param('i', $userId);
    $cs->execute();
    $cartCount = (int)($cs->get_result()->fetch_assoc()['t'] ?? 0);
}

// Current page for active link
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar" id="mainNavbar">
    <div class="navbar-inner">

        <!-- Logo -->
        <a href="<?= $base ?>index.php" class="logo">Quick<span style="color:#FF4747">Bite</span></a>

        <!-- Nav Links -->
        <ul class="nav-links" id="navLinks">
            <li><a href="<?= $base ?>index.php" class="<?= $currentPage==='index.php'?'active':'' ?>">Home</a></li>
            <li><a href="<?= $base ?>user/restaurants.php" class="<?= $currentPage==='restaurants.php'?'active':'' ?>">Restaurants</a></li>
            <li><a href="<?= $base ?>about.php" class="<?= $currentPage==='about.php'?'active':'' ?>">About</a></li>
            <li><a href="<?= $base ?>contact.php" class="<?= $currentPage==='contact.php'?'active':'' ?>">Contact</a></li>
            <?php if($loggedIn): ?>
            <li><a href="<?= $base ?>user/dashboard.php" class="<?= $currentPage==='dashboard.php'?'active':'' ?>">Dashboard</a></li>
            <?php endif; ?>
        </ul>

        <!-- Actions -->
        <div class="nav-actions">

            <?php if($loggedIn): ?>
            <!-- Cart Button -->
            <a href="<?= $base ?>user/cart.php" class="cart-btn" data-open-cart>
                🛒
                <?php if($cartCount > 0): ?>
                <span class="cart-badge"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>

            <!-- User Menu -->
            <div style="position:relative" id="userMenu">
                <button onclick="document.getElementById('userDropdown').classList.toggle('open')"
                    style="background:var(--bg-glass);border:1px solid var(--bg-glass-border);border-radius:var(--radius-full);
                    padding:6px 12px;display:flex;align-items:center;gap:8px;color:var(--text-secondary);cursor:pointer;font-size:0.85rem;">
                    <span style="background:var(--grad-primary);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:700">
                        <?= strtoupper(substr($userName,0,1)) ?>
                    </span>
                    <?= htmlspecialchars($userName) ?>
                    <span>▾</span>
                </button>
                <div id="userDropdown" style="position:absolute;top:calc(100%+10px);right:0;
                    background:var(--bg-secondary);border:1px solid var(--bg-glass-border);
                    border-radius:var(--radius-md);min-width:180px;z-index:200;
                    opacity:0;visibility:hidden;transform:translateY(-8px);
                    transition:all 0.25s ease;padding:8px;">
                    <a href="<?= $base ?>user/dashboard.php" style="display:flex;align-items:center;gap:10px;padding:10px 12px;
                        border-radius:8px;color:var(--text-secondary);font-size:0.88rem;transition:0.2s">
                        👤 Dashboard
                    </a>
                    <a href="<?= $base ?>user/orders.php" style="display:flex;align-items:center;gap:10px;padding:10px 12px;
                        border-radius:8px;color:var(--text-secondary);font-size:0.88rem;transition:0.2s">
                        📦 My Orders
                    </a>
                    <a href="<?= $base ?>user/cart.php" style="display:flex;align-items:center;gap:10px;padding:10px 12px;
                        border-radius:8px;color:var(--text-secondary);font-size:0.88rem;transition:0.2s">
                        🛒 Cart
                    </a>
                    <hr style="border:none;border-top:1px solid var(--bg-glass-border);margin:6px 0">
                    <a href="<?= $base ?>auth/logout.php" style="display:flex;align-items:center;gap:10px;padding:10px 12px;
                        border-radius:8px;color:var(--danger-red);font-size:0.88rem;transition:0.2s">
                        🚪 Logout
                    </a>
                </div>
            </div>
            <?php else: ?>
            <a href="<?= $base ?>auth/login.php" class="btn btn-secondary btn-sm">Login</a>
            <a href="<?= $base ?>auth/register.php" class="btn btn-primary btn-sm">Sign Up</a>
            <?php endif; ?>

            <!-- Hamburger -->
            <button class="hamburger" id="hamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>

    </div>
</nav>

<!-- Input for CSRF in AJAX requests -->
<input type="hidden" name="csrf_token" value="<?= function_exists('csrf_token') ? csrf_token() : '' ?>">

<style>
#userDropdown.open { opacity:1 !important; visibility:visible !important; transform:translateY(0) !important; }
#userDropdown a:hover { background:var(--bg-glass); color:var(--text-primary) !important; }
</style>