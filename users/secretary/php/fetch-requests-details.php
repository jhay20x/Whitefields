<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$data = [];
$id;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("SELECT DATE(ar.request_datetime) AS Request_Date, TIME(ar.request_datetime) AS Request_Time,
        ar.id AS AptId, ar.past_appoint_id AS PastAptId,
		DATE(ar.start_datetime) AS Start_Date, TIME(ar.start_datetime) AS Start_Time,
        DATE(ar.cancel_datetime) AS Cancel_Date, TIME(ar.cancel_datetime) AS Cancel_Time,
        DATE(th.timestamp) AS Examined_Date, TIME(th.timestamp) AS Examined_Time,
        DATE(ar.completed_datetime) AS Completed_Date, TIME(ar.completed_datetime) AS Completed_Time,
        DATE(tr.timestamp) AS Partial_Date, TIME(tr.timestamp) AS Partial_Time,
        DATE(ar.approved_datetime) AS Approved_Date, TIME(ar.approved_datetime) AS Approved_Time, st.status_name AS Status,
        CASE WHEN pi.accounts_id = ar.verdict_by THEN 'The Client' ELSE CONCAT(si.fname , CASE WHEN si.mname = 'None' THEN ' ' ELSE CONCAT(' ' , si.mname , ' ') END , si.lname, 
        CASE WHEN si.suffix = 'None' THEN '' ELSE CONCAT(' ' , si.suffix) END ) END AS Cancelled_By,
        CONCAT(si.fname , CASE WHEN si.mname = 'None' THEN ' ' ELSE CONCAT(' ' , si.mname , ' ') END , si.lname, 
        CASE WHEN si.suffix = 'None' THEN '' ELSE CONCAT(' ' , si.suffix) END ) AS Approved_By, ar.verdict_by AS Approved_ID,
        CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
        CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS Name,
        CONCAT(di.fname , CASE WHEN di.mname = 'None' THEN ' ' ELSE CONCAT(' ' , di.mname , ' ') END , di.lname, 
        CASE WHEN di.suffix = 'None' THEN '' ELSE CONCAT(' ' , di.suffix) END ) AS Dentist, di.id as Dentist_ID, ar.oral_concern AS Concern,
        rr.reason AS Reason, ar.reason_other AS ReasonOther,
        cr.reason AS CancelReason, ar.cancel_reason_other AS CancelReasonOther
        FROM appointment_requests ar
        LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
        LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
        LEFT OUTER JOIN dentist_info di ON di.id = ar.dentist_info_id
        LEFT OUTER JOIN secretary_info si ON si.accounts_id = ar.verdict_by
        LEFT OUTER JOIN rejected_reasons rr ON rr.id = ar.reason_id
        LEFT OUTER JOIN cancel_reasons cr ON cr.id = ar.cancel_reason_id
        LEFT OUTER JOIN treatment_history th ON th.appointment_requests_id  = ar.id
        LEFT OUTER JOIN transactions tr ON tr.appointment_requests_id  = ar.id
        WHERE ar.id = ?
        GROUP BY tr.timestamp
        ORDER BY tr.timestamp DESC;");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $data['Name'] = $row['Name'];
        $data['AptId'] = $row['AptId'];
        $data['PastAptId'] = $row['PastAptId'] ?? "N/A";
        $data['Request_Time'] = date('h:i A', strtotime($row['Request_Time']));
        $data['Request_Date'] = $row['Request_Date'];
        $data['Examined_Time'] = date('h:i A', strtotime($row['Examined_Time']));
        $data['Examined_Date'] = $row['Examined_Date'];
        $data['Completed_Time'] = date('h:i A', strtotime($row['Completed_Time']));
        $data['Completed_Date'] = $row['Completed_Date'];
        $data['Partial_Time'] = date('h:i A', strtotime($row['Partial_Time']));
        $data['Partial_Date'] = $row['Partial_Date'];
        $data['Start_Time'] = date('h:i A', strtotime($row['Start_Time']));
        $data['Start_Date'] = $row['Start_Date'];
        $data['Approved_Time'] = date('h:i A', strtotime($row['Approved_Time']));
        $data['Approved_Date'] = $row['Approved_Date'];
        $data['Cancel_Time'] = date('h:i A', strtotime($row['Cancel_Time']));
        $data['Cancel_Date'] = $row['Cancel_Date'];
        $data['Approved_By'] = ($row['Approved_ID'] == $_SESSION['user_id']) ? "Me" : $row['Approved_By'];
        $data['Cancelled_By'] = $row['Cancelled_By'];
        $data['Reason'] = $row['Reason'];
        $data['ReasonOther'] = $row['ReasonOther'];
        $data['CancelReason'] = $row['CancelReason'];
        $data['CancelReasonOther'] = $row['CancelReasonOther'];
        $data['Dentist'] = $row['Dentist'];
        $data['Dentist_ID'] = $row['Dentist_ID'];
        $data['Status'] = $row['Status'];
        $data['Concern'] = $row['Concern'];
    }
}

echo json_encode($data);