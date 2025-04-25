<?php 
session_start();

global $conn;
require_once '../database/config.php';

$error;
$data = [];
$otpCode = $_SESSION["passwordResetOTP"];

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = 'OTP fetched.';
    $data['otpCode'] = $otpCode;
}

echo json_encode($data);