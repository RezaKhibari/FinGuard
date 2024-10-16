<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body class="login-page">
    <div class="logincontainer">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <input type="submit" value="Login">
            <a href="register.php" class="btn">Register</a>
            <a href="index.php" class="btn">Home</a>
        </form>
    </div>
</body>
</html>

<?php
session_start();
require_once("db_connect.php"); 

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Array to store errors
    $errors = [];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Check if there are no validation errors
    if (empty($errors)) {
        // Prepare SQL statement to get user by email
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password matches, start a session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['account_type'] = $user['account_type'];

                // Redirect to a dashboard or homepage
                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Incorrect password. Please try again.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }

        // Close the prepared statement
        $stmt->close();
    }

    // Show errors if any
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}

$db->close();
?>
