<?php
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';

$code = isset($_GET['code']) ? sanitize($_GET['code']) : '';
$certificate = null;

if (!empty($code)) {
    $certificate = fetchSingleSecure("
        SELECT cert.*, u.full_name AS student_name, c.title AS course_title, c.duration, inst.full_name AS instructor_name
        FROM certificates cert
        JOIN users u ON cert.user_id = u.id
        JOIN courses c ON cert.course_id = c.id
        LEFT JOIN users inst ON c.instructor_id = inst.id
        WHERE cert.certificate_code = ?
        LIMIT 1
    ", [$code]);
}

$pageTitle = 'Verify Certificate';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container" style="margin-top:4rem; margin-bottom:6rem; max-width:800px; text-align:center;">
    <?php if ($certificate): ?>
        <span class="badge badge-success" style="font-size:1rem; padding:0.5rem 1.5rem; margin-bottom:2rem;">
            <i class="fa-solid fa-circle-check"></i> Verifiable SkillSphere Credential
        </span>

        <!-- Certificate Card layout -->
        <div class="card reveal" style="padding: 4rem; border: 8px double var(--brand-500); background: var(--bg-surface); position:relative; box-shadow: var(--shadow-xl);">
            <div style="font-size:1.5rem; letter-spacing:4px; text-transform:uppercase; color:var(--text-tertiary); margin-bottom:2rem;">Certificate of Completion</div>
            
            <p style="font-size:1.25rem; color:var(--text-secondary); margin-bottom:1.5rem;">This is to certify that</p>
            <h2 class="text-gradient" style="font-size:2.5rem; font-family:var(--font-display); margin-bottom:1.5rem;"><?php echo htmlspecialchars($certificate['student_name']); ?></h2>
            
            <p style="font-size:1.25rem; color:var(--text-secondary); margin-bottom:2rem; max-width:600px; margin-left:auto; margin-right:auto;">
                has successfully completed all module criteria and final assignments for the premium course
            </p>
            
            <h3 style="font-size:1.75rem; color:var(--text-primary); margin-bottom:2.5rem; font-family:var(--font-display);"><?php echo htmlspecialchars($certificate['course_title']); ?></h3>
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4rem; border-top:1px solid var(--border-default); padding-top:2rem;">
                <div style="text-align:left;">
                    <span style="font-size:0.875rem; color:var(--text-tertiary);">Instructor</span><br>
                    <strong><?php echo htmlspecialchars($certificate['instructor_name'] ?? 'SkillSphere Mentor'); ?></strong>
                </div>
                <div>
                    <span style="font-size:0.875rem; color:var(--text-tertiary);">Issued On</span><br>
                    <strong><?php echo date('d M Y', strtotime($certificate['issued_at'])); ?></strong>
                </div>
                <div style="text-align:right;">
                    <span style="font-size:0.875rem; color:var(--text-tertiary);">Credential Code</span><br>
                    <code><?php echo htmlspecialchars($certificate['certificate_code']); ?></code>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card reveal" style="padding:4rem;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size:3rem; color:var(--danger); margin-bottom:1.5rem;"></i>
            <h2>Credential Verification Failed</h2>
            <p style="color:var(--text-secondary); margin-top:1rem; margin-bottom:2rem;">
                The certificate verification code you provided does not exist or has been revoked.
            </p>
            <a href="index.php" class="btn btn-primary">Go to Home</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
