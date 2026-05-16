<?php

include '../config/db.php';

include '../config/session.php';

include '../auth/auth_check.php';

include '../includes/header.php';

include '../includes/navbar.php';

include '../includes/sidebar.php';

/* =========================
   FETCH USERS
========================= */

$sql = "SELECT * FROM users ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

?>

<!-- MAIN CONTENT -->

<div class="main-content">

    <!-- PAGE HEADER -->

    <div class="page-header">

        <div>

            <h1>
                <i class="fa-solid fa-users"></i>
                Manage Users
            </h1>

            <p>
                View, edit and manage all registered users
            </p>

        </div>

        <a href="add_user.php" class="btn">

            <i class="fa-solid fa-user-plus"></i>

            Add User

        </a>

    </div>

    <!-- USER TABLE CARD -->

    <div class="table-card">

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Profile</th>

                    <th>Name</th>

                    <th>Email</th>

                    <th>Role</th>

                    <th>Joined</th>

                    <th>Actions</th>

                </tr>

            </thead>

            <tbody>

                <?php while($row = mysqli_fetch_assoc($result)) { ?>

                <tr>

                    <td>
                        #<?php echo $row['id']; ?>
                    </td>

                    <td>

                        <img
                        src="../uploads/profile_pictures/<?php echo $row['profile_pic']; ?>"

                        class="table-profile-img">

                    </td>

                    <td>

                        <?php echo $row['name']; ?>

                    </td>

                    <td>

                        <?php echo $row['email']; ?>

                    </td>

                    <td>

                        <span class="role-badge">

                            <?php echo ucfirst($row['role']); ?>

                        </span>

                    </td>

                    <td>

                        <?php
                        echo date(
                            "d M Y",
                            strtotime($row['created_at'])
                        );
                        ?>

                    </td>

                    <td>

                        <div class="action-buttons">

                            <a
                            href="edit_user.php?id=<?php echo $row['id']; ?>"

                            class="edit-btn">

                                <i class="fa-solid fa-pen"></i>

                            </a>

                            <a
                            href="delete_user.php?id=<?php echo $row['id']; ?>"

                            class="delete-btn"

                            onclick="return confirm('Delete this user?')">

                                <i class="fa-solid fa-trash"></i>

                            </a>

                        </div>

                    </td>

                </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</div>

<?php include '../includes/footer.php'; ?>