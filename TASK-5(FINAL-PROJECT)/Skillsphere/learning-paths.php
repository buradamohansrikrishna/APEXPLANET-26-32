<?php
$pageTitle = 'Learning Paths';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';

$paths = [
    [
        'title' => 'Frontend Engineering Career Path',
        'desc' => 'Master React, Next.js, web accessibility, and performance tuning. From baseline HTML to scaling enterprise SPAs.',
        'courses' => ['React 19 Complete Guide', 'Modern Javascript ES6 Mastery', 'UI UX Design Systems'],
        'icon' => 'fa-laptop-code'
    ],
    [
        'title' => 'Backend Architect Career Path',
        'desc' => 'Master Go microservices, database engine indexing, query optimizations, Docker containers, and gRPC communication.',
        'courses' => ['Advanced Backend with Go & gRPC', 'High Performance Database Engineering', 'Docker, Kubernetes & AWS DevOps'],
        'icon' => 'fa-server'
    ]
];
?>

<section class="page-header">
    <div class="container">
        <span class="badge badge-primary">Structured Pathways</span>
        <h1 class="fade">Guided Learning Paths</h1>
        <p class="fade">Follow structured, step-by-step pathways designed by industry leads to take you from beginner to job-ready.</p>
    </div>
</section>

<div class="container" style="margin-top:4rem; margin-bottom:6rem;">
    <div style="display:flex; flex-direction:column; gap:2.5rem; max-width:800px; margin: 0 auto;">
        <?php foreach ($paths as $path): ?>
            <div class="card reveal" style="padding:2.5rem; display:flex; gap:2rem; align-items:flex-start;">
                <div style="font-size:3.5rem; color:var(--brand-500); padding: 1rem; background:var(--brand-50); border-radius:12px; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid <?php echo $path['icon']; ?>"></i>
                </div>
                <div>
                    <h2><?php echo htmlspecialchars($path['title']); ?></h2>
                    <p style="color:var(--text-secondary); margin-top:0.75rem; margin-bottom:1.5rem; line-height:1.6;"><?php echo htmlspecialchars($path['desc']); ?></p>
                    
                    <h4 style="margin-bottom:0.75rem;">Courses in this path:</h4>
                    <ul style="list-style:none; padding:0; display:flex; flex-direction:column; gap:0.5rem; color:var(--text-secondary);">
                        <?php foreach ($path['courses'] as $c): ?>
                            <li><i class="fa-solid fa-circle-play" style="color:var(--brand-500); margin-right:0.5rem;"></i> <?php echo htmlspecialchars($c); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
