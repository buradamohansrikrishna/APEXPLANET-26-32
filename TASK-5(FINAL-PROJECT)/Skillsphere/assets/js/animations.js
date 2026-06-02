/* SkillSphere — Animations */

const revealObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('active');
        revealObserver.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
);

document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach((el) => {
  revealObserver.observe(el);
});

/* Counter animation */
const counterObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      const counter = entry.target;
      const target = parseInt(counter.getAttribute('data-target'), 10) || 0;
      const duration = 1800;
      const start = performance.now();

      const tick = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        counter.textContent = Math.floor(eased * target).toLocaleString();
        if (progress < 1) requestAnimationFrame(tick);
        else counter.textContent = target.toLocaleString();
      };

      requestAnimationFrame(tick);
      counterObserver.unobserve(counter);
    });
  },
  { threshold: 0.5 }
);

document.querySelectorAll('.counter').forEach((c) => counterObserver.observe(c));

/* Button ripple */
document.querySelectorAll('.btn').forEach((button) => {
  button.addEventListener('click', function (e) {
    const ripple = document.createElement('span');
    ripple.classList.add('ripple');
    const rect = this.getBoundingClientRect();
    ripple.style.left = `${e.clientX - rect.left}px`;
    ripple.style.top = `${e.clientY - rect.top}px`;
    ripple.style.width = ripple.style.height = '20px';
    this.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);
  });
});

/* Scroll to top */
const scrollBtn = document.querySelector('.scroll-top');
if (scrollBtn) {
  window.addEventListener('scroll', () => {
    scrollBtn.classList.toggle('show', window.scrollY > 400);
  }, { passive: true });

  scrollBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

/* Page loaded */
window.addEventListener('load', () => {
  document.body.classList.add('loaded');
});

/* Parallax */
const parallax = document.querySelector('.parallax');
if (parallax) {
  window.addEventListener('scroll', () => {
    parallax.style.transform = `translateY(${window.pageYOffset * 0.35}px)`;
  }, { passive: true });
}

/* Float stagger */
document.querySelectorAll('.float').forEach((item, index) => {
  item.style.animationDelay = `${index * 0.15}s`;
});
