/* ================================================
   QUICKBITE 2.0 — MAIN JAVASCRIPT
================================================ */

/* ── SCROLL REVEAL ──────────────────────────── */
function initScrollReveal() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('[data-reveal]').forEach((el, i) => {
        el.style.transitionDelay = el.dataset.delay || `${i * 0.08}s`;
        observer.observe(el);
    });
}

/* ── NAVBAR SCROLL BEHAVIOR ─────────────────── */
function initNavbar() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    const updateNavbar = () => {
        navbar.classList.toggle('scrolled', window.scrollY > 20);
    };
    window.addEventListener('scroll', updateNavbar, { passive: true });
    updateNavbar();

    // Mobile hamburger
    const hamburger = document.querySelector('.hamburger');
    const navLinks  = document.querySelector('.nav-links');
    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('open');
        });
        // Close on link click
        navLinks.querySelectorAll('a').forEach(a => {
            a.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navLinks.classList.remove('open');
            });
        });
    }
}

/* ── COUNTER ANIMATION ──────────────────────── */
function animateCounter(el) {
    const target  = parseInt(el.dataset.target || el.textContent.replace(/\D/g, ''), 10);
    const prefix  = el.dataset.prefix  || '';
    const suffix  = el.dataset.suffix  || '';
    const duration = 1800;
    const start    = performance.now();

    function step(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased    = 1 - Math.pow(1 - progress, 3);
        el.textContent = prefix + Math.floor(eased * target).toLocaleString('en-IN') + suffix;
        if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

function initCounters() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                entry.target.classList.add('counted');
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('[data-counter]').forEach(el => observer.observe(el));
}

/* ── PARTICLE BACKGROUND ────────────────────── */
function initParticles() {
    const containers = document.querySelectorAll('.particles-bg');
    containers.forEach(container => {
        const count = window.innerWidth < 768 ? 15 : 30;
        for (let i = 0; i < count; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const colors = ['#00F7FF', '#3A86FF', '#9D4EDD', '#FF00FF', '#00FF88'];
            const color  = colors[Math.floor(Math.random() * colors.length)];
            const size   = Math.random() * 3 + 1;
            p.style.cssText = `
                left: ${Math.random() * 100}%;
                width: ${size}px; height: ${size}px;
                background: ${color};
                animation-duration: ${Math.random() * 15 + 8}s;
                animation-delay: ${Math.random() * 10}s;
                opacity: 0;
                box-shadow: 0 0 ${size * 2}px ${color};
            `;
            container.appendChild(p);
        }
    });
}

/* ── TOAST NOTIFICATION SYSTEM ──────────────── */
let toastContainer;

function initToasts() {
    toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }
}

window.showToast = function(type, title, message, duration = 4000) {
    const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <span class="toast-icon">${icons[type] || 'ℹ️'}</span>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            ${message ? `<div class="toast-msg">${message}</div>` : ''}
        </div>
        <button class="toast-close" onclick="this.closest('.toast').remove()">×</button>
    `;
    toastContainer.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'fade-in 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, duration);
};

/* ── CART DRAWER ────────────────────────────── */
function initCartDrawer() {
    const overlay = document.getElementById('cartOverlay');
    const drawer  = document.getElementById('cartDrawer');
    const openBtns = document.querySelectorAll('[data-open-cart]');
    const closeBtns = document.querySelectorAll('[data-close-cart]');

    if (!drawer) return;

    const open  = () => { drawer.classList.add('open'); overlay && overlay.classList.add('open'); document.body.style.overflow = 'hidden'; };
    const close = () => { drawer.classList.remove('open'); overlay && overlay.classList.remove('open'); document.body.style.overflow = ''; };

    openBtns.forEach(btn => btn.addEventListener('click', open));
    closeBtns.forEach(btn => btn.addEventListener('click', close));
    overlay && overlay.addEventListener('click', close);
}

/* ── SMOOTH SCROLL FOR ANCHORS ──────────────── */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}

/* ── THEME (dark mode only for now) ─────────── */
function initTheme() {
    // Future: toggle between themes
    document.documentElement.setAttribute('data-theme', 'dark');
}

/* ── ACTIVE NAV LINK ────────────────────────── */
function setActiveNavLink() {
    const path = window.location.pathname;
    document.querySelectorAll('.nav-links a').forEach(a => {
        a.classList.toggle('active', path.includes(a.getAttribute('href')?.replace('../', '').replace('/', '').split('?')[0]));
    });
}

/* ── MODAL SYSTEM ───────────────────────────── */
window.openModal = function(id) {
    const modal = document.getElementById(id);
    if (modal) { modal.classList.add('open'); document.body.style.overflow = 'hidden'; }
};

window.closeModal = function(id) {
    const modal = document.getElementById(id);
    if (modal) { modal.classList.remove('open'); document.body.style.overflow = ''; }
};

// Close modal on overlay click
document.addEventListener('click', e => {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('open');
        document.body.style.overflow = '';
    }
});

// Close modal on Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.open').forEach(m => {
            m.classList.remove('open');
            document.body.style.overflow = '';
        });
    }
});

/* ── IMAGE PREVIEW ──────────────────────────── */
function initImagePreviews() {
    document.querySelectorAll('[data-image-input]').forEach(input => {
        const previewId = input.dataset.imageInput;
        const preview   = document.getElementById(previewId);
        if (!preview) return;
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    preview.classList.add('animate-scale-in');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
}

/* ── DRAG & DROP UPLOAD ─────────────────────── */
function initDropZones() {
    document.querySelectorAll('.drop-zone').forEach(zone => {
        const input = zone.querySelector('input[type="file"]');
        if (!input) return;

        ['dragenter','dragover'].forEach(evt => {
            zone.addEventListener(evt, e => { e.preventDefault(); zone.classList.add('drag-over'); });
        });
        ['dragleave','dragend','drop'].forEach(evt => {
            zone.addEventListener(evt, () => zone.classList.remove('drag-over'));
        });
        zone.addEventListener('drop', e => {
            e.preventDefault();
            const files = e.dataTransfer.files;
            if (files.length) { input.files = files; input.dispatchEvent(new Event('change')); }
        });
        zone.addEventListener('click', () => input.click());
    });
}

/* ── PASSWORD STRENGTH ──────────────────────── */
function initPasswordStrength() {
    const inputs = document.querySelectorAll('[data-password-strength]');
    inputs.forEach(input => {
        const barId = input.dataset.passwordStrength;
        const bar   = document.getElementById(barId);
        if (!bar) return;

        input.addEventListener('input', () => {
            const val = input.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = ['', 'weak', 'fair', 'good', 'strong'];
            const colors = ['', '#EF4444', '#F59E0B', '#3B82F6', '#10B981'];
            bar.style.width = `${score * 25}%`;
            bar.style.background = colors[score] || 'transparent';
            bar.dataset.level = levels[score] || '';
        });
    });
}

/* ── TESTIMONIAL CAROUSEL ───────────────────── */
function initCarousel() {
    document.querySelectorAll('[data-carousel]').forEach(carousel => {
        const track  = carousel.querySelector('.carousel-track');
        const slides = carousel.querySelectorAll('.carousel-slide');
        const prev   = carousel.querySelector('[data-prev]');
        const next   = carousel.querySelector('[data-next]');
        if (!track || !slides.length) return;

        let current = 0;
        const total = slides.length;

        const go = (idx) => {
            current = (idx + total) % total;
            track.style.transform = `translateX(-${current * 100}%)`;
            carousel.querySelectorAll('.carousel-dot').forEach((d, i) => {
                d.classList.toggle('active', i === current);
            });
        };

        prev && prev.addEventListener('click', () => go(current - 1));
        next && next.addEventListener('click', () => go(current + 1));

        // Auto play
        if (carousel.dataset.carousel === 'auto') {
            setInterval(() => go(current + 1), 4500);
        }
    });
}

/* ── INIT ALL ───────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initNavbar();
    initScrollReveal();
    initCounters();
    initParticles();
    initToasts();
    initCartDrawer();
    initSmoothScroll();
    setActiveNavLink();
    initImagePreviews();
    initDropZones();
    initPasswordStrength();
    initCarousel();

    // Show queued flash messages from PHP
    const flash = document.getElementById('flash-data');
    if (flash) {
        const { type, title, msg } = flash.dataset;
        if (type) setTimeout(() => showToast(type, title, msg), 300);
    }

    console.log('%cQuickBite 2.0 🚀', 'color:#00F7FF;font-size:16px;font-weight:700');
});