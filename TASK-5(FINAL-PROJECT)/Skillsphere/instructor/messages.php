<?php
require_once '../auth.php';
require_once '../middleware.php';
requireRole('instructor');

$jsonFile = '../storage/cache/messages.json';
if (!file_exists($jsonFile)) {
    $defaults = [
        [
            'id' => 1,
            'student' => 'Sravanthi',
            'course' => 'Advanced Go with gRPC',
            'question' => 'How can I configure dynamic middleware in the grpc server interceptors?',
            'replied' => false
        ],
        [
            'id' => 2,
            'student' => 'Sai Kiran',
            'course' => 'React 19 Complete Guide',
            'question' => 'Should I use server actions for fetching initial data, or use standard fetch inside client components?',
            'replied' => true,
            'reply' => 'For initial page loading, Server Components with direct DB calls are best. Reserve Server Actions for mutating data!'
        ]
    ];
    file_put_contents($jsonFile, json_encode($defaults, JSON_PRETTY_PRINT));
}

$messages = json_decode(file_get_contents($jsonFile), true);

if (isset($_POST['reply_btn'])) {
    $msgId = (int)$_POST['msg_id'];
    $replyText = sanitize($_POST['reply_text']);
    
    foreach ($messages as &$m) {
        if ($m['id'] === $msgId) {
            $m['replied'] = true;
            $m['reply'] = $replyText;
        }
    }
    file_put_contents($jsonFile, json_encode($messages, JSON_PRETTY_PRINT));
    $_SESSION['success'] = 'Reply sent successfully!';
    header('Location: messages.php');
    exit();
}

$pageTitle = 'QA Messages - Instructor Portal';
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <!-- Navigation links for portal -->
    <div style="display:flex; gap:1rem; border-bottom:1px solid var(--border-default); padding-bottom:1rem; margin-bottom:2rem;">
        <a href="dashboard.php" class="btn btn-sm btn-outline">Courses List</a>
        <a href="students.php" class="btn btn-sm btn-outline">My Students</a>
        <a href="analytics.php" class="btn btn-sm btn-outline">Analytics</a>
        <a href="earnings.php" class="btn btn-sm btn-outline">Earnings Report</a>
        <a href="messages.php" class="btn btn-sm btn-primary">QA Messages</a>
        <a href="reviews.php" class="btn btn-sm btn-outline">Course Reviews</a>
    </div>

    <h2 style="margin-bottom: 1.5rem;">Student Questions</h2>
    <div class="card reveal" style="padding: 2rem;">
        <?php if (!empty($messages)): ?>
            <div style="display:flex; flex-direction:column; gap:1.5rem;">
                <?php foreach ($messages as $msg): ?>
                    <div class="card" style="padding:1.5rem; border:1px solid var(--border-default);">
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.75rem;">
                            <span>Student <strong><?php echo htmlspecialchars($msg['student']); ?></strong> on <em><?php echo htmlspecialchars($msg['course']); ?></em></span>
                            <?php if ($msg['replied']): ?>
                                <span class="badge badge-success">Replied</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Awaiting Reply</span>
                            <?php endif; ?>
                        </div>
                        <p style="font-weight:bold; font-size:1.1rem; margin-bottom:1rem;">"<?php echo htmlspecialchars($msg['question']); ?>"</p>
                        
                        <?php if ($msg['replied']): ?>
                            <div style="background:var(--bg-subtle); padding:1rem; border-radius:8px; border-left:4px solid var(--brand-500);">
                                <p style="font-size:0.875rem; color:var(--text-tertiary); margin-bottom:0.25rem;">Your Answer:</p>
                                <p><?php echo htmlspecialchars($msg['reply']); ?></p>
                            </div>
                        <?php else: ?>
                            <form method="POST" style="display:flex; gap:0.5rem; margin-top:1rem;">
                                <input type="hidden" name="msg_id" value="<?php echo $msg['id']; ?>">
                                <input type="text" name="reply_text" class="form-control" placeholder="Write your reply..." required autocomplete="off">
                                <button type="submit" name="reply_btn" class="btn btn-primary">Send</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No questions submitted by students.</p>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
