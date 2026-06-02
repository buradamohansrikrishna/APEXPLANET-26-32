<?php
$pageTitle = 'About';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <span class="badge badge-primary">Our story</span>
        <h1 class="fade">About SkillSphere</h1>
        <p class="fade">Empowering learners with modern skills, industry-ready courses, and an experience built for professionals.</p>
    </div>
</section>

<section class="section about-section">
    <div class="about-glow"></div>
    <div class="container">
        <div class="about-grid">
            <div class="about-content reveal">
                <span class="badge badge-primary"><i class="fa-solid fa-graduation-cap"></i> Our mission</span>
                <h2>Learn skills that build your future</h2>
                <p class="about-lead">SkillSphere is a next-generation learning platform designed to help you master real-world technology through practical, engaging, industry-focused courses.</p>
                
                <ul class="about-list">
                    <li>
                        <div class="about-list__icon"><i class="fa-solid fa-check"></i></div>
                        <div>
                            <strong>Modern Curriculum</strong>
                            <p>We focus on web development, artificial intelligence, data science, cyber security, mobile development, and modern software engineering.</p>
                        </div>
                    </li>
                    <li>
                        <div class="about-list__icon"><i class="fa-solid fa-check"></i></div>
                        <div>
                            <strong>Project-Based Approach</strong>
                            <p>Our platform combines premium UX, interactive learning, expert instructors, and project-based education — so every hour you invest moves your career forward.</p>
                        </div>
                    </li>
                </ul>

                <div class="about-actions">
                    <a href="courses.php" class="btn btn-primary">Explore courses</a>
                    <a href="contact.php" class="btn btn-outline">Contact us</a>
                </div>
            </div>
            <div class="about-image-wrapper reveal stagger-2">
                <div class="about-image-glow"></div>
                <div class="about-image">
                    <img src="assets/images/illustrations/hero-learning.png" alt="About SkillSphere" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section courses-section">
    <div class="container">
        <header class="section-header reveal">
            <span class="badge badge-primary">Why choose us</span>
            <h2>The future of learning</h2>
            <p>Everything you need for a modern, effective learning journey.</p>
        </header>
        <div class="cards-grid">
            <div class="card feature-card reveal">
                <div class="feature-card__icon"><i class="fa-solid fa-bullseye"></i></div>
                <h3 class="feature-card__title">Industry focused</h3>
                <p class="feature-card__text">Trending technologies and practical skills employers hire for today.</p>
            </div>
            <div class="card feature-card reveal stagger-1">
                <div class="feature-card__icon"><i class="fa-solid fa-laptop-code"></i></div>
                <h3 class="feature-card__title">Hands-on projects</h3>
                <p class="feature-card__text">Strengthen your portfolio with guided builds and milestones.</p>
            </div>
            <div class="card feature-card reveal stagger-2">
                <div class="feature-card__icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                <h3 class="feature-card__title">Modern platform</h3>
                <p class="feature-card__text">Advanced UI, smooth flows, and dashboards you'll enjoy using daily.</p>
            </div>
            <div class="card feature-card reveal stagger-3">
                <div class="feature-card__icon"><i class="fa-solid fa-book"></i></div>
                <h3 class="feature-card__title">Expert courses</h3>
                <p class="feature-card__text">High-quality content from experienced mentors and practitioners.</p>
            </div>
        </div>
    </div>
</section>

<section class="section-sm stats-strip">
    <div class="container">
        <div class="dashboard-cards">
            <div class="stat-card dashboard-card reveal">
                <p class="stat-card__label">Students</p>
                <p class="stat-card__value counter" data-target="15000">0</p>
            </div>
            <div class="stat-card dashboard-card reveal stagger-1">
                <p class="stat-card__label">Courses</p>
                <p class="stat-card__value counter" data-target="250">0</p>
            </div>
            <div class="stat-card dashboard-card reveal stagger-2">
                <p class="stat-card__label">Instructors</p>
                <p class="stat-card__value counter" data-target="120">0</p>
            </div>
            <div class="stat-card dashboard-card reveal stagger-3">
                <p class="stat-card__label">Certificates</p>
                <p class="stat-card__value counter" data-target="5000">0</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
