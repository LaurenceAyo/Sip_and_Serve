<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cafe Dashboard - L' PRIMERO CAFE</title>
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

            .filter-dropdown {
                width: 320px;
                padding: 18px 24px;
                font-size: 1.2rem;
                text-align: center;
                border: 3px solid #d4c5a9;
                border-radius: 8px;
            }

            .button-group {
                display: flex;
                gap: 1.9rem;
                width: auto;
            }

            .btn-primary,
            .btn-secondary {
                padding: 18px 28px;
                font-size: 1.2rem;
                width: auto;
                min-height: 60px;
                min-width: 180px;
                border-radius: 8px;
                font-weight: 600;
            }

            .legend-section {
                flex-wrap: nowrap;
                justify-content: flex-start;
                gap: 3rem;
                padding: 1.5rem;
                background: rgba(245, 230, 211, 0.3);
                border-radius: 12px;
                margin-bottom: 2rem;
            }

            .legend-item {
                display: flex;
                align-items: center;
                gap: 1rem;
                font-size: 1.3rem;
                font-weight: 600;
            }

            .status-indicator {
                width: 28px;
                height: 28px;
                border-radius: 6px;
            }

            .table-container {
                max-height: 50vh;
                border-radius: 12px;
                border: 3px solid #d4c5a9;
            }

            .inventory-table thead th {
                padding: 20px 12px;
                font-size: 1.2rem;
                font-weight: 700;
            }

            .inventory-table td {
                padding: 18px 8px;
                font-size: 1.1rem;
                font-weight: 500;
            }

            .bottom-nav {
                display: flex;
                flex-direction: row;
                gap: 0;
                margin-top: 14rem;
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
            }

            .modal-content {
                width: 70%;
                max-width: 500px;
                margin: 2rem;
                padding: 3rem;
                border-radius: 16px;
            }

            .modal-title {
                font-size: 1.8rem;
                margin-bottom: 2.5rem;
                font-weight: 700;
            }

            .modal-btn {
                padding: 20px 32px;
                font-size: 1.4rem;
                min-height: 70px;
                border-radius: 35px;
                margin: 10px 0;
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

            /* Add Item Modal adjustments */
            .add-modal-content {
                width: 70%;
                max-width: 500px;
                padding: 3rem;
                border-radius: 16px;
            }

            .add-modal-title {
                font-size: 1.8rem;
                margin-bottom: 2.5rem;
                font-weight: 700;
            }

            .form-group label {
                font-size: 1.2rem;
                margin-bottom: 0.8rem;
                font-weight: 600;
            }

            .form-group input {
                padding: 1.2rem;
                font-size: 1.1rem;
                border: 2px solid #d1d5db;
                border-radius: 8px;
            }

            .unit-option {
                gap: 0.8rem;
                margin-bottom: 0.5rem;
            }

            .unit-option label {
                font-size: 1.1rem;
                margin: 0;
            }

            .unit-option input[type="radio"] {
                width: 20px;
                height: 20px;
            }


            /* Fix for tablet layout - replace the existing controls-section CSS */
            .controls-section {
                display: grid;
                grid-template-columns: 1fr auto auto;
                gap: 2rem;
                align-items: center;
                margin-bottom: 2rem;
            }

            .filter-section {
                grid-column: 1;
                justify-self: start;
            }

            .button-group {
                grid-column: 2;
                display: flex;
                gap: 0.3rem;
            }

            .filter-dropdown {
                grid-column: 3;
                justify-self: end;
                width: 200px;
                /* Fixed width instead of 320px */
                padding: 18px 20px;
                font-size: 1.1rem;
                text-align: center;
                border: 3px solid #d4c5a9;
                border-radius: 8px;
            }

            /* For smaller tablets */
            @media (max-width: 1000px) {
                .controls-section {
                    grid-template-columns: 1fr;
                    gap: 1.5rem;
                    text-align: center;
                }

                .filter-section,
                .button-group,
                .filter-dropdown {
                    grid-column: 1;
                    justify-self: center;
                }

                .button-group {
                    justify-content: center;
                }

                .filter-dropdown {
                    width: 100%;
                    max-width: 300px;
                }
            }


            /* For even smaller screens */
            @media (max-width: 1000px) {
                .filter-dropdown {
                    width: 200px;
                    /* Even more width for smaller screens */
                    font-size: 1rem;
                }
            }

            .btn-cancel-add,
            .btn-save-add {
                padding: 1.2rem 2rem;
                font-size: 1.1rem;
                border-radius: 8px;
                font-weight: 600;
            }

            .success-popup {
                padding: 1.5rem 3rem;
                font-size: 1.4rem;
                border-radius: 12px;
                font-weight: 700;
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

            .inventory-table th,
            .inventory-table td {
                padding: 12px 4px;
                font-size: 0.85rem;
            }

            .table-container {
                max-height: 50vh;
            }
        }

        /* Portrait tablet */
        @media (max-width: 768px) {
            .max-w-7xl {
                padding: 0 1rem;
            }

            .inventory-table th,
            .inventory-table td {
                padding: 10px 2px;
                font-size: 0.8rem;
            }

            .status-indicator {
                width: 16px;
                height: 16px;
            }

            .button-group {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .button-group .btn-secondary {
                grid-column: span 1;
            }

            .legend-section {
                gap: 1rem;
            }

            .legend-item {
                font-size: 0.9rem;
            }

            .tab-button {
                padding: 14px 16px;
                font-size: 0.9rem;
                min-height: 55px;
            }
        }

        .table-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
        }

        .table-container table {
            width: 100%;
        }

        .table-container thead th {
            position: sticky;
            top: 0;
            background-color: #d4c5a9;
            z-index: 10;
        }

        .header-section {
            background: #F5E6D3;
            color: #5d4037;
            padding: 1.5rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-bottom: 3px solid #8b4513;
        }

        .inventory-table {
            border-collapse: collapse;
            width: 100%;
            background: #f8f6f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .inventory-table th {
            background: #d4c5a9;
            color: #5d4037;
            font-weight: 600;
            padding: 12px;
            text-align: center;
            border-bottom: 2px solid #b8a082;
        }

        .status-good {
            background: #4caf50;
        }

        .status-low {
            background: #ff9800;
        }

        .status-critical {
            background: #f44336;
        }

        .status-indicator {
            width: 20px;
            height: 20px;
            border-radius: 3px;
            display: inline-block;
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

        .filter-dropdown {
            position: sticky;
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
        }

        .logout-btn:hover {
            background: #6d3410;
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

        .header-title {
            font-weight: 600;
            letter-spacing: 0.5px;
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

        .manager-info {
            background: rgba(139, 69, 19, 0.1);
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #8b4513;
        }

        .table-container {
            max-height: 400px;
            overflow-y: auto;
            border-radius: 8px;
        }

        .inventory-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .inventory-table thead {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .inventory-table thead th {
            position: sticky;
            top: 0;
            background: #d4c5a9;
            color: #5d4037;
            font-weight: 600;
            padding: 12px;
            text-align: center;
            border-bottom: 2px solid #b8a082;
            font-size: 0.85rem;
            z-index: 100;
        }

        .inventory-table td {
            padding: 12px 4px;
            text-align: center;
            border-bottom: 1px solid #e0d4c3;
            font-size: 0.85rem;
        }

        .inventory-table tr:hover {
            background: #f0ebe1;
        }

        /* Add Item Modal Styles */
        .add-modal-content {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .add-modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            font-size: 0.9rem;
            background: #f9fafb;
        }

        .form-group input::placeholder {
            color: #9ca3af;
            font-style: italic;
        }

        .form-group input:focus {
            outline: none;
            border-color: #059669;
            background: white;
        }

        .quantity-group {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .quantity-input {
            flex: 1;
        }

        .unit-selector {
            flex: 0 0 auto;
            margin-top: 0;
        }

        .unit-options {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .unit-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .unit-option input[type="radio"] {
            width: auto;
            margin: 0;
        }

        .unit-option label {
            margin: 0;
            font-size: 0.9rem;
            color: #374151;
        }

        .date-field {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .add-modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .btn-cancel-add {
            background: #9ca3af;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.25rem;
            font-weight: 500;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .btn-save-add {
            background: #059669;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.25rem;
            font-weight: 500;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .success-popup {
            position: fixed;
            top: 25%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #10b981;
            color: white;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            font-weight: 500;
            display: none;
        }


        .btn-primary,
        .btn-secondary {
            padding: 16px 18px;
            /* Slightly reduced padding */
            font-size: 1.1rem;
            min-width: 150px;
            /* Slightly reduced width */
            min-height: 60px;
            white-space: nowrap;
        }

        .filter-dropdown {
            width: 160px;
            /* Fixed width to prevent overflow */
            padding: 16px 12px;
            font-size: 1rem;
            justify-self: end;
        }
        }

        /* For smaller screens */
        @media (max-width: 1100px) {
            .controls-section {
                grid-template-columns: 1fr;
                gap: 0.8rem;
                text-align: center;
            }

            .button-group {
                justify-content: center;
                gap: 0.8rem;
            }

            .btn-primary,
            .btn-secondary {
                min-width: 130px;
                font-size: 1rem;
                padding: 14px 16px;
            }

            .filter-dropdown {
                width: 180px;
                margin: 0 auto;
                justify-self: center;
            }
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
            <!-- Controls Section -->
            <div class="controls-section flex justify-between items-center mb-6">
                <div class="filter-section flex items-center space-x-4">
                    <!-- Empty div to maintain layout -->
                </div>

                <div class="button-group flex items-center space-x-1">
                    <button class="btn-primary" onclick="openShoppingListModal()">🛒 Generate Shopping List</button>
                    <button class="btn-primary" onclick="openAddModal()">+ ADD ITEM</button>
                    <button class="btn-secondary" onclick="openEditModal()">EDIT ITEMS</button>
                </div>
                <select class="filter-dropdown">
                    <option>ALL ITEMS</option>
                    <option>INGREDIENTS</option>
                    <option>PACKAGING SUPPLIES</option>
                    <option>SERVICE ITEMS</option>
                </select>
            </div>

            <!-- Stock Level Legend -->
            <div class="flex items-center space-x-6 mb-4">
                <span class="font-semibold">CURRENT STOCK LEVEL</span>
                <div class="flex items-center space-x-2">
                    <div class="status-indicator status-good"></div>
                    <span>Good</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="status-indicator status-low"></div>
                    <span>Low</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="status-indicator status-critical"></div>
                    <span>Critical</span>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="bg-white rounded-lg shadow-lg">
                <!-- Fixed Header -->
                <div class="table-header"
                    style="background: #d4c5a9; display: flex; padding: 12px; font-weight: 600; color: #5d4037; border-bottom: 2px solid #b8a082;">
                    <div style="flex: 1; text-align: center;">ITEMS</div>
                    <div style="flex: 1; text-align: center;">IN (Stock received)</div>
                    <div style="flex: 1; text-align: center;">OUT (Stock used)</div>
                    <div style="flex: 1; text-align: center;">CURRENT INVENTORY</div>
                    <div style="flex: 1; text-align: center;">STATUS</div>
                </div>

                <!-- Scrollable Body -->
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="inventory-table" style="width: 100%;">
                        <tbody id="inventoryBody">
                            @foreach($inventory as $item)

                                <tr data-category="{{ $item->ingredient->category ?? 'ingredients' }}" style="display: flex;">
                                    <td style="flex: 1; padding: 12px; text-align: center;">
                                        {{ $item->ingredient->name ?? 'Item ' . $item->menu_item_id }}
                                    </td>

                                    <td style="flex: 1; padding: 12px; text-align: center;">
                                        {{ number_format($item->maximum_stock, 2) }}
                                    </td>

                                    <td style="flex: 1; padding: 12px; text-align: center;">
                                        {{ number_format($item->maximum_stock - $item->current_stock, 2) }}
                                    </td>

                                    <td style="flex: 1; padding: 12px; text-align: center;">
                                        {{ number_format($item->current_stock, 2) }} {{ $item->unit }}
                                    </td>

                                    <td style="flex: 1; padding: 12px; text-align: center;">
                                        <div class="status-indicator status-{{ $item->status }}"
                                            title="Current: {{ number_format($item->current_stock, 2) }} | Min: {{ number_format($item->minimum_stock, 2) }} | Max: {{ number_format($item->maximum_stock, 2) }}">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @if($inventory->isEmpty())
                                <tr style="display: flex;">
                                    <td colspan="5" style="flex: 1; padding: 20px; text-align: center; color: #6b7280;">
                                        No inventory items found
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Bottom Navigation -->
                <div class="bottom-nav flex justify-between items-center">
                    <div class="tab-section flex space-x-5">
                        <button class="tab-button active">INVENTORY</button>
                        <button class="tab-button">SALES</button>
                        <button class="tab-button">PRODUCT</button>
                    </div>

                    <button class="logout-btn" onclick="openLogoutModal()">
                        🚪 LOG OUT
                    </button>
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

        <!-- Success Popup -->
        <div id="successPopup" class="success-popup">
            ITEM ADDED TO INVENTORY
        </div>

        <!-- Shopping List Modal -->
        <div id="shoppingListModal" class="modal-overlay">
            <div class="add-modal-content" style="max-width: 500px;">
                <h3 class="add-modal-title">SHOPPING LIST</h3>
                <div style="max-height: 300px; overflow-y: auto; margin-bottom: 1.5rem;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f3f4f6;">
                                <th style="padding: 0.5rem; text-align: left; border: 1px solid #d1d5db;">Item</th>
                                <th style="padding: 0.5rem; text-align: center; border: 1px solid #d1d5db;">Current</th>
                                <th style="padding: 0.5rem; text-align: center; border: 1px solid #d1d5db;">Needed</th>
                            </tr>
                        </thead>
                        <tbody id="shoppingListBody">
                            <!-- Items will be populated here -->
                        </tbody>
                    </table>
                </div>
                <div class="add-modal-actions">
                    <button type="button" class="btn-cancel-add" onclick="closeShoppingListModal()">CANCEL</button>
                    <button type="button" class="btn-save-add" onclick="printShoppingList()">PRINT</button>
                </div>
            </div>
        </div>

        <!-- Edit Item Modal -->
        <div id="editItemModal" class="modal-overlay">
            <div class="add-modal-content">
                <h3 class="add-modal-title">EDIT ITEM DETAILS</h3>
                <form id="editItemForm">
                    <div class="form-group">
                        <label>Item Name:</label>
                        <div style="position: relative;">
                            <input type="text" id="editItemName" placeholder="Search item..."
                                oninput="searchItems(this.value)">
                            <div id="searchResults"
                                style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #d1d5db; border-top: none; max-height: 200px; overflow-y: auto; z-index: 1000; display: none;">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>New Item Name: <span style="color: #9ca3af; font-size: 0.8rem;">(optional)</span></label>
                        <input type="text" id="editNewItemName" placeholder="product name">
                    </div>

                    <div class="form-group">
                        <label>Modify Unit Type:</label>
                        <div style="display: flex; gap: 1rem; align-items: center; margin-top: 0.5rem;">
                            <input type="text" id="editUnitDisplay" value="" readonly
                                style="width: 80px; text-align: center; background: #f3f4f6; color: #6b7280; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.25rem;">
                            <span style="color: #9ca3af; font-size: 0.8rem;">Smart Unit Detection</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Modify Quantity:</label>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <button type="button" class="quantity-btn" onclick="decreaseQuantity()"
                                style="background: #d1d5db; border: none; width: 30px; height: 30px; border-radius: 4px; cursor: pointer; font-size: 1.2rem;">-</button>
                            <input type="number" id="editQuantity" value="0" min="0" step="0.1"
                                style="width: 80px; text-align: center; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.25rem;">
                            <button type="button" class="quantity-btn" onclick="increaseQuantity()"
                                style="background: #d1d5db; border: none; width: 30px; height: 30px; border-radius: 4px; cursor: pointer; font-size: 1.2rem;">+</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Critical Level: <span
                                style="color: #9ca3af; font-size: 0.8rem;">(optional)</span></label>
                        <input type="number" id="editAlertLevel" placeholder="enter critical stock number" min="1"
                            step="0.1">
                    </div>

                    <div class="add-modal-actions">
                        <button type="button" class="btn-cancel-add" onclick="closeEditModal()">CANCEL</button>
                        <button type="submit" class="btn-save-add">SAVE</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="addItemModal" class="modal-overlay">
            <div class="add-modal-content">
                <h3 class="add-modal-title">ADD ITEM</h3>
                <form id="addItemForm">
                    <div class="form-group">
                        <label>Item Name:</label>
                        <input type="text" id="itemName" placeholder="product name" required>
                    </div>

                    <div class="quantity-group">
                        <div class="quantity-input">
                            <div class="form-group">
                                <label>Add Quantity:</label>
                                <input type="number" id="quantity" value="1" min="0" step="0.1" required>
                            </div>
                        </div>

                        <div class="unit-selector">
                            <div class="form-group">
                                <label>Unit:</label>
                                <input type="text" id="unitDisplay" value="kg" readonly
                                    style="width: 80px; text-align: center; background: #f3f4f6; color: #6b7280;">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Date Added:</label>
                        <input type="text" class="date-field" id="currentDate" readonly>
                    </div>

                    <div class="add-modal-actions">
                        <button type="button" class="btn-cancel-add" onclick="closeAddModal()">CANCEL</button>
                        <button type="submit" class="btn-save-add">SAVE</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Admin Panel Access (only for laurenceayo7@gmail.com) -->
        @if(auth()->user()->email === 'laurenceayo7@gmail.com')
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-shield-alt me-2"></i>
                                Administrator Panel
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('admin.users') }}" class="text-decoration-none">
                                        <div class="card h-100 border-0 shadow-sm admin-card">
                                            <div class="card-body text-center">
                                                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                                <h6>User Management</h6>
                                                <p class="text-muted small mb-0">Manage system users, roles & permissions
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="#" class="text-decoration-none">
                                        <div class="card h-100 border-0 shadow-sm admin-card">
                                            <div class="card-body text-center">
                                                <i class="fas fa-shield-check fa-3x text-success mb-3"></i>
                                                <h6>Security Logs</h6>
                                                <p class="text-muted small mb-0">View system access logs</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .admin-card {
                    transition: all 0.3s ease;
                    cursor: pointer;
                }

                .admin-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
                }
            </style>
        @endif

        <script>
            //for filtering items
            document.querySelector('.filter-dropdown').addEventListener('change', function () {
                const filter = this.value.toLowerCase().replace(/\s+/g, '-');
                const rows = document.querySelectorAll('.inventory-table tbody tr');

                rows.forEach(row => {
                    if (filter === 'all-items') {
                        row.style.display = 'flex';
                    } else {
                        const category = row.dataset.category;
                        row.style.display = category === filter ? 'flex' : 'none';
                    }
                });
            });

            // Search functionality for edit modal
            let ingredientsData = [
                @foreach($inventory as $item)
                    {
                                id: {{ $item->id }},
                                menu_item_id: {{ $item->menu_item_id }},
                                name: {!! json_encode($item->ingredient->name ?? 'Unknown') !!},
                                unit: {!! json_encode($item->unit ?? 'units') !!},
                                current_stock: {{ $item->current_stock ?? 0 }},
                                minimum_stock: {{ $item->minimum_stock ?? 0 }},
                                maximum_stock: {{ $item->maximum_stock ?? 0 }}
                    }@if(!$loop->last), @endif
                @endforeach
];

            console.log('Ingredients data loaded:', ingredientsData);

            function searchItems(query) {
                try {
                    const resultsContainer = document.getElementById('searchResults');

                    if (!resultsContainer) {
                        console.error('Search results container not found');
                        return;
                    }

                    if (query.length === 0) {
                        resultsContainer.style.display = 'none';
                        return;
                    }

                    const filteredItems = ingredientsData.filter(function (item) {
                        return item.name.toLowerCase().includes(query.toLowerCase());
                    });

                    if (filteredItems.length === 0) {
                        resultsContainer.innerHTML = '<div style="padding: 0.75rem; color: #6b7280;">No items found</div>';
                        resultsContainer.style.display = 'block';
                        return;
                    }

                    resultsContainer.innerHTML = filteredItems.map(function (item) {
                        return '<div onclick="selectItem(\'' + item.name.replace(/'/g, "\\'") + '\', \'' +
                            item.unit + '\', ' + item.current_stock + ', ' + item.minimum_stock + ')" ' +
                            'style="padding: 0.75rem; cursor: pointer; border-bottom: 1px solid #f3f4f6;">' +
                            item.name + ' - Current: ' + item.current_stock + ' ' + item.unit + '</div>';
                    }).join('');

                    resultsContainer.style.display = 'block';

                } catch (error) {
                    console.error('Error in searchItems:', error);
                }
            }

            // Test route accessibility
            function testRoutes() {
                console.log('Testing route accessibility...');

                // You can uncomment these to test if routes work
                // fetch('/sales').then(response => console.log('Sales route status:', response.status));
                // fetch('/product').then(response => console.log('Product route status:', response.status));
            }

            function selectItem(name, unit, currentStock, minimumStock = null) {
                document.getElementById('editItemName').value = name;
                document.getElementById('editQuantity').value = currentStock;
                document.getElementById('editAlertLevel').value = minimumStock || '';
                document.getElementById('searchResults').style.display = 'none';

                // Auto-detect and set unit for edit modal
                const detectedUnit = detectUnit(name) || unit;
                document.getElementById('editUnitDisplay').value = detectedUnit;
            }

            // Update unit when editing item name changes
            document.getElementById('editItemName').addEventListener('input', function () {
                const unit = detectUnit(this.value);
                document.getElementById('editUnitDisplay').value = unit;
            });

            // Hide search results when clicking outside
            document.addEventListener('click', function (e) {
                if (!e.target.closest('#editItemName') && !e.target.closest('#searchResults')) {
                    document.getElementById('searchResults').style.display = 'none';
                }
            });

            function openEditModal() {
                document.getElementById('editItemModal').classList.add('show');
            }

            function closeEditModal() {
                document.getElementById('editItemModal').classList.remove('show');
                document.getElementById('editItemForm').reset();
            }

            function increaseQuantity() {
                const input = document.getElementById('editQuantity');
                input.value = parseFloat(input.value) + 1;
            }

            function decreaseQuantity() {
                const input = document.getElementById('editQuantity');
                const currentValue = parseFloat(input.value);
                if (currentValue > 0) {
                    input.value = currentValue - 1;
                }
            }

            // Edit item form submission
            document.getElementById('editItemForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const itemName = document.getElementById('editItemName').value;
                const newQuantity = parseFloat(document.getElementById('editQuantity').value);
                const minimumStock = document.getElementById('editAlertLevel').value ? parseFloat(document.getElementById('editAlertLevel').value) : null;

                if (itemName.trim()) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        console.error('CSRF token not found');
                        alert('CSRF token missing. Please refresh the page.');
                        return;
                    }

                    // Send AJAX request to Laravel backend with correct field names
                    fetch('/ingredients/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                        },
                        body: JSON.stringify({
                            name: itemName,
                            current_stock: newQuantity, // Changed from stock_quantity
                            minimum_stock: minimumStock // Changed from critical_level
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateTableRow(itemName, newQuantity, minimumStock);
                                closeEditModal();
                                showSuccessPopup('ITEM UPDATED SUCCESSFULLY');
                            } else {
                                alert('Error updating item: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            alert('Network error. Check console for details.');
                        });
                } else {
                    alert('Please select an item first');
                }
            });

            function updateTableRow(itemName, newQuantity, minimumStock = null) {
                const rows = document.querySelectorAll('.inventory-table tbody tr');
                rows.forEach(row => {
                    const nameCell = row.querySelector('td:first-child');
                    if (nameCell && nameCell.textContent.trim() === itemName) {
                        const cells = row.querySelectorAll('td');

                        // Update the current inventory column (4th column)
                        const updatedUnit = document.getElementById('editUnitDisplay').value || 'units';
                        cells[3].textContent = `${newQuantity.toFixed(2)} ${updatedUnit}`;

                        // Update status indicator
                        const statusCell = cells[4].querySelector('.status-indicator');
                        const newStatus = getStockLevel(newQuantity, minimumStock);
                        statusCell.className = `status-indicator ${newStatus}`;
                        statusCell.title = `Current: ${newQuantity} | Min: ${minimumStock || 'Auto'}`;
                    }
                });
            }

            function showSuccessPopup(message) {
                const popup = document.getElementById('successPopup');
                popup.textContent = message;
                popup.style.display = 'block';
                setTimeout(() => {
                    popup.style.display = 'none';
                }, 2000);
            }

            // Close edit modal when clicking outside
            document.getElementById('editItemModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeEditModal();
                }
            });

            // Auto-detect unit based on ingredient name
            const unitMapping = {
                // Dry ingredients - grams
                'flour': 'grams', 'sugar': 'grams', 'salt': 'grams', 'coffee': 'grams', 'tea': 'grams', 'cocoa': 'grams',
                'beans': 'grams', 'powder': 'grams', 'spice': 'grams', 'garlic': 'grams', 'lemongrass': 'grams',
                'butter': 'grams', 'cheese': 'grams', 'meat': 'grams', 'chicken': 'grams', 'fish': 'grams',
                'potato': 'grams', 'vegetables': 'grams', 'chocolate': 'grams', 'tofu': 'grams', 'pasta': 'grams',
                'crab': 'grams', 'rice': 'grams', 'kaya': 'grams', 'spread': 'grams',

                // Liquids - ml
                'water': 'ml', 'milk': 'ml', 'oil': 'ml', 'juice': 'ml', 'syrup': 'ml',
                'sauce': 'ml', 'liquid': 'ml', 'cream': 'ml', 'honey': 'ml', 'pepper': 'ml',
                'soy': 'ml', 'yogurt': 'ml',

                // Countable items - pieces
                'eggs': 'pieces', 'cups': 'pieces', 'plates': 'pieces', 'napkins': 'pieces', 'straws': 'pieces',
                'bags': 'pieces', 'bottles': 'pieces', 'cans': 'pieces', 'boxes': 'pieces', 'containers': 'pieces',
                'utensils': 'pieces', 'meatballs': 'pieces', 'bread': 'slices'
            };

            function detectUnit(itemName) {
                const name = itemName.toLowerCase();

                // Check for exact matches first
                if (unitMapping[name]) {
                    return unitMapping[name];
                }

                // Check for partial matches
                for (let ingredient in unitMapping) {
                    if (name.includes(ingredient)) {
                        return unitMapping[ingredient];
                    }
                }

                // Default to grams
                return 'grams';
            }

            // Update unit when item name changes
            document.getElementById('itemName').addEventListener('input', function () {
                const unit = detectUnit(this.value);
                document.getElementById('unitDisplay').value = unit;
            });

            // Set current date
            function setCurrentDate() {
                const today = new Date();
                const day = String(today.getDate()).padStart(2, '0');
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const year = today.getFullYear();
                document.getElementById('currentDate').value = `${day}/${month}/${year}`;
            }

            // Set date when page loads
            document.addEventListener('DOMContentLoaded', setCurrentDate);

            // Shopping List Modal functions
            function openShoppingListModal() {
                generateShoppingList();
                document.getElementById('shoppingListModal').classList.add('show');
            }

            function closeShoppingListModal() {
                document.getElementById('shoppingListModal').classList.remove('show');
            }

            function generateShoppingList() {
                const tbody = document.getElementById('shoppingListBody');
                tbody.innerHTML = '';

                // Get low stock items from current inventory
                const rows = document.querySelectorAll('.inventory-table tbody tr');
                const lowStockItems = [];

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 4) {
                        const itemName = cells[0].textContent.trim();
                        const currentStock = parseFloat(cells[3].textContent.trim().split(' ')[0]);
                        const unit = cells[3].textContent.trim().split(' ')[1];
                        const statusIndicator = cells[4].querySelector('.status-indicator');

                        // Check if item is low or critical stock
                        if (statusIndicator && (statusIndicator.classList.contains('status-low') || statusIndicator.classList.contains('status-critical'))) {
                            const neededAmount = statusIndicator.classList.contains('status-critical') ?
                                Math.max(currentStock * 3, 10) : Math.max(currentStock * 2, 5);
                            lowStockItems.push({
                                name: itemName,
                                current: currentStock,
                                needed: neededAmount,
                                unit: unit
                            });
                        }
                    }
                });

                if (lowStockItems.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 1rem; color: #6b7280;">No items need restocking</td></tr>';
                    return;
                }

                lowStockItems.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td style="padding: 0.5rem; border: 1px solid #d1d5db;">${item.name}</td>
                    <td style="padding: 0.5rem; text-align: center; border: 1px solid #d1d5db;">${item.current} ${item.unit}</td>
                    <td style="padding: 0.5rem; text-align: center; border: 1px solid #d1d5db;">${item.needed} ${item.unit}</td>
                `;
                    tbody.appendChild(row);
                });
            }

            function printShoppingList() {
                const printWindow = window.open('', '_blank');
                const shoppingListContent = document.getElementById('shoppingListBody').innerHTML;

                printWindow.document.write(`
                <html>
                <head>
                    <title>Shopping List - Sip & Serve</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h1 { color: #8b4513; text-align: center; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { padding: 10px; border: 1px solid #d1d5db; text-align: left; }
                        th { background: #f3f4f6; font-weight: bold; }
                        .date { text-align: right; margin-bottom: 20px; color: #6b7280; }
                    </style>
                </head>
                <body>
                    <div class="date">Generated: ${new Date().toLocaleDateString()}</div>
                    <h1>Sip & Serve - Shopping List</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Current Stock</th>
                                <th>Amount Needed</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${shoppingListContent}
                        </tbody>
                    </table>
                </body>
                </html>
            `);

                printWindow.document.close();
                printWindow.print();
            }

            // Close shopping list modal when clicking outside
            document.getElementById('shoppingListModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeShoppingListModal();
                }
            });

            function openAddModal() {
                document.getElementById('addItemModal').classList.add('show');
            }

            function closeAddModal() {
                document.getElementById('addItemModal').classList.remove('show');
                document.getElementById('addItemForm').reset();
                document.getElementById('quantity').value = '1';
            }

            function getStockLevel(currentStock, minimumStock = null) {
                if (minimumStock !== null && minimumStock > 0) {
                    if (currentStock <= minimumStock) {
                        return 'status-critical';
                    } else if (currentStock <= (minimumStock * 1.5)) {
                        return 'status-low';
                    }
                    return 'status-good';
                }

                // Fallback logic when minimum stock is not set
                if (currentStock <= 10) {
                    return 'status-critical';
                } else if (currentStock <= 25) {
                    return 'status-low';
                }
                return 'status-good';
            }

            // Add item form submission
            document.getElementById('addItemForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const itemName = document.getElementById('itemName').value;
                const quantity = parseFloat(document.getElementById('quantity').value);
                const unit = document.getElementById('unitDisplay').value;

                if (itemName.trim()) {
                    const tbody = document.getElementById('inventoryBody');
                    const row = document.createElement('tr');
                    row.setAttribute('data-category', 'ingredients');
                    row.style.display = 'flex';

                    row.innerHTML = `
                    <td style="flex: 1; padding: 12px; text-align: center;">${itemName}</td>
                    <td style="flex: 1; padding: 12px; text-align: center;">${quantity.toFixed(2)}</td>
                    <td style="flex: 1; padding: 12px; text-align: center;">0</td>
                    <td style="flex: 1; padding: 12px; text-align: center;">${quantity.toFixed(2)} ${unit}</td>
                    <td style="flex: 1; padding: 12px; text-align: center;">
                        <div class="status-indicator ${getStockLevel(quantity)}"></div>
                    </td>
                `;

                    tbody.appendChild(row);
                    closeAddModal();
                    showSuccessPopup();
                }
            });

            function showSuccessPopup() {
                const popup = document.getElementById('successPopup');
                popup.style.display = 'block';
                setTimeout(() => {
                    popup.style.display = 'none';
                }, 2000);
            }

            // Close add modal when clicking outside
            document.getElementById('addItemModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeAddModal();
                }
            });

            // Modal functions
            function openLogoutModal() {
                document.getElementById('logoutModal').classList.add('show');
            }

            function closeLogoutModal() {
                document.getElementById('logoutModal').classList.remove('show');
            }

            function confirmLogout() {
                // Create a form and submit it to the logout route
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/logout';

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }

            // Close modal when clicking outside
            document.getElementById('logoutModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeLogoutModal();
                }
            });

            // Tab switching functionality
            document.addEventListener('DOMContentLoaded', function () {
                console.log('DOM loaded, setting up navigation...');

                // Get all tab buttons
                const tabButtons = document.querySelectorAll('.tab-button');
                console.log('Found tab buttons:', tabButtons.length);

                // Add click listeners to each button
                tabButtons.forEach(function (button, index) {
                    console.log('Button ' + index + ':', button.textContent.trim());

                    button.addEventListener('click', function (event) {
                        event.preventDefault();

                        const buttonText = this.textContent.trim();
                        console.log('Clicked button:', buttonText);

                        // Update active state
                        tabButtons.forEach(function (btn) {
                            btn.classList.remove('active');
                        });
                        this.classList.add('active');

                        // Navigate based on button text
                        if (buttonText === 'SALES') {
                            console.log('Navigating to /sales');
                            window.location.href = '/sales';
                        } else if (buttonText === 'PRODUCT') {
                            console.log('Navigating to /product');
                            window.location.href = '/product';
                        } else {
                            console.log('Staying on current page for:', buttonText);
                        }
                    });
                });
            });
            // Backup method using direct button selection
            function setupDirectButtonHandlers() {
                console.log('Setting up direct button handlers...');

                const buttons = document.querySelectorAll('.tab-button');

                if (buttons.length >= 3) {
                    // Sales button (second button)
                    if (buttons[1]) {
                        buttons[1].onclick = function (e) {
                            e.preventDefault();
                            console.log('Direct sales click');
                            window.location.href = '/sales';
                        };
                    }

                    // Product button (third button)  
                    if (buttons[2]) {
                        buttons[2].onclick = function (e) {
                            e.preventDefault();
                            console.log('Direct product click');
                            window.location.href = '/product';
                        };
                    }
                }
            }
            setTimeout(setupDirectButtonHandlers, 500);

            // Filter dropdown functionality
            document.querySelector('.filter-dropdown').addEventListener('change', function () {
                console.log('Filter changed to:', this.value);
                // Add your filter logic here
            });
        </script>
</body>

</html>