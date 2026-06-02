<?php
$pageTitle = 'AI Study Assistant';
require_once 'db.php';
require_once 'functions.php';
require_once 'helpers.php';
include 'includes/header.php';
include 'includes/navbar.php';

// Fetch recommendations based on category
$recs = fetchAllSecure("
    SELECT c.*, cat.category_name, u.full_name AS instructor_name
    FROM courses c
    LEFT JOIN categories cat ON c.category_id = cat.id
    LEFT JOIN users u ON c.instructor_id = u.id
    WHERE c.status = 'published'
    ORDER BY RAND()
    LIMIT 3
");
?>

<section class="page-header">
    <div class="container">
        <span class="badge badge-primary">AI Copilot</span>
        <h1 class="fade">AI Learning Assistant</h1>
        <p class="fade">Ask questions, get study plans, and review concepts with your personal virtual copilot.</p>
    </div>
</section>

<div class="container" style="margin-top:4rem; margin-bottom:6rem;">
    <div class="grid grid-3">
        <!-- Assistant Interface -->
        <div class="card reveal" style="grid-column: span 2; padding:2.5rem;">
            <h2>Ask your Study Assistant</h2>
            <p style="color:var(--text-secondary); margin-bottom:1.5rem;">Need a summary of a coding topic? Input a topic below and get an AI study roadmap.</p>
            
            <form id="aiAssistantForm" style="display:flex; gap:0.5rem; margin-bottom:2rem;">
                <input type="text" id="aiPromptInput" class="form-control" placeholder="e.g. Write a study plan to learn gRPC protocol buffers" required autocomplete="off">
                <button type="submit" class="btn btn-primary">Generate Roadmap</button>
            </form>

            <div id="aiAssistantResponse" style="min-height:100px; line-height:1.7; color:var(--text-secondary);">
                <!-- Output will load here -->
            </div>
        </div>

        <!-- Recommendations Sidebar -->
        <div class="card reveal stagger-1" style="grid-column: span 1; padding:2rem;">
            <h2>Recommended for You</h2>
            <p style="color:var(--text-secondary); font-size:0.875rem; margin-bottom:1.5rem;">Curated based on trending categories.</p>
            
            <div style="display:flex; flex-direction:column; gap:1.25rem;">
                <?php foreach ($recs as $c): ?>
                    <div style="border-bottom:1px solid var(--border-default); padding-bottom:1rem;">
                        <h4 style="font-size:1rem;"><a href="course-details.php?id=<?php echo $c['id']; ?>" style="text-decoration:none; color:var(--text-primary);"><?php echo htmlspecialchars($c['title']); ?></a></h4>
                        <span style="font-size:0.75rem; color:var(--text-tertiary); display:block; margin-top:0.25rem;">
                            <?php echo htmlspecialchars($c['category_name']); ?> · <?php echo htmlspecialchars($c['duration']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/ai-assistant.js"></script>
<?php include 'includes/footer.php'; ?>
