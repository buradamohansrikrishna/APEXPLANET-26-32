<?php

include '../config/db.php';

/* CHECK IF ID EXISTS */

if(isset($_GET['id'])){

    $id = $_GET['id'];

    /* DELETE QUERY */

    $query = "DELETE FROM foods WHERE id='$id'";

    $result = mysqli_query($conn, $query);

    /* SUCCESS */

    if($result){

        echo "
        <script>
            alert('Food Item Deleted Successfully');
            window.location.href='foods.php';
        </script>
        ";

    }else{

        echo "
        <script>
            alert('Failed To Delete Food Item');
            window.location.href='foods.php';
        </script>
        ";
    }

}else{

    echo "
    <script>
        alert('Invalid Request');
        window.location.href='foods.php';
    </script>
    ";
}
?>