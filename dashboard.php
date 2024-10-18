<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

echo "<h1>Welcome, " . $_SESSION['email'] . "!</h1>";
echo "<p>Account Type: " . $_SESSION['account_type'] . "</p>";
?>