<?php 
session_start();

global $conn;
require_once '../../../database/config.php';

sleep(1);

$error;
$message;
$data = [];

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    if (isset($_POST['emailAccountOTP'])) {
        if (!empty($_POST['emailAccountOTP'])) {
            $userOTP = $_POST['emailAccountOTP'] ?? "";
            $otpSent = $_SESSION['emailVerifyOTP'] ?? "";
            $userEmail = $_POST['emailAccount'] ?? "";
            $username = $_SESSION['user_username'] ?? "";

            if ($userOTP == $otpSent) {
                $stmt = $conn->prepare("UPDATE accounts SET email_address = ?, email_verified = 1 WHERE username = ?");
                $stmt->bind_param("ss", $userEmail, $username);

                if ($stmt->execute()) {
                    $message = "Email address has been saved.";
                } else {
                    $error = $stmt->error;
                }
                
                $stmt->close();
            } else {
                $error = "OTP Code is invalid. Please try again.";
            }
        } else {
            $error = "OTP is Empty.";
        }
    } else {
        $error = "OTP is not set.";
    }

    if (!empty($error)) {
        $data['success'] = false;
        $data['error'] = $error;
    } else {
        $data['success'] = true;
        $data['message'] = $message;
    }

    
    echo json_encode($data);
}