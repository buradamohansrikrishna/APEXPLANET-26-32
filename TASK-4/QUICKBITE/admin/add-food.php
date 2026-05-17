<?php
include '../config/db.php';

$message = "";

if(isset($_POST['add_food'])){

    $restaurant_id = $_POST['restaurant_id'];

    $food_name = $_POST['food_name'];

    $price = $_POST['price'];

    $category = $_POST['category'];

    $description = $_POST['description'];

    /* =========================
       IMAGE UPLOAD
    ========================= */

    $image = $_FILES['image']['name'];

    $tmp_name = $_FILES['image']['tmp_name'];

    $target =
    "../assets/images/foods/" . $image;

    move_uploaded_file($tmp_name, $target);

    /* =========================
       INSERT QUERY
    ========================= */

    $query = "INSERT INTO foods(

                restaurant_id,
                food_name,
                price,
                category,
                image,
                description

            )

            VALUES(

                '$restaurant_id',
                '$food_name',
                '$price',
                '$category',
                '$image',
                '$description'

            )";

    $result = mysqli_query($conn, $query);

    if($result){

        $message =
        "Food Added Successfully ✅";

    }else{

        $message =
        "Failed To Add Food ❌";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width,
          initial-scale=1.0">

    <title>Add Food</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <style>

        body{

            background:
            linear-gradient(
                to right,
                #fff7ed,
                #ffffff
            );

            font-family:Poppins, sans-serif;
        }

        .page-container{

            width:100%;

            min-height:100vh;

            display:flex;

            justify-content:center;

            align-items:center;

            padding:120px 20px;
        }

        .food-form{

            width:100%;

            max-width:650px;

            background:
            rgba(255,255,255,0.85);

            backdrop-filter:blur(12px);

            border-radius:35px;

            padding:45px;

            box-shadow:
            0 10px 40px rgba(0,0,0,0.08);

            position:relative;

            overflow:hidden;
        }

        .food-form::before{

            content:'';

            position:absolute;

            width:250px;
            height:250px;

            background:
            rgba(255,107,53,0.08);

            border-radius:50%;

            top:-100px;
            right:-100px;

            filter:blur(40px);
        }

        .food-form h2{

            text-align:center;

            font-size:2.5rem;

            margin-bottom:15px;

            color:#111827;

            position:relative;
            z-index:2;
        }

        .food-form p.subtitle{

            text-align:center;

            color:#6b7280;

            margin-bottom:35px;

            position:relative;
            z-index:2;
        }

        .success-message{

            background:#dcfce7;

            color:#166534;

            padding:15px;

            border-radius:14px;

            margin-bottom:25px;

            text-align:center;

            font-weight:600;
        }

        .form-group{

            margin-bottom:22px;

            position:relative;
            z-index:2;
        }

        .form-group label{

            display:block;

            margin-bottom:10px;

            font-weight:600;

            color:#111827;
        }

        .form-group input,
        .form-group textarea,
        .form-group select{

            width:100%;

            padding:16px 18px;

            border:2px solid #f1f5f9;

            border-radius:16px;

            background:white;

            font-size:1rem;

            transition:0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus{

            border-color:#ff6b35;

            outline:none;
        }

        .form-group textarea{

            resize:none;
        }

        .file-upload{

            background:#fff7ed;

            border:2px dashed #ff6b35;

            padding:25px;

            border-radius:20px;

            text-align:center;

            cursor:pointer;
        }

        .file-upload input{

            border:none;

            background:none;
        }

        .preview-image{

            width:100%;

            max-height:250px;

            object-fit:cover;

            border-radius:20px;

            margin-top:20px;

            display:none;
        }

        .submit-btn{

            width:100%;

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            color:white;

            border:none;

            padding:18px;

            border-radius:16px;

            font-size:1rem;

            font-weight:700;

            cursor:pointer;

            transition:0.3s;

            box-shadow:
            0 10px 25px rgba(255,107,53,0.25);
        }

        .submit-btn:hover{

            transform:
            translateY(-4px);
        }

        @media(max-width:768px){

            .food-form{

                padding:35px 25px;
            }

            .food-form h2{

                font-size:2rem;
            }
        }

    </style>

</head>

<body>

<div class="page-container">

    <form method="POST"
          enctype="multipart/form-data"
          class="food-form">

        <h2>

            Add Food Item 🍔

        </h2>

        <p class="subtitle">

            Add delicious food items
            to your restaurant menu.

        </p>

<?php
if($message != ""){
?>

        <div class="success-message">

            <?php echo $message; ?>

        </div>

<?php } ?>

        <div class="form-group">

            <label>
                Restaurant ID
            </label>

            <input
                type="number"
                name="restaurant_id"
                placeholder="Enter Restaurant ID"
                required
            >

        </div>

        <div class="form-group">

            <label>
                Food Name
            </label>

            <input
                type="text"
                name="food_name"
                placeholder="Enter Food Name"
                required
            >

        </div>

        <div class="form-group">

            <label>
                Price
            </label>

            <input
                type="number"
                step="0.01"
                name="price"
                placeholder="Enter Price"
                required
            >

        </div>

        <div class="form-group">

            <label>
                Category
            </label>

            <input
                type="text"
                name="category"
                placeholder="Burger / Pizza / Biryani"
                required
            >

        </div>

        <div class="form-group">

            <label>
                Description
            </label>

            <textarea
                name="description"
                rows="5"
                placeholder="Enter Food Description"
                required
            ></textarea>

        </div>

        <div class="form-group">

            <label>
                Upload Food Image
            </label>

            <div class="file-upload">

                <input
                    type="file"
                    name="image"
                    id="imageInput"
                    accept=".jpg,.jpeg,.png,.webp"
                    required
                >

                <p>

                    Supported:
                    JPG, PNG, WEBP

                </p>

            </div>

            <img
                id="preview"
                class="preview-image"
            >

        </div>

        <button
            type="submit"
            name="add_food"
            class="submit-btn"
        >

            Add Food Item

        </button>

    </form>

</div>

<script>

/* =========================
   IMAGE PREVIEW
========================= */

const imageInput =
document.getElementById('imageInput');

const preview =
document.getElementById('preview');

imageInput.addEventListener('change', function(){

    const file =
    this.files[0];

    if(file){

        const reader =
        new FileReader();

        reader.onload = function(e){

            preview.src =
            e.target.result;

            preview.style.display =
            'block';
        }

        reader.readAsDataURL(file);
    }
});

</script>

</body>
</html>