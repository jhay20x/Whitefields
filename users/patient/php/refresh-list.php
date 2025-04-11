<?php
session_start();

require_once '../../../database/config.php';
include 'fetch-id.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $id = fetchPatientID();

    // $stmt = $conn->prepare("SELECT DATE(ar.request_datetime) AS RequestDate, TIME(ar.request_datetime) AS RequestTime, 
    // DATE(ar.start_datetime) AS ApprovedDate, TIME(ar.start_datetime) AS ApprovedTime, 

    $stmt = $conn->prepare("SELECT ar.request_datetime AS RequestDateTime, 
        CONCAT(di.fname , CASE WHEN di.mname = 'None' THEN ' ' ELSE CONCAT(' ' , di.mname , ' ') END , di.lname, 
        CASE WHEN di.suffix = 'None' THEN '' ELSE CONCAT(' ' , di.suffix) END ) AS Dentist,
        ar.start_datetime AS ApprovedDateTime, 
        st.status_name AS Status, ar.id AS ID
        FROM appointment_requests ar
        LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
        LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id        
        LEFT OUTER JOIN dentist_info di ON di.id = ar.dentist_info_id
        WHERE ar.patient_id = ?
        ORDER BY ar.id DESC;");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    $status = "";
    $data = "";

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $requesttime = date('Y-m-d', strtotime($row['RequestDateTime']));
            $approvedtime = date('Y-m-d', strtotime($row['ApprovedDateTime']));

            if ($row['Status'] == "Approved") {
                $status = "text-success";
            } else if ($row['Status'] == "Denied" || $row['Status'] == "Cancelled") {
                $status = "text-danger";
            } else if ($row['Status'] == "Evaluated") {
                $status = "text-secondary";
            } else {
                $status = "text-warning";
            }
            // <td id="appointRequestTime">' .  $requesttime . '</td>
            // <td id="appointApprovedTime">' .  $approvedtime . '</td>
            $data .= '
                <tr>
                    <td id="appointID">' . $row['ID'] . '</td>
                    <td id="appointRequestDate">' . $requesttime . '</td>
                    <td id="appointApprovedDate">' . $approvedtime . '</td>
                    <td id="appointDentist">' . $row['Dentist'] . '</td>
                    <td id="appointStatus" class="' . $status . ' fw-bold">' . $row['Status'] . '</td>
                    <td class="appointID">
                    <button type="button" value="' . $row['ID'] . '" class="btn btn-sm btn-outline-primary viewAptDetail" data-bs-toggle="modal" data-bs-target="#appointListModal">View
                    </button>
                    </td>
                </tr>
            ';
        }
    }
}

echo json_encode($data);