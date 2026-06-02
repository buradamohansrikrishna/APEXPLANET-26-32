// =========================================
// SKILLSPHERE CLIENT TOAST NOTIFICATIONS
// assets/js/notifications.js
// =========================================

const Notifications = {
    toast(message, type = 'success') {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast-message toast-${type}`;
        
        let icon = '<i class="fa-solid fa-circle-check"></i>';
        if (type === 'error') icon = '<i class="fa-solid fa-circle-xmark"></i>';
        if (type === 'warning') icon = '<i class="fa-solid fa-triangle-exclamation"></i>';
        if (type === 'info') icon = '<i class="fa-solid fa-circle-info"></i>';

        toast.innerHTML = `${icon}<span>${message}</span>`;
        container.appendChild(toast);

        // Slide in
        setTimeout(() => toast.classList.add('show'), 10);

        // Remove
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }
};
window.Notifications = Notifications;
