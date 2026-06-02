<?php
include 'auth/auth_check.php';
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';
?>

<div class="main-content">

    <h1>
        Welcome,
        <?php echo $_SESSION['name']; ?>
    </h1>

    <div class="dashboard">

        <div class="card">

            <h3>Manage Users</h3>

            <p>
                Add, Edit and Delete Users
            </p>

            <a href="users/view_users.php"
            class="btn">
                Open
            </a>

        </div>

        <div class="card">

            <h3>My Profile</h3>

            <p>
                View Profile Information
            </p>

            <a href="profile/profile_view.php"
            class="btn">
                Open
            </a>

        </div>

        <div class="card">

            <h3>Logout</h3>

            <p>
                Secure Logout From System
            </p>

            <a href="logout.php"
            class="btn">
                Logout
            </a>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>