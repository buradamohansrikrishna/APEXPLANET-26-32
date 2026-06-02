<div class="navbar">

    <!-- LEFT SECTION -->

    <div class="navbar-left">

        <!-- LOGO -->

        <div class="logo">

            <i class="fa-solid fa-graduation-cap"></i>

            CampusConnect

        </div>

    </div>

    <!-- RIGHT SECTION -->

    <div class="navbar-right">

        <!-- NAV LINKS -->

        <div class="nav-links">

            <a href="/campusconnect/dashboard.php">

                <i class="fa-solid fa-house"></i>

                Dashboard

            </a>

            <a href="/campusconnect/users/view_users.php">

                <i class="fa-solid fa-users"></i>

                Users

            </a>

            <a href="/campusconnect/profile/profile_view.php">

                <i class="fa-solid fa-user"></i>

                Profile

            </a>

        </div>

        <!-- USER INFO -->

        <div class="top-user">

            <span>

                <i class="fa-solid fa-circle-user"></i>

                <?php echo $_SESSION['name']; ?>

            </span>

        </div>

        <!-- LOGOUT BUTTON -->

        <a href="/campusconnect/logout.php"
        class="logout-btn">

            <i class="fa-solid fa-right-from-bracket"></i>

            Logout

        </a>

    </div>

</div>