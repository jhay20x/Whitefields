<?php
session_start();

require_once '../../../database/config.php';
include 'fetch-id.php';

$data = "";
$aptId;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $aptId = $_POST['aptId'];
    
    $stmt = $conn->prepare("SELECT tc.id AS TransactionID, ar.id AS AppointmentID, pr.name AS ProcedureName, pt.name AS PaymentType, tc.amount_paid AS AmountPaid, tc.payment_ref_no AS PaymentRef,
        CONCAT(si.fname , CASE WHEN si.mname = 'None' THEN ' ' ELSE CONCAT(' ' , si.mname , ' ') END , si.lname, 
        CASE WHEN si.suffix = 'None' THEN '' ELSE CONCAT(' ' , si.suffix) END ) AS SecretaryName,
        CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
        CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS PatientName, tc.timestamp AS Timestamp
        FROM transactions tc
        LEFT OUTER JOIN payment_types pt ON pt.id = tc.payment_type_id
        LEFT OUTER JOIN appointment_requests ar ON ar.id = tc.appointment_requests_id
        LEFT OUTER JOIN procedures pr ON pr.id = tc.procedures_id
        LEFT OUTER JOIN secretary_info si ON si.id = tc.secretary_id
        LEFT OUTER JOIN patient_info pi ON pi.id = tc.patient_id
        WHERE tc.appointment_requests_id  = ?;");

    $stmt->bind_param("i", $aptId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $data = [
        "TransactionID" => null,
        "AppointmentID" => null,
        "PaymentType" => null,
        "PaymentRef" => null,
        "SecretaryName" => null,
        "PatientName" => null,
        "Timestamp" => null,
        "Procedures" => []
    ];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if (empty($data["TransactionID"])) {
                $data["TransactionID"] = $row["TransactionID"];
                $data["AppointmentID"] = $row["AppointmentID"];
                $data["PaymentType"]   = $row["PaymentType"];
                $data["PaymentRef"]    = $row["PaymentRef"] ?? "N/A";
                $data["SecretaryName"] = $row["SecretaryName"];
                $data["PatientName"]   = $row["PatientName"];
                $data["Timestamp"]     = $row["Timestamp"];
            }

            $data["Procedures"][] = [
                "ProcedureName" => $row["ProcedureName"],
                "AmountPaid"    => $row["AmountPaid"]
            ];
        }
    }
}

echo json_encode($data);