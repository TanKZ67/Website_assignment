<?php
require 'Devonpart/web/config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM user_profile WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $created_at = date('Y-m-d H:i:s');
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $conn->prepare("INSERT INTO user_profile (email, reset_token, created_at, expires_at, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("ssss", $email, $token, $created_at, $expires_at);
        $stmt->execute();

        $reset_link = "http://localhost/user_profile.php?token=$token";
        $message = "Click this link to reset your password: <a href='$reset_link'>$reset_link</a>";
        send_email($email, "Password Reset Request", $message);

        $success_message = "A password reset link has been sent to your email!";
    } else {
        $error_message = "This email does not exist in our records.";
    }
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT email, created_at, expires_at FROM user_profile WHERE reset_token = ? AND status = 'pending'");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email, $created_at, $expires_at);
        $stmt->fetch();

        $current_time = strtotime($created_at);
        $expiry_time = strtotime($expires_at);

        if ($current_time > $expiry_time) {
            $error_message = "The token has expired. Please request a new one.";
        } else {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $new_password = $_POST["password"];

                if (empty($new_password)) {
                    $error_message = "Password cannot be empty!";
                } elseif (!preg_match("/[A-Za-z]/", $new_password) || !preg_match("/\d/", $new_password) || strlen($new_password) < 8) {
                    $error_message = "Password must contain at least one letter, one number, and be at least 8 characters long!";
                } else {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    $stmt = $conn->prepare("UPDATE user_profile SET password_hash = ? WHERE email = ?");
                    $stmt->bind_param("ss", $hashed_password, $email);
                    if ($stmt->execute()) {
                        $stmt = $conn->prepare("UPDATE user_profile SET reset_token = NULL, status = 'active' WHERE reset_token = ?");
                        $stmt->bind_param("s", $token);
                        if ($stmt->execute()) {
                            $success_message = "Password has been reset successfully!";
                        } else {
                            $error_message = "Error updating token: " . $stmt->error;
                        }
                    } else {
                        $error_message = "Error updating password: " . $stmt->error;
                    }
                }
            }
        }
    } else {
        $error_message = "Invalid or expired token!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Password Reset</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        input[type="email"],
        input[type="text"],
        input[type="password"] {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 250px;
            box-sizing: border-box;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color:rgb(5, 11, 17);
            outline: none;
        }

        button[type="submit"] {
            width: 100%;
            padding: 14px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error-message,
        .success-message {
            margin-top: 5px;
            font-size: 14px;
            text-align: left;
            color: #e74c3c;
        }

        .success-message {
            color: #2ecc71;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .eye-icon {
            position: absolute;
            top: 30%;
            right: 120px;
            cursor: pointer;
            color: #aaa;
        }

        @media (max-width: 600px) {
            .form-container {
                padding: 25px;
            }

            h2 {
                font-size: 20px;
            }

            input[type="email"],
            input[type="password"] {
                padding: 12px;
            }

            button[type="submit"] {
                padding: 12px;
            }
        }
    </style>
</head>

<body>

    <div class="form-container">
        <?php if (!isset($_GET['token'])): ?>
            <h2>Request Password Reset</h2>
            <form method="POST">
                <input type="email" name="email" required placeholder="Enter your email">

                <?php if (isset($error_message)): ?>
                    <p class="error-message"><?php echo $error_message; ?></p>
                <?php endif; ?>

                <button type="submit">Submit</button>
            </form>

            <?php if (isset($success_message)): ?>
                <p class="success-message"><?php echo $success_message; ?></p>
            <?php endif; ?>

        <?php else: ?>
            <h2>Reset Your Password</h2>
            <form method="POST">
                <div style="position: relative;">
                    <input type="password" name="password" required id="password" placeholder="New Password">
                    <i class="eye-icon" id="toggle-eye" onclick="togglePassword()">👁️</i>
                </div>

                <?php if (isset($error_message)): ?>
                    <p class="error-message"><?php echo $error_message; ?></p>
                <?php endif; ?>

                <button type="submit">Update Password</button>

                <?php if (isset($success_message)): ?>
                    <p class="success-message"><?php echo $success_message; ?></p>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('toggle-eye');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.style.color = '#007bff';  
            } else {
                passwordField.type = 'password';
                eyeIcon.style.color = '#aaa';  
            }
        }
    </script>

</body>

</html>
