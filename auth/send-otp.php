<?php 
session_start();
	
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require_once '../database/config.php';


sleep(1);

$error;
$data = [];

if (isset($_SESSION['email_address'])) {
    if ($_SESSION['email_address'] == "None") {
        $userEmail = "jhay20x@gmail.com";
    } else {
        $userEmail = $_SESSION['email_address'] ?? "";
    }

    $username = $_SESSION['user_username'] ?? "";
    $mode = $_POST['mode'] ?? "";

    $otp = rand(100000, 999999);
    
    if ($mode == "passwordReset") {
        $subject = 'Whitefields Password Reset Email Verification: OTP';
        $message = 'Your OTP for password reset is: ' . $otp;
    } else {
        $subject = 'Whitefields Email Verification: OTP';
        $message = 'Your OTP for email verification is: ' . $otp;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jhay20x@gmail.com';
        $mail->Password = 'grvf jkur pyxw pdqs';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
    
        $mail->setFrom('jhay20x@gmail.com', 'Whitefields Dental Clinic');
        $mail->addAddress($userEmail, $username);
    
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body =  $message;
        $mail->AltBody = $message;
        
        $mail->send();        
    
        $_SESSION["passwordResetOTP"] = $otp;
    } catch (Exception $e) {
        $error = "Message could not be sent. {$mail->ErrorInfo}";
    }
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
    $data['otpSent'] = false;
} else {
    $data['success'] = true;
    $data['message'] = 'OTP Code has been sent.';
    $data['otpSent'] = true;
}

echo json_encode($data);