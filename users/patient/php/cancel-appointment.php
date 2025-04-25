<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$data = [];
$message;
$error;

date_default_timezone_set('Asia/Manila');

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $appoint_id = $_POST['appoint_id'];
    $cancel_reason_id = ($_POST['cancel_reason_id'] == NULL || $_POST['cancel_reason_id'] == "") ? NULL : $_POST['cancel_reason_id'];
    $cancel_reason_other = ($_POST['cancel_reason_other'] == NULL || $_POST['cancel_reason_other'] == "") ? NULL : $_POST['cancel_reason_other'];
    $appoint_status_id = 3;
    $cancel_datetime = date('Y/m/d H:i:s A', time());
    
    $stmt = $conn->prepare("UPDATE `appointment_requests` SET `appoint_status_id` = ?, `cancel_reason_id` = ?, `cancel_reason_other` = ?, `cancel_datetime` = ? WHERE `id` = ?");
    $stmt->bind_param("iissi", $appoint_status_id, $cancel_reason_id, $cancel_reason_other, $cancel_datetime, $appoint_id);
    
    if ($stmt->execute()) {
        $message = "Your appointment has been successfully canceled.";
    } else {
        $error = "Your appointment has not been successfully canceled.";
    }
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
