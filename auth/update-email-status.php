<?php 
session_start();

require_once '../database/config.php';

sleep(1);

$error;
$data = [];

if (isset($_SESSION['email_address'])) {
    if (!empty($_SESSION['email_address'])) {
        $stmt = $conn->prepare("UPDATE `accounts` SET `email_verified`= 1 WHERE `email_address` = ?");
        $stmt->bind_param("s", $_SESSION['email_address']);
        $stmt->execute();        
        
        $stmt = $conn->prepare("SELECT id, account_type_id, username FROM `accounts` WHERE `email_address` = ?");
        $stmt->bind_param("s", $_SESSION['email_address']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            unset($_SESSION['email_address']);
            $_SESSION["user_id"] = $user['id'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['account_type'] = $user['account_type_id'];
        } 
    }
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = 'Email successfully verified.';
}

echo json_encode($data);