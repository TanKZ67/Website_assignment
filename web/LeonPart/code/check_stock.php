<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $items = $data['items'] ?? [];
    
    $conn->beginTransaction();
    
    $errors = [];
    foreach ($items as $item) {
        $stmt = $conn->prepare("SELECT stock FROM products WHERE name = ? FOR UPDATE");
        $stmt->execute([$item['name']]);
        $product = $stmt->fetch();
        
        if (!$product) {
            $errors[] = "Product {$item['name']} not found";
        } elseif ($product['stock'] < $item['quantity']) {
            $errors[] = "Insufficient stock for {$item['name']} (Available: {$product['stock']}, Requested: {$item['quantity']})";
        } else {
            $updateStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE name = ?");
            $updateStmt->execute([$item['quantity'], $item['name']]);
        }
    }
    
    if (empty($errors)) {
        $conn->commit();
        echo json_encode(['success' => true]);
    } else {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => implode("\n", $errors)]);
    }
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>