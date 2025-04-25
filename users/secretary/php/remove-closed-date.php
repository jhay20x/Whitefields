<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$message;
$data = [];

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $id = $_POST["id"] ?? "";

    $stmt = $conn->prepare("DELETE FROM store_closed_dates WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()){    
        $message = "The date has been successfully removed.";
    } else {
        $error = "The date has not been removed. " + $stmt->error;
    }
    
    $stmt->close();
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}

echo json_encode($data);