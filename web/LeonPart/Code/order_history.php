<?php
session_start();
require_once 'db_connection.php';

// 确保用户已登录（可选）
if (!isset($_SESSION['user_id'])) {
    echo "Please login to view order history.";
    exit();
}

$userId = $_SESSION['user_id'];

// 查询当前用户的订单历史
$stmt = $conn->prepare("SELECT * FROM order_history WHERE user_id = ? ORDER BY paid_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order History</title>
    <style>
        table { border-collapse: collapse; width: 90%; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Your Order History</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price (RM)</th>
            <th>Payment Method</th>
            <th>Total</th>
            <th>Paid At</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['order_id'] ?></td>
            <td><?= htmlspecialchars($order['product_name']) ?></td>
            <td><?= $order['quantity'] ?></td>
            <td><?= number_format($order['price'], 2) ?></td>
            <td><?= $order['payment_method'] ?></td>
            <td><?= number_format($order['total_amount'], 2) ?></td>
            <td><?= $order['paid_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
