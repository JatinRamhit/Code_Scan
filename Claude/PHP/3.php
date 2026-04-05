<!-- comments.php -->
<?php
$comments = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = htmlspecialchars(trim($_POST['name']  ?? ''), ENT_QUOTES, 'UTF-8');
    $comment = htmlspecialchars(trim($_POST['comment'] ?? ''), ENT_QUOTES, 'UTF-8');

    if ($name && $comment) {
        $comments[] = ['name' => $name, 'text' => $comment];
    }
}
?>
<!DOCTYPE html>
<h2>Comments</h2>
<form method="post">
  <input name="name" placeholder="Your name" required><br>
  <textarea name="comment" placeholder="Write a comment..." required></textarea><br>
  <button type="submit">Submit</button>
</form>
<?php foreach ($comments as $c): ?>
  <div>
    <strong><?= $c['name'] ?></strong>
    <p><?= $c['text'] ?></p>
  </div>
<?php endforeach; ?>
