<?php
include '../config/db.php';

$id = $_GET['id'];

$sql = "SELECT * FROM users WHERE id='$id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if(isset($_POST['update'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];

    $update = "UPDATE users
    SET name='$name', email='$email'
    WHERE id='$id'";

    mysqli_query($conn, $update);

    header("Location: view_users.php");
}
?>

<form method="POST">

    <input type="text" name="name"
    value="<?php echo $row['name']; ?>">

    <input type="email" name="email"
    value="<?php echo $row['email']; ?>">

    <button type="submit" name="update">Update</button>

</form>