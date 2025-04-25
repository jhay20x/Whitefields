<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$data = [];
$pid;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $pid = $_POST["pid"] ?? "";

    $stmt = $conn->prepare("SELECT * FROM procedures WHERE id = ?");
    $stmt->bind_param('i',$pid);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        $data['name'] = $row['name'];
        $data['description'] = $row['description'];
        $data['allow_installment'] = $row['allow_installment'];
        $data['price_min'] = $row['price_min'];
        $data['price_max'] = $row['price_max'];
        $data['status'] = $row['status'];
    }
}

echo json_encode($data);