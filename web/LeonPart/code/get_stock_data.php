<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

try {
    $stmt = $conn->prepare("SELECT name, stock FROM products");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stockData = [];
    foreach ($products as $product) {
        $stockData[$product['name']] = (int)$product['stock'];
    }

    echo json_encode($stockData);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>