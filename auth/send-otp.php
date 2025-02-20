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
    $userEmail = $_SESSION['email_address'];
    $username = $_SESSION['user_username'];
    $mode = $_POST['mode'];

    $otp = rand(100000, 999999);
    
    if ($mode == "passwordReset") {
        $subject = 'Whitefields Password Reset Email Verification: OTP';
        $message = 'Your OTP for password reset is: ' . $otp;
    } else {
        $subject = 'Whitefields Email Verification: OTP';
        $message = 'Your OTP for email verification is: ' . $otp;
    }

    $mail = new PHPMailer(true);
    
    // $stmt = $conn->prepare("UPDATE `accounts` SET `reset_otp`= ? WHERE `email_address` = ?");
    // $stmt->bind_param("ss", $otp, $userEmail);
    // $stmt->execute();

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jhay20x@gmail.com';
        $mail->Password = 'grvf jkur pyxw pdqs';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
    
        // Email Recipients
        $mail->setFrom('jhay20x@gmail.com', 'Whitefields Dental Clinic');
        $mail->addAddress($userEmail, $username);
    
        //Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body =  $message;
        $mail->AltBody = $message;
        
        // Send email
        $mail->send();        
    
        // header("Location: ../verify-email.php");

        // $_SESSION['email_address'] = $userEmail;
        $_SESSION["passwordResetOTP"] = $otp;
        
        // echo 'Message has been sent';
    } catch (Exception $e) {
        $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
    $data['otpSent'] = false;
} else {
    $data['success'] = true;
    $data['message'] = 'Message has been sent.';
    $data['otpSent'] = true;
}

echo json_encode($data);