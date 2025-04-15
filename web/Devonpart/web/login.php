<?php
session_start();
require 'config/db.php';
require 'email/send_email.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT user_id, username, password_hash FROM user_profile WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;

            $device_info = $_SERVER['HTTP_USER_AGENT'];
            $ip_address = $_SERVER['REMOTE_ADDR'];

            $stmt = $conn->prepare("SELECT id FROM user_devices WHERE user_id = ? AND ip_address = ? AND device_info = ?");
            $stmt->bind_param("iss", $id, $ip_address, $device_info);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 0) {
                send_email($email, $device_info, $ip_address);
                $stmt = $conn->prepare("INSERT INTO user_devices (user_id, device_info, ip_address, last_login) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("iss", $id, $device_info, $ip_address);
                $stmt->execute();
            }

            header("Location: ../../LeonPart/Code/index.php ");
            exit();
        } else {
            echo "<script>alert('Incorrect password');</script>";
        }
    } else {
        echo "<script>alert('No account found');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid #ccc;
            padding: 20px;
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
        }

        input[type="text"],
        input[type="password"] {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 250px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST">
            <input type="email" name="email" required placeholder="Email"><br>

            <div class="input-container">
                <input type="password" name="password" id="password" required placeholder="Password">
                <span id="eye-icon" class="eye-icon" onclick="togglePassword()">👁️</span>
            </div>

            <button type="submit">Login</button>
        </form>
        <p><a href="forgot_password.php">Forgot Password?</a></p>
    </div>

    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var eyeIcon = document.getElementById("eye-icon");

            // Toggle the type of the password input between "password" and "text"
            if (passwordInput.type === "password") {
                passwordInput.type = "text"; // Show password
            } else {
                passwordInput.type = "password"; // Hide password
            }
        }
    </script>
</body>

</html>