<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['section'];
    
    // Define the correct path to your content file
    $contentFile = 'content.json';
    
    // Check if the file exists
    if (!file_exists($contentFile)) {
        echo json_encode(['status' => 'error', 'message' => "Content file not found: $contentFile"]);
        exit;
    }
    
    // Try to read the content file
    $contentJson = file_get_contents($contentFile);
    if ($contentJson === false) {
        echo json_encode(['status' => 'error', 'message' => "Unable to read content file"]);
        exit;
    }
    
    // Try to decode the JSON
    $content = json_decode($contentJson, true);
    if ($content === null) {
        echo json_encode(['status' => 'error', 'message' => "Invalid JSON in content file"]);
        exit;
    }
    
    $uploadDir = 'images/';
    
    // Make sure upload directory exists
    if (!file_exists($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        echo json_encode(['status' => 'error', 'message' => "Failed to create upload directory"]);
        exit;
    }
    
    // Helper function to get clean filename (without images/ prefix)
    function getCleanFilename($filename) {
        return str_replace('images/', '', $filename);
    }
    
    if ($section === 'services') {
        $count = intval($_POST['count']);
        $services = [];
        
        for ($i = 0; $i < $count; $i++) {
            $title = htmlspecialchars($_POST["title$i"]);
            $description = htmlspecialchars($_POST["description$i"]);
            $existingImage = isset($_POST["existingImage$i"]) ? getCleanFilename($_POST["existingImage$i"]) : "";
            $imageField = "Image$i";
            $imageName = $existingImage;
            
            if (isset($_FILES[$imageField]) && $_FILES[$imageField]['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES[$imageField]['tmp_name'];
                $newName = basename($_FILES[$imageField]['name']);
                $targetPath = $uploadDir . $newName;
                
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $imageName = $newName;
                } else {
                    echo json_encode(['status' => 'error', 'message' => "Failed to upload image for service $i"]);
                    exit;
                }
            }
            
            $services[] = [
                "title" => $title,
                "description" => $description,
                "image" => $imageName  // Store just the filename without images/ prefix
            ];
        }
        
        $content['services'] = $services;
    } elseif ($section === 'aboutus') {
        $content['about']['hero']['title'] = htmlspecialchars($_POST['heroTitle']);
        $content['about']['hero']['subtitle'] = htmlspecialchars($_POST['heroSubtitle']);
        $content['about']['mission'] = htmlspecialchars($_POST['mission']);
        $content['about']['vision'] = htmlspecialchars($_POST['vision']);
        
        // Handle core values
        if (isset($content['about']['values'])) {
            for ($i = 0; $i < count($content['about']['values']); $i++) {
                if (isset($_POST["valueIcon$i"])) {
                    $content['about']['values'][$i]['icon'] = htmlspecialchars($_POST["valueIcon$i"]);
                }
                if (isset($_POST["valueTitle$i"])) {
                    $content['about']['values'][$i]['title'] = htmlspecialchars($_POST["valueTitle$i"]);
                }
                if (isset($_POST["valueDesc$i"])) {
                    $content['about']['values'][$i]['description'] = htmlspecialchars($_POST["valueDesc$i"]);
                }
            }
        }
        
        // Handle team members
        if (isset($content['about']['team'])) {
            for ($i = 0; $i < count($content['about']['team']); $i++) {
                if (isset($_POST["teamName$i"])) {
                    $content['about']['team'][$i]['name'] = htmlspecialchars($_POST["teamName$i"]);
                }
                if (isset($_POST["teamRole$i"])) {
                    $content['about']['team'][$i]['role'] = htmlspecialchars($_POST["teamRole$i"]);
                }
                if (isset($_POST["teamBio$i"])) {
                    $content['about']['team'][$i]['bio'] = htmlspecialchars($_POST["teamBio$i"]);
                }
                
                $existingImage = isset($_POST["existingTeamImage$i"]) ? getCleanFilename($_POST["existingTeamImage$i"]) : "";
                $imageField = "TeamImage$i";
                $imageName = $existingImage;
                
                if (isset($_FILES[$imageField]) && $_FILES[$imageField]['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES[$imageField]['tmp_name'];
                    $newName = basename($_FILES[$imageField]['name']);
                    $targetPath = $uploadDir . $newName;
                    
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $imageName = $newName;
                    }
                }
                
                $content['about']['team'][$i]['image'] = $imageName;  // Store just the filename
            }
        }
        
        // Handle gallery
        if (isset($content['about']['gallery']) && isset($_POST['galleryCount'])) {
            $galleryCount = intval($_POST['galleryCount']);
            for ($i = 0; $i < $galleryCount; $i++) {
                if (isset($_POST["galleryAlt$i"])) {
                    $content['about']['gallery'][$i]['alt'] = htmlspecialchars($_POST["galleryAlt$i"]);
                }
                
                $existingImage = isset($_POST["existingGalleryImage$i"]) ? getCleanFilename($_POST["existingGalleryImage$i"]) : "";
                $imageField = "GalleryImage$i";
                $imageName = $existingImage;
                
                if (isset($_FILES[$imageField]) && $_FILES[$imageField]['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES[$imageField]['tmp_name'];
                    $newName = basename($_FILES[$imageField]['name']);
                    $targetPath = $uploadDir . $newName;
                    
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $imageName = $newName;
                    }
                }
                
                $content['about']['gallery'][$i]['image'] = "images/" . $imageName;
            }
        }
        
        // Handle CTA
        if (isset($content['about']['cta'])) {
            if (isset($_POST['ctaText'])) {
                $content['about']['cta']['text'] = htmlspecialchars($_POST['ctaText']);
            }
            if (isset($_POST['ctaSubtext'])) {
                $content['about']['cta']['subtext'] = htmlspecialchars($_POST['ctaSubtext']);
            }
            if (isset($_POST['ctaButtonLabel'])) {
                $content['about']['cta']['button']['label'] = htmlspecialchars($_POST['ctaButtonLabel']);
            }
            if (isset($_POST['ctaButtonLink'])) {
                $content['about']['cta']['button']['link'] = htmlspecialchars($_POST['ctaButtonLink']);
            }
        }
    } elseif ($section === 'contact') {
        // Handle contact form settings
        if (!isset($content['contact'])) {
            $content['contact'] = [];
        }
        
        $content['contact']['header'] = htmlspecialchars($_POST['contactHeader']);
        
        // Update contact form field labels
        if (!isset($content['contact']['fields'])) {
            $content['contact']['fields'] = [];
        }
        $content['contact']['fields']['name'] = htmlspecialchars($_POST['contactNameField']);
        $content['contact']['fields']['email'] = htmlspecialchars($_POST['contactEmailField']);
        $content['contact']['fields']['subject'] = htmlspecialchars($_POST['contactSubjectField']);
        $content['contact']['fields']['message'] = htmlspecialchars($_POST['contactMessageField']);
        
        // Update button and messages
        $content['contact']['button'] = htmlspecialchars($_POST['contactButton']);
        $content['contact']['success'] = htmlspecialchars($_POST['contactSuccess']);
        $content['contact']['error'] = htmlspecialchars($_POST['contactError']);
    } 
    
    // Try to write the updated content
    $result = file_put_contents($contentFile, json_encode($content, JSON_PRETTY_PRINT));
    
    if ($result === false) {
        echo json_encode(['status' => 'error', 'message' => "Failed to write to content file. Check permissions."]);
    } else {
        echo json_encode(['status' => 'success', 'message' => "Content updated successfully!"]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => "Invalid request method"]);
}
?>