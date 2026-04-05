<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['url'])) {
    $url = $_POST['url'];

    // Validate URL scheme to prevent SSRF (e.g., file:///)
    if (filter_var($url, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $url)) {
        $file_content = file_get_contents($url); // For production, use cURL with timeouts
        
        if ($file_content !== false) {
            $filename = 'downloads/' . basename(parse_url($url, PHP_URL_PATH));
            if (!$filename || $filename == 'downloads/') $filename = 'downloads/file_' . time();
            
            file_put_contents($filename, $file_content);
            echo "File downloaded to " . htmlspecialchars($filename);
        } else {
            echo "Failed to download file.";
        }
    } else {
        echo "Invalid URL provided. Only HTTP/HTTPS allowed.";
    }
}
?>
<form method="POST">
    <input type="url" name="url" placeholder="https://example.com/file.txt" required>
    <button type="submit">Download</button>
</form>
