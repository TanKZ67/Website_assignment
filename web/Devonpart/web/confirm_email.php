<?php
require 'config/db.php';

if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];

    $stmt = $conn->prepare("SELECT user_account, password_hash FROM pending_users WHERE email = ? AND token = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $stmt_insert = $conn->prepare("INSERT INTO user_profile (user_account, email, password_hash) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("sss", $row['user_account'], $email, $row['password_hash']);
        $stmt_insert->execute();

        $stmt_delete = $conn->prepare("DELETE FROM pending_users WHERE email = ?");
        $stmt_delete->bind_param("s", $email);
        $stmt_delete->execute();

        echo "<h1>Registration Confirmed!</h1><p>Your email has been successfully confirmed. You can now log in.</p>";
    } else {
        echo "<h1>Invalid or Expired Token!</h1><p>The token you used has either expired or is invalid. Please request a new confirmation email.</p>";
    }
}
?>
