<?php include 'includes/header.php'; ?>

<div class="form-container">

    <h2>Register</h2>

    <form action="auth/register_process.php"
    method="POST">

        <input type="text"
        name="name"
        placeholder="Enter Name"
        required>

        <input type="email"
        name="email"
        placeholder="Enter Email"
        required>

        <input type="password"
        name="password"
        placeholder="Enter Password"
        required>

        <select name="role">

            <option value="student">
                Student
            </option>

            <option value="admin">
                Admin
            </option>

        </select>

        <button type="submit">
            Register
        </button>

    </form>

</div>

<?php include 'includes/footer.php'; ?>