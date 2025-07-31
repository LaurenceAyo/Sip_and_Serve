<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Cafe Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f1eb 0%, #e8ddd4 100%);
            min-height: 100vh;
        }

        .dashboard-container {
            background: white;
            min-height: 100vh;
        }

        /* Enhanced Tablet Responsiveness */
        @media (max-width: 1280px) {
            .header-section {
                padding: 1.25rem 0;
            }

            .header-section h1 {
                font-size: 1.5rem;
            }

            .header-section h2 {
                font-size: 1.75rem;
            }

            .max-w-7xl {
                padding: 0 1.5rem;
            }

            .controls-section {
                gap: 1.5rem;
                align-items: stretch;
            }

            .filter-section {
                justify-content: left;
            }

            .filter-dropdown {
                width: 130px;
                padding: 14px 20px;
                font-size: 1rem;
                text-align: center;
            }

            .button-group {
                justify-content: LEFT;
                width: 100%;
            }

            .btn-primary {
                padding: 14px 20px;
                font-size: 1rem;
                min-height: 50px;
            }

            .metrics-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
                margin-bottom: 2rem;
            }

            .sales-card {
                padding: 1.5rem;
                border-width: 15px;
            }

            .metric-card {
                padding: 1.5rem;
                margin-bottom: 0;
            }

            .metric-card h4 {
                font-size: 1.1rem;
                margin-bottom: 1rem;
            }

            .metric-card p {
                font-size: 1.5rem;
            }

            .top-items {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .item-card {
                padding: 1.5rem;
            }

            .item-image {
                width: 70px;
                height: 70px;
                margin-bottom: 1rem;
            }

            .item-card h5 {
                font-size: 1rem;
                margin-bottom: 0.5rem;
            }

            .item-card p {
                font-size: 0.9rem;
            }

            .bottom-nav {
                flex-direction: column;
                gap: 2rem;
                margin-top: 2rem;
                padding: 1.5rem;
                background: rgba(245, 230, 211, 0.2);
                border-radius: 12px;
            }

            .tab-section {
                position: static;
                transform: none;
                width: 100%;
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 0;
                background: rgba(212, 197, 169, 0.3);
                border-radius: 8px;
                overflow: hidden;
            }

            .tab-button {
                padding: 16px 20px;
                font-size: 1rem;
                border-radius: 0;
                min-height: 60px;
            }

            .tab-button:first-child {
                border-top-left-radius: 8px;
                border-bottom-left-radius: 8px;
            }

            .tab-button:last-child {
                border-top-right-radius: 8px;
                border-bottom-right-radius: 8px;
            }

            .logout-btn {
                width: 100%;
                padding: 16px;
                font-size: 1.1rem;
                min-height: 60px;
                border-radius: 8px;
                margin-left: 0;
            }

            .modal-content {
                width: 85%;
                max-width: 400px;
                margin: 1rem;
                padding: 2.5rem;
            }

            .modal-title {
                font-size: 1.3rem;
                margin-bottom: 2rem;
            }

            .modal-btn {
                padding: 16px 24px;
                font-size: 1.1rem;
                min-height: 55px;
            }

            .manager-info {
                padding: 12px 16px;
                font-size: 1rem;
            }

            .separator-line {
                height: 50px;
                margin: 0 15px;
            }
        }

        /* Smaller tablet adjustments */
        @media (max-width: 900px) {
            .header-flex {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .separator-line {
                display: none;
            }

            .header-section h1 {
                font-size: 1.3rem;
            }

            .header-section h2 {
                font-size: 1.5rem;
            }

            .top-items {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }

            .item-image {
                width: 60px;
                height: 60px;
            }

            .item-card {
                padding: 1rem;
            }
        }

        /* Portrait tablet */
        @media (max-width: 768px) {
            .max-w-7xl {
                padding: 0 1rem;
            }

            .top-items {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .item-card {
                display: flex;
                align-items: center;
                text-align: left;
                gap: 1rem;
            }

            .item-image {
                width: 50px;
                height: 50px;
                margin: 0;
                flex-shrink: 0;
            }

            .item-info {
                flex: 1;
            }

            .tab-button {
                padding: 14px 16px;
                font-size: 0.9rem;
                min-height: 55px;
            }

            .metric-card h4 {
                font-size: 1rem;
            }

            .metric-card p {
                font-size: 1.3rem;
            }
        }

        .header-section {
            background: #F5E6D3;
            color: #5d4037;
            padding: 1.5rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-bottom: 3px solid #8b4513;
        }

        .sales-summary {
            background: linear-gradient(135deg, #ffd54f 0%, #ffb74d 100%);
            border-radius: 12px;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            color: #5d4037;
            margin-bottom: 8px;
        }

        .metric-card {
            background: #e8ddd4;
            border-radius: 2px;
            padding: 5px;
            text-align: center;
            margin-bottom: 10px;
        }

        .sales-card {
            background: #f8f6f0;
            border-radius: 12px;
            padding: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 20px solid #d4c5a9;
        }

        .top-items {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .separator-line {
            width: 2px;
            height: 40px;
            background: rgba(139, 69, 19, 0.3);
            position: relative;
            margin: 0 10px;
        }

        .separator-line::after {
            content: '';
            position: absolute;
            top: 0;
            right: -2px;
            width: 2px;
            height: 100%;
            background: rgba(139, 69, 19, 0.1);
            box-shadow: 2px 0 4px rgba(139, 69, 19, 0.2);
        }

        .item-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            margin: 0 auto 10px;
            object-fit: cover;
        }

        .sales-table {
            border-collapse: collapse;
            width: 100%;
            background: #f8f6f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: #F5E6D3;
            padding: 35px 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 320px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 25px;
            letter-spacing: 0.3px;
        }

        .modal-btn {
            width: 100%;
            padding: 14px 20px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            margin: 6px 0;
            transition: all 0.2s ease;
            font-size: 16px;
            letter-spacing: 0.5px;
        }

        .modal-btn-logout {
            background: #2c2c2c;
            color: white;
            margin-bottom: 12px;
        }

        .modal-btn-logout:hover {
            background: #404040;
            transform: translateY(-1px);
        }

        .modal-btn-cancel {
            background: white;
            color: #666;
            border: 1px solid #e0e0e0;
        }

        .modal-btn-cancel:hover {
            background: #f8f8f8;
            transform: translateY(-1px);
        }

        .sales-table th {
            background: #d4c5a9;
            color: #5d4037;
            font-weight: 600;
            padding: 12px;
            text-align: center;
            border-bottom: 2px solid #b8a082;
        }

        .sales-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #e0d4c3;
        }

        .sales-table tr:hover {
            background: #f0ebe1;
        }

        .btn-primary {
            background: #8b4513;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #6d3410;
            transform: translateY(-1px);
        }

        .filter-dropdown {
            background: #f8f6f0;
            border: 2px solid #d4c5a9;
            border-radius: 5px;
            padding: 8px 15px;
            color: #5d4037;
            font-weight: 500;
            cursor: pointer;
        }

        .tab-button {
            background: none;
            border: none;
            padding: 15px 30px;
            cursor: pointer;
            font-weight: 500;
            color: #8b4513;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            flex: 1;
            text-align: center;
        }

        .tab-button.active {
            background: #5d4037;
            color: white;
            border-bottom: 3px solid #d4c5a9;
        }

        .tab-button:hover {
            background: #f0ebe1;
        }

        .tab-button.active:hover {
            background: #5d4037;
        }

        .logout-btn {
            background: #8b4513;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-left: 1rem;
        }

        .logout-btn:hover {
            background: #6d3410;
        }

        .header-title {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .manager-info {
            background: rgba(139, 69, 19, 0.1);
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #8b4513;
        }
    </style>
</head>

<body class="bg-gray-800">
    <div class="min-h-screen p-4">
        <!-- Header -->
        <div class="bg-amber-50 rounded-t-lg p-6 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-amber-100 px-4 py-2 rounded">
                    <h1 class="text-2xl font-serif text-gray-800">Sip & Serve</h1>
                </div>
                <h2 class="text-3xl font-serif text-gray-800 tracking-wider">CAFE DASHBOARD</h2>
            </div>
            <div class="text-sm text-gray-600">
                Kiosk (Tablet-overview)
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-b-lg p-6">
            <!-- Manager Info -->
            <div class="mb-6">
                <p class="text-sm text-gray-600">Manager ID: <span class="font-medium">10023</span></p>
            </div>

            <!-- Menu Item List Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-medium text-gray-800">Menu Item List</h3>
                    <div class="relative">
                        <input type="text" placeholder="Search Menu"
                            class="bg-gray-100 px-4 py-2 pr-10 rounded border focus:outline-none focus:ring-2 focus:ring-amber-500"
                            id="searchInput">
                        <button class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="border border-gray-300 rounded overflow-hidden">
                    <div class="max-h-80 overflow-y-auto scrollbar-thin">
                        <table class="w-full">
                            <thead class="bg-gray-200 sticky top-0">
                                <tr>
                                    <th class="text-left p-3 font-medium text-gray-700 border-r border-gray-300">Item
                                        Name</th>
                                    <th class="text-left p-3 font-medium text-gray-700">Price</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                @forelse($products ?? [] as $product)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 cursor-pointer"
                                        data-product-id="{{ $product->id }}">
                                        <td class="p-3 border-r border-gray-300">{{ $product->name }}</td>
                                        <td class="p-3">PHP {{ number_format($product->price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="p-6 text-center text-gray-500">No products found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-8">
                <h3 class="text-xl font-medium text-gray-800 mb-4">Quick Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <button onclick="openAddModal()"
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded font-medium transition-colors">
                        Add New Item
                    </button>
                    <button onclick="editSelectedItem()"
                        class="bg-yellow-400 hover:bg-yellow-500 text-gray-800 px-6 py-3 rounded font-medium transition-colors">
                        Edit Price
                    </button>
                    <button onclick="deleteSelectedItem()"
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded font-medium transition-colors">
                        Delete Item
                    </button>
                </div>
            </div>

            <!-- Bottom Navigation -->
            <div class="bottom-nav flex justify-between items-center">
                <div class="tab-section flex space-x-5">
                    <button class="tab-button">INVENTORY</button>
                    <button class="tab-button ">SALES</button>
                    <button class="tab-button active">PRODUCT</button>
                </div>

                <button class="logout-btn" onclick="openLogoutModal()">
                    🚪 LOG OUT
                </button>
            </div>
        </div>
    </div>
    </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-96">
            <h3 class="text-lg font-medium mb-4">Add New Product</h3>
            <form id="addProductForm" onsubmit="addProduct(event)">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                    <input type="text" name="name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price (PHP)</label>
                    <input type="number" name="price" step="0.01" required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddModal()"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Add
                        Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-96">
            <h3 class="text-lg font-medium mb-4">Edit Product Price</h3>
            <form id="editProductForm" onsubmit="updateProduct(event)">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                    <input type="text" id="editProductName" readonly
                        class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price (PHP)</label>
                    <input type="number" name="price" id="editProductPrice" step="0.01" required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button type="submit"
                        class="bg-yellow-400 hover:bg-yellow-500 text-gray-800 px-4 py-2 rounded">Update Price</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Logout Modal -->
    <div id="logoutModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-96">
            <h3 class="text-lg font-medium mb-4">Confirm Logout</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to log out?</p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeLogoutModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button onclick="confirmLogout()"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</button>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="messageContainer" class="fixed top-4 right-4 z-50"></div>

    <script>
        let selectedProductId = null;
        let productIdCounter = 9; // Start from 9 since we have 8 default products

        // This will be populated from your Laravel backend
        let products = [];

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#productsTableBody tr');

            rows.forEach(row => {
                const productName = row.querySelector('td:first-child').textContent.toLowerCase();
                row.style.display = productName.includes(searchTerm) ? '' : 'none';
            });
        });

        // Initialize row selection
        function initializeRowSelection() {
            document.querySelectorAll('#productsTableBody tr').forEach(row => {
                row.addEventListener('click', function () {
                    // Remove previous selection
                    document.querySelectorAll('#productsTableBody tr').forEach(r => r.classList.remove('selected-row'));

                    // Add selection to current row
                    this.classList.add('selected-row');
                    selectedProductId = parseInt(this.dataset.productId);
                });
            });
        }

        // Modal functions
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.getElementById('addModal').classList.add('flex');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.getElementById('addModal').classList.remove('flex');
            document.getElementById('addProductForm').reset();
        }

        function editSelectedItem() {
            if (!selectedProductId) {
                showMessage('Please select a product to edit', 'error');
                return;
            }

            const product = products.find(p => p.id === selectedProductId);
            if (!product) {
                showMessage('Product not found', 'error');
                return;
            }

            document.getElementById('editProductName').value = product.name;
            document.getElementById('editProductPrice').value = product.price;

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
            document.getElementById('editProductForm').reset();
        }

        function deleteSelectedItem() {
            if (!selectedProductId) {
                showMessage('Please select a product to delete', 'error');
                return;
            }

            const product = products.find(p => p.id === selectedProductId);
            if (!product) {
                showMessage('Product not found', 'error');
                return;
            }

            if (confirm(`Are you sure you want to delete "${product.name}"?`)) {
                products = products.filter(p => p.id !== selectedProductId);
                renderProducts();
                selectedProductId = null;
                showMessage('Product deleted successfully', 'success');
            }
        }

        // Form handlers
        function addProduct(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const name = formData.get('name').trim();
            const price = parseFloat(formData.get('price'));

            if (!name || price < 0) {
                showMessage('Please provide valid product details', 'error');
                return;
            }

            const newProduct = {
                id: productIdCounter++,
                name: name,
                price: price
            };

            products.push(newProduct);
            renderProducts();
            closeAddModal();
            showMessage('Product added successfully', 'success');
        }

        function updateProduct(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const price = parseFloat(formData.get('price'));

            if (price < 0) {
                showMessage('Please provide a valid price', 'error');
                return;
            }

            const productIndex = products.findIndex(p => p.id === selectedProductId);
            if (productIndex === -1) {
                showMessage('Product not found', 'error');
                return;
            }

            products[productIndex].price = price;
            renderProducts();
            closeEditModal();
            showMessage('Product price updated successfully', 'success');
        }

        // Render products table
        function renderProducts() {
            const tbody = document.getElementById('productsTableBody');
            tbody.innerHTML = '';

            products.forEach(product => {
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-200 hover:bg-gray-50 cursor-pointer';
                row.dataset.productId = product.id;
                row.innerHTML = `
                    <td class="p-3 border-r border-gray-300">${product.name}</td>
                    <td class="p-3">PHP ${product.price.toFixed(2)}</td>
                `;
                tbody.appendChild(row);
            });

            // Reinitialize row selection
            initializeRowSelection();
        }

        // Show messages
        function showMessage(message, type) {
            const messageContainer = document.getElementById('messageContainer');
            const messageDiv = document.createElement('div');
            messageDiv.className = `px-6 py-3 rounded shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            messageDiv.textContent = message;

            messageContainer.appendChild(messageDiv);

            setTimeout(() => {
                messageDiv.remove();
            }, 3000);
        }

        // Logout modal logic
        function openLogoutModal() {
            document.getElementById('logoutModal').classList.add('show');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.remove('show');
        }

        function confirmLogout() {
            window.location.href = 'http://127.0.0.1:8000';
        }

        // Close modal if background clicked
        document.getElementById('logoutModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeLogoutModal();
            }
        });

        // Tab switching functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function () {
                // Remove active class from all buttons
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');

                // Handle navigation
                if (this.textContent.trim() === 'INVENTORY') {
                    window.location.href = '/dashboard';
                } else if (this.textContent.trim() === 'SALES') {
                    window.location.href = '/sales';
                } else if (this.textContent.trim() === 'PRODUCT') {
                    window.location.href = '/product';
                }
            });
        });

        // Logout modal logic
        function openLogoutModal() {
            document.getElementById('logoutModal').classList.add('show');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.remove('show');
        }

        function confirmLogout() {
            // In a real application, this would call your logout route
            window.location.href = '{{ route("logout") }}';
        }

        // Close modal if background clicked
        document.getElementById('logoutModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeLogoutModal();
            }
        });

        // Touch-friendly enhancements for tablets
        document.addEventListener('DOMContentLoaded', function () {
            // Add touch feedback for buttons
            const buttons = document.querySelectorAll('button, .tab-button');
            buttons.forEach(button => {
                button.addEventListener('touchstart', function () {
                    this.style.transform = 'scale(0.98)';
                });
                button.addEventListener('touchend', function () {
                    this.style.transform = 'scale(1)';
                });
            });
        });

        // Close modals when clicking outside
        document.getElementById('addModal').addEventListener('click', function (e) {
            if (e.target === this) closeAddModal();
        });

        document.getElementById('editModal').addEventListener('click', function (e) {
            if (e.target === this) closeEditModal();
        });

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function () {
            initializeRowSelection();
        });
    </script>
</body>

</html>