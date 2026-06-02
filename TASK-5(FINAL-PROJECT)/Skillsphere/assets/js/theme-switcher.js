(function () {
  const STORAGE_KEY = 'skillsphere-theme';

  function getSystemTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function getStoredTheme() {
    return localStorage.getItem(STORAGE_KEY);
  }

  function applyTheme(theme, animate) {
    const root = document.documentElement;
    if (animate) root.classList.add('theme-transition');
    root.setAttribute('data-theme', theme);
    if (animate) {
      window.setTimeout(function () {
        root.classList.remove('theme-transition');
      }, 400);
    }
  }

  function initTheme() {
    const stored = getStoredTheme();
    const theme = stored || getSystemTheme();
    applyTheme(theme, false);
    return theme;
  }

  function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme') || getSystemTheme();
    const next = current === 'dark' ? 'light' : 'dark';
    localStorage.setItem(STORAGE_KEY, next);
    applyTheme(next, true);
    return next;
  }

  initTheme();

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
    if (!getStoredTheme()) {
      applyTheme(e.matches ? 'dark' : 'light', true);
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
      btn.addEventListener('click', toggleTheme);
    });
  });

  window.SkillSphereTheme = { toggle: toggleTheme, apply: applyTheme, init: initTheme };
})();
