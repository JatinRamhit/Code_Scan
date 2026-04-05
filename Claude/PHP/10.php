<!-- activity_log.php -->
<?php
session_start();

$log_file = __DIR__ . '/logs/activity.log';

function log_activity(string $log_file, string $action): void {
    // Sanitize: strip newlines and non-printable chars to prevent log injection
    $safe_action = preg_replace('/[\r\n\x00-\x1F\x7F]/', ' ', $action);
    $safe_action = substr($safe_action, 0, 200); // cap length

    $ip        = filter_var($_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP) ?: 'unknown';
    $user_id   = (int) ($_SESSION['user_id'] ?? 0);
    $timestamp = date('Y-m-d H:i:s');

    $line = sprintf(
        "[%s] user_id=%d ip=%s action=%s\n",
        $timestamp, $user_id, $ip, $safe_action
    );

    file_put_contents($log_file, $line, FILE_APPEND | LOCK_EX);
}

// Example: log an action from POST input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim($_POST['action'] ?? '');

    if ($action !== '') {
        log_activity($log_file, $action);
        echo "Activity logged.";
    } else {
        echo "No action provided.";
    }
}
?>
<!DOCTYPE html>
<form method="post">
  <input name="action" placeholder="Describe your action" required style="width:300px">
  <button type="submit">Log</button>
</form>
<!-- Ensure logs/ directory exists and is NOT web-accessible -->
<!-- Add to .htaccess: Deny from all -->
