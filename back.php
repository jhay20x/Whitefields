<?php

function updateLoginAttempts($uname) {
	global $conn;
	global $attempts;
	global $hasLogin;
	global $error;
	global $failed_login_timestamp;

	$attempts = fetchAttempts($uname);

	if (is_null($attempts)) {
		$attempts = 0;
	} else {
		$attempts += 1;
		$_SESSION['maxAttempt'] -= 1;
	}

	if ($attempts >= 3) {
		$lastLogAttempt = fecthLastFailedAttempt($uname);
		$time = date_create(date('H:i:s', strtotime($lastLogAttempt)));
		$curtime = date_create(strtotime(time()));
		$timediff = date_diff($time, $curtime);

		$minutes = $timediff->i;

		$minutes = ($minutes - 3) * -1;

		if ($minutes <= 0) {
			$attempts = NULL;
			$failed_login_timestamp = NULL;
			$error = "1Incorect username or password. " . $_SESSION['maxAttempt'] . ' attempts remaining.';
		} else {
			$failed_login_timestamp = $lastLogAttempt;
			$error = "Login restricted due to multiple failed attempts. Please try again after " . $minutes . " minutes.";			
		}
		
	} else {
		$failed_login_timestamp = date('Y-m-d H:i:s');
		$error = "2Incorect username or password. " . $_SESSION['maxAttempt'] . ' attempts remaining.';
	}

	if ($hasLogin) {
		$attempts = NULL;
		$failed_login_timestamp = NULL;
	}
	
    $stmt = $conn->prepare("UPDATE `accounts` SET `failed_login_attempts`= ?, `failed_login_timestamp` = ? WHERE `username` = ? OR `email_address` = ?");
    $stmt->bind_param("isss", $attempts, $failed_login_timestamp, $uname, $uname);
    $stmt->execute();    
	$stmt->close();
}

function fecthLastFailedAttempt($uname){
	global $conn;

	$stmt = $conn->prepare("SELECT `failed_login_timestamp` FROM `accounts` WHERE `username` = ? OR `email_address` = ?");
	$stmt->bind_param("ss", $uname, $uname);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();

	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();

		return $row['failed_login_timestamp'];
	}
}

function fetchAttempts($uname) {
	global $conn;

	$stmt = $conn->prepare("SELECT `failed_login_attempts` FROM `accounts` WHERE `username` = ? OR `email_address` = ?");
	$stmt->bind_param("ss", $uname, $uname);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();

	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();

		$attempts = $row['failed_login_attempts'];

		$_SESSION['maxAttempt'] = 3 - $attempts;
		return $attempts;
	}
}