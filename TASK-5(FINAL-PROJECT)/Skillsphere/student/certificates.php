<?php
require_once '../auth.php';
require_once '../middleware.php';
requireRole('student');

$userId = (int)$_SESSION['user_id'];
$certificates = fetchAllSecure("
    SELECT cert.*, c.title AS course_title
    FROM certificates cert
    JOIN courses c ON cert.course_id = c.id
    WHERE cert.user_id = ?
    ORDER BY cert.issued_at DESC
", [$userId]);

$pageTitle = 'My Certificates';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <span class="badge badge-primary">Student Portal</span>
            <h1 class="text-gradient">Your Earned Credentials</h1>
        </div>
    </div>

    <!-- Navigation links for portal -->
    <div style="display:flex; gap:1rem; border-bottom:1px solid var(--border-default); padding-bottom:1rem; margin-bottom:2rem;">
        <a href="dashboard.php" class="btn btn-sm btn-outline">Active Courses</a>
        <a href="progress.php" class="btn btn-sm btn-outline">Study Progress</a>
        <a href="certificates.php" class="btn btn-sm btn-primary">Certificates</a>
        <a href="achievements.php" class="btn btn-sm btn-outline">Achievements</a>
        <a href="leaderboard.php" class="btn btn-sm btn-outline">Leaderboard</a>
        <a href="wishlist.php" class="btn btn-sm btn-outline">Wishlist</a>
    </div>

    <div class="card reveal" style="padding: 2rem;">
        <?php if (!empty($certificates)): ?>
            <div class="admin-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Course Title</th>
                            <th>Verification Code</th>
                            <th>Issued At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certificates as $cert): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($cert['course_title']); ?></strong></td>
                                <td><code><?php echo htmlspecialchars($cert['certificate_code']); ?></code></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($cert['issued_at'])); ?></td>
                                <td>
                                    <a href="../certificate.php?code=<?php echo htmlspecialchars($cert['certificate_code']); ?>" target="_blank" class="btn btn-sm btn-primary">View / Share</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align:center; padding: 2rem;">
                <i class="fa-solid fa-graduation-cap" style="font-size:3rem; color:var(--text-muted); margin-bottom:1rem;"></i>
                <p>No certificates earned yet. Complete all lessons of any course to earn your verified credential!</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
