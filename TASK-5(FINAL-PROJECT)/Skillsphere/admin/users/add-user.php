<?php
require_once '../../auth.php';
requireAdmin();

$message = '';
$errors = [];

if (isset($_POST['add_user'])) {
    $full_name = sanitize($_POST['full_name']);
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $role = sanitize($_POST['role']);
    $status = sanitize($_POST['status']);

    if (empty($full_name)) $errors[] = 'Full name is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($password)) $errors[] = 'Password is required';

    // Check duplicate email
    $duplicate = fetchSingleSecure('SELECT id FROM users WHERE email = ? LIMIT 1', [$email]);
    if ($duplicate) {
        $errors[] = 'Email is already registered';
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $res = dbQuery(
            "INSERT INTO users (full_name, username, email, phone, password, role, status, email_verified) VALUES (?, ?, ?, ?, ?, ?, ?, 1)",
            [$full_name, $username, $email, $phone, $hashed, $role, $status]
        );
        if ($res) {
            $_SESSION['success'] = 'User added successfully';
            header('Location: manage-users.php');
            exit();
        } else {
            $message = 'Database error adding user';
        }
    } else {
        $message = implode('<br>', $errors);
    }
}

$adminTitle = 'Add User';
$adminPage = 'users';
$adminHeading = 'Add user';
$adminSubheading = 'Create a new user account';
$adminIllustration = '../assets/images/admin-users.svg';
$adminHeroTitle = 'Account creation';
$adminHeroText = 'Provision new student, instructor, or admin accounts manually.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<div class="admin-panel reveal">
    <div class="admin-panel__head"><h3>User Details</h3></div>
    <div class="admin-panel__body">
        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" class="admin-form">
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control">
                </div>
            </div>
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control">
                </div>
            </div>

            <div class="grid grid-3">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" class="form-control">
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="blocked">Blocked</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" name="add_user" class="admin-btn admin-btn--primary">Create User</button>
                <a href="manage-users.php" class="admin-btn admin-btn--outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
