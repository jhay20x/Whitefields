<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$data = [];
$message;
$error;

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $dentistId = $_POST['dentistId'];
    $values = [];

    foreach ($_POST['values'] as $value) {
        $values[] = $value == "" ? null : $value;
    }

    // $count = 0;
    $valueParam = [];

    foreach ($dentistId as $id) {
        $valueParam = array_slice($values, 0, 7);

        $values = array_slice($values, 7); 

        if (!checkSchedule($conn, $id)){
            insertSchedule($conn, $id, $valueParam);
            $message = "Dentist's schedule/s has been saved.";
        } else {
            updateSchedule($conn, $id, $valueParam);
            $message = "Dentist's schedule/s has been updated.";
        };
    }
}

function insertSchedule($conn, $id, $valueParam) {
    $stmt = $conn->prepare("INSERT INTO `schedules`(`dentist_id`, `Sun`, `Mon`, `Tue`, `Wed`, `Thu`, `Fri`, `Sat`) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("isssssss", $id, ...$valueParam); 
    $stmt->execute();
	$stmt->close();
}

function updateSchedule($conn, $id, $valueParam) {
    array_push($valueParam, $id);

    $stmt = $conn->prepare("UPDATE `schedules` SET `Sun` = ?, `Mon` = ?, `Tue` = ?, `Wed` = ?, `Thu` = ?, `Fri` = ?, `Sat` = ? WHERE `dentist_id` = ?");
    $stmt->bind_param("sssssssi", ...$valueParam);
    $stmt->execute();
	$stmt->close();
}

function checkSchedule($conn, $id) {
    $stmt = $conn->prepare("SELECT sc.dentist_id AS ID FROM schedules sc WHERE sc.dentist_id = ?;");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
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