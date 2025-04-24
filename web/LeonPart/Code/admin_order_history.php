<?php
session_start();
require_once 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "Please log in as admin to view all order histories.";
    exit();
}

// Pagination settings
$recordsPerPage = 10; // Display 10 orders per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Ensure pagination parameters are integers
$recordsPerPage = (int) $recordsPerPage;
$offset = (int) $offset;

try {
    // Get total number of orders
    $countStmt = $conn->prepare("
        SELECT COUNT(*) as total 
        FROM order_history
    ");
    $countStmt->execute();
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Calculate total pages
    $totalPages = ceil($totalRecords / $recordsPerPage);

    // Fetch orders for the current page, including color and size
    $stmt = $conn->prepare("
        SELECT user_id, order_id, product_name, quantity, price, payment_method, total_amount, paid_at, colour, size 
        FROM order_history 
        ORDER BY paid_at DESC
        LIMIT $recordsPerPage OFFSET $offset
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total quantity for all orders (not just the current page)
    $totalQuantityStmt = $conn->prepare("
        SELECT SUM(quantity) as total_quantity 
        FROM order_history
    ");
    $totalQuantityStmt->execute();
    $totalQuantity = (int) $totalQuantityStmt->fetch(PDO::FETCH_ASSOC)['total_quantity'];

} catch (PDOException $e) {
    echo "Failed to retrieve order history: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Order History</title>
    <style>
        table { border-collapse: collapse; width: 90%; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .no-orders { text-align: center; font-size: 18px; margin-top: 40px; color: #666; }
        .product-name { cursor: pointer; color: blue; text-decoration: underline; }

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
            text-decoration: underline;
            background-color: #eee;
        }

        .total-quantity { text-align: right; width: 90%; margin: 10px auto; font-weight: bold; }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 350px;
            text-align: center;
            border-radius: 5px;
        }
        .modal-content img {
            max-width: 100%;
            height: auto;
            margin-bottom: 15px;
        }
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-btn:hover {
            color: #000;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">All Order History</h2>

    <!-- Modal -->
    <div id="greetingModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">×</span>
            <img id="modalImage" src="" alt="Product Image">
            <p>Color: <span id="modalColor"></span></p>
            <p>Size: <span id="modalSize"></span></p>
        </div>
    </div>

    <?php if (count($orders) > 0): ?>
        <table>
            <tr>
                <th>No.</th>
                <th>User ID</th>
                <th>Order ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price (RM)</th>
                <th>Payment Method</th>
                <th>Total</th>
                <th>Paid At</th>
            </tr>
            <?php
            // Calculate the starting number for the current page
            $startNumber = ($page - 1) * $recordsPerPage + 1;
            $counter = $startNumber;
            foreach ($orders as $order): ?>
            <tr>
                <td><?= $counter++ ?></td>
                <td><?= htmlspecialchars($order['user_id']) ?></td>
                <td><?= htmlspecialchars($order['order_id']) ?></td>
                <td>
                    <span class="product-name" 
                          onclick="showModal('<?= htmlspecialchars($order['product_name']) ?>', '<?= htmlspecialchars($order['colur'] ?? 'N/A') ?>', '<?= htmlspecialchars($order['size'] ?? 'N/A') ?>')">
                        <?= htmlspecialchars($order['product_name']) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($order['quantity']) ?></td>
                <td><?= number_format($order['price'], 2) ?></td>
                <td><?= htmlspecialchars($order['payment_method']) ?></td>
                <td><?= number_format($order['total_amount'], 2) ?></td>
                <td><?= htmlspecialchars($order['paid_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <!-- Display total quantity for all orders -->
        <div class="total-quantity">
            Total Quantity (All Orders): <?= $totalQuantity ?>
        </div>
    <?php else: ?>
        <p class="no-orders">No orders found.</p>
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

    <script>
        // Product name to image URL mapping
        const productImages = {
            "Cropped short-sleeved sweatshirt": "http://localhost/a/Website_assignment/web/YuchenPart/W1Demo/image/99b409182e6c8c61e9f99758a2325cfb09f10b1c.avif",
            "Loose Fit Seam-detail T-shirt": "http://localhost/a/Website_assignment/web/YuchenPart/W1Demo/image/4a68c1a9e7c6f627e1e40fae929bca0b5932d13b.avif",
            "Slim Fit Cotton twill trousers": "http://localhost/a/Website_assignment/web/YuchenPart/W1Demo/image/hmgoepprod.jpg",
            "Mid-length 2-in-1 sports shorts with DryMove™": "http://localhost/a/Website_assignment/web/YuchenPart/W1Demo/image/3d6b5b0bf2811207034d2ff4279dd615861b0d3f.avif",
            "Embroidered Peter Pan-collared blouse": "http://localhost/a/Website_assignment/web/YuchenPart/W1Demo/image/dcd1b55d5074e4bb0c0dd34207227994fa36f0e9.avif",
            "Barrel-leg jeans": "http://localhost/a/Website_assignment/web/YuchenPart/W1Demo/image/bcc0a005f9990d44e613460e3472abce037a8670.avif",
            "Cotton pyjamas": "http://localhost/a/Website_assignment/web/YuchenPart/W1Demo/image/9061ab824853874ed099532bb997018fede089f7.avif",
            "Boxy denim jacket": "http://localhost/a/Website_assignment/web/YuchenPart/W1Demo/image/a12186ac78ad701e5b359329c4e14b9a9953147a.avif",
            "Washed-look sweatshirt": "http://localhost/a/Website_assignment/web/YuchenPart/W1Demo/image/56b2ad109cfc2160a382a80632f4d84c0a4e0a36.avif",
            "Flared Leg Low Jeans": "http://localhost/a/Website_assignment/web/YuchenPart/W1Demo/image/hmgoepprod%20(1).jpg"
        };
        

        function showModal(productName, color, size) {
            // Set color and size
            document.getElementById('modalColor').textContent = color;
            document.getElementById('modalSize').textContent = size;

            // Set product image
            const imageUrl = productImages[productName] || "image/default.jpg"; // Fallback image if not found
            document.getElementById('modalImage').src = imageUrl;

            // Show modal
            document.getElementById('greetingModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('greetingModal').style.display = 'none';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            var modal = document.getElementById('greetingModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>