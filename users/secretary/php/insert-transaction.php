<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$data = [];
$message;
$error;

sleep(1);

function checkBalances($conn, $appointment_requests_id) {
    $stmt = $conn->prepare("SELECT remaining_balance FROM treatment_history WHERE remaining_balance > 0 AND appointment_requests_id = ?;");
    $stmt->bind_param("i",  $appointment_requests_id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        return 7;
    } else {
        return 5;
    }
}

function insertTransaction($conn, $patient_id, $secId, $payment_id, $payment_ref_no, $amount_paid, $remaining_balance, $timestamp, $appointment_requests_id, $procedures_id) {
    $stmt = $conn->prepare("INSERT INTO transactions (patient_id, secretary_id, payment_type_id, 
    payment_ref_no, amount_paid, remaining_balance, timestamp, appointment_requests_id, procedures_id) VALUES (?,?,?,?,?,?,?,?,?);");
    $stmt->bind_param("iiissssii",  $patient_id, $secId, $payment_id, $payment_ref_no, $amount_paid, $remaining_balance, $timestamp, $appointment_requests_id, $procedures_id);
    $stmt->execute();
	$stmt->close();
}

function updateTreatmentHistory($conn, $patient_id, $remaining_balance, $appointment_requests_id, $procedures_id) {
    $stmt = $conn->prepare("UPDATE treatment_history SET remaining_balance = ? WHERE patient_id = ? AND appointment_requests_id = ? AND procedures_id = ?;");
    $stmt->bind_param("isii",  $remaining_balance, $patient_id, $appointment_requests_id, $procedures_id);
    $stmt->execute();
	$stmt->close();
}

function updateAppointmentStatus($conn, $appointment_requests_id, $appoint_status_id) {
    $stmt = $conn->prepare("UPDATE appointment_requests SET appoint_status_id = ? WHERE id = ?;");
    $stmt->bind_param("ii", $appoint_status_id, $appointment_requests_id);
    $stmt->execute();
	$stmt->close();
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $patient_id = $_POST['patient_id'] ?? "";
    $secId = fetchSecretaryID();
    $payment_id = $_POST['paymentType'] ?? "";
    $payment_ref_no = $_POST['paymentRefNo'] ?? null;
    $timestamp = date('Y-m-d H:i:s');
    $appointment_requests_id = $_POST['appointment_requests_id'] ?? "";
    
    $amount_paid = $_POST['amount_paid'] ?? [];
    $remaining_balance = $_POST['remaining_balance'] ?? [];
    $procedures_id = $_POST['procedures_id'] ?? [];

    if (count($procedures_id) === count($amount_paid) && 
        count($procedures_id) === count($remaining_balance)) {
        for ($i = 0; $i < count($procedures_id); $i++) {
            $balance = $remaining_balance[$i] - $amount_paid[$i];

            insertTransaction($conn, $patient_id, $secId, $payment_id, $payment_ref_no, $amount_paid[$i], 
                $balance, $timestamp, $appointment_requests_id, $procedures_id[$i]
            );

            updateTreatmentHistory($conn, $patient_id, $balance, 
            $appointment_requests_id, $procedures_id[$i]
            );
        };
    };
    $appoint_status_id = checkBalances($conn, $appointment_requests_id);
    updateAppointmentStatus($conn, $appointment_requests_id, $appoint_status_id);
    $message = "Transaction succeeded.";
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}

echo json_encode($data);