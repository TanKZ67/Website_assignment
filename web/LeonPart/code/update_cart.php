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
    // Get current cart item info including all variants of this product in cart
    $stmt = $conn->prepare("SELECT c.*, p.stock, 
                          (SELECT SUM(quantity) FROM cart 
                           WHERE product_id = c.product_id AND user_id = ?) as total_in_cart
                          FROM cart c 
                          JOIN products p ON c.product_id = p.id 
                          WHERE c.id = ? AND c.user_id = ?");
    $stmt->execute([$_SESSION['user_id'], $data['cart_id'], $_SESSION['user_id']]);
    $cartItem = $stmt->fetch();

    if (!$cartItem) {
        echo json_encode(['success' => false, 'message' => 'Cart item not found']);
        exit();
    }

    // Calculate new quantity for this specific cart item
    $newQuantity = $cartItem['quantity'] + $data['change'];
    $totalInCart = $cartItem['total_in_cart'] + $data['change']; // Total across all variants

    // Check if increasing quantity
    if ($data['change'] > 0) {
        // Check against total product stock (across all variants)
        if ($totalInCart > $cartItem['stock']) {
            $available = $cartItem['stock'] - ($cartItem['total_in_cart'] - $cartItem['quantity']);
            echo json_encode([
                'success' => false, 
                'message' => 'Cannot add more than available stock (Max additional: ' . $available . ')',
                'max_additional' => $available
            ]);
            exit();
        }
    }

    // Don't allow quantity less than 1
    if ($newQuantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Quantity cannot be less than 1']);
        exit();
    }

    // Update quantity
    $updateStmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    $updateStmt->execute([$newQuantity, $data['cart_id'], $_SESSION['user_id']]);

    echo json_encode([
        'success' => true,
        'newQuantity' => $newQuantity
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>