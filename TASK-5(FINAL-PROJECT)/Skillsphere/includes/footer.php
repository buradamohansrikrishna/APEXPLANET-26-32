<?php
$rootPrefix = $rootPrefix ?? '';
?>
<footer class="site-footer footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-column">
                <h2 class="footer-logo">Skill<span>Sphere</span></h2>
                <p class="footer-text">
                    Enterprise-grade online learning for web development, AI, data science, and career-ready skills — built for modern teams and ambitious learners.
                </p>
                <div class="footer-social">
                    <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="#" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <div class="footer-column">
                <h3>Platform</h3>
                <ul>
                    <li><a href="<?php echo $rootPrefix; ?>index.php">Home</a></li>
                    <li><a href="<?php echo $rootPrefix; ?>courses.php">Courses</a></li>
                    <li><a href="<?php echo $rootPrefix; ?>about.php">About</a></li>
                    <li><a href="<?php echo $rootPrefix; ?>contact.php">Contact</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Categories</h3>
                <ul>
                    <li><a href="<?php echo $rootPrefix; ?>courses.php">Web Development</a></li>
                    <li><a href="<?php echo $rootPrefix; ?>courses.php">Artificial Intelligence</a></li>
                    <li><a href="<?php echo $rootPrefix; ?>courses.php">Data Science</a></li>
                    <li><a href="<?php echo $rootPrefix; ?>courses.php">Cyber Security</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Stay updated</h3>
                <p class="footer-text">Weekly drops on new courses and learning paths.</p>
                <form class="newsletter-form" action="#" method="post" onsubmit="return false;">
                    <input type="email" placeholder="you@company.com" aria-label="Email for newsletter">
                    <button type="submit" class="footer-btn">Subscribe</button>
                </form>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> SkillSphere. All rights reserved.</p>
            <p>Built for learners who ship.</p>
        </div>
    </div>
</footer>

<button type="button" class="scroll-top" aria-label="Scroll to top">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<?php include __DIR__ . '/chatbot.php'; ?>

<script src="<?php echo $rootPrefix; ?>assets/js/app.js"></script>
<script src="<?php echo $rootPrefix; ?>assets/js/animations.js"></script>
<script src="<?php echo $rootPrefix; ?>assets/js/search.js"></script>
<script src="<?php echo $rootPrefix; ?>assets/js/validation.js"></script>
</body>
</html>
