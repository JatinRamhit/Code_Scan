<?php
// Secure POST Login Alternative
session_start();

// Dummy credentials for demonstration (in reality, fetch from DB)
$valid_username = 'admin';
$valid_password_hash = password_hash('secret123', PASSWORD_DEFAULT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($user === $valid_username && password_verify($pass, $valid_password_hash)) {
        $_SESSION['logged_in'] = true;
        echo "Login successful!";
    } else {
        echo "Invalid credentials.";
    }
}
?>
<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
