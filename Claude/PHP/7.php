<!-- contact.php -->
<?php
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validate email address
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status = "Invalid email address.";
    } elseif ($name === '' || $message === '') {
        $status = "All fields are required.";
    } else {
        // Strip newlines from name/email to block header injection
        $safe_name  = str_replace(["\r", "\n"], '', $name);
        $safe_email = str_replace(["\r", "\n"], '', $email);

        $to      = 'you@yourdomain.com';
        $subject = 'Contact form message from ' . $safe_name;
        $body    = "Name: "    . $safe_name  . "\n"
                 . "Email: "   . $safe_email . "\n\n"
                 . "Message:\n" . $message;
        $headers = "From: noreply@yourdomain.com\r\n"
                 . "Reply-To: " . $safe_email . "\r\n"
                 . "X-Mailer: PHP/" . phpversion();

        if (mail($to, $subject, $body, $headers)) {
            $status = "Message sent successfully.";
        } else {
            $status = "Failed to send. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<?php if ($status): ?>
  <p><?= htmlspecialchars($status) ?></p>
<?php endif; ?>
<form method="post">
  <input name="name"    placeholder="Your name"    required><br>
  <input name="email"   type="email" placeholder="you@example.com" required><br>
  <textarea name="message" placeholder="Your message" required></textarea><br>
  <button type="submit">Send</button>
</form>
