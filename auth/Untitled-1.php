<?php 
// session_id("session-customer");
session_start();
	
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// require '../resources/PHPMailer/src/Exception.php';
// require '../resources/PHPMailer/src/PHPMailer.php';
// require '../resources/PHPMailer/src/SMTP.php';
include './database/config.php';

sleep(1);

$error;
$emailVerification;
$data = [];

if (isset($_POST['loginUserEmail']) && isset($_POST['password'])) {	
	
	$uname = $_POST['loginUserEmail'];
	$password = $_POST['password'];

	if (empty($uname)) {
		$error = 'Email or Username is required.';
		// header("Location: ../login.php?userLogin=1&error=Username is required.");
	}else if (empty($password)){
		$error = 'Password is required.';
		// header("Location: ../login.php?userLogin=1&error=Password is required.&loginUserEmail=$uname");
	}else {
		$stmt = $conn->prepare("SELECT * FROM `accounts` WHERE `username` = ? OR `email_address` = ?");
		$stmt->bind_param("ss", $uname, $uname);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows == 1) {
			$user = $result->fetch_assoc();

			$isEmailVerified = $user['email_verified'];

			if ($isEmailVerified) {
				$emailVerification = true;
				$user_id = $user['id'];
				$user_username = $user['username'];
				$user_email = $user['email_address'];
				$user_password = $user['password'];
				$user_type = $user['account_type_id'];

				if (($uname === $user_username || $uname === $user_email) && $user_type == 2) {
					if (password_verify($password, $user_password)) {
						$_SESSION['user_id'] = $user_id;
						$_SESSION['user_username'] = $user_username;

						// $_SESSION['user_type'] = 2;
						// header("Location: ../users/secretary/dashboard.php");	
					}else {
						$error = "Incorect username or password.";
						// header("Location: ../login.php?userLogin=1&error=Incorect username or password.&loginUserEmail=$uname");
					}
				} else {
					$error = "Incorect username or password.";
					// header("Location: ../login.php?userLogin=1&error=Incorect username or password.&loginUserEmail=$uname");
				}

			} else {				

				// $mail = new PHPMailer(true);

				// try {
				// 	Server settings
				// 	$mail->isSMTP();
				// 	$mail->Host = 'smtp.gmail.com';
				// 	$mail->SMTPAuth = true;
				// 	$mail->Username = 'jhay20x@gmail.com';
				// 	$mail->Password = 'grvf jkur pyxw pdqs';
				// 	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				// 	$mail->Port = 587;

				// 	// Email Recipients
				// 	$mail->setFrom('jhay20x@gmail.com', 'Whitefields Dental Clinic');
				// 	$mail->addAddress($email, $signUpUsername);

				// 	//Email Content
				// 	$mail->isHTML(true);
				// 	$mail->Subject = 'Whitefields Email Verification: OTP';
				// 	$mail->Body = 'Your OTP for email verification is: ' . $otp;
				// 	$mail->AltBody = 'Your OTP for email verification is: ' . $otp;
					
				// 	// Send email
				// 	$mail->send();
					$emailVerification = false;
					$_SESSION['email_address'] = $user['email_address'];
				// 	$_SESSION['user_id'] = $user['id'];
				// 	$_SESSION['user_username'] = $user['username'];
				// 	header("Location: ../verify-email.php");
					
				// 	echo 'Message has been sent';
				// } catch (Exception $e) {
				// 	echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
				// }
			}
		}else {

			$error = "Incorect username or password.";
			// header("Location: ../login.php?userLogin=1&error=Incorect username or password.&loginUserEmail=$uname");
		}
	}	

	if (!empty($error)) {
		$data['success'] = false;
		$data['error'] = $error;
	} else {
		$data['success'] = true;
		$data['message'] = 'Logged in successfully.';
		$data['emailVerification'] = $emailVerification;
	}
	
	echo json_encode($data);
}