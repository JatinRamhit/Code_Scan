<!-- cookie_query.php -->
<?php
$host = 'localhost'; $db = 'myapp';
$user = 'dbuser';   $pass = 'dbpass';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("DB connection failed.");
}

// Read the cookie — treat it as untrusted user input
$user_id = $_COOKIE['user_id'] ?? null;

// Validate: must be a positive integer
if (!filter_var($user_id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
    die("Invalid or missing session cookie.");
}

// Use a prepared statement — never interpolate cookie data into SQL
$stmt = $pdo->prepare(
    "SELECT id, username, email FROM users WHERE id = :id LIMIT 1"
);
$stmt->execute([':id' => (int) $user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo "Welcome, " . htmlspecialchars($row['username']);
    echo " <br>Email: " . htmlspecialchars($row['email']);
} else {
    echo "User not found.";
}
?>
