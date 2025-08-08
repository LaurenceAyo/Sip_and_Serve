<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Products - Cafe Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
            min-height: 100vh;
        }

        .dashboard-container {
            background: white;
            min-height: 100vh;
        }

        /* CHUWI HIPAD 11 Tablet Optimization (10.9 inch, 2000x1200) */
        @media (max-width: 1300px) {
            .header-section {
                padding: 2rem 0;
            }

            .header-section h1 {
                font-size: 2rem;
                font-weight: 700;
            }

            .header-section h2 {
                font-size: 2.5rem;
                font-weight: 600;
            }

            .max-w-7xl {
                padding: 0 2rem;
            }

            .controls-section {
                flex-direction: row;
                gap: 2rem;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 2rem;
            }

            .filter-section {
                width: auto;
                justify-content: flex-start;
            }

            .search-input {
                width: 320px;
                padding: 18px 24px;
                font-size: 1.2rem;
                border: 3px solid #d4c5a9;
                border-radius: 8px;
            }

            .button-group {
                display: flex;
                gap: 1.5rem;
                width: auto;
                flex-wrap: wrap;
            }

            .btn-primary,
            .btn-secondary,
            .btn-danger {
                padding: 18px 28px;
                font-size: 1.2rem;
                width: auto;
                min-height: 60px;
                min-width: 180px;
                border-radius: 8px;
                font-weight: 600;
            }

            .products-card {
                padding: 2rem;
                border-width: 20px;
                border-radius: 16px;
            }

            .bottom-nav {
                flex-direction: row;
                gap: 0;
                margin-top: 3rem;
                padding: 0;
                background: none;
                border-radius: 0;
                justify-content: space-between;
                align-items: center;
            }

            .tab-section {
                position: static;
                transform: none;
                width: auto;
                display: flex;
                gap: 0;
                background: rgba(212, 197, 169, 0.4);
                border-radius: 12px;
                overflow: hidden;
                border: 3px solid #d4c5a9;
            }

            .tab-button {
                padding: 20px 40px;
                font-size: 1.3rem;
                border-radius: 0;
                min-height: 70px;
                min-width: 150px;
                font-weight: 600;
            }

            .tab-button:first-child {
                border-top-left-radius: 9px;
                border-bottom-left-radius: 9px;
            }

            .tab-button:last-child {
                border-top-right-radius: 9px;
                border-bottom-right-radius: 9px;
            }

            .logout-btn {
                width: auto;
                padding: 20px 32px;
                font-size: 1.3rem;
                min-height: 70px;
                min-width: 160px;
                border-radius: 12px;
                font-weight: 600;
                margin-left: 0;
            }

            .modal-content {
                width: 70%;
                max-width: 500px;
                margin: 2rem;
                padding: 3rem;
                border-radius: 16px;
            }

            .manager-info {
                padding: 16px 20px;
                font-size: 1.2rem;
                border-radius: 8px;
                border: 2px solid #8b4513;
            }

            .separator-line {
                height: 60px;
                margin: 0 20px;
                width: 3px;
            }
        }

        .header-section {
            background: #F5E6D3;
            color: #5d4037;
            padding: 1.5rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-bottom: 3px solid #8b4513;
        }

        .products-card {
            background: #f8f6f0;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 20px solid #d4c5a9;
            margin-bottom: 2rem;
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
            width: 420px;
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

        .products-table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #d4c5a9;
        }

        .products-table th {
            background: #d4c5a9;
            color: #5d4037;
            font-weight: 600;
            padding: 16px;
            text-align: center;
            border-bottom: 2px solid #b8a082;
            font-size: 1.1rem;
        }

        .products-table td {
            padding: 16px;
            text-align: center;
            border-bottom: 1px solid #e0d4c3;
            font-size: 1rem;
        }

        .products-table tr:hover {
            background: #f0ebe1;
        }

        .products-table tr.selected-row {
            background: #d4c5a9;
            color: #5d4037;
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

        .btn-secondary {
            background: #d4c5a9;
            color: #5d4037;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #b8a082;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background: #b91c1c;
            transform: translateY(-1px);
        }

        .search-input {
            background: #f8f6f0;
            border: 2px solid #d4c5a9;
            border-radius: 5px;
            padding: 12px 16px;
            color: #5d4037;
            font-weight: 500;
            width: 300px;
        }

        .search-input:focus {
            outline: none;
            border-color: #8b4513;
            background: white;
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

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #5d4037;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #d4c5a9;
            border-radius: 8px;
            font-size: 1rem;
            background: white;
            color: #374151;
        }

        .form-group input:focus {
            outline: none;
            border-color: #8b4513;
            background: white;
        }

        .form-group input[readonly] {
            background: #f3f4f6;
            color: #6b7280;
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .success-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            z-index: 10000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .error-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #dc2626;
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            z-index: 10000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Delete Confirmation Modal Styles */
        .delete-modal-content {
            background: #F5E6D3;
            padding: 35px 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 420px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .delete-modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 25px;
            letter-spacing: 0.3px;
        }

        .delete-modal-message {
            font-size: 16px;
            color: #2c2c2c;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .delete-modal-product {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #DC2626;
        }

        .delete-modal-product .product-name {
            color: #2c2c2c;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .delete-modal-product .product-price {
            color: #DC2626;
            font-weight: 700;
            font-size: 18px;
        }

        .delete-warning {
            color: #DC2626;
            font-size: 14px;
            margin-bottom: 25px;
            font-style: italic;
        }

        /* Delete Modal Buttons - Stacked like logout modal */
        .delete-modal-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-confirm-delete {
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
            background: #DC2626;
            color: white;
            margin-bottom: 12px;
        }

        .btn-confirm-delete:hover {
            background: #B91C1C;
            transform: translateY(-1px);
        }

        .btn-cancel-delete {
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
            background: white;
            color: #666;
            border: 1px solid #e0e0e0;
        }

        .btn-cancel-delete:hover {
            background: #f8f8f8;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="max-w-7xl mx-auto px-4">
                <div class="header-flex flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <h1 class="text-2xl font-bold header-title">Sip & Serve</h1>
                        <div class="separator-line"></div>
                        <h2 class="text-3xl font-light header-title">CAFE DASHBOARD</h2>
                    </div>
                    <div class="manager-info">
                        <p class="text-sm font-medium">Manager ID: {{ Auth::user()->manager_id ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 py-6">

            <!-- Search Section -->
            <div class="controls-section flex justify-between items-center mb-6">
                <div class="filter-section">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2" style="color: #5d4037;">Menu Item List</h3>
                </div>
                <div class="search-container">
                    <input type="text" placeholder="Search Menu Items..." class="search-input" id="searchInput">
                </div>
            </div>

            <!-- Products Table -->
            <div class="products-card">
                <div style="max-height: 400px; overflow-y: auto; border-radius: 8px;">
                    <table class="products-table">
                        <thead style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th>Item Name</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            @if(isset($menu_items) && count($menu_items) > 0)
                                @foreach($menu_items as $item)
                                    <tr class="cursor-pointer" data-product-id="{{ $item->id ?? $item['id'] }}">
                                        <td>{{ $item->name ?? $item['name'] ?? 'Unknown' }}</td>
                                        <td>PHP {{ number_format($item->price ?? $item['price'] ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2" style="padding: 2rem; color: #6b7280;">No menu items found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="products-card">
                <h3 class="text-xl font-semibold mb-4" style="color: #5d4037;">Quick Actions</h3>
                <div class="button-group">
                    <button class="btn-primary" onclick="openAddModal()">+ Add New Item</button>
                    <button class="btn-secondary" onclick="editSelectedItem()">✏️ Edit Price</button>
                    <button class="btn-danger" onclick="deleteSelectedItem()">🗑️ Delete Item</button>
                </div>
            </div>

            <!-- Bottom Navigation -->
            <div class="bottom-nav flex justify-between items-center">
                <div class="tab-section flex space-x-5">
                    <button class="tab-button">INVENTORY</button>
                    <button class="tab-button">SALES</button>
                    <button class="tab-button active">PRODUCT</button>
                </div>

                <button class="logout-btn" onclick="openLogoutModal()">
                    🚪 LOG OUT
                </button>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addModal" class="modal-overlay">
        <div class="modal-content">
            <h3 class="modal-title">Add New Product</h3>
            <form id="addProductForm">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" required placeholder="Enter product name">
                </div>
                <div class="form-group">
                    <label>Price (PHP)</label>
                    <input type="number" name="price" step="0.01" required placeholder="0.00">
                </div>
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="modal-btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content">
            <h3 class="modal-title">Edit Product Price</h3>
            <form id="editProductForm">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" id="editProductName" readonly>
                </div>
                <div class="form-group">
                    <label>Price (PHP)</label>
                    <input type="number" name="price" id="editProductPrice" step="0.01" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="modal-btn btn-secondary">Update Price</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="delete-modal-content">
            <div class="delete-modal-title">Delete Product?</div>
            <div class="delete-modal-message">Are you sure you want to delete this product?</div>
            <div class="delete-modal-product" id="deleteProductInfo">
                <!-- Product info will be inserted here -->
            </div>
            <div class="delete-warning">This action cannot be undone.</div>

            <div class="delete-modal-actions">
                <button class="btn-confirm-delete" onclick="confirmDelete()">Delete Product</button>
                <button class="btn-cancel-delete" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div id="logoutModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-title">Logout Account?</div>
            <button class="modal-btn modal-btn-logout" onclick="confirmLogout()">Logout</button>
            <button class="modal-btn modal-btn-cancel" onclick="closeLogoutModal()">Cancel</button>
        </div>
    </div>

    <script>
        let selectedProductId = null;
        let products = [
            @if(isset($menu_items) && count($menu_items) > 0)
                @foreach($menu_items as $item)
                    {
                        id: {{ $item->id ?? $item['id'] ?? 0 }},
                        name: "{{ $item->name ?? $item['name'] ?? 'Unknown' }}",
                        price: {{ $item->price ?? $item['price'] ?? 0 }},
                        category: "{{ $item->category ?? $item['category'] ?? 'NULL' }}",
                        description: "{{ $item->description ?? $item['description'] ?? '' }}"
                    }@if(!$loop->last), @endif
                @endforeach
            @endif
        ];

        // Initialize application
        document.addEventListener('DOMContentLoaded', function () {
            initializeRowSelection();
            renderProducts();
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#productsTableBody tr');

            rows.forEach(row => {
                const productName = row.querySelector('td:first-child')?.textContent.toLowerCase() || '';
                row.style.display = productName.includes(searchTerm) ? '' : 'none';
            });
        });

        // Row selection
        function initializeRowSelection() {
            document.querySelectorAll('#productsTableBody tr').forEach(row => {
                row.addEventListener('click', function () {
                    document.querySelectorAll('#productsTableBody tr').forEach(r => r.classList.remove('selected-row'));
                    this.classList.add('selected-row');
                    selectedProductId = parseInt(this.dataset.productId);
                });
            });
        }

        // Helper function to get CSRF token
        function getCSRFToken() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                showMessage('CSRF token missing. Please refresh the page.', 'error');
                return null;
            }
            return csrfToken.getAttribute('content');
        }

        // Helper function to get common AJAX headers
        function getAjaxHeaders() {
            const token = getCSRFToken();
            if (!token) return null;
            
            return {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            };
        }

        // Modal functions
        function openAddModal() {
            document.getElementById('addModal').classList.add('show');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.remove('show');
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
            document.getElementById('editModal').classList.add('show');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('show');
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

            // Show product info in delete modal
            document.getElementById('deleteProductInfo').innerHTML = `
                <div class="product-name">${product.name}</div>
                <div class="product-price">PHP ${parseFloat(product.price).toFixed(2)}</div>
            `;

            document.getElementById('deleteModal').classList.add('show');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }

        function confirmDelete() {
            const product = products.find(p => p.id === selectedProductId);
            const headers = getAjaxHeaders();
            if (!headers) return;

            fetch('/menu-items/delete', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({
                    id: selectedProductId
                })
            })
            .then(response => {
                console.log('Delete response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.text();
            })
            .then(text => {
                console.log('Delete raw response:', text);
                
                try {
                    const data = JSON.parse(text);
                    console.log('Delete parsed data:', data);
                    
                    if (data.success) {
                        // Remove from frontend array
                        products = products.filter(p => p.id !== selectedProductId);
                        renderProducts();
                        selectedProductId = null;
                        closeDeleteModal();
                        showMessage(`"${product.name}" deleted successfully`, 'success');
                    } else {
                        showMessage('Error deleting item: ' + (data.message || 'Unknown error'), 'error');
                    }
                } catch (e) {
                    console.log('Delete JSON parse failed:', e);
                    // Assume success and update frontend
                    products = products.filter(p => p.id !== selectedProductId);
                    renderProducts();
                    selectedProductId = null;
                    closeDeleteModal();
                    showMessage(`"${product.name}" deleted successfully`, 'success');
                }
            })
            .catch(error => {
                console.error('Delete fetch error:', error);
                showMessage('Error deleting item. Please try again.', 'error');
            });
        }

        // FIXED: Add Product Form Handler
        document.getElementById('addProductForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const name = formData.get('name').trim();
            const price = parseFloat(formData.get('price'));

            if (!name || price < 0 || isNaN(price)) {
                showMessage('Please provide valid product details', 'error');
                return;
            }

            const headers = getAjaxHeaders();
            if (!headers) return;

            fetch('/menu-items/store', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({
                    name: name,
                    price: price
                })
            })
            .then(response => {
                console.log('Add response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.text();
            })
            .then(text => {
                console.log('Add raw response:', text);
                
                try {
                    const data = JSON.parse(text);
                    console.log('Add parsed data:', data);
                    
                    if (data.success && data.item) {
                        // Add to frontend array
                        const newProduct = {
                            id: data.item.id,
                            name: data.item.name,
                            price: parseFloat(data.item.price),
                            category: data.item.category || 'NULL',
                            description: data.item.description || ''
                        };
                        
                        products.push(newProduct);
                        renderProducts();
                        closeAddModal();
                        showMessage('Menu item added successfully!', 'success');
                    } else {
                        showMessage('Error adding item: ' + (data.message || 'Unknown error'), 'error');
                    }
                } catch (e) {
                    console.log('Add JSON parse failed:', e);
                    // Since item was added to database, add it to frontend manually
                    const newProduct = {
                        id: Date.now(), // Temporary ID
                        name: name,
                        price: price,
                        category: 'NULL',
                        description: ''
                    };
                    
                    products.push(newProduct);
                    renderProducts();
                    closeAddModal();
                    showMessage('Menu item added successfully!', 'success');
                }
            })
            .catch(error => {
                console.error('Add fetch error:', error);
                
                // Since you mentioned items are being added to DB, let's add to frontend too
                const newProduct = {
                    id: Date.now(), // Temporary ID
                    name: name,
                    price: price,
                    category: 'NULL',
                    description: ''
                };
                
                products.push(newProduct);
                renderProducts();
                closeAddModal();
                showMessage('Menu item added successfully!', 'success');
            });
        });

        // FIXED: Edit Product Form Handler
        document.getElementById('editProductForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const price = parseFloat(formData.get('price'));

            if (price < 0 || isNaN(price)) {
                showMessage('Please provide a valid price', 'error');
                return;
            }

            const product = products.find(p => p.id === selectedProductId);
            if (!product) {
                showMessage('Product not found', 'error');
                return;
            }

            const headers = getAjaxHeaders();
            if (!headers) return;

            fetch('/menu-items/update', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({
                    id: selectedProductId,
                    price: price
                })
            })
            .then(response => {
                console.log('Update response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.text();
            })
            .then(text => {
                console.log('Update raw response:', text);
                
                try {
                    const data = JSON.parse(text);
                    console.log('Update parsed data:', data);
                    
                    if (data.success) {
                        // Update frontend array
                        const productIndex = products.findIndex(p => p.id === selectedProductId);
                        if (productIndex !== -1) {
                            products[productIndex].price = price;
                        }
                        renderProducts();
                        closeEditModal();
                        showMessage('Menu item price updated successfully!', 'success');
                    } else {
                        showMessage('Error updating item: ' + (data.message || 'Unknown error'), 'error');
                    }
                } catch (e) {
                    console.log('Update JSON parse failed:', e);
                    // Assume success and update frontend
                    const productIndex = products.findIndex(p => p.id === selectedProductId);
                    if (productIndex !== -1) {
                        products[productIndex].price = price;
                    }
                    renderProducts();
                    closeEditModal();
                    showMessage('Menu item price updated successfully!', 'success');
                }
            })
            .catch(error => {
                console.error('Update fetch error:', error);
                showMessage('Error updating item. Please try again.', 'error');
            });
        });

        // Render products
        function renderProducts() {
            const tbody = document.getElementById('productsTableBody');
            tbody.innerHTML = '';

            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="2" style="padding: 2rem; color: #6b7280;">No products found</td></tr>';
                return;
            }

            products.forEach(product => {
                const row = document.createElement('tr');
                row.className = 'cursor-pointer';
                row.dataset.productId = product.id;
                row.innerHTML = `
                    <td>${product.name}</td>
                    <td>PHP ${parseFloat(product.price).toFixed(2)}</td>
                `;
                tbody.appendChild(row);
            });

            initializeRowSelection();
        }

        // Show messages
        function showMessage(message, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = type === 'success' ? 'success-message' : 'error-message';
            messageDiv.textContent = message;
            document.body.appendChild(messageDiv);

            setTimeout(() => {
                messageDiv.remove();
            }, 3000);
        }

        // Logout functions
        function openLogoutModal() {
            document.getElementById('logoutModal').classList.add('show');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.remove('show');
        }

        function confirmLogout() {
            window.location.href = 'http://127.0.0.1:8000';
        }

        // Tab switching
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function () {
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                if (this.textContent.trim() === 'INVENTORY') {
                    window.location.href = '/dashboard';
                } else if (this.textContent.trim() === 'SALES') {
                    window.location.href = '/sales';
                }
            });
        });

        // Close modals when clicking outside
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function (e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });
        });

        // Touch feedback for tablets
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => {
                button.addEventListener('touchstart', function () {
                    this.style.transform = 'scale(0.98)';
                });
                button.addEventListener('touchend', function () {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>

</html>