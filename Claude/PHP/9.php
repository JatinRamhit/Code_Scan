<!-- page.php -->
<?php
// Explicit whitelist — NEVER use the raw parameter directly in include()
$allowed_pages = [
    'home'    => 'pages/home.php',
    'about'   => 'pages/about.php',
    'contact' => 'pages/contact.php',
];

$page = $_GET['page'] ?? 'home';

if (array_key_exists($page, $allowed_pages)) {
    $file = $allowed_pages[$page];

    // Extra safety: confirm the resolved path is inside the project root
    $real = realpath($file);
    $root = realpath(__DIR__);

    if ($real && str_starts_with($real, $root)) {
        include $real;
    } else {
        echo "Access denied.";
    }
} else {
    http_response_code(404);
    echo "Page not found.";
}
?>
<!-- Usage: page.php?page=about -->
<!-- pages/home.php, pages/about.php, pages/contact.php must exist -->
