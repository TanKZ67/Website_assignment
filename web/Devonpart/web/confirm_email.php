<?php
require 'config/db.php';

if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];

    $stmt = $conn->prepare("SELECT username, password_hash, email, gender, date_of_birth, phone_number, picture FROM pending_users WHERE email = ? AND token = ?");

    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();


        $user_account = $row['username'];
        $user_account_old = $row['username'];
        $password_hash = $row['password_hash'];
        $gender = $row['gender'];
        $date_of_birth = $row['date_of_birth'];
        $phone_number = $row['phone_number'];
        $imagePath = $row['picture'];


        $stmt_insert = $conn->prepare("INSERT INTO user_profile (user_account,user_account_old, email, password_hash, gender, date_of_birth, phone_number, picture) " .
            "VALUES (?,?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("ssssssss", $user_account,$user_account_old, $email, $password_hash, $gender, $date_of_birth, $phone_number, $imagePath);
        $stmt_insert->execute();

        $stmt_delete = $conn->prepare("DELETE FROM pending_users WHERE email = ?");
        $stmt_delete->bind_param("s", $email);
        $stmt_delete->execute();

        echo "<h1>Registration Confirmed!</h1><p>Your email has been successfully confirmed. You can now log in.</p>";
    } else {
        echo "<h1>Invalid or Expired Token!</h1><p>The token you used has either expired or is invalid. Please request a new confirmation email.</p>";
    }
}
