<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forgot Password</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="container">
            <h2>Forgot Password</h2>
            <form action="forgot_password.php" method="POST">
                <label for="email">Enter your email:</label><br>
                <input type="email" id="email" name="email" required><br>

                <input type="submit" value="Submit">
            </form>
        </div>
    </body>
</html>

<?php
require_once("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $token = bin2hex(random_bytes(50)); // Generate password reset token
            $expires = date("U") + 1800; // Token expires in 30 minutes

            $stmt = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $token, $expires);
            $stmt->execute();
            $stmt->close();

            // Send reset link to email
            $reset_link = "http://yourdomain.com/reset_password.php?token=$token";
            mail($email, "Password Reset Request", "Click on the link to reset your password: $reset_link");

            echo "<p style='color:green;'>Password reset link has been sent to your email.</p>";
        } else {
            $errors[] = "No account found with that email.";
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