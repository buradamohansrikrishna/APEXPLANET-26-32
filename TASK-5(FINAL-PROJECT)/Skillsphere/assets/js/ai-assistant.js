// =========================================
// SKILLSPHERE AI LEARNING ASSISTANT
// assets/js/ai-assistant.js
// =========================================

document.addEventListener('DOMContentLoaded', () => {
    const aiForm = document.getElementById('aiAssistantForm');
    const aiResponse = document.getElementById('aiAssistantResponse');
    if (!aiForm) return;

    aiForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('aiPromptInput').value;
        if (!input.trim()) return;

        aiResponse.innerHTML = '<div class="loader-circle"></div> Generating study assistant guide...';
        
        try {
            const res = await fetch('api/ai/learning-assistant.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `prompt=${encodeURIComponent(input)}`
            });
            const data = await res.json();
            if (data.success) {
                aiResponse.innerHTML = `<div class="ai-guide-box reveal">${data.guide}</div>`;
            } else {
                aiResponse.innerHTML = '<div class="alert alert-danger">Error fetching suggestions</div>';
            }
        } catch (err) {
            aiResponse.innerHTML = '<div class="alert alert-danger">Network error</div>';
        }
    });
});
