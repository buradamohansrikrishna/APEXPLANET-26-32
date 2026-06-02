<!-- =========================================
     SKILLSPHERE NEWSLETTER NEWS COMPONENT
     includes/newsletter.php
========================================= -->
<section class="newsletter-section reveal">
    <div class="newsletter-box">
        <div class="newsletter-content">
            <h3>Keep up with tech news</h3>
            <p>Subscribe to our newsletter to receive curated dev tutorials, industry trends, and product offers directly in your inbox.</p>
        </div>
        <form class="newsletter-form" id="newsletterForm" onsubmit="event.preventDefault(); alert('Subscribed successfully!');">
            <div class="form-group">
                <input type="email" class="form-control" placeholder="Enter your email address" required autocomplete="email">
                <button type="submit" class="btn btn-primary">Subscribe</button>
            </div>
        </form>
    </div>
</section>
