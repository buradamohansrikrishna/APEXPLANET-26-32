// =========================================
// SKILLSPHERE MOCK REAL-TIME NOTIFICATIONS
// assets/js/realtime.js
// =========================================

document.addEventListener('DOMContentLoaded', () => {
    // Simulate real-time announcements or ranking updates every 90 seconds
    setInterval(() => {
        const events = [
            "Ravi Teja just completed 'Docker & Kubernetes DevOps' course!",
            "Dr. Sravani Devi added a new lesson in 'AI & Deep Learning Bootcamp'!",
            "You are now ranked 3rd on the Leaderboard!",
            "System performance optimized. Cached modules flushed."
        ];
        const randomEvent = events[Math.floor(Math.random() * events.length)];
        
        if (typeof Notifications !== 'undefined') {
            Notifications.toast(randomEvent, 'info');
        }
    }, 90000);
});
