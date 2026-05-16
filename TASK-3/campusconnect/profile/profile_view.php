<?php

include '../config/db.php';

include '../config/session.php';

include '../auth/auth_check.php';

include '../includes/header.php';

include '../includes/navbar.php';

include '../includes/sidebar.php';

/* =========================
   GET USER DETAILS
========================= */

$id = $_SESSION['user_id'];

$stmt = $conn->prepare(

    "SELECT * FROM users WHERE id = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

?>

<!-- MAIN CONTENT -->

<div class="main-content">

    <!-- PROFILE CARD -->

    <div class="profile-card">

        <!-- PROFILE IMAGE -->

        <div class="profile-image-section">

            <img
            src="../uploads/profile_pictures/<?php echo $user['profile_pic']; ?>"

            alt="Profile Picture"

            class="profile-img">

        </div>

        <!-- PROFILE INFO -->

        <div class="profile-info">

            <h2>

                <?php echo $user['name']; ?>

            </h2>

            <p>

                <i class="fa-solid fa-envelope"></i>

                <?php echo $user['email']; ?>

            </p>

            <p>

                <i class="fa-solid fa-user-shield"></i>

                Role :
                <?php echo ucfirst($user['role']); ?>

            </p>

            <p>

                <i class="fa-solid fa-calendar"></i>

                Joined :
                <?php echo date(
                    "d M Y",
                    strtotime($user['created_at'])
                ); ?>

            </p>

            <!-- BUTTONS -->

            <div class="profile-buttons">

                <a href="upload_photo.php"
                class="btn">

                    <i class="fa-solid fa-upload"></i>

                    Upload Photo

                </a>

                <a href="../dashboard.php"
                class="btn">

                    <i class="fa-solid fa-house"></i>

                    Dashboard

                </a>

            </div>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>