<div class="sidebar">

    <!-- PROJECT TITLE -->

    <h2 class="sidebar-title">
        CampusConnect
    </h2>

    <!-- USER INFO -->

    <div class="user-box">

        <div class="user-avatar">
            👨‍🎓
        </div>

        <h4>
            <?php echo $_SESSION['name']; ?>
        </h4>

        <p>
            <?php echo ucfirst($_SESSION['role']); ?>
        </p>

    </div>

    <!-- MENU -->

    <ul>

        <li>
            <a href="/campusconnect/dashboard.php">
                🏠 Dashboard
            </a>
        </li>

        <li>
            <a href="/campusconnect/users/view_users.php">
                👥 Manage Users
            </a>
        </li>

        <li>
            <a href="/campusconnect/users/add_user.php">
                ➕ Add User
            </a>
        </li>

        <li>
            <a href="/campusconnect/profile/profile_view.php">
                👤 My Profile
            </a>
        </li>

        <li>
            <a href="/campusconnect/logout.php">
                🚪 Logout
            </a>
        </li>

    </ul>

</div>