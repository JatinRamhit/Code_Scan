<?php
session_start();

$host = 'localhost';
$db   = 'testdb';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL
        )
    ");

    $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $check->execute(['admin']);

    if ($check->fetchColumn() == 0) {
        $hash = password_hash('secret123', PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        $insert->execute(['admin', $hash]);
    }
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$error = '';

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userRow && password_verify($password, $userRow['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userRow['id'];
        $_SESSION['username'] = $userRow['username'];
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Secure Login</title>
</head>
<body>
<?php if (isset($_SESSION['user_id'])): ?>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></h2>
    <p>You are logged in securely.</p>
    <p>Demo account: admin / secret123</p>
    <a href="?logout=1">Logout</a>
<?php else: ?>
    <h2>Secure Login</h2>
    <p>Demo account: admin / secret123</p>
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Username:
            <input type="text" name="username" required>
        </label>
        <br><br>
        <label>Password:
            <input type="password" name="password" required>
        </label>
        <br><br>
        <button type="submit">Login</button>
    </form>
<?php endif; ?>
</body>
</html>
