<?php
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
require_once 'middleware.php';

// Force login to checkout
requireLogin();

$userId = (int)$_SESSION['user_id'];
$cartItems = [];
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', $_SESSION['cart']));
    $cartItems = fetchAllSecure("SELECT * FROM courses WHERE id IN ($ids)");
    foreach ($cartItems as $item) {
        $total += (float)$item['price'];
    }
} else {
    header("Location: cart.php");
    exit();
}

$message = '';
if (isset($_POST['pay_btn'])) {
    // Generate mock transaction ID
    $txId = 'TXN-' . strtoupper(bin2hex(random_bytes(6)));
    $method = sanitize($_POST['payment_method'] ?? 'UPI');
    
    $success = true;
    foreach ($cartItems as $item) {
        $courseId = (int)$item['id'];
        
        // Insert payment record
        $payRes = dbQuery(
            "INSERT INTO payments (user_id, course_id, amount, payment_method, transaction_id, payment_status, paid_at) VALUES (?, ?, ?, ?, ?, 'success', NOW())",
            [$userId, $courseId, $item['price'], $method, $txId]
        );
        
        // Insert enrollment record
        $enrollRes = dbQuery(
            "INSERT INTO enrollments (user_id, course_id, payment_status, enrolled_at) VALUES (?, ?, 'paid', NOW()) ON DUPLICATE KEY UPDATE payment_status='paid'",
            [$userId, $courseId]
        );
        
        // Increment student count
        dbQuery("UPDATE courses SET total_students = total_students + 1 WHERE id = ?", [$courseId]);
        
        // Add system notification
        dbQuery(
            "INSERT INTO notifications (user_id, title, message) VALUES (?, 'Course Enrolled Successfully', ?)",
            [$userId, "Congratulations! You have enrolled in " . $item['title']]
        );
    }
    
    // Clear cart
    $_SESSION['cart'] = [];
    $_SESSION['success'] = "Payment successful! Your courses are now unlocked.";
    header("Location: student/dashboard.php");
    exit();
}

$pageTitle = 'Secure Checkout';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <h1 class="fade">Secure Checkout</h1>
        <p class="fade">Complete your purchase using credit card or UPI options.</p>
    </div>
</section>

<div class="container" style="margin-top:3rem; margin-bottom:6rem; max-width:600px;">
    <div class="card reveal" style="padding:2.5rem;">
        <h3 style="margin-bottom:1.5rem;">Purchase Summary</h3>
        <ul style="list-style:none; padding:0; display:flex; flex-direction:column; gap:0.75rem; margin-bottom:1.5rem;">
            <?php foreach ($cartItems as $item): ?>
                <li style="display:flex; justify-content:space-between;">
                    <span><?php echo htmlspecialchars($item['title']); ?></span>
                    <strong>₹<?php echo number_format($item['price'], 0); ?></strong>
                </li>
            <?php endforeach; ?>
            <li style="display:flex; justify-content:space-between; border-top:1px solid var(--border-default); padding-top:1rem; margin-top:1rem; font-size:1.25rem;">
                <strong>Grand Total:</strong>
                <strong style="color:var(--brand-500);">₹<?php echo number_format($total, 2); ?></strong>
            </li>
        </ul>

        <form method="POST" style="margin-top:2rem; display:flex; flex-direction:column; gap:1.5rem;">
            <div class="form-group">
                <label>Select Payment Method</label>
                <div style="display:flex; gap:1.5rem; margin-top:0.5rem;">
                    <label class="form-check" style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="radio" name="payment_method" value="UPI" checked>
                        <span>UPI / QR</span>
                    </label>
                    <label class="form-check" style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="radio" name="payment_method" value="Card">
                        <span>Credit / Debit Card</span>
                    </label>
                </div>
            </div>

            <button type="submit" name="pay_btn" class="btn btn-primary" style="width:100%; justify-content:center;">Pay ₹<?php echo number_format($total, 2); ?></button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
