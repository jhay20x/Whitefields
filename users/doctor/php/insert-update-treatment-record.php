<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$data = [];

sleep(1);

function checkTreatmentRecord($conn, $aptId) {    
    $stmt = $conn->prepare("SELECT th.appointment_requests_id FROM treatment_history th WHERE th.appointment_requests_id = ?;");
    $stmt->bind_param("i", $aptId);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function fetchTimestamp($conn, $aptId) {    
    $stmt = $conn->prepare("SELECT th.timestamp FROM treatment_history th WHERE th.appointment_requests_id = ?;");
    $stmt->bind_param("i", $aptId);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        return $row["timestamp"];
    } else {
        return false;
    }
}

function updateTreatmentRecord($conn, $pid, $dentist_id, $aptId, $patientToothNo, $dentistNote, $proceduresList, $proceduresPrice, $prevTimestamp) {
    $data = [];

    $stmt = $conn->prepare("DELETE FROM treatment_history WHERE appointment_requests_id = ?");
    $stmt->bind_param("i", $aptId);
    $stmt->execute();
    $stmt->close();

    $allSuccess = true;

    $stmt = $conn->prepare("INSERT INTO `treatment_history`(`patient_id`, `dentist_id`, `appointment_requests_id`, `tooth_number`, `dentist_note`, `procedures_id`, `procedure_price`,  `remaining_balance`, `timestamp`) VALUES (?,?,?,?,?,?,?,?,?)");
    
    for ($i = 0; $i < count($proceduresList); $i++) {
        $stmt->bind_param("iiissssss", $pid, $dentist_id, $aptId, $patientToothNo[$i], $dentistNote, $proceduresList[$i], $proceduresPrice[$i], $proceduresPrice[$i], $prevTimestamp);
        
        if (!$stmt->execute()) {
            $allSuccess = false;
            $data['error'] = $stmt->error;
            break;
        }
    }
    
	$stmt->close();

    if ($allSuccess) {
        $data['success'] = true;
        $data['message'] = "Treatment record has been successfully updated.";
    } else {
        $data['success'] = false;
        $data['error'] = $stmt->error;
    }
    return $data;
}

function updateAppointmentStatus($conn, $aptId) {
    $stmt = $conn->prepare("UPDATE `appointment_requests` SET `appoint_status_id` = 6 WHERE `id` = ?");
    $stmt->bind_param("i", $aptId);
    $stmt->execute();
	$stmt->close();
}

function insertTreatmentRecord($conn, $pid, $dentist_id, $aptId, $patientToothNo, $dentistNote, $proceduresList, $proceduresPrice, $timestamp) {
    $data = [];
    $allSuccess = true;

    $stmt = $conn->prepare("INSERT INTO `treatment_history`(`patient_id`, `dentist_id`, `appointment_requests_id`, `tooth_number`, `dentist_note`, `procedures_id`, `procedure_price`, `remaining_balance`, `timestamp`) VALUES (?,?,?,?,?,?,?,?,?)");
    
    for ($i = 0; $i < count($proceduresList); $i++) {
        $stmt->bind_param("iiissssss", $pid, $dentist_id, $aptId, $patientToothNo[$i], $dentistNote, $proceduresList[$i], $proceduresPrice[$i], $proceduresPrice[$i], $timestamp);
        
        if (!$stmt->execute()) {
            $allSuccess = false;
            $data['error'] = $stmt->error;
            break;
        }
    }
    
	$stmt->close();

    if ($allSuccess) {
        updateAppointmentStatus($conn, $aptId);
        $data['success'] = true;
        $data['message'] = "Treatment record has been successfully saved.";
    } else {
        $data['success'] = false;
        $data['error'] = $stmt->error;
    }
    return $data;
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $pid = $_SESSION["pid"] ?? "";
    $aptId = $_SESSION["aptId"] ?? "";
    $dentist_id = fetchDentistID() ?? "";
    $dentistNote = $_POST["dentistNote"] ?? "";
    $patientToothNo = $_POST["patientToothNo"] ?? [];
    $proceduresList = $_POST["patientProcedure"] ?? [];
    $proceduresPrice = $_POST["patientPrice"] ?? [];

    $timestamp = date('Y-m-d H:i:s', time());
    $prevTimestamp = fetchTimestamp($conn, $aptId);

    if (checkTreatmentRecord($conn, $aptId)) {
        $data = updateTreatmentRecord($conn, $pid, $dentist_id, $aptId, $patientToothNo, $dentistNote, $proceduresList, $proceduresPrice, $prevTimestamp);
    } else {
        $data = insertTreatmentRecord($conn, $pid, $dentist_id, $aptId, $patientToothNo, $dentistNote, $proceduresList, $proceduresPrice, $timestamp);
    }
}

echo json_encode($data);