<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please login to view your order history.";
    exit();
}

$userId = $_SESSION['user_id'];
$recordsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

$recordsPerPage = (int) $recordsPerPage;
$offset = (int) $offset;

try {
    $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM order_history WHERE user_id = ?");
    $countStmt->execute([$userId]);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    $totalPages = ceil($totalRecords / $recordsPerPage);

    $stmt = $conn->prepare("
        SELECT order_id, product_name, quantity, price, payment_method, total_amount, paid_at 
        FROM order_history 
        WHERE user_id = ? 
        ORDER BY paid_at DESC
        LIMIT $recordsPerPage OFFSET $offset
    ");
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalQuantityStmt = $conn->prepare("SELECT SUM(quantity) as total_quantity FROM order_history WHERE user_id = ?");
    $totalQuantityStmt->execute([$userId]);
    $totalQuantity = (int) $totalQuantityStmt->fetch(PDO::FETCH_ASSOC)['total_quantity'];

} catch (Exception $e) {
    echo "Failed to retrieve order history: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Order History</title>
    <style>
        table { border-collapse: collapse; width: 90%; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .product-name { cursor: pointer; color: blue; text-decoration: underline; }
        .no-orders { text-align: center; font-size: 18px; margin-top: 40px; color: #666; }

        .pagination { text-align: center; margin-top: 20px; }
        .pagination a {
            margin: 0 5px;
            padding: 6px 12px;
            text-decoration: none;
            border: 1px solid #ccc;
            color: #333;
        }
        .pagination a.active {
            font-weight: bold;
            background-color: #eee;
        }

        .total-quantity { text-align: right; width: 90%; margin: 10px auto; font-weight: bold; }

        #imageModal {
            display: none;
            position: fixed;
            z-index: 999;
            padding-top: 60px;
            left: 0; top: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.6);
        }
        #imageModal img {
            display: block;
            margin: auto;
            max-width: 80%;
            max-height: 80%;
            border-radius: 10px;
        }
        #imageModal .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Your Order History</h2>

    <?php if (count($orders) > 0): ?>
        <table>
            <tr>
                <th>No.</th>
                <th>Order ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price (RM)</th>
                <th>Payment Method</th>
                <th>Total</th>
                <th>Paid At</th>
            </tr>
            <?php
            $startNumber = ($page - 1) * $recordsPerPage + 1;
            $counter = $startNumber;
            foreach ($orders as $order): ?>
            <tr>
                <td><?= $counter++ ?></td>
                <td><?= htmlspecialchars($order['order_id']) ?></td>
                <td class="product-name" onclick="showImage('<?= htmlspecialchars($order['product_name']) ?>')">
                    <?= htmlspecialchars($order['product_name']) ?>
                </td>
                <td><?= htmlspecialchars($order['quantity']) ?></td>
                <td><?= number_format($order['price'], 2) ?></td>
                <td><?= htmlspecialchars($order['payment_method']) ?></td>
                <td><?= number_format($order['total_amount'], 2) ?></td>
                <td><?= htmlspecialchars($order['paid_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <div class="total-quantity">
            Total Quantity (All Orders): <?= $totalQuantity ?>
        </div>
    <?php else: ?>
        <p class="no-orders">You have no orders yet.</p>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">« Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>">Next »</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Modal -->
    <div id="imageModal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="productImage" src="" alt="Product Image">
    </div>

    <script>
    function showImage(productName) {
    const imageMap = {
        "Cropped short-sleeved sweatshirt": "/a/WEBSITE_ASSIGNMENT/web/YuchenPart/W1Demo/image/3d6b5b0bf2811207034d2ff4279dd615861b0d3f.avif",
        "Slim Fit Cotton twill trousers": "product_images/slimfit.jpg",
        "Barrel-leg jeans": "product_images/barrel.jpg",
        "Flared Leg Low Jeans": "product_images/flared.jpg"
        // Add more mappings as needed
    };

    const imagePath = imageMap[productName];
    const modal = document.getElementById('imageModal');
    const img = document.getElementById('productImage');

    if (imagePath) {
        img.src = imagePath;
        modal.style.display = 'block';
    } else {
        alert("No image found for this product.");
    }
}

    </script>
</body>
</html>
