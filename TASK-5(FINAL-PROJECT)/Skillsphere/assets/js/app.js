/* SkillSphere — Main Application */

document.addEventListener('DOMContentLoaded', () => {
  initStickyHeader();
  initMobileNav();
  initPasswordToggle();
  initFaq();
  initSmoothScroll();
});

function initStickyHeader() {
  const header = document.getElementById('siteHeader') || document.querySelector('.header');
  if (!header) return;

  const onScroll = () => {
    header.classList.toggle('scrolled', window.scrollY > 24);
  };

  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
}

function initMobileNav() {
  const menuBtn = document.getElementById('menuToggle');
  const navLinks = document.getElementById('navLinks');

  if (!menuBtn || !navLinks) return;

  menuBtn.addEventListener('click', () => {
    const open = navLinks.classList.toggle('active');
    menuBtn.classList.toggle('open', open);
    menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  navLinks.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      navLinks.classList.remove('active');
      menuBtn.classList.remove('open');
      menuBtn.setAttribute('aria-expanded', 'false');
    });
  });
}

function initPasswordToggle() {
  document.querySelectorAll('.toggle-password').forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-target');
      const input = document.getElementById(id);
      if (!input) return;

      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      const icon = btn.querySelector('i');
      if (icon) {
        icon.classList.toggle('fa-eye', !show);
        icon.classList.toggle('fa-eye-slash', show);
      }
    });
  });
}

function initFaq() {
  document.querySelectorAll('.faq-item').forEach((item) => {
    const question = item.querySelector('.faq-question');
    if (!question) return;

    question.addEventListener('click', () => {
      const wasActive = item.classList.contains('active');
      document.querySelectorAll('.faq-item.active').forEach((el) => {
        el.classList.remove('active');
        el.querySelector('.faq-question')?.setAttribute('aria-expanded', 'false');
      });

      if (!wasActive) {
        item.classList.add('active');
        question.setAttribute('aria-expanded', 'true');
      }
    });
  });
}

function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', (e) => {
      const href = anchor.getAttribute('href');
      if (!href || href.length < 2) return;
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
}

function showNotification(message, type = 'success') {
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;
  notification.setAttribute('role', 'status');
  notification.textContent = message;
  document.body.appendChild(notification);
  requestAnimationFrame(() => notification.classList.add('show'));
  setTimeout(() => notification.remove(), 3200);
}

function copyText(text) {
  navigator.clipboard.writeText(text).then(() => {
    showNotification('Copied to clipboard', 'success');
  });
}

window.showNotification = showNotification;
window.copyText = copyText;
