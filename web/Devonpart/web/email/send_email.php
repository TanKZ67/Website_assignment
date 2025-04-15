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
