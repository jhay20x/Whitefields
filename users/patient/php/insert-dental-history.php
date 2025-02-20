<?php
session_start();

include '../../../database/config.php';
include 'fetch-id.php';

$data = [];
$message;
$error;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $patient_id = fetchPatientID();
    $lastDental = $_POST["lastDentalVisit"];
    $prevDentist = $_POST["prevDentist"];

    $stmt = $conn->prepare("INSERT INTO `dental_history`(`patient_id`, `prev_dentist`, `last_dental`) VALUES (?,?,?)");
    $stmt->bind_param("iss", $patient_id, $prevDentist, $lastDental);

    if ($stmt->execute()) {
        $message = "Your dental history has been successfully updated.";
    } else {
        $error = "Your dental history has failed to be updated. Please try again.";
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