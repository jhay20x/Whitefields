<?php 
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$error;
$data = [];
$emailVerified;
$id;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $id = fetchDentistID();

    if (is_int($id)) {
        $stmt = $conn->prepare("SELECT ar.id AS ID, CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
            CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS Name, st.status_name,
            ar.start_datetime_str AS Date, ar.patient_id as PID, ar.past_appoint_id as PAID FROM appointment_requests ar
            LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
            LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
            WHERE ar.appoint_status_id = 1 AND ar.dentist_info_id = ?;");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    
        if ($result->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = [
                    "start" => $row["Date"], 
                    "title" => $row["Name"],
                    "url" => "appointment-list.php?id=" . $row["ID"] . "&pid=" . $row['PID'] . "&paid=" . $row['PAID'] . "&status=" . $row['status_name']
                ];
            }
        } else {
            $data = [];
        }
    } else {
        $error = $id;
    }
}

// function fetchRequest() {
// }

// function fetchDentistId() {
//     global $conn;
//     global $error;

//     $user_id = $_SESSION['user_id'];

//     $stmt = $conn->prepare("SELECT di.id AS id FROM dentist_info di WHERE di.accounts_id = ?;");
//     $stmt->bind_param("i", $user_id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $stmt->close();

//     if ($result->num_rows > 0) {        
//         $user = $result->fetch_assoc();
//         fetchRequest($user['id']);        
//     } else {
//         $error = "ID not found. Please update your personal information first.";
//     }
// }


echo json_encode($data);