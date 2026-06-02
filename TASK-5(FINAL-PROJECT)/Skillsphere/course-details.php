<?php

// =========================================
// SKILLSPHERE COURSE DETAILS
// course-details.php
// =========================================

// =========================================
// REQUIRED FILES
// =========================================

require_once 'db.php';

require_once 'functions.php';
require_once 'helpers.php';

// =========================================
// VALIDATE COURSE ID
// =========================================

if(!isset($_GET['id'])){

    header("Location: courses.php");

    exit();

}

$id = intval($_GET['id']);

// =========================================
// FETCH COURSE
// =========================================

$query = dbQuery(
    "SELECT c.*, cat.category_name, u.full_name AS instructor_name
     FROM courses c
     LEFT JOIN categories cat ON c.category_id = cat.id
     LEFT JOIN users u ON c.instructor_id = u.id
     WHERE c.id = ?
     LIMIT 1",
     [$id]
);

$course = $query ? mysqli_fetch_assoc($query) : null;

if(!$course){

    header("Location: courses.php");

    exit();

}

$pageTitle = "Course Details";

include 'includes/header.php';

include 'includes/navbar.php';

// =========================================
// ENROLLMENT CHECK
// =========================================

$isEnrolled = false;

if(isset($_SESSION['user_id'])){

    $user_id =
    intval($_SESSION['user_id']);

    $enrollCheck =
    mysqli_query(

        $conn,

        "SELECT id
         FROM enrollments
         WHERE user_id = '$user_id'
         AND course_id = '$id'
         LIMIT 1"

    );

    if($enrollCheck){

        $isEnrolled =
        mysqli_num_rows($enrollCheck) > 0;

    }

}

?>

<!-- =========================================
     COURSE HERO
========================================= -->

<section class="page-header">

    <div class="container">

        <span class="badge badge-primary">

            <?php

            echo htmlspecialchars(
                $course['category_name']
            );

            ?>

        </span>

        <h1 class="fade">

            <?php

            echo htmlspecialchars(
                $course['title']
            );

            ?>

        </h1>

        <p class="fade">

            Learn modern skills with practical
            industry-focused training.

        </p>

    </div>

</section>

<!-- =========================================
     COURSE DETAILS
========================================= -->

<section class="courses-section">

    <div class="container">

        <div
            class="about-grid"
            style="
                align-items:flex-start;
            "
        >

            <!-- LEFT SIDE -->

            <div class="reveal">

                <!-- THUMBNAIL -->

                <img

                    src="<?php echo htmlspecialchars(courseThumbnailUrl($course['thumbnail'] ?? ($course['slug'] ?? ''))); ?>"

                    alt="<?php

                    echo htmlspecialchars(
                        $course['title']
                    );

                    ?>"

                    onerror="this.src='uploads/thumbnails/default-course.jpg';"

                    style="
                        width:100%;
                        border-radius:24px;
                        margin-bottom:30px;
                        box-shadow:
                        0 20px 50px rgba(0,0,0,0.35);
                    "

                >

                <!-- DESCRIPTION -->

                <div class="card">

                    <h2
                        style="
                            margin-bottom:20px;
                        "
                    >

                        Course Description

                    </h2>

                    <p
                        style="
                            line-height:1.9;
                        "
                    >

                        <?php

                        echo nl2br(

                            htmlspecialchars(
                                $course['description']
                            )

                        );

                        ?>

                    </p>

                </div>

            </div>

            <!-- RIGHT SIDE -->

            <div class="reveal">

                <div class="card">

                    <!-- COURSE INFO -->

                    <h2
                        style="
                            margin-bottom:25px;
                        "
                    >

                        Course Information

                    </h2>

                    <!-- PRICE -->

                    <div
                        style="
                            margin-bottom:20px;
                        "
                    >

                        <strong>

                            💰 Price :

                        </strong>

                        <span
                            style="
                                color:#4ade80;
                                font-size:24px;
                                font-weight:700;
                            "
                        >

                            ₹<?php

                            echo htmlspecialchars(
                                $course['price']
                            );

                            ?>

                        </span>

                    </div>

                    <!-- INSTRUCTOR -->

                    <div
                        style="
                            margin-bottom:20px;
                        "
                    >

                        <strong>

                            👨‍🏫 Instructor :

                        </strong>

                        <?php

                        echo htmlspecialchars(
                            $course['instructor_name']
                        );

                        ?>

                    </div>

                    <!-- LEVEL -->

                    <div
                        style="
                            margin-bottom:20px;
                        "
                    >

                        <strong>

                            ⭐ Level :

                        </strong>

                        <?php

                        echo htmlspecialchars(
                            $course['level']
                        );

                        ?>

                    </div>

                    <!-- DURATION -->

                    <div
                        style="
                            margin-bottom:20px;
                        "
                    >

                        <strong>

                            ⏳ Duration :

                        </strong>

                        <?php

                        echo htmlspecialchars(
                            $course['duration']
                        );

                        ?>

                    </div>

                    <!-- CREATED -->

                    <div
                        style="
                            margin-bottom:30px;
                        "
                    >

                        <strong>

                            📅 Published :

                        </strong>

                        <?php

                        echo date(

                            'd M Y',

                            strtotime(
                                $course['created_at']
                            )

                        );

                        ?>

                    </div>

                    <!-- ENROLL BUTTON -->

                    <?php if($isEnrolled): ?>

                        <button
                            class="btn btn-success btn-block"
                            disabled
                        >

                            Already Enrolled

                        </button>

                    <?php else: ?>

                        <button

                            class="btn btn-block"

                            onclick="
                                enrollCourse(
                                    <?php echo $course['id']; ?>
                                )
                            "

                        >

                            Enroll Now

                        </button>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- =========================================
     ENROLLMENT AJAX
========================================= -->

<script>

async function enrollCourse(courseId){

    try{

        const response =
        await fetch(

            'ajax/enroll.php',

            {

                method:'POST',

                headers:{

                    'Content-Type':
                    'application/x-www-form-urlencoded'

                },

                body:
                `course_id=${courseId}`

            }

        );

        const data =
        await response.json();

        alert(data.message);

        if(data.status === 'success'){

            location.reload();

        }

    }

    catch(error){

        console.error(error);

        alert('Enrollment failed');

    }

}

</script>

<?php

include 'includes/footer.php';

?>
