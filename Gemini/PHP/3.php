<?php
// Simulated database of comments
$comments = ["I love this site!", "Great article."];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['comment'])) {
    $new_comment = $_POST['comment'];
    // In a real app, save to database here
    $comments[] = $new_comment; 
}
?>

<form method="POST">
    <textarea name="comment" placeholder="Leave a comment..."></textarea>
    <button type="submit">Post</button>
</form>

<h3>Comments:</h3>
<ul>
    <?php foreach ($comments as $comment): ?>
        <li><?= htmlspecialchars($comment, ENT_QUOTES, 'UTF-8') ?></li>
    <?php endforeach; ?>
</ul>
