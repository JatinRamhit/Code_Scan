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
