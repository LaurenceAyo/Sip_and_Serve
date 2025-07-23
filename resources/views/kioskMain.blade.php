<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L'PRIMERO CAFE - Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
        }

        /* Left Sidebar */
        .sidebar {
            width: 250px;
            background: #F5E6D3;
            border-right: 5px solid #d4c4a8;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #d4c4a8;
        }

        .sidebar-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c1810;
        }

        .category-list {
            flex: 1;
            padding: 20px 0;
        }

        .category-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px 20px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 5px 15px;
        }

        .category-item:hover {
            background: rgba(44, 24, 16, 0.1);
        }

        .category-item.active {
            background: rgba(44, 24, 16, 0.15);
            border-left: 4px solid #8b4513;
        }

        .category-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            margin-bottom: 8px;
            object-fit: cover;
            border: 2px solid #d4c4a8;
        }

        .category-name {
            font-size: 0.9rem;
            font-weight: 500;
            color: #2c1810;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .menu-header {
            padding: 30px;
            text-align: center;
            background: #F5E6D3;
            border-bottom: 1px solid #d4c4a8;
            position: relative;
        }

        .menu-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 3px;
        }

        /* Order Type Dropdown */
        .order-type-dropdown {
            position: absolute;
            top: 20px;
            right: 30px;
        }

        .dropdown-btn {
            background: #8b4513;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 120px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .dropdown-btn:hover {
            background: #6d3410;
            transform: translateY(-2px);
        }

        .dropdown-arrow {
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        .dropdown-btn.active .dropdown-arrow {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #8b4513;
            border-radius: 8px;
            margin-top: 5px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            padding: 12px 20px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-weight: 500;
            color: #2c1810;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background: #F5E6D3;
        }

        .dropdown-item:first-child {
            border-radius: 6px 6px 0 0;
        }

        .dropdown-item:last-child {
            border-radius: 0 0 6px 6px;
        }

        .dropdown-item.selected {
            background: #8b4513;
            color: white;
        }

        .dropdown-item.selected:hover {
            background: #6d3410;
        }

        /* Products Grid */
        .products-section {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            max-width: 1200px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: #8b4513;
        }

        .product-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            background: #f0f0f0;
        }

        .product-info {
            padding: 15px;
            text-align: center;
        }

        .product-name {
            font-size: 1rem;
            font-weight: 600;
            color: #2c1810;
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #8b4513;
        }

        /* Bottom Cart Section */
        .cart-section {
            background: white;
            border-top: 2px solid #d4c4a8;
            padding: 20px 30px;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.1);
            transition: height 0.3s ease, padding 0.3s ease;
        }

        .cart-buttons {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-top: 0;
            margin-top: -20px;
        }

        .close-cart-btn {
            background: #8b4513;
            color: white;
            border: none;
            padding: 18px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            margin: 0 auto;
            margin-top: 0;
            min-height: 50px;
        }

        .cart-section.minimized {
            height: 50px;
            padding: 5px 30px;
            overflow: hidden;
        }

        .cart-section.minimized .cart-buttons {
            margin-bottom: 0;
            padding-top: 0;
            margin-top: 0;
        }

        .cart-section.minimized .cart-total,
        .cart-section.minimized .checkout-actions {
            display: none;
        }

        .close-cart-btn:hover {
            background: #6d3410;
            transform: translateY(-2px);
        }

        .cart-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .total-label {
            color: #2c1810;
        }

        .total-amount {
            color: #8b4513;
        }

        .checkout-actions {
            display: flex;
            gap: 15px;
        }

        .checkout-btn, .cancel-btn {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .checkout-btn {
            background: #8b4513;
            color: white;
        }

        .checkout-btn:hover {
            background: #6d3410;
            transform: translateY(-2px);
        }

        .cancel-btn {
            background: #e0e0e0;
            color: #666;
        }

        .cancel-btn:hover {
            background: #d0d0d0;
        }

        /* Custom Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: #F5E6D3;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c1810;
            margin-bottom: 30px;
            letter-spacing: 1px;
        }

        .modal-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .modal-btn-yes {
            background: #2c1810;
            color: white;
        }

        .modal-btn-yes:hover {
            background: #1a0f08;
            transform: translateY(-2px);
        }

        .modal-btn-no {
            background: white;
            color: #2c1810;
            border: 2px solid #d4c4a8;
        }

        .modal-btn-no:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }
        .item-counter {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .order-type-dropdown {
                top: 15px;
                right: 15px;
            }
            
            .dropdown-btn {
                padding: 10px 15px;
                font-size: 0.9rem;
                min-width: 100px;
            }
            
            .menu-title {
                font-size: 2rem;
                margin-right: 140px;
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
            
            <div class="category-list">
                <div class="category-item active" data-category="coffee">
                    <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 60'><rect width='60' height='60' fill='%238B4513' rx='8'/><circle cx='30' cy='30' r='15' fill='%23D2691E'/><circle cx='30' cy='25' r='8' fill='%23F4A460'/></svg>" alt="Coffee" class="category-image">
                    <span class="category-name">Coffee</span>
                </div>
                {{-- You can import additional categories from a partial if needed --}}
                {{-- @include('kiosk') --}}
                <div class="category-item" data-category="noodles">
                    <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 60'><rect width='60' height='60' fill='%23DAA520' rx='8'/><rect x='10' y='20' width='40' height='20' fill='%23FFD700' rx='4'/><rect x='15' y='25' width='30' height='3' fill='%23FFA500'/><rect x='15' y='32' width='30' height='3' fill='%23FFA500'/></svg>" alt="Noodles" class="category-image">
                    <span class="category-name">Noodles<br>& Pasta</span>
                </div>
                
                <div class="category-item" data-category="salads">
                    <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 60'><rect width='60' height='60' fill='%23228B22' rx='8'/><circle cx='30' cy='30' r='15' fill='%2332CD32'/><circle cx='25' cy='25' r='4' fill='%23ADFF2F'/><circle cx='35' cy='28' r='3' fill='%23ADFF2F'/><circle cx='30' cy='35' r='3' fill='%23ADFF2F'/></svg>" alt="Salads" class="category-image">
                    <span class="category-name">Salads</span>
                </div>
                
                <div class="category-item" data-category="appetizers">
                    <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 60'><rect width='60' height='60' fill='%23FF6347' rx='8'/><rect x='15' y='20' width='30' height='20' fill='%23FFA07A' rx='4'/><rect x='20' y='25' width='20' height='2' fill='%23FF4500'/><rect x='20' y='30' width='20' height='2' fill='%23FF4500'/><rect x='20' y='35' width='20' height='2' fill='%23FF4500'/></svg>" alt="Appetizers" class="category-image">
                    <span class="category-name">Appetizers</span>
                </div>
                
                <div class="category-item" data-category="rice">
                    <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 60'><rect width='60' height='60' fill='%23DEB887' rx='8'/><rect x='10' y='15' width='40' height='30' fill='%23F5DEB3' rx='6'/><circle cx='20' cy='25' r='2' fill='%23FFE4B5'/><circle cx='30' cy='30' r='2' fill='%23FFE4B5'/><circle cx='40' cy='35' r='2' fill='%23FFE4B5'/></svg>" alt="Rice Meals" class="category-image">
                    <span class="category-name">Rice Meals</span>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Menu Header -->
            <header class="menu-header">
                <h1 class="menu-title">MENU</h1>
                
                <!-- Order Type Dropdown -->
                <div class="order-type-dropdown">
                    <button class="dropdown-btn" id="orderTypeBtn">
                        <span id="selectedOrderType">{{ strtoupper($orderType ?? 'DINE IN') }}</span>
                        <span class="dropdown-arrow">▼</span>
                    </button>
                    <div class="dropdown-menu" id="orderTypeMenu">
                        <div class="dropdown-item" data-type="dine-in">DINE IN</div>
                        <div class="dropdown-item" data-type="take-out">TAKE OUT</div>
                    </div>
                </div>
            </header>

            <!-- Products Section -->
            <section class="products-section">
                <div class="products-grid" id="productsGrid">
                    <!-- Coffee Items -->
                    <div class="product-card" data-category="coffee" data-name="Affogato" data-price="150.00">
                        <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 150'><rect width='200' height='150' fill='%23F5F5F5'/><circle cx='100' cy='75' r='40' fill='%238B4513'/><circle cx='100' cy='65' r='25' fill='%23D2691E'/><circle cx='100' cy='55' r='15' fill='%23F4A460'/></svg>" alt="Affogato" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name">Affogato</h3>
                            <p class="product-price">PHP 150.00</p>
                        </div>
                    </div>

                    <div class="product-card" data-category="coffee" data-name="Espresso" data-price="150.00">
                        <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 150'><rect width='200' height='150' fill='%23F5F5F5'/><circle cx='100' cy='75' r='35' fill='%23654321'/><circle cx='100' cy='70' r='20' fill='%238B4513'/></svg>" alt="Espresso" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name">Espresso</h3>
                            <p class="product-price">PHP 150.00</p>
                        </div>
                    </div>

                    <div class="product-card" data-category="coffee" data-name="Affogato Special" data-price="150.00">
                        <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 150'><rect width='200' height='150' fill='%23F5F5F5'/><circle cx='100' cy='75' r='40' fill='%238B4513'/><circle cx='100' cy='65' r='25' fill='%23D2691E'/><circle cx='100' cy='55' r='15' fill='%23FFFACD'/><rect x='90' y='40' width='20' height='8' fill='%23DEB887'/></svg>" alt="Affogato Special" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name">Affogato</h3>
                            <p class="product-price">PHP 150.00</p>
                        </div>
                    </div>

                    <div class="product-card" data-category="coffee" data-name="Latte" data-price="150.00">
                        <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 150'><rect width='200' height='150' fill='%23F5F5F5'/><circle cx='100' cy='80' r='45' fill='%23DEB887'/><circle cx='100' cy='75' r='30' fill='%23F4A460'/><circle cx='100' cy='65' r='15' fill='%23FFFACD'/></svg>" alt="Latte" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name">Latte</h3>
                            <p class="product-price">PHP 150.00</p>
                        </div>
                    </div>

                    <div class="product-card" data-category="coffee" data-name="Cold Brew" data-price="150.00">
                        <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 150'><rect width='200' height='150' fill='%23F5F5F5'/><rect x='70' y='40' width='60' height='80' fill='%23654321' rx='8'/><rect x='75' y='45' width='50' height='70' fill='%238B4513' rx='4'/></svg>" alt="Cold Brew" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name">Cold Brew</h3>
                            <p class="product-price">PHP 150.00</p>
                        </div>
                    </div>

                    <div class="product-card" data-category="coffee" data-name="Doppio" data-price="150.00">
                        <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 150'><rect width='200' height='150' fill='%23F5F5F5'/><circle cx='100' cy='75' r='30' fill='%232F1B14'/><circle cx='100' cy='70' r='18' fill='%23654321'/></svg>" alt="Doppio" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name">Doppio</h3>
                            <p class="product-price">PHP 150.00</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Cart Section -->
            <footer class="cart-section">
                <div class="cart-buttons">
                    <button class="close-cart-btn" id="closeCartBtn">
                        V HIDE CART
                    </button>
                </div>
                
                <div class="cart-total">
                    <span class="total-label">TOTAL:</span>
                    <span class="total-amount" id="totalAmount">PHP 0.00</span>
                </div>
                
                <div class="checkout-actions">
                    <button class="checkout-btn" id="checkoutBtn">CHECKOUT</button>
                    <button class="cancel-btn" id="cancelBtn">CANCEL</button>
                </div>
            </footer>
        </main>
    </div>

    <!-- Custom Modal -->
    <div class="modal-overlay" id="cancelModal">
        <div class="modal-content">
            <h3 class="modal-title">CANCEL ORDERING?</h3>
            <button class="modal-btn modal-btn-yes" id="confirmYes">YES, CANCEL MY ORDER</button>
            <button class="modal-btn modal-btn-no" id="confirmNo">NO</button>
        </div>
    </div>

    <script>
        let cart = [];
        let total = 0;
        let currentOrderType = '{{ $orderType ?? "dine-in" }}';

        // Order Type Dropdown functionality
        const orderTypeBtn = document.getElementById('orderTypeBtn');
        const orderTypeMenu = document.getElementById('orderTypeMenu');
        const selectedOrderTypeSpan = document.getElementById('selectedOrderType');

        // Toggle dropdown
        orderTypeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            orderTypeBtn.classList.toggle('active');
            orderTypeMenu.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            orderTypeBtn.classList.remove('active');
            orderTypeMenu.classList.remove('active');
        });

        // Handle dropdown item selection
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                const selectedType = this.dataset.type;
                const displayText = this.textContent;
                
                // Update button text
                selectedOrderTypeSpan.textContent = displayText;
                currentOrderType = selectedType;
                
                // Update selected state
                document.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');
                
                // Close dropdown
                orderTypeBtn.classList.remove('active');
                orderTypeMenu.classList.remove('active');
                
                // Optional: Update session or send to server
                updateOrderType(selectedType);
            });
        });

        // Set initial selected state
        document.addEventListener('DOMContentLoaded', function() {
            const initialType = currentOrderType;
            const initialItem = document.querySelector(`[data-type="${initialType}"]`);
            if (initialItem) {
                initialItem.classList.add('selected');
            }
        });

        function updateOrderType(type) {
            // You can send an AJAX request to update the session
            fetch('/kiosk/update-order-type', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ order_type: type })
            }).catch(error => {
                console.log('Order type updated locally:', type);
            });
        }

        // Category switching
        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', function() {
                // Remove active class from all items
                document.querySelectorAll('.category-item').forEach(cat => cat.classList.remove('active'));
                // Add active class to clicked item
                this.classList.add('active');
                
                // Filter products (for now, show all coffee items)
                const category = this.dataset.category;
                filterProducts(category);
            });
        });

        // Product selection
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function() {
                const name = this.dataset.name;
                const price = parseFloat(this.dataset.price);
                
                // Add to cart
                const existingItem = cart.find(item => item.name === name);
                if (existingItem) {
                    existingItem.quantity++;
                } else {
                    cart.push({ name, price, quantity: 1 });
                }
                
                updateTotal();
                updateCartDisplay();
                
                // Visual feedback
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });

        function filterProducts(category) {
            const products = document.querySelectorAll('.product-card');
            products.forEach(product => {
                if (product.dataset.category === category) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }

        function updateTotal() {
            total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            document.getElementById('totalAmount').textContent = `PHP ${total.toFixed(2)}`;
        }

        function updateCartDisplay() {
            // Update cart count display (could add visual indicators)
            console.log('Cart updated:', cart);
        }

        // Checkout functionality
        document.getElementById('checkoutBtn').addEventListener('click', function() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            
            // Process checkout
            alert(`Order type: ${currentOrderType.toUpperCase()}\nOrder total: PHP ${total.toFixed(2)}\nProceeding to payment...`);
        });

        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('cancelModal').style.display = 'flex';
        });

        document.getElementById('confirmYes').addEventListener('click', function() {
            window.location.href = 'http://127.0.0.1:8000/kiosk/';
        });

        document.getElementById('confirmNo').addEventListener('click', function() {
            document.getElementById('cancelModal').style.display = 'none';
        });

        // Close modal when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });

        // Cart toggle functionality
        document.getElementById('closeCartBtn').addEventListener('click', function() {
            const cartSection = document.querySelector('.cart-section');
            const closeBtn = this;
            
            if (cartSection.classList.contains('minimized')) {
                // Restore cart
                cartSection.classList.remove('minimized');
                closeBtn.textContent = 'V HIDE CART';
            } else {
                // Minimize cart
                cartSection.classList.add('minimized');
                closeBtn.textContent = 'Λ SHOW CART';
            }
        });

        // Initialize with coffee category
        filterProducts('coffee');
    </script>
</body>
</html>