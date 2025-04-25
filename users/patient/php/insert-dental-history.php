<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$data = [];
$message;
$error;

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $patient_id = fetchPatientID();
    $hasHistory = [];

    $lastDental = $_POST["lastDentalVisit"];
    $prevDentist = $_POST["prevDentist"];
    $timestamp = date('Y-m-d H:i:s', time());
    $curdate = date("Y-m-d");
    $remarks;
    $insertId;

    if (empty($prevDentist)) {
        $error = "Dentist name is empty. Please try again.";
        return false;
    }

    if (empty($lastDental)) {
        $error = "Last dental visit is empty. Please try again.";
        return false;
    }
    
    $hasHistory = checkHistory($patient_id);

    if ($lastDental > $curdate) {
        $error = "You have set an invalid date. Please try again with a different date.";
    } else if (($hasHistory['hasId'] ?? null) === null) {
        $remarks = "Added: $prevDentist";
        insertHistory($patient_id, $prevDentist, $lastDental,$timestamp);
        insertHistoryLogs($patient_id, $remarks, $lastDental, $timestamp, $insertId);
        $message = "Your dental history has been successfully saved.";
    } else if (($hasHistory['hasId'] ?? null) && ($hasHistory['last_dental'] ?? null) !== $lastDental) {
        $remarks = "Added: $prevDentist";
        updateHistory($patient_id, $prevDentist, $lastDental,$timestamp);
        insertHistoryLogs($patient_id, $remarks, $lastDental, $timestamp, $hasHistory['id']);
        $message = "Your dental history has been successfully saved.";
    } else {
        $remarks = "Updated to: $prevDentist";
        updateHistory($patient_id, $prevDentist, $lastDental,$timestamp);
        insertHistoryLogs($patient_id, $remarks, $lastDental, $timestamp, $hasHistory['id']);
        $message = "Your dental history has been successfully updated.";
    }
}

function insertHistory($patient_id, $prevDentist, $lastDental, $timestamp) {
    global $conn, $message, $error, $insertId;

    $stmt = $conn->prepare("INSERT INTO `dental_history`(`patient_id`, `prev_dentist`, `last_dental`, `timestamp`) VALUES (?,?,?,?)");
    $stmt->bind_param("isss", $patient_id, $prevDentist, $lastDental, $timestamp);

    if ($stmt->execute()) {
        $insertId = $conn->insert_id;
    } else {
        $error = "Your dental history has failed to be saved. Please try again.";
    }  
}

function updateHistory($patient_id, $prevDentist, $lastDental, $timestamp) {
    global $conn, $message, $error;

    $stmt = $conn->prepare("UPDATE dental_history SET prev_dentist = ?, last_dental = ?, timestamp = ? WHERE patient_id = ?");
    $stmt->bind_param("sssi",  $prevDentist, $lastDental, $timestamp, $patient_id);

    if ($stmt->execute()) {
        $message = "Your dental history has been successfully updated.";
    } else {
        $error = "Your dental history has failed to be updated. Please try again.";
    }  
}

function insertHistoryLogs($patient_id, $remarks, $lastDental, $timestamp, $insertId) {
    global $conn, $message, $error;

    $stmt = $conn->prepare("INSERT INTO `dental_history_logs`(`patient_id`, `remarks`, `visit_date`, `timestamp`, `dental_history_id`) VALUES (?,?,?,?,?)");
    $stmt->bind_param("isssi", $patient_id, $remarks, $lastDental, $timestamp, $insertId);
    $stmt->execute();
	$stmt->close();
}

// function checkHistoryDate($patient_id, $lastDental) {
//     global $conn;

//     $stmt = $conn->prepare("SELECT * FROM `dental_history` WHERE last_dental = ? AND patient_id = ?");
//     $stmt->bind_param('si',$lastDental,$patient_id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//	   $stmt->close();
    
//     if ($result->num_rows > 0) {
//         $row = $result->fetch_assoc();

//         return $row['id'];
//     } else {
//         return false;
//     }
// }

function checkHistory($patient_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM `dental_history` WHERE patient_id = ?");
    $stmt->bind_param('i',$patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    $data = ['hasId', 'id', 'last_dental'];

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $data = [];

        $data['hasId'] = $row['id'] !== null ? 1 : 0;
        $data['id'] = $row['id'];
        $data['last_dental'] = $row['last_dental'];
        
        return $data;
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