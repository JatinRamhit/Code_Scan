<?php
$message = '';
$downloadDir = __DIR__ . '/downloads/';

if (!is_dir($downloadDir)) {
    mkdir($downloadDir, 0755, true);
}

function isPrivateIp($ip) {
    return filter_var(
        $ip,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    ) === false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim($_POST['url'] ?? '');

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $message = 'Invalid URL.';
    } else {
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = $parts['host'] ?? '';

        if (!in_array($scheme, ['http', 'https'], true)) {
            $message = 'Only HTTP and HTTPS URLs are allowed.';
        } elseif ($host === '') {
            $message = 'Missing host.';
        } else {
            $ip = gethostbyname($host);

            if ($ip === $host || isPrivateIp($ip)) {
                $message = 'That host is not allowed.';
            } else {
                $context = stream_context_create([
                    'http' => ['timeout' => 10],
                    'https' => ['timeout' => 10]
                ]);

                $data = @file_get_contents($url, false, $context);

                if ($data === false) {
                    $message = 'Could not download the file.';
                } elseif (strlen($data) > 2 * 1024 * 1024) {
                    $message = 'File is too large.';
                } else {
                    $path = parse_url($url, PHP_URL_PATH);
                    $basename = basename($path ?: 'downloaded_file');
                    $basename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $basename);

                    if ($basename === '' || $basename === '.' || $basename === '..') {
                        $basename = 'downloaded_file';
                    }

                    $filename = time() . '_' . $basename;
                    $target = $downloadDir . $filename;

                    if (file_put_contents($target, $data) !== false) {
                        $message = 'File saved successfully as: ' . $filename;
                    } else {
                        $message = 'Could not save the file.';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Safe File Downloader</title>
</head>
<body>
    <h2>Download a File</h2>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post">
        <label>File URL:
            <input type="url" name="url" size="60" required>
        </label>
        <button type="submit">Download</button>
    </form>
</body>
</html>
