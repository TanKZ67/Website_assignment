<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);

try {
    $conn->beginTransaction();
    
    // 验证阶段
    foreach ($data['items'] as $item) {
        $checkStmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
        $checkStmt->execute([$item['product_id']]);
        $stock = $checkStmt->fetchColumn();
        
        if ($stock < $item['quantity']) {
            throw new Exception("Insufficient stock for product ID: ".$item['product_id']);
        }
    }
    
    // 执行扣减
    foreach ($data['items'] as $item) {
        $updateStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $updateStmt->execute([$item['quantity'], $item['product_id']]);
    }
    
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollBack();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>