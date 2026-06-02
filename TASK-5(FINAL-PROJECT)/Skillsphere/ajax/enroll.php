<?php

// =========================================
// SKILLSPHERE AJAX ENROLLMENT
// ajax/enroll.php
// =========================================

require_once '../db.php';

// =========================================
// RESPONSE FORMAT
// =========================================

header('Content-Type: application/json');

// =========================================
// LOGIN CHECK
// =========================================

if(!isset($_SESSION['user_id'])){

    echo json_encode([

        'status' => 'error',
        'message' => 'Please login first'

    ]);

    exit();

}

// =========================================
// VALIDATE COURSE ID
// =========================================

if(!isset($_POST['course_id'])){

    echo json_encode([

        'status' => 'error',
        'message' => 'Invalid course request'

    ]);

    exit();

}

$user_id =
intval($_SESSION['user_id']);

$course_id =
intval($_POST['course_id']);

// =========================================
// CHECK COURSE EXISTS
// =========================================

$courseCheck = fetchSingleSecure(
    "SELECT * FROM courses WHERE id = ?",
    [$course_id]
);

if(!$courseCheck){

    echo json_encode([

        'status' => 'error',
        'message' => 'Course not found'

    ]);

    exit();

}

// =========================================
// CHECK ALREADY ENROLLED
// =========================================

$alreadyEnrolled = fetchSingleSecure(
    "SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?",
    [$user_id, $course_id]
);

if($alreadyEnrolled){

    echo json_encode([

        'status' => 'warning',
        'message' => 'Already enrolled in this course'

    ]);

    exit();

}

// =========================================
// INSERT ENROLLMENT
// =========================================

$insertQuery = dbQuery(
    "INSERT INTO enrollments (user_id, course_id, payment_status, enrolled_at) VALUES (?, ?, 'paid', NOW())",
    [$user_id, $course_id]
);

// =========================================
// UPDATE TOTAL STUDENTS
// =========================================

dbQuery(
    "UPDATE courses SET total_students = total_students + 1 WHERE id = ?",
    [$course_id]
);

// =========================================
// ADD NOTIFICATION
// =========================================

dbQuery(
    "INSERT INTO notifications (user_id, title, message) VALUES (?, 'Enrollment Successful', 'You successfully enrolled in a course.')",
    [$user_id]
);

// =========================================
// SUCCESS RESPONSE
// =========================================

if($insertQuery){

    echo json_encode([

        'status' => 'success',

        'message' =>
        'Enrollment Successful'

    ]);

}else{

    echo json_encode([

        'status' => 'error',

        'message' =>
        'Enrollment Failed'

    ]);

}

?>