<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Account Information</title>
    <link rel="stylesheet" href="css/update.css">
</head>
<body>
    <div class="container">
        <h2>Update Personal Information</h2>
        <form action="update_account.php" method="POST">
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>

            <label for="password">New Password (leave blank to keep current):</label><br>
            <input type="password" id="password" name="password"><br><br>

            <input type="submit" name="update" value="Update Info"><br>
        </form>

        <form action="update_account.php" method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
            <input type="submit" name="delete" value="Delete Account" style="background-color: red; color: white;"><br><br>
        </form>
        <a href="index.php" class="btn">Home</a>
    </div>
</body>
</html>