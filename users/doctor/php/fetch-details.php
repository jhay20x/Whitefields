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
    $id = fetchDentistID();

    $stmt = $conn->prepare("SELECT di.lname, di.fname, di.mname, di.suffix, di.specialist, di.contactno, di.bdate, di.gender, 
    di.religion, di.nationality, di.about_me, di.address, ac.email_address, ac.username
    FROM `dentist_info` di
    LEFT OUTER JOIN accounts ac
    ON ac.id = di.accounts_id
    WHERE di.id = ?");
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
        $data['specialist'] = $row['specialist'];
        $data['bdate'] = $row['bdate'];
        $data['gender'] = $row['gender'];
        $data['religion'] = $row['religion'];
        $data['nationality'] = $row['nationality'];
        $data['contactno'] = $row['contactno'];
        $data['address'] = $row['address'];
        $data['about_me'] = $row['about_me'];
        $data['email'] = $row['email_address'];
        $data['username'] = $row['username'];
    }
}

echo json_encode($data);