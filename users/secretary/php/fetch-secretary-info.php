<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$data = [];
$id;

function calculateAge($birthdate) {
    $birthDate = new DateTime($birthdate);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    return $age;
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $sid = $_POST['sid'];

    $stmt = $conn->prepare("SELECT CONCAT(si.fname , CASE WHEN si.mname = 'None' THEN ' ' ELSE CONCAT(' ' , si.mname , ' ') END , si.lname, 
    CASE WHEN si.suffix = 'None' THEN '' ELSE CONCAT(' ' , si.suffix) END ) AS Name,
    si.accounts_id, si.bdate, si.contactno, si.gender, si.address, si.religion, si.nationality, ac.email_address, ac.username, ac.status, ac.id as AccountID
    FROM secretary_info si
    LEFT OUTER JOIN accounts ac
    ON ac.id = si.accounts_id
    WHERE si.id = ?");
    $stmt->bind_param('i',$sid);
    $stmt->execute();
    $result = $stmt->get_result();  
	$stmt->close();  

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        $data['AccountID'] = $row['AccountID'];
        $data['Name'] = $row['Name'];
        $data['age'] = calculateAge($row['bdate']);
        $data['bdate'] = $row['bdate'];
        $data['contactno'] = $row['contactno'];
        $data['gender'] = $row['gender'];
        $data['address'] = $row['address'];
        $data['religion'] = $row['religion'];
        $data['nationality'] = $row['nationality'];
        $data['email_address'] = $row['email_address'];
        $data['username'] = $row['username'];
        $data['status'] = $row['status'];
    }
}


echo json_encode($data);