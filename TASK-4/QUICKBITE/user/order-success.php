<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$order_number  = htmlspecialchars($_GET['order']  ?? 'QB------');
$grand_total   = htmlspecialchars($_GET['total']  ?? '0.00');
$payment_method= htmlspecialchars($_GET['method'] ?? 'COD');
$txn_id        = htmlspecialchars($_GET['txn']    ?? '');
$user_name     = $_SESSION['user_name'] ?? 'Customer';

$method_icons = [
    'COD'        => '💵',
    'UPI'        => '📱',
    'Card'       => '💳',
    'Wallet'     => '👛',
    'Netbanking' => '🏦',
];
$method_icon = $method_icons[$payment_method] ?? '💳';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed! — QuickBite</title>
    <meta name="description" content="Your QuickBite order has been placed successfully.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root{
            --neon-cyan:#FF5A00;--bg-dark:#F8FAFC;--bg-secondary:#FFFFFF;
            --bg-card:#FFFFFF;--text-primary:#0F172A;--text-secondary:#475569;
            --border-glass:#E2E8F0;--green:#00D084;
            --grad:linear-gradient(135deg,#FF4747,#3A86FF);
        }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:var(--bg-dark);color:var(--text-primary);min-height:100vh;overflow-x:hidden;}

        .success-page{
            min-height:100vh;display:flex;flex-direction:column;align-items:center;
            justify-content:center;padding:4rem 1.5rem;position:relative;
        }

        /* confetti canvas */
        #confettiCanvas{position:fixed;inset:0;pointer-events:none;z-index:0;}

        .success-card{
            background:var(--bg-card);border:1px solid rgba(255,71,71,0.15);
            border-radius:24px;padding:3rem 2.5rem;max-width:540px;width:100%;
            text-align:center;position:relative;z-index:1;
            backdrop-filter:blur(20px);
            box-shadow:0 0 60px rgba(255,71,71,0.06),0 30px 80px rgba(0,0,0,0.4);
            animation:cardIn 0.6s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        @keyframes cardIn{from{opacity:0;transform:translateY(40px) scale(0.92);}to{opacity:1;transform:translateY(0) scale(1);}}

        /* success icon */
        .success-icon{
            width:100px;height:100px;border-radius:50%;
            background:linear-gradient(135deg,#00D084,#059669);
            margin:0 auto 1.8rem;
            display:flex;align-items:center;justify-content:center;
            font-size:3rem;
            box-shadow:0 0 40px rgba(0,208,132,0.4);
            animation:iconPop 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.3s both;
        }
        @keyframes iconPop{from{transform:scale(0);}to{transform:scale(1);}}

        .success-title{font-size:1.9rem;font-weight:800;margin-bottom:0.5rem;}
        .success-subtitle{color:var(--text-secondary);font-size:0.95rem;margin-bottom:2rem;line-height:1.6;}

        /* order details */
        .order-details-grid{
            display:grid;grid-template-columns:1fr 1fr;gap:0.8rem;margin-bottom:1.8rem;
        }
        .detail-cell{
            background:rgba(255,255,255,0.03);border:1px solid var(--border-glass);
            border-radius:14px;padding:1rem;text-align:left;
        }
        .detail-label{font-size:0.72rem;color:var(--text-secondary);font-weight:600;margin-bottom:0.3rem;text-transform:uppercase;letter-spacing:0.5px;}
        .detail-value{font-size:1rem;font-weight:700;}
        .detail-value.highlight{background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}

        /* status timeline */
        .status-timeline{
            display:flex;align-items:center;justify-content:space-between;
            margin-bottom:2rem;padding:1.2rem;
            background:rgba(255,255,255,0.02);border:1px solid var(--border-glass);border-radius:16px;
        }
        .status-step{display:flex;flex-direction:column;align-items:center;gap:0.3rem;flex:1;}
        .status-step-icon{
            width:40px;height:40px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;font-size:1.1rem;
            border:2px solid var(--border-glass);background:var(--bg-secondary);
            transition:all 0.3s;
        }
        .status-step.active .status-step-icon{
            background:rgba(0,208,132,0.15);border-color:var(--green);
            box-shadow:0 0 15px rgba(0,208,132,0.3);
        }
        .status-step-label{font-size:0.65rem;color:var(--text-secondary);font-weight:600;text-align:center;}
        .status-step.active .status-step-label{color:var(--green);}
        .status-connector{height:2px;flex:1;background:var(--border-glass);margin-bottom:1.5rem;}
        .status-connector.done{background:var(--green);}

        /* action buttons */
        .action-btns{display:flex;gap:0.8rem;flex-wrap:wrap;}
        .btn-track{
            flex:1;padding:0.9rem;
            background:var(--grad);border:none;border-radius:12px;
            color:#0F172A;font-weight:800;font-size:0.95rem;
            cursor:pointer;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:0.5rem;
            transition:all 0.3s;box-shadow:0 4px 20px rgba(255,71,71,0.3);
        }
        .btn-track:hover{opacity:0.9;transform:translateY(-2px);}
        .btn-secondary{
            flex:1;padding:0.9rem;
            background:rgba(255,255,255,0.05);border:1px solid var(--border-glass);border-radius:12px;
            color:var(--text-primary);font-weight:700;font-size:0.95rem;
            cursor:pointer;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:0.5rem;
            transition:all 0.3s;
        }
        .btn-secondary:hover{background:rgba(255,255,255,0.1);}

        /* COD notice */
        .cod-notice{
            background:rgba(249,115,22,0.08);border:1px solid rgba(249,115,22,0.25);
            border-radius:12px;padding:0.9rem 1.1rem;margin-bottom:1.5rem;
            font-size:0.82rem;color:#F97316;text-align:left;line-height:1.5;
        }

        @media(max-width:500px){
            .success-card{padding:2rem 1.2rem;}
            .order-details-grid{grid-template-columns:1fr;}
            .success-title{font-size:1.5rem;}
        }
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<canvas id="confettiCanvas"></canvas>

<div class="success-page">
    <div class="success-card">

        <div class="success-icon">✓</div>

        <h1 class="success-title">Order Confirmed! 🎉</h1>
        <p class="success-subtitle">
            Hey <?= htmlspecialchars($user_name) ?>! Your food is on its way.<br>
            Estimated delivery in <strong style="color:var(--neon-cyan);">~35 minutes</strong>.
        </p>

        <!-- ORDER DETAILS GRID -->
        <div class="order-details-grid">
            <div class="detail-cell">
                <div class="detail-label">Order Number</div>
                <div class="detail-value highlight"><?= $order_number ?></div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Amount Paid</div>
                <div class="detail-value">₹<?= $grand_total ?></div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Payment</div>
                <div class="detail-value"><?= $method_icon ?> <?= $payment_method ?></div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Status</div>
                <div class="detail-value" style="color:var(--green);">✅ Confirmed</div>
            </div>
        </div>

        <?php if ($txn_id): ?>
        <div style="font-size:0.75rem;color:var(--text-secondary);margin-bottom:1.2rem;font-family:monospace;">
            Transaction ID: <span style="color:var(--neon-cyan);"><?= $txn_id ?></span>
        </div>
        <?php endif; ?>

        <?php if ($payment_method === 'COD'): ?>
        <div class="cod-notice">
            💵 <strong>Cash on Delivery</strong> — Please keep ₹<?= $grand_total ?> ready when your delivery partner arrives.
        </div>
        <?php endif; ?>

        <!-- STATUS TIMELINE -->
        <div class="status-timeline">
            <div class="status-step active">
                <div class="status-step-icon">✅</div>
                <div class="status-step-label">Order<br>Placed</div>
            </div>
            <div class="status-connector done"></div>
            <div class="status-step">
                <div class="status-step-icon">👨‍🍳</div>
                <div class="status-step-label">Preparing</div>
            </div>
            <div class="status-connector"></div>
            <div class="status-step">
                <div class="status-step-icon">🛵</div>
                <div class="status-step-label">On the Way</div>
            </div>
            <div class="status-connector"></div>
            <div class="status-step">
                <div class="status-step-icon">🎉</div>
                <div class="status-step-label">Delivered</div>
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="action-btns">
            <a href="orders.php" class="btn-track">📦 Track Order</a>
            <a href="restaurants.php" class="btn-secondary">🍽️ Order More</a>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// ── CONFETTI ────────────────────────────────────────
(function() {
    const canvas = document.getElementById('confettiCanvas');
    const ctx    = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    window.addEventListener('resize', () => { canvas.width = window.innerWidth; canvas.height = window.innerHeight; });

    const colors = ['#FF4747','#3A86FF','#9D4EDD','#00D084','#F59E0B','#FF6B9D','#FFD700'];
    const pieces = Array.from({length: 120}, () => ({
        x: Math.random() * canvas.width,
        y: Math.random() * -canvas.height,
        w: Math.random() * 10 + 5,
        h: Math.random() * 6 + 3,
        color: colors[Math.floor(Math.random() * colors.length)],
        vy: Math.random() * 3 + 2,
        vx: (Math.random() - 0.5) * 2,
        rot: Math.random() * 360,
        rotV: (Math.random() - 0.5) * 6,
        opacity: 1,
    }));

    let frame = 0;
    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        pieces.forEach(p => {
            ctx.save();
            ctx.globalAlpha = p.opacity;
            ctx.translate(p.x + p.w/2, p.y + p.h/2);
            ctx.rotate(p.rot * Math.PI / 180);
            ctx.fillStyle = p.color;
            ctx.fillRect(-p.w/2, -p.h/2, p.w, p.h);
            ctx.restore();

            p.x   += p.vx;
            p.y   += p.vy;
            p.rot += p.rotV;

            if (frame > 120) p.opacity -= 0.005;
            if (p.y > canvas.height) {
                p.y = -20;
                p.x = Math.random() * canvas.width;
                p.opacity = 1;
            }
        });
        frame++;
        if (frame < 300) requestAnimationFrame(draw);
        else ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
    draw();
})();
</script>
</body>
</html>
