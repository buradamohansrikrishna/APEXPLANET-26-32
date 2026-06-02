<?php
// ========================================================
// SKILLSPHERE THUMBNAIL UPDATE SCRIPT
// database/update_thumbnails.php
// ========================================================

require_once __DIR__ . '/../db.php';

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

echo "Connecting and updating course thumbnails in database...\n";

$slugs = [
    'react-19-next-js-15-complete-guide',
    'advanced-system-design-microservices',
    'ai-deep-learning-bootcamp',
    'ethical-hacking-network-pen-testing',
    'docker-kubernetes-aws-devops',
    'ui-ux-design-systems-premium-saas',
    'high-performance-database-engineering',
    'python-data-science-analytics',
    'advanced-backend-go-grpc',
    'modern-javascript-es6-complete-mastery',
];

foreach ($slugs as $slug) {
    $thumbnail = resolveCourseThumbnail($slug);
    $stmt = mysqli_prepare($conn, 'UPDATE courses SET thumbnail = ? WHERE slug = ?');
    mysqli_stmt_bind_param($stmt, 'ss', $thumbnail, $slug);
    if (mysqli_stmt_execute($stmt)) {
        echo "Updated $slug -> $thumbnail\n";
    } else {
        echo "Failed: $slug\n";
    }
}

$extra = syncCourseThumbnailsInDb();
echo "Thumbnail sync complete ($extra additional row(s) normalized).\n";
?>
