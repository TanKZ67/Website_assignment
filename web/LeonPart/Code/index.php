<?php
session_start();
$_SESSION["user_id"] =33;
// For testing only - remove this in production


require_once 'db_connection.php';
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="\W1Demo\image\a7963aaa-618f-4c51-9f7e-e8699e81eed8.png">
    <style>
        /* 新增未搜索到产品的提示样式 */
        .no-results {
            display: none;
            /* 默认隐藏 */
            text-align: center;
            font-size: 50px;
            color: white;
            margin-top: 250px;
        }

        #modalName {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            margin-right: 50px;
        }

        .button-position {
            clear: both;
            margin-top: 20px;
            text-align: center;
        }

        .size-buttons,
        .color-buttons,
        .quantity-selector {
            margin-bottom: 15px;
        }

        #modalDescription {
            font-size: 16px;
            color: #555;
            text-align: left;
            margin-top: 20px;
            list-style-type: disc;
            padding-left: 20px;
        }

        #modalDescription li {
            margin-bottom: 10px;
        }

        .color-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }

        .color-button-lightblue {
            padding: 15px;
            border: 2px solid #ccc;
            border-radius: 50px;
            cursor: pointer;
            background-color: lightblue;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .color-button-darkblue {
            padding: 15px;
            border: 2px solid #ccc;
            border-radius: 50px;
            cursor: pointer;
            background-color: darkblue;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .color-button-white {
            padding: 15px;
            border: 2px solid #ccc;
            border-radius: 50px;
            cursor: pointer;
            background-color: white;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .color-button-black {
            padding: 15px;
            border: 2px solid #ccc;
            border-radius: 50px;
            cursor: pointer;
            background-color: black;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .color-button-khakigreen {
            padding: 15px;
            border: 2px solid #ccc;
            border-radius: 50px;
            cursor: pointer;
            background-color: rgb(97, 110, 79);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .color-button-lightbeige {
            padding: 15px;
            border: 2px solid #ccc;
            border-radius: 50px;
            cursor: pointer;
            background-color: beige;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .color-button-beige {
            padding: 15px;
            border: 2px solid #ccc;
            border-radius: 50px;
            cursor: pointer;
            background-color: beige;
            transition: background-color 0.3s ease, color 0.3s ease;
        }


        .color-button-grey {
            padding: 15px;
            border: 2px solid #ccc;
            border-radius: 50px;
            cursor: pointer;
            background-color: gray;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .color-button-pink {
            padding: 15px;
            border: 2px solid #ccc;
            border-radius: 50px;
            cursor: pointer;
            background-color: pink;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .color-button-lightblue:hover,
        .color-button-beige:hover,
        .color-button-white:hover,
        .color-button-pink:hover,
        .color-button-black:hover,
        .color-button-grey:hover,
        .color-button-khakigreen:hover,
        .color-button-lightbeige:hover,
        .color-button-darkblue:hover {
            border-color: black;
            transition: 0.3s ease;
        }

        .color-button-lightblue.selected,
        .color-button-beige.selected,
        .color-button-pink.selected,
        .color-button-white.selected,
        .color-button-black.selected,
        .color-button-lightbeige.selected,
        .color-button-khakigreen.selected,
        .color-button-grey.selected,
        .color-button-darkblue.selected {
            border-color: black;
        }

        /* 数量选择器样式 */
        .quantity-selector {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .quantity-button {
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
            background-color: white;
            transition: background-color 0.3s ease, color 0.3s ease;
            margin: 10px;
        }

        .quantity-button:hover,
        .buy-modal .add-to-cart-button:hover,
        .size-button:hover {
            background-color: blueviolet;
            color: white;
        }

        .quantity-display {
            font-size: 16px;
            font-weight: bold;
            min-width: 30px;
            text-align: center;
            color: black;
        }

        /* 交叉按钮样式 */
        .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #333;
        }

        .close-button:hover {
            color: #ff4d4d;
            /* 鼠标悬停时变为红色 */
        }

        /* 弹窗样式 */
        .buy-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 800px;
            height: 600px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            text-align: center;
            z-index: 1001;
            padding-top: 40px;
        }

        .buy-modal img {
            width: 40%;
            max-height: 350px;
            object-fit: contain;
            border-radius: 5px;
            float: left;
            margin-right: 20px;
        }


        .size-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 10px;
        }

        .size-button {
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
            background-color: white;
            transition: background-color 0.3s ease, color 0.3s ease;
            margin-top: 20px;
        }

        .size-button.selected {
            background-color: blueviolet;
            color: white;
            border-color: blueviolet;
        }

        .buy-modal .add-to-cart-button {
            margin: 10px;
            padding: 10px 20px;
            font-size: 18px;
        }

        /* 遮罩层样式 */
        .overlay {
            display: none;
            /* 默认隐藏 */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            /* 确保在弹窗下层 */
        }

        /* 购物车样式 */
        .cart-window {
            display: none;
            /* 默认隐藏 */
            position: fixed;
            top: 50px;
            right: 10px;
            width: 400px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            padding: 15px;
            text-align: left;
            padding-top: 20px;
            /* 为交叉按钮留出空间 */
        }

        /* 其他样式保持不变 */
        .size-select {
            margin-top: 5px;
            padding: 3px;
            width: 80%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .category {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .category-toggle {
            cursor: pointer;
        }

        .sidebar {
            width: auto;
            background-color: blanchedalmond;
            padding: 15px;
            height: auto;
            position: absolute;
            left: 10px;
            top: 30px;
            display: none;
            border-radius: 10px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .sidebar h3 {
            color: black;
            text-decoration: underline;
        }

        a:hover {
            color: aliceblue;
        }

        .login {
            position: fixed;
            top: 1px;
            color: white;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: linear-gradient(to bottom, black 100px, transparent);
            z-index: 1000;
        }

        .search-button:hover {
            background-color: blanchedalmond;
        }

        body {
            text-align: center;
            background-repeat: no-repeat;
            padding-top: 100px;
            background-image: url('\\W1Demo\\image\\d6d45420a5ec7ddd340125915c3cbe688c7ac3ef45faf8-gkTYsT_fw658.webp');
            background-attachment: fixed;
            background-size: cover;
        }

        .search-container {
            position: absolute;
            bottom: 5px;
            right: 600px;
            display: inline-block;
        }

        input[type="search"] {
            padding: 8px;
            width: 600px;
            border: 1px solid #ccc;
            padding-right: 50px;
        }

        .search-button {
            position: relative;
            right: 45px;
            top: 0%;
            background-color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .cart {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: white;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 24px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.46);
        }

        .cart:hover {
            background-color: blanchedalmond;
        }

        .products {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
            color: white;
        }

        .product {
            border: 1px solid purple;
            padding-left: 15px;
            padding-right: 15px;
            margin: 5px;
            background-color: white;
            width: 200px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            /* 添加过渡效果 */
        }

        .product:hover {
            transform: scale(1.05);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            /* 悬停时添加阴影 */
        }

        .product p {
            min-height: 40px;
        }

        .product img {
            width: 100%;
            object-fit: contain;
            max-height: 150px;
        }

        .product button:hover {
            background-color: blueviolet;
        }

        .product button {
            margin-top: 10px;
            padding: 5px 10px;
            background-color: rgb(255, 0, 221);
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        a {
            text-decoration: none;
            margin: 5px;
            color: white;
        }

        a:hover {

            color: pink;
        }


        #promotionimage {
            position: relative;
            margin-left: 480px;
            width: 50%;
            height: 300px;
            overflow: hidden;
        }

        #promotionimage img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            object-position: top;
            top: 0;
            left: 0;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        #promotionimage img.active {
            opacity: 1;
        }

        .dots-container {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 5px;
        }

        .dot {
            width: 10px;
            height: 10px;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .dot.active {
            background-color: white;
        }

        p {
            color: black;
        }
    </style>

    <title> Online Clothes Shop </title>

</head>

<body>
    <!-- 新增未搜索到产品的提示 -->
    <div id="noResultsMessage" class="no-results">
        No results found
    </div>

    <div class="header">
        <div class="sidebar" id="categorySidebar">
            <h3>Categories</h3>

            <div class="category">
                <input type="checkbox" id="men" value="men" onclick="filterProducts()">
                <label for="men">Men</label>
            </div>

            <div class="category">
                <input type="checkbox" id="women" value="women" onclick="filterProducts()">
                <label for="women">Women</label>
            </div>

            <div class="category">
                <input type="checkbox" id="kids 2-8Y" value="kids 2-8Y" onclick="filterProducts()">
                <label for="kids 2-8Y">Kids 2-8Y</label>
            </div>

            <div class="category">
                <input type="checkbox" id="kids 9-14Y" value="kids 9-14Y" onclick="filterProducts()">
                <lable for="kids 9-14Y">Kids 9-14Y</lable>
            </div>
            <button class="close-button" onclick="closesidebar()">×</button>
        </div>

        <div onsubmit="return false">

            <div class="search-container">
                <input id="searchInput" type="search" name="search" placeholder="Search Item">
                <button class="search-button" type="button" onclick="searchProducts()">
                    <i class="fa-solid fa-magnifying-glass"></i></button>
            </div>

        </div>

        <nav class="login">
            <a class="category-toggle" onclick="toggleSidebar()">☰ Categories</a>|
            <a href="../../Devonpart/web/login.php">Login</a>|
            <a href="../../Devonpart/web/register.php">Sign Up</a>|
            <a href="http://localhost/a/Website_assignment/web/YuchenPart/Content.php">Admin</a> |
            <a href="order_history.php">Order History</a>
        </nav>

        <div class="cart" onclick="showCart()">🛒 <span id="cartCount">0</span></div>

        <div id="cartWindow" class="cart-window">
            <h3>Shopping Cart</h3>
            <ul id="cartItems"></ul>
            <button class="close-button" onclick="closeCart()">×</button>
        </div>

        <h1 style="color:white;font-size: 30px; position:absolute;right:730px; bottom:25px; "><img style="height: 40px"
                src="\W1Demo\image\giphy.gif" alt="Clothes-Gif">TARUMT Clothes Shop<img style="height: 40px"
                src="\W1Demo\image\giphy.gif" alt="Clothes-Gif"></h1>
    </div>

    <div id="denglong">
        <img style="position:absolute;right:50px;" src="\W1Demo\image\c4f118dfc49446d789f9c61a27c1d359.gif"
            alt="Beutiful.Gif">
        <img style="position:absolute;left:50px;" src="\W1Demo\image\c4f118dfc49446d789f9c61a27c1d359.gif"
            alt="Beutiful.Gif">
    </div>

    <div id="promotionimage">

        <img src="\W1Demo\image\b615fe5275462027dd9604606b3b03d5d93b0215.jpg" alt="Promotion Image 1" class="active">
        <img src="\W1Demo\image\1977b0a0051bb199821ae68f639d966bdee6ab81.jpg" alt="Promotion Image 2">
        <img src="\W1Demo\image\dcd1b55d5074e4bb0c0dd34207227994fa36f0e9.jpg" alt="Promotion Image 3">
        <div class="dots-container">
            <div class="dot active" data-index="0"></div>
            <div class="dot" data-index="1"></div>
            <div class="dot" data-index="2"></div>

        </div>
    </div>


    <div class="products">
        <div class="overlay" id="overlay"></div>
        <div class="buy-modal" id="buyModal">
            <button class="close-button" onclick="closeModal()">×</button>
            <img id="modalImage" src="" alt="Product Image">
            <p id="modalName"></p>
            <ul id="modalDescription"></ul>
            <div class="button-position">
                <div class="size-buttons">
                    <button class="size-button" data-size="S">S</button>
                    <button class="size-button" data-size="M">M</button>
                    <button class="size-button" data-size="L">L</button>
                    <button class="size-button" data-size="XL">XL</button>
                </div>
                <div class="color-buttons"></div>
                <div class="quantity-selector">
                    <button class="quantity-button" onclick="changeQuantity(-1)">-</button>
                    <span class="quantity-display" id="quantityDisplay">1</span>
                    <button class="quantity-button" onclick="changeQuantity(1)">+</button>
                </div>
                <button class="add-to-cart-button" onclick="addToCartFromModal()">Add to Cart</button>
            </div>
        </div>
        <?php
        require_once 'db_connection.php';

        $stmt = $conn->prepare("SELECT id, name, price, main_image, description, stock, category, colors FROM products");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            $colors = json_decode($product['colors'], true);
            echo '
    <div class="product" 
        data-id="' . $product['id'] . '"
        data-name="' . htmlspecialchars($product['name'], ENT_QUOTES) . '"
        data-category="' . $product['category'] . '"
        data-colors=\'' . htmlspecialchars(json_encode($colors), ENT_QUOTES) . '\'
        data-description="' . htmlspecialchars($product['description'], ENT_QUOTES) . '"
        data-stock="' . $product['stock'] . '"
        onclick="showModal(\'' . addslashes($product['name']) . '\', ' . $product['price'] . ', \'' . $product['main_image'] . '\', this)">
        <img src="' . $product['main_image'] . '">
        <p>' . $product['name'] . '</p>
        <p>$' . $product['price'] . '</p>
    </div>';
        }
        ?>
    </div>

    <script>
        function initCartCount() {
            fetch('get_cart.php')
                .then(response => response.json())
                .then(cartItems => {
                    // 更新全局cartItems变量
                    window.cartItems = cartItems;
                    document.getElementById("cartCount").textContent = cartItems.length;
                })
                .catch(error => {
                    console.error('Error fetching cart count:', error);
                });
        }

        // 页面加载时初始化购物车数量
        window.onload = initCartCount;

        // 颜色按钮点击事件
        document.querySelectorAll('.color-button-lightblue,.color-button-white,.color-button-black,.color-button-grey,.color-button-darkblue,.color-button-pink,.color-button-khakigreen,.color-button-lightbeige,.color-button-beige').forEach(button => {
            button.addEventListener('click', () => {
                // 移除其他按钮的选中状态
                document.querySelectorAll('.color-button-lightblue,.color-button-beige,.color-button-white,.color-button-black,.color-button-pink,.color-button-grey,.color-button-darkblue,.color-button-khakigreen,.color-button-lightbeige').forEach(btn => btn.classList.remove('selected'));
                // 设置当前按钮为选中状态
                button.classList.add('selected');
                // 切换图片
                const modalImage = document.getElementById('modalImage');
                modalImage.src = button.getAttribute('data-image');
                // 记录选中的颜色
                selectedColor = button.getAttribute('data-color');
            });
        });

        function showModal(name, price, imageSrc, productElement) {
            const modal = document.getElementById('buyModal');
            const overlay = document.getElementById('overlay');
            const modalImage = document.getElementById('modalImage');
            const modalName = document.getElementById('modalName');
            const modalDescription = document.getElementById('modalDescription');
            const colorButtonsContainer = document.querySelector('.color-buttons');

            // 设置弹窗内容
            modalImage.src = imageSrc;
            modalName.textContent = `${name} - $${price}`;

            // 获取描述文本
            const descriptionText = productElement.getAttribute('data-description');
            const descriptionLines = descriptionText.split('|');
            modalDescription.innerHTML = '';
            descriptionLines.forEach(line => {
                const li = document.createElement('li');
                li.textContent = line.trim();
                modalDescription.appendChild(li);
            });

            // 重置尺寸、颜色选择和数量
            selectedSize = null;
            selectedColor = null;
            quantity = 1;
            document.getElementById('quantityDisplay').textContent = quantity;
            const sizeButtons = document.querySelectorAll('.size-button');
            sizeButtons.forEach(button => button.classList.remove('selected'));

            // 清空颜色按钮
            colorButtonsContainer.innerHTML = '';

            // 获取产品的颜色数据
            const colors = JSON.parse(productElement.getAttribute('data-colors'));

            // 动态生成颜色按钮
            colors.forEach(colorData => {
                const button = document.createElement('button');
                button.className = `color-button-${colorData.color.toLowerCase()}`;
                button.setAttribute('data-color', colorData.color);
                button.setAttribute('data-image', colorData.image);
                button.addEventListener('click', () => {
                    document.querySelectorAll('.color-buttons button').forEach(btn => btn.classList.remove('selected'));
                    button.classList.add('selected');
                    modalImage.src = colorData.image;
                    selectedColor = colorData.color;
                });
                colorButtonsContainer.appendChild(button);
            });

            // 显示库存信息
            const stock = productElement.getAttribute('data-stock'); // 直接读取数据库中的值
            const stockInfo = document.createElement('p');
            stockInfo.textContent = `Stock: ${stock}`;
            stockInfo.style.marginTop = '10px';
            stockInfo.style.fontWeight = 'bold';
            modalDescription.appendChild(stockInfo);

            // 显示弹窗和遮罩层
            modal.style.display = 'block';
            overlay.style.display = 'block';
        }

        // 关闭弹窗
        function closeModal() {
            const modal = document.getElementById('buyModal');
            const overlay = document.getElementById('overlay');

            // 隐藏弹窗和遮罩层
            modal.style.display = 'none';
            overlay.style.display = 'none';
        }

        let cartItems = [];
        let selectedSize = null; // 用于存储用户选择的尺寸
        let selectedColor = null; // 用于存储用户选择的颜色
        let quantity = 1; // 默认数量为 1

        // 修改数量
        function changeQuantity(amount) {
            quantity += amount;
            if (quantity < 1) quantity = 1; // 数量不能小于 1
            document.getElementById('quantityDisplay').textContent = quantity;
        }


        // 尺寸按钮点击事件
        document.querySelectorAll('.size-button').forEach(button => {
            button.addEventListener('click', () => {
                // 移除其他按钮的选中状态
                document.querySelectorAll('.size-button').forEach(btn => btn.classList.remove('selected'));
                // 设置当前按钮为选中状态
                button.classList.add('selected');
                // 记录选中的尺寸
                selectedSize = button.getAttribute('data-size');
            });
        });

        function addToCartFromModal() {
            if (!selectedSize) {
                alert('Please select a size.');
                return;
            } else if (!selectedColor) {
                alert('Please select a color.')
                return;
            }

            const modal = document.getElementById('buyModal');
            const modalName = document.getElementById('modalName').textContent.split(' - ')[0];
            const modalPrice = parseFloat(document.getElementById('modalName').textContent.split(' - $')[1]);
            const modalImage = document.getElementById('modalImage').src;
            const productElement = document.querySelector('.product[data-name="' + modalName + '"]');
            const productId = productElement ? productElement.getAttribute('data-id') : null;

            if (!productId) {
                alert('Product ID not found');
                return;
            }

            // Prepare the cart item data
            const cartItem = {
                product_id: parseInt(productId),
                quantity: quantity,
                size: selectedSize,
                color: selectedColor
            };

            // Send to server
            fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(cartItem)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Item added to cart successfully');
                        updateCartDisplay();
                        location.reload(); // 添加这行代码来刷新页面
                        closeModal();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to add item to cart');
                });
        }

        // 更新购物车显示 - 完全基于数据库
function updateCartDisplay() {
    fetch('get_cart.php')
        .then(response => response.json())
        .then(cartItemsFromDB => {
            // 更新全局cartItems变量
            window.cartItems = cartItemsFromDB;
            
            const cartList = document.getElementById("cartItems");
            cartList.innerHTML = "";
            let total = 0;

            cartItemsFromDB.forEach((item) => {
                const li = document.createElement("li");
                li.className = "cart-item";
                li.innerHTML = `
                    <img src="${item.main_image}" alt="${item.name}">
                    <span>${item.name} - $${item.price * item.quantity}<br> 
                    Size: ${item.size} <br> Color: ${item.color}</span>
                    <div style="display: flex; align-items: center;">
                        <button onclick="changeCartQuantity(${item.cart_id}, -1)">-</button>
                        <span style="margin: 0 10px;">${item.quantity}</span>
                        <button onclick="changeCartQuantity(${item.cart_id}, 1)">+</button>
                    </div>
                    <button onclick="removeFromCart(${item.cart_id})">×</button>
                `;
                cartList.appendChild(li);
                total += item.price * item.quantity;
            });

            document.getElementById("cartTotal").textContent = total.toFixed(2);
            document.getElementById("proceedToPayment").disabled = cartItemsFromDB.length === 0;
            document.getElementById("cartCount").textContent = cartItemsFromDB.length;
        })
        .catch(error => {
            console.error('Error updating cart display:', error);
        });
}

        // 重定向到购物车页面
        function showCart() {
            window.location.href = 'cart.php';
        }


        function searchProducts() {
            let query = document.getElementById("searchInput").value.trim();
            if (query) {
                alert(`Searching for: ${query}`);
            } else {
                alert("Please enter a search term.");
            }
            return false;
        }

        function filterProducts() {
            let checkedCategories = [];
            let checkboxes = document.querySelectorAll(".sidebar input[type='checkbox']:checked");

            checkboxes.forEach(checkbox => {
                checkedCategories.push(checkbox.value);
            });

            let products = document.querySelectorAll(".product");

            products.forEach(product => {
                let productCategory = product.getAttribute("data-category");

                if (checkedCategories.length === 0 || checkedCategories.includes(productCategory)) {
                    product.style.display = "block";
                } else {
                    product.style.display = "none";
                }
            });
        }

        function toggleSidebar() {
            let sidebar = document.getElementById("categorySidebar");
            if (sidebar.style.display === "none" || sidebar.style.display === "") {
                sidebar.style.display = "block";
            } else {
                sidebar.style.display = "none";
            }
        }

        function searchProducts() {
            let query = document.getElementById("searchInput").value.trim().toLowerCase();
            let products = document.querySelectorAll(".product");
            let promotionImage = document.getElementById("promotionimage");
            let denglong = document.getElementById("denglong");
            let noResultsMessage = document.getElementById("noResultsMessage");

            if (query) {
                promotionImage.style.display = "none"; // 隐藏图片
                denglong.style.display = "none"; // 隐藏图片
            } else {
                promotionImage.style.display = "block"; // 恢复显示图片
                denglong.style.display = "block"; // 恢复显示图片
                noResultsMessage.style.display = "none"; // 隐藏未搜索到产品的提示
                products.forEach(product => product.style.display = "block"); // 显示所有产品
                return;
            }

            let found = false;
            products.forEach(product => {
                let productName = product.querySelector("p").textContent.toLowerCase();
                if (productName.includes(query)) {
                    product.style.display = "block"; // 显示匹配的商品
                    found = true;
                } else {
                    product.style.display = "none"; // 隐藏不匹配的商品
                }
            });

            noResultsMessage.style.display = found ? "none" : "block"; // 根据是否找到匹配商品显示或隐藏提示
        }

        function closesidebar() {
            document.getElementById("categorySidebar").style.display = "none";
        }
        // 监听搜索框输入事件
        document.getElementById("searchInput").addEventListener("input", searchProducts);
        // 图片轮播功能
        const images = document.querySelectorAll("#promotionimage img");
        const dots = document.querySelectorAll(".dot");
        let currentImageIndex = 0;

        function showImage(index) {
            // 隐藏所有图片
            images.forEach(img => img.classList.remove("active"));
            // 显示当前图片
            images[index].classList.add("active");

            // 更新指示点
            dots.forEach(dot => dot.classList.remove("active"));
            dots[index].classList.add("active");
        }

        function nextImage() {
            currentImageIndex = (currentImageIndex + 1) % images.length;
            showImage(currentImageIndex);
        }

        // 自动轮播
        setInterval(nextImage, 3000); // 每3秒切换一次图片

        // 点击指示点切换图片
        dots.forEach((dot, index) => {
            dot.addEventListener("click", () => {
                currentImageIndex = index;
                showImage(currentImageIndex);
            });
        });
    </script>

</body>

</html>