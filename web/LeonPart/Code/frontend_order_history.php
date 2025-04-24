<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Order History</title>
    <style>
        table { border-collapse: collapse; width: 90%; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
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

        <!-- 示例数据（你可替换为 PHP 循环） -->
        <tr>
            <td>1</td>
            <td>36</td>
            <td class="product-name" onclick="showImage('Cropped short-sleeved sweatshirt')">Cropped short-sleeved sweatshirt</td>
            <td>3</td>
            <td>15.00</td>
            <td>paypal</td>
            <td>90.00</td>
            <td>2025-04-24 15:31:36</td>
        </tr>
        <tr>
            <td>2</td>
            <td>34</td>
            <td class="product-name" onclick="showImage('Slim Fit Cotton twill trousers')">Slim Fit Cotton twill trousers</td>
            <td>1</td>
            <td>17.00</td>
            <td>paypal</td>
            <td>17.00</td>
            <td>2025-04-24 15:22:18</td>
        </tr>
        <!-- 更多数据…… -->
    </table>

    <div class="total-quantity">Total Quantity (All Orders): 46</div>

    <!-- Modal 弹窗 -->
    <div id="imageModal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="productImage" src="" alt="Product Image">
    </div>

    <script>
function showImage(productName) {
    const imageMap = {
        "Cropped short-sleeved sweatshirt": "product_images/3d6b5b0bf2811207034d2ff4279dd615861b0d3f.avif",
        // 可以继续添加其他产品映射
        "Slim Fit Cotton twill trousers": "product_images/slimfit.jpg",
        "Barrel-leg jeans": "product_images/barrel.jpg",
        "Flared Leg Low Jeans": "product_images/flared.jpg"
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

function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
}
</script>

</body>
</html>
