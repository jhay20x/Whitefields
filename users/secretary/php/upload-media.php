<?php

session_start();

global $conn;
require_once '../../../database/config.php';

$error;
$data = [];

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    
    $id = $_POST['id'] ?? "";
    $pid = $_POST['pid'] ?? "";
    
    $tempFolderName = getFolderName($conn, $pid);
    $tempMediaFolderName = getMediaFolderName($conn, $id );

    $mediaFolderName = $tempMediaFolderName ?? getRandomString();    
    $foldername = $tempFolderName ?? getRandomString();
    
    $path = "../../../files/$foldername/$mediaFolderName";

    if (!empty($_FILES['fileToUpload']['name'][0])) {
        $files = $_FILES['fileToUpload'];
        $allowed_ext = ['jpg' => 'image/jpg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
        $maxsize = 6 * 1024 * 1024;
    
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    
        foreach ($files['name'] as $index => $file_name) {
            $file_tmp = $files['tmp_name'][$index];
            $file_type = $files['type'][$index];
            $file_size = $files['size'][$index];
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
    
            if (!array_key_exists($ext, $allowed_ext)) {
                $error = "One or more files have an invalid format.";
                continue;
            }
    
            if ($file_size > $maxsize) {
                $error = "One or more files exceed the 6MB size limit.";
                continue;
            }
    
            $uniqueName = uniqid() . '.' . $ext;
            $destination = "$path/$uniqueName";
    
            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($file_tmp);
                    imagejpeg($image, $destination, 75);
                    break;
                case 'png':
                    $image = imagecreatefrompng($file_tmp);
                    imagepng($image, $destination, 6);
                    break;
            }
    
            imagedestroy($image);
        }

        if ($tempFolderName === null) {
            $stmt = $conn->prepare("UPDATE accounts ac LEFT OUTER JOIN patient_info pi ON pi.accounts_id = ac.id SET ac.profile_path = ? WHERE pi.id = ?");
            $stmt->bind_param("si", $foldername, $pid);
            $stmt->execute();
            $stmt->close();
        }

        if ($tempMediaFolderName === null) {
            $stmt = $conn->prepare("UPDATE appointment_requests SET media_path = ? WHERE id = ?");
            $stmt->bind_param("si", $mediaFolderName, $id);
            $stmt->execute();
            $stmt->close();
        }
    
        $message = "Upload complete. Patient media has been saved.";
    } else {
        $error = "No images selected.";
    }
}

function getFolderName($conn, $pid ) {
    $stmt = $conn->prepare("SELECT ac.profile_path FROM accounts ac LEFT OUTER JOIN patient_info pi ON pi.accounts_id = ac.id WHERE pi.id = ?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();   
	$stmt->close(); 

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        return $row['profile_path'];
    } else {
        return null;
    }
}

function getMediaFolderName($conn, $id ) {
    $stmt = $conn->prepare("SELECT ar.media_path FROM appointment_requests ar WHERE ar.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();   
	$stmt->close(); 

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        return $row['media_path'];
    } else {
        return null;
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