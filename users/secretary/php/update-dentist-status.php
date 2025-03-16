<?php
session_start();

require_once '../../../database/config.php';

$error;
$message;
$data = [];

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $status = $_POST["status"] ?? "";
    $id = $_POST["id"] ?? "";

    $stmt = $conn->prepare("UPDATE `accounts` SET `status` = ? WHERE `id` = ?;");
    $stmt->bind_param("ii", $status, $id);
    
    if ($stmt->execute()){    
        $message = "Dentist account status has been successfully updated.";
    } else {
        $error = "Dentist account status has failed to be updated. Please try again.";
    }
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}

echo json_encode($data);