<?php
session_start();
require_once 'config.php';

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            echo "Connexion réussie!";
            header("Location: dashboard.php"); // Créez cette page pour rediriger après login
        } else {
            echo "Email ou mot de passe incorrect!";
        }
    } catch(PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}
?> 