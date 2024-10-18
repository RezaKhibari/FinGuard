<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="container">
            <h2>Reset Password</h2>
            <form action="reset_password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>" required>

                <label for="password">New Password:</label><br>
                <input type="password" id="password" name="password" required><br><br>

                <label for="confirm_password">Confirm Password:</label><br>
                <input type="password" id="confirm_password" name="confirm_password" required><br><br>

                <input type="submit" value="Reset Password">
            </form>
        </div>
    </body>
</html>

<?php
require_once("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $errors = [];

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if (empty($errors)) {
        $query = "SELECT * FROM password_resets WHERE token = ? AND expires_at >= ?";
        $stmt = $db->prepare($query);
        $current_time = date("U");
        $stmt->bind_param("ss", $token, $current_time);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = $row['email'];

            // Hash new password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Update user's password
            $update_query = "UPDATE users SET password = ? WHERE email = ?";
            $stmt = $db->prepare($update_query);
            $stmt->bind_param("ss", $hashed_password, $email);
            $stmt->execute();

            // Remove the reset token from the database
            $stmt = $db->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();

            echo "<p style='color:green;'>Password reset successful! You can now <a href='login.php'>login</a>.</p>";
        } else {
            $errors[] = "Invalid or expired token.";
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}

$db->close();
?>