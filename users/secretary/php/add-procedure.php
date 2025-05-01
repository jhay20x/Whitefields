<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$data = [];
$message;
$error;

sleep(1);

function checkIfProcedureExist($conn, $procedureName) {
    $stmt = $conn->prepare("SELECT pr.name FROM procedures pr WHERE pr.name = ? AND status = 1");
    $stmt->bind_param("s", $procedureName);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {        
        return true;
    } else {
        return false;
    }
}

function checkIfProcedureExistInactive($conn, $procedureName) {
    $stmt = $conn->prepare("SELECT pr.name FROM procedures pr WHERE pr.name = ? AND status = 0");
    $stmt->bind_param("s", $procedureName);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {        
        return true;
    } else {
        return false;
    }
}

function insertInfo($conn, $procedureName, $procedureDesc, $procedurePriceMin, $procedurePriceMax) {
    $stmt = $conn->prepare("INSERT INTO `procedures`(`name`, `description`, `price_min`, `price_max`) VALUES (?,?,?,?)");
    $stmt->bind_param("ssiii", $procedureName, $procedureDesc, $procedurePriceMin, $procedurePriceMax);
    $stmt->execute();
	$stmt->close();
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {     
    $procedureName = $_POST["procedureName"] ?? "";
    $procedureDesc = $_POST["procedureDesc"] ?? "";
    $procedurePriceMin = $_POST["procedurePriceMin"] ?? "";
    $procedurePriceMax = $_POST["procedurePriceMax"] ?? "";

    if (checkIfProcedureExist($conn, $procedureName)) {
        $data['success'] = false;
        $data['error'] = "A procedure with the same name has already been registered. Please try again.";
        echo json_encode($data);
        return;
    }

    if (checkIfProcedureExistInactive($conn, $procedureName)) {
        $data['success'] = false;
        $data['error'] = "A procedure with the same name but is set as inactive has already been registered. Please try again.";
        echo json_encode($data);
        return;
    }

    if ($procedurePriceMin > $procedurePriceMax) {
        $data['success'] = false;
        $data['error'] = "Minimum procedure price can't be higher than the procedure price maximum. Please try again.";
        echo json_encode($data);
        return;
    }

    if ($procedurePriceMax < $procedurePriceMin) {
        $data['success'] = false;
        $data['error'] = "Maximum procedure price can't be lower than the procedure price minimum. Please try again.";
        echo json_encode($data);
        return;
    }
    
    insertInfo($conn, $procedureName, $procedureDesc, $procedurePriceMin, $procedurePriceMax);
    $message = "A new procedure has been successfully added.";
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}


echo json_encode($data);