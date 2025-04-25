

<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json'); // 保留这行

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

    // ✅ 如果你想调试查看 $orderItems 的内容，可以在异常中返回
    if (!is_array($orderItems)) {
        throw new Exception("order_details 无法解码为数组。原始值: " . $_POST['order_details']);
    }

    $orderItemStmt = $conn->prepare("INSERT INTO order_items 
                                   (order_id, product_id, product_name, quantity, price, size, color) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $historyStmt = $conn->prepare("INSERT INTO order_history 
   (order_id, user_id, product_name, quantity, price, payment_method, total_amount, paid_at, colour, size) 
   VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)");
    
    $updateStockStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    $checkStockStmt = $conn->prepare("SELECT stock, name FROM products WHERE id = ? FOR UPDATE");

    $invoiceItems = [];

    foreach ($orderItems as $item) {
        $checkStockStmt->execute([$item['product_id']]);
        $product = $checkStockStmt->fetch(PDO::FETCH_ASSOC);

        if ($product['stock'] < $item['quantity']) {
            throw new Exception("库存不足：" . $product['name']);
        }

        $orderItemStmt->execute([
            $orderId,
            $item['product_id'],
            $item['name'],
            $item['quantity'],
            $item['price'],
            $item['size'],
            $item['color']
        ]);

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

        $updateStockStmt->execute([
            $item['quantity'],
            $item['product_id']
        ]);

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
        'message' => $e->getMessage(),
        'debug_orderItems' => isset($orderItems) ? $orderItems : null
    ]);
}
?>
