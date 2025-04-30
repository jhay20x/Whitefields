<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $id = fetchDentistID();

    $stmt = $conn->prepare("SELECT DATE(ar.start_datetime) AS Date, TIME(ar.start_datetime) AS Time, st.status_name AS Status, 
        CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
        CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS Name, ar.id AS ID, ar.patient_id AS PID, ar.past_appoint_id AS PAID
        FROM appointment_requests ar
        LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
        LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
        WHERE (ar.appoint_status_id = 1 OR ar.appoint_status_id = 6 OR ar.appoint_status_id = 5 OR ar.appoint_status_id = 7) AND ar.dentist_info_id = ?
        ORDER BY Date DESC, Time ASC;");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    $data = "";
    $status;

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $time = date('h:i A', strtotime($row['Time']));

            if ($row['Status'] == "Approved") {
                $status = "text-success";
            } else if ($row['Status'] == "Denied" || $row['Status'] == "Cancelled" || $row['Status'] == "Partially Paid") {
                $status = "text-danger";
            } else if ($row['Status'] == "Completed") {
                $status = "text-secondary";
            } else {
                $status = "text-warning";
            }
            $data .= '
                <tr>
                    <td id="appointID">' . $row['ID'] . '</td>
                    <td id="appointDate">' . $row['Date'] . '</td>
                    <td id="appointTime">' .  $time . '</td>
                    <td id="appointName">' . $row['Name'] . '</td>
                    <td id="appointStatus" class="' . $status . ' fw-bold">' . $row['Status'] . '</td>
                    <td class="appointID">
                        <button type="button" data-past-apt-id="' . ($row['PAID'] ?? 0) . '" data-p-id="' . $row['PID'] . '" value="' . $row['ID'] . '" class="btn btn-sm btn-outline-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#appointListModal">View
                        </button>
                    </td>
                </tr>
            ';
        }
    }
}


echo json_encode($data);