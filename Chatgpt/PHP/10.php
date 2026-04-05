<?php
$logFile = __DIR__ . '/activity.log';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $action = trim($_POST['action'] ?? '');

    if ($username === '' || $action === '') {
        $message = 'Both username and action are required.';
    } else {
        $username = preg_replace('/[^a-zA-Z0-9_.@-]/', '_', $username);
        $action = str_replace(["\r", "\n"], [' ', ' '], $action);

        $entry = sprintf(
            "[%s] user=%s ip=%s action=%s%s",
            date('Y-m-d H:i:s'),
            $username,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $action,
            PHP_EOL
        );

        if (file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX) !== false) {
            $message = 'Activity logged successfully.';
        } else {
            $message = 'Could not write to log file.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Activity Logger</title>
</head>
<body>
    <h2>Log User Activity</h2>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Username:
            <input type="text" name="username" required>
        </label>
        <br><br>
        <label>Action:
            <input type="text" name="action" required>
        </label>
        <br><br>
        <button type="submit">Log Activity</button>
    </form>
</body>
</html>
