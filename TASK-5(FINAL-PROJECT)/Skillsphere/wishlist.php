<?php

// =========================================
// SKILLSPHERE WISHLIST PAGE
// wishlist.php
// =========================================

require_once 'auth.php';

require_once 'db.php';

// =========================================
// USER ID
// =========================================

$user_id =
$_SESSION['user_id'];

// =========================================
// REMOVE FROM WISHLIST
// =========================================

if(isset($_GET['remove'])){

    $course_id =
    intval($_GET['remove']);

    dbQuery(
        "DELETE FROM wishlist WHERE user_id = ? AND course_id = ?",
        [$user_id, $course_id]
    );

    header(
        "Location: wishlist.php"
    );

    exit();

}

$pageTitle = "Wishlist";

include 'includes/header.php';

include 'includes/navbar.php';

// =========================================
// FETCH WISHLIST COURSES
// =========================================

$query = dbQuery(
    "SELECT c.*, cat.category_name, u.full_name AS instructor_name
     FROM wishlist w
     INNER JOIN courses c ON w.course_id = c.id
     LEFT JOIN categories cat ON c.category_id = cat.id
     LEFT JOIN users u ON c.instructor_id = u.id
     WHERE w.user_id = ?
     ORDER BY w.id DESC",
    [$user_id]
);

?>

<!-- =========================================
     PAGE HEADER
========================================= -->

<section class="page-header">

    <div class="container">

        <h1 class="fade">

            My Wishlist

        </h1>

        <p class="fade">

            Save your favorite courses
            and access them anytime.

        </p>

    </div>

</section>

<!-- =========================================
     WISHLIST SECTION
========================================= -->

<section class="courses-section">

    <div class="container">

        <?php if(mysqli_num_rows($query) > 0): ?>

            <!-- GRID -->

            <div class="course-grid">

                <?php

                while(

                    $course =
                    mysqli_fetch_assoc($query)

                ):

                ?>

                    <!-- COURSE CARD -->

                    <div class="course-card reveal">

                        <!-- IMAGE -->

                        <div class="course-image">

                            <img

                                src="<?php echo htmlspecialchars(courseThumbnailUrl($course['thumbnail'] ?? ($course['slug'] ?? ''))); ?>"

                                alt="<?php

                                echo $course['title'];

                                ?>"

                            >

                        </div>

                        <!-- CONTENT -->

                        <div class="course-content">

                            <!-- CATEGORY -->

                            <span class="course-category">

                                <?php

                                echo $course['category_name'];

                                ?>

                            </span>

                            <!-- TITLE -->

                            <h2 class="course-title">

                                <?php

                                echo $course['title'];

                                ?>

                            </h2>

                            <!-- DESCRIPTION -->

                            <p class="course-description">

                                <?php

                                echo limitText(

                                    $course['description'],

                                    120

                                );

                                ?>

                            </p>

                            <!-- META -->

                            <div
                                style="
                                    margin-bottom:20px;
                                    display:flex;
                                    flex-direction:column;
                                    gap:10px;
                                    color:#cbd5e1;
                                "
                            >

                                <span>

                                    👨‍🏫 Instructor :
                                    <?php

                                    echo $course['instructor_name'];

                                    ?>

                                </span>

                                <span>

                                    ⭐ Level :
                                    <?php

                                    echo $course['level'];

                                    ?>

                                </span>

                                <span>

                                    ⏳ Duration :
                                    <?php

                                    echo $course['duration'];

                                    ?>

                                </span>

                            </div>

                            <!-- FOOTER -->

                            <div class="course-footer">

                                <h3 class="course-price">

                                    ₹<?php

                                    echo $course['price'];

                                    ?>

                                </h3>

                                <div
                                    style="
                                        display:flex;
                                        gap:10px;
                                    "
                                >

                                    <!-- VIEW -->

                                    <a

                                        href="course-details.php?id=<?php

                                        echo $course['id'];

                                        ?>"

                                        class="btn btn-sm"

                                    >

                                        View

                                    </a>

                                    <!-- REMOVE -->

                                    <a

                                        href="wishlist.php?remove=<?php

                                        echo $course['id'];

                                        ?>"

                                        class="btn btn-danger btn-sm"

                                        onclick="
                                            return confirm(
                                                'Remove from wishlist?'
                                            );
                                        "

                                    >

                                        Remove

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <!-- EMPTY -->

            <div
                class="card text-center reveal"
                style="
                    padding:70px 40px;
                "
            >

                <h2
                    style="
                        margin-bottom:20px;
                    "
                >

                    Your Wishlist Is Empty

                </h2>

                <p
                    style="
                        margin-bottom:30px;
                    "
                >

                    Save courses to your wishlist
                    and access them later.

                </p>

                <a
                    href="courses.php"
                    class="btn"
                >

                    Explore Courses

                </a>

            </div>

        <?php endif; ?>

    </div>

</section>

<?php

include 'includes/footer.php';

?>
