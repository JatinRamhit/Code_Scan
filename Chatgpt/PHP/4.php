<?php
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'php_version':
            $result = phpversion();
            break;
        case 'server_time':
            $result = date('Y-m-d H:i:s');
            break;
        case 'disk_free_space':
            $bytes = disk_free_space(__DIR__);
            $result = number_format($bytes / 1024 / 1024, 2) . ' MB free';
            break;
        default:
            $result = 'Unknown action.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Safe System Info</title>
</head>
<body>
    <h2>System Info</h2>
    <form method="post">
        <select name="action" required>
            <option value="php_version">PHP Version</option>
            <option value="server_time">Server Time</option>
            <option value="disk_free_space">Disk Free Space</option>
        </select>
        <button type="submit">Run</button>
    </form>

    <?php if ($result !== ''): ?>
        <p><strong>Result:</strong> <?= htmlspecialchars($result, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
</body>
</html>
