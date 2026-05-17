<?php
include '../config/db.php';

$query = "SELECT * FROM foods ORDER BY id DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Foods</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <style>

        body{

            background:#f8fafc;

            font-family:Poppins, sans-serif;
        }

        .foods-container{

            width:95%;

            margin:100px auto;
        }

        .foods-header{

            display:flex;

            justify-content:space-between;

            align-items:center;

            margin-bottom:40px;

            flex-wrap:wrap;

            gap:20px;
        }

        .foods-header h1{

            font-size:3rem;

            color:#111827;
        }

        .foods-header span{

            color:#ff6b35;
        }

        .add-btn{

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            color:white;

            padding:14px 26px;

            text-decoration:none;

            border-radius:14px;

            font-weight:600;

            transition:0.3s;

            box-shadow:
            0 10px 25px rgba(255,107,53,0.25);
        }

        .add-btn:hover{

            transform:
            translateY(-5px);
        }

        .table-container{

            overflow-x:auto;

            background:
            rgba(255,255,255,0.8);

            backdrop-filter:blur(10px);

            border-radius:30px;

            box-shadow:
            0 10px 30px rgba(0,0,0,0.08);

            padding:25px;
        }

        table{

            width:100%;

            border-collapse:collapse;
        }

        table th{

            background:
            linear-gradient(
                135deg,
                #ff6b35,
                #ff9f1c
            );

            color:white;

            padding:18px;

            text-align:center;

            font-size:1rem;
        }

        table th:first-child{

            border-top-left-radius:16px;
        }

        table th:last-child{

            border-top-right-radius:16px;
        }

        table td{

            padding:18px;

            text-align:center;

            border-bottom:
            1px solid #e5e7eb;

            vertical-align:middle;
        }

        table tr{

            transition:0.3s;
        }

        table tr:hover{

            background:#fff7ed;

            transform:scale(1.01);
        }

        .food-img{

            width:80px;

            height:80px;

            object-fit:cover;

            border-radius:16px;

            box-shadow:
            0 8px 20px rgba(0,0,0,0.08);

            transition:0.4s;
        }

        .food-img:hover{

            transform:scale(1.08);
        }

        .food-name{

            font-weight:700;

            color:#111827;
        }

        .food-category{

            background:#fff1e8;

            color:#ff6b35;

            padding:8px 14px;

            border-radius:30px;

            display:inline-block;

            font-size:0.9rem;

            font-weight:600;
        }

        .food-price{

            font-weight:700;

            color:#ff6b35;

            font-size:1.1rem;
        }

        .description{

            color:#6b7280;

            line-height:1.7;

            max-width:300px;

            margin:auto;
        }

        .action-buttons{

            display:flex;

            justify-content:center;

            gap:10px;

            flex-wrap:wrap;
        }

        .edit-btn{

            background:#3b82f6;

            color:white;

            padding:10px 18px;

            border-radius:12px;

            text-decoration:none;

            font-size:0.9rem;

            transition:0.3s;
        }

        .edit-btn:hover{

            transform:translateY(-3px);
        }

        .delete-btn{

            background:#ef4444;

            color:white;

            padding:10px 18px;

            border-radius:12px;

            text-decoration:none;

            font-size:0.9rem;

            transition:0.3s;
        }

        .delete-btn:hover{

            transform:translateY(-3px);
        }

        .empty-foods{

            text-align:center;

            padding:80px 20px;
        }

        .empty-foods img{

            width:180px;

            margin-bottom:25px;
        }

        .empty-foods h2{

            color:#111827;

            margin-bottom:12px;
        }

        .empty-foods p{

            color:#6b7280;
        }

        @media(max-width:768px){

            .foods-header{

                flex-direction:column;

                align-items:flex-start;
            }

            .foods-header h1{

                font-size:2.2rem;
            }

            table{

                min-width:900px;
            }
        }

    </style>

</head>

<body>

<div class="foods-container">

    <div class="foods-header">

        <h1>

            Manage <span>Foods</span>

        </h1>

        <a href="add-food.php"
           class="add-btn">

           + Add New Food

        </a>

    </div>

    <div class="table-container">

<?php if(mysqli_num_rows($result) > 0){ ?>

        <table>

            <tr>

                <th>ID</th>
                <th>Image</th>
                <th>Food Name</th>
                <th>Price</th>
                <th>Category</th>
                <th>Description</th>
                <th>Actions</th>

            </tr>

            <?php while($food = mysqli_fetch_assoc($result)) { ?>

            <tr>

                <td>

                    <?php echo $food['id']; ?>

                </td>

                <td>

                    <img
                    src="../assets/images/foods/<?php echo $food['image']; ?>"
                    class="food-img"
                    alt="<?php echo $food['food_name']; ?>"
                    >

                </td>

                <td class="food-name">

                    <?php echo $food['food_name']; ?>

                </td>

                <td class="food-price">

                    ₹ <?php echo $food['price']; ?>

                </td>

                <td>

                    <span class="food-category">

                        <?php echo $food['category']; ?>

                    </span>

                </td>

                <td class="description">

                    <?php echo $food['description']; ?>

                </td>

                <td>

                    <div class="action-buttons">

                        <a href="edit-food.php?id=<?php echo $food['id']; ?>"
                           class="edit-btn">

                           Edit

                        </a>

                        <a href="delete-food.php?id=<?php echo $food['id']; ?>"
                           class="delete-btn"
                           onclick="return confirm('Delete this food item?')">

                           Delete

                        </a>

                    </div>

                </td>

            </tr>

            <?php } ?>

        </table>

<?php } else { ?>

        <div class="empty-foods">

            <img
            src="../assets/images/icons/empty-cart.png"
            alt="No Foods"
            >

            <h2>

                No Foods Added

            </h2>

            <p>

                Start adding delicious food items
                to your restaurant menu.

            </p>

        </div>

<?php } ?>

    </div>

</div>

</body>
</html>