<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

global $conn;
require_once '../../../database/config.php';
require '../../../vendor/autoload.php';

date_default_timezone_set('Asia/Manila');

$error;
$message;
$data = [];

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $id = $_POST['id'] ?? "";
    $pid = $_POST['pid'] ?? "";
    $dentist_id = $_POST['dentist_id'] ?? "";

    $user_id = $_SESSION['user_id'];
    $setStatus = $_POST['setStatus'] ?? "";
    $setStatusText = $_POST['setStatusText'];
    $datetime = $_POST['datetime'];
    $reasonText = $_POST['reasonText'];
    $pastAptId = isset($_POST['pastAptId']) && $_POST['pastAptId'] !== "" ? $_POST['pastAptId'] : NULL;

    if ($setStatus == 1) {
        $reason = NULL;
        $reasonOther = NULL;

        $query = "UPDATE `appointment_requests` SET `dentist_info_id` = ?, `past_appoint_id` = ? WHERE `id` = ?;";
        $params = [$dentist_id, $pastAptId, $id];
        $type = "iii";
    } else {
        $reason = isset($_POST['reason']) && $_POST['reason'] !== "" ? $_POST['reason'] : NULL;
        $reasonOther = $reason == 6 && isset($_POST['reasonOther']) && $_POST['reasonOther'] !== '' ? $_POST['reasonOther'] : NULL;

        $query = "UPDATE `appointment_requests` SET `appoint_status_id`= ?, `reason_id` = ?, `reason_other` = ?, `dentist_info_id` = ? WHERE `id` = ?;";
        $params = [$setStatus, $reason, $reasonOther, $dentist_id, $id];
        $type = "iisii";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param($type, ...$params);

    if ($stmt->execute()) {
        $user = fetchEmail($conn, $pid);
        $username = $user["username"];
        $userEmail = $user["emailAddress"];

        if ($setStatus == 2) {
            $message = "Appointment request details has been updated. Appointment been successfully rejected.";

            if ($reason != 6){
                $content = "Good Day $username!<br><br>
                    Your appointment request for the date $datetime has been unfortunately rejected due to the following reason:<br><br>
                    $reasonText.";
            } else {
                $content = "Good Day $username!<br><br>
                    Your appointment request for the date $datetime has been unfortunately rejected due to the following reason:<br><br>
                    $reasonText - $reasonOther.";                
            }
            
            if ($userEmail != "None") {
                sendEmail($userEmail, $username, $content);
            }
        } else {
            $message = "Appointment request details has been updated.";
        };
    }
}

function fetchEmail($conn, $pid)
{
    $stmt = $conn->prepare("SELECT DISTINCT ac.email_address, ac.username 
        FROM patient_info pi
        LEFT OUTER JOIN appointment_requests ar ON ar.patient_id = pi.id
        LEFT OUTER JOIN accounts ac ON pi.accounts_id = ac.id
        WHERE pi.id = ?;");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        $data = [];

        $data["emailAddress"] = $row['email_address'];
        $data["username"] = $row['username'];

        return $data;
    }
}

function sendEmail($userEmail, $username, $content){
    global $error;
    
    $subject = 'Whitefields Appointment Request Update';
    $emailmessage = $content;
    $mail = new PHPMailer(true);

    // $stmt = $conn->prepare("UPDATE `accounts` SET `reset_otp`= ? WHERE `email_address` = ?");
    // $stmt->bind_param("ss", $otp, $userEmail);
    // $stmt->execute();
	// $stmt->close();

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jhay20x@gmail.com';
        $mail->Password = 'grvf jkur pyxw pdqs';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email Recipients
        $mail->setFrom('jhay20x@gmail.com', 'Whitefields Dental Clinic');
        $mail->addAddress($userEmail, $username);

        //Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $emailmessage;
        $mail->AltBody = $emailmessage;

        // Send email
        $mail->send();

        // header("Location: ../verify-email.php");

        // $_SESSION['email_address'] = $userEmail;

        // echo 'Message has been sent';
    } catch (Exception $e) {
        $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
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