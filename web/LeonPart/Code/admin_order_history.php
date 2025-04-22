<?php
session_start();
require_once 'db_connection.php';

// 检查是否为管理员身份
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "Access denied. Admins only.";
    exit();
}

// 查询所有用户的订单历史，使用 JOIN 获取用户名
try {
    $stmt = $conn->prepare("
        SELECT oh.order_id, u.username, oh.product_name, oh.quantity, oh.price, 
               oh.payment_method, oh.total_amount, oh.paid_at
        FROM order_history oh
        JOIN users u ON oh.user_id = u.id
        ORDER BY oh.paid_at DESC
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "查询失败: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>All User Order History</title>
    <style>
        table { border-collapse: collapse; width: 95%; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">All User Order History (Admin)</h2>

    <?php if (count($orders) > 0): ?>
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
                    <td><?= htmlspecialchars($order['order_id']) ?></td>
                    <td><?= htmlspecialchars($order['username']) ?></td>
                    <td><?= htmlspecialchars($order['product_name']) ?></td>
                    <td><?= htmlspecialchars($order['quantity']) ?></td>
                    <td><?= number_format($order['price'], 2) ?></td>
                    <td><?= htmlspecialchars($order['payment_method']) ?></td>
                    <td><?= number_format($order['total_amount'], 2) ?></td>
                    <td><?= htmlspecialchars($order['paid_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="text-align:center;">No orders found.</p>
    <?php endif; ?>
</body>
</html>
