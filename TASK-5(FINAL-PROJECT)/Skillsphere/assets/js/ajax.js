// =========================================
// SKILLSPHERE AJAX UTILITY
// assets/js/ajax.js
// =========================================

const Ajax = {
    async get(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            return { success: false, error: error.message };
        }
    },

    async post(url, data) {
        const formData = new FormData();
        for (const key in data) {
            formData.append(key, data[key]);
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error('Post error:', error);
            return { success: false, error: error.message };
        }
    }
};
window.Ajax = Ajax;
