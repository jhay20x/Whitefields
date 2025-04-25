<?php
session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$data = [];
$id;

function calculateAge($birthdate) {
    $birthDate = new DateTime($birthdate);
    $today = new DateTime(); // Current date
    $age = $today->diff($birthDate)->y; // Calculate age in years
    return $age;
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("SELECT CONCAT(di.fname , CASE WHEN di.mname = 'None' THEN ' ' ELSE CONCAT(' ' , di.mname , ' ') END , di.lname, 
    CASE WHEN di.suffix = 'None' THEN '' ELSE CONCAT(' ' , di.suffix) END ) AS Name,
    di.accounts_id, di.specialist, di.bdate, di.contactno, di.gender, di.address, di.about_me, di.religion, di.nationality, ac.email_address, ac.username, ac.status, ac.id as AccountID
    FROM dentist_info di
    LEFT OUTER JOIN accounts ac
    ON ac.id = di.accounts_id
    WHERE di.id = ?");
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $result = $stmt->get_result();  
	$stmt->close();  

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        $data['AccountID'] = $row['AccountID'];
        $data['Name'] = $row['Name'];
        $data['specialist'] = $row['specialist'];
        $data['age'] = calculateAge($row['bdate']);
        $data['bdate'] = $row['bdate'];
        $data['contactno'] = $row['contactno'];
        $data['gender'] = $row['gender'];
        $data['address'] = $row['address'];
        $data['about_me'] = $row['about_me'];
        $data['religion'] = $row['religion'];
        $data['nationality'] = $row['nationality'];
        $data['email_address'] = $row['email_address'];
        $data['username'] = $row['username'];
        $data['status'] = $row['status'];
    }
}

echo json_encode($data);