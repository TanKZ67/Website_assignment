<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Aftersigin.css">
    <title>Dashboard</title>
</head>
<body>

    <div class="container">

        <!-- 上排：Memberlist + Order History -->
        <div class="top-row">
            <div class="box-section">
                <h1>Memberlist</h1>
                <div class="SPACE01">
                    <a href="Content.php">
                        <img src="user_icon_007.jpg" class="Clickanimation" alt="User Icon">
                    </a>
                </div>
            </div>

            <div class="box-section">
                <h1>Order History</h1>
                <div class="SPACE02">
                <a href="#" onclick="location.href='/a/Website_assignment/web/LeonPart/code/admin_order_history.php'">
                        <img src="Order.history.png" class="Clickanimation" alt="Order History Icon">
                    </a>
                </div>
            </div>
        </div>

        <!-- 中排：Product -->
        <div class="bottom-row">
            <div class="box-section">
                <h1>Product</h1>
                <div class="SPACE03">
                <a href="#" onclick="location.href='/a/Website_assignment/web/ZheYongPart/admin.php'">
                    <img src="Product_sample_icon_picture.png" class="Clickanimation" alt="Product Image">
                </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 底部 Logout 按钮 -->
    <div class="bottom-center">
        <a href="logout.php" class="logout-button">Logout</a>
    </div>

</body>
</html>
