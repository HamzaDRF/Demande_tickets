<?php
session_start();
require_once 'config.php';

if(isset($_POST['admin_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded admin credentials (you should use database in production)
    $admin_username = "admin";
    $admin_password = "admin123"; // Use hashed password in production

    if($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin'] = true;
        header("Location: admin_dashboard.php");
    } else {
        echo "Invalid credentials!";
    }
}
?> 