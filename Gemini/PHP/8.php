<?php
// Assume $pdo is established as shown in example 5
if (isset($_COOKIE['user_session_id'])) {
    $session_id = $_COOKIE['user_session_id'];

    // Use prepared statements to prevent SQL injection via manipulated cookies
    $stmt = $pdo->prepare('SELECT username FROM user_sessions WHERE session_id = :session_id');
    $stmt->execute(['session_id' => $session_id]);
    $user = $stmt->fetch();

    if ($user) {
        echo "Welcome back, " . htmlspecialchars($user['username']) . "!";
    } else {
        echo "Session invalid. Please log in.";
    }
} else {
    echo "No session cookie found.";
}
?>
