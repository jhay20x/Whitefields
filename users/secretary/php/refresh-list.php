<?php
session_start();

require_once '../../../database/config.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $stmt = $conn->prepare("SELECT ar.request_datetime AS RequestDateTime, st.status_name AS Status, ar.start_datetime AS ApprovedDateTime,
    CONCAT(pi.fname , CASE WHEN pi.mname = 'None' THEN ' ' ELSE CONCAT(' ' , pi.mname , ' ') END , pi.lname) AS Name, ar.id AS ID, ar.patient_id AS PID
    FROM appointment_requests ar
    LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
    LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
    WHERE ar.appoint_status_id = 4;");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = "";
    
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $requesttime = date('Y-m-d h:i A', strtotime($row['RequestDateTime']));
            $approvedtime = date('Y-m-d h:i A', strtotime($row['ApprovedDateTime']));
            $data .= '<tr>
                <td id="appointRequestDate">' . $requesttime . '</td>
                <td id="appointName">' . $row['Name'] . '</td>
                <td id="appointApprovedDate">' . $approvedtime . '</td>
                <td id="appointStatus" class="text-warning fw-bold">' . $row['Status'] . '</td>
                <td class="appointID">
                <button type="button" value="' . $row['ID'] . '" class="btn btn-sm btn-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#appointRequestModal">View
                </button>
                </td>
            </tr>
            ';
        }
    }
}

echo json_encode($data);