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
