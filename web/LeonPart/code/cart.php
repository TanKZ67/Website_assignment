<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Devonpart/web/login");
    exit();
}

// Fetch cart items from database
$stmt = $conn->prepare("SELECT c.id as cart_id, c.*, p.name, p.price, p.main_image 
                       FROM cart c
                       JOIN products p ON c.product_id = p.id
                       WHERE c.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="\W1Demo\image\a7963aaa-618f-4c51-9f7e-e8699e81eed8.png">
    <title>Shopping Cart</title>
    <style>
        /* Add to your existing styles */
        #invoiceContent {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #eee;
            max-height: 400px;
            overflow-y: auto;
        }

        .invoice-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .invoice-header {
            font-weight: bold;
            margin-bottom: 15px;
        }

        .invoice-total {
            font-weight: bold;
            font-size: 1.2em;
            margin-top: 15px;
            text-align: right;
        }

        .cart-item button:disabled {
            opacity: 0.5;
            cursor: not-allowed !important;
            background-color: #ccc !important;
        }

        .modal-open .cart-item button {
            pointer-events: none;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1001;
            cursor: not-allowed;
        }

        body.modal-open {
            overflow: hidden;
        }

        .payment-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1002;
        }

        .payment-modal h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .payment-modal label {
            display: block;
            margin-top: 10px;
        }

        .payment-modal input {
            width: 100%;
            height: 30px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .payment-modal button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: blueviolet;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .payment-modal button:hover {
            background-color: #8a2be2;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
        }

        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 10px;
        }

        .cart-item span {
            flex-grow: 1;
        }

        .cart-item button {
            margin: 10px;
            border: none;
            color: white;
            border-radius: 3px;
            cursor: pointer;
            height: 50px;
            width: 50px;
        }

        .cart-item button[onclick*="changeCartQuantity"] {
            background-color: rgb(145, 133, 156);
        }

        .cart-item button[onclick*="changeCartQuantity"]:hover {
            background-color: #8a2be2;
        }

        .cart-item button[onclick*="removeFromCart"] {
            background-color: rgb(145, 133, 156);
        }

        .cart-item button[onclick*="removeFromCart"]:hover {
            background-color: red;
        }

        .total {
            font-size: 1.2em;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }

        .success-message {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1003;
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div id="modalOverlay" class="modal-overlay"></div>
    <div id="paymentModal" class="payment-modal">
        <button class="close-button" style="position:relative;margin-left:360px;margin-top: 0px;"
            onclick="closePaymentModal()">×</button>
        <h3>Payment Details</h3>
        <form id="paymentForm" action="process_payment.php" method="POST">
            <label for="payment_method">Payment Method:</label>
            <select id="payment_method" name="payment_method" required onchange="updatePaymentFields()">
                <option value="credit_card">Credit Card</option>
                <option value="paypal">PayPal</option>
                <option value="bank_transfer">Bank Transfer</option>
            </select><br>
            <div id="paymentFields">
                <div id="creditCardFields">
                    <label for="card_number">Card Number:</label>
                    <input type="text" id="card_number" name="card_number" required><br>
                    <label for="expiry_date">Expiry Date:</label>
                    <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" required><br>
                    <label for="cvv">CVV:</label>
                    <input type="text" id="cvv" name="cvv" required><br>
                </div>
            </div>
            <label for="total_amount">Total Amount:</label>
            <input type="text" id="total_amount" name="total_amount" readonly><br>
            <input type="hidden" id="order_details" name="order_details">
            <button type="submit">Pay Now</button>
        </form>
    </div>

    <div id="successMessage" class="success-message">
        Payment Successful!.
    </div>

    <!-- Add this right after the success-message div -->
    <div id="invoiceModal" class="payment-modal">
        <button class="close-button" style="position:relative;margin-left:360px;margin-top: 0px;"
            onclick="closeInvoiceModal()">×</button>
        <h3>Order Invoice</h3>
        <div id="invoiceContent"></div>
        <button onclick="printInvoice()">Print Invoice</button>
        <button onclick="closeInvoiceModal()">Close</button>
    </div>

    <h1>Shopping Cart</h1>
    <ul id="cartItems"></ul>
    <div class="total">Total: $<span id="cartTotal">0</span></div>
    <button onclick="window.location.href = 'index2.php'">Continue Shopping</button>
    <button id="proceedToPayment" onclick="openPaymentModal()" disabled>Proceed to Payment</button>

    <script>
        // Add these new functions
function displayInvoice(data) {
    const invoiceModal = document.getElementById('invoiceModal');
    const invoiceContent = document.getElementById('invoiceContent');
    
    // Format the invoice data
    let invoiceHTML = `
        <div class="invoice-header">
            <p>Order #: ${data.order_id}</p>
            <p>Date: ${new Date(data.order_date).toLocaleString()}</p>
            <p>Payment Method: ${data.payment_method}</p>
        </div>
        <div class="invoice-items">
            <h4>Items:</h4>
    `;
    
    data.items.forEach(item => {
        invoiceHTML += `
            <div class="invoice-item">
                <span>${item.name} (${item.quantity} × $${item.price})</span>
                <span>$${(item.quantity * item.price).toFixed(2)}</span>
            </div>
        `;
    });
    
    invoiceHTML += `
        <div class="invoice-total">
            <p>Total: $${data.total_amount}</p>
        </div>
    `;
    
    invoiceContent.innerHTML = invoiceHTML;
    invoiceModal.style.display = 'block';
}

function closeInvoiceModal() {
    document.getElementById('invoiceModal').style.display = 'none';
}

function printInvoice() {
    const invoiceContent = document.getElementById('invoiceContent').innerHTML;
    const originalContent = document.body.innerHTML;
    
    document.body.innerHTML = `
        <div style="width: 80%; margin: 0 auto; padding: 20px;">
            <h2 style="text-align: center;">Order Invoice</h2>
            ${invoiceContent}
        </div>
    `;
    
    window.print();
    document.body.innerHTML = originalContent;
    updateCartDisplay(); // Refresh the cart display after printing
}

// Modify the form submit handler to show the invoice
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!validateForm()) {
        return;
    }

    const formData = new FormData(this);

    fetch('process_payment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('paymentModal').style.display = 'none';
                restoreUIAfterPayment();
                
                // Clear the cart
                cartItems = [];
                updateCartDisplay();
                
                // Display the invoice instead of the simple success message
                displayInvoice(data.invoice);
            } else {
                alert(data.message || "Payment failed");
                restoreUIAfterPayment();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Payment processing error");
            restoreUIAfterPayment();
        });
});
        // 全局变量存储购物车项
        let cartItems = <?php echo json_encode($cartItems); ?>;

        // 页面加载时初始化购物车显示
        document.addEventListener('DOMContentLoaded', function() {
            updateCartDisplay();
        });

        // 更新购物车显示
        function updateCartDisplay() {
            const cartList = document.getElementById("cartItems");
            cartList.innerHTML = "";
            let total = 0;

            cartItems.forEach((item) => {
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
            document.getElementById("proceedToPayment").disabled = cartItems.length === 0;
        }

        // 修改购物车商品数量
        function changeCartQuantity(cartId, change) {
            fetch('update_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        cart_id: cartId,
                        change: change
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 更新本地购物车数据
                        const itemIndex = cartItems.findIndex(item => item.cart_id == cartId);
                        if (itemIndex !== -1) {
                            cartItems[itemIndex].quantity = data.newQuantity;
                            updateCartDisplay();
                        }
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to update cart');
                });
        }

        // 从购物车移除商品
        function removeFromCart(cartId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('remove_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            cart_id: cartId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // 从本地购物车数据中移除
                            cartItems = cartItems.filter(item => item.cart_id != cartId);
                            updateCartDisplay();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to remove item');
                    });
            }
        }

        // 打开支付模态框
        function openPaymentModal() {
            const paymentModal = document.getElementById('paymentModal');
            const overlay = document.getElementById('modalOverlay');
            const totalAmount = document.getElementById('cartTotal').textContent;

            // 准备订单数据
            const orderData = cartItems.map(item => ({
                product_id: item.product_id,
                name: item.name,
                price: item.price,
                quantity: item.quantity,
                size: item.size,
                color: item.color
            }));

            document.getElementById('total_amount').value = totalAmount;
            document.getElementById('order_details').value = JSON.stringify(orderData);

            // 显示模态框和遮罩层
            paymentModal.style.display = 'block';
            overlay.style.display = 'block';
            document.body.classList.add('modal-open');

            // 禁用所有购物车按钮
            disableCartButtons(true);
        }
        // 关闭支付模态框
        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
            restoreUIAfterPayment(); // 使用统一的恢复函数
        }

        // 禁用/启用购物车按钮函数
        function disableCartButtons(disabled) {
            const buttons = document.querySelectorAll('.cart-item button');
            buttons.forEach(button => {
                button.disabled = disabled;
                if (disabled) {
                    button.style.opacity = '0.5';
                    button.style.cursor = 'not-allowed';
                } else {
                    button.style.opacity = '1';
                    button.style.cursor = 'pointer';
                }
            });

            // 也禁用继续购物和支付按钮
            document.querySelector('button[onclick="window.location.href = \'index.php\'"]').disabled = disabled;
            document.getElementById('proceedToPayment').disabled = disabled;
        }

        // 根据支付方式更新表单字段
        function updatePaymentFields() {
            const paymentMethod = document.getElementById('payment_method').value;
            const paymentFields = document.getElementById('paymentFields');

            paymentFields.innerHTML = '';

            if (paymentMethod === 'credit_card') {
                paymentFields.innerHTML = `
                    <div id="creditCardFields">
                        <label for="card_number">Card Number:</label>
                        <input type="text" id="card_number" name="card_number" required><br>
                        <label for="expiry_date">Expiry Date:</label>
                        <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" required><br>
                        <label for="cvv">CVV:</label>
                        <input type="text" id="cvv" name="cvv" required><br>
                    </div>`;
            } else if (paymentMethod === 'paypal') {
                paymentFields.innerHTML = `
                    <div id="paypalFields">
                        <label for="paypal_email">PayPal Email:</label>
                        <input type="email" id="paypal_email" name="paypal_email" required><br>
                    </div>`;
            } else if (paymentMethod === 'bank_transfer') {
                paymentFields.innerHTML = `
                    <div id="bankTransferFields">
                        <label for="bank_name">Bank Name:</label>
                        <input type="text" id="bank_name" name="bank_name" required><br>
                        <label for="account_number">Account Number:</label>
                        <input type="text" id="account_number" name="account_number" required><br>
                        <label for="routing_number">Routing Number:</label>
                        <input type="text" id="routing_number" name="routing_number" required><br>
                    </div>`;
            }
        }

        // 初始化支付表单
        updatePaymentFields();

        // 验证信用卡信息
        function validateCreditCard(cardNumber, expiryDate, cvv) {
            const cardNumberRegex = /^\d{16}$/;
            if (!cardNumberRegex.test(cardNumber)) {
                alert("Invalid card number. Please enter a 16-digit card number.");
                return false;
            }

            const expiryDateRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
            if (!expiryDateRegex.test(expiryDate)) {
                alert("Invalid expiry date. Please use the format MM/YY.");
                return false;
            }

            const cvvRegex = /^\d{3,4}$/;
            if (!cvvRegex.test(cvv)) {
                alert("Invalid CVV. Please enter a 3 or 4-digit CVV.");
                return false;
            }

            return true;
        }

        // 验证表单
        function validateForm() {
            const paymentMethod = document.getElementById('payment_method').value;

            if (paymentMethod === 'credit_card') {
                const cardNumber = document.getElementById('card_number').value;
                const expiryDate = document.getElementById('expiry_date').value;
                const cvv = document.getElementById('cvv').value;
                return validateCreditCard(cardNumber, expiryDate, cvv);
            } else if (paymentMethod === 'paypal') {
                const email = document.getElementById('paypal_email').value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert("Invalid PayPal email. Please enter a valid email address.");
                    return false;
                }
                return true;
            } else if (paymentMethod === 'bank_transfer') {
                const bankName = document.getElementById('bank_name').value;
                const accountNumber = document.getElementById('account_number').value;
                const routingNumber = document.getElementById('routing_number').value;

                if (!bankName.trim()) {
                    alert("Bank name cannot be empty.");
                    return false;
                }
                if (!/^\d{6,}$/.test(accountNumber)) {
                    alert("Invalid account number. Please enter at least 6 digits.");
                    return false;
                }
                if (!/^\d{9}$/.test(routingNumber)) {
                    alert("Invalid routing number. Please enter a 9-digit routing number.");
                    return false;
                }
                return true;
            }

            return true;
        }

        // 处理表单提交
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateForm()) {
                return;
            }

            const formData = new FormData(this);

            fetch('process_payment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('successMessage').style.display = 'block';
                        document.getElementById('paymentModal').style.display = 'none';

                        // 支付成功后恢复界面交互
                        restoreUIAfterPayment();

                        // 清空购物车
                        cartItems = [];
                        updateCartDisplay();

                        setTimeout(() => {
                            document.getElementById('successMessage').style.display = 'none';
                        }, 3000);
                    } else {
                        alert(data.message || "Payment failed");
                        // 支付失败也恢复界面交互
                        restoreUIAfterPayment();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Payment processing error");
                    // 出错时也恢复界面交互
                    restoreUIAfterPayment();
                });
        });

        // 新增函数：支付后恢复UI
        function restoreUIAfterPayment() {
            const overlay = document.getElementById('modalOverlay');
            overlay.style.display = 'none';
            document.body.classList.remove('modal-open');
            disableCartButtons(false);
        }
    </script>
</body>

</html>