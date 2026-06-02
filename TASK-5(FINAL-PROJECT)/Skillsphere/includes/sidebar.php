<!-- =========================================
     SKILLSPHERE SIDEBAR
     includes/sidebar.php
========================================= -->

<?php

if(session_status() === PHP_SESSION_NONE){

    session_start();

}

?>

<div class="sidebar">

    <!-- =========================================
         LOGO
    ========================================= -->

    <div class="sidebar-logo">

        <a href="dashboard.php">

            Skill<span>Sphere</span>

        </a>

    </div>

    <!-- =========================================
         USER PROFILE
    ========================================= -->

    <div class="sidebar-profile">

        <img

            src="../uploads/profiles/<?php

            echo $_SESSION['profile_image']
            ?? 'default.png';

            ?>"

            alt="Profile"

        >

        <h4>

            <?php

            echo $_SESSION['user_name']
            ?? 'Admin';

            ?>

        </h4>

        <p>

            <?php

            echo ucfirst(

                $_SESSION['user_role']
                ?? 'Admin'

            );

            ?>

        </p>

    </div>

    <!-- =========================================
         MENU
    ========================================= -->

    <ul class="sidebar-menu">

        <li>

            <a

                href="dashboard.php"

                class="<?php echo activePage('dashboard.php'); ?>"

            >

                📊 Dashboard

            </a>

        </li>

        <li>

            <a

                href="analytics.php"

                class="<?php echo activePage('analytics.php'); ?>"

            >

                📈 Analytics

            </a>

        </li>

        <li>

            <a

                href="manage-users.php"

                class="<?php echo activePage('manage-users.php'); ?>"

            >

                👥 Users

            </a>

        </li>

        <li>

            <a

                href="manage-courses.php"

                class="<?php echo activePage('manage-courses.php'); ?>"

            >

                📚 Courses

            </a>

        </li>

        <li>

            <a

                href="add-course.php"

                class="<?php echo activePage('add-course.php'); ?>"

            >

                ➕ Add Course

            </a>

        </li>

        <li>

            <a

                href="../index.php"

            >

                🌐 Website

            </a>

        </li>

        <li>

            <a

                href="../logout.php"

                class="logout-btn"

            >

                🚪 Logout

            </a>

        </li>

    </ul>

</div>