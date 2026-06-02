<!-- =========================================
     SKILLSPHERE AI CHATBOT COMPONENT
     includes/chatbot.php
========================================= -->
<div class="chatbot-widget">
    <button type="button" class="chatbot-toggle" id="chatbotToggle" aria-label="Open AI Assistant">
        <i class="fa-solid fa-message"></i>
    </button>
    
    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-window__header">
            <div class="chatbot-window__title">
                <i class="fa-solid fa-robot"></i>
                <div>
                    <strong>SkillSphere AI</strong>
                    <small>Online Assistant</small>
                </div>
            </div>
            <button type="button" class="chatbot-close" id="chatbotClose" aria-label="Close Chat">&times;</button>
        </div>
        
        <div class="chatbot-window__messages" id="chatbotMessages">
            <div class="chatbot__msg chatbot__msg--bot">
                <div class="chatbot__bubble">
                    Hello! I am your SkillSphere learning assistant. How can I help you today?
                </div>
            </div>
        </div>
        
        <form class="chatbot-window__input-wrap" id="chatbotForm">
            <input type="text" id="chatbotInput" placeholder="Ask a question about courses..." required autocomplete="off">
            <button type="submit" aria-label="Send message">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>
<script>
    window.SKILLSPHERE_ROOT = '<?php echo $rootPrefix ?? ""; ?>';
</script>
<script src="<?php echo $rootPrefix ?? ''; ?>assets/js/chatbot.js"></script>
