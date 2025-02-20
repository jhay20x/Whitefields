<?php
session_start();

include '../../../database/config.php';

$data = [];
$message;
$error;

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
    $specialist = $_POST['specialist'];
    $bdate = $_POST['bdate'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $nationality = $_POST['nationality'];
    $contnumber = $_POST['contnumber'];
    $address = $_POST['address'];
    $aboutme = $_POST['aboutme'];

    $stmt = $conn->prepare("SELECT di.accounts_id FROM dentist_info di WHERE di.accounts_id = ?;");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        updateInfo($user_id, $lname, $fname, $mname, $contnumber, $bdate, $specialist, $gender, $religion, $nationality, $aboutme, $address);
        $message = "Profile details has been successfully updated.";
    } else {
        insertInfo($user_id, $lname, $fname, $mname, $contnumber, $bdate, $specialist, $gender, $religion, $nationality, $aboutme, $address);
        $message = "Profile details has been successfully saved.";
    }
}

function insertInfo($user_id, $lname, $fname, $mname, $contnumber, $bdate, $specialist, $gender, $religion, $nationality, $aboutme, $address) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO `dentist_info`(`accounts_id`, `lname`, `fname`, `mname`, `contactno`, `bdate`, `specialist`, `gender`, `religion`, `nationality`, `about_me`, `address`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssssssss", $user_id, $lname, $fname, $mname, $contnumber, $bdate, $specialist, $gender, $religion, $nationality, $aboutme, $address);
    $stmt->execute();
}

function updateInfo($user_id, $lname, $fname, $mname, $contnumber, $bdate, $specialist, $gender, $religion, $nationality, $aboutme, $address) {
    global $conn;

    $stmt = $conn->prepare("UPDATE `dentist_info` SET `lname` = ?, `fname` = ?, `mname` = ?, `contactno` = ?, `bdate` = ?, `specialist` = ?, `gender` = ?, `religion` = ?, `nationality` = ?, `about_me` = ?, `address` = ? WHERE `accounts_id` = ?");
    $stmt->bind_param("sssssssssssi", $lname, $fname, $mname, $contnumber, $bdate, $specialist, $gender, $religion, $nationality, $aboutme, $address, $user_id);
    $stmt->execute();
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}

echo json_encode($data);