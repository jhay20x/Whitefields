<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$message;
$data = [];

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $rid = $_POST["rid"] ?? "";
    $type = $_POST["type"] ?? "";
    $viewReasonName = $_POST["viewReasonName"] ?? "";
    $viewReasonStatus = $_POST["viewReasonStatus"] ?? "";
    $reason = "";

    if ($type == "Denied") {
        $stmt = $conn->prepare("UPDATE rejected_reasons SET reason = ?, status = ? WHERE id = ?;");
        $stmt->bind_param("sii", $viewReasonName, $viewReasonStatus, $rid);
        $reason = "deny";
    } else {
        $stmt = $conn->prepare("UPDATE cancel_reasons SET reason = ?, status = ? WHERE id = ?;");
        $stmt->bind_param("sii", $viewReasonName, $viewReasonStatus, $rid);
        $reason = "cancel";
    }
    
    if ($stmt->execute()){    
        $message = "The reason for $reason has been updated.";
    } else {
        $error = "The reason for $reason has not been updated. " + $stmt->error;
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