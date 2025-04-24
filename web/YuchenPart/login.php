<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_shopping";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

session_start();
$stored_otp = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // ✅ 管理员/用户 登录逻辑
    if ($action === '' || $action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
    
        if (empty($email) || empty($password)) {
            echo "Email or password missing";
            exit;
        }
    
        // 先检查 admin 表
        $stmt = $conn->prepare("SELECT admin_email, admin_password FROM admin WHERE admin_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // 是管理员
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['admin_password'])) {
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_email'] = $row['admin_email'];
                echo "admin_success"; // ✅ 前端根据这个跳转到 Aftersignin.php
            } else {
                echo "Incorrect password";
            }
            $stmt->close();
            exit;
        }
    
        $stmt->close();
    
        // 再检查 user_profile 表
        $stmt = $conn->prepare("SELECT user_id, email, password_hash FROM user_profile WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // 是用户
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['is_user'] = true;
                $_SESSION['email'] = $row['email'];
                $_SESSION['user_id'] = $row['user_id']; // ✅ 这里存 user_id 方便之后使用
                echo "user_success"; // ✅ 前端根据这个跳转到 index2.php
            } else {
                echo "Incorrect password";
            }
        } else {
            echo "Email not found";
        }
    
        $stmt->close();
        exit;
    }
    
    // ✅ 验证 OTP 并更新密码（管理员用）
    elseif ($action === 'verify_otp_and_update') {
        $email = $_POST['to_email'] ?? '';
        $otp = $_POST['otp'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $admin_email = $_SESSION['admin_email'] ?? '';

        if (empty($email) || empty($otp) || empty($new_password) || empty($confirm_password) || empty($admin_email)) {
            echo "All fields are required";
            exit;
        }

        if ($new_password !== $confirm_password) {
            echo "Passwords do not match";
            exit;
        }

        $otp_key = $email;
        if (isset($stored_otp[$otp_key]) && $stored_otp[$otp_key] == $otp) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE admin SET admin_password = ? WHERE admin_email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);

            if ($stmt->execute()) {
                unset($stored_otp[$otp_key]);
                echo "Password updated successfully";
            } else {
                echo "Failed to update password";
            }
            $stmt->close();
        } else {
            echo "Invalid OTP";
        }
    }

    // ✅ 重置密码（通用，admin/user都可以）
    elseif ($action === 'reset_password') {
        $email = $_POST['email'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
    
        if (empty($email) || empty($new_password)) {
            echo "All fields are required";
            exit;
        }
    
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
        // 检查 admin 表
        $stmt = $conn->prepare("SELECT admin_email FROM admin WHERE admin_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE admin SET admin_password = ? WHERE admin_email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);
            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "Password update failed";
            }
            $stmt->close();
            exit;
        }
    
        $stmt->close();
    
        // 再检查 user_profile 表
        $stmt = $conn->prepare("SELECT email FROM user_profile WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE user_profile SET password_hash = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);
            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "Password update failed";
            }
        } else {
            echo "email does not exist";
        }
        $stmt->close();
    }
}
?>
