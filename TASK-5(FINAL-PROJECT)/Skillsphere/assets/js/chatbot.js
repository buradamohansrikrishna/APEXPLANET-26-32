// =========================================
// SKILLSPHERE AI CHATBOT INTERACTION
// assets/js/chatbot.js
// =========================================

document.addEventListener('DOMContentLoaded', () => {
    const chatbotToggle = document.getElementById('chatbotToggle');
    const chatbotWindow = document.getElementById('chatbotWindow');
    const chatbotClose = document.getElementById('chatbotClose');
    const chatbotForm = document.getElementById('chatbotForm');
    const chatbotInput = document.getElementById('chatbotInput');
    const chatbotMessages = document.getElementById('chatbotMessages');

    if (!chatbotToggle) return;

    // Toggle Chatbot
    chatbotToggle.addEventListener('click', () => {
        chatbotWindow.classList.toggle('is-active');
        chatbotInput.focus();
    });

    chatbotClose.addEventListener('click', () => {
        chatbotWindow.classList.remove('is-active');
    });

    // Send message
    chatbotForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const query = chatbotInput.value.trim();
        if (!query) return;

        // Append User Message
        appendMessage('user', query);
        chatbotInput.value = '';

        // Typing indicator
        const typingId = appendMessage('bot', '<span class="typing-loader"></span>');

        // Fetch Response
        try {
            const rootPrefix = window.SKILLSPHERE_ROOT || '';
            const res = await fetch(rootPrefix + 'api/ai/ai-chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `message=${encodeURIComponent(query)}`
            });
            const data = await res.json();
            
            // Remove typing loader
            document.getElementById(typingId).remove();
            
            if (data.success) {
                appendMessage('bot', data.response);
            } else {
                appendMessage('bot', 'Sorry, I encountered an issue. Please try again.');
            }
        } catch (err) {
            document.getElementById(typingId).remove();
            appendMessage('bot', 'Network error. Please make sure the server is reachable.');
        }
    });

    function appendMessage(sender, text) {
        const msgId = 'msg-' + Date.now();
        const msgDiv = document.createElement('div');
        msgDiv.id = msgId;
        msgDiv.className = `chatbot__msg chatbot__msg--${sender}`;
        msgDiv.innerHTML = `<div class="chatbot__bubble">${text}</div>`;
        chatbotMessages.appendChild(msgDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        return msgId;
    }
});
