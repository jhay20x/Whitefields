<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$message;
$data = [];

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $status = $_POST["status"] ?? "";
    $id = $_POST["id"] ?? "";

    $stmt = $conn->prepare("UPDATE `accounts` SET `status` = ? WHERE `id` = ?;");
    $stmt->bind_param("ii", $status, $id);
    
    if ($stmt->execute()){    
        $message = "Secretary account status has been successfully updated.";
    } else {
        $error = "Secretary account status has failed to be updated. Please try again.";
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