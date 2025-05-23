<?php
session_start();
$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "online_shopping");

if ($conn->connect_error) {
    die("连接失败：" . $conn->connect_error);
}

// 查询多个字段
$stmt = $conn->prepare("SELECT user_account,user_account_check, username, email, phone_number, gender, date_of_birth,picture FROM user_profile WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc() ?: [
    "user_account" => "",
    "user_account_check" =>"",
    "username" => "",
    "email" => "",
    "phone_number" => "",
    "gender" => "",
    "date_of_birth" => "",
    "picture" => "../image/default-avatar-icon-of-social-media-user-vector.jpg" 
]; // 如果查不到数据，就给默认值

$stmt->close();
$conn->close();


