<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$message;
$data = [];

sleep(1);

function checkHasProcedure($conn, $reason_id, $reason_type) {   
    if ($reason_type == "Denied") {
        $stmt = $conn->prepare("SELECT * FROM appointment_requests WHERE reason_id = ?;");
        $stmt->bind_param("i", $reason_id);
    } else {
        $stmt = $conn->prepare("SELECT * FROM appointment_requests WHERE cancel_reason_id = ?;");
        $stmt->bind_param("i", $reason_id);
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

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $reason_id = $_POST["reason_id"] ?? "";
    $reason_type = $_POST["reason_type"] ?? "";

    if (checkHasProcedure($conn, $reason_id, $reason_type)) {
        $data['success'] = false;
        $data['error'] = "This reason has been used. It cannot be removed.";
        echo json_encode($data);
        return;
    }
   
    if ($reason_type == "Denied") {
        $stmt = $conn->prepare("DELETE FROM rejected_reasons WHERE id = ?");
        $stmt->bind_param("i", $reason_id);
    } else {
        $stmt = $conn->prepare("DELETE FROM cancel_reasons WHERE id = ?");
        $stmt->bind_param("i", $reason_id);
    }
    
    if ($stmt->execute()){    
        $message = "The reason has been successfully removed.";
    } else {
        $error = "The reason has not been removed. " + $stmt->error;
    }
    
    $stmt->close();
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}

echo json_encode($data);