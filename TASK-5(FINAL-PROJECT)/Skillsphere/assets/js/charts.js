// =========================================
// SKILLSPHERE DASHBOARD CHARTS
// assets/js/charts.js
// =========================================

class DashboardChart {
    static drawLineChart(canvasId, data, labels, color = '#6366f1') {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        const width = canvas.width = canvas.parentElement.clientWidth;
        const height = canvas.height = 200;

        ctx.clearRect(0, 0, width, height);

        // Grid lines
        ctx.strokeStyle = '#e2e8f0';
        ctx.lineWidth = 1;
        for (let i = 1; i < 4; i++) {
            ctx.beginPath();
            ctx.moveTo(40, (height - 40) * (i / 4) + 10);
            ctx.lineTo(width - 20, (height - 40) * (i / 4) + 10);
            ctx.stroke();
        }

        // Draw Line
        const maxVal = Math.max(...data, 100);
        const points = data.map((val, idx) => {
            const x = 40 + (width - 60) * (idx / (data.length - 1));
            const y = height - 30 - (height - 60) * (val / maxVal);
            return { x, y };
        });

        // Gradient below the line
        const gradient = ctx.createLinearGradient(0, 0, 0, height);
        gradient.addColorStop(0, color + '40');
        gradient.addColorStop(1, color + '00');
        ctx.fillStyle = gradient;

        ctx.beginPath();
        ctx.moveTo(points[0].x, height - 30);
        points.forEach(p => ctx.lineTo(p.x, p.y));
        ctx.lineTo(points[points.length - 1].x, height - 30);
        ctx.closePath();
        ctx.fill();

        // Main line
        ctx.strokeStyle = color;
        ctx.lineWidth = 3;
        ctx.beginPath();
        points.forEach((p, idx) => {
            if (idx === 0) ctx.moveTo(p.x, p.y);
            else ctx.lineTo(p.x, p.y);
        });
        ctx.stroke();

        // Dots and Labels
        ctx.fillStyle = '#0f172a';
        ctx.font = '10px sans-serif';
        points.forEach((p, idx) => {
            ctx.beginPath();
            ctx.arc(p.x, p.y, 4, 0, Math.PI * 2);
            ctx.fillStyle = color;
            ctx.fill();
            ctx.stroke();

            // Text
            ctx.fillStyle = '#64748b';
            ctx.fillText(labels[idx], p.x - 10, height - 10);
        });
    }
}
window.DashboardChart = DashboardChart;
