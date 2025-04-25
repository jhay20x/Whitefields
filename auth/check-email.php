<?php 
session_start();

global $conn;
require_once '../database/config.php';

sleep(1);

$error;
$data = [];
$emailVerified;

if (isset($_POST['forgotEmail'])) {
    if (!empty($_POST['forgotEmail'])) {
        $email = $_POST['forgotEmail'];

        $stmt = $conn->prepare("SELECT * FROM `accounts` WHERE `email_address` = ? OR Username = ?");
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $user_username = $user['username'];
            $user_email = $user['email_address'];

            $_SESSION["user_username"] = $user_username;
            $_SESSION["email_address"] = $user_email;
            $emailVerified = true;
        } else {
            $emailVerified = false;
            $error = "Email can't be found. Please try again.";
        }
    } else {
        $emailVerified = false;
        $error = "Email is required.";
    }
} else {
    $emailVerified = false;
    $error = "Email is required.";
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
    $data['emailVerification'] = $emailVerified;
} else {
    $data['success'] = true;
    $data['message'] = 'User found.';
    $data['emailVerification'] = $emailVerified;
    $data['username'] = $user_username;
}


echo json_encode($data);