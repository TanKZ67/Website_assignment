<?php
require 'config/db.php';
require 'email/send_email.php';

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $user_account = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $phone_number = $_POST['phone_number'];

    // 密码验证逻辑
    if (!preg_match("/[A-Za-z]/", $password) || !preg_match("/\d/", $password) || strlen($password) < 8) {
        $_SESSION['password_error'] = "Password must contain at least one letter, one number, and be at least 8 characters.";
    } else {
        // ✅ 手机号码格式验证（8~15位数字）
        if (!preg_match("/^\d{8,15}$/", $phone_number)) {
            die("Invalid phone number. It should be 8 to 15 digits.");
        }

        // ✅ 照片验证：确保图片文件已上传
        if (!isset($_FILES["profile"]) || $_FILES["profile"]["error"] === UPLOAD_ERR_NO_FILE) {
            $photo_error = "Profile picture is required.";
        } else {
            // 图片上传逻辑
            $imagePath = null;
            if ($_FILES["profile"]["error"] === UPLOAD_ERR_OK) {
                $targetDir = "uploads/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true); // 自动创建目录
                }
                $filename = basename($_FILES["profile"]["name"]);
                $targetFile = $targetDir . time() . "_" . $filename;

                if (move_uploaded_file($_FILES["profile"]["tmp_name"], $targetFile)) {
                    $imagePath = $targetFile;
                }
            }
        }

        // 加密密码
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // 检查邮箱是否已注册
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
                <a href='$confirm_link' style='padding: 10px; background: green; color: white;'>Confirm</a>
                <p>Or cancel registration:</p>
                <a href='$cancel_link' style='padding: 10px; background: red; color: white;'>Cancel</a>
            ";

            send_email($email, "Confirm Your Registration", $message);

            // 存入 pending_users
            $stmt = $conn->prepare("INSERT INTO pending_users (username, email, password_hash, gender, date_of_birth, phone_number, picture, token) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $user_account, $email, $password_hash, $gender, $date_of_birth, $phone_number, $imagePath, $token);
            $stmt->execute();

            echo "<script>alert('A confirmation email has been sent!'); window.location.href='login.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        .important-icon{
            position:absolute;
            height: 350px;
            width: 480px;
            transform: translate(-430px,390px);
        }
        .container{
            position:absolute;
            height: 250px;
            width: 600px;
            background-color: rgb(255, 255, 255);            
            transform: translate(-430px,100px);
        }
        h1{
            transform: translate(60px,-50px);
            font-size: 115px;
            color:rgb(250, 41, 41);
        }
        h2 {
            text-align: center;
            color: #333;
            font-style: italic;
            font-size: 30px;
        }
        h3 {
            font-size: 50px;
            transform: translate(65px,-130px);
            color:rgb(102, 31, 255);
        }        
        body {
            font-family: Arial;
            display: flex;
            justify-content: center;
            padding-top: 40px;
            background-color:rgb(170, 231, 255);
        }
        .register-container {
            width: 400px;
            border: 1px solid #ccc;
            padding: 25px;
            border-radius: 15px;
            background-color:rgb(101, 196, 220);
            transform: translate(400px,60px);
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="date"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; 
            background-color: white;
        }
        .gender-group {
            display: flex;
            align-items: center;
            margin-top: 8px;
            margin-bottom: 16px;
        }
        .gender-group label {
            margin-right: 10px;
        }
        #eye-icon {
            cursor: pointer;
            position: absolute;
            right: 5px;
            top: 16px;
        }
        .error {
            color: red;
            font-size: 13px;
        }
        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            margin-top: 10px;
            border-radius: 5px;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        .preview-img {
            margin-top: 10px;
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
<div class="container">
<h1>TARUMT</h1>
<h3>ONLINE SHOPPING</h3>

    </div>
    <img src="../../TanKZpart/app/image/importan_icon/giphy.gif" alt="Important Icon"  class ="important-icon" >

<div class="register-container">
    <h2>Register Member</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>

        <div style="position: relative;">
            <input type="password" name="password" id="password" placeholder="Password" required oninput="validatePassword(this.value)">
            <span id="eye-icon" onclick="togglePassword()">👁️</span>
        </div>
        <div id="password-error" class="error" style="display: none;">
            Password must contain at least one letter, one number, and be at least 8 characters.
        </div>

        <label>Upload Profile Picture:</label>
        <input type="file" name="profile" accept="image/*" required onchange="previewImage(event)">
        <img id="preview" class="preview-img" src="#" style="display:none;"/>
        
        <!-- 显示错误信息 -->
        <?php if (isset($photo_error)) : ?>
            <div class="error"><?php echo $photo_error; ?></div>
        <?php endif; ?>

        <label>Phone Number:</label>
        <input type="text" name="phone_number" id="phone_number" placeholder="Enter phone number" required oninput="validatePhoneNumber(this.value)">
        <div id="phone-error" class="error" style="display: none;">
            Phone number must be 8 to 15 digits.
        </div>

        <label>Gender:</label>
        <div class="gender-group">
            <input type="radio" id="male" name="gender" value="M" required>
            <label for="male">Male</label>
            <input type="radio" id="female" name="gender" value="F">
            <label for="female">Female</label>
        </div>

        <label>Date of Birth:</label>
        <input type="date" name="date_of_birth" required>

        <button type="submit">Register</button>
        <p style="text-align:center; margin-top: 10px;"><a href="login.php">Already have an account?</a></p>
    </form>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        passwordInput.type = passwordInput.type === "password" ? "text" : "password";
    }

    function validatePassword(value) {
        const error = document.getElementById("password-error");
        const valid = /[A-Za-z]/.test(value) && /\d/.test(value) && value.length >= 8;
        error.style.display = value && !valid ? "block" : "none";
    }

    function validatePhoneNumber(value) {
        const error = document.getElementById("phone-error");
        const valid = /^\d{8,15}$/.test(value);
        error.style.display = value && !valid ? "block" : "none";
    }

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const preview = document.getElementById("preview");
            preview.src = reader.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
</body>
</html>
