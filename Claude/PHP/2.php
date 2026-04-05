<!-- upload.php -->
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file     = $_FILES['file'];
    $allowed  = ['image/jpeg', 'image/png', 'application/pdf'];
    $max_size = 2 * 1024 * 1024; // 2 MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("Upload error code: " . $file['error']);
    }
    if (!in_array($file['type'], $allowed)) {
        die("File type not allowed.");
    }
    if ($file['size'] > $max_size) {
        die("File exceeds 2 MB limit.");
    }

    $dest = 'uploads/' . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        echo "File uploaded to: " . htmlspecialchars($dest);
    } else {
        echo "Failed to move uploaded file.";
    }
}
?>
<!DOCTYPE html>
<form method="post" enctype="multipart/form-data">
  <input type="file" name="file">
  <button type="submit">Upload</button>
</form>
