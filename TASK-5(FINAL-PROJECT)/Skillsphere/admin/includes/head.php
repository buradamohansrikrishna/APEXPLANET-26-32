<?php
$adminTitle = $adminTitle ?? 'Admin';
$adminPage = $adminPage ?? '';

$pathPrefix = '';
$currSelf = $_SERVER['PHP_SELF'];
if (strpos($currSelf, '/users/') !== false || 
    strpos($currSelf, '/courses/') !== false || 
    strpos($currSelf, '/instructors/') !== false || 
    strpos($currSelf, '/students/') !== false || 
    strpos($currSelf, '/content/') !== false) {
    $pathPrefix = '../';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?php echo htmlspecialchars($adminTitle); ?> | SkillSphere Admin</title>
<script src="<?php echo $pathPrefix; ?>../assets/js/theme.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
<link rel="stylesheet" href="<?php echo $pathPrefix; ?>admin.css">
</head>
<body class="admin-body">
<div class="admin-shell">

