<?php
$pageTitle = 'Contact';
include 'includes/header.php';
include 'includes/navbar.php';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';

$message = '';

if (isset($_POST['send_message'])) {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $message = 'CSRF token mismatch. Please try again.';
    } else {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $subject = sanitize($_POST['subject']);
        $userMessage = sanitize($_POST['message']);

        $insertQuery = dbQuery(
            'INSERT INTO contact_messages(name, email, subject, message) VALUES(?, ?, ?, ?)',
            [$name, $email, $subject, $userMessage]
        );

        $message = $insertQuery ? 'Message Sent Successfully' : 'Failed To Send Message';
    }
}
?>

<section class="page-header">
    <div class="container">
        <span class="badge badge-primary">Contact</span>
        <h1 class="fade">Get in touch</h1>
        <p class="fade">Questions, feedback, or partnership ideas — we'd love to hear from you.</p>
    </div>
</section>

<section class="section contact-section">
    <div class="container">
        <div class="about-grid" style="align-items: flex-start;">
            <div class="reveal">
                <span class="badge badge-primary">Reach us</span>
                <h2 style="margin: 1.5rem 0 1rem;">Let's connect</h2>
                <p style="margin-bottom: 2rem;">We help students, instructors, and teams build futures through technology and online education.</p>

                <div class="flex flex-col gap-4">
                    <div class="card feature-card" style="padding: 1.5rem;">
                        <h3 class="feature-card__title"><i class="fa-regular fa-envelope" style="color: var(--brand-500); margin-right: 0.5rem;"></i> Email</h3>
                        <p class="feature-card__text">support@skillsphere.com</p>
                    </div>
                    <div class="card feature-card" style="padding: 1.5rem;">
                        <h3 class="feature-card__title"><i class="fa-solid fa-location-dot" style="color: var(--brand-500); margin-right: 0.5rem;"></i> Location</h3>
                        <p class="feature-card__text">Andhra Pradesh, India</p>
                    </div>
                    <div class="card feature-card" style="padding: 1.5rem;">
                        <h3 class="feature-card__title"><i class="fa-regular fa-clock" style="color: var(--brand-500); margin-right: 0.5rem;"></i> Hours</h3>
                        <p class="feature-card__text">Monday – Saturday · 9:00 AM – 7:00 PM</p>
                    </div>
                </div>
            </div>

            <div class="contact-form reveal stagger-2">
                <?php if ($message !== ''): ?>
                    <div class="alert <?php echo $message === 'Message Sent Successfully' ? 'alert-success' : 'alert-danger'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="contactForm" onsubmit="return validateForm('contactForm');">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="form-group">
                        <label for="contact-name">Full name</label>
                        <input type="text" id="contact-name" name="name" class="form-control" placeholder="Your name" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-email">Email</label>
                        <input type="email" id="contact-email" name="email" class="form-control" placeholder="you@email.com" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-subject">Subject</label>
                        <input type="text" id="contact-subject" name="subject" class="form-control" placeholder="How can we help?" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-message">Message</label>
                        <textarea id="contact-message" name="message" class="form-control" rows="5" placeholder="Your message" required></textarea>
                    </div>
                    <button type="submit" name="send_message" class="btn btn-primary btn-block">Send message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

