<?php
$pageTitle = 'Shopping Cart';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';

// Remove helper
if (isset($_GET['remove'])) {
    $removeId = (int)$_GET['remove'];
    if (isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], fn($id) => $id !== $removeId);
    }
    $_SESSION['success'] = "Course removed from cart";
    header("Location: cart.php");
    exit();
}

$cartItems = [];
$total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', $_SESSION['cart']));
    $cartItems = fetchAllSecure("SELECT * FROM courses WHERE id IN ($ids)");
    foreach ($cartItems as $item) {
        $total += (float)$item['price'];
    }
}
?>

<section class="page-header">
    <div class="container">
        <h1 class="fade">Shopping Cart</h1>
        <p class="fade">Review your selected courses before completing your purchase.</p>
    </div>
</section>

<div class="container" style="margin-top:3rem; margin-bottom:6rem; max-width:800px;">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card reveal" style="padding:2rem;">
        <?php if (!empty($cartItems)): ?>
            <div style="display:flex; flex-direction:column; gap:1.5rem; margin-bottom:2rem;">
                <?php foreach ($cartItems as $item): ?>
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-default); padding-bottom:1rem;">
                        <div>
                            <h3 style="font-size:1.25rem;"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <span style="font-size:0.875rem; color:var(--text-tertiary);"><?php echo htmlspecialchars($item['duration']); ?> · <?php echo htmlspecialchars(ucfirst($item['level'])); ?></span>
                        </div>
                        <div style="display:flex; align-items:center; gap:1.5rem;">
                            <strong style="font-size:1.25rem;">₹<?php echo number_format($item['price'], 0); ?></strong>
                            <a href="cart.php?remove=<?php echo $item['id']; ?>" style="color:var(--danger);" aria-label="Remove item"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="display:flex; justify-content:space-between; align-items:center; border-top:2px solid var(--border-strong); padding-top:1.5rem; margin-bottom:2rem;">
                <span style="font-size:1.25rem; font-weight:bold;">Total Amount:</span>
                <strong style="font-size:1.75rem; color:var(--brand-500);">₹<?php echo number_format($total, 2); ?></strong>
            </div>

            <div style="display:flex; justify-content:space-between;">
                <a href="courses.php" class="btn btn-outline">Keep Shopping</a>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            </div>
        <?php else: ?>
            <div style="text-align:center; padding:3rem 0;">
                <i class="fa-solid fa-cart-shopping" style="font-size:3rem; color:var(--text-muted); margin-bottom:1rem;"></i>
                <h3>Your cart is empty</h3>
                <p style="margin-top:0.5rem; margin-bottom:1.5rem;">Find a premium course to start learning.</p>
                <a href="courses.php" class="btn btn-primary">Explore Courses</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
