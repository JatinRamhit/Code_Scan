<?php
// Database configuration
$host = '127.0.0.1';
$db   = 'test_db';
$user = 'db_user';
$pass = 'db_pass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepared statement prevents SQL Injection
        $stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $user_record = $stmt->fetch();

        if ($user_record && password_verify($password, $user_record['password_hash'])) {
            echo "Login secure and successful!";
        } else {
            echo "Invalid credentials.";
        }
    }
} catch (PDOException $e) {
    // Log error, don't display to user
    error_log($e->getMessage());
    echo "Database error occurred.";
}
?>
