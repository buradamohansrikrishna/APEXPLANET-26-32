<?php
include '../auth/auth_check.php';
include '../config/db.php';
include '../includes/header.php';
include '../includes/navbar.php';
include '../includes/sidebar.php';

$id = $_GET['id'];

$sql = "SELECT * FROM users WHERE id='$id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>

<div class="main-content">

    <h2>User Profile</h2>

    <img src="../uploads/profile_pictures/<?php echo $user['profile_pic']; ?>"
    width="120"
    height="120"
    style="border-radius:50%;">

    <p><strong>Name:</strong> <?php echo $user['name']; ?></p>

    <p><strong>Email:</strong> <?php echo $user['email']; ?></p>

    <p><strong>Role:</strong> <?php echo $user['role']; ?></p>

    <a href="view_users.php">Back</a>

</div>

<?php include '../includes/footer.php'; ?>