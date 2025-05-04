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

function insertTransaction($conn, $patient_id, $secInfoId, $payment_id, $payment_ref_no, $patientTransactionAmountPaid, $patientTransactionRemainingBalance, $lastPaidDate, $appointment_requests_id, $patientProcedure) {
    $stmt = $conn->prepare("INSERT INTO transactions (patient_id, secretary_id, payment_type_id, payment_ref_no, amount_paid, remaining_balance, timestamp, appointment_requests_id, procedures_id) VALUES (?,?,?,?,?,?,?,?,?);");
    $stmt->bind_param("iiissssii",  $patient_id, $secInfoId, $payment_id, $payment_ref_no, $patientTransactionAmountPaid, $patientTransactionRemainingBalance, $lastPaidDate, $appointment_requests_id, $patientProcedure);
    $stmt->execute();
	$stmt->close();
}

function insertTreatmentHistory($conn, $patient_id, $dentist_id, $appointment_requests_id, $patientToothNo, $dentistNote, $patientProcedure, $patientTransactionPrice, $patientTransactionRemainingBalance, $datetime) {
    $stmt = $conn->prepare("INSERT INTO treatment_history (patient_id, dentist_id, appointment_requests_id, tooth_number, dentist_note, procedures_id, procedure_price, remaining_balance, timestamp) VALUES (?,?,?,?,?,?,?,?,?);");
    $stmt->bind_param("iiiisssss", $patient_id, $dentist_id, $appointment_requests_id, $patientToothNo, $dentistNote, $patientProcedure, $patientTransactionPrice, $patientTransactionRemainingBalance, $datetime);
    $stmt->execute();
	$stmt->close();
}

function insertRequest($conn, $patient_id, $dentist_id, $requestdatetime, $datetime, $approveddatetime, $secAccountId, $datetimestr, $appointstatus, $concern, $completeddate) {
    if ($appointstatus == 5) {
        $stmt = $conn->prepare("INSERT INTO appointment_requests(patient_id, dentist_info_id, request_datetime, start_datetime, approved_datetime, verdict_by, start_datetime_str, appoint_status_id, oral_concern, completed_datetime) VALUES (?,?,?,?,?,?,?,?,?,?);");
        $stmt->bind_param("iisssisiss", $patient_id, $dentist_id, $requestdatetime, $datetime, $approveddatetime, $secAccountId, $datetimestr, $appointstatus, $concern, $completeddate);
    } else {
        $stmt = $conn->prepare("INSERT INTO appointment_requests(patient_id, dentist_info_id, request_datetime, start_datetime, approved_datetime, verdict_by, start_datetime_str, appoint_status_id, oral_concern) VALUES (?,?,?,?,?,?,?,?,?);");
        $stmt->bind_param("iisssisis", $patient_id, $dentist_id, $requestdatetime, $datetime, $approveddatetime, $secAccountId, $datetimestr, $appointstatus, $concern);        
    }

    $stmt->execute();
    $insertedId = $conn->insert_id;
    $stmt->close();

    return $insertedId;
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $secAccountId = $_SESSION['user_id'];
    $secInfoId = fetchSecretaryID();
    
    // Insert Appointment
    $patient_id = $_POST['selectPatientId'] ?? "";
    $dentist_id = $_POST['selectDentistId'] ?? "";    
    $requestdatetime = date('Y-m-d H:i:s');
    $date = $_POST['date'] ?? "";
    $time_str = $_POST['timeHour'] . ":" . $_POST['timeMinute'] . ' ' . $_POST['timeAMPM'];
    $time = date("H:i:s", strtotime($time_str));
    $datetime = date('Y-m-d H:i:s', strtotime("$date $time"));
    $datetimestr = $date . 'T' . date('H:i:s', strtotime($time));
    $concern = $_POST['concern'] ?? "";
    
    // Insert Transaction    
    $payment_id = 1;
    $payment_ref_no = null;
    $lastPaidDate = $_POST['lastPaidDate'] ?? "";
    $patientTransactionAmountPaid = $_POST['patientTransactionAmountPaid'] ?? [];
    $patientTransactionRemainingBalance = $_POST['patientTransactionRemainingBalance'] ?? [];

    $allPaid = !empty($patientTransactionRemainingBalance) && array_sum($patientTransactionRemainingBalance) == 0;

    $appointstatus = $allPaid ? 5 : 7;

    // Insert Treatment History    
    $patientToothNo = $_POST['patientToothNo'] ?? [];
    $patientProcedure = $_POST['patientProcedure'] ?? [];
    $patientTransactionPrice = $_POST['patientTransactionPrice'] ?? [];
    $dentistNote = $_POST['dentistNote'];

    if (count($patientProcedure) === count($patientTransactionAmountPaid) && 
        count($patientProcedure) === count($patientTransactionRemainingBalance)) {
        
        $conn->begin_transaction();

        try {
            $appointment_requests_id = insertRequest($conn, $patient_id, $dentist_id, 
            $requestdatetime, $datetime, $requestdatetime, 
            $secAccountId, $datetimestr, $appointstatus, 
            $concern, $requestdatetime);

            for ($i = 0; $i < count($patientProcedure); $i++) {
                // $balance = $patientTransactionRemainingBalance[$i] - $patientTransactionAmountPaid[$i];

                insertTransaction($conn, $patient_id, $secInfoId, $payment_id, 
                $payment_ref_no, $patientTransactionAmountPaid[$i], 
                $patientTransactionRemainingBalance[$i], $lastPaidDate, 
                $appointment_requests_id, $patientProcedure[$i]);

                insertTreatmentHistory($conn, $patient_id, $dentist_id, 
                $appointment_requests_id, $patientToothNo[$i], 
                $dentistNote, $patientProcedure[$i], 
                $patientTransactionPrice[$i], 
                $patientTransactionRemainingBalance[$i], 
                $datetime);
            };

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
        
        $message = "Patient record has been saved successfully.";
    };
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}


echo json_encode($data);