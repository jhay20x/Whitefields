<?php 
// session_id("session-customer");
session_start();

global $conn;
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
		}else if (empty($signUpEmail)) {
			$error = 'Email is required.';
		}else if (empty($password)){
			$error = 'Password is required.';
		}else {
			$stmt = $conn->prepare("SELECT `username`, `email_address` FROM `accounts` WHERE `username` = ? OR `email_address` = ?");
			$stmt->bind_param("ss", $signUpUsername, $signUpEmail);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->close();

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
			} else {
				$hash = password_hash($password, PASSWORD_DEFAULT);

				$stmt = $conn->prepare("INSERT INTO `accounts`(`account_type_id`, `email_address`, `username`, `password`, `email_verified`, `status`) VALUES (2,?,?,?,0,1)");
				$stmt->bind_param("sss", $signUpEmail, $signUpUsername, $hash);
				$stmt->execute();
				$accountID = $stmt->insert_id;
				$stmt->close();

				$_SESSION['user_username'] = $signUpUsername;
				$_SESSION['email_address'] = $signUpEmail;
			}
		}
	} else {
		$error = "Error verifying reCAPTCHA, please try again.";
	}
	
	if (!empty($error)) {
		$data['success'] = false;
		$data['error'] = $error;
	} else {
		$data['success'] = true;
		$data['message'] = 'An OTP Code is being sent to your email.';
	}

	$conn->close();
	echo json_encode($data);
}