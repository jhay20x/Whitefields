<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$data = [];
$rid;
$type;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $rid = $_POST["rid"] ?? "";
    $type = $_POST["type"] ?? "";

    if($type == "Denied") {
        $stmt = $conn->prepare("SELECT * FROM rejected_reasons WHERE id = ?");
        $stmt->bind_param('i',$rid);
    } else {
        $stmt = $conn->prepare("SELECT * FROM cancel_reasons WHERE id = ?");
        $stmt->bind_param('i',$rid);
    }

    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        $data['reason'] = $row['reason'];
        $data['status'] = $row['status'];
    }
}


echo json_encode($data);