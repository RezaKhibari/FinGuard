<?php
    session_start();
    
    // Get form data
    $email = filter_input(INPUT_POST, 'email');
    $password = filter_input(INPUT_POST, 'password');
    $account_type = filter_input(INPUT_POST, 'account_type');
    
    // Server-side validation
    $errors = [];
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    // Check if password length is sufficient
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    
    // Check if email already exists in the database
    require_once("db_connect.php");
    $query = 'SELECT * FROM users';
    $stmt = $db->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll();
    $stmt->closeCursor();

    foreach ($users as $user){
        if ($email == $user["email"]){
            $errors[] = "Email already exists. Please use a different email.";
        }
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password using bcrypt
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into the database
        $query = "INSERT INTO users (email, password, account_type) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sss", $email, $hashed_password, $account_type);

        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $db->error;
        }
    } else {
        // Display validation errors
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }

    $stmt->close();
?>