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
		DATE(ar.start_datetime) AS Start_Date, TIME(ar.start_datetime) AS Start_Time,
        DATE(ar.approved_datetime) AS Approved_Date, TIME(ar.approved_datetime) AS Approved_Time, st.status_name AS Status,
        CONCAT(si.fname , CASE WHEN si.mname = 'None' THEN ' ' ELSE CONCAT(' ' , si.mname , ' ') END , si.lname, 
        CASE WHEN si.suffix = 'None' THEN '' ELSE CONCAT(' ' , si.suffix) END ) AS Approved_By,
        CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
        CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS Name, ar.id AS ID,
        CONCAT(di.fname , CASE WHEN di.mname = 'None' THEN ' ' ELSE CONCAT(' ' , di.mname , ' ') END , di.lname, 
        CASE WHEN di.suffix = 'None' THEN '' ELSE CONCAT(' ' , di.suffix) END ) AS Dentist, ar.oral_concern AS Concern
        FROM appointment_requests ar
        LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
        LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
        LEFT OUTER JOIN dentist_info di ON di.id = ar.dentist_info_id
        LEFT OUTER JOIN secretary_info si on si.accounts_id = ar.approved_by
        WHERE ar.id = ?;");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $data['Name'] = $row['Name'];
        $data['Request_Time'] = date('h:i A', strtotime($row['Request_Time']));
        $data['Request_Date'] = date("F d, Y", strtotime($row['Request_Date']));
        $data['Start_Time'] = date('h:i A', strtotime($row['Start_Time']));
        $data['Start_Date'] = date("F d, Y", strtotime($row['Start_Date']));
        $data['Approved_Time'] = date('h:i A', strtotime($row['Approved_Time']));
        $data['Approved_Date'] = date("F d, Y", strtotime($row['Approved_Date']));
        $data['Approved_By'] = $row['Approved_By'];
        $data['Dentist'] = $row['Dentist'];
        $data['Status'] = $row['Status'];
        $data['Concern'] = $row['Concern'];
    }
}


echo json_encode($data);