<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="regcontainer">
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
    </div>
</body>
</html>

<?php
    session_start();
    require_once("db_dbect.php");

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture and sanitize user input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $account_type = $_POST['account_type'];

    // Array to store error messages
    $errors = [];

    // Server-side validation
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate password length (at least 8 characters)
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Check if the email already exists in the database
    $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
    $stmt = $db->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors[] = "Email already registered. Please use another one.";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password using bcrypt
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert user details into the database
        $insertQuery = "INSERT INTO users (email, password, account_type) VALUES (?, ?, ?)";
        $stmt = $db->prepare($insertQuery);
        $stmt->bind_param("sss", $email, $hashed_password, $account_type);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>Registration successful!</p>";
        } else {
            echo "<p style='color:red;'>Error: " . $db->error . "</p>";
        }
    } else {
        // Display validation errors
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }

    // Close statement and dbection
    $stmt->close();
}

$db->close();
?>
    
   