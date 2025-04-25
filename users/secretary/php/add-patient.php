<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$data = [];
$message;
$error;

sleep(1);

function checkNameDOB($conn, $fname, $lname, $bdate) {    
    $stmt = $conn->prepare("SELECT pi.fname, pi.lname, pi.bdate FROM patient_info pi WHERE pi.fname LIKE ? AND pi.lname LIKE ? AND pi.bdate = ?;");
    $fname = "%$fname%";
    $lname = "%$lname%";
    $stmt->bind_param("sss",  $fname, $lname, $bdate);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function checkEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT ac.email_address FROM accounts ac WHERE ac.email_address = ? AND ac.email_address != 'None';");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {        
        return true;
    } else {
        return false;
    }
}

function insertInfo($conn, $insertId, $lname, $fname, $mname, $suffix, $contnumber, $bdate, $gender, $religion, $nationality, $occupation, $address) {
    $stmt = $conn->prepare("INSERT INTO `patient_info`(`accounts_id`, `lname`, `fname`, `mname`, `suffix`, `contactno`, `bdate`, `gender`, `religion`, `nationality`, `occupation`, `address`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssssssss", $insertId, $lname, $fname, $mname, $suffix, $contnumber, $bdate, $gender, $religion, $nationality, $occupation, $address);
    $stmt->execute();
	$stmt->close();
}

function insertAccount($conn, $email, $username, $hash, $emailVerified) {
    $stmt = $conn->prepare("INSERT INTO `accounts`(`account_type_id`, `email_address`, `username`, `password`, `status`, `email_verified`) VALUES (2,?,?,?,1,?)");
    $stmt->bind_param("sssi", $email, $username, $hash, $emailVerified);
    $stmt->execute();
    $insertId = $conn->insert_id;
	$stmt->close();

    return $insertId;
}

function checkUsername($conn, $username) {
    $stmt = $conn->prepare("SELECT ac.username FROM accounts ac WHERE ac.username = ?;");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {        
        return true;
    } else {
        return false;
    }
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {        
    $username = $_POST['username'] ?? "";
    $email = $_POST['email'] ?? "";
    $userPasswordCheck = $_POST['userPasswordCheck'] ?? "";
    $confirmUserPasswordCheck = $_POST['confirmUserPasswordCheck'] ?? "";
    $fname = $_POST['fname'] ?? "";
    $mname = $_POST['mname'] ?? "";
    $lname = $_POST['lname'] ?? "";
    $suffix = $_POST['suffix'] ?? "";
    $bdate = $_POST['bdate'] ?? "";
    $gender = $_POST['gender'] ?? "";
    $religion = $_POST['religion'] ?? "";
    $nationality = $_POST['nationality'] ?? "";
    $contnumber = $_POST['contnumber'] ?? "";
    $address = $_POST['address'] ?? "";
    $occupation = $_POST['occupation'] ?? "";
    $emailVerified = $email === "None" ? 1 : 0;

    if ($userPasswordCheck !== $confirmUserPasswordCheck) {
        $data['success'] = false;
        $data['error'] = "Passwords pid not match. Please try again. $emailVerified";
        echo json_encode($data);
        return;
    } else {
        $hash = password_hash($userPasswordCheck, PASSWORD_DEFAULT);
    }

    if (checkEmail($conn, $email)) {
        $data['success'] = false;
        $data['error'] = "This email is already used. Please try another valid email.";
        echo json_encode($data);
        return;
    }

    if (checkUsername($conn, $username)) {
        $data['success'] = false;
        $data['error'] = "This username is already used. Please try another username.";
        echo json_encode($data);
        return;
    }

    if (checkNameDOB($conn, $fname, $lname, $bdate)) {
        $data['success'] = false;
        $data['error'] = "A patient with the same name and birthdate is already registered.";
        echo json_encode($data);
        return;
    }
    
    $insertId = insertAccount($conn, $email, $username, $hash, $emailVerified);
    insertInfo($conn, $insertId, $lname, $fname, $mname, $suffix, $contnumber, $bdate, $gender, $religion, $nationality, $occupation, $address);
    $message = "A new patient has been successfully added.";
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}

echo json_encode($data);