<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$error;
$data = [];
$id;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {    
    $id = fetchSecretaryID();

    $curdate = date("Y-m-d");

    $stmt = $conn->prepare("SELECT ar.id, 
        COUNT(CASE WHEN DATE(ar.start_datetime) = CURDATE() AND ar.appoint_status_id = 1
            THEN 1 
            END) AppointToday,
        COUNT(CASE WHEN ar.appoint_status_id = 4
            THEN 1
               END) AppointAll,
        (SELECT COUNT(di.id) FROM dentist_info di
        LEFT OUTER JOIN accounts ac
        ON di.accounts_id = ac.id
        WHERE ac.status != 0) TotalDentist,            
        (SELECT COUNT(pi.id) FROM patient_info pi) TotalPatient,
        (SELECT COUNT(DISTINCT ar.patient_id) FROM appointment_requests ar WHERE ar.appoint_status_id = 7 OR ar.appoint_status_id = 5) PatientToday,
        (SELECT SUM(tr.amount_paid) FROM transactions tr WHERE DATE(tr.timestamp) = ?) IncomeToday
        FROM appointment_requests ar;");

    $stmt->bind_param("s", $curdate);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $res = $conn->query("SELECT CONNECTION_ID()");
    $row = $res->fetch_row();
    header("X-DB-Conn-ID: " . $row[0]);
    $res->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $data['AppointToday'] = $row['AppointToday'];
        $data['PatientToday'] = $row['PatientToday'];
        $data['AppointAll'] = $row['AppointAll'];
        $data['TotalDentist'] = $row['TotalDentist'];
        $data['TotalPatient'] = $row['TotalPatient'];
        $data['IncomeToday'] = $row['IncomeToday'] ?? "0.00";
    }
}

echo json_encode($data);