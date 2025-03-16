<?php 
session_start();

require_once '../../../database/config.php';

$error;
$data = [];
$emailVerified;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $stmt = $conn->prepare("SELECT ar.id AS ID, CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname) AS Name,
        ar.start_datetime_str AS Date, ar.patient_id as PID FROM appointment_requests ar
        LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
        WHERE ar.appoint_status_id = 1;");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = [
                "start" => $row["Date"], 
                "title" => $row["Name"],
                "url" => "appointment-list.php?id=" . $row["ID"] . "&pid=" . $row['PID']
            ];
        }
    } else {
        $data = [];
    }
}

echo json_encode($data);