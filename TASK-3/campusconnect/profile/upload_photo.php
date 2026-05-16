<?php
include '../config/db.php';
include '../config/session.php';

if(isset($_POST['upload'])) {

    $file_name = $_FILES['photo']['name'];
    $temp_name = $_FILES['photo']['tmp_name'];

    move_uploaded_file($temp_name,
    "../uploads/profile_pictures/$file_name");

    $id = $_SESSION['user_id'];

    $sql = "UPDATE users
    SET profile_pic='$file_name'
    WHERE id='$id'";

    mysqli_query($conn, $sql);

    header("Location: profile_view.php");
}
?>

<form method="POST" enctype="multipart/form-data">

    <input type="file" name="photo" required>

    <button type="submit" name="upload">Upload</button>

</form>