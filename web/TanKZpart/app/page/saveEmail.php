<?php
include '../lib/database.php';
session_start();

$user_id = $_SESSION["user_id"];
$email = $_POST["email"] ?? ($_GET["email"] ?? "");

// 如果是 GET 请求或有 check 参数，表示只检查 email 是否被使用
if ($_SERVER["REQUEST_METHOD"] === "GET" || isset($_GET["check"])) {
    $stmt = $conn->prepare("SELECT 1 FROM user_profile WHERE email = ? AND user_id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    echo json_encode(["exists" => $exists]);
    exit;
}

// 否则就是 POST 请求，执行更新
$stmt = $conn->prepare("SELECT 1 FROM user_profile WHERE email = ? AND user_id != ?");
$stmt->bind_param("si", $email, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo "used";
} else {
    $stmt = $conn->prepare("UPDATE user_profile SET email = ? WHERE user_id = ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    echo "success";
}
$stmt->close();
$conn->close();
?>

