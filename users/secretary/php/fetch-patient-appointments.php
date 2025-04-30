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

    $stmt = $conn->prepare("SELECT ar.id FROM appointment_requests ar WHERE ar.patient_id = ? AND (ar.appoint_status_id = 5 OR ar.appoint_status_id = 7)");
    $stmt->bind_param('i',$pid);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    $data = [];
    
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row['id'];
        }
    }
}

echo json_encode($data);