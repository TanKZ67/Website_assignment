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

    // ✅ 管理员登录逻辑（用 email 登录）
    if ($action === '' || $action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo "Email or password missing";
            exit;
        }

        $stmt = $conn->prepare("SELECT admin_email, admin_password FROM admin WHERE admin_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_password_from_db = $row['admin_password'];
            $admin_email = $row['admin_email'];

            if (password_verify($password, $hashed_password_from_db)) {
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_email'] = $email;
                $_SESSION['admin_email'] = $admin_email;
                echo "success";
            } else {
                echo "Incorrect password";
            }
        } else {
            echo "Incorrect email";
        }
        $stmt->close();
    }

    // ✅ 发送 OTP
    elseif ($action === 'send_otp') {
        $email = $_POST['to_email'] ?? '';
        $admin_email = $_SESSION['admin_email'] ?? '';

        if (empty($email) || empty($admin_email)) {
            echo json_encode(["success" => false, "message" => "Email missing"]);
            exit;
        }

        $otp = rand(1000, 9999);
        $otp_key = $email;
        $stored_otp[$otp_key] = $otp;

        echo json_encode(["success" => true, "otp" => $otp]);
        exit;
    }

    // ✅ 验证 OTP 并更新密码
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

    // ✅ 重置密码（用 email）
    elseif ($action === 'reset_password') {
        $email = $_POST['email'] ?? '';
        $new_password = $_POST['new_password'] ?? '';

        if (empty($email) || empty($new_password)) {
            echo "All fields are required";
            exit;
        }

        $stmt = $conn->prepare("SELECT admin_email FROM admin WHERE admin_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admin SET admin_password = ? WHERE admin_email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);

            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "Password update fail";
            }
        } else {
            echo "email does not exist";
        }
        $stmt->close();
    }

    // ✅ 检查管理员登录状态
    elseif ($action === 'check_session') {
        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
            echo "logged_in";
        } else {
            echo "not_logged_in";
        }
    }
}

$conn->close();
?>
