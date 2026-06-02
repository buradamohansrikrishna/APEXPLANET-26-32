<?php
require_once '../../auth.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = fetchSingleSecure('SELECT * FROM users WHERE id = ? LIMIT 1', [$id]);

if (!$user) {
    $_SESSION['error'] = 'User not found';
    header('Location: manage-users.php');
    exit();
}

$message = '';
$errors = [];

if (isset($_POST['edit_user'])) {
    $full_name = sanitize($_POST['full_name']);
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $role = sanitize($_POST['role']);
    $status = sanitize($_POST['status']);
    $password = $_POST['password'];

    if (empty($full_name)) $errors[] = 'Full name is required';
    if (empty($email)) $errors[] = 'Email is required';

    if (empty($errors)) {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $res = dbQuery(
                "UPDATE users SET full_name = ?, username = ?, email = ?, phone = ?, password = ?, role = ?, status = ? WHERE id = ?",
                [$full_name, $username, $email, $phone, $hashed, $role, $status, $id]
            );
        } else {
            $res = dbQuery(
                "UPDATE users SET full_name = ?, username = ?, email = ?, phone = ?, role = ?, status = ? WHERE id = ?",
                [$full_name, $username, $email, $phone, $role, $status, $id]
            );
        }
        if ($res) {
            $_SESSION['success'] = 'User updated successfully';
            header('Location: manage-users.php');
            exit();
        } else {
            $message = 'Database error updating user';
        }
    } else {
        $message = implode('<br>', $errors);
    }
}

$adminTitle = 'Edit User';
$adminPage = 'users';
$adminHeading = 'Edit user';
$adminSubheading = 'Modify account settings';
$adminIllustration = '../assets/images/admin-users.svg';
$adminHeroTitle = 'Account operations';
$adminHeroText = 'Update profile fields, passwords, and authorization levels.';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<div class="admin-panel reveal">
    <div class="admin-panel__head"><h3>Edit details for <?php echo htmlspecialchars($user['full_name']); ?></h3></div>
    <div class="admin-panel__body">
        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" class="admin-form">
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
            </div>

            <div class="grid grid-3">
                <div class="form-group">
                    <label for="password">Password <small>(Leave blank to keep current)</small></label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" class="form-control">
                        <option value="student" <?php echo $user['role'] === 'student' ? 'selected' : ''; ?>>Student</option>
                        <option value="instructor" <?php echo $user['role'] === 'instructor' ? 'selected' : ''; ?>>Instructor</option>
                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="pending" <?php echo $user['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="blocked" <?php echo $user['status'] === 'blocked' ? 'selected' : ''; ?>>Blocked</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" name="edit_user" class="admin-btn admin-btn--primary">Save Changes</button>
                <a href="manage-users.php" class="admin-btn admin-btn--outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
