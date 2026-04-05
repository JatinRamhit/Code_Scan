<!-- downloader.php -->
<?php
$save_dir   = __DIR__ . '/downloads/';
$allowed_hosts = ['example.com', 'trusted-cdn.net'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim($_POST['url'] ?? '');

    // 1. Validate it is a proper HTTP/HTTPS URL
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        die("Invalid URL.");
    }

    // 2. Restrict to trusted hosts (prevents SSRF)
    $host = parse_url($url, PHP_URL_HOST);
    if (!in_array($host, $allowed_hosts, true)) {
        die("Host not permitted: " . htmlspecialchars($host));
    }

    // 3. Sanitize filename — use only basename, strip dangerous chars
    $raw_name = basename(parse_url($url, PHP_URL_PATH));
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $raw_name);
    $dest     = $save_dir . $filename;

    // 4. Fetch with a timeout and size cap (10 MB)
    $ctx = stream_context_create([
        'http' => ['timeout' => 10],
        'ssl'  => ['verify_peer' => true]
    ]);
    $data = file_get_contents($url, false, $ctx);

    if ($data === false) {
        die("Failed to fetch the URL.");
    }
    if (strlen($data) > 10 * 1024 * 1024) {
        die("File exceeds 10 MB limit.");
    }

    file_put_contents($dest, $data);
    echo "Saved to: " . htmlspecialchars($filename);
}
?>
<!DOCTYPE html>
<form method="post">
  <input name="url" type="url" placeholder="https://example.com/file.pdf" style="width:360px">
  <button type="submit">Download</button>
</form>
