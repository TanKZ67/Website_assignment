<?php
require 'config/db.php';
require 'email/send_email.php'; 

$message_sent = false; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT user_id FROM user_profile WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $created_at = date('Y-m-d H:i:s');
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $conn->prepare("UPDATE user_profile SET reset_token = ?, created_at = ?, expires_at = ?, status = 'pending' WHERE email = ?");
        $stmt->bind_param("ssss", $token, $created_at, $expires_at, $email);
        $stmt->execute();

        $reset_link = "http://localhost/a/Website_assignment/web/Devonpart/reset_password.php?token=$token";
        $message = "Click this link to reset your password: <a href='$reset_link'>$reset_link</a>";
        send_email($email, "Password Reset Request", $message);

        $message_sent = true; 
    } else {
        $message_sent = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid #ccc;
            padding: 20px 30px;
            border-radius: 10px;
        }

        input[type="email"] {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 250px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        h2 {
            margin-bottom: 20px;
        }

        .message-box {
            background-color: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .message-box h2 {
            margin-bottom: 15px;
        }

        .message-box a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 6px;
        }

        .message-box a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="form-container">
    <?php if ($message_sent): ?>
        <div class="message-box">
            <h2>📧 Email Sent!</h2>
            <p>A password reset link has been sent to your email.</p>
            <a href="login.php">Back to Login</a>
        </div>
    <?php else: ?>
        <h2>Request Password Reset</h2>
        <form method="POST">
            <input type="email" name="email" required placeholder="Enter your email">
            <button type="submit">Submit</button>
        </form>
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !$message_sent): ?>
            <p style="color: red;">This email does not exist in our records.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
