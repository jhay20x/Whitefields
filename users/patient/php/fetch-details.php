<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$error;
$data = [];
$id;

sleep(1);

function calculateAge($birthdate) {
    $birthDate = new DateTime($birthdate);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    return $age;
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $id = fetchPatientID();

    $stmt = $conn->prepare("SELECT pi.lname, pi.fname, pi.mname, pi.suffix, pi.contactno, pi.bdate, pi.gender, 
    pi.religion, pi.nationality, pi.occupation, pi.address, ac.email_address, ac.username
    FROM `patient_info` pi
    LEFT OUTER JOIN accounts ac
    ON ac.id = pi.accounts_id
    WHERE pi.id = ?");
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $result = $stmt->get_result();
	$stmt->close();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        $data['fname'] = $row['fname'];
        $data['lname'] = $row['lname'];
        $data['mname'] = $row['mname'];
        $data['suffix'] = $row['suffix'];
        $data['bdate'] = $row['bdate'];
        $data['gender'] = $row['gender'];
        $data['religion'] = $row['religion'];
        $data['nationality'] = $row['nationality'];
        $data['contactno'] = $row['contactno'];
        $data['address'] = $row['address'];
        $data['occupation'] = $row['occupation'];
        $data['email'] = $row['email_address'];
        $data['username'] = $row['username'];
    }
}


echo json_encode($data);