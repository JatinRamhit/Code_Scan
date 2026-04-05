<?php
$log_file = 'activity.log';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['action'])) {
    $user_action = $_POST['action'];
    
    // Remove newlines to prevent log forging/injection
    $user_action = str_replace(array("\r", "\n"), '', $user_action);
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $timestamp = date("Y-m-d H:i:s");

    $log_entry = sprintf("[%s] IP: %s | Action: %s" . PHP_EOL, $timestamp, $ip_address, $user_action);

    // FILE_APPEND ensures we add to the end, LOCK_EX prevents concurrent write issues
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    
    echo "Action logged securely.";
}
?>
<form method="POST">
    <input type="text" name="action" placeholder="What did you do?" required>
    <button type="submit">Log Action</button>
</form>
