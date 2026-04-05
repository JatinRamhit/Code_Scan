<?php
$uploadDir = __DIR__ . '/uploads/';
$maxSize = 2 * 1024 * 1024; // 2MB
$allowedMimeTypes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'application/pdf' => 'pdf'
];

$message = '';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = 'Upload failed.';
    } elseif ($file['size'] > $maxSize) {
        $message = 'File is too large.';
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!isset($allowedMimeTypes[$mimeType])) {
            $message = 'Invalid file type.';
        } else {
            $extension = $allowedMimeTypes[$mimeType];
            $newName = bin2hex(random_bytes(16)) . '.' . $extension;
            $destination = $uploadDir . $newName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $message = 'File uploaded successfully: ' . $newName;
            } else {
                $message = 'Could not save the file.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>File Upload</title>
</head>
<body>
    <h2>Upload a File</h2>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
