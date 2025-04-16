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
    // 验证购物车项属于当前用户
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$data['cart_id'], $_SESSION['user_id']]);
    $item = $stmt->fetch();
    
    if (!$item) {
        echo json_encode(['success' => false, 'message' => 'Item not found in your cart']);
        exit();
    }
    
    $newQuantity = $item['quantity'] + $data['change'];
    if ($newQuantity < 1) $newQuantity = 1;
    
    // 更新数量
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->execute([$newQuantity, $data['cart_id']]);
    
    echo json_encode(['success' => true, 'newQuantity' => $newQuantity]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>