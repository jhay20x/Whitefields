<?php 
// session_id("session-customer");
session_start();
	
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

require '../resources/PHPMailer/src/Exception.php';
// require '../resources/PHPMailer/src/PHPMailer.php';
// require '../resources/PHPMailer/src/SMTP.php';
require_once '../database/config.php';

$errors = [];
$data = [];

if (isset($_POST['signUpEmail']) && isset($_POST['password']) && isset($_POST['signUpUsername'])) {
	$signUpUsername = $_POST['signUpUsername'];
	$signUpEmail = $_POST['signUpEmail'];
	$password = $_POST['password'];	
	
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$privatekey = "6LfUKIYqAAAAAAjzfGXPnoA-RWIV6T03AfoDHWEZ";
	$response = file_get_contents($url."?secret=".$privatekey."&response=".$_POST['g-recaptcha-response']);
	$response = json_decode($response);
	
	if ($response->success == true) {
		if (empty($signUpUsername)) {
			$error = 'Username is required.';
			// header("Location: ../login.php?userLogin=0&error=Username is required.");		
		}else if (empty($signUpEmail)) {
			$error = 'Email is required.';
			// header("Location: ../login.php?userLogin=0&error=Email is required.&signUpUsername=$signUpUsername");
		}else if (empty($password)){
			$error = 'Password is required.';
			// header("Location: ../login.php?userLogin=0&error=Password is required.&signUpUsername=$signUpUsername&signUpEmail=$signUpEmail");
		}else {
			$stmt = $conn->prepare("SELECT `username`, `email_address` FROM `accounts` WHERE `username` = ? OR `email_address` = ?");
			$stmt->bind_param("ss", $signUpUsername, $signUpEmail);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows >= 1) {
				foreach ($result as $row) {
					if (strtolower($signUpUsername) == strtolower($row['username'])) {
						$error = 'Username ' . $signUpUsername . ' is already used.';
						break;
					}

					if (strtolower($signUpEmail) == strtolower($row['email_address'])) {
						$error = 'Email ' . $signUpEmail . ' is already used.';						
						break;
					}
				}
				// $user = $result->fetch_assoc();
				
				// $email = $user['email_address'];
				// $username = $user['username'];

				// if (!empty($email)) {
				// 	$error = 'Email is already used.';
				// } else {
				// 	$error = 'Username is already used.';					
				// }

			// header("Location: ../login.php?userLogin=0&error=Email is already used.&signUpUsername=$signUpUsername&signUpEmail=$signUpEmail");
			} else {
				$otp = rand(100000, 999999);
				$hash = password_hash($password, PASSWORD_DEFAULT);

				$stmt = $conn->prepare("INSERT INTO `accounts`(`account_type_id`, `email_address`, `username`, `password`, `otp`) VALUES (2,?,?,?,?)");
				$stmt->bind_param("ssss", $signUpEmail, $signUpUsername, $hash, $otp);
				$stmt->execute();
				$accountID = $stmt->insert_id;

				$_SESSION['user_username'] = $signUpUsername;
				$_SESSION['email_address'] = $signUpEmail;

				// $mail = new PHPMailer(true);

				// try {
				// 	// Server settings
				// 	$mail->isSMTP();
				// 	$mail->Host = 'smtp.gmail.com';
				// 	$mail->SMTPAuth = true;
				// 	$mail->Username = 'jhay20x@gmail.com';
				// 	$mail->Password = 'grvf jkur pyxw pdqs';
				// 	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				// 	$mail->Port = 587;

				// 	// Email Recipients
				// 	$mail->setFrom('jhay20x@gmail.com', 'Whitefields Dental Clinic');
				// 	$mail->addAddress($signUpEmail, $signUpUsername);

				// 	//Email Content
				// 	$mail->isHTML(true);
				// 	$mail->Subject = 'Whitefields Email Verification: OTP';
				// 	$mail->Body = 'Your OTP for email verification is: ' . $otp;
				// 	$mail->AltBody = 'Your OTP for email verification is: ' . $otp;
					
				// 	// Send email
				// 	$mail->send();

				// 	$_SESSION['email_address'] = $signUpEmail;

				// 	// header("Location: ../verify-email.php");
					
				// 	// echo 'Message has been sent';
				// } catch (Exception $e) {
				// 	// echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
				// }
			}
		}
	} else {
		$error = "Error verifying reCAPTCHA, please try again.";
	}
	// header("Location: ../login.php?userLogin=0&error=Incorect username or password.&firstName=$fname&lastName=$lname&username=$signUpEmail");
	
	if (!empty($error)) {
		$data['success'] = false;
		$data['error'] = $error;
	} else {
		$data['success'] = true;
		$data['message'] = 'OTP has been sent to your email.';
	}
	
	echo json_encode($data);
}