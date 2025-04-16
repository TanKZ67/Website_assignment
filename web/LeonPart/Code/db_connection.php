<?php
$host = 'localhost';  // 数据库主机
$dbname = 'online_shopping';  // 数据库名
$username = 'root';  // 数据库用户名
$password = '';  // 数据库密码

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}
?>