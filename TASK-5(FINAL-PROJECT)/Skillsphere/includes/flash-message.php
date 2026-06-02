<?php

// =========================================
// SKILLSPHERE FLASH MESSAGE
// includes/flash-message.php
// =========================================

// =========================================
// CHECK FLASH MESSAGE
// =========================================

if(

    isset($_SESSION['flash_message']) &&

    !empty($_SESSION['flash_message'])

):

    $type =

    $_SESSION['flash_type']
    ?? 'success';

?>

    <div

        class="flash-message flash-<?php echo $type; ?>"

        id="flashMessage"

    >

        <div class="flash-content">

            <!-- ICON -->

            <div class="flash-icon">

                <?php if($type === 'success'): ?>

                    ✅

                <?php elseif($type === 'error'): ?>

                    ❌

                <?php elseif($type === 'warning'): ?>

                    ⚠️

                <?php else: ?>

                    ℹ️

                <?php endif; ?>

            </div>

            <!-- MESSAGE -->

            <div class="flash-text">

                <?php

                echo htmlspecialchars(

                    $_SESSION['flash_message']

                );

                ?>

            </div>

        </div>

        <!-- CLOSE BUTTON -->

        <button

            class="flash-close"

            onclick="closeFlashMessage()"

        >

            ×

        </button>

    </div>

    <!-- FLASH MESSAGE CSS -->

    <style>

        .flash-message{

            width:100%;

            display:flex;

            justify-content:space-between;

            align-items:center;

            padding:18px 22px;

            border-radius:16px;

            margin-bottom:20px;

            animation:slideDown 0.5s ease;

            font-family:'Poppins',sans-serif;

        }

        .flash-content{

            display:flex;

            align-items:center;

            gap:15px;

        }

        .flash-icon{

            font-size:22px;

        }

        .flash-text{

            font-size:15px;

            font-weight:500;

        }

        .flash-close{

            background:none;

            border:none;

            color:inherit;

            font-size:24px;

            cursor:pointer;

            transition:0.3s;

        }

        .flash-close:hover{

            transform:scale(1.2);

        }

        /* SUCCESS */

        .flash-success{

            background:rgba(34,197,94,0.15);

            border:1px solid rgba(34,197,94,0.3);

            color:#4ade80;

        }

        /* ERROR */

        .flash-error{

            background:rgba(239,68,68,0.15);

            border:1px solid rgba(239,68,68,0.3);

            color:#f87171;

        }

        /* WARNING */

        .flash-warning{

            background:rgba(245,158,11,0.15);

            border:1px solid rgba(245,158,11,0.3);

            color:#facc15;

        }

        /* INFO */

        .flash-info{

            background:rgba(59,130,246,0.15);

            border:1px solid rgba(59,130,246,0.3);

            color:#60a5fa;

        }

        /* ANIMATION */

        @keyframes slideDown{

            from{

                opacity:0;

                transform:translateY(-20px);

            }

            to{

                opacity:1;

                transform:translateY(0);

            }

        }

    </style>

    <!-- FLASH MESSAGE SCRIPT -->

    <script>

        // AUTO HIDE

        setTimeout(()=>{

            const flash =

            document.getElementById(
                'flashMessage'
            );

            if(flash){

                flash.style.opacity =
                '0';

                flash.style.transform =
                'translateY(-20px)';

                flash.style.transition =
                '0.4s ease';

                setTimeout(()=>{

                    flash.remove();

                },400);

            }

        },4000);

        // CLOSE BUTTON

        function closeFlashMessage(){

            const flash =

            document.getElementById(
                'flashMessage'
            );

            if(flash){

                flash.remove();

            }

        }

    </script>

<?php

// =========================================
// CLEAR SESSION MESSAGE
// =========================================

unset($_SESSION['flash_message']);

unset($_SESSION['flash_type']);

endif;

?>