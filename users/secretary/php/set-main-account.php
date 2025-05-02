<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$message;
$data = [];

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $newMain = $_POST['newMain'];
    $curMain = fetchCurrentMain($conn) ?? "";

    if (!checkAccountStatus($conn, $newMain)) {
        $data['success'] = false;
        $data['error'] = "This account is set as inactive. It cannot be set as the main account.";
        echo json_encode($data);
        return;
    }

    updateCurrentMain($conn, $curMain);

    $stmt = $conn->prepare("UPDATE accounts SET is_main = 1 WHERE id = ?;");
    $stmt->bind_param("i",$newMain);
    
    if ($stmt->execute()){    
        $message = "Main Account has been updated.";
    } else {
        $error = "Main Account has not been updated. " + $stmt->error;
    }

    $stmt->close();
}

function updateCurrentMain($conn, $curMain) {
    $stmt = $conn->prepare("UPDATE accounts SET is_main = NULL WHERE id = ?;");
    $stmt->bind_param("i",$curMain);
    $stmt->execute();
	$stmt->close();
}

function checkAccountStatus($conn, $newMain) {
    $stmt = $conn->prepare("SELECT status FROM accounts WHERE id = ? AND status != 0;");
    $stmt->bind_param("i", $newMain);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['status'] == 1;
    } else {            
        return false;
    }
}

function fetchCurrentMain($conn) {
    $stmt = $conn->prepare("SELECT ac.id, ac.is_main FROM accounts ac WHERE ac.is_main IS NOT NULL;");
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['id'];
    } else {            
        return false;
    }
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}


echo json_encode($data);