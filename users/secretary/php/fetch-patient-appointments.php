<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$data = [];
$pid;

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $pid = $_POST['pid'] ?? "";

    $stmt = $conn->prepare("SELECT ar.id, GROUP_CONCAT(DISTINCT pr.name ORDER BY pr.name SEPARATOR ', ') AS procedures
        FROM appointment_requests ar
        LEFT JOIN treatment_history th ON th.appointment_requests_id = ar.id
        LEFT JOIN procedures pr ON pr.id = th.procedures_id
        WHERE ar.patient_id = ? AND (ar.appoint_status_id = 5 OR ar.appoint_status_id = 7)
        GROUP BY ar.id");
    $stmt->bind_param('i',$pid);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    $data = [];
    
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = [
                'id' => $row['id'],
                'procedures' => $row['procedures']
            ];
        }
    }
}

echo json_encode($data);