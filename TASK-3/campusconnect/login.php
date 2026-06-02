<?php include 'includes/header.php'; ?>

<div class="form-container">

    <h2>Login</h2>

    <form action="auth/login_process.php" method="POST">

        <input type="email" name="email" placeholder="Enter Email" required>

        <input type="password" name="password" placeholder="Enter Password" required>

        <button type="submit">Login</button>

    </form>

</div>

<?php include 'includes/footer.php'; ?>