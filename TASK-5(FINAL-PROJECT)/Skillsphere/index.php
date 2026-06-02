<?php
$pageTitle = 'Home';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';

$coursesQuery = dbQuery(
    "SELECT c.*, cat.category_name, u.full_name AS instructor_name
     FROM courses c
     LEFT JOIN categories cat ON c.category_id = cat.id
     LEFT JOIN users u ON c.instructor_id = u.id
     ORDER BY c.created_at DESC
     LIMIT 6"
);
?>

<section class="hero">
    <div class="hero__glow hero__glow--1"></div>
    <div class="hero__glow hero__glow--2"></div>
    <div class="hero__orb hero__orb--1"></div>
    <div class="hero__orb hero__orb--2"></div>

    <div class="container">
        <div class="hero__grid">
            <div class="hero__content reveal">
                <div class="hero__eyebrow">
                    <span class="badge badge-primary"><i class="fa-solid fa-bolt"></i> EdTech Platform</span>
                </div>
                <h1 class="hero__title">
                    Master in-demand skills with <span class="text-gradient">SkillSphere</span>
                </h1>
                <p class="hero__text">
                    Industry-aligned courses, project-based learning, and expert mentors — designed for professionals who want production-ready skills, not passive video watching.
                </p>
                <div class="hero__actions">
                    <a href="courses.php" class="btn btn-lg btn-primary">Explore courses</a>
                    <a href="register.php" class="btn btn-lg btn-outline">Start free</a>
                </div>
                <div class="hero__trust">
                    <span class="hero__trust-label">Trusted by teams at</span>
                    <div class="hero__trust-logos">
                        <span>Vercel</span>
                        <span>Stripe</span>
                        <span>Notion</span>
                        <span>Linear</span>
                    </div>
                </div>
            </div>

            <div class="hero__visual reveal stagger-2">
                <div class="hero__visual-card">
                    <img src="assets/images/illustrations/hero-learning.png" alt="SkillSphere learning dashboard preview" width="640" height="480">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-sm stats-strip">
    <div class="container">
        <div class="dashboard-cards grid-4">
            <div class="stat-card dashboard-card reveal">
                <p class="stat-card__label">Active learners</p>
                <p class="stat-card__value counter" data-target="15000">0</p>
            </div>
            <div class="stat-card dashboard-card reveal stagger-1">
                <p class="stat-card__label">Premium courses</p>
                <p class="stat-card__value counter" data-target="250">0</p>
            </div>
            <div class="stat-card dashboard-card reveal stagger-2">
                <p class="stat-card__label">Expert instructors</p>
                <p class="stat-card__value counter" data-target="120">0</p>
            </div>
            <div class="stat-card dashboard-card reveal stagger-3">
                <p class="stat-card__label">Certificates issued</p>
                <p class="stat-card__value counter" data-target="5000">0</p>
            </div>
        </div>
    </div>
</section>

<section class="section about-section">
    <div class="container">
        <header class="section-header reveal">
            <span class="badge badge-primary">Why SkillSphere</span>
            <h2>Learn smarter. Ship faster.</h2>
            <p>Everything you need for a modern learning journey — structured paths, real projects, and a platform that feels as polished as the products you build.</p>
        </header>

        <div class="cards-grid">
            <div class="card feature-card reveal">
                <div class="feature-card__icon"><i class="fa-solid fa-code"></i></div>
                <h3 class="feature-card__title">Project-based learning</h3>
                <p class="feature-card__text">Build portfolio-ready applications with guided milestones and code reviews.</p>
            </div>
            <div class="card feature-card reveal stagger-1">
                <div class="feature-card__icon"><i class="fa-solid fa-briefcase"></i></div>
                <h3 class="feature-card__title">Industry-ready skills</h3>
                <p class="feature-card__text">Curricula aligned with what hiring managers expect from senior engineers.</p>
            </div>
            <div class="card feature-card reveal stagger-2">
                <div class="feature-card__icon"><i class="fa-solid fa-graduation-cap"></i></div>
                <h3 class="feature-card__title">Expert-led courses</h3>
                <p class="feature-card__text">Learn from practitioners who ship at scale — not theory-only instructors.</p>
            </div>
            <div class="card feature-card reveal stagger-3">
                <div class="feature-card__icon"><i class="fa-solid fa-gauge-high"></i></div>
                <h3 class="feature-card__title">Modern experience</h3>
                <p class="feature-card__text">Fast UI, progress tracking, and dashboards designed for daily use.</p>
            </div>
        </div>
    </div>
</section>

<section class="section courses-section" id="courses">
    <div class="container">
        <header class="section-header reveal">
            <span class="badge badge-primary">Featured</span>
            <h2>Trending courses</h2>
            <p>Hand-picked programs learners are enrolling in this week.</p>
        </header>

        <div class="courses-carousel course-grid">
            <?php if ($coursesQuery && mysqli_num_rows($coursesQuery) > 0): ?>
                <?php while ($course = mysqli_fetch_assoc($coursesQuery)): ?>
                    <?php include 'includes/course-card.php'; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="card text-center" style="grid-column: 1 / -1;">
                    <h3>No courses yet</h3>
                    <p style="margin-top: 1rem;">Add courses from the admin panel to populate this section.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center" style="margin-top: 3rem;">
            <a href="courses.php" class="btn btn-outline btn-lg">View all courses</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <header class="section-header reveal">
            <span class="badge badge-primary">Testimonials</span>
            <h2>Loved by ambitious learners</h2>
        </header>
        <div class="grid grid-3">
            <div class="testimonial-card reveal">
                <p class="testimonial-card__quote">"The platform UI alone sets SkillSphere apart. Courses are structured like real product work — I landed a frontend role within three months."</p>
                <div class="testimonial-card__author">
                    <div class="testimonial-card__avatar">AK</div>
                    <div>
                        <p class="testimonial-card__name">Ananya Krishnan</p>
                        <p class="testimonial-card__role">Frontend Engineer</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal stagger-1">
                <p class="testimonial-card__quote">"Finally an EdTech product that doesn't feel like a template. Project reviews and clear learning paths made upskilling actually stick."</p>
                <div class="testimonial-card__author">
                    <div class="testimonial-card__avatar">MR</div>
                    <div>
                        <p class="testimonial-card__name">Madhava Rao</p>
                        <p class="testimonial-card__role">Full-stack Developer</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal stagger-2">
                <p class="testimonial-card__quote">"Our team uses SkillSphere for onboarding. The quality rivals platforms we pay enterprise rates for elsewhere."</p>
                <div class="testimonial-card__author">
                    <div class="testimonial-card__avatar">SC</div>
                    <div>
                        <p class="testimonial-card__name">Swapna Chowdary</p>
                        <p class="testimonial-card__role">Engineering Manager</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section about-section">
    <div class="container">
        <header class="section-header reveal">
            <span class="badge badge-primary">Instructors</span>
            <h2>Learn from builders</h2>
        </header>
        <div class="grid grid-4">
            <div class="instructor-card reveal">
                <div class="instructor-card__avatar">VS</div>
                <h3 class="instructor-card__name">Venkata Srinivas</h3>
                <p class="instructor-card__role">Web Architecture</p>
                <p class="instructor-card__bio">Ex-staff engineer. 12+ years shipping SaaS at scale.</p>
            </div>
            <div class="instructor-card reveal stagger-1">
                <div class="instructor-card__avatar">SD</div>
                <h3 class="instructor-card__name">Dr. Sravani Devi</h3>
                <p class="instructor-card__role">AI & ML</p>
                <p class="instructor-card__bio">Research to production pipelines for ML systems.</p>
            </div>
            <div class="instructor-card reveal stagger-2">
                <div class="instructor-card__avatar">LP</div>
                <h3 class="instructor-card__name">Lakshmi Prasanna</h3>
                <p class="instructor-card__role">Data Science</p>
                <p class="instructor-card__bio">Analytics lead focused on decision-ready insights.</p>
            </div>
            <div class="instructor-card reveal stagger-3">
                <div class="instructor-card__avatar">CP</div>
                <h3 class="instructor-card__name">Chaitanya Prasad</h3>
                <p class="instructor-card__role">Security</p>
                <p class="instructor-card__bio">AppSec specialist for modern cloud-native stacks.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" id="faq">
    <div class="container container-narrow">
        <header class="section-header reveal">
            <span class="badge badge-primary">FAQ</span>
            <h2>Common questions</h2>
        </header>
        <div class="faq-list" style="display: flex; flex-direction: column; gap: 1rem;">
            <div class="faq-item reveal">
                <button type="button" class="faq-question" aria-expanded="false">
                    How do I get started?
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="faq-answer"><div class="faq-answer-inner">Create a free account, browse courses, and enroll instantly. Your dashboard tracks progress across all programs.</div></div>
            </div>
            <div class="faq-item reveal">
                <button type="button" class="faq-question" aria-expanded="false">
                    Are certificates included?
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="faq-answer"><div class="faq-answer-inner">Yes — complete course requirements to earn verifiable certificates you can share on LinkedIn and your portfolio.</div></div>
            </div>
            <div class="faq-item reveal">
                <button type="button" class="faq-question" aria-expanded="false">
                    Can teams use SkillSphere?
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="faq-answer"><div class="faq-answer-inner">Absolutely. Contact us for team plans with centralized billing, progress reporting, and custom learning paths.</div></div>
            </div>
        </div>
    </div>
</section>

<section class="section cta-section">
    <div class="container">
        <div class="cta-banner reveal">
            <h2>Ready to level up your career?</h2>
            <p>Join thousands of learners building skills that matter — on a platform designed for professionals.</p>
            <a href="register.php" class="btn btn-lg" style="background: #fff; color: #059669;">Get started free</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
