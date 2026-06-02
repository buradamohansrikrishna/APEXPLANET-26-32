<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$rootPrefix = '';
$currSelf = $_SERVER['PHP_SELF'];
if (strpos($currSelf, '/student/') !== false || strpos($currSelf, '/instructor/') !== false) {
    $rootPrefix = '../';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="SkillSphere — Premium online learning platform for modern tech skills, projects, and career growth.">
<title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | SkillSphere' : 'SkillSphere — Learn Skills That Matter'; ?></title>

<script src="<?php echo $rootPrefix; ?>assets/js/theme.js"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
<link rel="stylesheet" href="<?php echo $rootPrefix; ?>assets/css/main.css">
</head>
<body class="page-main">
