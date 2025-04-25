<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$data = "";
$items = "";
$pid;

function fetchProcedures($conn, $pid) {
    $stmt = $conn->prepare("SELECT * FROM procedures;");
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    $data = [];

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[$row['id']] = $row['name'];
        }

        return $data;
    }
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $pid = $_POST['pid'];
    //$dentist_id = fetchDentistID() ?? "";
    $proceduresList = fetchProcedures($conn, $pid) ?? [];

    $stmt = $conn->prepare("SELECT th.appointment_requests_id, th.tooth_number, th.dentist_note, th.procedures_id, th.timestamp,
	CONCAT(di.fname , CASE WHEN di.mname = 'None' THEN ' ' ELSE CONCAT(' ' , di.mname , ' ') END , di.lname, 
	CASE WHEN di.suffix = 'None' THEN '' ELSE CONCAT(' ' , di.suffix) END ) AS DentistName, th.procedure_price
	FROM treatment_history th
    LEFT OUTER JOIN dentist_info di ON di.id = th.dentist_id
    WHERE patient_id = ?
    ORDER BY appointment_requests_id DESC;");
    $stmt->bind_param('i', $pid);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    $procedures = [];

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $procedures = [];
            $patientProcedures = explode("-", $row['procedures_id']);

            foreach ($patientProcedures as $value) {
                if (isset($proceduresList[$value])) {
                    array_push($procedures,$proceduresList[$value]);
                }
            }

            // var_dump($proceduresList);

            $procedureString = implode(", ", $procedures);

            $timestamp = date('m/d/Y h:i A', strtotime($row['timestamp']));
            $data .= '
                <tr>
                    <td>' . $row['appointment_requests_id'] . '</td>
                    <td>' . $row['DentistName'] . '</td>
                    <td>' . $row['tooth_number'] . '</td>
                    <td>' . $row['dentist_note'] . '</td>
                    <td>' . $row['procedure_price'] . '</td>
                    <td>' . $procedureString . '</td>
                    <td>' . $timestamp . '</td>
                </tr>
            ';
        }
    }
}

echo json_encode($data);