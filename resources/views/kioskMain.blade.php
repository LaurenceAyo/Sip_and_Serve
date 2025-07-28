<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>L'PRIMERO CAFE - Menu</title>
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
            /* Add this */
            overflow: hidden;
            /* Add this to prevent horizontal scroll */
        }

        /* Left Sidebar */
        .sidebar {
            flex: 0 0 200px;
            /* Don't grow, don't shrink, stay 200px */
            min-width: 200px;
            /* Minimum width */
            max-width: 200px;
            /* Maximum width */
            background: #F5E6D3;
            border-right: 5px solid #d4c4a8;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            overflow-x: hidden;
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
            min-width: 0;
            /* Allows flex shrinking */
            flex: 1;
            /* This should take up remaining space */
            overflow-x: auto;
            /* Add this if content might overflow */
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

            display: block;
        }

        .product-card.hide {
            display: none !important;
        }

        .product-card.show {
            display: block !important;
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
            position: fixed;
            /* Add this */
            bottom: 0;
            /* Add this */
            left: 200px;
            /* Add this - starts after sidebar width */
            right: 0;
            /* Add this - extends to right edge */
            z-index: 1000;
            /* Add this - keeps it above other content */
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

        .category-prompt {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            text-align: center;
            color: #8b4513;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .category-prompt h2 {
            margin: 0;
            padding: 20px;
            background: rgba(245, 230, 211, 0.8);
            border-radius: 10px;
            border: 2px solid #d4c4a8;
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

        .checkout-btn,
        .cancel-btn {
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

        .product-card.hide {
            display: none;
        }

        .product-card.show {
            display: block;
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

            .sidebar {
                width: 150px !important;
                flex-shrink: 0;
                /* Prevents shrinking */
                flex-grow: 0;
                /* Prevents growing */
            }

            .kiosk-container {
                flex-wrap: nowrap;
            }

            .main-content {
                min-width: 0;
                /* Allows flex shrinking */
                flex: 1;
                /* Add this line - takes up remaining space */
            }

            .products-section {
                flex: 1;
                padding: 30px;
                padding-bottom: 120px;
                /* Add this - adjust based on your cart height */
                overflow-y: auto;
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
                @foreach($categories as $index => $category)
                    <div class="category-item {{ $index === 0 ? 'active' : '' }}"
                        data-category="{{ strtolower(str_replace(' ', '_', $category->name)) }}"
                        data-category-id="{{ $category->id }}">
                        @if($category->image && file_exists(public_path('assets/' . $category->image)))
                            <img src="{{ asset('assets/' . $category->image) }}" alt="{{ $category->name }}"
                                class="category-image">
                        @else
                            <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 60'><rect width='60' height='60' fill='%238B4513' rx='8'/><circle cx='30' cy='30' r='15' fill='%23D2691E'/><circle cx='30' cy='25' r='8' fill='%23F4A460'/></svg>"
                                alt="{{ $category->name }}" class="category-image">
                        @endif
                        <span class="category-name">{{ str_replace('_', ' ', $category->name) }}</span>
                    </div>
                @endforeach
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
                        <span id="selectedOrderType">{{ strtoupper(str_replace('-', ' ', $orderType)) }}</span>
                        <span class="dropdown-arrow">▼</span>
                    </button>
                    <div class="dropdown-menu" id="orderTypeMenu">
                        <div class="dropdown-item {{ $orderType === 'dine-in' ? 'selected' : '' }}" data-type="dine-in">
                            DINE IN</div>
                        <div class="dropdown-item {{ $orderType === 'take-out' ? 'selected' : '' }}"
                            data-type="take-out">TAKE OUT</div>
                    </div>
                </div>
            </header>

            <!-- Products Section -->
            <section class="products-section">
                <div class="products-grid" id="productsGrid">
                    <div id="categoryPrompt" class="category-prompt">
                        <h2>PLEASE CHOOSE FROM CATEGORY</h2>
                    </div>
                    @foreach($menuItems as $item)
                        <div class="product-card hide"
                            data-category="{{ strtolower(str_replace(' ', '_', $item->category->name)) }}"
                            data-name="{{ $item->name }}" data-price="{{ $item->price }}" data-id="{{ $item->id }}"
                            data-has-variants="{{ $item->has_variants ? 'true' : 'false' }}">

                            @if($item->image && file_exists(public_path('assets/' . $item->image)))
                                <img src="{{ asset('assets/' . $item->image) }}" alt="{{ $item->name }}" class="product-image">
                            @else
                                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 150'><rect width='200' height='150' fill='%23F5F5F5'/><circle cx='100' cy='75' r='40' fill='%238B4513'/><circle cx='100' cy='65' r='25' fill='%23D2691E'/><circle cx='100' cy='55' r='15' fill='%23F4A460'/></svg>"
                                    alt="{{ $item->name }}" class="product-image">
                            @endif

                            <div class="product-info">
                                <h3 class="product-name">{{ $item->name }}</h3>
                                <p class="product-price">PHP {{ number_format($item->price, 2) }}</p>
                                @if($item->description)
                                    <p class="product-description" style="font-size: 0.8rem; color: #666; margin-top: 4px;">
                                        {{ Str::limit($item->description, 50) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
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
        let menuItems = @json($menuItems);
        let categories = @json($categories);

        // Order Type Dropdown functionality
        const orderTypeBtn = document.getElementById('orderTypeBtn');
        const orderTypeMenu = document.getElementById('orderTypeMenu');
        const selectedOrderTypeSpan = document.getElementById('selectedOrderType');

        // Toggle dropdown
        orderTypeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            orderTypeBtn.classList.toggle('active');
            orderTypeMenu.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function () {
            orderTypeBtn.classList.remove('active');
            orderTypeMenu.classList.remove('active');
        });

        // Handle dropdown item selection
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function () {
                const selectedType = this.dataset.type;
                const displayText = this.textContent;

                // Update button text
                document.querySelector('.dropdown-btn span').textContent = displayText;
                selectedOrderTypeSpan.textContent = displayText;

                // Update selected state
                document.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');

                // Close dropdown
                orderTypeBtn.classList.remove('active');
                orderTypeMenu.classList.remove('active');

                // Update order type
                updateOrderType(selectedType);
            });
        });

        // DOMContentLoaded event handler
        document.addEventListener('DOMContentLoaded', function () {
            // Set initial dropdown text based on order type
            const orderType = '{{ $orderType ?? "dine-in" }}';
            const displayText = orderType === 'dine-in' ? 'DINE IN' : 'TAKE OUT';
            document.querySelector('.dropdown-btn span').textContent = displayText;

            // Set initial selected state for order type
            const initialType = currentOrderType;
            const initialItem = document.querySelector(`[data-type="${initialType}"]`);
            if (initialItem) {
                initialItem.classList.add('selected');
            }

            // IMPORTANT: Remove initial active state and hide all products
            document.querySelector('.category-item.active')?.classList.remove('active');

            // Hide all products initially and show category prompt
            document.querySelectorAll('.product-card').forEach(card => {
                card.classList.remove('show');
                card.classList.add('hide');
                card.style.display = 'none';
            });

            // Show the category prompt
            const categoryPrompt = document.getElementById('categoryPrompt');
            if (categoryPrompt) {
                categoryPrompt.style.display = 'block';
            }
        });

        function updateOrderType(type) {
            currentOrderType = type;
            // Send AJAX request to update session
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

        // SINGLE CATEGORY CLICK HANDLER - This replaces all the conflicting ones
        document.addEventListener('click', function (e) {
            // Check if clicked element is a category item or inside one
            const categoryItem = e.target.closest('.category-item');

            if (categoryItem) {
                // Remove active class from all items
                document.querySelectorAll('.category-item').forEach(cat => cat.classList.remove('active'));
                // Add active class to clicked item
                categoryItem.classList.add('active');

                // Hide category prompt
                const categoryPrompt = document.getElementById('categoryPrompt');
                if (categoryPrompt) {
                    categoryPrompt.style.display = 'none';
                }

                const categoryId = categoryItem.dataset.categoryId;
                const category = categoryItem.dataset.category;

                console.log('Category clicked:', category, 'ID:', categoryId);

                // Hide all products first
                document.querySelectorAll('.product-card').forEach(card => {
                    card.classList.remove('show');
                    card.classList.add('hide');
                    card.style.display = 'none';
                });

                // Show products for selected category
                const categoryProducts = document.querySelectorAll(`.product-card[data-category="${category}"]`);
                console.log('Found products for category:', categoryProducts.length);

                categoryProducts.forEach(card => {
                    card.classList.remove('hide');
                    card.classList.add('show');
                    card.style.display = 'block';
                });

                // If no products found, try fetching from server
                if (categoryProducts.length === 0 && categoryId) {
                    fetch(`/category/${categoryId}/items`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Fetched items:', data);
                            menuItems = data.menuItems;
                            updateProductsGrid(data.menuItems);
                        })
                        .catch(error => {
                            console.error('Error fetching category items:', error);
                        });
                }
            }
        });

        function updateProductsGrid(items) {
            const productsGrid = document.getElementById('productsGrid');
            productsGrid.innerHTML = '';

            items.forEach(item => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card show';
                productCard.dataset.category = item.category ? item.category.name.toLowerCase().replace(' ', '_') : '';
                productCard.dataset.name = item.name;
                productCard.dataset.price = item.price;
                productCard.dataset.id = item.id;
                productCard.dataset.hasVariants = item.has_variants ? 'true' : 'false';

                const imageUrl = item.image && item.image !== ''
                    ? `/assets/${item.image}`
                    : "data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 150'><rect width='200' height='150' fill='%23F5F5F5'/><circle cx='100' cy='75' r='40' fill='%238B4513'/><circle cx='100' cy='65' r='25' fill='%23D2691E'/><circle cx='100' cy='55' r='15' fill='%23F4A460'/></svg>";

                productCard.innerHTML = `
            <img src="${imageUrl}" alt="${item.name}" class="product-image">
            <div class="product-info">
                <h3 class="product-name">${item.name}</h3>
                <p class="product-price">PHP ${parseFloat(item.price).toFixed(2)}</p>
                ${item.description ? `<p class="product-description" style="font-size: 0.8rem; color: #666; margin-top: 4px;">${item.description.substring(0, 50)}${item.description.length > 50 ? '...' : ''}</p>` : ''}
            </div>
        `;

                // Add click event for the new product card
                productCard.addEventListener('click', function () {
                    const itemId = parseInt(this.dataset.id);
                    const hasVariants = this.dataset.hasVariants === 'true';
                    const menuItem = menuItems.find(item => item.id === itemId);

                    if (!menuItem) return;

                    if (hasVariants && menuItem.variants && menuItem.variants.length > 0) {
                        showVariantModal(menuItem);
                    } else {
                        addToCart(menuItem);
                    }

                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });

                productsGrid.appendChild(productCard);
            });
        }

        // Product selection with variant support
        document.addEventListener('click', function (e) {
            if (e.target.closest('.product-card')) {
                const card = e.target.closest('.product-card');
                const itemId = parseInt(card.dataset.id);
                const hasVariants = card.dataset.hasVariants === 'true';
                const menuItem = menuItems.find(item => item.id === itemId);

                if (!menuItem) return;

                if (hasVariants && menuItem.variants && menuItem.variants.length > 0) {
                    showVariantModal(menuItem);
                } else {
                    addToCart(menuItem);
                }

                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.style.transform = '';
                }, 150);
            }
        });

        function showVariantModal(menuItem) {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.style.display = 'flex';

            let variantOptions = '';
            menuItem.variants.forEach(variant => {
                const variantPrice = parseFloat(menuItem.price) + parseFloat(variant.price_adjustment || 0);
                variantOptions += `
        <button class="modal-btn modal-btn-variant" 
                data-variant-id="${variant.id}" 
                data-variant-name="${variant.variant_name}: ${variant.variant_value}"
                data-variant-price="${variantPrice}">
            ${variant.variant_name}: ${variant.variant_value} - PHP ${variantPrice.toFixed(2)}
        </button>
    `;
            });

            modal.innerHTML = `
    <div class="modal-content">
        <h3 class="modal-title">Choose ${menuItem.name}</h3>
        ${variantOptions}
        <button class="modal-btn modal-btn-no" onclick="this.closest('.modal-overlay').remove()">Cancel</button>
    </div>
`;

            document.body.appendChild(modal);

            modal.querySelectorAll('.modal-btn-variant').forEach(btn => {
                btn.addEventListener('click', function () {
                    const variantId = this.dataset.variantId;
                    const variantName = this.dataset.variantName;
                    const variantPrice = parseFloat(this.dataset.variantPrice);

                    addToCart(menuItem, {
                        id: variantId,
                        name: variantName,
                        price: variantPrice
                    });

                    modal.remove();
                });
            });

            modal.addEventListener('click', function (e) {
                if (e.target === this) {
                    this.remove();
                }
            });
        }

        function addToCart(menuItem, variant = null) {
            const itemName = variant ? `${menuItem.name} (${variant.name})` : menuItem.name;
            const itemPrice = variant ? variant.price : parseFloat(menuItem.price);
            const itemId = menuItem.id;
            const variantId = variant ? variant.id : null;

            const existingItemIndex = cart.findIndex(item =>
                item.menu_item_id === itemId && item.variant_id === variantId
            );

            if (existingItemIndex !== -1) {
                cart[existingItemIndex].quantity++;
            } else {
                cart.push({
                    menu_item_id: itemId,
                    variant_id: variantId,
                    name: itemName,
                    price: itemPrice,
                    quantity: 1
                });
            }

            updateTotal();
            updateCartDisplay();
        }

        function updateTotal() {
            total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            document.getElementById('totalAmount').textContent = `PHP ${total.toFixed(2)}`;

            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const closeBtn = document.getElementById('closeCartBtn');
            const cartSection = document.querySelector('.cart-section');

            if (totalItems > 0) {
                if (cartSection.classList.contains('minimized')) {
                    closeBtn.innerHTML = `Λ SHOW CART (${totalItems})`;
                } else {
                    closeBtn.innerHTML = `V HIDE CART (${totalItems})`;
                }
            } else {
                if (cartSection.classList.contains('minimized')) {
                    closeBtn.textContent = 'Λ SHOW CART';
                } else {
                    closeBtn.textContent = 'V HIDE CART';
                }
            }
        }

        function updateCartDisplay() {
            console.log('Cart updated:', cart);
        }

        // Checkout functionality
        document.getElementById('checkoutBtn').addEventListener('click', function () {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }

            const orderData = {
                order_type: currentOrderType,
                items: cart,
                total: total,
                subtotal: total,
                tax_amount: 0,
                discount_amount: 0
            };

            fetch('/kiosk/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(orderData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect_url || '/kiosk/payment';
                    } else {
                        alert('Error processing order: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Checkout error:', error);
                    alert('Error processing order. Please try again.');
                });
        });

        document.getElementById('cancelBtn').addEventListener('click', function () {
            document.getElementById('cancelModal').style.display = 'flex';
        });

        document.getElementById('confirmYes').addEventListener('click', function () {
            cart = [];
            total = 0;
            updateTotal();
            window.location.href = '/kiosk';
        });

        document.getElementById('confirmNo').addEventListener('click', function () {
            document.getElementById('cancelModal').style.display = 'none';
        });

        document.getElementById('cancelModal').addEventListener('click', function (e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });

        // Cart toggle functionality
        document.getElementById('closeCartBtn').addEventListener('click', function () {
            const cartSection = document.querySelector('.cart-section');
            const closeBtn = this;
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

            if (cartSection.classList.contains('minimized')) {
                cartSection.classList.remove('minimized');
                closeBtn.innerHTML = totalItems > 0 ? `V HIDE CART (${totalItems})` : 'V HIDE CART';
            } else {
                cartSection.classList.add('minimized');
                closeBtn.innerHTML = totalItems > 0 ? `Λ SHOW CART (${totalItems})` : 'Λ SHOW CART';
            }
        });

        // Add CSS for variant modal buttons
        const additionalStyles = `
.modal-btn-variant {
    background: white;
    color: #2c1810;
    border: 2px solid #d4c4a8;
    margin-bottom: 10px;
    text-align: left;
    padding: 12px 20px;
}

.modal-btn-variant:hover {
    background: #F5E6D3;
    border-color: #8b4513;
}

.product-card.hide {
    display: none !important;
}

.product-card.show {
    display: block !important;
}
`;

        const styleSheet = document.createElement('style');
        styleSheet.textContent = additionalStyles;
        document.head.appendChild(styleSheet);
    </script>
</body>

</html>