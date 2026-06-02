<?php
require_once '../auth.php';
require_once '../middleware.php';
requireRole('student');

// Rank students by completed lessons
$rankings = fetchAllSecure("
    SELECT u.id, u.full_name, u.profile_image, COUNT(lp.id) AS score
    FROM users u
    LEFT JOIN lesson_progress lp ON u.id = lp.user_id AND lp.completed = 1
    WHERE u.role = 'student'
    GROUP BY u.id
    ORDER BY score DESC
    LIMIT 10
");

$pageTitle = 'Platform Leaderboard';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <span class="badge badge-primary">Student Portal</span>
            <h1 class="text-gradient">SkillSphere Leaderboard</h1>
        </div>
    </div>

    <!-- Navigation links for portal -->
    <div style="display:flex; gap:1rem; border-bottom:1px solid var(--border-default); padding-bottom:1rem; margin-bottom:2rem;">
        <a href="dashboard.php" class="btn btn-sm btn-outline">Active Courses</a>
        <a href="progress.php" class="btn btn-sm btn-outline">Study Progress</a>
        <a href="certificates.php" class="btn btn-sm btn-outline">Certificates</a>
        <a href="achievements.php" class="btn btn-sm btn-outline">Achievements</a>
        <a href="leaderboard.php" class="btn btn-sm btn-primary">Leaderboard</a>
        <a href="wishlist.php" class="btn btn-sm btn-outline">Wishlist</a>
    </div>

    <div class="card reveal" style="padding: 2rem; max-width:800px; margin: 0 auto;">
        <div class="admin-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Student</th>
                        <th>Lectures Completed</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    foreach ($rankings as $r): 
                        $isCurrent = $r['id'] === (int)$_SESSION['user_id'];
                        ?>
                        <tr style="<?php echo $isCurrent ? 'background:var(--brand-50);' : ''; ?>">
                            <td>
                                <?php if ($rank === 1): ?>
                                    🥇 <strong style="color:#d97706;">1st</strong>
                                <?php elseif ($rank === 2): ?>
                                    🥈 <strong style="color:#64748b;">2nd</strong>
                                <?php elseif ($rank === 3): ?>
                                    🥉 <strong style="color:#b45309;">3rd</strong>
                                <?php else: ?>
                                    #<?php echo $rank; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="admin-profile-cell">
                                    <img src="../uploads/profiles/<?php echo htmlspecialchars($r['profile_image'] ?? 'default.png'); ?>" alt="" onerror="this.src='../uploads/profiles/default.png'" style="width:36px; height:36px; border-radius:50%;">
                                    <strong><?php echo htmlspecialchars($r['full_name']); ?> <?php echo $isCurrent ? '(You)' : ''; ?></strong>
                                </div>
                            </td>
                            <td><strong><?php echo (int)$r['score']; ?></strong> lectures</td>
                            <td><strong><?php echo (int)$r['score'] * 10; ?></strong> pts</td>
                        </tr>
                        <?php 
                        $rank++;
                    endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
