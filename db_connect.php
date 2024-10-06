<?php
    session_start();
    $dsn = 'mysql:host=localhost;dbname=fin_guard';
    $username = 'root';
    $password = '123456';

    try{
        $db = new PDO($dsn, $username, $password); // creates the connection
    }
    catch (PDOException $e){
        $_SESSION["database_error"] = $e->getMessage();
        $url = "database_error.php";
        header("Location: " . $url);
        exit();
    }
?>