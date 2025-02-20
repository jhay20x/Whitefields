<?php

$error;
$data = [];
$id;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {    
    function fetchDentistID() {
        global $conn;
        
        $user_id = $_SESSION['user_id'];
        
        $stmt = $conn->prepare("SELECT di.id AS id FROM dentist_info di WHERE di.accounts_id = ?;");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {        
            $user = $result->fetch_assoc();
            return $user['id'];
        } else {
            return "ID not found. Please update your personal information first.";
        }
    }        
}