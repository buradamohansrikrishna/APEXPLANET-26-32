<?php

// =========================================
// SKILLSPHERE AJAX SEARCH
// ajax/search.php
// =========================================

require_once '../db.php';

// =========================================
// JSON RESPONSE
// =========================================

header('Content-Type: application/json');

// =========================================
// VALIDATE SEARCH INPUT
// =========================================

if(

    !isset($_GET['query']) ||
    empty(trim($_GET['query']))

){

    echo json_encode([

        'status' => 'error',

        'message' =>
        'Search query is empty'

    ]);

    exit();

}

// =========================================
// SANITIZE SEARCH
// =========================================

$searchVal = "%" . trim($_GET['query']) . "%";

// =========================================
// SEARCH QUERY
// =========================================

$coursesData = fetchAllSecure(
    "SELECT c.*, cat.category_name, u.full_name AS instructor_name
     FROM courses c
     LEFT JOIN categories cat ON c.category_id = cat.id
     LEFT JOIN users u ON c.instructor_id = u.id
     WHERE c.title LIKE ?
     OR cat.category_name LIKE ?
     OR u.full_name LIKE ?
     OR c.level LIKE ?
     ORDER BY c.created_at DESC
     LIMIT 20",
    [$searchVal, $searchVal, $searchVal, $searchVal]
);

// =========================================
// STORE RESULTS
// =========================================

$courses = [];

foreach ($coursesData as $row) {

    $courses[] = [

        'id' =>

        $row['id'],

        'title' =>

        $row['title'],

        'slug' =>

        $row['slug'],

        'thumbnail' =>

        $row['thumbnail'],

        'category' =>

        $row['category_name'],

        'instructor' =>

        $row['instructor_name'],

        'level' =>

        $row['level'],

        'price' =>

        $row['price'],

        'duration' =>

        $row['duration'],

        'url' =>

        'course-details.php?id=' .
        $row['id']

    ];

}

// =========================================
// RESPONSE
// =========================================

if(count($courses) > 0){

    echo json_encode([

        'status' => 'success',

        'total_results' =>
        count($courses),

        'courses' =>
        $courses

    ]);

}else{

    echo json_encode([

        'status' => 'empty',

        'message' =>
        'No courses found'

    ]);

}

?>