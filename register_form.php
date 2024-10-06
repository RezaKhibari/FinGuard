<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register.php" method="POST">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="account_type">Account Type:</label><br>
        <select id="account_type" name="account_type" required>
            <option value="individual">Individual</option>
            <option value="business">Business</option>
        </select><br><br>

        <input type="submit" value="Register">
    </form>
</body>
</html>