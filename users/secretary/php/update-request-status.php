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

    $user_id = $_SESSION['user_id'];
    $setStatus = $_POST['setStatus'];
    $setStatusText = $_POST['setStatusText'];
    $datetime = $_POST['datetime'];
    $reasonText = $_POST['reasonText'];

    if ($setStatus == 2) {
        if (is_null($_POST['reason'])) {
            $reason = NULL;
        } else {
            $reason = $_POST['reason'];
        }

        if ($_POST['reason'] == 6) {
            $reasonOther = $_POST['reasonOther'];
        } else {
            $reasonOther = NULL;
        }
    } else {
        $reason = NULL;
        $reasonOther = NULL;
    }

    $approveddatetime = date('Y/m/d H:i:s A', time());

    $stmt = $conn->prepare("UPDATE `appointment_requests` SET `appoint_status_id`= ?, `approved_datetime`= ?, `verdict_by`= ?, `reason_id` = ?, `reason_other` = ? WHERE `id` = ?;");
    $stmt->bind_param("isiisi", $setStatus, $approveddatetime, $user_id, $reason, $reasonOther, $id);

    if ($stmt->execute()) {
        $user = fetchEmail($conn, $pid);
        $username = $user["username"];
        $userEmail = $user["emailAddress"];

        if ($setStatus == 1) {
            $message = 'Appointment request has been successfully approved.';
            $content = "Good Day $username!<br><br>
                Your appointment request for the date $datetime has been approved. Failing to attend on your appointed date and time will result to the cancellation of your appointment. Have a nice day.";
        } else if ($setStatus == 2) {
            $message = 'Appointment request has been successfully rejected.';

            if ($reason != 6){
                $content = "Good Day $username!<br><br>
                    Your appointment request for the date $datetime has been rejected due to the following reason:<br><br>
                    $reasonText.";                
            } else {
                $content = "Good Day $username!<br><br>
                    Your appointment request for the date $datetime has been rejected due to the following reason:<br><br>
                    $reasonText - $reasonOther.";                
            }
        }
        
        if ($userEmail != "None") {
            sendEmail($userEmail, $username, $content);
        }
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

    $subject = 'Whitefields Appointment Request';
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