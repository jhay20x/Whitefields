<?php
session_start();

global $conn;
require_once '../../../database/config.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $stmt = $conn->prepare("SELECT ar.request_datetime AS RequestDateTime, st.status_name AS Status, ar.start_datetime AS ApprovedDateTime,
        CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname, 
        CASE WHEN pi.suffix = 'None' THEN '' ELSE CONCAT(' ' , pi.suffix) END ) AS Name, ar.id AS ID, ar.patient_id AS PID
        FROM appointment_requests ar
        LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
        LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
        WHERE ar.appoint_status_id = 4;");
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    $data = "";
    
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $requesttime = date('Y-m-d', strtotime($row['RequestDateTime']));
            $approvedtime = date('Y-m-d', strtotime($row['ApprovedDateTime']));
            $data .= '
                <tr>                                            
                    <td id="appointID">' . $row['ID'] . '</td>
                    <td id="appointRequestDate">' . $requesttime . '</td>
                    <td id="appointName">' . $row['Name'] . '</td>
                    <td id="appointApprovedDate">' . $approvedtime . '</td>
                    <td id="appointStatus" class="text-warning fw-bold">' . $row['Status'] . '</td>
                    <td class="appointID">
                        <button type="button" data-p-id="' . $row['PID'] . '" value="' . $row['ID'] . '" class="btn btn-sm btn-outline-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#appointRequestModal">View
                        </button>
                    </td>
                </tr>
            ';
        }
    }
}
echo json_encode($data);