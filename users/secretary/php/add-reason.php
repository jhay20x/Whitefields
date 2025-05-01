<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$data = [];
$message;
$error;

sleep(1);

function checkIfProcedureExist($conn, $deniedReason, $cancelReason, $reasonType) {
    if ($reasonType == "Denied") {
        $stmt = $conn->prepare("SELECT rr.reason FROM rejected_reasons rr WHERE rr.reason = ? AND status = 1");
        $stmt->bind_param("s", $deniedReason);
    } else {
        $stmt = $conn->prepare("SELECT cr.reason FROM cancel_reasons cr WHERE cr.reason = ? AND status = 1");
        $stmt->bind_param("s", $cancelReason);
    }

    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {        
        return true;
    } else {
        return false;
    }
}

function checkIfProcedureExistInactive($conn, $deniedReason, $cancelReason, $reasonType) {
    if ($reasonType == "Denied") {
        $stmt = $conn->prepare("SELECT rr.reason FROM rejected_reasons rr WHERE rr.reason = ? AND status = 1");
        $stmt->bind_param("s", $deniedReason);
    } else {
        $stmt = $conn->prepare("SELECT cr.reason FROM cancel_reasons cr WHERE cr.reason = ? AND status = 1");
        $stmt->bind_param("s", $cancelReason);
    }

    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {        
        return true;
    } else {
        return false;
    }
}

function insertInfo($conn, $deniedReason, $cancelReason, $reasonType) {
    if ($reasonType == "Denied") {
        $stmt = $conn->prepare("INSERT INTO `rejected_reasons`(`reason`, `status`) VALUES (?,1)");
        $stmt->bind_param("s", $deniedReason);
    } else {
        $stmt = $conn->prepare("INSERT INTO `cancel_reasons`(`reason`, `status`) VALUES (?,1)");
        $stmt->bind_param("s", $cancelReason);    }
    
    $stmt->execute();
	$stmt->close();
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {     
    $deniedReason = $_POST["deniedReason"] ?? "";
    $cancelReason = $_POST["cancelReason"] ?? "";
    $reasonType = $_POST["reasonType"] ?? "";

    if (checkIfProcedureExist($conn, $deniedReason, $cancelReason, $reasonType)) {
        $data['success'] = false;
        $data['error'] = "A reason with the same description has already been registered. Please try again.";
        echo json_encode($data);
        return;
    }

    if (checkIfProcedureExistInactive($conn, $deniedReason, $cancelReason, $reasonType)) {
        $data['success'] = false;
        $data['error'] = "A reason with the same description but is set as inactive has already been registered. Please try again.";
        echo json_encode($data);
        return;
    }
    
    insertInfo($conn, $deniedReason, $cancelReason, $reasonType);
    $message = "A new reason has been successfully added.";
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}


echo json_encode($data);