<?php
include '../config/db.php';

if(isset($_POST['submit'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users(name,email,password)
    VALUES('$name','$email','$password')";

    mysqli_query($conn, $sql);

    header("Location: view_users.php");
}
?>

<form method="POST">

    <input type="text" name="name" placeholder="Name" required>

    <input type="email" name="email" placeholder="Email" required>

    <input type="password" name="password" placeholder="Password" required>

    <button type="submit" name="submit">Add User</button>

</form>