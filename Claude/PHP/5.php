<!-- secure_login.php -->
<?php
session_start();

$host = 'localhost'; $db = 'myapp';
$user = 'dbuser';   $pass = 'dbpass';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database connection failed.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare(
            "SELECT id, username, password_hash FROM users WHERE username = :u LIMIT 1"
        );
        $stmt->execute([':u' => $username]);
        $user_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_row && password_verify($password, $user_row['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user_row['id'];
            $_SESSION['username']  = $user_row['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<h2>Login</h2>
<?php if ($error): ?>
  <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="post">
  <input name="username" placeholder="Username" required><br>
  <input name="password" type="password" placeholder="Password" required><br>
  <button type="submit">Log in</button>
</form>
<!-- Store passwords with: password_hash($plaintext, PASSWORD_BCRYPT) -->
