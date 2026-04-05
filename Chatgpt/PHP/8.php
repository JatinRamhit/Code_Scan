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
