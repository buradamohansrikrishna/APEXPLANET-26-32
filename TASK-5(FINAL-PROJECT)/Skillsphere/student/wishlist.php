<?php
require_once '../auth.php';
require_once '../middleware.php';
requireRole('student');

$userId = (int)$_SESSION['user_id'];

// Remove from wishlist helper
if (isset($_GET['remove'])) {
    $cId = (int)$_GET['remove'];
    dbQuery("DELETE FROM wishlist WHERE user_id = ? AND course_id = ?", [$userId, $cId]);
    $_SESSION['success'] = "Course removed from wishlist";
    header("Location: wishlist.php");
    exit();
}

$wishlistCourses = fetchAllSecure("
    SELECT w.course_id, c.title, c.price, c.thumbnail, cat.category_name
    FROM wishlist w
    JOIN courses c ON w.course_id = c.id
    LEFT JOIN categories cat ON c.category_id = cat.id
    WHERE w.user_id = ?
", [$userId]);

$pageTitle = 'My Wishlist';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <span class="badge badge-primary">Student Portal</span>
            <h1 class="text-gradient">Your Wishlist</h1>
        </div>
    </div>

    <!-- Navigation links for portal -->
    <div style="display:flex; gap:1rem; border-bottom:1px solid var(--border-default); padding-bottom:1rem; margin-bottom:2rem;">
        <a href="dashboard.php" class="btn btn-sm btn-outline">Active Courses</a>
        <a href="progress.php" class="btn btn-sm btn-outline">Study Progress</a>
        <a href="certificates.php" class="btn btn-sm btn-outline">Certificates</a>
        <a href="achievements.php" class="btn btn-sm btn-outline">Achievements</a>
        <a href="leaderboard.php" class="btn btn-sm btn-outline">Leaderboard</a>
        <a href="wishlist.php" class="btn btn-sm btn-primary">Wishlist</a>
    </div>

    <div class="course-grid">
        <?php if (!empty($wishlistCourses)): ?>
            <?php foreach ($wishlistCourses as $wc): ?>
                <article class="course-card reveal">
                    <div class="course-card__media">
                        <?php 
                        $thumbPath = !empty($wc['thumbnail']) ? '../uploads/thumbnails/' . htmlspecialchars($wc['thumbnail']) : '';
                        if ($thumbPath): ?>
                            <img src="<?php echo $thumbPath; ?>" alt="" onerror="this.style.display='none';">
                        <?php endif; ?>
                        <div class="course-card__thumb course-thumb-art"></div>
                    </div>
                    <div class="course-card__body">
                        <span class="course-card__category"><?php echo htmlspecialchars($wc['category_name'] ?? 'Web Development'); ?></span>
                        <h3 class="course-card__title">
                            <a href="../course-details.php?id=<?php echo $wc['course_id']; ?>">
                                <?php echo htmlspecialchars($wc['title']); ?>
                            </a>
                        </h3>
                        <div class="course-card__footer">
                            <div class="course-card__price">₹<?php echo number_format($wc['price'], 0); ?></div>
                            <div style="display:flex; gap:0.5rem;">
                                <a href="../course-details.php?id=<?php echo $wc['course_id']; ?>" class="btn btn-sm btn-primary">Buy Now</a>
                                <a href="wishlist.php?remove=<?php echo $wc['course_id']; ?>" class="btn btn-sm btn-outline" style="color:var(--danger); border-color:var(--danger);"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card text-center" style="grid-column: 1 / -1; padding: 3rem;">
                <h3>Your wishlist is empty</h3>
                <p style="margin-top: 1rem; margin-bottom: 1.5rem;">Save courses to buy them later.</p>
                <a href="../courses.php" class="btn btn-primary">Explore Courses</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
