<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$data = "";
$pid;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $pid = $_POST['pid'];
    
    $stmt = $conn->prepare("SELECT ar.id as AppointmentID, ar.past_appoint_id as PastAppointmentID, st.status_name AS AppointStatus, ar.start_datetime AS Timestamp
        FROM appointment_requests ar
        LEFT OUTER JOIN appointment_status st ON st.id = ar.appoint_status_id
        WHERE ar.patient_id = ? AND (ar.appoint_status_id = 6 OR ar.appoint_status_id = 5  OR ar.appoint_status_id = 7)
        GROUP BY ar.id;");

    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $datetime = date("Y-m-d h:i A", strtotime($row['Timestamp']));
            switch (($row['AppointStatus'])) {
                case 'Completed':
                    $color = "text-success";
                    break;

                case 'Partially Paid':
                    $color = "text-danger";
                    break;
                
                default:
                    $color = "text-warning";
                    break;
            }
            //<td id="transactionTransID">' . $row['TransactionID'] . '</td>
            $data .= '
            <tr>
                <td id="transactionAptID">' . $row['AppointmentID'] . '</td>
                <td id="transactionAppointStatus" class="fw-bold ' . $color . '">' . $row['AppointStatus'] . '</td>
                <td id="transactionTimestamp">' . $datetime . '</td>
                <td class="transactionAction">
                    <button type="button" data-past-apt-id="' . $row['PastAppointmentID'] . '" data-apt-id="' . $row['AppointmentID'] . '" class="btn btn-sm btn-outline-primary viewTransDetail" data-bs-toggle="modal" data-bs-target="#transactionDetailsModal">View</button>
                </td>
            </tr>
        ';
        }
    }
}


echo json_encode($data);