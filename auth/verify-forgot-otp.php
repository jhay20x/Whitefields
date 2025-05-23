<?php 
session_start();

global $conn;
require_once '../database/config.php';

sleep(1);

$error;
$data = [];

if (isset($_POST['otpCode'])) {
    if (!empty($_POST['otpCode'])) {
        $stmt = $conn->prepare("SELECT id, username, otp FROM `accounts` WHERE `email_address` = ?");
        $stmt->bind_param("s", $_SESSION['email_address']);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
    
            $userOTP = $_POST['otpCode'];
            $savedOTP = $user['otp'];
            $user_id = $user['id'];
            $user_username = $user['username'];
    
            if ($userOTP === $savedOTP) {
                $stmt = $conn->prepare("UPDATE `accounts` SET `email_verified`= 1 WHERE `email_address` = ?");
                $stmt->bind_param("s", $_SESSION['email_address']);
                $stmt->execute();
                $stmt->close();
                
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_username'] = $user_username;
                $_SESSION['emailVerified'] = true;
            } else {
                $_SESSION['emailVerified'] = false;
                $error = "OTP did not match. Please try again. ";
            }
        } else {
            $_SESSION['emailVerified'] = false;
            $error = "OTP is required." . $_POST['otpCode'];
        }
    } else {
        $_SESSION['emailVerified'] = false;
        $error = "OTP is required." . $_POST['otpCode'];
    }
} else {
    $_SESSION['emailVerified'] = false;
    $error = "OTP is required." . $_POST['otpCode'];
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = 'Email successfully verified.';
}


echo json_encode($data);