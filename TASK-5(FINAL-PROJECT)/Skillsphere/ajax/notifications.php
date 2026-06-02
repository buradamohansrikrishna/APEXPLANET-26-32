<?php

// =========================================
// SKILLSPHERE AJAX NOTIFICATIONS
// ajax/notifications.php
// =========================================

require_once '../db.php';

// =========================================
// JSON RESPONSE
// =========================================

header('Content-Type: application/json');

// =========================================
// LOGIN CHECK
// =========================================

if(!isset($_SESSION['user_id'])){

    echo json_encode([

        'status' => 'error',
        'message' => 'Unauthorized Access'

    ]);

    exit();

}

$user_id =
intval($_SESSION['user_id']);

// =========================================
// FETCH NOTIFICATIONS
// =========================================

$notificationQuery = dbQuery(
    "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10",
    [$user_id]
);

// =========================================
// STORE DATA
// =========================================

$notifications = [];

if ($notificationQuery) {
    while(
        $row =
        mysqli_fetch_assoc(
            $notificationQuery
        )
    ){

        $notifications[] = [

            'id' =>
            $row['id'],

            'title' =>
            $row['title'],

            'message' =>
            $row['message'],

            'is_read' =>
            $row['is_read'],

            'created_at' =>
            date(

                'd M Y h:i A',

                strtotime(
                    $row['created_at']
                )

            )

        ];

    }
}

// =========================================
// UNREAD COUNT
// =========================================

$unreadData = fetchSingleSecure(
    "SELECT COUNT(*) AS unread FROM notifications WHERE user_id = ? AND is_read = 0",
    [$user_id]
);

$unreadCount = $unreadData['unread'] ?? 0;

// =========================================
// MARK AS READ
// =========================================

if(isset($_GET['mark_read'])){

    dbQuery(
        "UPDATE notifications SET is_read = 1 WHERE user_id = ?",
        [$user_id]
    );

}

// =========================================
// RESPONSE
// =========================================

echo json_encode([

    'status' => 'success',

    'unread_count' =>
    $unreadCount,

    'notifications' =>
    $notifications

]);

?>