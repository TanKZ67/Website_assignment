<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

try {
    $conn->beginTransaction(); // 事务开始
    
    // ==================== 在这里添加库存检查代码 ====================
    $orderItems = json_decode($_POST['order_details'], true);
    
    // 检查所有商品库存是否充足
    foreach ($orderItems as $item) {
        $checkStmt = $conn->prepare("SELECT name, stock FROM products WHERE id = ?");
        $checkStmt->execute([$item['product_id']]);
        $product = $checkStmt->fetch();
        
        if (!$product) {
            $conn->rollBack();
            echo json_encode([
                'success' => false, 
                'message' => "The product does not exist or has been removed from sale"
            ]);
            exit();
        }
        
        if ($product['stock'] < $item['quantity']) {
            $conn->rollBack();
            echo json_encode([
                'success' => false, 
                'message' => "Product {$product['name']} has insufficient stock (remaining: {$product['stock']})"
            ]);
            exit();
        }
    }
    // ==================== 库存检查代码结束 ====================
    
     // 1. 创建订单
     $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method) 
     VALUES (?, ?, ?)");
$stmt->execute([
$_SESSION['user_id'],
$_POST['total_amount'],
$_POST['payment_method']
]);
$orderId = $conn->lastInsertId();

// 2. 添加订单项并更新库存
$orderItems = json_decode($_POST['order_details'], true);
$orderItemStmt = $conn->prepare("INSERT INTO order_items 
    (order_id, product_id, product_name, quantity, price, size, color) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");

$updateStockStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

foreach ($orderItems as $item) {
// 添加订单项
$orderItemStmt->execute([
$orderId,
$item['product_id'],
$item['name'],
$item['quantity'],
$item['price'],
$item['size'],
$item['color']
]);

// 更新库存
$updateStockStmt->execute([
$item['quantity'],
$item['product_id']
]);
}

// 3. 清空购物车
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);

$historyStmt = $conn->prepare("INSERT INTO order_history 
(user_id, order_id, product_name, quantity, price, payment_method, total_amount) 
VALUES (?, ?, ?, ?, ?, ?, ?)");

foreach ($orderItems as $item) {
$historyStmt->execute([
    $_SESSION['user_id'],
    $orderId,
    $item['name'],
    $item['quantity'],
    $item['price'],
    $_POST['payment_method'],
    $_POST['total_amount']
]);
}
    
    $conn->commit();
    echo json_encode(['success' => true, 'order_id' => $orderId]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>