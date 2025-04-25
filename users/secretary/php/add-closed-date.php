<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$data = [];
$message;
$error;

function checkClosedDate($conn, $closedDate) {    
    $stmt = $conn->prepare("SELECT Date FROM store_closed_dates WHERE Date = ?;");
    $stmt->bind_param("s", $closedDate);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function checkHaveAppointment($conn, $closedDate) {
    $stmt = $conn->prepare("SELECT COUNT(ar.id) AS HaveAppoint, ar.start_datetime
        FROM appointment_requests ar
        WHERE DATE(ar.start_datetime) = ? AND (ar.appoint_status_id = 1 || ar.appoint_status_id = 4);");
    $stmt->bind_param('s', $closedDate);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if($row['HaveAppoint'] > 0) {
            return $row['HaveAppoint'];
        } else {            
            return false;
        }
    }
}

function insertClosedDate($conn, $closedDate, $remarks) {
    $stmt = $conn->prepare("INSERT INTO `store_closed_dates`(`Remarks`, `Date`) VALUES (?,?)");
    $stmt->bind_param("ss", $remarks, $closedDate);
    $stmt->execute();
	$stmt->close();
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {        
    $remarks = $_POST['remarks'] ?? "";
    $closedDate = $_POST['closedDate'] ?? "";

    if (checkClosedDate($conn, $closedDate)) {
        $data['success'] = false;
        $data['error'] = "This date is already added in the system. Please select another date.";
        echo json_encode($data);
        return;
    }

    $haveAppoint = checkHaveAppointment($conn, $closedDate);

    if ($haveAppoint) {
        $data['success'] = false;
        $data['error'] = "The date has not been saved. There are still $haveAppoint Approved/Pending appointment" . ($haveAppoint > 1 ? "s" : "") . ". Deny or Cancel " . ($haveAppoint > 1 ? "those" : "this") . " appointment" . ($haveAppoint > 1 ? "s" : "") . " first.";
        echo json_encode($data);
        return;
    }
    
    insertClosedDate($conn, $closedDate, $remarks);
    $message = "A new date has been successfully added.";
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