<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$data = [];
$message;
$error;

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $user_id = $_SESSION["user_id"];
    
    checkInfo($user_id);
}

function checkInfo($user_id) {
    global $conn;
    global $message;
        
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $suffix = $_POST['suffix'];
    $bdate = $_POST['bdate'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $nationality = $_POST['nationality'];
    $contnumber = $_POST['contnumber'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("SELECT si.accounts_id FROM secretary_info si WHERE si.accounts_id = ?;");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        updateInfo($user_id, $lname, $fname, $mname,$suffix, $contnumber, $bdate, $gender, $religion, $nationality, $address);
        $message = "Profile details has been successfully updated.";
    } else {
        insertInfo($user_id, $lname, $fname, $mname,$suffix, $contnumber, $bdate, $gender, $religion, $nationality, $address);
        $message = "Profile details has been successfully saved.";
    }
}

function insertInfo($user_id, $lname, $fname, $mname, $suffix, $contnumber, $bdate, $gender, $religion, $nationality, $address) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO `secretary_info`(`accounts_id`, `lname`, `fname`, `mname`, `suffix`, `contactno`, `bdate`, `gender`, `religion`, `nationality`, `address`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssssssss", $user_id, $lname, $fname, $mname, $suffix, $contnumber, $bdate, $gender, $religion, $nationality, $address);
    $stmt->execute();
	$stmt->close();
}

function updateInfo($user_id, $lname, $fname, $mname, $suffix, $contnumber, $bdate, $gender, $religion, $nationality, $address) {
    global $conn;

    $stmt = $conn->prepare("UPDATE `secretary_info` SET `lname` = ?, `fname` = ?, `mname` = ?, `suffix` = ?, `contactno` = ?, `bdate` = ?, `gender` = ?, `religion` = ?, `nationality` = ?, `address` = ? WHERE `accounts_id` = ?");
    $stmt->bind_param("ssssssssssi", $lname, $fname, $mname, $suffix, $contnumber, $bdate, $gender, $religion, $nationality, $address, $user_id);
    $stmt->execute();
	$stmt->close();
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}

$conn->close();
echo json_encode($data);