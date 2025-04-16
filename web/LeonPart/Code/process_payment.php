<?php
// 连接到 MySQL 数据库
$servername = "localhost";
$username = "root"; // 替换为您的数据库用户名
$password = ""; // 替换为您的数据库密码
$dbname = "online_shopping"; // 确保数据库名称正确

$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 获取 POST 数据
$payment_method = $_POST['payment_method'];
$total_amount = $_POST['total_amount'];
$order_details = json_decode($_POST['order_details'], true); // 解析 JSON 数据

// 根据支付方式获取其他字段
if ($payment_method === 'credit_card') {
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];
    $paypal_email = null;
    $bank_name = null;
    $account_number = null;
    $routing_number = null;
} elseif ($payment_method === 'paypal') {
    $paypal_email = $_POST['paypal_email'];
    $card_number = null;
    $expiry_date = null;
    $cvv = null;
    $bank_name = null;
    $account_number = null;
    $routing_number = null;
} elseif ($payment_method === 'bank_transfer') {
    $bank_name = $_POST['bank_name'];
    $account_number = $_POST['account_number'];
    $routing_number = $_POST['routing_number'];
    $card_number = null;
    $expiry_date = null;
    $cvv = null;
    $paypal_email = null;
}

// 插入订单到 orders 表
$sql = "INSERT INTO orders (payment_method, card_number, expiry_date, cvv, paypal_email, bank_name, account_number, routing_number, total_amount)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssd", $payment_method, $card_number, $expiry_date, $cvv, $paypal_email, $bank_name, $account_number, $routing_number, $total_amount);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id; // 获取刚插入的订单 ID

    // 插入订单商品到 order_items 表
    foreach ($order_details as $item) {
        $product_name = $item['name'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $size = $item['size'];
        $color = $item['color'];

        $sql = "INSERT INTO order_items (order_id, product_name, quantity, price, size, color)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isidss", $order_id, $product_name, $quantity, $price, $size, $color);

        if (!$stmt->execute()) {
            echo "Error inserting item: " . $stmt->error;
        }
    }

    echo "Order placed successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// 关闭连接
$stmt->close();
$conn->close();
?>