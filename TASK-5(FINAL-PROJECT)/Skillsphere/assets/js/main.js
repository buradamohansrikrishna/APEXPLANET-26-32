// =========================================
// SKILLSPHERE MAIN JS
// assets/js/main.js
// =========================================

document.addEventListener('DOMContentLoaded', () => {
    console.log('SkillSphere initialized');

    // Alert auto-fadeout
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // Mobile nav toggle
    const navToggle = document.querySelector('.navbar__toggle');
    const navMenu = document.querySelector('.navbar__menu');
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            const expanded = navToggle.getAttribute('aria-expanded') === 'true' || false;
            navToggle.setAttribute('aria-expanded', !expanded);
            navMenu.classList.toggle('is-active');
        });
    }
});
