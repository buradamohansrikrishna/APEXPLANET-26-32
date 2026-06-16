<?php
$depth = 0;
if (isset($_SERVER['PHP_SELF'])) {
    $parts = explode('QUICKBITE', $_SERVER['PHP_SELF']);
    if (isset($parts[1])) $depth = substr_count($parts[1], '/') - 1;
}
$base = $depth > 0 ? str_repeat('../', $depth) : '';
?>
<footer class="footer">
    <div class="footer-grid">

        <!-- Brand -->
        <div class="footer-brand">
            <a href="<?= $base ?>index.php" class="logo">Quick<span style="color:#FF4747">Bite</span></a>
            <p>Order Fast, Eat Fresh 🍔<br>
            QuickBite is a modern food ordering platform designed for fast, easy, and seamless restaurant ordering experiences.</p>
            <div class="footer-social">
                <a href="#" class="social-link" title="Twitter">🐦</a>
                <a href="#" class="social-link" title="Instagram">📸</a>
                <a href="#" class="social-link" title="Facebook">📘</a>
                <a href="#" class="social-link" title="LinkedIn">💼</a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?= $base ?>index.php">Home</a></li>
                <li><a href="<?= $base ?>user/restaurants.php">Restaurants</a></li>
                <li><a href="<?= $base ?>user/menu.php">Menu</a></li>
                <li><a href="<?= $base ?>about.php">About Us</a></li>
                <li><a href="<?= $base ?>contact.php">Contact</a></li>
            </ul>
        </div>

        <!-- Account -->
        <div class="footer-col">
            <h4>Account</h4>
            <ul>
                <li><a href="<?= $base ?>auth/login.php">Login</a></li>
                <li><a href="<?= $base ?>auth/register.php">Register</a></li>
                <li><a href="<?= $base ?>user/dashboard.php">Dashboard</a></li>
                <li><a href="<?= $base ?>user/orders.php">My Orders</a></li>
                <li><a href="<?= $base ?>user/cart.php">Cart</a></li>
            </ul>
        </div>

        <!-- Contact -->
        <div class="footer-col">
            <h4>Contact</h4>
            <div class="footer-contact-item"><span>📧</span><span>support@quickbite.com</span></div>
            <div class="footer-contact-item"><span>📞</span><span>+91 98765 43210</span></div>
            <div class="footer-contact-item"><span>📍</span><span>Hyderabad, India</span></div>
            <div class="footer-contact-item"><span>⏰</span><span>24/7 Support</span></div>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© <?= date('Y') ?> <strong style="color:var(--neon-cyan)">QuickBite</strong>. All Rights Reserved. Made with ❤️ in India.</p>
    </div>
</footer>