<?php
session_start();
require __DIR__ . '/config/db.php';

if (!isset($_GET['id'])) {
    // 如果没有传递 id 参数，重定向回 members 页面
    header("Location: members.php");
    exit();
}

$user_account = $_GET['id'];

// 获取当前用户的头像
$sql = "SELECT picture FROM user_profile WHERE user_account = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_account);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found!";
    exit();
}

$user = $result->fetch_assoc();
$current_picture = $user['picture'];  // 当前头像路径

// 处理头像上传
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar'])) {
    $upload_dir = 'uploads/';
    $upload_file = $upload_dir . basename($_FILES['avatar']['name']);
    $imageFileType = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION));
    
    // 检查文件是否为图片
    if (getimagesize($_FILES['avatar']['tmp_name']) === false) {
        echo "File is not an image.";
        exit();
    }

    // 移动上传的文件到指定目录
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_file)) {
        // 更新数据库中的头像路径
        $sql = "UPDATE user_profile SET picture = ? WHERE user_account = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $upload_file, $user_account);
        $stmt->execute();

        // 更新成功后，重定向到成员列表页面
        header("Location: members.php");
        exit();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Avatar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
            margin: 0 auto;
        }

        label {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
            display: block;
        }

        input[type="file"] {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
            margin-bottom: 20px;
        }

        .avatar-preview {
            margin-bottom: 20px;
            text-align: center;
        }

        .avatar-preview img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .avatar-preview p {
            color: #777;
            font-size: 14px;
        }

        button {
            background-color: #3baa95;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 4px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #338f7c;
        }

        button:active {
            background-color: #2e7a64;
        }

        /* Error message styling */
        .error-message {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Edit Picture for <?php echo htmlspecialchars($user_account); ?></h2>
    
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label for="avatar">Upload New Avatar:</label>
            <input type="file" name="avatar" id="avatar" required>
        </div>

        <div class="avatar-preview">
            <p>Current Avatar:</p>
            <?php if ($current_picture): ?>
                <img src="<?php echo $current_picture; ?>" alt="Current Avatar" width="300" height="300">
            <?php else: ?>
                <p>No avatar uploaded yet.</p>
            <?php endif; ?>
        </div>

        <button type="submit">Upload</button>
    </form>

    <?php if (isset($upload_error)): ?>
        <div class="error-message"><?php echo $upload_error; ?></div>
    <?php endif; ?>
</body>
</html>
