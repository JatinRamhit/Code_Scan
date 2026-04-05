<?php
session_start();

$validUser = 'admin';
$validPass = 'secret123'; // Demo only

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $validUser && $password === $validPass) {
        $_SESSION['user'] = $username;
    } else {
        $error = 'Invalid username or password.';
    }
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Simple Login</title>
</head>
<body>
<?php if (isset($_SESSION['user'])): ?>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8') ?></h2>
    <p>You are logged in.</p>
    <a href="?logout=1">Logout</a>
<?php else: ?>
    <h2>Login</h2>
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
