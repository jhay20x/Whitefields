<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$message;
$data = [];

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $pid = $_POST["pid"] ?? "";
    $viewProcedureName = $_POST["viewProcedureName"] ?? "";
    $viewProcedureStatus = $_POST["viewProcedureStatus"] ?? "";
    $viewProcedureDesc = $_POST["viewProcedureDesc"] ?? "";
    $viewProcedureInstallment = $_POST["viewProcedureInstallment"] ?? "";
    $viewProcedurePriceMin = $_POST["viewProcedurePriceMin"] ?? "";
    $viewProcedurePriceMax = $_POST["viewProcedurePriceMax"] ?? "";

    $stmt = $conn->prepare("UPDATE procedures SET name = ?, description = ?, allow_installment = ?, price_min = ?, price_max = ?, status = ? WHERE id = ?;");
    $stmt->bind_param("ssiiiii",$viewProcedureName, $viewProcedureDesc, $viewProcedureInstallment, $viewProcedurePriceMin, $viewProcedurePriceMax, $viewProcedureStatus, $pid);
    
    if ($stmt->execute()){    
        $message = "Procedure details has been updated.";
    } else {
        $error = "Procedure details has not been updated. " + $stmt->error;
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

$conn->close();
echo json_encode($data);