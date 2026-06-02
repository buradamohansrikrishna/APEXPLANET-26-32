<?php
header('Content-Type: application/json');
require_once '../../db.php';
require_once '../../functions.php';

$message = isset($_POST['message']) ? sanitize($_POST['message']) : '';

if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Message is empty']);
    exit();
}

$response = "I can help you find technical courses on SkillSphere! We offer training in Web Development, Data Science, Cyber Security, and UI UX Design. Try asking about 'Go', 'React', 'Database', or 'Docker'.";

$msg = strtolower($message);
if (strpos($msg, 'go') !== false || strpos($msg, 'grpc') !== false) {
    $response = "We offer a premium 'Advanced Backend with Go & gRPC' course taught by Lakshmi Prasanna. It covers protocol buffers, interceptors, and high performance architecture.";
} elseif (strpos($msg, 'react') !== false || strpos($msg, 'next') !== false) {
    $response = "Check out our 'React 19 & Next.js 15 Complete Guide'. Learn React Server Components, App Router, and scaling rendering modes.";
} elseif (strpos($msg, 'database') !== false || strpos($msg, 'sql') !== false) {
    $response = "Learn indexes, locks, and query optimization in 'High Performance Database Engineering' course.";
} elseif (strpos($msg, 'docker') !== false || strpos($msg, 'kubernetes') !== false || strpos($msg, 'aws') !== false) {
    $response = "Learn scaling pipelines in our 'Docker, Kubernetes & AWS DevOps' masterclass.";
} elseif (strpos($msg, 'pricing') !== false || strpos($msg, 'cost') !== false) {
    $response = "We support individual course purchases, or you can join our Monthly Membership for ₹999/month to unlock all catalog courses.";
}

echo json_encode(['success' => true, 'response' => $response]);
?>
