<?php
session_start();
require_once 'db_connection.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    echo "Please login to view order history.";
    exit();
}

$userId = $_SESSION['user_id'];

// 查询当前用户的订单历史
try {
    $stmt = $conn->prepare("
        SELECT order_id, product_name, quantity, price, payment_method, total_amount, paid_at 
        FROM order_history 
        WHERE user_id = ? 
        ORDER BY paid_at DESC
    ");
    $stmt->execute([$userId]);
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
    <title>Your Order History</title>
    <style>
        table { border-collapse: collapse; width: 90%; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .no-orders { text-align: center; font-size: 18px; margin-top: 40px; color: #666; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Your Order History</h2>

    <?php if (count($orders) > 0): ?>
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
        <p class="no-orders">You have no orders yet.</p>
    <?php endif; ?>
</body>
</html>
