<?php
// Safe alternative: Allowlist approach for system commands
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Map specific inputs to hardcoded, safe commands
    switch ($action) {
        case 'disk_space':
            $output = shell_exec('df -h');
            break;
        case 'uptime':
            $output = shell_exec('uptime');
            break;
        default:
            $output = "Invalid action.";
            break;
    }
    
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
}
?>
<a href="?action=disk_space">Check Disk Space</a> | 
<a href="?action=uptime">Check Uptime</a>
