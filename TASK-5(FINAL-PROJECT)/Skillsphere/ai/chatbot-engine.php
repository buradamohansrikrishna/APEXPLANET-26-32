<?php
// =========================================
// SKILLSPHERE AI CHATBOT ENGINE
// ai/chatbot-engine.php
// =========================================

class ChatbotEngine {
    public static function parseResponse($userQuery) {
        $query = strtolower(trim($userQuery));
        if (strpos($query, 'go') !== false || strpos($query, 'grpc') !== false) {
            return "Our 'Advanced Backend with Go & gRPC' teaches protocol buffers, custom interceptors, and high performance connection pools.";
        }
        if (strpos($query, 'react') !== false || strpos($query, 'next') !== false) {
            return "We recommend the 'React 19 & Next.js 15 Complete Guide'. Learn React Server Components, App Router, and scaling rendering modes.";
        }
        if (strpos($query, 'database') !== false || strpos($query, 'sql') !== false) {
            return "Try 'High Performance Database Engineering' to master SQL indexes, ACID locks, and database replication.";
        }
        return "I am your SkillSphere AI copilot. Ask me about our engineering courses, microservices, databases, or pricing plans!";
    }
}
?>
