<?php
session_start();

include '../../../database/config.php';
include 'fetch-id.php';

$data = [];

sleep(1);

function checkTreatmentRecord($conn, $aptId) {    
    $stmt = $conn->prepare("SELECT th.appointment_requests_id FROM treatment_history th WHERE th.appointment_requests_id = ?;");
    $stmt->bind_param("i", $aptId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function updateAppointmentStatus($conn, $aptId) {
    $stmt = $conn->prepare("UPDATE `appointment_requests` SET `appoint_status_id` = 6 WHERE `id` = ?");
    $stmt->bind_param("i", $aptId);
    $stmt->execute();
}

function insertTreatmentRecord($conn, $pid, $dentist_id, $aptId, $patientToothNo, $dentistNote, $procedures, $timestamp) {
    $data = [];

    $stmt = $conn->prepare("INSERT INTO `treatment_history`(`patient_id`, `dentist_id`, `appointment_requests_id`, `tooth_number`, `dentist_note`, `procedures`, `timestamp`) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("iiissss", $pid, $dentist_id, $aptId, $patientToothNo, $dentistNote, $procedures, $timestamp);
    
    if ($stmt->execute()) {
        updateAppointmentStatus($conn, $aptId);
        $data['success'] = true;
        $data['message'] = "Treatment record has been successfully saved.";
    } else {
        $data['success'] = false;
        $data['error'] = $stmt->error;
    }

    return $data;
}

function updateTreatmentRecord($conn, $patientToothNo, $dentistNote, $procedures, $aptId) {
    $data = [];

    $stmt = $conn->prepare("UPDATE `treatment_history` SET `tooth_number` = ?, `dentist_note` = ?, `procedures` = ? WHERE `appointment_requests_id` = ?");
    $stmt->bind_param("sssi", $patientToothNo, $dentistNote, $procedures, $aptId);
    
    if ($stmt->execute()) {
        $data['success'] = true;
        $data['message'] = "Treatment record has been successfully updated.";
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
    $proceduresList = $_POST["procedure"] ?? [];
    $patientToothNo = !empty($_POST["patientToothNo"]) ? $_POST["patientToothNo"] : NULL;
    $timestamp = date('Y-m-d H:i:s', time());

    $procedures = implode("-", $proceduresList);

    if (checkTreatmentRecord($conn, $aptId)) {
        $data = updateTreatmentRecord($conn, $patientToothNo, $dentistNote, $procedures, $aptId);
    } else {
        $data = insertTreatmentRecord($conn, $pid, $dentist_id, $aptId, $patientToothNo, $dentistNote, $procedures, $timestamp);
    }
}

echo json_encode($data);