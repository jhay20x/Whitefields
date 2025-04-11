<?php

$error;
$data = [];
$id;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {    
    function fetchSecretaryID() {
        global $conn;
        
        $user_id = $_SESSION['user_id'];
        
        $stmt = $conn->prepare("SELECT si.id AS id FROM secretary_info si WHERE si.accounts_id = ?;");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        if ($result->num_rows > 0) {        
            $user = $result->fetch_assoc();
            return $user['id'];
        } else {
            return "ID not found. Please update your personal information first.";
        }
    }        
}