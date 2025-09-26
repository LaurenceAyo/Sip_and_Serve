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
                max-width: 600px;
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
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 550px;
            max-height: 85vh;
            overflow-y: auto;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 25px;
            letter-spacing: 0.3px;
            text-align: center;
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

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #d4c5a9;
            border-radius: 8px;
            font-size: 1rem;
            background: white;
            color: #374151;
        }

        .form-group input:focus, .form-group textarea:focus {
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

        /* Ingredient Section Styles */
        .ingredients-section {
            background: rgba(139, 69, 19, 0.05);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .ingredients-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .ingredient-row {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            align-items: center;
        }

        .ingredient-select {
            flex: 1;
            padding: 10px;
            border: 2px solid #d4c5a9;
            border-radius: 6px;
            background: white;
            color: #374151;
            font-size: 0.95rem;
        }

        .ingredient-quantity {
            width: 120px;
            padding: 10px;
            border: 2px solid #d4c5a9;
            border-radius: 6px;
            background: white;
            font-size: 0.95rem;
        }

        .btn-remove {
            padding: 10px 15px;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-remove:hover {
            background: #b91c1c;
        }

        .btn-add-ingredient {
            padding: 8px 16px;
            background: #8b4513;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-add-ingredient:hover {
            background: #6d3410;
        }

        .cost-display {
            background: rgba(139, 69, 19, 0.1);
            padding: 10px;
            border-radius: 6px;
            margin-top: 1rem;
            font-weight: 600;
            color: #5d4037;
            text-align: center;
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
                                <th>Cost</th>
                                <th>Ingredients</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            @if(isset($menu_items) && count($menu_items) > 0)
                                @foreach($menu_items as $item)
                                    <tr class="cursor-pointer" data-product-id="{{ $item->id ?? $item['id'] }}">
                                        <td>{{ $item->name ?? $item['name'] ?? 'Unknown' }}</td>
                                        <td>PHP {{ number_format($item->price ?? $item['price'] ?? 0, 2) }}</td>
                                        <td>PHP {{ number_format($item->cost ?? $item['cost'] ?? 0, 2) }}</td>
                                        <td>{{ isset($item->ingredients) ? count($item->ingredients) : 0 }} items</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" style="padding: 2rem; color: #6b7280;">No menu items found</td>
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
                    <button class="btn-secondary" onclick="editSelectedItem()">✏️ Edit Item</button>
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
                    <label>Category</label>
                    <input type="text" name="category" placeholder="e.g., Coffee, Pastries">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="2" placeholder="Product description"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Price (PHP)</label>
                    <input type="number" name="price" step="0.01" required placeholder="0.00">
                </div>

                <div class="ingredients-section">
                    <div class="ingredients-header">
                        <label style="margin: 0; font-weight: 600; color: #5d4037;">Ingredients</label>
                        <button type="button" class="btn-add-ingredient" onclick="addIngredientRow('add')">+ Add</button>
                    </div>
                    <div id="addIngredientsContainer"></div>
                    <div id="addCostDisplay" class="cost-display" style="display: none;">
                        Calculated Cost: PHP <span id="addCalculatedCost">0.00</span>
                    </div>
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
            <h3 class="modal-title">Edit Product</h3>
            <form id="editProductForm">
                <input type="hidden" id="editProductId">
                
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" id="editProductName" required>
                </div>
                
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" id="editProductCategory">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="editProductDescription" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Price (PHP)</label>
                    <input type="number" name="price" id="editProductPrice" step="0.01" required>
                </div>

                <div class="ingredients-section">
                    <div class="ingredients-header">
                        <label style="margin: 0; font-weight: 600; color: #5d4037;">Ingredients</label>
                        <button type="button" class="btn-add-ingredient" onclick="addIngredientRow('edit')">+ Add</button>
                    </div>
                    <div id="editIngredientsContainer"></div>
                    <div id="editCostDisplay" class="cost-display" style="display: none;">
                        Calculated Cost: PHP <span id="editCalculatedCost">0.00</span>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="modal-btn btn-secondary">Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="delete-modal-content">
            <div class="delete-modal-title">Delete Product?</div>
            <div class="delete-modal-message">Are you sure you want to delete this product?</div>
            <div class="delete-modal-product" id="deleteProductInfo"></div>
            <div class="delete-warning">This action cannot be undone.</div>

            <div class="delete-modal-actions">
                <button class="btn-confirm-delete" onclick="confirmDelete()">Delete Product</button>
                <button class="btn-cancel-delete" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div id="logoutModal" class="modal-overlay">
        <div class="modal-content" style="max-width: 420px;">
            <div class="modal-title">Logout Account?</div>
            <button class="modal-btn modal-btn-logout" onclick="confirmLogout()">Logout</button>
            <button class="modal-btn modal-btn-cancel" onclick="closeLogoutModal()">Cancel</button>
        </div>
    </div>

    <script>
        let selectedProductId = null;
        const ingredients = @json($ingredients ?? []);
        let products = [
            @if(isset($menu_items) && count($menu_items) > 0)
                @foreach($menu_items as $item)
                    {
                        id: {{ $item->id ?? $item['id'] ?? 0 }},
                        name: "{{ $item->name ?? $item['name'] ?? 'Unknown' }}",
                        price: {{ $item->price ?? $item['price'] ?? 0 }},
                        cost: {{ $item->cost ?? $item['cost'] ?? 0 }},
                        category: "{{ $item->category ?? $item['category'] ?? 'NULL' }}",
                        description: "{{ $item->description ?? $item['description'] ?? '' }}",
                        ingredients: @json($item->ingredients ?? [])
                    }@if(!$loop->last), @endif
                @endforeach
            @endif
        ];

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            initializeRowSelection();
            addIngredientRow('add');
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

        // Ingredient Management
        function addIngredientRow(mode) {
            const container = document.getElementById(mode + 'IngredientsContainer');
            const row = document.createElement('div');
            row.className = 'ingredient-row';
            row.innerHTML = `
                <select class="ingredient-select" onchange="calculateCost('${mode}')" required>
                    <option value="">Select Ingredient</option>
                    ${ingredients.map(ing => `
                        <option value="${ing.id}" data-cost="${ing.cost_per_unit}">
                            ${ing.name} (${ing.stock_quantity} ${ing.unit})
                        </option>
                    `).join('')}
                </select>
                <input type="number" step="0.01" placeholder="Qty" class="ingredient-quantity" 
                    onchange="calculateCost('${mode}')" required>
                <button type="button" class="btn-remove" onclick="removeIngredientRow(this, '${mode}')">Remove</button>
            `;
            container.appendChild(row);
            document.getElementById(mode + 'CostDisplay').style.display = 'block';
        }

        function removeIngredientRow(btn, mode) {
            btn.parentElement.remove();
            calculateCost(mode);
            const container = document.getElementById(mode + 'IngredientsContainer');
            if (container.children.length === 0) {
                document.getElementById(mode + 'CostDisplay').style.display = 'none';
            }
        }

        function calculateCost(mode) {
            const rows = document.querySelectorAll(`#${mode}IngredientsContainer .ingredient-row`);
            let totalCost = 0;
            
            rows.forEach(row => {
                const select = row.querySelector('.ingredient-select');
                const quantity = parseFloat(row.querySelector('.ingredient-quantity').value) || 0;
                const option = select.options[select.selectedIndex];
                const cost = parseFloat(option.dataset.cost) || 0;
                totalCost += cost * quantity;
            });
            
            document.getElementById(mode + 'CalculatedCost').textContent = totalCost.toFixed(2);
        }

        function getIngredients(mode) {
            const rows = document.querySelectorAll(`#${mode}IngredientsContainer .ingredient-row`);
            const ingredientsData = [];
            
            rows.forEach(row => {
                const select = row.querySelector('.ingredient-select');
                const quantity = row.querySelector('.ingredient-quantity');
                if (select.value && quantity.value) {
                    ingredientsData.push({
                        id: parseInt(select.value),
                        quantity: parseFloat(quantity.value)
                    });
                }
            });
            
            return ingredientsData;
        }

        // Helper functions
        function getCSRFToken() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                showMessage('CSRF token missing. Please refresh the page.', 'error');
                return null;
            }
            return csrfToken.getAttribute('content');
        }

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
            document.getElementById('addIngredientsContainer').innerHTML = '';
            addIngredientRow('add');
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

            document.getElementById('editProductId').value = product.id;
            document.getElementById('editProductName').value = product.name;
            document.getElementById('editProductCategory').value = product.category || '';
            document.getElementById('editProductDescription').value = product.description || '';
            document.getElementById('editProductPrice').value = product.price;
            
            document.getElementById('editIngredientsContainer').innerHTML = '';
            if (product.ingredients && product.ingredients.length > 0) {
                product.ingredients.forEach(ing => {
                    const container = document.getElementById('editIngredientsContainer');
                    const row = document.createElement('div');
                    row.className = 'ingredient-row';
                    row.innerHTML = `
                        <select class="ingredient-select" onchange="calculateCost('edit')" required>
                            <option value="">Select Ingredient</option>
                            ${ingredients.map(i => `
                                <option value="${i.id}" data-cost="${i.cost_per_unit}" ${i.id === ing.id ? 'selected' : ''}>
                                    ${i.name} (${i.stock_quantity} ${i.unit})
                                </option>
                            `).join('')}
                        </select>
                        <input type="number" step="0.01" value="${ing.pivot?.quantity_needed || 0}" 
                            placeholder="Qty" class="ingredient-quantity" onchange="calculateCost('edit')" required>
                        <button type="button" class="btn-remove" onclick="removeIngredientRow(this, 'edit')">Remove</button>
                    `;
                    container.appendChild(row);
                });
                calculateCost('edit');
            } else {
                addIngredientRow('edit');
            }
            
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

            document.getElementById('deleteProductInfo').innerHTML = `
                <div class="product-name">${product.name}</div>
                <div class="product-price">PHP ${parseFloat(product.price).toFixed(2)}</div>
            `;

            document.getElementById('deleteModal').classList.add('show');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }

        async function confirmDelete() {
            const headers = getAjaxHeaders();
            if (!headers) return;

            try {
                const response = await fetch(`/product/${selectedProductId}`, {
                    method: 'DELETE',
                    headers: headers
                });

                const result = await response.json();
                
                if (result.success) {
                    products = products.filter(p => p.id !== selectedProductId);
                    renderProducts();
                    selectedProductId = null;
                    closeDeleteModal();
                    showMessage('Product deleted successfully', 'success');
                } else {
                    showMessage('Error: ' + result.message, 'error');
                }
            } catch (error) {
                showMessage('Error deleting product', 'error');
            }
        }

        // Form handlers
        document.getElementById('addProductForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const ingredientsData = getIngredients('add');

            if (ingredientsData.length === 0) {
                showMessage('Please add at least one ingredient', 'error');
                return;
            }

            const data = {
                name: formData.get('name'),
                category: formData.get('category') || 'NULL',
                description: formData.get('description') || '',
                price: parseFloat(formData.get('price')),
                ingredients: ingredientsData
            };

            const headers = getAjaxHeaders();
            if (!headers) return;

            try {
                const response = await fetch('/product/store', {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (result.success) {
                    showMessage('Product added successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage('Error: ' + result.message, 'error');
                }
            } catch (error) {
                showMessage('Error adding product', 'error');
            }
        });

        document.getElementById('editProductForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const ingredientsData = getIngredients('edit');

            if (ingredientsData.length === 0) {
                showMessage('Please add at least one ingredient', 'error');
                return;
            }

            const data = {
                name: formData.get('name'),
                category: formData.get('category') || 'NULL',
                description: formData.get('description') || '',
                price: parseFloat(formData.get('price')),
                ingredients: ingredientsData
            };

            const headers = getAjaxHeaders();
            if (!headers) return;

            try {
                const response = await fetch(`/product/${selectedProductId}`, {
                    method: 'PUT',
                    headers: headers,
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (result.success) {
                    showMessage('Product updated successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage('Error: ' + result.message, 'error');
                }
            } catch (error) {
                showMessage('Error updating product', 'error');
            }
        });

        // Render products
        function renderProducts() {
            const tbody = document.getElementById('productsTableBody');
            tbody.innerHTML = '';

            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="padding: 2rem; color: #6b7280;">No products found</td></tr>';
                return;
            }

            products.forEach(product => {
                const row = document.createElement('tr');
                row.className = 'cursor-pointer';
                row.dataset.productId = product.id;
                row.innerHTML = `
                    <td>${product.name}</td>
                    <td>PHP ${parseFloat(product.price).toFixed(2)}</td>
                    <td>PHP ${parseFloat(product.cost).toFixed(2)}</td>
                    <td>${product.ingredients?.length || 0} items</td>
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