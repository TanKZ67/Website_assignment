<?php
header('Content-Type: application/json');

// 数据库连接
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_shopping";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// 获取所有订单（按日期降序）
$sql = "SELECT * FROM orders ORDER BY order_date DESC";
$result = $conn->query($sql);

$orders = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $order_id = $row['id'];
        
        // 获取订单商品
        $items_sql = "SELECT * FROM order_items WHERE order_id = $order_id";
        $items_result = $conn->query($items_sql);
        
        $items = [];
        if ($items_result->num_rows > 0) {
            while ($item = $items_result->fetch_assoc()) {
                // 添加商品图片（根据商品名称映射）
                $item['image'] = getProductImage($item['product_name']);
                $items[] = $item;
            }
        }
        
        $row['items'] = $items;
        $orders[] = $row;
    }
}

echo json_encode($orders);
$conn->close();

// 辅助函数：根据商品名称返回图片路径
function getProductImage($productName) {
    $imageMap = [
        "Cropped short-sleeved sweatshirt" => "/W1Demo/image/99b409182e6c8c61e9f99758a2325cfb09f10b1c.avif",
        "Loose Fit Seam-detail T-shirt" => "/W1Demo/image/4a68c1a9e7c6f627e1e40fae929bca0b5932d13b.avif",
        // 其他商品映射...
    ];
    return $imageMap[$productName] ?? "/W1Demo/image/default-product.jpg";
}
?>