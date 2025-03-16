<?php 
// session_id("session-customer");
session_start();
	
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// require '../resources/PHPMailer/src/Exception.php';
// require '../resources/PHPMailer/src/PHPMailer.php';
// require '../resources/PHPMailer/src/SMTP.php';
include '../database/config.php';

sleep(1);

$error;
$emailVerification;
$data = [];
$lastFailedAttemptTime;
$minutesLeft;
$attempts;
$uname;

if (isset($_POST['loginUserEmail']) && isset($_POST['password'])) {	
	
	$uname = str_replace(' ', '', $_POST['loginUserEmail']);
	$password = str_replace(' ', '', $_POST['password']);

	// echo $_POST['g-recaptcha-response'];
	
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$privatekey = "6LfUKIYqAAAAAAjzfGXPnoA-RWIV6T03AfoDHWEZ";
	$response = file_get_contents($url."?secret=".$privatekey."&response=".$_POST['g-recaptcha-response']);
	$response = json_decode($response);
	
	if ($response->success == true) {
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

				$user_username = $user['username'];
				$user_email = $user['email_address'];
				$user_id = $user['id'];
				$user_password = $user['password'];
				$user_type = $user['account_type_id'];
				// if (($uname === $user_username || $uname === $user_email) && $user_type == 2) {

				if ((strtolower($uname) === strtolower($user_username) || strtolower($uname) === strtolower($user_email))) {
					if (password_verify($password, $user_password)) {
						checkHasFailedAttempt($uname);

						if (!checkTimeRemaining()) {
							updateFailedLogin(NULL, NULL, $uname);
	
							if ($emailVerification = $user['email_verified']) {
								$emailVerification = true;
								$_SESSION['user_id'] = $user_id;
								$_SESSION['user_username'] = $user_username;
								$_SESSION['account_type'] = $user_type;
							} else {
								$emailVerification = false;
								$_SESSION['email_address'] = $user_email;
							};
						} else {
							$error = "Login restricted due to multiple failed attempts. Please try again after $minutesLeft minute/s or reset your password with <a href='./forgot-password.php'>Forgot Password</a>";
						}

						// $_SESSION['user_type'] = 2;
						// header("Location: ../users/secretary/dashboard.php");	
					}else {
						// header("Location: ../login.php?userLogin=1&error=Incorect username or password.&loginUserEmail=$uname");

						// echo var_dump(checkHasFailedAttempt($uname));
						// echo var_dump(checkTimeRemaining());

						if (checkHasFailedAttempt($uname)) {
							if (!checkTimeRemaining()) {
								updateFailedLogin(NULL, NULL, $uname);
								$error = "Incorect password. Please try again.";
							} else {
								$error = "Login restricted due to multiple failed attempts. Please try again after $minutesLeft minute/s or reset your password with <a href='./forgot-password.php'>Forgot Password</a>";
							}
							
						} else {		
							global $attempts;
							
							if ($attempts == 0) {
								$failed_login_timestamp = date('Y-m-d H:i:s');
							} else {
								$failed_login_timestamp = $lastFailedAttemptTime;
							}

							$attempts += 1;
							$_SESSION['maxAttempt'] -= 1;

							updateFailedLogin($attempts,$failed_login_timestamp,$uname);

							if ($attempts == 4) {
								if (!checkTimeRemaining()) {
									updateFailedLogin(NULL, NULL, $uname);
									$error = "Incorect password. Please try again.";
								} else {
									$error = "Login restricted due to multiple failed attempts. Please try again after $minutesLeft minute/s or reset your password with <a href='./forgot-password.php'>Forgot Password</a>";
								}
							} else {
								$error = "Incorect password. " . $_SESSION['maxAttempt'] . " attempt/s remaining.";
							}
							
						}
					}

				} else {
					$error = "Incorect username or password.";
					// header("Location: ../login.php?userLogin=1&error=Incorect username or password.&loginUserEmail=$uname");
				}
			}else {
				$error = "Incorect username or password.";
				// header("Location: ../login.php?userLogin=1&error=Incorect username or password.&loginUserEmail=$uname");
			}
		}
	} else {
		$error = "Error verifying reCAPTCHA, please try again.";
	}

	if (!empty($error)) {
		$data['success'] = false;
		$data['error'] = $error;
		$data['username'] = $uname;
	} else {
		$data['success'] = true;
		$data['message'] = 'Logged in successfully.';
		$data['emailVerification'] = $emailVerification;
		$data['user_id'] = $user_id;
		$data['userEmail'] = $user_email;
		$data['username'] = $user_username;
		$data['usertype'] = $user_type;
	}
	
	echo json_encode($data);
}

function checkHasFailedAttempt($uname){
	global $conn;
	global $lastFailedAttemptTime;
	global $attempts;

	$stmt = $conn->prepare("SELECT `failed_login_timestamp`, `failed_login_attempts` FROM `accounts` WHERE `username` = ? OR `email_address` = ?");
	$stmt->bind_param("ss", $uname, $uname);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();

		$lastFailedAttemptTime = $row['failed_login_timestamp'];
		$attempts = $row['failed_login_attempts'];

		if (is_null($attempts)) {
			$attempts = 0;
			$_SESSION['maxAttempt'] = 4;
			return false;
		} else if ($attempts == 4){
			$_SESSION['maxAttempt'] = 4 - $attempts;
			return true;
		} else {
			return false;
		}
	}
}

function checkTimeRemaining() {
	global $lastFailedAttemptTime;
	global $minutesLeft;
	global $attempts;

	$time = date_create(date('H:i:s', strtotime($lastFailedAttemptTime)));
	$curtime = date_create(date('H:i:s', time()));
	$timediff = date_diff($time, $curtime);
	
	$minutesLeft = $timediff->i;

	$minutesLeft = ($minutesLeft - 3) * -1;

	// echo $attempts;
	// echo $minutesLeft;
	
	if ($attempts < 4 && $minutesLeft <= 0) {
		return false;
	} else if ($attempts == 4 && $minutesLeft >=0) {
		return true;
	}
}

function updateFailedLogin($attempts, $failed_login_timestamp, $uname) {
	global $conn;	
	
    $stmt = $conn->prepare("UPDATE `accounts` SET `failed_login_attempts`= ?, `failed_login_timestamp` = ? WHERE `username` = ? OR `email_address` = ?");
    $stmt->bind_param("isss", $attempts, $failed_login_timestamp, $uname, $uname);
    $stmt->execute();  
}