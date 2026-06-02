<?php
// =========================================
// SKILLSPHERE AI RECOMMENDATION ENGINE
// ai/recommendation-engine.php
// =========================================

require_once __DIR__ . '/../db.php';

class RecommendationEngine {
    public static function getRecommendations($userId, $limit = 3) {
        // Query user's current categories
        $enrolledCats = fetchAllSecure("
            SELECT DISTINCT c.category_id 
            FROM enrollments e 
            JOIN courses c ON e.course_id = c.id 
            WHERE e.user_id = ?
        ", [$userId]);
        
        $catIds = array_column($enrolledCats, 'category_id');
        
        if (!empty($catIds)) {
            $catStr = implode(',', array_map('intval', $catIds));
            return fetchAllSecure("
                SELECT c.*, cat.category_name 
                FROM courses c
                JOIN categories cat ON c.category_id = cat.id
                WHERE c.category_id IN ($catStr) AND c.id NOT IN (
                    SELECT course_id FROM enrollments WHERE user_id = ?
                )
                ORDER BY RAND() LIMIT ?
            ", [$userId, $limit]);
        }
        
        // Fallback: Random premium courses
        return fetchAllSecure("
            SELECT c.*, cat.category_name 
            FROM courses c
            JOIN categories cat ON c.category_id = cat.id
            ORDER BY RAND() LIMIT ?
        ", [$limit]);
    }
}
?>
