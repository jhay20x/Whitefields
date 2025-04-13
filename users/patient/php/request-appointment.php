<?php
session_start();

include '../../../database/config.php';

date_default_timezone_set('Asia/Manila');

$data = [];
$message;
$error;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $user_id = $_SESSION["user_id"];
    
    checkUser($user_id);
}

function checkUser($user_id) {
    global $conn;
    global $error;

    $date = $_POST['date'];
    $time_str = $_POST['timeHour'] . ":" . $_POST['timeMinute'] . ' ' . $_POST['ampmText'];
    $time = date("H:i:s", strtotime($time_str));
    $dentist = $_POST['dentist'];

    if ($dentist == 0) {
        $error = "No dentist available on this date. Please choose another date.";
        return;
    }
    
    if (!checkDateTime($date, $time)) {
        return;
    };
    
    if (!checkDateSched($date)) {
        return;
    };    
    
    if (!checkStoreAvailability($date)) {
        return;
    };

    if (!checkTimeSched($time, $date)) {
        return;
    };
    
    $datetime = date('Y-m-d H:i:s', strtotime("$date $time"));
    $datetimestr = $_POST['date'] . 'T' . date('H:i:s', strtotime("$time"));
    $concern = $_POST['concern'];
    $requestdatetime = date('Y/m/d H:i:s', time());
    $appoint_status = 4;

    $stmt = $conn->prepare("SELECT pi.id AS id FROM patient_info pi WHERE pi.accounts_id = ?;");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $patient_id = $user['id'];
        checkHavePending($patient_id, $dentist,$requestdatetime,  $datetime, $datetimestr, $appoint_status, $concern);
    } else {
        $error = "ID not found. Please update your personal information first.";
    }
}

function checkTimeSched($time, $date){
    global $conn;
    global $error;

    $stmt = $conn->prepare("SELECT ar.start_datetime FROM appointment_requests ar WHERE DATE(ar.start_datetime) = ? AND TIME(ar.start_datetime) = ? AND ar.appoint_status_id = 1;");
    $stmt->bind_param("ss", $date,$time);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {        
        $error = "The time slot you've chosen has already been taken. Please choose a different time.";
        return false;
    } else {
        return true;
    }
}

function checkDateTime($date, $time) {
    global $error;

    $curdate = date("Y-m-d");
    $curtime = date("H:i:s", time());
    $maxtime = date("H:i:s", strtotime("18:30:00"));

    if ($date == $curdate && $curtime > $time && $curtime < $maxtime){
        $error = "Unfortunately you can't set appointment for this selected time. Please choose a later time or date.";
        return false;
    } else if ($date == $curdate && $curtime > $maxtime){
        $error = "Unfortunately you can't set any appointments for today. Please choose a different date.";
        return false;
    } else {
        return true;
    }
}

function checkDateSched($date) {
    global $error;

    if ($date < date("Y-m-d")){
        $error = "The date you've submitted is invalid. Please select a valid date.";
        return false;
    } else {
        return true;
    }
}

function checkStoreAvailability($date) {
    global $conn;
    global $error;

    $stmt = $conn->prepare('SELECT sa.availability, sa.time_from, sa.time_to FROM store_availability sa WHERE sa.day = DAYNAME(?) AND sa.availability IS NOT NULL;');
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows == 0) {
        $error = "The clinic is closed for this day. Please select a different day for your appointment.";
        return false;
    } else {
        return true;
    }
}

function insertRequest($patient_id, $dentist, $requestdatetime, $datetime, $datetimestr, $appoint_status, $concern) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO `appointment_requests`(`patient_id`, `dentist_info_id`, `request_datetime`, `start_datetime`, `start_datetime_str`, `appoint_status_id`, `oral_concern`) VALUES (?,?,?,?,?,?,?);");
    $stmt->bind_param("iisssis", $patient_id, $dentist, $requestdatetime, $datetime, $datetimestr, $appoint_status, $concern);
    $stmt->execute();
	$stmt->close();
}

function checkHavePending ($patient_id, $dentist, $requestdatetime, $datetime, $datetimestr, $appoint_status, $concern) {
    global $conn;
    global $error;
    global $message;
    $date = date('Y-m-d',strtotime($datetime));
    
    if (countPendingAppointment($patient_id) >= 3) {
        $error = "Your request for an appointment failed. You already have reached the maximun number of request (3).";
    } else {
        $stmt = $conn->prepare("SELECT ar.patient_id as AppointCount, ar.start_datetime as StartDateTime FROM appointment_requests ar WHERE ar.patient_id = ? AND DATE(ar.start_datetime) = ? AND (ar.appoint_status_id = 1 OR ar.appoint_status_id = 4);");
        $stmt->bind_param("is", $patient_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $error = "You already have a pending request on " . date('l, F j, Y \a\t g:i A', strtotime($row['StartDateTime'])) . ". Please select a different date.";
        } else {
            insertRequest($patient_id, $dentist,$requestdatetime, $datetime, $datetimestr, $appoint_status, $concern);
            $message = "Your request for an appointment succeeded.";
        }
    }
}

function countPendingAppointment($patient_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(ar.patient_id) as AppointCount FROM appointment_requests ar WHERE ar.patient_id = ? AND ar.appoint_status_id = 4;");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        return $row['AppointCount'];
    }
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}

echo json_encode($data);