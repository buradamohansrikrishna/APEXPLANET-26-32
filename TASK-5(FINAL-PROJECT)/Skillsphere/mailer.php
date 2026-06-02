<?php
// =========================================
// SKILLSPHERE MAIL SENDER
// mailer.php
// =========================================

require_once __DIR__ . '/config.php';

if (!function_exists('sendMail')) {
    function sendMail($to, $subject, $bodyTemplate, $data = []) {
        $mailDir = __DIR__ . '/logs';
        if (!is_dir($mailDir)) {
            mkdir($mailDir, 0777, true);
        }
        
        // Compile template
        $body = $bodyTemplate;
        foreach ($data as $key => $val) {
            $body = str_replace('{{' . $key . '}}', $val, $body);
        }
        
        $logFile = $mailDir . '/mail.log';
        $logMessage = "[" . date('Y-m-d H:i:s') . "] TO: $to | SUBJECT: $subject\nBODY:\n$body\n" . str_repeat('-', 50) . "\n";
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        // Return true as simulated success
        return true;
    }
}
?>
