<?php
session_start();

require_once '../../../database/config.php';

$error;
$data = [];
$pid;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $pid = $_POST["pid"] ?? "";

    $stmt = $conn->prepare("SELECT price_min, price_max FROM procedures WHERE id = ?");
    $stmt->bind_param('i',$pid);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $data['price_min'] = $row['price_min'];
        $data['price_max'] = $row['price_max'];
    }
}

echo json_encode($data);