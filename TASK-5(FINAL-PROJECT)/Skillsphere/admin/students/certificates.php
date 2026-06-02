<?php
require_once '../../auth.php';
requireAdmin();

$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$student = null;
$certificates = [];

if ($studentId > 0) {
    $student = fetchSingleSecure("SELECT * FROM users WHERE id = ? AND role = 'student'", [$studentId]);
    if ($student) {
        $certificates = fetchAllSecure("
            SELECT cert.*, c.title AS course_title
            FROM certificates cert
            JOIN courses c ON cert.course_id = c.id
            WHERE cert.user_id = ?
            ORDER BY cert.issued_at DESC
        ", [$studentId]);
    }
} else {
    // Fetch all certificates
    $certificates = fetchAllSecure("
        SELECT cert.*, c.title AS course_title, u.full_name AS student_name
        FROM certificates cert
        JOIN courses c ON cert.course_id = c.id
        JOIN users u ON cert.user_id = u.id
        ORDER BY cert.issued_at DESC
    ");
}

$adminTitle = 'Student Certificates';
$adminPage = 'students';
$adminHeading = 'Earned certificates';
$adminSubheading = 'View verifiable credentials';
$adminIllustration = '../assets/images/admin-courses.svg';
$adminHeroTitle = 'Credentials';
$adminHeroText = 'Track credentials generated upon 100% completion of curricula.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<div class="admin-panel reveal">
    <div class="admin-panel__head">
        <h3><?php echo $student ? 'Certificates for ' . htmlspecialchars($student['full_name']) : 'All issued certificates'; ?></h3>
    </div>
    <div class="admin-panel__body">
        <div class="admin-table-wrap">
            <?php if (!empty($certificates)): ?>
                <table>
                    <thead>
                        <tr>
                            <?php if (!$student): ?>
                                <th>Student Name</th>
                            <?php endif; ?>
                            <th>Course Title</th>
                            <th>Verification Code</th>
                            <th>Issued On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certificates as $cert): ?>
                            <tr>
                                <?php if (!$student): ?>
                                    <td><strong><?php echo htmlspecialchars($cert['student_name']); ?></strong></td>
                                <?php endif; ?>
                                <td><?php echo htmlspecialchars($cert['course_title']); ?></td>
                                <td><code><?php echo htmlspecialchars($cert['certificate_code']); ?></code></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($cert['issued_at'])); ?></td>
                                <td>
                                    <a href="../../certificate.php?code=<?php echo htmlspecialchars($cert['certificate_code']); ?>" target="_blank" class="edit-btn">Verify view</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No certificates have been issued yet.</div>
            <?php endif; ?>
        </div>
        <div style="margin-top:1.5rem;">
            <a href="manage-students.php" class="admin-btn admin-btn--outline">Back to directory</a>
        </div>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
