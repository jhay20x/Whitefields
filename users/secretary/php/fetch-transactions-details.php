<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$data = "";
$aptId;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $secId = fetchSecretaryID();
    $aptId = $_POST['aptId'];
    $pastAptId = $_POST['pastAptId'];
    
    $stmt = $conn->prepare("SELECT ar.id AS AppointmentID, ar.past_appoint_id AS PastAppointmentID, pr.name AS ProcedureName, pt.name AS PaymentType, tr.payment_ref_no AS PaymentRef, ar.appoint_status_id AS AppointStatus,
        CONCAT(si.fname , CASE WHEN si.mname = 'None' THEN ' ' ELSE CONCAT(' ' , si.mname , ' ') END , si.lname, 
        CASE WHEN si.suffix = 'None' THEN '' ELSE CONCAT(' ' , si.suffix) END ) AS SecretaryName, si.id AS SecID,
        CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
        CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS PatientName, SUM(tr.amount_paid) AS AmountPaid,
        tr.timestamp AS Timestamp, th.procedure_price AS TotalAmount, th.remaining_balance AS RemainingBalance, pr.id AS ProcedureID, th.patient_id AS PatientID
        FROM treatment_history th
        LEFT OUTER JOIN transactions tr ON tr.appointment_requests_id = th.appointment_requests_id AND tr.procedures_id = th.procedures_id
        LEFT OUTER JOIN payment_types pt ON pt.id = tr.payment_type_id
        LEFT OUTER JOIN appointment_requests ar ON ar.id = th.appointment_requests_id
        LEFT OUTER JOIN procedures pr ON pr.id = th.procedures_id
        LEFT OUTER JOIN secretary_info si ON si.id = tr.secretary_id
        LEFT OUTER JOIN patient_info pi ON pi.id = th.patient_id
        WHERE th.appointment_requests_id IN (?,?)
        GROUP BY ar.id, pr.name
        ORDER BY ar.id DESC;");

    $stmt->bind_param("ii", $aptId, $pastAptId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $data = [
        "AppointmentID" => null,
        "PaymentType" => null,
        "PaymentRef" => null,
        "SecretaryName" => null,
        "PatientName" => null,
        "Timestamp" => null,
        "AppointStatus" => null,
        "Procedures" => []
    ];

    $curdate = date("Y-m-d h:i A");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if (empty($data["AppointmentID"])) {
                $data["AppointmentID"] = $row["AppointmentID"];
                $data["PastAppointmentID"] = $row["PastAppointmentID"] ?? "N/A";
                $data["PaymentType"]   = $row["PaymentType"] ?? "N/A";
                $data["PaymentRef"]    = $row["PaymentRef"] ?? "N/A";
                $data["SecretaryName"] = ($row["SecID"] ?? $secId) == $secId ? "Me" : $row["SecretaryName"];
                $data["PatientID"]   = $row["PatientID"];
                $data["PatientName"]   = $row["PatientName"];
                $data["Timestamp"]     = empty($row["Timestamp"]) ? $curdate : $row["Timestamp"];
                $data["AppointStatus"]   = $row["AppointStatus"];
            }

            $data["Procedures"][] = [
                "AppointmentID" => $row["AppointmentID"],
                "ProcedureID" => $row["ProcedureID"],
                "ProcedureName" => $row["ProcedureName"],
                "AmountPaid"    => $row["AmountPaid"] ?? "0.00",
                "TotalAmount"    => $row["TotalAmount"] ?? "0.00",
                "RemainingBalance"    => $row["RemainingBalance"] ?? "0.00"
            ];
        }
    }
}


echo json_encode($data);