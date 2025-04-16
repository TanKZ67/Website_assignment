<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Check if product exists
    $stmt = $conn->prepare("SELECT id, stock FROM products WHERE id = ?");
    $stmt->execute([$data['product_id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit();
    }
    
    // Check stock availability
    if ($product['stock'] < $data['quantity']) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        exit();
    }
    
    // Check if item already in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart 
                           WHERE user_id = ? AND product_id = ? AND size = ? AND color = ?");
    $stmt->execute([
        $_SESSION['user_id'],
        $data['product_id'],
        $data['size'],
        $data['color']
    ]);
    $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingItem) {
        // Update quantity if item exists
        $newQuantity = $existingItem['quantity'] + $data['quantity'];
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$newQuantity, $existingItem['id']]);
    } else {
        // Add new item to cart
        $stmt = $conn->prepare("INSERT INTO cart 
            (user_id, product_id, quantity, size, color) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $data['product_id'],
            $data['quantity'],
            $data['size'],
            $data['color']
        ]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Item added to cart']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>