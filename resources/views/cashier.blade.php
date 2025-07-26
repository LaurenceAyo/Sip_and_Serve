<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            height: 100vh;
        }
        
        .header {
            background-color: #8B4513;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        
        .container {
            display: flex;
            height: calc(100vh - 80px);
        }
        
        .left-panel {
            flex: 1;
            background-color: #f5f5f5;
            padding: 20px;
            border-right: 2px solid #ddd;
        }
        
        .right-panel {
            flex: 1;
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        
        .order-card {
            background-color: white;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .order-header {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }
        
        .order-time {
            color: #666;
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            color: #333;
        }
        
        .item-name {
            flex: 1;
        }
        
        .item-price {
            font-weight: bold;
            color: #8B4513;
        }
        
        .order-total {
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            color: #333;
        }
        
        .select-order-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 10px;
            text-transform: uppercase;
        }
        
        .select-order-btn:hover {
            background-color: #0056b3;
        }
        
        .processing-section {
            background-color: white;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            color: #999;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .processing-section p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        CASHIER
    </div>
    
    <div class="container">
        <!-- Left Panel - Pending Cash Orders -->
        <div class="left-panel">
            <h2 class="section-title">Pending Cash Orders</h2>
            
            <!-- Order #0001 -->
            <div class="order-card">
                <div class="order-header">Order # 0001</div>
                <div class="order-time">Time: 8:30</div>
                
                <div class="order-item">
                    <span class="item-name">Pad Thai x1</span>
                    <span class="item-price">PHP 250.00</span>
                </div>
                <div class="order-item">
                    <span class="item-name">Cappuccino x1</span>
                    <span class="item-price">PHP 150.00</span>
                </div>
                
                <div class="order-total">
                    <span>Total:</span>
                    <span class="item-price">PHP 350.00</span>
                </div>
                
                <button class="select-order-btn">SELECT ORDER</button>
            </div>
            
            <!-- Order #0002 -->
            <div class="order-card">
                <div class="order-header">Order # 0002</div>
                <div class="order-time">Time: 7:45</div>
                
                <div class="order-item">
                    <span class="item-name">Pad Thai x1</span>
                    <span class="item-price">PHP 250.00</span>
                </div>
                <div class="order-item">
                    <span class="item-name">Cold Brew x3</span>
                    <span class="item-price">PHP 450.00</span>
                </div>
                
                <div class="order-total">
                    <span>Total:</span>
                    <span class="item-price">PHP 650.00</span>
                </div>
                
                <button class="select-order-btn">SELECT ORDER</button>
            </div>
            
            <!-- Order #0003 -->
            <div class="order-card">
                <div class="order-header">Order # 0003</div>
                <div class="order-time">Time: 10:20</div>
                
                <div class="order-item">
                    <span class="item-name">Pad Thai x1</span>
                    <span class="item-price">PHP 250.00</span>
                </div>
                
                <div class="order-total">
                    <span>Total:</span>
                    <span class="item-price">PHP 250.00</span>
                </div>
                
                <button class="select-order-btn">SELECT ORDER</button>
            </div>
        </div>
        
        <!-- Right Panel - Processing Order Section -->
        <div class="right-panel">
            <h2 class="section-title">Processing Order Section</h2>
            
            <div class="processing-section">
                <p>SELECT ORDERS</p>
                <p>FROM THE LEFT TO</p>
                <p>BEGIN</p>
                <p>PROCESSING PAYMENT</p>
            </div>
        </div>
    </div>
</body>
</html>