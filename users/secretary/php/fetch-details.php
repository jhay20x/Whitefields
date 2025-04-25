<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$error;
$data = [];
$id;

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $id = fetchSecretaryID();

    $stmt = $conn->prepare("SELECT si.lname, si.fname, si.mname, si.suffix, si.contactno, si.bdate, si.gender, 
    si.religion, si.nationality, si.address, ac.email_address, ac.username
    FROM `secretary_info` si
    LEFT OUTER JOIN accounts ac
    ON ac.id = si.accounts_id
    WHERE si.id = ?");
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
        $data['email'] = $row['email_address'];
        $data['username'] = $row['username'];
    }
}


echo json_encode($data);