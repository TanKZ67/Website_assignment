<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT c.id as cart_id, c.*, p.name, p.price, p.main_image 
                          FROM cart c
                          JOIN products p ON c.product_id = p.id
                          WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>