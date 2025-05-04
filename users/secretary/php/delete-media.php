<?php
session_start();

sleep(1);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_username']) && isset($_SESSION['account_type'])) {
    $url = $_POST['url'] ?? '';

    if (!$url) {
        http_response_code(400);
        $data['success'] = false;
        $data['error'] = "Media deletion failed. Invalid path.";
        exit;
    }

    $fullPath = str_replace('../../', '../../../', $url);

    if (file_exists($fullPath)) {
        unlink($fullPath);
        $data['success'] = true;
        $data['message'] = "The media has been successfully deleted.";
    } else {
        http_response_code(404);
        $data['success'] = false;
        $data['error'] = "Media deletion failed. File not found.";
    }
}

echo json_encode($data);