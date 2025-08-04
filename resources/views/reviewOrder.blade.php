<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Order - Sip & Serve</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #2c2c2c;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background: #f5f1e8;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .header {
            display: flex;
            align-items: center;
            background: #f5f1e8;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        .logo {
            background: #8B4513;
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            margin-right: 20px;
        }

        .title {
            font-size: 28px;
            color: #333;
            font-weight: 300;
        }

        .order-items {
            background: white;
            padding: 0;
        }

        .order-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            gap: 15px;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 4px;
        }

        .item-modifier {
            font-size: 12px;
            color: #666;
            font-style: italic;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-right: 15px;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 2px solid #333;
            background: white;
            border-radius: 50%;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-btn:hover {
            background: #333;
            color: white;
        }

        .quantity-display {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            min-width: 20px;
            text-align: center;
        }

        .item-price {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-right: 15px;
        }

        .delete-btn {
            width: 35px;
            height: 35px;
            background: none;
            border: 2px solid #666;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 18px;
        }

        .delete-btn:hover {
            background: #ff4444;
            border-color: #ff4444;
            color: white;
        }

        .order-summary {
            background: #f5f1e8;
            padding: 30px 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .summary-label {
            color: #8B4513;
            font-weight: bold;
        }

        .summary-value {
            color: #333;
            font-weight: bold;
        }

        .total-row {
            border-top: 1px solid #ddd;
            padding-top: 8px;
            font-size: 16px;
            font-weight: bold;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-secondary {
            background: #ddd;
            color: #666;
        }

        .btn-secondary:hover {
            background: #ccc;
        }

        .btn-cancel {
            background: #8B4513;
            color: white;
        }

        .btn-cancel:hover {
            background: #7a3d10;
        }

        .btn-primary {
            background: #8B4513;
            color: white;
        }

        .btn-primary:hover {
            background: #7a3d10;
        }

        @media (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            
            .header {
                padding: 15px;
            }
            
            .title {
                font-size: 24px;
            }
            
            .order-item {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Sip & Serve</div>
            <h1 class="title">Review Order</h1>
        </div>

        <div class="order-items">
            <div class="order-item">
                <img src="https://images.unsplash.com/photo-1580933073521-dc49ac0d4e6a?w=60&h=60&fit=crop" alt="Espresso" class="item-image">
                <div class="item-details">
                    <div class="item-name">Espresso</div>
                    <div class="item-modifier">*milk foam</div>
                </div>
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="decreaseQuantity(1)">−</button>
                    <span class="quantity-display">2</span>
                    <button class="quantity-btn" onclick="increaseQuantity(1)">+</button>
                </div>
                <div class="item-price">PHP 320.00</div>
                <button class="delete-btn" onclick="removeItem(1)">🗑</button>
            </div>

            <div class="order-item">
                <img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=60&h=60&fit=crop" alt="Pad Thai" class="item-image">
                <div class="item-details">
                    <div class="item-name">Pad Thai</div>
                    <div class="item-modifier">*no prawns</div>
                </div>
                <div class="quantity-controls">
                    <span class="quantity-display">1</span>
                    <button class="quantity-btn" onclick="increaseQuantity(2)">+</button>
                </div>
                <div class="item-price">PHP 250.00</div>
                <button class="delete-btn" onclick="removeItem(2)">🗑</button>
            </div>
        </div>

        <div class="order-summary">
            <div class="summary-row">
                <span class="summary-label">Sub Total:</span>
                <span class="summary-value">PHP 570.00</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Discounts:</span>
                <span class="summary-value">PHP 0.00</span>
            </div>
            <div class="summary-row total-row">
                <span class="summary-label">TOTAL:</span>
                <span class="summary-value">PHP 570.00</span>
            </div>

            <div class="action-buttons">
                <button class="btn btn-secondary" onclick="backToMenu()">BACK TO MENU</button>
                <button class="btn btn-cancel" onclick="cancelOrder()">CANCEL ORDER</button>
                <button class="btn btn-primary" onclick="proceedToPay()">PAY</button>
            </div>
        </div>
    </div>

    <script>
        function decreaseQuantity(itemId) {
            // Implement decrease quantity logic
            console.log('Decrease quantity for item:', itemId);
        }

        function increaseQuantity(itemId) {
            // Implement increase quantity logic
            console.log('Increase quantity for item:', itemId);
        }

        function removeItem(itemId) {
            // Implement remove item logic
            console.log('Remove item:', itemId);
        }

        function backToMenu() {
            // Navigate back to menu
            window.location.href = '/menu';
        }

        function cancelOrder() {
            // Cancel order logic
            if (confirm('Are you sure you want to cancel this order?')) {
                window.location.href = '/menu';
            }
        }

        function proceedToPay() {
            // Navigate to payment
            window.location.href = '/payment';
        }
    </script>
</body>
</html>