<?php
$conn = new mysqli("localhost", "root", "", "online_shopping");

    $address_id = $_GET['edit'];

    $stmt = $conn->prepare("DELETE FROM user_address WHERE address_id = ?");
    $stmt->bind_param("i", $address_id);

    if ($stmt->execute()) {
        echo "删除成功！";
        // 删除完可以重定向回列表页
        header("Location: ../../program/address.php");
        exit();

    } else {
        echo "删除失败: " . $stmt->error;
    }

    $stmt->close();


$mysqli->close();
?>
