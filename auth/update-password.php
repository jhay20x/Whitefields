<?php 
session_start();

global $conn;
require_once '../database/config.php';

sleep(1);

$error;
$data = [];

if (isset($_POST['newPass']) && isset($_POST['userEmail'])) {
    if (!empty($_POST['newPass']) && !empty($_POST['userEmail'])) {   
        $newPass = $_POST['newPass'];
        
        $hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE `accounts` SET `password`= ? WHERE `email_address` = ?");
        $stmt->bind_param("ss", $hashedPassword, $_POST['userEmail']);
        $stmt->execute();
        $stmt->close();
    } else {
        $_SESSION['emailVerified'] = false;
        $error = "Password is required." . $_POST['newPass'];
    }
} else {
    $_SESSION['emailVerified'] = false;
    $error = "Password is required." . $_POST['newPass'];
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = 'Password has been successfully updated.';
}

echo json_encode($data);