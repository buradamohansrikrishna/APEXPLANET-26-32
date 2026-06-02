<?php
// ========================================================
// SKILLSPHERE HIGH-FIDELITY VECTOR SVG GENERATOR
// database/create_svgs.php
// ========================================================

require_once __DIR__ . '/../db.php';

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

echo "=== SkillSphere Premium SVG Visuals Builder ===\n";

// Target Directories
$heroDir = __DIR__ . '/../assets/images/hero';
$thumbDir = __DIR__ . '/../uploads/thumbnails';

// Ensure directories exist
if (!file_exists($heroDir)) mkdir($heroDir, 0777, true);
if (!file_exists($thumbDir)) mkdir($thumbDir, 0777, true);

// 1. Homepage Hero Banner SVG
$heroBannerSvg = '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 900 500" width="100%" height="100%">
    <defs>
        <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#f8fafc" />
            <stop offset="100%" stop-color="#f1f5f9" />
        </linearGradient>
        <linearGradient id="primaryGrad" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stop-color="#4f46e5" />
            <stop offset="100%" stop-color="#7c3aed" />
        </linearGradient>
        <linearGradient id="chartGrad" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#818cf8" stop-opacity="0.4" />
            <stop offset="100%" stop-color="#818cf8" stop-opacity="0.0" />
        </linearGradient>
        <filter id="shadow" x="-5%" y="-5%" width="110%" height="115%" filterUnits="userSpaceOnUse">
            <feDropShadow dx="0" dy="15" stdDeviation="20" flood-color="#4f46e5" flood-opacity="0.08" />
        </filter>
    </defs>
    
    <!-- Background -->
    <rect width="900" height="500" rx="24" fill="url(#bgGrad)" stroke="#e2e8f0" stroke-width="1"/>
    
    <!-- SaaS Window Mockup -->
    <g filter="url(#shadow)">
        <rect x="80" y="60" width="740" height="380" rx="16" fill="#ffffff" stroke="#e2e8f0" stroke-width="1.5" />
        
        <!-- Window Header -->
        <path d="M 80,76 A 16,16 0 0 1 96,60 L 804,60 A 16,16 0 0 1 820,76 L 820,95 L 80,95 Z" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1" />
        <!-- Window Dots -->
        <circle cx="105" cy="78" r="6" fill="#ef4444" />
        <circle cx="125" cy="78" r="6" fill="#eab308" />
        <circle cx="145" cy="78" r="6" fill="#22c55e" />
        <rect x="180" y="70" width="120" height="16" rx="8" fill="#e2e8f0" />
        
        <!-- Sidebar -->
        <rect x="80" y="95" width="160" height="345" fill="#f8fafc" border-right="1px solid #e2e8f0" />
        <line x1="240" y1="95" x2="240" y2="440" stroke="#e2e8f0" stroke-width="1.5" />
        <rect x="95" y="120" width="130" height="20" rx="10" fill="#e0e7ff" />
        <rect x="95" y="160" width="100" height="14" rx="7" fill="#cbd5e1" />
        <rect x="95" y="195" width="110" height="14" rx="7" fill="#cbd5e1" />
        <rect x="95" y="230" width="90" height="14" rx="7" fill="#cbd5e1" />
        
        <!-- Main Content (Dashboard Charts) -->
        <!-- Stat Cards -->
        <rect x="265" y="120" width="165" height="80" rx="12" fill="#ffffff" stroke="#e2e8f0" stroke-width="1" />
        <rect x="285" y="135" width="70" height="10" rx="5" fill="#94a3b8" />
        <text x="285" y="175" font-family="system-ui" font-size="28" font-weight="bold" fill="#0f172a">15,420</text>
        <circle cx="400" cy="160" r="14" fill="#ecfdf5" />
        <path d="M394,163 L400,157 L406,163" stroke="#10b981" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
        
        <rect x="445" y="120" width="165" height="80" rx="12" fill="#ffffff" stroke="#e2e8f0" stroke-width="1" />
        <rect x="465" y="135" width="80" height="10" rx="5" fill="#94a3b8" />
        <text x="465" y="175" font-family="system-ui" font-size="28" font-weight="bold" fill="#0f172a">₹8,450</text>
        <circle cx="580" cy="160" r="14" fill="#eff6ff" />
        <path d="M574,163 L580,157 L586,163" stroke="#3b82f6" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
        
        <rect x="625" y="120" width="170" height="80" rx="12" fill="#ffffff" stroke="#e2e8f0" stroke-width="1" />
        <rect x="645" y="135" width="60" height="10" rx="5" fill="#94a3b8" />
        <text x="645" y="175" font-family="system-ui" font-size="28" font-weight="bold" fill="#0f172a">98.2%</text>
        
        <!-- Big Area Chart -->
        <rect x="265" y="220" width="530" height="195" rx="16" fill="#ffffff" stroke="#e2e8f0" stroke-width="1.5" />
        <rect x="290" y="240" width="120" height="12" rx="6" fill="#64748b" />
        
        <!-- Chart Grid Lines -->
        <line x1="290" y1="280" x2="770" y2="280" stroke="#f1f5f9" stroke-width="1" />
        <line x1="290" y1="320" x2="770" y2="320" stroke="#f1f5f9" stroke-width="1" />
        <line x1="290" y1="360" x2="770" y2="360" stroke="#f1f5f9" stroke-width="1" />
        
        <!-- Area Path -->
        <path d="M 290,360 Q 350,330 410,290 T 530,310 T 650,260 T 770,240 L 770,380 L 290,380 Z" fill="url(#chartGrad)" />
        <path d="M 290,360 Q 350,330 410,290 T 530,310 T 650,260 T 770,240" fill="none" stroke="url(#primaryGrad)" stroke-width="4" stroke-linecap="round" />
        
        <!-- Chart Dots -->
        <circle cx="410" cy="290" r="6" fill="#4f46e5" stroke="#ffffff" stroke-width="2" />
        <circle cx="650" cy="260" r="6" fill="#7c3aed" stroke="#ffffff" stroke-width="2" />
        <circle cx="770" cy="240" r="6" fill="#7c3aed" stroke="#ffffff" stroke-width="2" />
    </g>
</svg>';

// 2. About Page Hero SVG
$aboutSvg = '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" width="100%" height="100%">
    <defs>
        <linearGradient id="circle1" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#6366f1" />
            <stop offset="100%" stop-color="#4f46e5" />
        </linearGradient>
        <linearGradient id="circle2" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#a78bfa" />
            <stop offset="100%" stop-color="#7c3aed" />
        </linearGradient>
        <linearGradient id="circle3" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#fda4af" />
            <stop offset="100%" stop-color="#e11d48" />
        </linearGradient>
        <filter id="cardShadow" x="-10%" y="-10%" width="120%" height="120%">
            <feDropShadow dx="0" dy="10" stdDeviation="15" flood-color="#0f172a" flood-opacity="0.05" />
        </filter>
    </defs>
    
    <rect width="500" height="500" rx="32" fill="#faf9f5" />
    
    <!-- Abstract Collaboration Visuals -->
    <g transform="translate(50, 50)">
        <!-- Overlapping glowing spheres representing skills -->
        <circle cx="160" cy="180" r="110" fill="url(#circle1)" opacity="0.85" />
        <circle cx="280" cy="220" r="90" fill="url(#circle2)" opacity="0.8" />
        <circle cx="200" cy="290" r="80" fill="url(#circle3)" opacity="0.75" />
        
        <!-- Connection Network overlay -->
        <line x1="160" y1="180" x2="280" y2="220" stroke="#ffffff" stroke-width="2.5" stroke-dasharray="6,6" opacity="0.8" />
        <line x1="280" y1="220" x2="200" y2="290" stroke="#ffffff" stroke-width="2.5" stroke-dasharray="6,6" opacity="0.8" />
        <line x1="200" y1="290" x2="160" y2="180" stroke="#ffffff" stroke-width="2.5" stroke-dasharray="6,6" opacity="0.8" />
        
        <!-- Floating Interface Card -->
        <g filter="url(#cardShadow)">
            <rect x="70" y="240" width="180" height="110" rx="16" fill="#ffffff" stroke="rgba(99, 102, 241, 0.08)" stroke-width="1.5" />
            <circle cx="100" cy="275" r="16" fill="#ecfdf5" />
            <path d="M96,277 C96,272 104,272 104,277" stroke="#10b981" stroke-width="2" fill="none" stroke-linecap="round" />
            <circle cx="100" cy="270" r="4" fill="#10b981" />
            
            <rect x="130" y="265" width="90" height="8" rx="4" fill="#4f46e5" />
            <rect x="130" y="280" width="60" height="6" rx="3" fill="#94a3b8" />
            
            <!-- Sparkles -->
            <path d="M 130,315 L 132,310 L 137,308 L 132,306 L 130,301 L 128,306 L 123,308 L 128,310 Z" fill="#eab308" />
            <rect x="145" y="306" width="55" height="6" rx="3" fill="#cbd5e1" />
        </g>
        
        <!-- Another Tiny Card -->
        <g filter="url(#cardShadow)">
            <rect x="230" y="100" width="150" height="90" rx="14" fill="#ffffff" stroke="rgba(99, 102, 241, 0.08)" stroke-width="1.5" />
            <rect x="250" y="125" width="70" height="10" rx="5" fill="#7c3aed" />
            <rect x="250" y="145" width="90" height="6" rx="3" fill="#cbd5e1" />
            <rect x="250" y="157" width="50" height="6" rx="3" fill="#cbd5e1" />
            <circle cx="345" cy="135" r="10" fill="#fef3c7" />
        </g>
    </g>
</svg>';

// 3. Course Thumbnails SVGs
$courseSvgs = [
    'react-19-next-js-15-complete-guide' => '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" width="100%" height="100%">
    <defs>
        <linearGradient id="reactGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#06b6d4" />
            <stop offset="100%" stop-color="#4f46e5" />
        </linearGradient>
    </defs>
    <!-- Background with abstract grid pattern -->
    <rect width="400" height="250" fill="#0f172a" />
    <path d="M0,0 L400,250 M0,50 L400,300 M-50,0 L350,250 M0,-50 L400,150" stroke="#1e293b" stroke-width="1" />
    
    <!-- React Logo representation (Glowing abstract orbitals) -->
    <g transform="translate(200, 125)">
        <ellipse rx="100" ry="38" fill="none" stroke="url(#reactGrad)" stroke-width="3" opacity="0.75" />
        <ellipse rx="100" ry="38" fill="none" stroke="url(#reactGrad)" stroke-width="3" transform="rotate(60)" opacity="0.75" />
        <ellipse rx="100" ry="38" fill="none" stroke="url(#reactGrad)" stroke-width="3" transform="rotate(120)" opacity="0.75" />
        <circle cx="0" cy="0" r="16" fill="#00d8ff" filter="drop-shadow(0 0 10px #00d8ff)" />
    </g>
    
    <!-- Title banner -->
    <rect x="25" y="190" width="160" height="28" rx="6" fill="#1e293b" stroke="rgba(6, 182, 212, 0.3)" stroke-width="1.5" />
    <text x="40" y="209" font-family="system-ui" font-size="12" font-weight="bold" fill="#00d8ff">REACT 19 &amp; NEXT.JS</text>
</svg>',

    'advanced-system-design-microservices' => '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" width="100%" height="100%">
    <defs>
        <linearGradient id="sysGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#3b82f6" />
            <stop offset="100%" stop-color="#1d4ed8" />
        </linearGradient>
    </defs>
    <rect width="400" height="250" fill="#faf9f5" />
    
    <!-- Grid nodes representing system design blocks -->
    <g transform="translate(40, 30)">
        <!-- Microservice nodes -->
        <rect x="20" y="70" width="80" height="50" rx="8" fill="url(#sysGrad)" stroke="#1e40af" stroke-width="1.5" />
        <text x="35" y="100" font-family="system-ui" font-size="11" font-weight="bold" fill="#ffffff">Gateway</text>
        
        <rect x="170" y="20" width="80" height="50" rx="8" fill="#ffffff" stroke="#3b82f6" stroke-width="1.5" />
        <text x="182" y="50" font-family="system-ui" font-size="11" font-weight="bold" fill="#1e3a8a">Auth-Svc</text>
        
        <rect x="170" y="120" width="80" height="50" rx="8" fill="#ffffff" stroke="#3b82f6" stroke-width="1.5" />
        <text x="182" y="150" font-family="system-ui" font-size="11" font-weight="bold" fill="#1e3a8a">Core-Svc</text>
        
        <!-- Database node -->
        <cylinder>
            <rect x="290" y="70" width="60" height="55" rx="10" fill="#f8fafc" stroke="#f59e0b" stroke-width="2" />
            <line x1="290" y1="88" x2="350" y2="88" stroke="#f59e0b" stroke-width="2" />
            <line x1="290" y1="106" x2="350" y2="106" stroke="#f59e0b" stroke-width="2" />
            <text x="303" y="105" font-family="system-ui" font-size="11" font-weight="bold" fill="#b45309">DB</text>
        </cylinder>
        
        <!-- Connection pipes -->
        <path d="M100,95 L170,45" fill="none" stroke="#64748b" stroke-width="2" stroke-dasharray="4,4" />
        <path d="M100,95 L170,145" fill="none" stroke="#64748b" stroke-width="2" stroke-dasharray="4,4" />
        <path d="M250,45 L290,95" fill="none" stroke="#64748b" stroke-width="2" />
        <path d="M250,145 L290,95" fill="none" stroke="#64748b" stroke-width="2" />
    </g>
</svg>',

    'ai-deep-learning-bootcamp' => '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" width="100%" height="100%">
    <defs>
        <radialGradient id="aiGlow" cx="50%" cy="50%" r="50%">
            <stop offset="0%" stop-color="#ec4899" stop-opacity="0.3" />
            <stop offset="100%" stop-color="#7c3aed" stop-opacity="0.0" />
        </radialGradient>
    </defs>
    <rect width="400" height="250" fill="#1e1b4b" />
    
    <!-- AI Brain nodes synapse network -->
    <rect width="400" height="250" fill="url(#aiGlow)" />
    
    <g transform="translate(50, 40)" stroke="#ec4899" stroke-width="1.5">
        <!-- Input Layer -->
        <circle cx="50" cy="50" r="8" fill="#ec4899" />
        <circle cx="50" cy="110" r="8" fill="#ec4899" />
        <circle cx="50" cy="170" r="8" fill="#ec4899" />
        
        <!-- Hidden Layer 1 -->
        <circle cx="150" cy="30" r="8" fill="#8b5cf6" stroke="#8b5cf6" />
        <circle cx="150" cy="90" r="8" fill="#8b5cf6" stroke="#8b5cf6" />
        <circle cx="150" cy="150" r="8" fill="#8b5cf6" stroke="#8b5cf6" />
        <circle cx="150" cy="210" r="8" fill="#8b5cf6" stroke="#8b5cf6" />
        
        <!-- Output Layer -->
        <circle cx="250" cy="80" r="8" fill="#e11d48" stroke="#e11d48" />
        <circle cx="250" cy="140" r="8" fill="#e11d48" stroke="#e11d48" />
        
        <!-- Connectors -->
        <line x1="50" y1="50" x2="150" y2="30" opacity="0.3" />
        <line x1="50" y1="50" x2="150" y2="90" opacity="0.3" />
        <line x1="50" y1="50" x2="150" y2="150" opacity="0.3" />
        
        <line x1="50" y1="110" x2="150" y2="30" opacity="0.3" />
        <line x1="50" y1="110" x2="150" y2="90" opacity="0.3" />
        <line x1="50" y1="110" x2="150" y2="150" opacity="0.3" />
        <line x1="50" y1="110" x2="150" y2="210" opacity="0.3" />
        
        <line x1="150" y1="30" x2="250" y2="80" opacity="0.4" />
        <line x1="150" y1="90" x2="250" y2="80" opacity="0.4" />
        <line x1="150" y1="90" x2="250" y2="140" opacity="0.4" />
        <line x1="150" y1="150" x2="250" y2="140" opacity="0.4" />
        <line x1="150" y1="210" x2="250" y2="140" opacity="0.4" />
    </g>
    
    <!-- Tech badge -->
    <rect x="25" y="195" width="140" height="24" rx="6" fill="#ec4899" />
    <text x="35" y="211" font-family="system-ui" font-size="10" font-weight="bold" fill="#ffffff">AI &amp; BOOTCAMP DEEP</text>
</svg>',

    'ethical-hacking-network-pen-testing' => '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" width="100%" height="100%">
    <rect width="400" height="250" fill="#0f172a" />
    <!-- Matrix digital green/crimson grid lines -->
    <line x1="0" y1="40" x2="400" y2="40" stroke="#1e293b" />
    <line x1="0" y1="80" x2="400" y2="80" stroke="#1e293b" />
    <line x1="0" y1="120" x2="400" y2="120" stroke="#1e293b" />
    <line x1="0" y1="160" x2="400" y2="160" stroke="#1e293b" />
    <line x1="0" y1="200" x2="400" y2="200" stroke="#1e293b" />
    
    <!-- Padlock Graphic -->
    <g transform="translate(200, 115)">
        <rect x="-35" y="-10" width="70" height="60" rx="10" fill="none" stroke="#ef4444" stroke-width="4" />
        <path d="M-20,-10 L-20,-35 A20,20 0 0,1 20,-35 L20,-10" fill="none" stroke="#ef4444" stroke-width="4" stroke-linecap="round" />
        <circle cx="0" cy="15" r="7" fill="#ef4444" />
        <path d="M0,22 L0,35" stroke="#ef4444" stroke-width="3" />
    </g>
</svg>',

    'docker-kubernetes-aws-devops' => '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" width="100%" height="100%">
    <defs>
        <linearGradient id="devopsGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#f97316" />
            <stop offset="100%" stop-color="#3b82f6" />
        </linearGradient>
    </defs>
    <rect width="400" height="250" fill="#f8fafc" />
    
    <!-- DevOps Infinity loop -->
    <path d="M 120,125 C 120,90 190,90 200,125 C 210,160 280,160 280,125 C 280,90 210,90 200,125 C 190,160 120,160 120,125 Z" 
          fill="none" stroke="url(#devopsGrad)" stroke-width="12" stroke-linecap="round" stroke-linejoin="round" />
          
    <text x="145" y="130" font-family="system-ui" font-size="14" font-weight="bold" fill="#0f172a">DEVOPS</text>
</svg>',

    'ui-ux-design-systems-premium-saas' => '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" width="100%" height="100%">
    <rect width="400" height="250" fill="#faf9f5" />
    
    <!-- Overlapping UI windows mockups -->
    <rect x="50" y="40" width="180" height="120" rx="10" fill="#ffffff" stroke="#e2e8f0" stroke-width="1.5" />
    <circle cx="70" cy="55" r="4" fill="#ff5f56" />
    <circle cx="80" cy="55" r="4" fill="#ffbd2e" />
    <circle cx="90" cy="55" r="4" fill="#27c93f" />
    
    <rect x="70" y="80" width="140" height="10" rx="5" fill="#f1f5f9" />
    <rect x="70" y="100" width="100" height="8" rx="4" fill="#6366f1" />
    
    <!-- Second overlapping wireframe window -->
    <rect x="150" y="80" width="200" height="130" rx="12" fill="#ffffff" stroke="#6366f1" stroke-width="2" />
    <circle cx="175" cy="100" r="10" fill="#e0e7ff" />
    
    <!-- Palette Dots -->
    <circle cx="170" cy="180" r="8" fill="#4f46e5" />
    <circle cx="190" cy="180" r="8" fill="#7c3aed" />
    <circle cx="210" cy="180" r="8" fill="#14b8a6" />
    <circle cx="230" cy="180" r="8" fill="#e11d48" />
</svg>',

    'high-performance-database-engineering' => '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" width="100%" height="100%">
    <rect width="400" height="250" fill="#faf9f5" />
    
    <!-- Server Stack Layout -->
    <g transform="translate(130, 45)">
        <rect x="0" y="0" width="140" height="40" rx="8" fill="#ffffff" stroke="#cbd5e1" stroke-width="2" />
        <rect x="15" y="15" width="10" height="10" rx="5" fill="#22c55e" />
        <line x1="40" y1="20" x2="110" y2="20" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round" />
        
        <rect x="0" y="55" width="140" height="40" rx="8" fill="#ffffff" stroke="#4f46e5" stroke-width="2" />
        <rect x="15" y="70" width="10" height="10" rx="5" fill="#22c55e" />
        <line x1="40" y1="75" x2="110" y2="75" stroke="#4f46e5" stroke-width="4" stroke-linecap="round" />
        
        <rect x="0" y="110" width="140" height="40" rx="8" fill="#ffffff" stroke="#cbd5e1" stroke-width="2" />
        <rect x="15" y="125" width="10" height="10" rx="5" fill="#cbd5e1" />
        <line x1="40" y1="130" x2="110" y2="130" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round" />
    </g>
</svg>',

    'python-data-science-analytics' => '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" width="100%" height="100%">
    <rect width="400" height="250" fill="#faf9f5" />
    
    <!-- Data science bar charts & curves -->
    <g transform="translate(60, 40)">
        <line x1="20" y1="160" x2="320" y2="160" stroke="#475569" stroke-width="2" />
        <line x1="20" y1="20" x2="20" y2="160" stroke="#475569" stroke-width="2" />
        
        <!-- Bars -->
        <rect x="50" y="90" width="24" height="70" fill="#3b82f6" />
        <rect x="90" y="60" width="24" height="100" fill="#8b5cf6" />
        <rect x="130" y="40" width="24" height="120" fill="#ec4899" />
        <rect x="170" y="80" width="24" height="80" fill="#f97316" />
        
        <!-- Trend Curve -->
        <path d="M 62,80 Q 102,50 142,30 T 222,90 T 302,40" fill="none" stroke="#10b981" stroke-width="4" />
        <circle cx="302" cy="40" r="6" fill="#10b981" />
    </g>
</svg>',

    'advanced-backend-go-grpc' => '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" width="100%" height="100%">
    <rect width="400" height="250" fill="#0f766e" />
    
    <!-- Server layout representing Go backends -->
    <g transform="translate(100, 60)" stroke="#ffffff" stroke-width="2">
        <circle cx="50" cy="60" r="24" fill="none" />
        <circle cx="150" cy="60" r="24" fill="none" />
        
        <path d="M 26,60 L 0,60 M 74,60 L 126,60 M 174,60 L 200,60" />
        <text x="38" y="66" font-family="system-ui" font-size="16" font-weight="bold" fill="#ffffff">Go</text>
        <text x="132" y="66" font-family="system-ui" font-size="15" font-weight="bold" fill="#ffffff">RPC</text>
    </g>
</svg>',

    'modern-javascript-es6-complete-mastery' => '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" width="100%" height="100%">
    <rect width="400" height="250" fill="#f7df1e" />
    
    <!-- JS Shield logo representation -->
    <g transform="translate(130, 45)">
        <polygon points="20,0 120,0 110,130 70,160 30,130" fill="#000000" />
        <text x="40" y="110" font-family="system-ui" font-weight="900" font-size="70" fill="#f7df1e">JS</text>
    </g>
    <rect x="25" y="195" width="130" height="24" rx="6" fill="#000000" />
    <text x="35" y="211" font-family="system-ui" font-size="10" font-weight="bold" fill="#f7df1e">JAVASCRIPT MASTER</text>
</svg>'
];

// Write Homepage Hero Banner
$heroPath = $heroDir . '/hero-banner.svg';
file_put_contents($heroPath, $heroBannerSvg);
echo "Generated Hero Banner: $heroPath\n";

// Write About Hero
$aboutPath = $heroDir . '/about.svg';
file_put_contents($aboutPath, $aboutSvg);
echo "Generated About Hero: $aboutPath\n";

// Write Course Thumbnails & Update Database
foreach ($courseSvgs as $slug => $svgCode) {
    $thumbPath = $thumbDir . '/' . $slug . '.svg';
    file_put_contents($thumbPath, $svgCode);
    echo "Generated Course Thumbnail SVG: $thumbPath\n";
    
    // Update DB (check for webp, jpg, jpeg, png, fallback to svg)
    $fileName = $slug . '.svg';
    foreach (['webp', 'jpg', 'jpeg', 'png'] as $ext) {
        if (file_exists($thumbDir . '/' . $slug . '.' . $ext)) {
            $fileName = $slug . '.' . $ext;
            break;
        }
    }
    $stmt = mysqli_prepare($conn, "UPDATE courses SET thumbnail = ? WHERE slug = ?");
    mysqli_stmt_bind_param($stmt, "ss", $fileName, $slug);
    mysqli_stmt_execute($stmt);
}

echo "\nAll SVGs successfully created and mapped to the database!\n";
?>
