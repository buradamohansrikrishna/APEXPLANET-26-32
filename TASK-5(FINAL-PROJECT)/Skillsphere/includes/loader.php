<!-- =========================================
     SKILLSPHERE LOADER COMPONENT
     includes/loader.php
========================================= -->
<div class="loader-overlay" id="pageLoader">
    <div class="loader-spinner">
        <div class="spinner-ring"></div>
        <div class="spinner-logo">S</div>
    </div>
    <p class="loader-text">Loading SkillSphere...</p>
</div>
<script>
window.addEventListener('load', () => {
    const loader = document.getElementById('pageLoader');
    if (loader) {
        loader.style.opacity = '0';
        setTimeout(() => loader.style.display = 'none', 500);
    }
});
</script>
