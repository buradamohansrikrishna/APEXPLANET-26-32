/* ================================================
   QUICKBITE 2.0 — CART JAVASCRIPT
================================================ */

/* ── UPDATE CART COUNT BADGE ─────────────────── */
function updateCartBadge(count) {
    const badges = document.querySelectorAll('.cart-badge, .float-cart-count');
    badges.forEach(b => {
        b.textContent = count;
        b.style.display = count > 0 ? 'flex' : 'none';
    });
}

/* ── ADD TO CART (AJAX) ─────────────────────── */
async function addToCart(foodId, qty = 1, btn = null) {
    if (btn) {
        const orig = btn.innerHTML;
        btn.innerHTML = '<span class="spinner"></span> Adding...';
        btn.disabled = true;
        btn.style.opacity = '0.8';
    }

    try {
        const res = await fetch('../user/ajax/add-to-cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `food_id=${foodId}&quantity=${qty}&csrf_token=${getCsrfToken()}`
        });
        const data = await res.json();

        if (data.success) {
            updateCartBadge(data.cart_count);
            showToast('success', 'Added to Cart!', data.food_name + ' added successfully.');

            // Animate float-cart-btn
            const floatBtn = document.querySelector('.float-cart-btn');
            if (floatBtn) {
                floatBtn.style.transform = 'scale(1.3)';
                setTimeout(() => floatBtn.style.transform = '', 300);
            }
        } else {
            if (data.redirect) { window.location.href = data.redirect; return; }
            showToast('error', 'Error', data.message || 'Could not add to cart.');
        }
    } catch (err) {
        showToast('error', 'Network Error', 'Please check your connection.');
    } finally {
        if (btn) {
            btn.innerHTML = '🛒 Add to Cart';
            btn.disabled = false;
            btn.style.opacity = '';
        }
    }
}

/* ── REMOVE FROM CART (AJAX) ─────────────────── */
async function removeFromCart(cartId, row = null) {
    try {
        const res = await fetch('../user/ajax/remove-from-cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `cart_id=${cartId}&csrf_token=${getCsrfToken()}`
        });
        const data = await res.json();
        if (data.success) {
            updateCartBadge(data.cart_count);
            if (row) {
                row.style.animation = 'fade-in 0.3s ease reverse';
                setTimeout(() => { row.remove(); updateCartTotals(data); }, 300);
            }
            showToast('info', 'Removed', 'Item removed from cart.');
        }
    } catch (err) {
        showToast('error', 'Error', 'Failed to remove item.');
    }
}

/* ── UPDATE QUANTITY (AJAX) ─────────────────── */
async function updateQuantity(cartId, newQty, subtotalEl = null) {
    if (newQty < 1) return;
    try {
        const res = await fetch('../user/ajax/update-quantity.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `cart_id=${cartId}&quantity=${newQty}&csrf_token=${getCsrfToken()}`
        });
        const data = await res.json();
        if (data.success) {
            if (subtotalEl) subtotalEl.textContent = '₹' + parseFloat(data.subtotal).toFixed(2);
            updateCartBadge(data.cart_count);
            updateCartTotals(data);
        }
    } catch (err) { /* silent */ }
}

/* ── UPDATE TOTALS IN CART PAGE ─────────────── */
function updateCartTotals(data) {
    const totalEl = document.getElementById('cart-grand-total');
    if (totalEl && data.total !== undefined) {
        totalEl.textContent = '₹' + parseFloat(data.total).toFixed(2);
    }
    const subtotalEl = document.getElementById('cart-subtotal');
    if (subtotalEl && data.subtotal !== undefined) {
        subtotalEl.textContent = '₹' + parseFloat(data.subtotal).toFixed(2);
    }
}

/* ── APPLY COUPON (AJAX) ─────────────────────── */
async function applyCoupon() {
    const input    = document.getElementById('coupon-input');
    const resultEl = document.getElementById('coupon-result');
    const code     = input?.value.trim().toUpperCase();

    if (!code) { showToast('warning', 'Enter Code', 'Please enter a coupon code.'); return; }

    try {
        const res = await fetch('../user/ajax/apply-coupon.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `code=${encodeURIComponent(code)}&csrf_token=${getCsrfToken()}`
        });
        const data = await res.json();

        if (data.valid) {
            if (resultEl) {
                resultEl.innerHTML = `<span style="color:var(--success-green)">✅ ${data.message}</span>`;
            }
            const discountEl = document.getElementById('cart-discount');
            if (discountEl) discountEl.textContent = '-₹' + parseFloat(data.discount).toFixed(2);
            updateCartTotals(data);
            showToast('success', 'Coupon Applied!', data.message);

            // Store applied coupon
            sessionStorage.setItem('applied_coupon', code);
            sessionStorage.setItem('coupon_discount', data.discount);
        } else {
            if (resultEl) {
                resultEl.innerHTML = `<span style="color:var(--danger-red)">❌ ${data.error}</span>`;
            }
            showToast('error', 'Invalid Coupon', data.error);
        }
    } catch (err) {
        showToast('error', 'Error', 'Failed to validate coupon.');
    }
}

/* ── TOGGLE FAVORITE (AJAX) ─────────────────── */
async function toggleFavorite(foodId, btn) {
    try {
        const res = await fetch('../user/ajax/toggle-favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `food_id=${foodId}&csrf_token=${getCsrfToken()}`
        });
        const data = await res.json();
        if (data.success) {
            btn.classList.toggle('active', data.is_favorite);
            btn.textContent = data.is_favorite ? '❤️' : '🤍';
            showToast(data.is_favorite ? 'success' : 'info',
                data.is_favorite ? 'Added to Favorites' : 'Removed from Favorites', '');
        } else if (data.redirect) {
            window.location.href = data.redirect;
        }
    } catch (err) { /* silent */ }
}

/* ── GET CSRF TOKEN ─────────────────────────── */
function getCsrfToken() {
    const el = document.querySelector('[name="csrf_token"]');
    return el ? el.value : '';
}

/* ── QUANTITY +/- BUTTONS ───────────────────── */
function initQtyButtons() {
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const cartId    = this.dataset.cartId;
            const isPlus    = this.dataset.action === 'plus';
            const numEl     = this.closest('.qty-controls').querySelector('.qty-num');
            const subtotEl  = this.closest('.cart-item')?.querySelector('.cart-item-subtotal');
            let current     = parseInt(numEl?.textContent || 1);
            const newQty    = isPlus ? current + 1 : Math.max(1, current - 1);

            if (numEl) numEl.textContent = newQty;
            if (cartId) updateQuantity(cartId, newQty, subtotEl);
        });
    });
}

/* ── CHECKOUT STEPS ─────────────────────────── */
function initCheckoutSteps() {
    const steps  = document.querySelectorAll('.checkout-step');
    const panels = document.querySelectorAll('.checkout-panel');
    if (!steps.length) return;

    window.goToStep = function(idx) {
        steps.forEach((s, i) => {
            s.classList.toggle('active', i === idx);
            s.classList.toggle('done', i < idx);
        });
        panels.forEach((p, i) => {
            p.style.display = i === idx ? 'block' : 'none';
            if (i === idx) p.classList.add('animate-fade-up');
        });
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
}

/* ── INIT ───────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    initQtyButtons();
    initCheckoutSteps();

    // Attach add-to-cart forms
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const fd     = new FormData(this);
            const foodId = fd.get('food_id');
            const qty    = parseInt(fd.get('quantity')) || 1;
            const btn    = this.querySelector('[type="submit"]');
            await addToCart(foodId, qty, btn);
        });
    });

    // Coupon form
    const couponForm = document.getElementById('coupon-form');
    if (couponForm) {
        couponForm.addEventListener('submit', e => { e.preventDefault(); applyCoupon(); });
    }
});