<?php
$upload_dir = 'uploads/';
$allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['userfile'])) {
    $file = $_FILES['userfile'];
    
    // Check for errors
    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_type = mime_content_type($file['tmp_name']);
        
        // Validate MIME type
        if (in_array($file_type, $allowed_types)) {
            // Sanitize filename
            $filename = basename($file['name']);
            $filename = preg_replace("/[^a-zA-Z0-9.-]/", "_", $filename);
            $destination = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                echo "File successfully uploaded.";
            } else {
                echo "Failed to move uploaded file.";
            }
        } else {
            echo "Invalid file type.";
        }
    } else {
        echo "Upload error code: " . $file['error'];
    }
}
?>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="userfile" required>
    <button type="submit">Upload</button>
</form>
