<?php
session_start();

global $conn;
require_once '../../../database/config.php';
require_once 'fetch-id.php';

$error;
$data = [];
$id;

function getFolderName($conn, $pid) {
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

function getMediaFolderName($conn, $id) {
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

function getAppointmentId($conn, $mediaFolderName) {
    $stmt = $conn->prepare("SELECT ar.id FROM appointment_requests ar WHERE ar.media_path = ?");
    $stmt->bind_param("s", $mediaFolderName);
    $stmt->execute();
    $result = $stmt->get_result();   
	$stmt->close(); 

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        return $row['id'];
    } else {
        return null;
    }
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    
    // Only show media of the appointment
    // $id = $_POST['id'] ?? "";
    // $pid = $_POST['pid'] ?? "";
    
    // $folderName = getFolderName($conn, $pid);
    // $mediaFolderName = getMediaFolderName($conn, $id );
    
    // if (!$mediaFolderName) {
    //     echo json_encode([]);
    //     exit;
    // }

    // $data = [];
    // $path = "../../../files/$folderName/$mediaFolderName";

    // if (is_dir($path)) {
    //     foreach (scandir($path) as $file) {
    //         if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])) {
    //             $data[] = "../../files/$folderName/$mediaFolderName/$file";
    //         }
    //     }
    // }

    // Show all media
    $pid = $_POST['pid'] ?? "";
    
    $folderName = getFolderName($conn, $pid);
    
    if (!$folderName) {
        echo json_encode([]);
        exit;
    }

    $data = [];
    $path = "../../../files/$folderName";

    if (is_dir($path)) {
        $subfolders = array_filter(glob("$path/*"), 'is_dir');

        foreach ($subfolders as $subfolder) {
            $mediaFolderName = basename($subfolder);
            $appointmentId = getAppointmentId($conn, $mediaFolderName);

            foreach (scandir($subfolder) as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $fullPath = "$subfolder/$file";
                    $relativePath = str_replace('../../../', '../../', $fullPath);

                    $data[] = [
                        "url" => $relativePath,
                        "filename" => $file,
                        "appointment_id" => $appointmentId,
                        "date" => date("Y-m-d h:i:s A", filemtime($fullPath))
                    ];
                }
            }
        }
    }
}

echo json_encode($data);