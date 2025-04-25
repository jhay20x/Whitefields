<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$data = [];
$id;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("SELECT * FROM `store_availability` WHERE `id` = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        $data['id'] = $row['id'];
        $data['day'] = $row['day'];
        $data['availability'] = $row['availability'];
        $data['timeFrom'] = date('h:i A', strtotime($row['time_from']));
        $data['timeTo'] = date('h:i A', strtotime($row['time_to']));
    }
}

echo json_encode($data);