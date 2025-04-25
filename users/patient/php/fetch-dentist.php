<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$data = [];
$id;
$dentist;
$dentist_id;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    
    function checkDay($day){
        global $error;
    
        $allowedDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    
        if (in_array($day, $allowedDays)) {
            return true;
        } else {
            $error = "Invalid day. Please try again.";
            return false;
        }
    } 

    function validateDate ($conn){
        $date = $_POST['date'];
            
        $day = date('D', strtotime($date));
    
        if (!checkDay($day)) {
            return;
        }

        getDentist($conn, $day);
    }
    
    function getDentist ($conn, $day) {
        global $error, $dentist, $dentist_id;

        $stmt = $conn->prepare("SELECT di.id AS ID, CONCAT(di.fname , ' ' , di.mname , ' ' , di.lname) AS Name,
            sc.Sun, sc.Mon, sc.Tue, sc.Wed, sc.Thu, sc.Fri, sc.Sat
            FROM dentist_info di
            LEFT OUTER JOIN schedules sc ON sc.dentist_id = di.id
            LEFT OUTER JOIN accounts ac ON ac.id = di.accounts_id
            WHERE sc." . $day . " = 1 AND ac.status = 1;");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
                
        if ($result->num_rows > 0) {        
            $row = $result->fetch_assoc();
            $dentist = $row['Name'];
            $dentist_id = $row['ID'];
        } else {
            $error = "Not Set";
        }  
    }

    validateDate($conn);

    if (!empty($error)) {
        $data['success'] = false;
        $data['error'] = $error;
    } else {
        $data['success'] = true;
        $data['dentist'] = $dentist;
        $data['dentist_id'] = $dentist_id;
    }
    
    echo json_encode($data);
}
