<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

if(isset($_POST['request_id']) && isset($_POST['status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    
    try {
        $stmt = $conn->prepare("UPDATE material_requests SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $request_id);
        $stmt->execute();
        
        header("Location: admin_dashboard.php");
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?> 