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
    $age = $_POST['age'];
    $bdate = $_POST['bdate'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $nationality = $_POST['nationality'];
    $contnumber = $_POST['contnumber'];
    $address = $_POST['address'];
    $occupation = $_POST['occupation'];

    $stmt = $conn->prepare("SELECT pi.accounts_id FROM patient_info pi WHERE pi.accounts_id = ?;");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        updateInfo($user_id, $lname, $fname, $mname, $contnumber, $bdate, $age, $gender, $religion, $nationality, $occupation, $address);
        $message = "Profile details has been successfully updated.";
    } else {
        insertInfo($user_id, $lname, $fname, $mname, $contnumber, $bdate, $age, $gender, $religion, $nationality, $occupation, $address);
        $message = "Profile details has been successfully saved.";
    }
}

function insertInfo($user_id, $lname, $fname, $mname, $contnumber, $bdate, $age, $gender, $religion, $nationality, $occupation, $address) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO `patient_info`(`accounts_id`, `lname`, `fname`, `mname`, `contactno`, `bdate`, `age`, `gender`, `religion`, `nationality`, `occupation`, `address`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssisssss", $user_id, $lname, $fname, $mname, $contnumber, $bdate, $age, $gender, $religion, $nationality, $occupation, $address);
    $stmt->execute();
}

function updateInfo($user_id, $lname, $fname, $mname, $contnumber, $bdate, $age, $gender, $religion, $nationality, $occupation, $address) {
    global $conn;

    $stmt = $conn->prepare("UPDATE `patient_info` SET `lname` = ?, `fname` = ?, `mname` = ?, `contactno` = ?, `bdate` = ?, `age` = ?, `gender` = ?, `religion` = ?, `nationality` = ?, `occupation` = ?, `address` = ? WHERE `accounts_id` = ?");
    $stmt->bind_param("sssssisssssi", $lname, $fname, $mname, $contnumber, $bdate, $age, $gender, $religion, $nationality, $occupation, $address, $user_id);
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