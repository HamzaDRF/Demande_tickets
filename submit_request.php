<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $material_type = $_POST['material_type'];
    $description = $_POST['description'];
    $urgency = $_POST['urgency'];
    
    try {
        $stmt = $conn->prepare("INSERT INTO material_requests (user_id, material_type, description, urgency) VALUES (:user_id, :material_type, :description, :urgency)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':material_type', $material_type);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':urgency', $urgency);
        $stmt->execute();
        
        // Store success message in session
        $_SESSION['request_message'] = [
            'type' => 'success',
            'text' => "Votre demande de $material_type a été soumise avec succès! Nous traiterons votre demande dans les plus brefs délais."
        ];
        
        header("Location: dashboard.php#new-request-section");
    } catch(PDOException $e) {
        $_SESSION['request_message'] = [
            'type' => 'danger',
            'text' => "Erreur lors de la soumission de la demande: " . $e->getMessage()
        ];
        header("Location: dashboard.php#new-request-section");
    }
}
?> 