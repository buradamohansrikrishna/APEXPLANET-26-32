/* ================================================
   QUICKBITE 2.0 — ADMIN DASHBOARD JAVASCRIPT
================================================ */

/* ── LIVE CLOCK ─────────────────────────────── */
function initClock() {
    const timeEl = document.getElementById('adminClock');
    const dateEl = document.getElementById('adminDate');
    if (!timeEl) return;

    function tick() {
        const now  = new Date();
        const h    = String(now.getHours()).padStart(2, '0');
        const m    = String(now.getMinutes()).padStart(2, '0');
        const s    = String(now.getSeconds()).padStart(2, '0');
        const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        timeEl.textContent = `${h}:${m}:${s}`;
        if (dateEl) {
            dateEl.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        }
    }
    tick();
    setInterval(tick, 1000);
}

/* ── SIDEBAR COLLAPSE ───────────────────────── */
function initSidebar() {
    const sidebar   = document.getElementById('adminSidebar');
    const mainArea  = document.getElementById('adminMain');
    const toggle    = document.getElementById('sidebarToggle');
    if (!sidebar) return;

    const KEY = 'qb_sidebar_collapsed';
    const setCollapsed = (collapsed) => {
        sidebar.classList.toggle('collapsed', collapsed);
        mainArea && mainArea.classList.toggle('expanded', collapsed);
        localStorage.setItem(KEY, collapsed ? '1' : '0');
    };

    // Restore state
    setCollapsed(localStorage.getItem(KEY) === '1');

    toggle && toggle.addEventListener('click', () => {
        setCollapsed(!sidebar.classList.contains('collapsed'));
    });
}

/* ── NOTIFICATION DROPDOWN ──────────────────── */
function initNotifDropdown() {
    const bell     = document.querySelector('.notif-bell');
    const bellBtn  = document.querySelector('.notif-bell-btn');
    if (!bell || !bellBtn) return;

    bellBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        bell.classList.toggle('open');
    });

    document.addEventListener('click', (e) => {
        if (!bell.contains(e.target)) bell.classList.remove('open');
    });
}

/* ── CHART.JS DEFAULTS ──────────────────────── */
function setChartDefaults() {
    if (typeof Chart === 'undefined') return;
    Chart.defaults.color            = '#94A3B8';
    Chart.defaults.font.family      = 'Inter, sans-serif';
    Chart.defaults.plugins.legend.labels.boxWidth = 12;
    Chart.defaults.plugins.tooltip.backgroundColor = '#0B1020';
    Chart.defaults.plugins.tooltip.borderColor      = 'rgba(0,247,255,0.3)';
    Chart.defaults.plugins.tooltip.borderWidth      = 1;
    Chart.defaults.plugins.tooltip.cornerRadius     = 10;
    Chart.defaults.plugins.tooltip.padding          = 12;
}

/* ── REVENUE CHART ──────────────────────────── */
function initRevenueChart(labels, data) {
    const ctx = document.getElementById('revenueChart');
    if (!ctx || typeof Chart === 'undefined') return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Revenue (₹)',
                data,
                borderColor: '#00F7FF',
                backgroundColor: 'rgba(0,247,255,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#00F7FF',
                pointRadius: 4,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748B' } },
                y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748B', callback: v => '₹' + v.toLocaleString() }, beginAtZero: true }
            }
        }
    });
}

/* ── ORDERS CHART ───────────────────────────── */
function initOrdersChart(labels, data) {
    const ctx = document.getElementById('ordersChart');
    if (!ctx || typeof Chart === 'undefined') return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Orders',
                data,
                backgroundColor: 'rgba(58,134,255,0.6)',
                borderColor: '#3A86FF',
                borderWidth: 1,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#64748B' } },
                y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748B' }, beginAtZero: true }
            }
        }
    });
}

/* ── ORDERS STATUS DONUT ─────────────────────── */
function initStatusChart(pending, preparing, delivered, cancelled) {
    const ctx = document.getElementById('statusChart');
    if (!ctx || typeof Chart === 'undefined') return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Preparing', 'Delivered', 'Cancelled'],
            datasets: [{
                data: [pending, preparing, delivered, cancelled],
                backgroundColor: ['rgba(245,158,11,0.8)', 'rgba(139,92,246,0.8)', 'rgba(0,255,136,0.8)', 'rgba(239,68,68,0.8)'],
                borderColor: '#0B1020',
                borderWidth: 3,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { position: 'bottom' } }
        }
    });
}

/* ── USER GROWTH CHART ──────────────────────── */
function initUserGrowthChart(labels, data) {
    const ctx = document.getElementById('userGrowthChart');
    if (!ctx || typeof Chart === 'undefined') return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'New Users',
                data,
                borderColor: '#9D4EDD',
                backgroundColor: 'rgba(157,78,221,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#9D4EDD',
                pointRadius: 4,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748B' } },
                y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748B' }, beginAtZero: true }
            }
        }
    });
}

/* ── FOOD POPULARITY CHART ──────────────────── */
function initFoodChart(labels, data) {
    const ctx = document.getElementById('foodPopularityChart');
    if (!ctx || typeof Chart === 'undefined') return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Orders',
                data,
                backgroundColor: [
                    'rgba(0,247,255,0.7)', 'rgba(58,134,255,0.7)', 'rgba(157,78,221,0.7)',
                    'rgba(255,0,255,0.7)', 'rgba(0,255,136,0.7)'
                ],
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748B' }, beginAtZero: true },
                y: { grid: { display: false }, ticks: { color: '#94A3B8' } }
            }
        }
    });
}

/* ── STATUS UPDATE ──────────────────────────── */
async function updateOrderStatus(orderId, newStatus, badge) {
    try {
        const res = await fetch('ajax/update-order-status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `order_id=${orderId}&status=${encodeURIComponent(newStatus)}&csrf_token=${document.querySelector('[name="csrf_token"]')?.value || ''}`
        });
        const data = await res.json();
        if (data.success) {
            if (badge) {
                badge.textContent = newStatus;
                badge.className   = 'badge badge-' + newStatus.toLowerCase().replace(/\s+/g, '');
            }
            showToast('success', 'Status Updated', `Order #${orderId} → ${newStatus}`);
        } else {
            showToast('error', 'Error', data.message || 'Failed to update status.');
        }
    } catch (e) {
        showToast('error', 'Network Error', 'Could not reach the server.');
    }
}

/* ── TABLE SEARCH FILTER ────────────────────── */
function initTableSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;

    input.addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        table.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    });
}

/* ── INIT ───────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    initClock();
    initSidebar();
    initNotifDropdown();
    setChartDefaults();
    initTableSearch('adminSearchInput', 'adminDataTable');

    // Status select dropdowns
    document.querySelectorAll('[data-status-select]').forEach(select => {
        select.addEventListener('change', function() {
            const orderId = this.dataset.statusSelect;
            const badge   = document.getElementById('badge-' + orderId);
            updateOrderStatus(orderId, this.value, badge);
        });
    });
});
