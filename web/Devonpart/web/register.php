<?php
require 'config/db.php';
require 'email/send_email.php';

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (!preg_match("/[A-Za-z]/", $password) || !preg_match("/\d/", $password)) {
        echo "<script>alert('Password must contain at least one letter and one number!');</script>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT user_id FROM user_profile WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<script>alert('This email is already registered!');</script>";
        } else {
            $token = bin2hex(random_bytes(32));
            $confirm_link = "http://localhost/a/Website_assignment/web/Devonpart/web/confirm_email.php?token=$token&email=$email";
            $cancel_link = "http://localhost/a/Website_assignment/web/Devonpart/web/cancel_email.php?email=$email";

            $message = "
                <h2>Email Confirmation</h2>
                <p>Click below to confirm your registration:</p>
                <a href='$confirm_link' style='padding: 10px; background: green; color: white; text-decoration: none;'>Confirm</a>
                <p>Or click here to cancel:</p>
                <a href='$cancel_link' style='padding: 10px; background: red; color: white; text-decoration: none;'>Cancel</a>
            ";

            send_email($email, "Confirm Your Registration", $message);

            $stmt = $conn->prepare("INSERT INTO pending_users (username, email, password_hash, token) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $token);
            $stmt->execute();

            echo "<script>alert('A confirmation email has been sent!'); window.location.href='login.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 250px;
            box-sizing: border-box;
        }

        .input-container {
            display: flex;
            align-items: center;
            position: relative;
            width: 250px;
        }

        .eye-icon {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            background: none;
            border: none;
        }

        input[type="password"] {
            width: 100%;
            padding-right: 30px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .login {
                    font-size: 13px;
                    margin-left: 116px;
                    position: absolute;
                    margin-top: -39px;
                    }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Register</h2>
        <form method="POST">
            <input type="text" name="username" required placeholder="Username"><br>
            <input type="email" name="email" required placeholder="Email"><br>

            <div class="input-container">
                <input type="password" id="password" name="password" required placeholder="Password">
                <span id="eye-icon" class="eye-icon" onclick="togglePassword()">👁️</span>
            </div>

            <button type="submit">Register</button>
            <p><a href="login.php" class="login">Already have account?</a></p>
        </form>
    </div>

    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var eyeIcon = document.getElementById("eye-icon");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }
    </script>
</body>

</html>