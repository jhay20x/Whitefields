<?php
session_start();

require_once '../../../database/config.php';
include 'fetch-id.php';

$error;
$data = [];
$id;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $aptId = $_POST['aptId'];

    $stmt = $conn->prepare("SELECT th.tooth_number, th.dentist_note, th.procedures
    FROM `treatment_history` th
    WHERE th.appointment_requests_id = ?");
    $stmt->bind_param('i',$aptId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $data['toothNumber'] = $row['tooth_number'];
        $data['dentistNote'] = $row['dentist_note'];
        $data['procedures'] = $row['procedures'];
    }
}

echo json_encode($data);