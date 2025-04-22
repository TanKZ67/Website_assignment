<?php
session_start();
require_once 'db_connection.php';

// 检查管理员是否已登录
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "Please log in as admin to view all order histories.";
    exit();
}

try {
    // 管理员：获取所有订单历史
    $stmt = $conn->prepare("
        SELECT user_id, order_id, product_name, quantity, price, payment_method, total_amount, paid_at 
        FROM order_history 
        ORDER BY paid_at DESC
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Failed to retrieve order history: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Order History</title>
    <style>
        table { border-collapse: collapse; width: 90%; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .no-orders { text-align: center; font-size: 18px; margin-top: 40px; color: #666; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">All Order History</h2>

    <?php if (count($orders) > 0): ?>
        <table>
            <tr>
                <th>User ID</th>
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
                <td><?= htmlspecialchars($order['user_id']) ?></td>
                <td><?= htmlspecialchars($order['order_id']) ?></td>
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
        <p class="no-orders">No orders found.</p>
    <?php endif; ?>
</body>
</html>
