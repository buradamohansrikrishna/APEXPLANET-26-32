<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
require_once '../config/db.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

$user_id  = (int)$_SESSION['user_id'];
$order_id = (int)($_GET['id'] ?? 0);

if ($order_id <= 0) {
    header('Location: orders.php');
    exit;
}

// Verify ownership
$stmt = $conn->prepare("SELECT id, order_number FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->bind_param('ii', $order_id, $user_id);
$stmt->execute();
$order_basic = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order_basic) {
    header('Location: orders.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order <?= htmlspecialchars($order_basic['order_number']) ?> — QuickBite</title>
    <meta name="description" content="Real-time tracking for your QuickBite order.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root{
            --neon-cyan:#FF5A00;--bg-dark:#F8FAFC;--bg-secondary:#FFFFFF;
            --bg-card:#FFFFFF;--text-primary:#0F172A;--text-secondary:#475569;
            --border-glass:#E2E8F0;--green:#00D084;--orange:#FF8C42;
        }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:var(--bg-dark);color:var(--text-primary);min-height:100vh;}

        .track-wrap{max-width:760px;margin:0 auto;padding:2rem 1.5rem 5rem;}

        /* PAGE HEADER */
        .track-header{
            background:linear-gradient(135deg,#F8FAFC,#F1F5F9);
            padding:2.5rem 2rem;text-align:center;border-bottom:1px solid var(--border-glass);
            margin-bottom:2rem;
        }
        .track-header h1{font-size:1.8rem;font-weight:800;}
        .order-num{font-size:1rem;color:var(--neon-cyan);font-weight:700;margin-top:0.3rem;}
        .last-updated{font-size:0.75rem;color:var(--text-secondary);margin-top:0.3rem;}

        /* STATUS CARD */
        .status-card{
            background:var(--bg-card);border:1px solid var(--border-glass);
            border-radius:18px;padding:1.8rem;margin-bottom:1.2rem;
            text-align:center;
        }
        .status-icon-big{font-size:3.5rem;margin-bottom:0.8rem;display:block;}
        .status-text{font-size:1.4rem;font-weight:800;margin-bottom:0.3rem;}
        .status-sub{font-size:0.9rem;color:var(--text-secondary);}
        .est-time{
            display:inline-flex;align-items:center;gap:0.5rem;
            margin-top:1rem;padding:0.5rem 1.2rem;
            background:rgba(255,71,71,0.08);border:1px solid rgba(255,71,71,0.2);
            border-radius:20px;font-size:0.9rem;font-weight:700;color:var(--neon-cyan);
        }

        /* STEP TRACKER */
        .steps-track{
            background:var(--bg-card);border:1px solid var(--border-glass);
            border-radius:18px;padding:1.8rem;margin-bottom:1.2rem;
        }
        .steps-track-title{font-size:0.95rem;font-weight:700;margin-bottom:1.5rem;color:var(--text-secondary);}
        .steps-list{display:flex;flex-direction:column;gap:0;}

        .step-row{display:flex;gap:1rem;position:relative;}
        .step-left{display:flex;flex-direction:column;align-items:center;width:44px;flex-shrink:0;}
        .step-circle-big{
            width:44px;height:44px;border-radius:50%;border:2px solid var(--border-glass);
            background:var(--bg-secondary);display:flex;align-items:center;justify-content:center;
            font-size:1.2rem;transition:all 0.5s;flex-shrink:0;z-index:1;
        }
        .step-circle-big.done{background:rgba(0,208,132,0.2);border-color:var(--green);box-shadow:0 0 16px rgba(0,208,132,0.3);}
        .step-circle-big.active{background:rgba(255,71,71,0.15);border-color:var(--neon-cyan);box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);animation:pulse-step 2s ease-in-out infinite;}
        @keyframes pulse-step{0%,100%{box-shadow:0 0 20px rgba(255,71,71,0.3);}50%{box-shadow:0 0 35px rgba(255,71,71,0.6);}}

        .step-connector{width:2px;flex:1;background:var(--border-glass);margin:4px auto;transition:background 0.5s;}
        .step-connector.done{background:var(--green);}

        .step-right{padding:0.6rem 0 1.5rem;}
        .step-title{font-size:0.95rem;font-weight:700;margin-bottom:0.2rem;}
        .step-desc{font-size:0.8rem;color:var(--text-secondary);}
        .step-time{font-size:0.72rem;color:var(--neon-cyan);margin-top:0.2rem;font-weight:600;}

        /* LIVE PULSE BADGE */
        .live-badge{
            display:inline-flex;align-items:center;gap:0.4rem;
            font-size:0.75rem;font-weight:700;color:var(--green);
            background:rgba(0,208,132,0.1);border:1px solid rgba(0,208,132,0.25);
            border-radius:20px;padding:0.3rem 0.8rem;margin-bottom:1.5rem;
        }
        .live-dot{
            width:8px;height:8px;border-radius:50%;background:var(--green);
            animation:blink-dot 1.2s ease-in-out infinite;
        }
        @keyframes blink-dot{0%,100%{opacity:1;}50%{opacity:0.2;}}

        /* ORDER SUMMARY */
        .summary-card{
            background:var(--bg-card);border:1px solid var(--border-glass);
            border-radius:18px;padding:1.5rem;margin-bottom:1.2rem;
        }
        .summary-card-title{font-size:0.95rem;font-weight:700;margin-bottom:1rem;border-bottom:1px solid var(--border-glass);padding-bottom:0.7rem;}
        .item-row{display:flex;align-items:center;gap:0.8rem;padding:0.55rem 0;border-bottom:1px solid rgba(255,255,255,0.04);}
        .item-row:last-child{border-bottom:none;}
        .item-img-sm{width:40px;height:40px;border-radius:8px;object-fit:cover;background:var(--bg-secondary);display:flex;align-items:center;justify-content:center;font-size:1.2rem;}
        .item-name-sm{flex:1;font-size:0.88rem;font-weight:600;}
        .item-qty-sm{font-size:0.78rem;color:var(--text-secondary);}
        .item-price-sm{font-size:0.9rem;font-weight:700;color:var(--neon-cyan);}

        .fee-row{display:flex;justify-content:space-between;font-size:0.88rem;padding:0.35rem 0;color:var(--text-secondary);}
        .fee-row.total{font-size:1rem;font-weight:800;color:var(--text-primary);border-top:1px solid var(--border-glass);padding-top:0.7rem;margin-top:0.3rem;}

        /* DELIVERY ADDRESS */
        .address-card{
            background:var(--bg-card);border:1px solid var(--border-glass);
            border-radius:18px;padding:1.5rem;margin-bottom:1.5rem;
        }
        .address-card-title{font-size:0.95rem;font-weight:700;margin-bottom:0.7rem;}
        .address-text{font-size:0.9rem;color:var(--text-secondary);line-height:1.6;}

        /* BACK BUTTON */
        .btn-back{
            display:inline-flex;align-items:center;gap:0.5rem;
            padding:0.7rem 1.3rem;margin-bottom:1.5rem;
            background:rgba(255,255,255,0.05);border:1px solid var(--border-glass);
            border-radius:12px;color:var(--text-primary);font-weight:600;font-size:0.88rem;
            text-decoration:none;transition:all 0.25s;
        }
        .btn-back:hover{background:rgba(255,255,255,0.1);}

        /* CANCELLED STATE */
        .cancelled-banner{
            background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);
            border-radius:14px;padding:1.2rem;text-align:center;
            font-size:0.95rem;color:#EF4444;margin-bottom:1.2rem;
        }
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="track-header">
    <h1>📍 Order Tracking</h1>
    <div class="order-num" id="order-num"><?= htmlspecialchars($order_basic['order_number']) ?></div>
    <div class="last-updated" id="last-updated">Loading…</div>
</div>

<div class="track-wrap">
    <a href="orders.php" class="btn-back">← Back to My Orders</a>

    <!-- LIVE BADGE -->
    <div style="margin-bottom:1rem;">
        <span class="live-badge"><span class="live-dot"></span> Live Tracking · Updates every 15s</span>
    </div>

    <!-- STATUS CARD -->
    <div class="status-card" id="status-card">
        <span class="status-icon-big" id="status-icon">⏳</span>
        <div class="status-text" id="status-text">Loading…</div>
        <div class="status-sub" id="status-sub">Fetching your order status</div>
        <div class="est-time" id="est-time">⏱ Estimated delivery: --</div>
    </div>

    <!-- STEP TRACKER -->
    <div class="steps-track">
        <div class="steps-track-title">Order Journey</div>
        <div class="steps-list" id="steps-list">
            <?php
            $track_steps = [
                ['⏳', 'Order Placed',    'Your order has been received and confirmed'],
                ['👨‍🍳', 'Preparing',      'Our chefs are preparing your food'],
                ['🛵', 'On the Way',     'Your delivery partner is heading to you'],
                ['🎉', 'Delivered',      'Enjoy your meal!'],
            ];
            foreach ($track_steps as $ti => $ts):
            ?>
            <div class="step-row">
                <div class="step-left">
                    <div class="step-circle-big" id="sc-<?= $ti ?>"><?= $ts[0] ?></div>
                    <?php if ($ti < count($track_steps) - 1): ?>
                    <div class="step-connector" id="conn-<?= $ti ?>"></div>
                    <?php endif; ?>
                </div>
                <div class="step-right">
                    <div class="step-title"><?= $ts[1] ?></div>
                    <div class="step-desc"><?= $ts[2] ?></div>
                    <div class="step-time" id="st-<?= $ti ?>"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ORDERED ITEMS -->
    <div class="summary-card">
        <div class="summary-card-title">🍽️ Your Order</div>
        <div id="items-list"><div style="color:var(--text-secondary);font-size:0.88rem;">Loading items…</div></div>
        <div id="fee-breakdown" style="margin-top:0.8rem;"></div>
    </div>

    <!-- DELIVERY ADDRESS -->
    <div class="address-card" id="address-card">
        <div class="address-card-title">📍 Delivery Address</div>
        <div class="address-text" id="delivery-address">Loading…</div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/main.js"></script>
<script>
const ORDER_ID = <?= $order_id ?>;
let lastStatus = null;

const statusMap = {
    'Pending':          { icon:'⏳', text:'Order Placed',     sub:'Waiting for restaurant to accept your order', step:0 },
    'Accepted':         { icon:'✅', text:'Order Accepted',    sub:'Restaurant has accepted your order!', step:1 },
    'Preparing':        { icon:'👨‍🍳', text:'Being Prepared',   sub:'Our chefs are cooking your food fresh!', step:1 },
    'Ready':            { icon:'📦', text:'Ready for Pickup',  sub:'Your order is packed and ready!', step:2 },
    'Out For Delivery': { icon:'🛵', text:'On the Way!',       sub:'Your delivery partner is on the way!', step:2 },
    'Delivered':        { icon:'🎉', text:'Delivered!',        sub:'Your food has been delivered. Enjoy your meal!', step:3 },
    'Cancelled':        { icon:'❌', text:'Order Cancelled',   sub:'This order was cancelled.', step:-1 },
};

function updateTracker(data) {
    const info = statusMap[data.status] || { icon:'📋', text:data.status, sub:'', step:0 };

    document.getElementById('status-icon').textContent = info.icon;
    document.getElementById('status-text').textContent = info.text;
    document.getElementById('status-sub').textContent  = info.sub;
    document.getElementById('est-time').innerHTML = `⏱ Estimated delivery: <strong>${data.est_delivery}</strong>`;
    document.getElementById('last-updated').textContent = 'Last updated: ' + new Date().toLocaleTimeString('en-IN');

    const step = info.step;

    if (data.is_cancelled) {
        document.getElementById('status-card').style.borderColor = 'rgba(239,68,68,0.3)';
    }

    // Update step circles
    for (let i = 0; i <= 3; i++) {
        const circle = document.getElementById('sc-' + i);
        if (!circle) continue;
        circle.classList.remove('done', 'active');
        if (i < step) circle.classList.add('done');
        else if (i === step && !data.is_cancelled) circle.classList.add('active');
    }

    // Update connectors
    for (let i = 0; i < 3; i++) {
        const conn = document.getElementById('conn-' + i);
        if (conn) { conn.classList.toggle('done', i < step); }
    }

    // Items list
    if (data.items && data.items.length) {
        const itemsEl = document.getElementById('items-list');
        if (lastStatus === null) { // only render items once
            itemsEl.innerHTML = data.items.map(item => `
                <div class="item-row">
                    ${item.image ? `<img src="../${item.image}" alt="" class="item-img-sm" style="width:40px;height:40px;border-radius:8px;object-fit:cover;">` : '<div class="item-img-sm">🍽️</div>'}
                    <div class="item-name-sm">${item.food_name}</div>
                    <div class="item-qty-sm">× ${item.quantity}</div>
                    <div class="item-price-sm">₹${(item.unit_price * item.quantity).toFixed(2)}</div>
                </div>
            `).join('');

            document.getElementById('fee-breakdown').innerHTML = `
                <div class="fee-row"><span>Subtotal</span><span>₹${data.items.reduce((a,i)=>a+(i.unit_price*i.quantity),0).toFixed(2)}</span></div>
                <div class="fee-row"><span>Delivery</span><span>₹${data.delivery_fee}</span></div>
                <div class="fee-row"><span>Tax</span><span>₹${data.tax}</span></div>
                ${parseFloat(data.discount) > 0 ? `<div class="fee-row" style="color:var(--green)"><span>Discount</span><span>−₹${data.discount}</span></div>` : ''}
                <div class="fee-row total"><span>Total</span><span style="color:var(--neon-cyan)">₹${data.total_price}</span></div>
            `;
        }
    }

    // Address
    document.getElementById('delivery-address').textContent = data.address || 'Not provided';

    // Payment info badge
    const payIcon = {COD:'💵', UPI:'📱', Card:'💳', Wallet:'👛'}[data.payment_method] || '💳';
    document.getElementById('delivery-address').insertAdjacentHTML('afterend',
        document.getElementById('pay-badge') ? '' :
        `<div id="pay-badge" style="margin-top:0.6rem;font-size:0.8rem;color:var(--text-secondary);">
            ${payIcon} <strong style="color:var(--text-primary);">${data.payment_method}</strong>
            ${data.txn_id ? '· TXN: <span style="font-family:monospace;color:var(--neon-cyan);">' + data.txn_id + '</span>' : ''}
        </div>`
    );

    lastStatus = data.status;

    // Stop polling if delivered or cancelled
    if (data.is_cancelled || data.status === 'Delivered') {
        clearInterval(pollInterval);
        document.querySelector('.live-badge').innerHTML = data.status === 'Delivered'
            ? '<span>✅ Order Delivered</span>'
            : '<span>❌ Order Cancelled</span>';
    }
}

function poll() {
    fetch('ajax/order-status.php?order_id=' + ORDER_ID)
    .then(r => r.json())
    .then(data => {
        if (data.success) updateTracker(data);
    })
    .catch(console.error);
}

// Initial load + polling every 15s
poll();
const pollInterval = setInterval(poll, 15000);
</script>
</body>
</html>
