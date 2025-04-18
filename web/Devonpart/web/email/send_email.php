<?php
require 'config/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function send_email($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  
        $mail->SMTPAuth = true;
        $mail->Username = 'devoneyc-wm24@student.tarc.edu.my';  
        $mail->Password = 'llfo wzaw tbrv aqsf';  
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('devoneyc-wm24@student.tarc.edu.my', 'Devon');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return "Email Send Successfully!";
    } catch (Exception $e) {
        return "Error: " . $mail->ErrorInfo;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    $password = isset($_POST["password"]) ? $_POST["password"] : null;

    if ($password) {
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
                
                $subject = "Login Alert - New Device Login";
                $body = "Hi $username, <br><br> We noticed a device has logged into your member account. If this was not you, please change your password immediately.";
                send_email($email, $subject, $body); 

                header("Location: ../../LeonPart/Code/index2.php");
                exit();
            } else {
                echo "";
            }
        }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <style>
        .password-container {
            position: relative;
            display: inline-block;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            background: none;
            border: none;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
