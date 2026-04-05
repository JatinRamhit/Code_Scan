<!-- login.php -->
<?php
$username = $_GET['username'] ?? '';
$password = $_GET['password'] ?? '';

$valid_user = 'admin';
$valid_pass = '1234';

if ($username === $valid_user && $password === $valid_pass) {
    echo "Login successful. Welcome, " . htmlspecialchars($username);
} elseif ($username !== '') {
    echo "Invalid credentials.";
} else {
    echo '<a href="?username=admin&password=1234">Test login</a>';
}
?>
