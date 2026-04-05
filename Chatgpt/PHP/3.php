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
