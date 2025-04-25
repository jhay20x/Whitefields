<?php 
session_start();
	
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

global $conn;
require_once '../../../database/config.php';
require '../../../vendor/autoload.php';

sleep(1);

$error;
$data = [];

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    function verifyUser($conn, $userEmail, $username){
        if (checkEmail($conn, $userEmail)) {
            return;
        }

        sendOTP($userEmail, $username);
    }

    function sendOTP($userEmail, $username) {
        global $error;
        
        $otp = rand(100000, 999999);
        $subject = 'Whitefields Email Verification: OTP';
        $message = 'Your OTP for email verification is: ' . $otp;

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
        
            $_SESSION["emailVerifyOTP"] = $otp;
        } catch (Exception $e) {
            $error = "Message could not be sent. {$mail->ErrorInfo}";
        }
    }

    function checkEmail($conn, $userEmail) {
        global $error;

        $stmt = $conn->prepare("SELECT email_address FROM `accounts` WHERE `email_address` = ?");
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {        
            $error = "Email address is already used. Please try again.";
            return true;
        } else {
            return false;
        }
    }
    
    if (isset($_POST['emailAccount'])) {
        $userEmail = $_POST['emailAccount'] ?? "";
        $username = $_SESSION['user_username'] ?? "";

        verifyUser($conn, $userEmail, $username);
    }

    if (!empty($error)) {
        $data['success'] = false;
        $data['error'] = $error;
        $data['otpSent'] = false;
    } else {
        $data['success'] = true;
        $data['message'] = "OTP Code has been sent to your email.";
        $data['otpSent'] = true;
    }

    
    echo json_encode($data);
}