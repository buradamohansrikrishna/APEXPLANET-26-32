<?php
/**
 * Course thumbnail cell — expects $course array and $thumbPrefix (e.g. '../../')
 */
if (!isset($course) || !is_array($course)) {
    return;
}

$thumbPrefix = $thumbPrefix ?? '../';
$thumbFile = resolveCourseThumbnail($course['thumbnail'] ?? ($course['slug'] ?? ''));
$thumbUrl = htmlspecialchars($thumbPrefix . THUMBNAIL_UPLOAD . $thumbFile);
$categorySlug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($course['category_name'] ?? 'course'));
$categorySlug = trim($categorySlug, '-') ?: 'course';
$title = htmlspecialchars($course['title'] ?? 'Course');
?>
<div class="admin-thumb">
    <img src="<?php echo $thumbUrl; ?>" alt="<?php echo $title; ?>" loading="lazy" width="88" height="56" onerror="this.hidden=true; this.nextElementSibling.hidden=false;">
    <div class="admin-thumb__art admin-thumb__art--<?php echo htmlspecialchars($categorySlug); ?>" hidden></div>
</div>
