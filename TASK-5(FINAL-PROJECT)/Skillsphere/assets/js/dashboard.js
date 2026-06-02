// =========================================
// SKILLSPHERE DASHBOARD ACTIONS
// assets/js/dashboard.js
// =========================================

document.addEventListener('DOMContentLoaded', () => {
    // Sidebar toggle for admin/instructor/student panels
    const toggleBtn = document.getElementById('adminSidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('is-visible');
        });
    }

    // Dashboard dynamic tab switching
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabPanels = document.querySelectorAll('.tab-panel');
    tabLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('data-tab');

            tabLinks.forEach(l => l.classList.remove('is-active'));
            tabPanels.forEach(p => p.setAttribute('hidden', 'true'));

            link.classList.add('is-active');
            const targetPanel = document.getElementById(targetId);
            if (targetPanel) {
                targetPanel.removeAttribute('hidden');
            }
        });
    });
});
