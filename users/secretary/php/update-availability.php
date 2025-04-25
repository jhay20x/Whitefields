<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$message;
$data = [];
$appointCount;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    updateAvailability();
}


function updateAvailability(){
    global $conn;
    global $message;
    global $error;

    $id = $_POST['id'];
    $dayTxt = $_POST['dayTxt'];    

    switch ($_POST['availability']) {
        case 1:
            $timeFrom = date('H:i:s', strtotime($_POST['timeFrom']));
            $timeTo = date('H:i:s', strtotime($_POST['timeTo']));
            $availability = $_POST['availability'];
            break;
        case 2:
            $timeFrom = NULL;
            $timeTo = NULL;
            $availability = NULL;
            if(!checkHaveAppointment($dayTxt)){
                return;
            }
            break;
    }

    $stmt = $conn->prepare("UPDATE `store_availability` SET `availability`= ?,`time_from`= ?,`time_to`= ? WHERE `id` = ?;");
    $stmt->bind_param("issi", $availability, $timeFrom, $timeTo, $id);
    
    if ($stmt->execute()){    
        $message = "Store Availability for $dayTxt has been changed.";
    } else {
        $error = "Store Availability for $dayTxt has not been saved. Please try again.";
    }
}

function checkHaveAppointment($dayTxt) {
    global $conn;
    global $error;

    $stmt = $conn->prepare("SELECT COUNT(ar.id) AS HaveAppoint, ar.start_datetime
        FROM appointment_requests ar
        WHERE DAYNAME(ar.start_datetime) = ? AND (ar.appoint_status_id = 1 || ar.appoint_status_id = 4);");
    $stmt->bind_param('s', $dayTxt);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if($row['HaveAppoint'] > 0) {
            $error = "Store Availability for $dayTxt has not been saved. There are still {$row['HaveAppoint']} Approved/Pending appointment" . ($row['HaveAppoint'] > 1 ? "s" : "") . ". Deny or Cancel " . ($row['HaveAppoint'] > 1 ? "those" : "this") . " appointment" . ($row['HaveAppoint'] > 1 ? "s" : "") . " first.";
            return false;
        } else {            
            return true;
        }
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