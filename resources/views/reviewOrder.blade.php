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
            margin-top: -900px;
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
            display: none; /* Hidden by default, can be shown for debugging */
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
                <span><strong>Order Type:</strong> <span id="orderType">Dine In</span></span>
                <span><strong>Items:</strong> <span id="itemCount">0</span></span>
            </div>
            <div class="order-meta-row">
                <span><strong>Order Time:</strong> <span id="orderTime"></span></span>
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
        // Sample order data - replace with actual data from your backend
        let orderData = {
            items: [
                {
                    id: 1,
                    name: "Coffee Item",
                    unitPrice: 95.00,
                    quantity: 1,
                    modifiers: [],
                    image: "https://images.unsplash.com/photo-1580933073521-dc49ac0d4e6a?w=60&h=60&fit=crop"
                }
            ],
            orderType: "Dine In",
            tableNumber: "A5",
            serviceChargeRate: 0, // 0% service charge - NO HIDDEN FEES
            taxRate: 0, // 0% tax - NO HIDDEN FEES
            discountAmount: 0,
            // Additional fee controls
            deliveryFee: 0,
            processingFee: 0,
            convenienceFee: 0
        };

        // Configuration
        const config = {
            enableDebug: true, // Set to true to show calculation debug info
            minQuantity: 1,
            maxQuantity: 99,
            currency: 'PHP',
            // STRICT PRICING CONTROLS
            enforceExactPricing: true, // Prevents any price modifications
            allowTax: false, // Explicitly disable tax
            allowServiceCharge: false, // Explicitly disable service charge
            allowProcessingFees: false // Explicitly disable processing fees
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
            itemDiv.setAttribute('data-item-id', item.id);

            const totalPrice = item.unitPrice * item.quantity;
            const modifiersText = item.modifiers && item.modifiers.length > 0 
                ? `*${item.modifiers.join(', ')}` 
                : '';

            itemDiv.innerHTML = `
                <img src="${item.image}" alt="${item.name}" class="item-image">
                <div class="item-details">
                    <div class="item-name">${item.name}</div>
                    ${modifiersText ? `<div class="item-modifier">${modifiersText}</div>` : ''}
                    <div class="item-unit-price">${config.currency} ${item.unitPrice.toFixed(2)} each</div>
                </div>
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="decreaseQuantity(${item.id})" ${item.quantity <= config.minQuantity ? 'disabled' : ''}>−</button>
                    <span class="quantity-display">${item.quantity}</span>
                    <button class="quantity-btn" onclick="increaseQuantity(${item.id})" ${item.quantity >= config.maxQuantity ? 'disabled' : ''}>+</button>
                </div>
                <div class="item-price">${config.currency} ${totalPrice.toFixed(2)}</div>
                <button class="delete-btn" onclick="removeItem(${item.id})" title="Remove item">🗑</button>
            `;

            return itemDiv;
        }

        function findItemById(itemId) {
            return orderData.items.find(item => item.id === itemId);
        }

        function findItemIndexById(itemId) {
            return orderData.items.findIndex(item => item.id === itemId);
        }

        function decreaseQuantity(itemId) {
            const item = findItemById(itemId);
            if (!item || item.quantity <= config.minQuantity) return;

            item.quantity--;
            
            // Re-render the specific item
            const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
            if (itemElement) {
                const newElement = createOrderItemElement(item);
                itemElement.replaceWith(newElement);
            }

            updateItemCount();
            calculateAndUpdateTotals();
            console.log(`Decreased quantity for item ${itemId}. New quantity: ${item.quantity}`);
        }

        function increaseQuantity(itemId) {
            const item = findItemById(itemId);
            if (!item || item.quantity >= config.maxQuantity) return;

            item.quantity++;
            
            // Re-render the specific item
            const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
            if (itemElement) {
                const newElement = createOrderItemElement(item);
                itemElement.replaceWith(newElement);
            }

            updateItemCount();
            calculateAndUpdateTotals();
            console.log(`Increased quantity for item ${itemId}. New quantity: ${item.quantity}`);
        }

        function removeItem(itemId) {
            const itemIndex = findItemIndexById(itemId);
            if (itemIndex === -1) return;

            const item = orderData.items[itemIndex];
            
            if (confirm(`Remove "${item.name}" from your order?`)) {
                // Remove item with animation
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
                        console.log(`Removed item ${itemId} from order`);
                    }, 300);
                } else {
                    // Fallback if element not found
                    orderData.items.splice(itemIndex, 1);
                    renderOrderItems();
                    updateItemCount();
                    calculateAndUpdateTotals();
                }
            }
        }

        function calculateAndUpdateTotals() {
            // Calculate subtotal (sum of all item totals) - THIS IS THE EXACT AMOUNT CUSTOMER PAYS
            const subtotal = orderData.items.reduce((sum, item) => {
                return sum + (item.unitPrice * item.quantity);
            }, 0);

            // IMPORTANT: Set all additional fees to ZERO to prevent price inflation
            const serviceCharge = 0; // Force to 0 - no service charge
            const tax = 0; // Force to 0 - no tax
            const deliveryFee = 0; // Force to 0 - no delivery fee
            const processingFee = 0; // Force to 0 - no processing fee
            const convenienceFee = 0; // Force to 0 - no convenience fee

            // Total should EXACTLY equal subtotal (sum of item prices)
            const total = subtotal - orderData.discountAmount;

            // Ensure total never goes below 0
            const finalTotal = Math.max(0, total);

            // Update display
            document.getElementById('subtotal').textContent = `${config.currency} ${subtotal.toFixed(2)}`;
            document.getElementById('serviceCharge').textContent = `${config.currency} ${serviceCharge.toFixed(2)}`;
            document.getElementById('tax').textContent = `${config.currency} ${tax.toFixed(2)}`;
            document.getElementById('discounts').textContent = `${config.currency} ${orderData.discountAmount.toFixed(2)}`;
            document.getElementById('total').textContent = `${config.currency} ${finalTotal.toFixed(2)}`;

            // Update pay button with EXACT amount
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

            // Show debug information if enabled
            if (config.enableDebug) {
                showCalculationDebug(subtotal, serviceCharge, tax, finalTotal);
            }

            // Log exact calculation for verification
            console.log('EXACT TOTAL CALCULATION:', {
                itemTotals: orderData.items.map(item => `${item.name}: ${item.quantity} × ${config.currency}${item.unitPrice} = ${config.currency}${(item.unitPrice * item.quantity).toFixed(2)}`),
                subtotal: `${config.currency}${subtotal.toFixed(2)}`,
                serviceCharge: `${config.currency}${serviceCharge.toFixed(2)} (FORCED TO 0)`,
                tax: `${config.currency}${tax.toFixed(2)} (FORCED TO 0)`,
                discounts: `${config.currency}${orderData.discountAmount.toFixed(2)}`,
                finalTotal: `${config.currency}${finalTotal.toFixed(2)}`,
                verification: `${finalTotal.toFixed(2)} should equal sum of item prices minus discounts`
            });

            // Store the exact total for payment processing
            orderData.calculatedTotal = finalTotal;
        }

        function showCalculationDebug(subtotal, serviceCharge, tax, total) {
            const debugElement = document.getElementById('calculationDebug');
            debugElement.style.display = 'block';
            debugElement.innerHTML = `
                <strong>Calculation Debug:</strong><br>
                Items: ${orderData.items.map(item => `${item.name} (${item.quantity} × ${item.unitPrice})`).join(', ')}<br>
                Subtotal: ${subtotal.toFixed(2)}<br>
                Service (${orderData.serviceChargeRate}%): ${serviceCharge.toFixed(2)}<br>
                Tax (${orderData.taxRate}%): ${tax.toFixed(2)}<br>
                Discount: ${orderData.discountAmount.toFixed(2)}<br>
                <strong>Total: ${total.toFixed(2)}</strong>
            `;
        }

        function backToMenu() {
            // Navigate back to menu
            if (orderData.items.length > 0) {
                if (confirm('Go back to menu? Your current order will be saved.')) {
                    window.location.href = '/menu';
                }
            } else {
                window.location.href = '/menu';
            }
        }

        function cancelOrder() {
            // Cancel order logic
            const itemCount = orderData.items.length;
            const message = itemCount > 0 
                ? `Cancel your order with ${itemCount} item${itemCount > 1 ? 's' : ''}? This action cannot be undone.`
                : 'Cancel your order?';
                
            if (confirm(message)) {
                // Clear order data
                orderData.items = [];
                localStorage.removeItem('currentOrder'); // Clear any saved order
                
                // Redirect to menu or home
                window.location.href = '/menu';
            }
        }

        function proceedToPay() {
            if (orderData.items.length === 0) {
                alert('Please add items to your order before proceeding to payment.');
                return;
            }

            // Calculate EXACT final total - NO HIDDEN FEES
            const subtotal = orderData.items.reduce((sum, item) => sum + (item.unitPrice * item.quantity), 0);
            const finalTotal = Math.max(0, subtotal - orderData.discountAmount);

            // Create order summary with EXACT amounts
            const orderSummary = {
                ...orderData,
                calculations: {
                    subtotal: subtotal,
                    serviceCharge: 0, // EXPLICITLY 0
                    tax: 0, // EXPLICITLY 0
                    discounts: orderData.discountAmount,
                    total: finalTotal, // EXACT AMOUNT TO CHARGE
                    // Additional verification
                    itemBreakdown: orderData.items.map(item => ({
                        name: item.name,
                        unitPrice: item.unitPrice,
                        quantity: item.quantity,
                        lineTotal: item.unitPrice * item.quantity
                    }))
                },
                timestamp: new Date().toISOString(),
                // CRITICAL: Store exact total for payment processing
                exactPaymentAmount: finalTotal,
                // Flag to prevent any additional fees being added later
                noAdditionalFees: true,
                paymentInstructions: {
                    exactAmount: finalTotal,
                    currency: config.currency,
                    noTax: true,
                    noServiceCharge: true,
                    noProcessingFee: true
                }
            };

            // Save to localStorage
            localStorage.setItem('orderForPayment', JSON.stringify(orderSummary));
            
            // Log for verification
            console.log('🔥 PROCEEDING TO PAYMENT - EXACT AMOUNT:', {
                displayedTotal: `${config.currency} ${finalTotal.toFixed(2)}`,
                itemsTotal: `${config.currency} ${subtotal.toFixed(2)}`,
                discounts: `${config.currency} ${orderData.discountAmount.toFixed(2)}`,
                exactPaymentAmount: `${config.currency} ${finalTotal.toFixed(2)}`,
                warning: 'Payment system MUST charge exactly this amount - NO additional fees!'
            });
            
            // Alert to confirm exact amount before proceeding
            const confirmMessage = `Proceed to payment for exactly ${config.currency} ${finalTotal.toFixed(2)}?\n\nThis is the final amount with no additional fees.`;
            
            if (confirm(confirmMessage)) {
                // Navigate to payment with exact amount
                window.location.href = `/payment?amount=${finalTotal.toFixed(2)}&currency=${config.currency}&no_fees=true`;
            }
        }

        // Auto-save order data periodically
        function saveOrderData() {
            if (orderData.items.length > 0) {
                localStorage.setItem('currentOrder', JSON.stringify(orderData));
            } else {
                localStorage.removeItem('currentOrder');
            }
        }

        // Load order data from localStorage if available
        function loadOrderData() {
            const savedOrder = localStorage.getItem('currentOrder');
            if (savedOrder) {
                try {
                    const parsedOrder = JSON.parse(savedOrder);
                    // Merge with default structure
                    orderData = {
                        ...orderData,
                        ...parsedOrder
                    };
                } catch (error) {
                    console.error('Error loading saved order:', error);
                }
            }
        }

        // Initialize page when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            loadOrderData();
            initializePage();
            
            // Auto-save every 30 seconds
            setInterval(saveOrderData, 30000);
        });

        // Save order data before page unload
        window.addEventListener('beforeunload', saveOrderData);

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                backToMenu();
            } else if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
                proceedToPay();
            }
        });

        // Debug function - can be called from browser console
        window.debugOrder = function() {
            config.enableDebug = !config.enableDebug;
            calculateAndUpdateTotals();
            console.log('Debug mode:', config.enableDebug ? 'enabled' : 'disabled');
        };
    </script>
</body>
</html>