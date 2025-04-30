<?php

$error;
$data = [];
$id;

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {    
    function fetchPatientID() {
        global $conn;
        
        $user_id = $_SESSION['user_id'];
        
        $stmt = $conn->prepare("SELECT pi.id AS id FROM patient_info pi WHERE pi.accounts_id = ?;");
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

    function checkMedical($conn, $mid) {
      $stmt = $conn->prepare("SELECT mi.id AS id FROM medical_history mi WHERE mi.patient_id = ?;");
      $stmt->bind_param("i", $mid);
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

    function checkDental($conn, $did) {
      $stmt = $conn->prepare("SELECT di.id AS id FROM dental_history di WHERE di.patient_id = ?;");
      $stmt->bind_param("i", $did);
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