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

        .order-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .item-details {
            flex: 1;
            min-width: 0;
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

        .item-unit-price {
            font-size: 11px;
            color: #888;
            margin-top: 2px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-right: 15px;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border: 2px solid #8B4513;
            background: white;
            border-radius: 50%;
            font-size: 16px;
            font-weight: bold;
            color: #8B4513;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .quantity-btn:hover {
            background: #8B4513;
            color: white;
        }

        .quantity-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            border-color: #ccc;
            color: #ccc;
        }

        .quantity-btn:disabled:hover {
            background: white;
            color: #ccc;
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
            color: #8B4513;
            margin-right: 15px;
            min-width: 80px;
            text-align: right;
        }

        .delete-btn {
            width: 35px;
            height: 35px;
            background: none;
            border: 2px solid #666;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 16px;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .delete-btn:hover {
            background: #ff4444;
            border-color: #ff4444;
            color: white;
        }

        .order-summary {
            background: #f5f1e8;
            padding: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 7px;
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
            border-top: 2px solid #8B4513;
            padding-top: 12px;
            margin-top: 12px;
            font-size: 18px;
            font-weight: bold;
        }

        .total-row .summary-label {
            font-size: 18px;
        }

        .total-row .summary-value {
            font-size: 18px;
            color: #8B4513;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 15px 10px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-secondary {
            background: #ddd;
            color: #666;
            border: 2px solid #ddd;
        }

        .btn-secondary:hover {
            background: #ccc;
            border-color: #ccc;
        }

        .btn-cancel {
            background: #ff4444;
            color: white;
            border: 2px solid #ff4444;
        }

        .btn-cancel:hover {
            background: #cc3333;
            border-color: #cc3333;
        }

        .btn-primary {
            background: #8B4513;
            color: white;
            border: 2px solid #8B4513;
        }

        .btn-primary:hover {
            background: #7a3d10;
            border-color: #7a3d10;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            background: white;
        }

        .empty-cart-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-cart-message {
            color: #666;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .empty-cart-submessage {
            color: #999;
            font-size: 14px;
        }

        .order-meta {
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
            font-size: 14px;
            color: #666;
        }

        .order-meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .order-meta-row:last-child {
            margin-bottom: 0;
        }

        .calculation-debug {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 12px;
            color: #856404;
            display: none;
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
                gap: 10px;
            }

            .quantity-controls {
                gap: 8px;
                margin-right: 10px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .btn {
                flex: none;
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

        <div class="order-meta" id="orderMeta">
            <div class="order-meta-row">
                <span><strong>Order Type:</strong> <span id="orderType">{{ $orderType ?? 'Dine In' }}</span></span>
                <span><strong>Items:</strong> <span id="itemCount">{{ count($cart ?? []) }}</span></span>
            </div>
            <div class="order-meta-row">
                <span><strong>Order Time:</strong> <span id="orderTime">{{ now()->format('h:i A') }}</span></span>
                <span><strong>Table:</strong> <span id="tableNumber">-</span></span>
            </div>
        </div>

        <div class="order-items" id="orderItems">
            <!-- Order items will be populated here -->
        </div>

        <div class="order-summary">
            <div class="calculation-debug" id="calculationDebug">
                <!-- Debug information will be shown here -->
            </div>

            <div class="summary-row">
                <span class="summary-label">Subtotal (Items Only):</span>
                <span class="summary-value" id="subtotal">PHP 0.00</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Service Charge (DISABLED):</span>
                <span class="summary-value" id="serviceCharge">PHP 0.00</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Tax (DISABLED):</span>
                <span class="summary-value" id="tax">PHP 0.00</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Discounts:</span>
                <span class="summary-value" id="discounts">PHP 0.00</span>
            </div>
            <div class="summary-row total-row">
                <span class="summary-label">FINAL TOTAL (EXACT):</span>
                <span class="summary-value" id="total">PHP 0.00</span>
            </div>

            <div class="action-buttons">
                <button class="btn btn-secondary" onclick="backToMenu()">Back to Menu</button>
                <button class="btn btn-cancel" onclick="cancelOrder()">Cancel Order</button>
                <button class="btn btn-primary" onclick="proceedToPay()" id="payButton">Pay</button>
            </div>
        </div>
    </div>

    <script>
        // Use real cart data from controller
        let orderData = {
            items: @json($cart ?? []),
            orderType: "{{ $orderType ?? 'Dine In' }}",
            tableNumber: "A5",
            serviceChargeRate: 0,
            taxRate: 0,
            discountAmount: 0,
            deliveryFee: 0,
            processingFee: 0,
            convenienceFee: 0
        };

        const config = {
            enableDebug: false,
            minQuantity: 1,
            maxQuantity: 99,
            currency: 'PHP',
            enforceExactPricing: true,
            allowTax: false,
            allowServiceCharge: false,
            allowProcessingFees: false
        };

        function initializePage() {
            updateOrderMeta();
            renderOrderItems();
            calculateAndUpdateTotals();
            updateOrderTime();
        }

        function updateOrderMeta() {
            document.getElementById('orderType').textContent = orderData.orderType;
            document.getElementById('tableNumber').textContent = orderData.tableNumber || '-';
            updateItemCount();
        }

        function updateItemCount() {
            const totalItems = orderData.items.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('itemCount').textContent = totalItems;
        }

        function updateOrderTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
            });
            document.getElementById('orderTime').textContent = timeString;
        }

        function renderOrderItems() {
            const container = document.getElementById('orderItems');
            
            if (orderData.items.length === 0) {
                container.innerHTML = `
                    <div class="empty-cart">
                        <div class="empty-cart-icon">🛒</div>
                        <div class="empty-cart-message">Your cart is empty</div>
                        <div class="empty-cart-submessage">Add some delicious items to get started!</div>
                    </div>
                `;
                return;
            }

            container.innerHTML = '';
            
            orderData.items.forEach(item => {
                const itemElement = createOrderItemElement(item);
                container.appendChild(itemElement);
            });
        }

        function createOrderItemElement(item) {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'order-item';
            itemDiv.setAttribute('data-item-id', item.id || item.menu_item_id);

            const unitPrice = parseFloat(item.price || item.unitPrice || 0);
            const quantity = parseInt(item.quantity || 1);
            const totalPrice = unitPrice * quantity;
            
            const modifiersText = item.modifiers && item.modifiers.length > 0 
                ? `*${item.modifiers.join(', ')}` 
                : '';

            const defaultImage = "https://images.unsplash.com/photo-1580933073521-dc49ac0d4e6a?w=60&h=60&fit=crop";
            const itemImage = item.image || defaultImage;

            itemDiv.innerHTML = `
                <img src="${itemImage}" alt="${item.name}" class="item-image">
                <div class="item-details">
                    <div class="item-name">${item.name}</div>
                    ${modifiersText ? `<div class="item-modifier">${modifiersText}</div>` : ''}
                    <div class="item-unit-price">${config.currency} ${unitPrice.toFixed(2)} each</div>
                </div>
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="decreaseQuantity(${item.id || item.menu_item_id})" ${quantity <= config.minQuantity ? 'disabled' : ''}>−</button>
                    <span class="quantity-display">${quantity}</span>
                    <button class="quantity-btn" onclick="increaseQuantity(${item.id || item.menu_item_id})" ${quantity >= config.maxQuantity ? 'disabled' : ''}>+</button>
                </div>
                <div class="item-price">${config.currency} ${totalPrice.toFixed(2)}</div>
                <button class="delete-btn" onclick="removeItem(${item.id || item.menu_item_id})" title="Remove item">🗑</button>
            `;

            return itemDiv;
        }

        function findItemById(itemId) {
            return orderData.items.find(item => (item.id || item.menu_item_id) === itemId);
        }

        function findItemIndexById(itemId) {
            return orderData.items.findIndex(item => (item.id || item.menu_item_id) === itemId);
        }

        function decreaseQuantity(itemId) {
            const item = findItemById(itemId);
            if (!item || item.quantity <= config.minQuantity) return;

            item.quantity--;
            
            const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
            if (itemElement) {
                const newElement = createOrderItemElement(item);
                itemElement.replaceWith(newElement);
            }

            updateItemCount();
            calculateAndUpdateTotals();
        }

        function increaseQuantity(itemId) {
            const item = findItemById(itemId);
            if (!item || item.quantity >= config.maxQuantity) return;

            item.quantity++;
            
            const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
            if (itemElement) {
                const newElement = createOrderItemElement(item);
                itemElement.replaceWith(newElement);
            }

            updateItemCount();
            calculateAndUpdateTotals();
        }

        function removeItem(itemId) {
            const itemIndex = findItemIndexById(itemId);
            if (itemIndex === -1) return;

            const item = orderData.items[itemIndex];
            
            if (confirm(`Remove "${item.name}" from your order?`)) {
                const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
                if (itemElement) {
                    itemElement.style.transition = 'all 0.3s ease';
                    itemElement.style.opacity = '0';
                    itemElement.style.transform = 'translateX(-100%)';
                    
                    setTimeout(() => {
                        orderData.items.splice(itemIndex, 1);
                        renderOrderItems();
                        updateItemCount();
                        calculateAndUpdateTotals();
                    }, 300);
                } else {
                    orderData.items.splice(itemIndex, 1);
                    renderOrderItems();
                    updateItemCount();
                    calculateAndUpdateTotals();
                }
            }
        }

        function calculateAndUpdateTotals() {
            const subtotal = orderData.items.reduce((sum, item) => {
                const unitPrice = parseFloat(item.price || item.unitPrice || 0);
                const quantity = parseInt(item.quantity || 1);
                return sum + (unitPrice * quantity);
            }, 0);

            const serviceCharge = 0;
            const tax = 0;
            const total = subtotal - orderData.discountAmount;
            const finalTotal = Math.max(0, total);

            document.getElementById('subtotal').textContent = `${config.currency} ${subtotal.toFixed(2)}`;
            document.getElementById('serviceCharge').textContent = `${config.currency} ${serviceCharge.toFixed(2)}`;
            document.getElementById('tax').textContent = `${config.currency} ${tax.toFixed(2)}`;
            document.getElementById('discounts').textContent = `${config.currency} ${orderData.discountAmount.toFixed(2)}`;
            document.getElementById('total').textContent = `${config.currency} ${finalTotal.toFixed(2)}`;

            const payButton = document.getElementById('payButton');
            if (orderData.items.length === 0) {
                payButton.textContent = 'Add Items';
                payButton.onclick = backToMenu;
                payButton.className = 'btn btn-secondary';
            } else {
                payButton.textContent = `Pay ${config.currency} ${finalTotal.toFixed(2)}`;
                payButton.onclick = proceedToPay;
                payButton.className = 'btn btn-primary';
            }

            orderData.calculatedTotal = finalTotal;
        }

        function backToMenu() {
            if (orderData.items.length > 0) {
                if (confirm('Go back to menu? Your current order will be saved.')) {
                    window.location.href = '{{ route("kiosk.main") }}';
                }
            } else {
                window.location.href = '{{ route("kiosk.main") }}';
            }
        }

        function cancelOrder() {
            const itemCount = orderData.items.length;
            const message = itemCount > 0 
                ? `Cancel your order with ${itemCount} item${itemCount > 1 ? 's' : ''}? This action cannot be undone.`
                : 'Cancel your order?';
                
            if (confirm(message)) {
                orderData.items = [];
                window.location.href = '{{ route("kiosk.main") }}';
            }
        }

        function proceedToPay() {
            if (orderData.items.length === 0) {
                alert('Please add items to your order before proceeding to payment.');
                return;
            }

            const subtotal = orderData.items.reduce((sum, item) => {
                const unitPrice = parseFloat(item.price || item.unitPrice || 0);
                const quantity = parseInt(item.quantity || 1);
                return sum + (unitPrice * quantity);
            }, 0);
            
            const finalTotal = Math.max(0, subtotal - orderData.discountAmount);

            const confirmMessage = `Proceed to payment for exactly ${config.currency} ${finalTotal.toFixed(2)}?\n\nThis is the final amount with no additional fees.`;
            
            if (confirm(confirmMessage)) {
                // Navigate to QR payment
                const itemsParam = encodeURIComponent(JSON.stringify(orderData.items.map(item => ({
                    menu_item_id: item.menu_item_id || item.id,
                    quantity: item.quantity,
                    special_instructions: item.modifiers?.join(', ') || null
                }))));
                
                window.location.href = `/qr/payment/new?type=${orderData.orderType.toLowerCase().replace(' ', '-')}&items=${itemsParam}`;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Cart data received:', orderData);
            initializePage();
        });
    </script>
</body>
</html>