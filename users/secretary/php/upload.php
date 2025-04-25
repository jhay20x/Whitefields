<?php

session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$data = [];

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $path = "../../../files/";

    $foldername = getFolderName() === null ? getRandomString() : getFolderName();

    if(isset($_FILES["fileToUpload"]) &&  $_FILES["fileToUpload"]["error"] == 0) {
        $target_dir = $foldername; 
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]); 

        if(!is_dir("$path$foldername")) {
            mkdir("$path$foldername");
        }

        $allowed_ext = ["jpg" => "image/jpg", 
                            "jpeg" => "image/jpeg",
                            "png" => "image/png"];
        $file_name = $_FILES["fileToUpload"]["name"]; 
        $file_type = $_FILES["fileToUpload"]["type"]; 
        $file_size = $_FILES["fileToUpload"]["size"]; 
        
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);

        if (!array_key_exists($ext, $allowed_ext)) { 
            $error = "Please select a valid file format."; 
        }
        
        $maxsize = 2 * 1024 * 1024; 
        
        if ($file_size > $maxsize) { 
            $error = "File size is larger than the allowed 2MB limit."; 
        }                     

        if (in_array($file_type, $allowed_ext)) { 
            if (file_exists("upload/" . $_FILES["fileToUpload"]["name"])) { 
                $error = $_FILES["fileToUpload"]["name"]." is already exists.";
            }         
            else {
                $imgfile = $_FILES["fileToUpload"]["tmp_name"];
                $image = imagecreatefromstring(file_get_contents($imgfile));
                list($w, $h) = getimagesize($imgfile);
                if ($w < $h) {
                    $image = imagecrop($image, 
                    [
                        "x" => 0,
                        "y" => ($h - $w) / 2,
                        "width" => $w,
                        "height" => $w
                    ]
                    );
                } else if ($h < $w) {
                    $image = imagecrop($image, 
                    [
                        "x" => ($w - $h) / 2,
                        "y" => 0,
                        "width" => $h,
                        "height" => $h
                        ]
                    );
                }
                $target_file = $foldername;
                if (imagejpeg($image, "$path$foldername/profile.jpg")) {
                    $stmt = $conn->prepare("UPDATE `accounts` SET `profile_path`= ? WHERE `username` = ?");
                    $stmt->bind_param("ss", $target_file, $_SESSION['user_username']);                    
                    if ($stmt->execute()) {
                        $message = "Your profile has been successfully uploaded.";
                    } else {
                        $error = "Sorry, there was an error uploading your file."; 
                    }
                }  
                else { 
                    $error = "Sorry, there was an error uploading your file."; 
                } 
            } 
        } 
        else { 
            $error = "Please select a valid file format."; 
        } 
    } 
    else {
        $error = "Failed to upload. No image selected."; 
    } 
}

function getFolderName() {
    global $conn;
    $id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT profile_path FROM accounts WHERE `id` = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();   
	$stmt->close(); 

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        return $row['profile_path'];
    } else {
        return "Not Found";
    }
}

function getRandomString() {
    return uniqid();
}

if (!empty($error)) {
    $data['success'] = false;
    $data['error'] = $error;
} else {
    $data['success'] = true;
    $data['message'] = $message;
}

echo json_encode($data);