<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$error;
$data = [];
$id;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $aptId = $_POST['aptId'];

    $stmt = $conn->prepare("SELECT th.tooth_number, th.dentist_note, th.procedures_id, th.procedure_price
    FROM `treatment_history` th
    WHERE th.appointment_requests_id = ?");
    $stmt->bind_param('i',$aptId);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];

    while ($row = $result->fetch_assoc()){
        $data[] = $row;
    }

	$stmt->close();
}

echo json_encode($data);