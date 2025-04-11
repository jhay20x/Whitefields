<?php
session_start();

require_once '../../../database/config.php';
include 'fetch-id.php';

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
    $dentist_id = fetchDentistID() ?? "";
    $proceduresList = fetchProcedures($conn, $pid) ?? [];

    $stmt = $conn->prepare("SELECT * FROM treatment_history
    WHERE patient_id = ? AND dentist_id = ?
    ORDER BY appointment_requests_id DESC;");
    $stmt->bind_param('ii', $pid, $dentist_id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    $procedures = [];

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $procedures = [];
            $patientProcedures = explode("-", $row['procedures']);

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
                    <td>' . $row['tooth_number'] . '</td>
                    <td>' . $row['dentist_note'] . '</td>
                    <td>' . $procedureString . '</td>
                    <td>' . $timestamp . '</td>
                </tr>
            ';
        }
    }
}

echo json_encode($data);