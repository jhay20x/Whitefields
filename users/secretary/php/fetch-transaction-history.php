<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$data = "";
$aptId;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $aptId = $_POST['aptId'];
    $pastAptId = $_POST['pastAptId'];
    
    // $stmt = $conn->prepare("SELECT tr.*, pr.name, pt.name AS PaymentType,
    //     CONCAT(si.fname , CASE WHEN si.mname = 'None' THEN ' ' ELSE CONCAT(' ' , si.mname , ' ') END , si.lname, 
    //     CASE WHEN si.suffix = 'None' THEN '' ELSE CONCAT(' ' , si.suffix) END ) AS SecretaryName
    //     FROM transactions tr 
    //     LEFT OUTER JOIN secretary_info si ON si.id = tr.secretary_id
    //     LEFT OUTER JOIN payment_types pt ON pt.id = tr.payment_type_id
    //     LEFT OUTER JOIN procedures pr ON pr.id = tr.procedures_id
    //     WHERE appointment_requests_id = ?;");

    $stmt = $conn->prepare("SELECT tr.*, pr.name
        FROM transactions tr 
        LEFT OUTER JOIN procedures pr ON pr.id = tr.procedures_id
        WHERE appointment_requests_id IN (?,?);");

    $stmt->bind_param("ii", $aptId, $pastAptId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $datetime = date("Y-m-d h:i A", strtotime($row['timestamp']));
            $data .= '
            <tr>
                <td id="transactionHistoryAptID">' . $row['id'] . '</td>
                <td id="transactionHistoryProcedure">' . $row['name'] . '</td>
                <td id="transactionHistoryAmountPaid">' . $row['amount_paid'] . '</td>
                <td id="transactionHistoryRemainingBalance">' . $row['remaining_balance'] . '</td>
                <td id="transactionHistoryTimestamp">' . $datetime . '</td>
                <td class="transactionHistoryAction">
                    <button type="button" data-transaction-id="' . $row['id'] . '" class="btn btn-sm btn-outline-primary viewTransHistory" data-bs-toggle="modal" data-bs-target="#transactionHistoryModal">View</button>
                </td>
            </tr>
        ';
        }
    }
}


echo json_encode($data);