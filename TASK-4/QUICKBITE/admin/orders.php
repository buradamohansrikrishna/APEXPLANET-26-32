<?php
include '../config/db.php';

/* FETCH ORDERS */

$query = "SELECT orders.*, users.name, foods.food_name
          FROM orders
          JOIN users ON orders.user_id = users.id
          JOIN foods ON orders.food_id = foods.id
          ORDER BY orders.id DESC";

$result = mysqli_query($conn, $query);

/* ORDER COUNT */

$totalOrders = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Order Management</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <style>

        body{
            background:#f8fafc;
            font-family:Poppins;
        }

        .orders-page{
            width:95%;
            margin:100px auto;
        }

        .orders-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:20px;
            margin-bottom:35px;
        }

        .orders-header h1{
            font-size:3rem;
            color:#111827;
        }

        .orders-header span{
            color:#ff6b35;
        }

        .orders-stats{
            display:flex;
            gap:20px;
            flex-wrap:wrap;
            margin-bottom:30px;
        }

        .stats-card{
            background:white;
            padding:25px 35px;
            border-radius:20px;
            box-shadow:0 10px 30px rgba(0,0,0,0.08);
        }

        .stats-card h3{
            color:#6b7280;
            margin-bottom:10px;
        }

        .stats-card p{
            font-size:2rem;
            font-weight:700;
            color:#111827;
        }

        .search-box{
            margin-bottom:30px;
        }

        .search-box input{
            width:100%;
            padding:16px;
            border:none;
            border-radius:14px;
            background:white;
            box-shadow:0 5px 20px rgba(0,0,0,0.05);
            font-size:1rem;
        }

        .table-container{
            overflow-x:auto;
            background:white;
            border-radius:25px;
            padding:20px;
            box-shadow:0 10px 30px rgba(0,0,0,0.08);
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        table th{
            background:#ff6b35;
            color:white;
            padding:18px;
            text-align:center;
        }

        table td{
            padding:18px;
            text-align:center;
            border-bottom:1px solid #e5e7eb;
        }

        table tr:hover{
            background:#fff7ed;
        }

        .status{
            padding:10px 18px;
            border-radius:30px;
            font-size:0.9rem;
            font-weight:600;
            display:inline-block;
        }

        .pending{
            background:#fef3c7;
            color:#92400e;
        }

        .completed{
            background:#dcfce7;
            color:#166534;
        }

        .cancelled{
            background:#fee2e2;
            color:#991b1b;
        }

        @media(max-width:768px){

            .orders-header h1{
                font-size:2.2rem;
            }
        }

    </style>

</head>

<body>

<div class="orders-page">

    <div class="orders-header">

        <h1>
            Order <span>Management</span>
        </h1>

    </div>

    <div class="orders-stats">

        <div class="stats-card">

            <h3>Total Orders</h3>

            <p><?php echo $totalOrders; ?></p>

        </div>

    </div>

    <div class="search-box">

        <input
            type="text"
            id="searchInput"
            placeholder="Search customer or food..."
        >

    </div>

    <div class="table-container">

        <table id="ordersTable">

            <tr>

                <th>Order ID</th>
                <th>Customer</th>
                <th>Food Item</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>

            </tr>

            <?php while($row = mysqli_fetch_assoc($result)) { ?>

            <tr>

                <td>
                    #<?php echo $row['id']; ?>
                </td>

                <td>
                    <?php echo $row['name']; ?>
                </td>

                <td>
                    <?php echo $row['food_name']; ?>
                </td>

                <td>
                    <?php echo $row['quantity']; ?>
                </td>

                <td>
                    ₹ <?php echo $row['total_price']; ?>
                </td>

                <td>

                    <?php
                    $status = strtolower($row['order_status']);
                    ?>

                    <span class="status <?php echo $status; ?>">

                        <?php echo $row['order_status']; ?>

                    </span>

                </td>

                <td>
                    <?php echo $row['order_date']; ?>
                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>

<script>

const searchInput = document.getElementById("searchInput");

searchInput.addEventListener("keyup", function(){

    const filter = searchInput.value.toLowerCase();

    const rows = document.querySelectorAll("#ordersTable tr");

    rows.forEach((row, index) => {

        if(index === 0) return;

        const text = row.innerText.toLowerCase();

        row.style.display =
            text.includes(filter)
            ? ""
            : "none";
    });
});

</script>

</body>
</html>