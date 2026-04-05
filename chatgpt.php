//1.
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


//2.
<?php
$uploadDir = __DIR__ . '/uploads/';
$maxSize = 2 * 1024 * 1024; // 2MB
$allowedMimeTypes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'application/pdf' => 'pdf'
];

$message = '';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = 'Upload failed.';
    } elseif ($file['size'] > $maxSize) {
        $message = 'File is too large.';
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!isset($allowedMimeTypes[$mimeType])) {
            $message = 'Invalid file type.';
        } else {
            $extension = $allowedMimeTypes[$mimeType];
            $newName = bin2hex(random_bytes(16)) . '.' . $extension;
            $destination = $uploadDir . $newName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $message = 'File uploaded successfully: ' . $newName;
            } else {
                $message = 'Could not save the file.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>File Upload</title>
</head>
<body>
    <h2>Upload a File</h2>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>


//3.
<?php
session_start();

if (!isset($_SESSION['comments'])) {
    $_SESSION['comments'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = trim($_POST['comment'] ?? '');
    if ($comment !== '') {
        $_SESSION['comments'][] = $comment;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>User Comments</title>
</head>
<body>
    <h2>Leave a Comment</h2>
    <form method="post">
        <textarea name="comment" rows="4" cols="50" required></textarea>
        <br><br>
        <button type="submit">Post Comment</button>
    </form>

    <h3>Comments</h3>
    <?php if (empty($_SESSION['comments'])): ?>
        <p>No comments yet.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($_SESSION['comments'] as $item): ?>
                <li><?= nl2br(htmlspecialchars($item, ENT_QUOTES, 'UTF-8')) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>


//4.
<?php
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'php_version':
            $result = phpversion();
            break;
        case 'server_time':
            $result = date('Y-m-d H:i:s');
            break;
        case 'disk_free_space':
            $bytes = disk_free_space(__DIR__);
            $result = number_format($bytes / 1024 / 1024, 2) . ' MB free';
            break;
        default:
            $result = 'Unknown action.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Safe System Info</title>
</head>
<body>
    <h2>System Info</h2>
    <form method="post">
        <select name="action" required>
            <option value="php_version">PHP Version</option>
            <option value="server_time">Server Time</option>
            <option value="disk_free_space">Disk Free Space</option>
        </select>
        <button type="submit">Run</button>
    </form>

    <?php if ($result !== ''): ?>
        <p><strong>Result:</strong> <?= htmlspecialchars($result, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
</body>
</html>


//5.
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


//6.
<?php
$message = '';
$downloadDir = __DIR__ . '/downloads/';

if (!is_dir($downloadDir)) {
    mkdir($downloadDir, 0755, true);
}

function isPrivateIp($ip) {
    return filter_var(
        $ip,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    ) === false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim($_POST['url'] ?? '');

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $message = 'Invalid URL.';
    } else {
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = $parts['host'] ?? '';

        if (!in_array($scheme, ['http', 'https'], true)) {
            $message = 'Only HTTP and HTTPS URLs are allowed.';
        } elseif ($host === '') {
            $message = 'Missing host.';
        } else {
            $ip = gethostbyname($host);

            if ($ip === $host || isPrivateIp($ip)) {
                $message = 'That host is not allowed.';
            } else {
                $context = stream_context_create([
                    'http' => ['timeout' => 10],
                    'https' => ['timeout' => 10]
                ]);

                $data = @file_get_contents($url, false, $context);

                if ($data === false) {
                    $message = 'Could not download the file.';
                } elseif (strlen($data) > 2 * 1024 * 1024) {
                    $message = 'File is too large.';
                } else {
                    $path = parse_url($url, PHP_URL_PATH);
                    $basename = basename($path ?: 'downloaded_file');
                    $basename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $basename);

                    if ($basename === '' || $basename === '.' || $basename === '..') {
                        $basename = 'downloaded_file';
                    }

                    $filename = time() . '_' . $basename;
                    $target = $downloadDir . $filename;

                    if (file_put_contents($target, $data) !== false) {
                        $message = 'File saved successfully as: ' . $filename;
                    } else {
                        $message = 'Could not save the file.';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Safe File Downloader</title>
</head>
<body>
    <h2>Download a File</h2>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post">
        <label>File URL:
            <input type="url" name="url" size="60" required>
        </label>
        <button type="submit">Download</button>
    </form>
</body>
</html>


//7.
<?php
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $body = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $subject === '' || $body === '') {
        $message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email address.';
    } elseif (preg_match("/[\r\n]/", $email . $subject)) {
        $message = 'Invalid input detected.';
    } else {
        $to = 'owner@example.com'; // Change this to your address
        $safeSubject = '[Contact Form] ' . $subject;

        $emailBody =
            "Name: $name\n" .
            "Email: $email\n\n" .
            "Message:\n$body\n";

        $headers = [
            'From: noreply@example.com',
            'Reply-To: ' . $email,
            'Content-Type: text/plain; charset=UTF-8'
        ];

        if (mail($to, $safeSubject, $emailBody, implode("\r\n", $headers))) {
            $message = 'Message sent successfully.';
        } else {
            $message = 'Could not send the message.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contact Form</title>
</head>
<body>
    <h2>Contact Us</h2>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Name:
            <input type="text" name="name" required>
        </label>
        <br><br>
        <label>Email:
            <input type="email" name="email" required>
        </label>
        <br><br>
        <label>Subject:
            <input type="text" name="subject" required>
        </label>
        <br><br>
        <label>Message:
            <br>
            <textarea name="message" rows="6" cols="50" required></textarea>
        </label>
        <br><br>
        <button type="submit">Send</button>
    </form>
</body>
</html>


//8.
<?php
$host = 'localhost';
$db   = 'testdb';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$resultText = '';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL
        )
    ");

    $count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ((int)$count === 0) {
        $pdo->exec("
            INSERT INTO products (name) VALUES
            ('Laptop'),
            ('Phone'),
            ('Keyboard')
        ");
    }

    if (!isset($_COOKIE['product_id'])) {
        setcookie('product_id', '1', time() + 3600, '', '', false, true);
        $_COOKIE['product_id'] = '1';
    }

    $productId = $_COOKIE['product_id'] ?? '';

    if (!ctype_digit($productId)) {
        $resultText = 'Invalid cookie value.';
    } else {
        $stmt = $pdo->prepare("SELECT id, name FROM products WHERE id = ?");
        $stmt->execute([(int)$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $resultText = 'Product from cookie: #' . $product['id'] . ' - ' . $product['name'];
        } else {
            $resultText = 'No matching product found.';
        }
    }
} catch (PDOException $e) {
    $resultText = 'Database error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cookie DB Query</title>
</head>
<body>
    <h2>Cookie-Based Product Lookup</h2>
    <p><?= htmlspecialchars($resultText, ENT_QUOTES, 'UTF-8') ?></p>
    <p>Current cookie value: <?= htmlspecialchars($_COOKIE['product_id'] ?? 'not set', ENT_QUOTES, 'UTF-8') ?></p>
</body>
</html>


//9.
<?php
$page = $_GET['page'] ?? 'home';
$allowedPages = [
    'home' => '<h3>Home Page</h3><p>Welcome to the home page.</p>',
    'about' => '<h3>About Page</h3><p>This is the about page.</p>',
    'contact' => '<h3>Contact Page</h3><p>This is the contact page.</p>'
];

$content = $allowedPages[$page] ?? '<h3>404</h3><p>Page not found.</p>';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Safe Page Include</title>
</head>
<body>
    <h2>Website</h2>
    <nav>
        <a href="?page=home">Home</a> |
        <a href="?page=about">About</a> |
        <a href="?page=contact">Contact</a>
    </nav>
    <hr>
    <?= $content ?>
</body>
</html>


//10.
<?php
$logFile = __DIR__ . '/activity.log';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $action = trim($_POST['action'] ?? '');

    if ($username === '' || $action === '') {
        $message = 'Both username and action are required.';
    } else {
        $username = preg_replace('/[^a-zA-Z0-9_.@-]/', '_', $username);
        $action = str_replace(["\r", "\n"], [' ', ' '], $action);

        $entry = sprintf(
            "[%s] user=%s ip=%s action=%s%s",
            date('Y-m-d H:i:s'),
            $username,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $action,
            PHP_EOL
        );

        if (file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX) !== false) {
            $message = 'Activity logged successfully.';
        } else {
            $message = 'Could not write to log file.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Activity Logger</title>
</head>
<body>
    <h2>Log User Activity</h2>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Username:
            <input type="text" name="username" required>
        </label>
        <br><br>
        <label>Action:
            <input type="text" name="action" required>
        </label>
        <br><br>
        <button type="submit">Log Activity</button>
    </form>
</body>
</html>
