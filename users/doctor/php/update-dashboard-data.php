<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$error;
$data = [];
$id;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {        
    $id = fetchDentistID();

    if (is_int($id)) {
        $stmt = $conn->prepare("SELECT ar.id, 
            COUNT(CASE WHEN DATE(ar.start_datetime) = CURDATE() AND ar.appoint_status_id = 1
                THEN 1 
                END) AppointToday, 
            COUNT(CASE WHEN ar.appoint_status_id = 1
                THEN 1
                END) AppointAll,         
            COUNT(DISTINCT CASE WHEN ar.dentist_info_id = ? AND (ar.appoint_status_id = 6 OR ar.appoint_status_id = 5 OR ar.appoint_status_id = 1)
                THEN ar.patient_id
                END) TotalPatient
            FROM appointment_requests ar
            WHERE ar.dentist_info_id = ?;");

        $stmt->bind_param('ii', $id,$id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $data['AppointToday'] = $row['AppointToday'];
            $data['AppointAll'] = $row['AppointAll'];
            $data['TotalPatient'] = $row['TotalPatient'];
        }
    } else {
        $data['AppointToday'] =  0;
        $data['AppointAll'] =  0;
        $data['TotalPatient'] =  0;
    }
}

echo json_encode($data);