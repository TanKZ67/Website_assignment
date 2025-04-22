<?php
session_start();
require_once 'db_connection.php';

// 检查用户是否登录并是管理员（这里假设 user_id = 1 是 admin）
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    echo "Access denied. Admins only.";
    exit();
}

// 查询所有用户的订单历史，加入 JOIN 显示用户名（如果有 user 表）
$stmt = $conn->prepare("
    SELECT oh.*, u.username 
    FROM order_history oh 
    JOIN users u ON oh.user_id = u.id 
    ORDER BY oh.paid_at DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All User Order History</title>
    <style>
        table { border-collapse: collapse; width: 95%; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">All User Order History (Admin)</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Username</th>
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
            <td><?= htmlspecialchars($order['username']) ?></td>
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
