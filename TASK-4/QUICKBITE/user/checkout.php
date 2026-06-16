<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
require_once '../config/db.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

$user_id    = (int)$_SESSION['user_id'];
$cart_items = get_cart_items($user_id, $conn);

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

$subtotal        = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart_items));
$delivery_fee    = 40.00;
$tax             = round($subtotal * 0.05, 2);
$coupon_discount = (float)($_SESSION['applied_coupon']['discount'] ?? 0);
$coupon_code     = $_SESSION['applied_coupon']['code'] ?? '';
$grand_total     = $subtotal + $delivery_fee + $tax - $coupon_discount;

// Fetch saved addresses
$addresses = db_fetch_all($conn, "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC", 'i', [$user_id]);

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — QuickBite 2.0</title>
    <meta name="description" content="Complete your QuickBite food order securely.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <style>
        :root {
            --neon-cyan:#FF5A00; --bg-dark:#F8FAFC; --bg-secondary:#FFFFFF;
            --bg-card:#FFFFFF; --text-primary:#0F172A; --text-secondary:#475569;
            --border-glass:#E2E8F0; --green:#00D084; --red:#FF4545;
            --grad:linear-gradient(135deg,#FF4747,#3A86FF);
        }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:var(--bg-dark);color:var(--text-primary);min-height:100vh;}

        /* ── PAGE HEADER ── */
        .checkout-header{
            background:linear-gradient(135deg,#F8FAFC 0%,#091428 100%);
            border-bottom:1px solid var(--border-glass);
            padding:2.5rem 2rem 2rem;
            text-align:center;
        }
        .checkout-header h1{font-size:2rem;font-weight:800;margin-bottom:0.3rem;}
        .checkout-header p{color:var(--text-secondary);font-size:0.95rem;}

        /* ── STEP INDICATOR ── */
        .steps-bar{
            display:flex;justify-content:center;align-items:center;gap:0;
            padding:1.5rem 2rem;max-width:600px;margin:0 auto;
        }
        .step{display:flex;align-items:center;gap:0.6rem;flex:1;justify-content:center;}
        .step-circle{
            width:36px;height:36px;border-radius:50%;border:2px solid var(--border-glass);
            display:flex;align-items:center;justify-content:center;font-size:0.85rem;font-weight:700;
            background:var(--bg-card);color:var(--text-secondary);transition:all 0.3s;flex-shrink:0;
        }
        .step.active .step-circle{background:var(--grad);border-color:transparent;color:#0F172A;}
        .step.done .step-circle{background:var(--green);border-color:transparent;color:#fff;}
        .step-label{font-size:0.8rem;font-weight:600;color:var(--text-secondary);}
        .step.active .step-label,.step.done .step-label{color:var(--text-primary);}
        .step-line{height:2px;flex:0 0 40px;background:var(--border-glass);transition:background 0.3s;}
        .step-line.done{background:var(--green);}

        /* ── LAYOUT ── */
        .checkout-layout{
            max-width:1100px;margin:2rem auto 5rem;padding:0 1.5rem;
            display:grid;grid-template-columns:1fr 340px;gap:2rem;
        }
        @media(max-width:820px){.checkout-layout{grid-template-columns:1fr;}}

        /* ── PANEL ── */
        .panel{
            background:var(--bg-card);border:1px solid var(--border-glass);
            border-radius:18px;padding:1.8rem;margin-bottom:1.2rem;
        }
        .panel-title{
            font-size:1rem;font-weight:700;margin-bottom:1.4rem;
            display:flex;align-items:center;gap:0.6rem;
            border-bottom:1px solid var(--border-glass);padding-bottom:0.8rem;
        }

        /* ── ADDRESS CARDS ── */
        .address-options{display:flex;flex-direction:column;gap:0.8rem;margin-bottom:1rem;}
        .address-card{
            display:flex;align-items:center;gap:1rem;padding:1rem 1.2rem;
            background:rgba(255,255,255,0.03);border:1px solid var(--border-glass);
            border-radius:12px;cursor:pointer;transition:all 0.25s;
        }
        .address-card:hover{border-color:rgba(255,71,71,0.3);}
        .address-card.selected{border-color:var(--neon-cyan);background:rgba(255,71,71,0.05);}
        .address-card input[type=radio]{accent-color:var(--neon-cyan);}
        .address-card-body{flex:1;min-width:0;}
        .address-label{font-size:0.78rem;font-weight:700;color:var(--neon-cyan);margin-bottom:0.2rem;}
        .address-text{font-size:0.88rem;color:var(--text-secondary);line-height:1.5;}

        /* ── NEW ADDRESS FORM ── */
        .new-addr-toggle{
            display:flex;align-items:center;gap:0.5rem;font-size:0.88rem;
            color:var(--neon-cyan);cursor:pointer;font-weight:600;
            background:none;border:1px dashed rgba(255,71,71,0.3);
            padding:0.7rem 1rem;border-radius:10px;transition:all 0.25s;width:100%;
        }
        .new-addr-toggle:hover{background:rgba(255,71,71,0.06);}
        #newAddrForm{display:none;margin-top:1rem;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:0.8rem;}
        @media(max-width:500px){.form-row{grid-template-columns:1fr;}}
        .form-group{display:flex;flex-direction:column;gap:0.4rem;margin-bottom:0.8rem;}
        .form-label{font-size:0.78rem;font-weight:600;color:var(--text-secondary);}
        .form-input{
            padding:0.7rem 1rem;background:rgba(255,255,255,0.05);
            border:1px solid var(--border-glass);border-radius:10px;
            color:var(--text-primary);font-size:0.9rem;font-family:'Inter',sans-serif;outline:none;
            transition:border-color 0.25s;
        }
        .form-input:focus{border-color:var(--neon-cyan);}
        .form-input::placeholder{color:rgba(148,163,184,0.5);}
        textarea.form-input{resize:vertical;min-height:80px;}

        /* ── PAYMENT METHODS ── */
        .payment-methods{display:grid;grid-template-columns:repeat(2,1fr);gap:0.8rem;}
        @media(max-width:500px){.payment-methods{grid-template-columns:1fr;}}
        .pay-card{
            padding:1rem;border:1px solid var(--border-glass);border-radius:12px;
            cursor:pointer;transition:all 0.25s;display:flex;align-items:center;gap:0.8rem;
            background:rgba(255,255,255,0.03);
        }
        .pay-card:hover{border-color:rgba(255,71,71,0.3);}
        .pay-card.selected{border-color:var(--neon-cyan);background:rgba(255,71,71,0.06);}
        .pay-card input[type=radio]{accent-color:var(--neon-cyan);}
        .pay-icon{font-size:1.8rem;line-height:1;}
        .pay-label{font-size:0.9rem;font-weight:600;}
        .pay-sublabel{font-size:0.72rem;color:var(--text-secondary);margin-top:0.15rem;}

        /* UPI input */
        #upiSection{display:none;margin-top:1rem;}
        .upi-row{display:flex;gap:0.6rem;align-items:center;}
        .upi-badge{
            display:inline-flex;align-items:center;gap:0.4rem;font-size:0.78rem;
            padding:0.3rem 0.7rem;border-radius:20px;font-weight:700;cursor:pointer;
            border:1px solid;transition:all 0.2s;
        }
        .upi-badge.gpay{color:#4285F4;border-color:#4285F4;background:rgba(66,133,244,0.08);}
        .upi-badge.ppay{color:#002970;border-color:#002970;background:rgba(0,41,112,0.08);}
        .upi-badge.bhim{color:#f97316;border-color:#f97316;background:rgba(249,115,22,0.08);}

        /* Card inputs */
        #cardSection{display:none;margin-top:1rem;}

        /* ── ORDER ITEMS ── */
        .order-item-row{
            display:flex;align-items:center;gap:0.8rem;padding:0.7rem 0;
            border-bottom:1px solid var(--border-glass);
        }
        .order-item-row:last-child{border-bottom:none;}
        .order-item-img{
            width:48px;height:48px;border-radius:10px;object-fit:cover;
            background:var(--bg-secondary);flex-shrink:0;display:flex;align-items:center;justify-content:center;
            font-size:1.3rem;
        }
        .order-item-name{flex:1;font-size:0.9rem;font-weight:600;}
        .order-item-qty{font-size:0.78rem;color:var(--text-secondary);}
        .order-item-price{font-size:0.95rem;font-weight:700;color:var(--neon-cyan);}

        /* ── SUMMARY CARD ── */
        .summary-card{
            background:var(--bg-card);border:1px solid var(--border-glass);
            border-radius:18px;padding:1.5rem;position:sticky;top:90px;height:fit-content;
        }
        .summary-title{font-size:1rem;font-weight:700;margin-bottom:1.2rem;border-bottom:1px solid var(--border-glass);padding-bottom:0.8rem;}
        .summary-row{display:flex;justify-content:space-between;margin-bottom:0.7rem;font-size:0.9rem;}
        .summary-row span:last-child{font-weight:600;}
        .summary-row.discount{color:var(--green);}
        .summary-row.total{font-size:1.05rem;font-weight:800;border-top:1px solid var(--border-glass);padding-top:0.8rem;margin-top:0.5rem;}

        /* ── COUPON ── */
        .coupon-box{margin:1rem 0;padding:0.9rem;background:rgba(255,255,255,0.02);border:1px dashed rgba(255,71,71,0.2);border-radius:12px;}
        .coupon-row{display:flex;gap:0.5rem;}
        .coupon-input{flex:1;padding:0.55rem 0.8rem;background:rgba(255,255,255,0.05);border:1px solid var(--border-glass);border-radius:8px;color:var(--text-primary);font-size:0.85rem;outline:none;transition:border-color 0.25s;}
        .coupon-input:focus{border-color:var(--neon-cyan);}
        .coupon-input::placeholder{color:var(--text-secondary);}
        .btn-apply{padding:0.55rem 0.9rem;background:rgba(255,71,71,0.12);border:1px solid rgba(255,71,71,0.3);border-radius:8px;color:var(--neon-cyan);font-size:0.82rem;font-weight:700;cursor:pointer;transition:all 0.25s;white-space:nowrap;}
        .btn-apply:hover{background:rgba(255,71,71,0.2);}
        #coupon-msg{font-size:0.78rem;margin-top:0.4rem;min-height:1em;}

        /* ── PLACE ORDER BUTTON ── */
        .btn-place-order{
            display:block;width:100%;text-align:center;
            padding:1rem;margin-top:1.5rem;
            background:linear-gradient(135deg,var(--neon-cyan),#00b8c8);
            border:none;border-radius:14px;color:#0F172A;font-weight:800;font-size:1rem;
            cursor:pointer;transition:all 0.3s;
            box-shadow:0 4px 20px rgba(255,71,71,0.3);
            font-family:'Inter',sans-serif;
        }
        .btn-place-order:hover{opacity:0.9;transform:translateY(-2px);}
        .btn-place-order:disabled{opacity:0.5;transform:none;cursor:not-allowed;}

        /* ── LOADING OVERLAY ── */
        #payLoading{
            display:none;position:fixed;inset:0;z-index:9999;
            background:rgba(5,8,22,0.9);backdrop-filter:blur(12px);
            flex-direction:column;align-items:center;justify-content:center;gap:1.5rem;
        }
        #payLoading.show{display:flex;}
        .pay-spinner{
            width:64px;height:64px;border-radius:50%;
            border:4px solid rgba(255,71,71,0.15);
            border-top-color:var(--neon-cyan);
            animation:spin 0.9s linear infinite;
        }
        @keyframes spin{to{transform:rotate(360deg);}}
        .pay-loading-text{font-size:1.1rem;font-weight:600;color:var(--neon-cyan);}
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<!-- PAGE HEADER -->
<div class="checkout-header">
    <h1>🛒 Secure Checkout</h1>
    <p>Review your order and complete your payment</p>
</div>

<!-- STEP INDICATOR -->
<div class="steps-bar">
    <div class="step active" id="step-1">
        <div class="step-circle">1</div>
        <span class="step-label">Address</span>
    </div>
    <div class="step-line" id="line-1"></div>
    <div class="step active" id="step-2">
        <div class="step-circle">2</div>
        <span class="step-label">Review</span>
    </div>
    <div class="step-line" id="line-2"></div>
    <div class="step" id="step-3">
        <div class="step-circle">3</div>
        <span class="step-label">Payment</span>
    </div>
</div>

<!-- HIDDEN INPUTS -->
<input type="hidden" id="csrf-token" value="<?= $csrf_token ?>">
<input type="hidden" id="selected-payment" value="COD">
<input type="hidden" id="coupon-code-val" value="<?= htmlspecialchars($coupon_code) ?>">

<!-- PAYMENT LOADING OVERLAY -->
<div id="payLoading">
    <div class="pay-spinner"></div>
    <div class="pay-loading-text" id="payLoadingText">Processing your order…</div>
</div>

<!-- CHECKOUT LAYOUT -->
<div class="checkout-layout">

    <!-- LEFT COLUMN -->
    <div>

        <!-- STEP 1: DELIVERY ADDRESS -->
        <div class="panel">
            <div class="panel-title">📍 Delivery Address</div>

            <?php if (!empty($addresses)): ?>
            <div class="address-options" id="addr-options">
                <?php foreach ($addresses as $i => $addr): ?>
                <label class="address-card <?= $i === 0 ? 'selected' : '' ?>" id="addr-card-<?= $addr['id'] ?>">
                    <input type="radio" name="saved_address" value="<?= htmlspecialchars($addr['full_address']) ?>"
                           <?= $i === 0 ? 'checked' : '' ?> onchange="selectAddress(this)">
                    <div class="address-card-body">
                        <div class="address-label">🏠 <?= htmlspecialchars($addr['label']) ?></div>
                        <div class="address-text">
                            <?= htmlspecialchars($addr['full_address']) ?>
                            <?php if ($addr['city']): ?>, <?= htmlspecialchars($addr['city']) ?><?php endif; ?>
                            <?php if ($addr['pincode']): ?> — <?= htmlspecialchars($addr['pincode']) ?><?php endif; ?>
                        </div>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <button class="new-addr-toggle" onclick="toggleNewAddr()" id="newAddrToggle">
                ➕ Use a new address
            </button>

            <div id="newAddrForm">
                <div class="form-group" style="margin-top:0.8rem;">
                    <label class="form-label">Address Label</label>
                    <select class="form-input" id="addr-label">
                        <option value="Home">🏠 Home</option>
                        <option value="Work">🏢 Work</option>
                        <option value="Other">📍 Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Full Address *</label>
                    <textarea class="form-input" id="addr-full" placeholder="Flat no., Street, Area, Landmark…" rows="3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">City *</label>
                        <input type="text" class="form-input" id="addr-city" placeholder="Hyderabad">
                    </div>
                    <div class="form-group">
                        <label class="form-label">PIN Code *</label>
                        <input type="text" class="form-input" id="addr-pin" placeholder="500001" maxlength="6" pattern="[0-9]{6}">
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 2: ORDER REVIEW -->
        <div class="panel">
            <div class="panel-title">🍽️ Order Review</div>
            <?php foreach ($cart_items as $item): ?>
            <div class="order-item-row">
                <?php if (!empty($item['image'])): ?>
                    <img src="../<?= htmlspecialchars($item['image']) ?>" alt="" class="order-item-img" style="width:48px;height:48px;border-radius:10px;object-fit:cover;">
                <?php else: ?>
                    <div class="order-item-img">🍽️</div>
                <?php endif; ?>
                <div class="order-item-name">
                    <?= htmlspecialchars($item['food_name'] ?? $item['name'] ?? 'Food Item') ?>
                    <div class="order-item-qty">Qty: <?= (int)$item['quantity'] ?></div>
                </div>
                <div class="order-item-price">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- STEP 3: PAYMENT METHOD -->
        <div class="panel">
            <div class="panel-title">💳 Payment Method</div>

            <div class="payment-methods" id="payment-methods">

                <label class="pay-card selected" onclick="selectPayment('COD', this)">
                    <input type="radio" name="payment" value="COD" checked>
                    <div class="pay-icon">💵</div>
                    <div>
                        <div class="pay-label">Cash on Delivery</div>
                        <div class="pay-sublabel">Pay when food arrives</div>
                    </div>
                </label>

                <label class="pay-card" onclick="selectPayment('UPI', this)">
                    <input type="radio" name="payment" value="UPI">
                    <div class="pay-icon">📱</div>
                    <div>
                        <div class="pay-label">UPI</div>
                        <div class="pay-sublabel">GPay, PhonePe, BHIM</div>
                    </div>
                </label>

                <label class="pay-card" onclick="selectPayment('Card', this)">
                    <input type="radio" name="payment" value="Card">
                    <div class="pay-icon">💳</div>
                    <div>
                        <div class="pay-label">Credit / Debit Card</div>
                        <div class="pay-sublabel">Visa, Mastercard, RuPay</div>
                    </div>
                </label>

                <label class="pay-card" onclick="selectPayment('Wallet', this)">
                    <input type="radio" name="payment" value="Wallet">
                    <div class="pay-icon">👛</div>
                    <div>
                        <div class="pay-label">Wallet</div>
                        <div class="pay-sublabel">Paytm, Amazon Pay</div>
                    </div>
                </label>

            </div>

            <!-- UPI Section -->
            <div id="upiSection">
                <div class="form-group" style="margin-top:0.5rem;">
                    <label class="form-label">Enter UPI ID</label>
                    <div class="upi-row">
                        <input type="text" class="form-input" id="upiId" placeholder="yourname@upi" style="flex:1;">
                    </div>
                    <div style="display:flex;gap:0.5rem;margin-top:0.6rem;flex-wrap:wrap;">
                        <span class="upi-badge gpay" onclick="document.getElementById('upiId').value='yourname@oksbi'">G Pay</span>
                        <span class="upi-badge ppay" onclick="document.getElementById('upiId').value='yourname@ybl'">PhonePe</span>
                        <span class="upi-badge bhim" onclick="document.getElementById('upiId').value='yourname@upi'">BHIM</span>
                    </div>
                </div>
            </div>

            <!-- Card Section -->
            <div id="cardSection">
                <div class="form-group" style="margin-top:0.5rem;">
                    <label class="form-label">Card Number</label>
                    <input type="text" class="form-input" id="cardNum" placeholder="1234 5678 9012 3456" maxlength="19" oninput="formatCard(this)">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Expiry (MM/YY)</label>
                        <input type="text" class="form-input" id="cardExpiry" placeholder="12/27" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CVV</label>
                        <input type="password" class="form-input" id="cardCvv" placeholder="•••" maxlength="4">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Cardholder Name</label>
                    <input type="text" class="form-input" id="cardName" placeholder="As on card">
                </div>
                <p style="font-size:0.75rem;color:var(--text-secondary);margin-top:0.3rem;">
                    🔒 Your card details are simulated and never stored. This is a demo environment.
                </p>
            </div>

        </div>

    </div>

    <!-- RIGHT COLUMN: ORDER SUMMARY -->
    <div>
        <div class="summary-card">
            <div class="summary-title">📋 Order Summary</div>

            <div class="summary-row">
                <span>Subtotal (<?= count($cart_items) ?> items)</span>
                <span>₹<?= number_format($subtotal, 2) ?></span>
            </div>
            <div class="summary-row">
                <span>Delivery Fee</span>
                <span>₹<?= number_format($delivery_fee, 2) ?></span>
            </div>
            <div class="summary-row">
                <span>GST (5%)</span>
                <span>₹<?= number_format($tax, 2) ?></span>
            </div>

            <!-- COUPON -->
            <div class="coupon-box">
                <div class="coupon-row">
                    <input type="text" class="coupon-input" id="coupon-input"
                           placeholder="🎟️ Coupon code"
                           value="<?= htmlspecialchars($coupon_code) ?>">
                    <button class="btn-apply" onclick="applyCoupon()">Apply</button>
                </div>
                <div id="coupon-msg" style="color:var(--green);"></div>
            </div>

            <div class="summary-row discount" id="discount-row" style="<?= $coupon_discount > 0 ? '' : 'display:none' ?>">
                <span>Coupon Discount</span>
                <span id="discount-amt">−₹<?= number_format($coupon_discount, 2) ?></span>
            </div>

            <div class="summary-row total">
                <span>Grand Total</span>
                <span id="grand-total" style="color:var(--neon-cyan);">₹<?= number_format($grand_total, 2) ?></span>
            </div>

            <div style="font-size:0.75rem;color:var(--text-secondary);margin-top:0.5rem;text-align:center;">
                ⏱ Estimated delivery: ~35 minutes
            </div>

            <button class="btn-place-order" id="placeOrderBtn" onclick="placeOrder()">
                🎉 Place Order
            </button>

            <p style="font-size:0.72rem;color:var(--text-secondary);text-align:center;margin-top:0.8rem;">
                🔒 Secured by QuickBite. By placing this order you agree to our Terms of Service.
            </p>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/main.js"></script>
<script>
// ── TOTALS (PHP-generated) ─────────────────────────
let subtotal     = <?= $subtotal ?>;
let deliveryFee  = <?= $delivery_fee ?>;
let tax          = <?= $tax ?>;
let discount     = <?= $coupon_discount ?>;
let selectedPayment = 'COD';

// ── ADDRESS SELECTION ───────────────────────────────
function selectAddress(radio) {
    document.querySelectorAll('.address-card').forEach(c => c.classList.remove('selected'));
    radio.closest('.address-card').classList.add('selected');
}

let newAddrOpen = false;
function toggleNewAddr() {
    newAddrOpen = !newAddrOpen;
    document.getElementById('newAddrForm').style.display = newAddrOpen ? 'block' : 'none';
    document.getElementById('newAddrToggle').textContent = newAddrOpen ? '✕ Cancel' : '➕ Use a new address';
    if (newAddrOpen) {
        // Deselect saved addresses
        document.querySelectorAll('input[name="saved_address"]').forEach(r => r.checked = false);
        document.querySelectorAll('.address-card').forEach(c => c.classList.remove('selected'));
    }
}

function getDeliveryAddress() {
    if (newAddrOpen) {
        const full = document.getElementById('addr-full').value.trim();
        const city = document.getElementById('addr-city').value.trim();
        const pin  = document.getElementById('addr-pin').value.trim();
        if (!full || !city || !pin) return null;
        return `${full}, ${city} - ${pin}`;
    }
    const checked = document.querySelector('input[name="saved_address"]:checked');
    return checked ? checked.value : null;
}

// ── PAYMENT SELECTION ───────────────────────────────
function selectPayment(method, el) {
    selectedPayment = method;
    document.getElementById('selected-payment').value = method;
    document.querySelectorAll('.pay-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');

    document.getElementById('upiSection').style.display  = method === 'UPI'  ? 'block' : 'none';
    document.getElementById('cardSection').style.display = method === 'Card' ? 'block' : 'none';
}

// ── CARD NUMBER FORMATTER ───────────────────────────
function formatCard(input) {
    let val = input.value.replace(/\D/g, '').substring(0, 16);
    input.value = val.replace(/(.{4})/g, '$1 ').trim();
}

// ── COUPON ──────────────────────────────────────────
function updateTotals(newDiscount) {
    discount = newDiscount;
    const grand = subtotal + deliveryFee + tax - discount;
    document.getElementById('grand-total').textContent = '₹' + grand.toFixed(2);
    if (discount > 0) {
        document.getElementById('discount-row').style.display = 'flex';
        document.getElementById('discount-amt').textContent = '−₹' + discount.toFixed(2);
    } else {
        document.getElementById('discount-row').style.display = 'none';
    }
}

function applyCoupon() {
    const code = document.getElementById('coupon-input').value.trim().toUpperCase();
    const msg  = document.getElementById('coupon-msg');
    if (!code) { msg.style.color = 'var(--red)'; msg.textContent = 'Enter a coupon code.'; return; }

    msg.style.color = 'var(--text-secondary)';
    msg.textContent = 'Validating…';

    fetch('ajax/apply-coupon.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'code=' + encodeURIComponent(code)
            + '&subtotal=' + subtotal
            + '&csrf_token=' + document.getElementById('csrf-token').value
    })
    .then(r => r.json())
    .then(data => {
        if (data.valid) {
            msg.style.color = 'var(--green)';
            msg.textContent = '✅ ' + data.message;
            document.getElementById('coupon-code-val').value = code;
            updateTotals(parseFloat(data.discount));
        } else {
            msg.style.color = 'var(--red)';
            msg.textContent = '❌ ' + data.error;
            document.getElementById('coupon-code-val').value = '';
            updateTotals(0);
        }
    })
    .catch(() => { msg.style.color='var(--red)'; msg.textContent = 'Network error.'; });
}

// ── PLACE ORDER ─────────────────────────────────────
function placeOrder() {
    const addr = getDeliveryAddress();
    if (!addr) {
        alert('Please enter a valid delivery address (Full address, city and PIN code are required).');
        return;
    }

    // Payment-specific validation
    if (selectedPayment === 'UPI') {
        const upi = document.getElementById('upiId').value.trim();
        if (!upi || !upi.includes('@')) { alert('Please enter a valid UPI ID (e.g. name@upi)'); return; }
    }
    if (selectedPayment === 'Card') {
        const num  = document.getElementById('cardNum').value.replace(/\s/g,'');
        const exp  = document.getElementById('cardExpiry').value.trim();
        const cvv  = document.getElementById('cardCvv').value.trim();
        const name = document.getElementById('cardName').value.trim();
        if (num.length < 16) { alert('Enter a valid 16-digit card number.'); return; }
        if (!/^\d{2}\/\d{2}$/.test(exp)) { alert('Enter expiry as MM/YY'); return; }
        if (cvv.length < 3) { alert('Enter a valid CVV.'); return; }
        if (!name) { alert('Enter cardholder name.'); return; }
    }

    // Show Gateway for Online Payments
    if (selectedPayment !== 'COD') {
        showGatewayModal();
        return;
    }

    executeOrderPlacement();
}

function showGatewayModal() {
    const gateway = document.getElementById('mockGatewayModal');
    const amt = document.getElementById('grand-total').textContent;
    document.getElementById('gateway-amt').textContent = amt;
    document.getElementById('gateway-method').textContent = selectedPayment;
    gateway.classList.add('show');
}

function closeGateway() {
    document.getElementById('mockGatewayModal').classList.remove('show');
    document.getElementById('placeOrderBtn').disabled = false;
}

function processGatewayPayment() {
    const btn = document.getElementById('gatewayPayBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="pay-spinner" style="width:20px;height:20px;border-width:2px;display:inline-block;vertical-align:middle;margin-right:8px;"></span> Processing...';
    
    setTimeout(() => {
        btn.innerHTML = '✅ Payment Successful';
        btn.style.background = 'var(--success-green)';
        setTimeout(() => {
            closeGateway();
            executeOrderPlacement();
        }, 1000);
    }, 2500);
}

function executeOrderPlacement() {
    const addr = getDeliveryAddress();
    
    // Show loading overlay
    const loadingEl = document.getElementById('payLoading');
    const loadingText = document.getElementById('payLoadingText');
    loadingEl.classList.add('show');

    const messages = ['Placing your order…', 'Securing payment…', 'Notifying restaurant…', 'Almost done…'];
    let mi = 0;
    const msgInterval = setInterval(() => { loadingText.textContent = messages[++mi % messages.length]; }, 900);

    document.getElementById('placeOrderBtn').disabled = true;

    fetch('ajax/place-order.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'delivery_address=' + encodeURIComponent(addr)
            + '&payment_method=' + encodeURIComponent(selectedPayment)
            + '&coupon_code=' + encodeURIComponent(document.getElementById('coupon-code-val').value)
            + '&csrf_token=' + encodeURIComponent(document.getElementById('csrf-token').value)
    })
    .then(r => r.json())
    .then(data => {
        clearInterval(msgInterval);
        if (data.success) {
            loadingText.textContent = '🎉 Order placed!';
            setTimeout(() => {
                window.location.href = 'order-success.php?order=' + encodeURIComponent(data.order_number)
                    + '&total=' + encodeURIComponent(data.grand_total)
                    + '&method=' + encodeURIComponent(data.payment_method)
                    + '&txn=' + encodeURIComponent(data.txn_id || '');
            }, 800);
        } else {
            loadingEl.classList.remove('show');
            document.getElementById('placeOrderBtn').disabled = false;
            alert('❌ ' + (data.error || 'Something went wrong. Please try again.'));
        }
    })
    .catch(() => {
        clearInterval(msgInterval);
        loadingEl.classList.remove('show');
        document.getElementById('placeOrderBtn').disabled = false;
        alert('Network error. Please check your connection and try again.');
    });
}
</script>

<!-- Mock Payment Gateway Modal -->
<div id="mockGatewayModal" class="modal">
    <div class="modal-content" style="max-width:400px;text-align:left;padding:0;overflow:hidden;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.2);">
        <div style="background:#0F172A;color:#fff;padding:1.2rem;display:flex;align-items:center;justify-content:space-between;">
            <div style="font-weight:700;font-size:1.1rem;display:flex;align-items:center;gap:8px;">
                <span style="display:inline-block;width:24px;height:24px;background:var(--neon-cyan);border-radius:4px;color:#fff;text-align:center;line-height:24px;font-size:0.9rem;">⚡</span>
                QuickPay Gateway
            </div>
            <button onclick="closeGateway()" style="background:none;border:none;color:#94A3B8;cursor:pointer;font-size:1.5rem;">&times;</button>
        </div>
        <div style="padding:1.5rem;background:var(--bg-secondary);">
            <p style="color:var(--text-secondary);font-size:0.9rem;margin-bottom:0.5rem;">Total Amount to Pay</p>
            <h2 id="gateway-amt" style="font-size:2rem;color:var(--text-primary);margin-bottom:1.5rem;">₹0.00</h2>
            
            <div style="border:1px solid var(--border-glass);border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
                <div style="font-size:0.85rem;color:var(--text-secondary);margin-bottom:0.3rem;">Paying via</div>
                <div id="gateway-method" style="font-size:1.05rem;font-weight:600;color:var(--text-primary);">Card</div>
            </div>
            
            <button id="gatewayPayBtn" onclick="processGatewayPayment()" style="width:100%;padding:1rem;border:none;border-radius:8px;background:var(--neon-cyan);color:#fff;font-weight:700;font-size:1.05rem;cursor:pointer;transition:background 0.3s;">
                Pay Now
            </button>
            <p style="text-align:center;font-size:0.75rem;color:var(--text-muted);margin-top:1rem;">🔒 100% Secure & Encrypted</p>
        </div>
    </div>
</div>
<style>
.modal { display:none; position:fixed; inset:0; z-index:10000; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); align-items:center; justify-content:center; }
.modal.show { display:flex; }
</style>
</body>
</html>