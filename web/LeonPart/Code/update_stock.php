<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

// 获取POST数据
$data = json_decode(file_get_contents('php://input'), true);

try {
    $conn->beginTransaction();
    
    foreach ($data['items'] as $item) {
        // 更新库存
        $stmt = $conn->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE name = ?");
        $stmt->execute([$item['quantity'], $item['name']]);
    }
    
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
}
?>