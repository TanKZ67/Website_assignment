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
    
    // ==================== Stock Check ====================
    $orderItems = json_decode($_POST['order_details'], true);
    $productsInfo = []; // We'll use this later for the invoice
    
    foreach ($orderItems as $item) {
        $checkStmt = $conn->prepare("SELECT id, name, price, stock, main_image FROM products WHERE id = ?");
        $checkStmt->execute([$item['product_id']]);
        $product = $checkStmt->fetch();
        
        if (!$product) {
            $conn->rollBack();
            echo json_encode([
                'success' => false, 
                'message' => "The product '{$item['name']}' does not exist or has been removed from sale"
            ]);
            exit();
        }
        
        if ($product['stock'] < $item['quantity']) {
            $conn->rollBack();
            echo json_encode([
                'success' => false, 
                'message' => "Product '{$product['name']}' has insufficient stock (remaining: {$product['stock']})"
            ]);
            exit();
        }
        
        // Store product info for invoice
        $productsInfo[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['main_image'],
            'quantity' => $item['quantity'],
            'size' => $item['size'],
            'color' => $item['color']
        ];
    }
    // ==================== End Stock Check ====================
    
    // 1. Create Order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method) 
                          VALUES (?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['total_amount'],
        $_POST['payment_method']
    ]);
    $orderId = $conn->lastInsertId();
    
    // 2. Add Order Items and Update Stock
    $orderItemStmt = $conn->prepare("INSERT INTO order_items 
                                   (order_id, product_id, product_name, quantity, price, size, color) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $updateStockStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    
    foreach ($orderItems as $item) {
        // Add order item
        $orderItemStmt->execute([
            $orderId,
            $item['product_id'],
            $item['name'],
            $item['quantity'],
            $item['price'],
            $item['size'],
            $item['color']
        ]);
        
        // Update stock
        $updateStockStmt->execute([
            $item['quantity'],
            $item['product_id']
        ]);
    }
    
    // 3. Clear Cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    $conn->commit();
    
    // Prepare invoice data
    $invoiceData = [
        'success' => true,
        'invoice' => [
            'order_id' => $orderId,
            'order_date' => date('Y-m-d H:i:s'),
            'payment_method' => $_POST['payment_method'],
            'total_amount' => $_POST['total_amount'],
            'items' => $productsInfo
        ]
    ];
    
    echo json_encode($invoiceData);
    
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}