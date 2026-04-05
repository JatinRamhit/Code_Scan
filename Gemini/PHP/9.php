<?php
// Safe alternative: Allowlist routing
$page = $_GET['page'] ?? 'home';

// Define exactly which files are allowed to be included
$allowed_pages = [
    'home' => 'pages/home.php',
    'about' => 'pages/about.php',
    'contact' => 'pages/contact.php'
];

if (array_key_exists($page, $allowed_pages)) {
    // It is safe to include because the path is strictly hardcoded in our array
    include($allowed_pages[$page]);
} else {
    // 404 Not Found
    echo "Page not found.";
    // include('pages/404.php');
}
?>
