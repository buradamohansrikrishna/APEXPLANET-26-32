<!-- =========================================
     SKILLSPHERE PASSWORD RESET MAIL TEMPLATE
     mail/password-reset.php
========================================= -->
<div style="font-family: sans-serif; padding: 2rem; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 12px;">
    <h2 style="color: #ef4444;">Password Reset Request</h2>
    <p>Hello,</p>
    <p>We received a request to reset the password for your SkillSphere account.</p>
    <p>Please click the button below to set a new password. This link will expire in 1 hour.</p>
    <a href="{{reset_url}}" style="display: inline-block; padding: 0.75rem 1.5rem; background: #ef4444; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 1rem 0;">Reset Password</a>
    <p>If you did not request a password reset, you can safely ignore this email.</p>
    <p>Best regards,<br>The SkillSphere Team</p>
</div>
