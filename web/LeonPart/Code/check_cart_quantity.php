<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$productId = $_GET['product_id'] ?? null;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT SUM(quantity) as quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $productId]);
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'quantity' => $result['quantity'] ? (int)$result['quantity'] : 0
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>