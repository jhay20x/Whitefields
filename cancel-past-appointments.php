<?php

global $conn;
require_once './public_html/vendor/autoload.php';
require_once './public_html/database/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function fetchMainAccount($conn) {
    $stmt = $conn->prepare("SELECT id FROM accounts WHERE is_main = 1;");
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['id'];
    } else {            
        return false;
    }
}

$query = "
    SELECT ar.id, ar.start_datetime, ac.email_address, ac.username, ar.patient_id
    FROM appointment_requests ar
    LEFT OUTER JOIN patient_info pi ON pi.id = ar.patient_id
    LEFT OUTER JOIN accounts ac ON ac.id = pi.accounts_id
    WHERE ar.appoint_status_id = 4
    AND ar.start_datetime < NOW();
";

$result = $conn->query($query);
$appointments = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'jhay20x@gmail.com';
$mail->Password = 'grvf jkur pyxw pdqs';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$mail->setFrom('jhay20x@gmail.com', 'Whitefields Dental Clinic');

$mail->isHTML(true);
$mail->Subject = 'Whitefields Appointment Request Update';

$secId = fetchMainAccount($conn);

if (!$secId) {
    return;
}

$cancel_reason_other = "Automatic cancellation due to not attending the appointment.";
$cancel_datetime = date('Y-m-d H:i:s', time());

foreach ($appointments as $apt) {
    try {
        // $update = $conn->prepare("UPDATE appointment_requests SET appoint_status_id = 3, verdict_by = ?, cancel_reason_id = 6, cancel_reason_other = ?, cancel_datetime = ? WHERE id = ?");
        $update = $conn->prepare("UPDATE appointment_requests SET appoint_status_id = 3, approved_by = ?, cancel_reason_id = 6, cancel_reason_other = ?, cancel_datetime = ? WHERE id = ?");
        $update->bind_param("issi", $secId, $cancel_reason_other, $cancel_datetime, $apt['id']);
        $update->execute();
        $update->close();

        $mail->clearAddresses();
        $mail->addAddress($apt['email_address'], $apt['username']);
        $datetime = date("F d, Y \\a\\t h:i:s A", strtotime($apt['start_datetime']));
        $mail->Body = "Dear {$apt['username']},<br><br>
            Your appointment scheduled on {$datetime} was automatically cancelled because you did not attend.<br><br>
            You can create a new appointment for a later date or visit the clinic directly. Thank you and have a nice day.";

        $mail->send();
        sleep(1);
    } catch (Exception $e) {
        error_log("Email failed to {$apt['email_address']}: " . $mail->ErrorInfo);
    }
}

echo "All emails sent.";