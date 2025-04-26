<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$message;
$data = [];

sleep(1);

function checkHasProcedure($conn, $procedure_id) {    
    $stmt = $conn->prepare("SELECT * FROM treatment_history WHERE procedures_id = ?;");
    $stmt->bind_param("i", $procedure_id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $procedure_id = $_POST["procedure_id"] ?? "";

    if (checkHasProcedure($conn, $procedure_id)) {
        $data['success'] = false;
        $data['error'] = "This procedure has been used. It cannot be removed.";
        echo json_encode($data);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM procedures WHERE id = ?");
    $stmt->bind_param("i", $procedure_id);
    
    if ($stmt->execute()){    
        $message = "The procedure has been successfully removed.";
    } else {
        $error = "The procedure has not been removed. " + $stmt->error;
    }
    
    $stmt->close();
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}

echo json_encode($data);