<?php
/**
 * Reusable course card — expects $course array with id, title, description, price,
 * category_name, instructor_name, level, duration, thumbnail
 */
if (!isset($course) || !is_array($course)) {
    return;
}

$thumbPath = htmlspecialchars(courseThumbnailUrl($course['thumbnail'] ?? ($course['slug'] ?? '')));
$categorySlug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($course['category_name'] ?? 'course'));
$categorySlug = trim($categorySlug, '-') ?: 'course';
$level = htmlspecialchars($course['level'] ?? 'All levels');
$duration = htmlspecialchars($course['duration'] ?? 'Self-paced');
$instructor = htmlspecialchars($course['instructor_name'] ?? 'Expert Instructor');
$rating = isset($course['rating']) ? (float) $course['rating'] : 4.8;
$students = isset($course['students']) ? (int) $course['students'] : rand(1200, 8900);
$stars = str_repeat('<i class="fa-solid fa-star"></i>', 5);
?>
<article class="course-card reveal">
    <div class="course-card__media">
        <?php if ($thumbPath): ?>
            <img
                src="<?php echo $thumbPath; ?>"
                alt="<?php echo htmlspecialchars($course['title']); ?>"
                loading="lazy"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
            >
        <?php endif; ?>
        <div class="course-card__thumb course-thumb-art course-thumb-art--<?php echo htmlspecialchars($categorySlug); ?>" <?php echo $thumbPath ? 'style="display:none"' : ''; ?> aria-hidden="true"></div>
        <div class="course-card__overlay"></div>
        <div class="course-card__badges">
            <span class="course-card__level"><?php echo $level; ?></span>
        </div>
    </div>

    <div class="course-card__body">
        <span class="course-card__category"><?php echo htmlspecialchars($course['category_name'] ?? 'Course'); ?></span>
        <h3 class="course-card__title">
            <a href="course-details.php?id=<?php echo (int) $course['id']; ?>">
                <?php echo htmlspecialchars($course['title']); ?>
            </a>
        </h3>
        <p class="course-card__desc">
            <?php echo htmlspecialchars(limitText($course['description'] ?? '', 110)); ?>
        </p>

        <div class="course-card__meta">
            <span><i class="fa-regular fa-user"></i> <?php echo $instructor; ?></span>
            <span><i class="fa-regular fa-clock"></i> <?php echo $duration; ?></span>
        </div>

        <div class="course-card__rating">
            <span class="course-card__stars" aria-label="Rating <?php echo $rating; ?> out of 5"><?php echo $stars; ?></span>
            <span><?php echo number_format($rating, 1); ?></span>
            <span class="course-card__meta">· <?php echo number_format($students); ?> learners</span>
        </div>

        <div class="course-card__footer">
            <div class="course-card__price">
                ₹<?php echo htmlspecialchars($course['price'] ?? '0'); ?>
            </div>
            <a href="course-details.php?id=<?php echo (int) $course['id']; ?>" class="btn btn-sm">View course</a>
        </div>
    </div>
</article>
