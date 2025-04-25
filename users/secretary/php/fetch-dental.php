<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$data = "";
$pid;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $pid = $_POST['pid'];

    $stmt = $conn->prepare("SELECT dl.*, dl.visit_date  FROM `dental_history_logs` dl
        LEFT OUTER JOIN dental_history dh
        ON dh.id = dl.dental_history_id
        WHERE dl.patient_id = ?
        ORDER BY timestamp DESC;");
    $stmt->bind_param('i', $pid);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $lastVisit = date('F d, Y', strtotime($row['visit_date']));
            $timestamp = date('F d, Y h:i:s A', strtotime($row['timestamp']));
            $data .= '
                <tr>
                    <td>' . $row['remarks'] . '</td>
                    <td>' . $lastVisit . '</td>
                    <td>' . $timestamp . '</td>
                </tr>
            ';
        }
    } else {
        $data .= '
            <tr>
                <td colspan="3" class="fw-semibold">No Records</td>
            </tr>
        ';
    }
}


echo json_encode($data);