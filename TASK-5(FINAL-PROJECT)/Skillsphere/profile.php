<?php

// =========================================
// SKILLSPHERE PROFILE PAGE
// profile.php
// =========================================

$pageTitle = "Profile";

require_once 'auth.php';

include 'includes/header.php';

include 'includes/navbar.php';

require_once 'db.php';

// =========================================
// USER DATA
// =========================================

$user_id = intval($_SESSION['user_id']);

// FETCH USER

$user = fetchSingleSecure(
    "SELECT * FROM users WHERE id = ? LIMIT 1",
    [$user_id]
);

// =========================================
// ENROLLED COURSES
// =========================================

$courseQuery = dbQuery(
    "SELECT c.*, cat.category_name, u.full_name AS instructor_name
     FROM enrollments e
     INNER JOIN courses c ON e.course_id = c.id
     LEFT JOIN categories cat ON c.category_id = cat.id
     LEFT JOIN users u ON c.instructor_id = u.id
     WHERE e.user_id = ?
     ORDER BY e.enrolled_at DESC",
    [$user_id]
);

?>

<!-- =========================================
     PROFILE HEADER
========================================= -->

<section class="page-header">

    <div class="container">

        <h1 class="fade">

            Welcome,
            <?php echo $user['full_name']; ?>

        </h1>

        <p class="fade">

            Manage your account,
            courses,
            and learning progress.

        </p>

    </div>

</section>

<!-- =========================================
     PROFILE SECTION
========================================= -->

<section class="about-section">

    <div class="container">

        <div
            class="about-grid"
            style="
                align-items:flex-start;
            "
        >

            <!-- LEFT SIDE -->

            <div class="card reveal">

                <!-- PROFILE IMAGE -->

                <div
                    style="
                        text-align:center;
                        margin-bottom:30px;
                    "
                >

                    <img

                        src="uploads/profiles/<?php

                        echo $user['profile_image']
                        ?: 'default.png';

                        ?>"

                        alt="Profile"

                        style="
                            width:140px;
                            height:140px;
                            border-radius:50%;
                            object-fit:cover;
                            border:5px solid var(--brand-500);
                            margin:auto;
                            margin-bottom:20px;
                        "

                    >

                    <h2>

                        <?php echo $user['full_name']; ?>

                    </h2>

                    <p>

                        <?php echo ucfirst($user['role']); ?>

                    </p>

                </div>

                <!-- USER DETAILS -->

                <div
                    style="
                        display:flex;
                        flex-direction:column;
                        gap:18px;
                    "
                >

                    <div>

                        <strong>

                            📧 Email :

                        </strong>

                        <p>

                            <?php echo $user['email']; ?>

                        </p>

                    </div>

                    <div>

                        <strong>

                            📅 Joined :

                        </strong>

                        <p>

                            <?php

                            echo date(

                                'd M Y',

                                strtotime(
                                    $user['created_at']
                                )

                            );

                            ?>

                        </p>

                    </div>

                    <div>

                        <strong>

                            🎓 Role :

                        </strong>

                        <p>

                            <?php

                            echo ucfirst(
                                $user['role']
                            );

                            ?>

                        </p>

                    </div>

                </div>

                <!-- ACTIONS -->

                <div
                    style="
                        margin-top:35px;
                        display:flex;
                        flex-direction:column;
                        gap:15px;
                    "
                >

                    <a
                        href="settings.php"
                        class="btn btn-block"
                    >

                        Account Settings

                    </a>

                    <a
                        href="wishlist.php"
                        class="btn btn-outline btn-block"
                    >

                        Wishlist

                    </a>

                </div>

            </div>

            <!-- RIGHT SIDE -->

            <div class="reveal">

                <!-- STATS -->

                <div class="dashboard-cards">

                    <div class="dashboard-card">

                        <h3>

                            Enrolled Courses

                        </h3>

                        <h1>

                            <?php

                            echo mysqli_num_rows(
                                $courseQuery
                            );

                            ?>

                        </h1>

                    </div>

                    <div class="dashboard-card">

                        <h3>

                            Certificates

                        </h3>

                        <h1>

                            0

                        </h1>

                    </div>

                </div>

                <!-- COURSES -->

                <div
                    class="card"
                    style="
                        margin-top:30px;
                    "
                >

                    <h2
                        style="
                            margin-bottom:25px;
                        "
                    >

                        My Courses

                    </h2>

                    <?php

                    if(

                        mysqli_num_rows(
                            $courseQuery
                        ) > 0

                    ):

                        while(

                            $course =
                            mysqli_fetch_assoc(
                                $courseQuery
                            )

                        ):

                    ?>

                        <!-- COURSE ITEM -->

                        <div
                            style="
                                display:flex;
                                gap:20px;
                                margin-bottom:25px;
                                padding-bottom:20px;
                                border-bottom:1px solid var(--border-subtle);
                            "
                        >

                            <!-- IMAGE -->

                            <img

                                src="<?php echo htmlspecialchars(courseThumbnailUrl($course['thumbnail'] ?? ($course['slug'] ?? ''))); ?>"

                                alt="<?php

                                echo $course['title'];

                                ?>"

                                style="
                                    width:120px;
                                    height:90px;
                                    border-radius:16px;
                                    object-fit:cover;
                                "

                            >

                            <!-- INFO -->

                            <div>

                                <h3
                                    style="
                                        margin-bottom:10px;
                                    "
                                >

                                    <?php

                                    echo $course['title'];

                                    ?>

                                </h3>

                                <p
                                    style="
                                        margin-bottom:12px;
                                    "
                                >

                                    <?php

                                    echo limitText(

                                        $course['description'],

                                        100

                                    );

                                    ?>

                                </p>

                                <a

                                    href="course-details.php?id=<?php

                                    echo $course['id'];

                                    ?>"

                                    class="btn btn-sm"

                                >

                                    Continue Learning

                                </a>

                            </div>

                        </div>

                    <?php

                        endwhile;

                    else:

                    ?>

                        <p>

                            You have not enrolled
                            in any course yet.

                        </p>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</section>

<?php

include 'includes/footer.php';

?>
