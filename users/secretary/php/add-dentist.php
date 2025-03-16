<?php
session_start();

include '../../../database/config.php';

$data = [];
$message;
$error;

sleep(1);

function checkNameDOB($conn, $fname, $lname, $bdate) {    
    $stmt = $conn->prepare("SELECT di.fname, di.lname, di.bdate FROM dentist_info di WHERE di.fname LIKE ? AND di.lname LIKE ? AND di.bdate = ?;");
    $fname = "%$fname%";
    $lname = "%$lname%";
    $stmt->bind_param("sss",  $fname, $lname, $bdate);
    $stmt->execute();
    $result = $stmt->get_result();

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

    if ($result->num_rows > 0) {        
        return true;
    } else {
        return false;
    }
}

function insertInfo($conn, $insertId, $lname, $fname, $mname, $suffix, $contnumber, $bdate, $specialist, $gender, $religion, $nationality, $aboutme, $address) {
    $stmt = $conn->prepare("INSERT INTO `dentist_info`(`accounts_id`, `lname`, `fname`, `mname`, `suffix`, `contactno`, `bdate`, `specialist`, `gender`, `religion`, `nationality`, `about_me`, `address`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssssssssss", $insertId, $lname, $fname, $mname, $suffix, $contnumber, $bdate, $specialist, $gender, $religion, $nationality, $aboutme, $address);
    $stmt->execute();
}

function insertAccount($conn, $email, $username, $hash) {
    $stmt = $conn->prepare("INSERT INTO `accounts`(`account_type_id`, `email_address`, `username`, `password`, `email_verified`) VALUES (3,?,?,?,0)");
    $stmt->bind_param("sss", $email, $username, $hash);
    $stmt->execute();
    $insertId = $conn->insert_id;

    return $insertId;
}

function checkUsername($conn, $username) {
    $stmt = $conn->prepare("SELECT ac.username FROM accounts ac WHERE ac.username = ?;");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {        
        return true;
    } else {
        return false;
    }

}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {        
    $username = $_POST['username'];
    $email = $_POST['email'];
    $userPasswordDentist = $_POST['userPasswordDentist'];
    $confirmUserPasswordDentist = $_POST['confirmUserPasswordDentist'];
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $suffix = $_POST['suffix'];
    $specialist = $_POST['specialist'];
    $bdate = $_POST['bdate'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $nationality = $_POST['nationality'];
    $contnumber = $_POST['contnumber'];
    $address = $_POST['address'];
    $aboutme = $_POST['aboutme'];

    if ($userPasswordDentist !== $confirmUserPasswordDentist) {
        $data['success'] = false;
        $data['error'] = "Passwords did not match. Please try again.";
        echo json_encode($data);
        return;
    } else {
        $hash = password_hash($userPasswordDentist, PASSWORD_DEFAULT);
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
        $data['error'] = "A dentist with the same name and birthdate is already registered.";
        echo json_encode($data);
        return;
    }
    
    $insertId = insertAccount($conn, $email, $username, $hash);
    insertInfo($conn, $insertId, $lname, $fname, $mname, $suffix, $contnumber, $bdate, $specialist, $gender, $religion, $nationality, $aboutme, $address);
    $message = "A new dentist has been successfully added.";
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}

echo json_encode($data);