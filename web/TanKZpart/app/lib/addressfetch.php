<?php
session_start();
$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "online_shopping");

if ($conn->connect_error) {
    die("连接失败：" . $conn->connect_error);
}

// 查询多个字段
$stmt = $conn->prepare("SELECT address_id,address_name,floor_unit,state,district,postcode FROM user_address WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


$addressrow=[];
while($rows=$result->fetch_assoc()){
    $addressrow[]=$rows;
}

$stmt->close();
$conn->close();


