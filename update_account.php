<?php
session_start();
require_once("db_connect.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch current user details
$user_id = $_SESSION['user_id'];
$query = "SELECT email, account_type FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found!";
    exit();
}

// Handle form submission for updating information
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $errors = [];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // If password is provided, hash it
    if (!empty($password) && strlen($password) >= 8) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    } else {
        $hashed_password = null;  // No update to password
    }

    // If no errors, proceed with update
    if (empty($errors)) {
        // Update query
        if ($hashed_password) {
            $updateQuery = "UPDATE users SET email = ?, password = ? WHERE id = ?";
            $stmt = $db->prepare($updateQuery);
            $stmt->bind_param("ssi", $email, $hashed_password, $user_id);
        } else {
            $updateQuery = "UPDATE users SET email = ? WHERE id = ?";
            $stmt = $db->prepare($updateQuery);
            $stmt->bind_param("si", $email, $user_id);
        }

        if ($stmt->execute()) {
            echo "<p style='color:green;'>Information updated successfully!</p>";
            $_SESSION['email'] = $email;
        } else {
            echo "<p style='color:red;'>Error updating your information.</p>";
        }
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}

// Handle account deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $db->prepare($deleteQuery);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Logout and redirect to homepage after account deletion
        session_destroy();
        header("Location: index.php");
        exit();
    } else {
        echo "<p style='color:red;'>Error deleting account.</p>";
    }
}

$db->close();
?>
