<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>L'PRIMERO CAFE - Review Order</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f1e8;
            min-height: 100vh;
        }

        .kiosk-container {
            height: 100vh;
            display: flex;
            background: #f5f1e8;
            width: 100%;
            overflow: hidden;
        }

        /* CANCEL ORDER MODAL */
        .cancel-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(44, 24, 16, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease-out;
        }

        .cancel-modal-overlay.show {
            display: flex;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .cancel-modal-container {
            background: #ffffff;
            width: 500px;
            max-width: 90vw;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(44, 24, 16, 0.3);
            overflow: hidden;
            animation: slideIn 0.4s ease-out;
            border: 3px solid #d4c4a8;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .cancel-modal-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .cancel-modal-icon {
            font-size: 4rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .cancel-modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: 1px;
        }

        .cancel-modal-subtitle {
            font-size: 1rem;
            opacity: 0.95;
            font-weight: 400;
            line-height: 1.4;
        }

        .cancel-modal-content {
            padding: 40px 30px;
            text-align: center;
            background: #f5f1e8;
        }

        .cancel-warning-box {
            background: #fff3cd;
            border: 2px solid #ffeaa7;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .cancel-warning-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #856404;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cancel-warning-text {
            color: #856404;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .cancel-confirmation-text {
            font-size: 1.2rem;
            color: #2c1810;
            font-weight: 600;
            margin-bottom: 30px;
            line-height: 1.4;
        }

        .cancel-modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .cancel-modal-btn {
            flex: 1;
            max-width: 180px;
            padding: 18px 25px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .cancel-modal-btn-no {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
        }

        .cancel-modal-btn-no:hover {
            background: linear-gradient(135deg, #7a3d10, #8b4513);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.4);
        }

        .cancel-modal-btn-yes {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .cancel-modal-btn-yes:hover {
            background: linear-gradient(135deg, #c82333, #a71e2a);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        }

        .cancel-modal-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .cancel-modal-btn:hover::before {
            left: 100%;
        }

        /* Left Sidebar */
        .sidebar {
            flex: 0 0 200px;
            min-width: 200px;
            max-width: 200px;
            background: #F5E6D3;
            border-right: 5px solid #d4c4a8;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-header {
            padding: 40px 20px;
            text-align: center;
            border-bottom: 1px solid #d4c4a8;
        }

        .sidebar-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c1810;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f5f1e8;
        }

        .review-header {
            padding: 40px;
            text-align: center;
            background: #F5E6D3;
            border-bottom: 3px solid #d4c4a8;
        }

        .review-title {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 2px;
            margin: 0;
        }

        /* Order Items Section */
        .order-items-section {
            flex: 1;
            padding: 30px 40px;
            overflow-y: auto;
        }

        .order-item {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .order-item:hover {
            border-color: #d4c4a8;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .order-item-image {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            margin-right: 20px;
            background: #f0f0f0;
            border: 2px solid #e9ecef;
        }

        .order-item-details {
            flex: 1;
            min-width: 0;
        }

        .order-item-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c1810;
            margin-bottom: 6px;
            line-height: 1.2;
        }

        .order-item-addons {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 8px;
            line-height: 1.3;
            font-style: italic;
        }

        .order-item-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 8px 12px;
            border: 2px solid #e9ecef;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: #8b4513;
            color: white;
            border-radius: 50%;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background: #6d3410;
            transform: scale(1.1);
        }

        .quantity-display {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2c1810;
            min-width: 30px;
            text-align: center;
        }

        .order-item-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #8b4513;
            margin-left: 20px;
        }

        .order-item-remove {
            width: 40px;
            height: 40px;
            border: none;
            background: #dc3545;
            color: white;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 20px;
            transition: all 0.3s ease;
        }

        .order-item-remove:hover {
            background: #c82333;
            transform: scale(1.05);
        }

        /* Bottom Summary Section */
        .order-summary {
            background: white;
            border-top: 3px solid #d4c4a8;
            padding: 30px 40px;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.1);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            font-size: 1.1rem;
            color: #2c1810;
        }

        .summary-row.total {
            border-top: 2px solid #d4c4a8;
            margin-top: 15px;
            padding-top: 20px;
            font-size: 1.4rem;
            font-weight: 700;
            color: #8b4513;
        }

        .action-buttons {
            display: flex;
            gap: 20px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 18px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .btn-cancel {
            background: #dc3545;
            color: white;
        }

        .btn-cancel:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-pay {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-pay:hover {
            background: linear-gradient(135deg, #7a3d10, #8b4513);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.4);
        }

        .btn-pay::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-pay:hover::before {
            left: 100%;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #8b4513;
        }

        .empty-cart h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .empty-cart p {
            font-size: 1.1rem;
            margin-bottom: 25px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 150px !important;
                min-width: 150px;
                max-width: 150px;
            }

            .review-title {
                font-size: 2.2rem;
            }

            .order-items-section {
                padding: 20px;
            }

            .order-summary {
                padding: 20px;
            }

            .order-item {
                padding: 15px;
                flex-direction: column;
                text-align: center;
            }

            .order-item-image {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .order-item-controls {
                justify-content: center;
                margin-top: 15px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 15px;
            }

            .cancel-modal-container {
                width: 400px;
            }

            .cancel-modal-actions {
                flex-direction: column;
                gap: 12px;
            }

            .cancel-modal-btn {
                max-width: none;
            }
        }

        /* Lenovo Xiaoxin Pad 2024 11" Optimizations */
        @media (min-width: 1200px) and (max-width: 1920px) {
            .review-title {
                font-size: 3.5rem;
            }

            .order-item {
                padding: 25px;
            }

            .order-item-image {
                width: 90px;
                height: 90px;
            }

            .order-item-name {
                font-size: 1.3rem;
            }

            .order-item-price {
                font-size: 1.3rem;
            }

            .quantity-btn {
                width: 36px;
                height: 36px;
                font-size: 18px;
            }

            .btn {
                padding: 22px 35px;
                font-size: 1.2rem;
            }

            .summary-row {
                font-size: 1.2rem;
            }

            .summary-row.total {
                font-size: 1.5rem;
            }

            .cancel-modal-container {
                width: 600px;
            }

            .cancel-modal-title {
                font-size: 2rem;
            }

            .cancel-confirmation-text {
                font-size: 1.3rem;
            }

            .cancel-modal-btn {
                padding: 20px 30px;
                font-size: 1.1rem;
            }
        }

        /* Portrait mode optimization for tablet */
        @media (orientation: portrait) and (min-width: 768px) {
            .sidebar {
                width: 180px !important;
                min-width: 180px;
                max-width: 180px;
            }
        }
    </style>
</head>

<body>
    <div class="kiosk-container">
        <!-- Left Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">Sip & Serve</h2>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Review Header -->
            <header class="review-header">
                <h1 class="review-title">Review Order</h1>
            </header>

            <!-- Order Items Section -->
            <section class="order-items-section" id="orderItemsSection">
                @if(session('cart') && count(session('cart')) > 0)
                    @foreach(session('cart') as $index => $item)
                        <div class="order-item">
                            <img src="{{ $item['image'] ?? 'data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 80 80\'><rect width=\'80\' height=\'80\' fill=\'%23F5F5F5\'/><circle cx=\'40\' cy=\'40\' r=\'20\' fill=\'%238B4513\'/></svg>' }}" 
                                 alt="{{ $item['name'] }}" class="order-item-image">
                            
                            <div class="order-item-details">
                                <div class="order-item-name">{{ $item['name'] }}</div>
                                @if(isset($item['addons']) && count($item['addons']) > 0)
                                    <div class="order-item-addons">
                                        Add-ons: {{ implode(', ', array_column($item['addons'], 'name')) }}
                                    </div>
                                @endif
                                
                                <div class="order-item-controls">
                                    <div class="quantity-controls">
                                        <button class="quantity-btn" onclick="updateItemQuantity({{ $index }}, -1)">−</button>
                                        <span class="quantity-display">{{ $item['quantity'] }}</span>
                                        <button class="quantity-btn" onclick="updateItemQuantity({{ $index }}, 1)">+</button>
                                    </div>
                                    <div class="order-item-price">
                                        PHP {{ number_format(($item['price'] + ($item['addonsPrice'] ?? 0)) * $item['quantity'], 2) }}
                                    </div>
                                </div>
                            </div>

                            <button class="order-item-remove" onclick="removeItem({{ $index }})">
                                🗑️
                            </button>
                        </div>
                    @endforeach
                @else
                    <div class="empty-cart">
                        <h3>Your cart is empty</h3>
                        <p>Please add items to your cart before reviewing your order.</p>
                        <a href="{{ route('kiosk.main', ['orderType' => session('orderType', 'dine-in')]) }}" class="btn btn-back">
                            Back to Menu
                        </a>
                    </div>
                @endif
            </section>

            @if(session('cart') && count(session('cart')) > 0)
                <!-- Order Summary -->
                <footer class="order-summary">
                    @php
                        $cart = session('cart', []);
                        $subtotal = 0;
                        foreach($cart as $item) {
                            $subtotal += ($item['price'] + ($item['addonsPrice'] ?? 0)) * $item['quantity'];
                        }
                        $discounts = 0;
                        $total = $subtotal - $discounts;
                    @endphp

                    <div class="summary-row">
                        <span><strong>Sub Total:</strong></span>
                        <span id="subtotalAmount">PHP {{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    @if($discounts > 0)
                        <div class="summary-row">
                            <span><strong>Discounts:</strong></span>
                            <span id="discountAmount">PHP {{ number_format($discounts, 2) }}</span>
                        </div>
                    @endif
                    
                    <div class="summary-row total">
                        <span><strong>TOTAL:</strong></span>
                        <span id="totalAmount">PHP {{ number_format($total, 2) }}</span>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('kiosk.main', ['orderType' => session('orderType', 'dine-in')]) }}" class="btn btn-back">
                            Back to Menu
                        </a>
                        <button type="button" class="btn btn-cancel" onclick="showCancelModal()">
                            Cancel Order
                        </button>
                        <button type="button" class="btn btn-pay" onclick="proceedToPayment()">
                            PAY
                        </button>
                    </div>
                </footer>
            @endif
        </main>
    </div>

    <!-- Cancel Order Modal -->
    <div class="cancel-modal-overlay" id="cancelModal">
        <div class="cancel-modal-container">
            <div class="cancel-modal-header">
                <div class="cancel-modal-icon">⚠️</div>
                <h2 class="cancel-modal-title">Cancel Order?</h2>
                <p class="cancel-modal-subtitle">This action cannot be undone</p>
            </div>
            
            <div class="cancel-modal-content">
                <div class="cancel-warning-box">
                    <div class="cancel-warning-title">
                        <span>📋</span>
                        What will happen:
                    </div>
                    <div class="cancel-warning-text">
                        • All items will be removed from your cart<br>
                        • Your order will be completely deleted<br>
                        • You'll return to the main kiosk screen
                    </div>
                </div>
                
                <div class="cancel-confirmation-text">
                    Are you sure you want to cancel your entire order?
                </div>
                
                <div class="cancel-modal-actions">
                    <button class="cancel-modal-btn cancel-modal-btn-no" onclick="hideCancelModal()">
                        No, Keep Order
                    </button>
                    <button class="cancel-modal-btn cancel-modal-btn-yes" onclick="confirmCancelOrder()">
                        Yes, Cancel Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = @json(session('cart', []));

        function updateItemQuantity(index, change) {
            fetch('{{ route("kiosk.updateCartItem") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    index: index,
                    change: change
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating item quantity');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating item quantity');
            });
        }

        function removeItem(index) {
            if (confirm('Are you sure you want to remove this item?')) {
                fetch('{{ route("kiosk.removeCartItem") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        index: index
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error removing item');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing item');
                });
            }
        }

        // CANCEL ORDER MODAL FUNCTIONS
        function showCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function hideCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        function confirmCancelOrder() {
            fetch('{{ route("kiosk.cancelOrder") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route("kiosk.index") }}';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.href = '{{ route("kiosk.index") }}';
            });
        }

        // Close modal when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideCancelModal();
            }
        });

        function proceedToPayment() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }

            // Calculate totals
            const subtotal = cart.reduce((sum, item) => {
                return sum + ((item.price + (item.addonsPrice || 0)) * item.quantity);
            }, 0);

            const orderData = {
                order_type: '{{ session("orderType", "dine-in") }}',
                items: cart,
                subtotal: subtotal,
                tax_amount: 0,
                discount_amount: 0,
                total_amount: subtotal
            };

            fetch('{{ route("kiosk.processOrder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect_url || '{{ route("kiosk.orderConfirmation") }}';
                } else {
                    alert('Error processing order: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing order. Please try again.');
            });
        }

        // Add touch feedback for better UX
        document.querySelectorAll('.btn, .quantity-btn, .order-item-remove, .cancel-modal-btn').forEach(button => {
            button.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            button.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
    </script>
</body>

</html>