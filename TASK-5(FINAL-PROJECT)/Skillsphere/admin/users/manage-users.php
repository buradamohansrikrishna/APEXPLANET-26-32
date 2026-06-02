<?php
require_once '../../auth.php';
requireAdmin();

$userQuery = dbQuery('SELECT * FROM users ORDER BY created_at DESC');

$adminTitle = 'Manage Users';
$adminPage = 'users';
$adminHeading = 'Manage users';
$adminSubheading = 'Students, instructors, and administrators';
$adminIllustration = '../assets/images/admin-users.svg';
$adminHeroTitle = 'Community management';
$adminHeroText = 'Review accounts, roles, verification status, and access across the platform.';
$adminTopbarActions = '<a href="add-user.php" class="admin-btn admin-btn--primary"><i class="fa-solid fa-plus"></i> Add user</a>';

include '../includes/head.php';
include '../includes/sidebar.php';
?>
<div class="admin-main">
<?php include '../includes/topbar.php'; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="admin-panel reveal">
    <div class="admin-panel__head">
        <h3>All users</h3>
        <span class="admin-pill"><?php echo $userQuery ? mysqli_num_rows($userQuery) : 0; ?> accounts</span>
    </div>
    <div class="admin-table-wrap">
        <?php if ($userQuery && mysqli_num_rows($userQuery) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Verified</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($userQuery)): ?>
                <tr>
                    <td><?php echo (int) $user['id']; ?></td>
                    <td>
                        <div class="admin-profile-cell">
                            <img src="../../uploads/profiles/<?php echo htmlspecialchars($user['profile_image'] ?? 'default.png'); ?>" alt="" onerror="this.src='../../uploads/profiles/default.png'">
                            <div>
                                <strong><?php echo htmlspecialchars($user['full_name']); ?></strong><br>
                                <small>@<?php echo htmlspecialchars($user['username'] ?? 'user'); ?></small>
                            </div>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><span class="badge <?php echo strtolower($user['role']); ?>"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span></td>
                    <td><span class="status <?php echo strtolower($user['status'] ?? 'active'); ?>"><?php echo htmlspecialchars(ucfirst($user['status'] ?? 'active')); ?></span></td>
                    <td><?php echo !empty($user['email_verified']) ? '✓ Verified' : 'Pending'; ?></td>
                    <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="edit-user.php?id=<?php echo (int) $user['id']; ?>" class="edit-btn">Edit</a>
                            <?php if ($user['status'] === 'blocked'): ?>
                                <a href="ban-user.php?id=<?php echo (int) $user['id']; ?>&action=unban" class="edit-btn" style="background:#10b981;">Unban</a>
                            <?php else: ?>
                                <a href="ban-user.php?id=<?php echo (int) $user['id']; ?>&action=ban" class="delete-btn" onclick="return confirm('Ban this user?');">Ban</a>
                            <?php endif; ?>
                            <a href="delete-user.php?id=<?php echo (int) $user['id']; ?>" class="delete-btn" onclick="return confirm('Delete this user permanently?');">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty">No users found</div>
        <?php endif; ?>
    </div>
</div>

</div>
<?php include '../includes/footer.php'; ?>
