<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    // 1. 检查产品库存
    $productStmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
    $productStmt->execute([$data['product_id']]);
    $stock = $productStmt->fetchColumn();
    
    // 2. 计算购物车中该产品的总数量
    $cartQtyStmt = $conn->prepare("SELECT COALESCE(SUM(quantity), 0) FROM cart 
                                  WHERE user_id = ? AND product_id = ?");
    $cartQtyStmt->execute([$_SESSION['user_id'], $data['product_id']]);
    $cartQuantity = $cartQtyStmt->fetchColumn();
    
    // 3. 验证库存是否足够
    if (($cartQuantity + $data['quantity']) > $stock) {
        echo json_encode([
            'success' => false,
            'message' => 'Not enough stock available'
        ]);
        exit();
    }
    
    // 4. 检查是否已存在相同变体
    $existingStmt = $conn->prepare("SELECT id, quantity FROM cart 
                                   WHERE user_id = ? AND product_id = ? 
                                   AND size = ? AND color = ?");
    $existingStmt->execute([
        $_SESSION['user_id'],
        $data['product_id'],
        $data['size'],
        $data['color']
    ]);
    $existingItem = $existingStmt->fetch();
    
    if ($existingItem) {
        // 更新现有项数量
        $updateStmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $updateStmt->execute([
            $existingItem['quantity'] + $data['quantity'],
            $existingItem['id']
        ]);
    } else {
        // 添加新项
        $insertStmt = $conn->prepare("INSERT INTO cart 
                                    (user_id, product_id, quantity, size, color) 
                                    VALUES (?, ?, ?, ?, ?)");
        $insertStmt->execute([
            $_SESSION['user_id'],
            $data['product_id'],
            $data['quantity'],
            $data['size'],
            $data['color']
        ]);
    }
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>