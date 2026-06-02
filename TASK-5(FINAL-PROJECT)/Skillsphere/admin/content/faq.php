<?php
require_once '../../auth.php';
requireAdmin();

$jsonFile = '../../storage/cache/faq.json';
if (!file_exists($jsonFile)) {
    $defaults = [
        [
            'id' => 1,
            'question' => 'How do I get started?',
            'answer' => 'Create a free account, browse courses, and enroll instantly. Your dashboard tracks progress.'
        ],
        [
            'id' => 2,
            'question' => 'Are certificates included?',
            'answer' => 'Yes — complete course requirements to earn verifiable certificates you can share on LinkedIn.'
        ]
    ];
    file_put_contents($jsonFile, json_encode($defaults, JSON_PRETTY_PRINT));
}

$faqs = json_decode(file_get_contents($jsonFile), true);

if (isset($_POST['add'])) {
    $q = sanitize($_POST['question']);
    $a = sanitize($_POST['answer']);
    
    $new = [
        'id' => count($faqs) > 0 ? max(array_column($faqs, 'id')) + 1 : 1,
        'question' => $q,
        'answer' => $a
    ];
    $faqs[] = $new;
    file_put_contents($jsonFile, json_encode($faqs, JSON_PRETTY_PRINT));
    $_SESSION['success'] = 'FAQ entry added!';
    header('Location: faq.php');
    exit();
}

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $faqs = array_filter($faqs, fn($f) => $f['id'] !== $delId);
    file_put_contents($jsonFile, json_encode(array_values($faqs), JSON_PRETTY_PRINT));
    $_SESSION['success'] = 'FAQ entry deleted!';
    header('Location: faq.php');
    exit();
}

$adminTitle = 'Manage FAQs';
$adminPage = 'faq';
$adminHeading = 'Frequently asked questions';
$adminSubheading = 'Configure accordion items';
$adminIllustration = '../assets/images/admin-courses.svg';
$adminHeroTitle = 'FAQ helpdesk';
$adminHeroText = 'Add, modify, and review common platform questions and support items.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="grid grid-3">
    <div class="admin-panel reveal" style="grid-column: span 1;">
        <div class="admin-panel__head"><h3>Create FAQ</h3></div>
        <div class="admin-panel__body">
            <form method="POST">
                <div class="form-group">
                    <label for="question">Question</label>
                    <input type="text" name="question" id="question" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="answer">Answer</label>
                    <textarea name="answer" id="answer" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" name="add" class="admin-btn admin-btn--primary" style="margin-top:1rem; width:100%;">Create FAQ</button>
            </form>
        </div>
    </div>

    <div class="admin-panel reveal" style="grid-column: span 2;">
        <div class="admin-panel__head"><h3>Current FAQ List</h3></div>
        <div class="admin-panel__body">
            <div class="admin-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($faqs as $f): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($f['question']); ?></strong></td>
                            <td><?php echo htmlspecialchars($f['answer']); ?></td>
                            <td>
                                <a href="faq.php?delete=<?php echo $f['id']; ?>" class="delete-btn" onclick="return confirm('Delete FAQ?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
