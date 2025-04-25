<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$data = [];
$transId;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {    
    $transId = $_POST["transId"] ?? "";

    $stmt = $conn->prepare("SELECT tr.id AS TransactionID, tr.appointment_requests_id AS AppointmentID, pr.name AS ProcedureName, pt.name AS PaymentType,
        CONCAT(si.fname , CASE WHEN si.mname = 'None' THEN ' ' ELSE CONCAT(' ' , si.mname , ' ') END , si.lname, 
        CASE WHEN si.suffix = 'None' THEN '' ELSE CONCAT(' ' , si.suffix) END ) AS SecretaryName,
        CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
        CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS PatientName,
        tr.amount_paid AS AmountPaid, tr.remaining_balance AS RemainingBalance, tr.timestamp AS Timestamp,
        tr.payment_ref_no AS PaymentRef
        FROM transactions tr 
        LEFT OUTER JOIN secretary_info si ON si.id = tr.secretary_id
        LEFT OUTER JOIN payment_types pt ON pt.id = tr.payment_type_id
        LEFT OUTER JOIN patient_info pi ON pi.id = tr.patient_id
        LEFT OUTER JOIN procedures pr ON pr.id = tr.procedures_id
        WHERE tr.id = ?;");
    $stmt->bind_param('i',$transId);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $datetime = date("Y-m-d h:i A", strtotime($row['Timestamp']));
        
        $data['PatientName'] = $row['PatientName'];
        $data['TransactionID'] = $row['TransactionID'];
        $data['AppointmentID'] = $row['AppointmentID'];
        $data['ProcedureName'] = $row['ProcedureName'];
        $data['AmountPaid'] = $row['AmountPaid'];
        $data['RemainingBalance'] = $row['RemainingBalance'];
        $data['Timestamp'] = $datetime;
        $data['PaymentType'] = $row['PaymentType'];
        $data['PaymentRef'] = $row['PaymentRef'] ?? "N/A";
        $data['SecretaryName'] = $row['SecretaryName'];
    }
}

echo json_encode($data);