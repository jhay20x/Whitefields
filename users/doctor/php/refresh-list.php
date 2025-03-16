<?php
session_start();

require_once '../../../database/config.php';
include 'fetch-id.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $id = fetchDentistID();

    $stmt = $conn->prepare("SELECT DATE(ar.start_datetime) AS Date, TIME(ar.start_datetime) AS Time, st.status_name AS Status, 
        CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname) AS Name, ar.id AS ID, ar.patient_id AS PID
        FROM appointment_requests ar
        LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
        LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
        WHERE (ar.appoint_status_id = 1 OR ar.appoint_status_id = 6) AND ar.dentist_info_id = ?;");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = "";
    $status;

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $time = date('h:i A', strtotime($row['Time']));

            if ($row['Status'] == "Approved") {
                $status = "text-success";
            } else if ($row['Status'] == "Denied" || $row['Status'] == "Cancelled") {
                $status = "text-danger";
            } else if ($row['Status'] == "Evaluated") {
                $status = "text-secondary";
            } else {
                $status = "text-warning";
            }
            $data .= '
                <tr>
                    <td id="appointDate">' . $row['Date'] . '</td>
                    <td id="appointTime">' .  $time . '</td>
                    <td id="appointName">' . $row['Name'] . '</td>
                    <td id="appointStatus" class="' . $status . ' fw-bold">' . $row['Status'] . '</td>
                    <td class="appointID">
                        <button type="button" data-p-id="' . $row['PID'] . '" value="' . $row['ID'] . '" class="btn btn-sm btn-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#appointListModal">View
                        </button>
                    </td>
                </tr>
            ';
        }
    }
}

echo json_encode($data);