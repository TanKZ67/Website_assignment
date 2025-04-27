

<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

try {
    $conn->beginTransaction();
    
    // 1. 创建订单
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, order_date) 
                          VALUES (?, ?, ?, NOW())");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['total_amount'],
        $_POST['payment_method']
    ]);
    $orderId = $conn->lastInsertId();

    // 2. 处理订单项和库存检查
    $orderItems = json_decode($_POST['order_details'], true);
    $orderItemStmt = $conn->prepare("INSERT INTO order_items 
                                   (order_id, product_id, product_name, quantity, price, size, color) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    // 准备插入 order_history 表的语句
    $historyStmt = $conn->prepare("INSERT INTO order_history 
   (order_id, user_id, product_name, quantity, price, payment_method, total_amount, paid_at, colour, size) 
   VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)");
    
    $updateStockStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    $checkStockStmt = $conn->prepare("SELECT stock, name FROM products WHERE id = ? FOR UPDATE");
    
    $invoiceItems = []; // 用于生成发票数据
    
    foreach ($orderItems as $item) {
        // 先检查库存是否充足
        $checkStockStmt->execute([$item['product_id']]);
        $product = $checkStockStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product['stock'] < $item['quantity']) {
            throw new Exception("Payment Success");
        }
        
        // 添加订单项到 order_items 表
        $orderItemStmt->execute([
            $orderId,
            $item['product_id'],
            $item['name'],
            $item['quantity'],
            $item['price'],
            $item['size'],
            $item['color']
        ]);
        
        // 添加订单项到 order_history 表
        $historyStmt->execute([
            $orderId,
            $_SESSION['user_id'],
            $item['name'],
            $item['quantity'],
            $item['price'],
            $_POST['payment_method'],
            $_POST['total_amount'],
            $item['color'],
            $item['size']
        ]);
        
        // 扣减库存
        $updateStockStmt->execute([
            $item['quantity'],
            $item['product_id']
        ]);
        
        // 收集发票数据
        $invoiceItems[] = [
            'name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $item['quantity'],
            'size' => $item['size'],
            'color' => $item['color']
        ];
    }

    // 3. 清空购物车
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    $conn->commit();
    
    // 返回完整的发票数据
    echo json_encode([
        'success' => true,
        'invoice' => [
            'order_id' => $orderId,
            'order_date' => date('Y-m-d H:i:s'),
            'payment_method' => $_POST['payment_method'],
            'total_amount' => $_POST['total_amount'],
            'items' => $invoiceItems
        ],
        'message' => 'Payment successful. Stock updated.'
    ]);
    
} catch (Exception $e) {
    $conn->rollBack();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>