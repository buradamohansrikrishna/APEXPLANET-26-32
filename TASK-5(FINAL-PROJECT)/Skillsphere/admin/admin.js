document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('adminSidebar');
  const toggle = document.getElementById('adminSidebarToggle');

  if (toggle && sidebar) {
    toggle.addEventListener('click', () => {
      sidebar.classList.toggle('is-open');
    });
    document.addEventListener('click', (e) => {
      if (sidebar.classList.contains('is-open') && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('is-open');
      }
    });
  }

  const reveals = document.querySelectorAll('.reveal');
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1 }
  );
  reveals.forEach((el) => observer.observe(el));
});
