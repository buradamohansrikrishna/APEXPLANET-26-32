<?php
// =========================================
// SKILLSPHERE AI LEARNING TRACKER
// ai/learning-tracker.php
// =========================================

require_once __DIR__ . '/../db.php';

class LearningTracker {
    public static function logProgress($userId, $lessonId) {
        // Record lesson completion progress
        $check = fetchSingleSecure("SELECT * FROM lesson_progress WHERE user_id = ? AND lesson_id = ?", [$userId, $lessonId]);
        if ($check) {
            dbQuery("UPDATE lesson_progress SET completed = 1, completed_at = NOW() WHERE user_id = ? AND lesson_id = ?", [$userId, $lessonId]);
        } else {
            dbQuery("INSERT INTO lesson_progress (user_id, lesson_id, completed, completed_at) VALUES (?, ?, 1, NOW())", [$userId, $lessonId]);
        }
    }
}
?>
