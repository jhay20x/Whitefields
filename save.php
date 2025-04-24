<?php
// Check if data is coming as JSON
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if (strpos($contentType, 'application/json') !== false) {
    // Handle JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
        exit;
    }
    
    $section = $data['section'];
    $updated = $data['data'];
    
    $filename = 'content.json';
    
    // Check if file exists
    if (!file_exists($filename)) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Content file not found']);
        exit;
    }
    
    $current = json_decode(file_get_contents($filename), true);
    if (!$current) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON in content file']);
        exit;
    }
    
    // Update based on section
    if ($section === 'services') {
        $current['services'] = $updated['services'];
    } elseif ($section === 'aboutus') {
        // Update about section with all its subfields
        if (isset($updated['about'])) {
            // Ensure we don't overwrite fields not included in the update
            foreach ($updated['about'] as $key => $value) {
                $current['about'][$key] = $value;
            }
        }
    } elseif ($section === 'contact') {
        // Fix: Update contact at root level, not under about->cta
        $current['contact'] = $updated['contact'];
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid section specified']);
        exit;
    }
    
    $result = file_put_contents($filename, json_encode($current, JSON_PRETTY_PRINT));
    
    if ($result === false) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to save content']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Content updated successfully']);
    }
} else {
    // This is the case where you should handle form data and file uploads
    // as in your original update_content.php script
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Content-Type must be application/json']);
}
?>