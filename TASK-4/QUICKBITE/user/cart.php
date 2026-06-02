<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

$user_id    = $_SESSION['user_id'];
$cart_items = get_cart_items($user_id, $conn);
$csrf_token = generate_csrf_token();

// ─── Handle quantity / remove via GET (fallback) ───────────────────────────
// (Main interactions are via AJAX in cart.js)

// Compute totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$delivery_fee   = $subtotal > 0 ? 40.00 : 0;
$tax_rate       = 0.05;
$tax            = $subtotal * $tax_rate;
$coupon_discount = 0;

if (!empty($_SESSION['applied_coupon'])) {
    $coupon_discount = $_SESSION['applied_coupon']['discount'] ?? 0;
}

$grand_total = $subtotal + $delivery_fee + $tax - $coupon_discount;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart 🛒 – QuickBite 2.0</title>
    <meta name="description" content="Review your cart items and proceed to checkout on QuickBite.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <style>
        :root{
            --neon-cyan:#00F7FF;--bg-dark:#050816;--bg-secondary:#0B1020;
            --bg-card:rgba(255,255,255,0.04);--text-primary:#F0F4FF;--text-secondary:#94A3B8;
            --border-glass:rgba(255,255,255,0.08);--neon-glow:0 0 20px rgba(0,247,255,0.3);
            --green:#00D084;--orange:#FF8C42;--red:#FF4545;
        }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:var(--bg-dark);color:var(--text-primary);min-height:100vh;}

        /* PAGE HEADER */
        .page-header{
            background:linear-gradient(135deg,#050816,#0a1a3e);
            padding:3rem 2rem 2.5rem;text-align:center;
            border-bottom:1px solid var(--border-glass);
        }
        .page-header h1{font-size:2.2rem;font-weight:800;margin-bottom:0.4rem;}
        .page-header p{color:var(--text-secondary);}

        /* LAYOUT */
        .cart-layout{max-width:1100px;margin:2.5rem auto 5rem;padding:0 2rem;display:grid;grid-template-columns:1fr 340px;gap:2rem;}
        @media(max-width:800px){.cart-layout{grid-template-columns:1fr;}}

        /* CART ITEM */
        .cart-item{
            background:var(--bg-card);border:1px solid var(--border-glass);
            border-radius:16px;padding:1.2rem;margin-bottom:1rem;
            display:flex;gap:1.2rem;align-items:center;
            transition:all 0.3s ease;
        }
        .cart-item:hover{border-color:rgba(0,247,255,0.2);}
        .item-img{width:80px;height:80px;border-radius:12px;object-fit:cover;flex-shrink:0;}
        .item-img-placeholder{width:80px;height:80px;border-radius:12px;background:linear-gradient(135deg,#0B1020,#0d1a40);display:flex;align-items:center;justify-content:center;font-size:2rem;flex-shrink:0;}
        .item-info{flex:1;min-width:0;}
        .item-name{font-size:0.98rem;font-weight:700;margin-bottom:0.2rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .item-cat{font-size:0.72rem;color:var(--neon-cyan);margin-bottom:0.5rem;}
        .item-price{font-size:0.85rem;color:var(--text-secondary);}
        .item-controls{display:flex;align-items:center;gap:0.5rem;margin-top:0.6rem;}
        .qty-btn{
            width:28px;height:28px;border-radius:8px;
            background:rgba(255,255,255,0.06);border:1px solid var(--border-glass);
            color:var(--text-primary);font-size:1rem;font-weight:700;cursor:pointer;
            display:flex;align-items:center;justify-content:center;transition:all 0.2s;
        }
        .qty-btn:hover{background:rgba(0,247,255,0.12);border-color:var(--neon-cyan);color:var(--neon-cyan);}
        .qty-display{
            width:36px;text-align:center;font-weight:700;font-size:0.95rem;
            background:rgba(255,255,255,0.04);border:1px solid var(--border-glass);
            border-radius:6px;padding:0.2rem;color:var(--text-primary);
        }
        .item-subtotal{font-size:1.05rem;font-weight:800;color:var(--neon-cyan);text-align:right;white-space:nowrap;}
        .remove-btn{
            width:32px;height:32px;border-radius:8px;
            background:rgba(255,69,69,0.1);border:1px solid rgba(255,69,69,0.2);
            color:var(--red);cursor:pointer;font-size:1rem;
            display:flex;align-items:center;justify-content:center;transition:all 0.2s;
            flex-shrink:0;
        }
        .remove-btn:hover{background:rgba(255,69,69,0.25);}

        /* ORDER SUMMARY */
        .order-summary{
            background:var(--bg-card);border:1px solid var(--border-glass);
            border-radius:18px;padding:1.5rem;position:sticky;top:90px;
            backdrop-filter:blur(20px);height:fit-content;
        }
        .summary-title{font-size:1.1rem;font-weight:700;margin-bottom:1.2rem;border-bottom:1px solid var(--border-glass);padding-bottom:0.8rem;}
        .summary-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:0.8rem;font-size:0.9rem;}
        .summary-row.discount{color:var(--green);}
        .summary-row.total{font-size:1.1rem;font-weight:800;border-top:1px solid var(--border-glass);padding-top:1rem;margin-top:0.5rem;}
        .summary-row span:last-child{font-weight:600;}
        .coupon-section{margin:1rem 0;padding:1rem;background:rgba(255,255,255,0.03);border:1px dashed rgba(0,247,255,0.2);border-radius:12px;}
        .coupon-label{font-size:0.8rem;color:var(--text-secondary);margin-bottom:0.6rem;font-weight:600;}
        .coupon-row{display:flex;gap:0.5rem;}
        .coupon-input{
            flex:1;padding:0.55rem 0.8rem;
            background:rgba(255,255,255,0.05);border:1px solid var(--border-glass);
            border-radius:8px;color:var(--text-primary);font-size:0.85rem;outline:none;
            transition:border-color 0.3s;
        }
        .coupon-input:focus{border-color:var(--neon-cyan);}
        .coupon-input::placeholder{color:var(--text-secondary);}
        .btn-apply{
            padding:0.55rem 0.9rem;background:rgba(0,247,255,0.12);
            border:1px solid rgba(0,247,255,0.3);border-radius:8px;
            color:var(--neon-cyan);font-size:0.82rem;font-weight:700;cursor:pointer;
            transition:all 0.25s;white-space:nowrap;
        }
        .btn-apply:hover{background:rgba(0,247,255,0.2);}
        #coupon-msg{font-size:0.78rem;margin-top:0.4rem;min-height:1em;}
        .btn-checkout{
            display:block;width:100%;text-align:center;
            padding:0.95rem;margin-top:1.5rem;
            background:linear-gradient(135deg,var(--neon-cyan),#00b8c8);
            border:none;border-radius:12px;color:#050816;font-weight:800;font-size:1rem;
            cursor:pointer;text-decoration:none;transition:all 0.3s;
            box-shadow:0 4px 20px rgba(0,247,255,0.25);
        }
        .btn-checkout:hover{opacity:0.9;transform:translateY(-2px);}

        /* EMPTY STATE */
        .empty-cart{text-align:center;padding:5rem 2rem;grid-column:1/-1;}
        .empty-cart .empty-icon{font-size:5rem;margin-bottom:1.2rem;display:block;}
        .empty-cart h2{font-size:1.5rem;margin-bottom:0.5rem;}
        .empty-cart p{color:var(--text-secondary);margin-bottom:1.5rem;}
        .btn-browse{
            display:inline-block;padding:0.8rem 2rem;
            background:linear-gradient(135deg,var(--neon-cyan),#00b8c8);
            border-radius:12px;color:#050816;font-weight:700;text-decoration:none;transition:opacity 0.3s;
        }
        .btn-browse:hover{opacity:0.85;}
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="page-header">
    <h1>My Cart 🛒</h1>
    <p><?= count($cart_items) ?> item<?= count($cart_items) !== 1 ? 's' : '' ?> in your cart</p>
</div>

<input type="hidden" id="csrf-token" value="<?= $csrf_token ?>">

<div class="cart-layout">
    <?php if (empty($cart_items)): ?>
    <div class="empty-cart">
        <span class="empty-icon">🛒</span>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added anything yet. Let's fix that!</p>
        <a href="restaurants.php" class="btn-browse" id="browse-foods-btn">Browse Foods</a>
    </div>
    <?php else: ?>
    <!-- CART ITEMS -->
    <div id="cart-items-container">
        <?php foreach ($cart_items as $item): ?>
        <div class="cart-item" id="cart-item-<?= (int)$item['cart_id'] ?>" data-cart-id="<?= (int)$item['cart_id'] ?>">
            <?php if (!empty($item['image'])): ?>
                <img src="../<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="item-img">
            <?php else: ?>
                <div class="item-img-placeholder">🍽️</div>
            <?php endif; ?>
            <div class="item-info">
                <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                <div class="item-cat"><?= htmlspecialchars($item['category'] ?? '') ?></div>
                <div class="item-price">₹<?= number_format($item['price'], 2) ?> each</div>
                <div class="item-controls">
                    <button class="qty-btn" data-cart-id="<?= (int)$item['cart_id'] ?>" data-action="decrease" title="Decrease quantity">−</button>
                    <span class="qty-display" id="qty-<?= (int)$item['cart_id'] ?>"><?= (int)$item['quantity'] ?></span>
                    <button class="qty-btn" data-cart-id="<?= (int)$item['cart_id'] ?>" data-action="increase" title="Increase quantity">+</button>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.8rem;">
                <div class="item-subtotal" id="subtotal-<?= (int)$item['cart_id'] ?>">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                <button class="remove-btn" data-cart-id="<?= (int)$item['cart_id'] ?>" title="Remove item">🗑</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ORDER SUMMARY -->
    <div class="order-summary">
        <div class="summary-title">📋 Order Summary</div>

        <div class="summary-row">
            <span>Subtotal</span>
            <span id="summary-subtotal">₹<?= number_format($subtotal, 2) ?></span>
        </div>
        <div class="summary-row">
            <span>Delivery Fee</span>
            <span id="summary-delivery">₹<?= number_format($delivery_fee, 2) ?></span>
        </div>
        <div class="summary-row">
            <span>Tax (5%)</span>
            <span id="summary-tax">₹<?= number_format($tax, 2) ?></span>
        </div>

        <!-- COUPON -->
        <div class="coupon-section">
            <div class="coupon-label">🎟️ Have a coupon?</div>
            <div class="coupon-row">
                <input type="text" id="coupon-code" class="coupon-input" placeholder="Enter coupon code"
                       value="<?= htmlspecialchars($_SESSION['applied_coupon']['code'] ?? '') ?>">
                <button class="btn-apply" id="apply-coupon-btn" onclick="applyCoupon()">Apply</button>
            </div>
            <div id="coupon-msg" style="color:var(--green);"></div>
        </div>

        <?php if ($coupon_discount > 0): ?>
        <div class="summary-row discount">
            <span>Coupon Discount</span>
            <span id="summary-discount">−₹<?= number_format($coupon_discount, 2) ?></span>
        </div>
        <?php else: ?>
        <div class="summary-row discount" id="discount-row" style="display:none;">
            <span>Coupon Discount</span>
            <span id="summary-discount">−₹0.00</span>
        </div>
        <?php endif; ?>

        <div class="summary-row total">
            <span>Grand Total</span>
            <span id="summary-total" style="color:var(--neon-cyan);">₹<?= number_format($grand_total, 2) ?></span>
        </div>

        <a href="checkout.php" class="btn-checkout" id="checkout-btn">Proceed to Checkout →</a>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/cart.js"></script>
</body>
</html>