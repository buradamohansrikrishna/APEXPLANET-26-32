<?php
require_once '../../auth.php';
requireAdmin();

// Testimonials management
$jsonFile = '../../storage/cache/testimonials.json';
if (!file_exists($jsonFile)) {
    $defaults = [
        [
            'id' => 1,
            'name' => 'Pranathi Goud',
            'role' => 'Frontend Engineer',
            'quote' => 'The platform UI alone sets SkillSphere apart. Courses are structured like real product work.',
            'approved' => true
        ],
        [
            'id' => 2,
            'name' => 'Kiran Kumar Chowdary',
            'role' => 'Full-stack Developer',
            'quote' => 'Finally an EdTech product that doesn\'t feel like a template.',
            'approved' => true
        ]
    ];
    file_put_contents($jsonFile, json_encode($defaults, JSON_PRETTY_PRINT));
}

$testimonials = json_decode(file_get_contents($jsonFile), true);

if (isset($_POST['add'])) {
    $name = sanitize($_POST['name']);
    $role = sanitize($_POST['role']);
    $quote = sanitize($_POST['quote']);
    
    $new = [
        'id' => count($testimonials) > 0 ? max(array_column($testimonials, 'id')) + 1 : 1,
        'name' => $name,
        'role' => $role,
        'quote' => $quote,
        'approved' => true
    ];
    $testimonials[] = $new;
    file_put_contents($jsonFile, json_encode($testimonials, JSON_PRETTY_PRINT));
    $_SESSION['success'] = 'Testimonial added!';
    header('Location: testimonials.php');
    exit();
}

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $testimonials = array_filter($testimonials, fn($t) => $t['id'] !== $delId);
    file_put_contents($jsonFile, json_encode(array_values($testimonials), JSON_PRETTY_PRINT));
    $_SESSION['success'] = 'Testimonial deleted!';
    header('Location: testimonials.php');
    exit();
}

$adminTitle = 'Testimonials';
$adminPage = 'testimonials';
$adminHeading = 'User reviews';
$adminSubheading = 'Manage public testimonials';
$adminIllustration = '../assets/images/admin-courses.svg';
$adminHeroTitle = 'Testimonials';
$adminHeroText = 'Edit, filter, and approve student success quotes displayed on the public landing page.';

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
        <div class="admin-panel__head"><h3>Add Testimonial</h3></div>
        <div class="admin-panel__body">
            <form method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="role">Role / Company</label>
                    <input type="text" name="role" id="role" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="quote">Quote</label>
                    <textarea name="quote" id="quote" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" name="add" class="admin-btn admin-btn--primary" style="margin-top:1rem; width:100%;">Create Testimonial</button>
            </form>
        </div>
    </div>

    <div class="admin-panel reveal" style="grid-column: span 2;">
        <div class="admin-panel__head"><h3>Active Testimonials</h3></div>
        <div class="admin-panel__body">
            <div class="admin-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Quote</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testimonials as $t): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($t['name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($t['role']); ?></small>
                            </td>
                            <td>"<?php echo htmlspecialchars($t['quote']); ?>"</td>
                            <td>
                                <a href="testimonials.php?delete=<?php echo $t['id']; ?>" class="delete-btn" onclick="return confirm('Delete testimonial?');">Delete</a>
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
