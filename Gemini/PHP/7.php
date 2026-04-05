<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $to = "admin@yoursite.com";
    // Sanitize to prevent header injection
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $message = htmlspecialchars($_POST['message']);
    
    // Validate email
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if ($email && $subject && $message) {
        $headers = "From: " . $email . "\r\n" .
                   "Reply-To: " . $email . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        if (mail($to, $subject, $message, $headers)) {
            echo "Email sent successfully!";
        } else {
            echo "Failed to send email.";
        }
    } else {
        echo "Invalid input.";
    }
}
?>
<form method="POST">
    <input type="email" name="email" placeholder="Your Email" required><br>
    <input type="text" name="subject" placeholder="Subject" required><br>
    <textarea name="message" placeholder="Message" required></textarea><br>
    <button type="submit">Send</button>
</form>
