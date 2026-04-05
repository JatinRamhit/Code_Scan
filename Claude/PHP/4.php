<!-- shell.php — DEMONSTRATION ONLY, never deploy -->
<?php
$allowed_commands = ['ls', 'pwd', 'date', 'whoami'];

$cmd = $_POST['command'] ?? '';

if ($cmd && in_array($cmd, $allowed_commands, true)) {
    $output = shell_exec(escapeshellcmd($cmd));
    echo '<pre>' . htmlspecialchars($output) . '</pre>';
} elseif ($cmd) {
    echo "Command not permitted.";
}
?>
<!DOCTYPE html>
<form method="post">
  <select name="command">
    <option value="ls">ls</option>
    <option value="pwd">pwd</option>
    <option value="date">date</option>
    <option value="whoami">whoami</option>
  </select>
  <button type="submit">Run</button>
</form>
