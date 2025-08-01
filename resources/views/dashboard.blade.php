<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                flex-direction: column;
                gap: 1.5rem;
                align-items: stretch;
            }

            .filter-section {
                width: 100%;
                justify-content: center;
            }

            .filter-dropdown {
                width: 280px;
                padding: 14px 20px;
                font-size: 1rem;
                text-align: center;
            }

            .button-group {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
                width: 100%;
            }

            .button-group .btn-secondary {
                grid-column: span 2;
            }

            .btn-primary,
            .btn-secondary {
                padding: 14px 20px;
                font-size: 1rem;
                width: 100%;
                min-height: 50px;
            }

            .legend-section {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1.5rem;
                padding: 1rem;
                background: rgba(245, 230, 211, 0.3);
                border-radius: 8px;
                margin-bottom: 1.5rem;
            }

            .legend-item {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-size: 1rem;
            }

            .inventory-table th {
                padding: 16px 8px;
                font-size: 0.9rem;
                font-weight: 700;
            }

            .inventory-table td {
                padding: 16px 8px;
                font-size: 0.95rem;
            }

            .table-container {
                max-height: 55vh;
                border-radius: 12px;
                border: 2px solid #d4c5a9;
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

        .inventory-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #e0d4c3;
        }

        .inventory-table tr:hover {
            background: #f0ebe1;
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
                        <p class="text-sm font-medium">Manager ID: 10023</p>
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

                <div class="button-group flex items-center space-x-4">
                    <button class="btn-primary">🛒 Generate Shopping List</button>
                    <button class="btn-primary">+ ADD ITEM</button>
                    <button class="btn-secondary">EDIT ITEMS</button>
                </div>
                <select class="filter-dropdown">
                    <option>ALL ITEMS</option>
                    <option>BEVERAGES</option>
                    <option>FOOD</option>
                    <option>INGREDIENTS</option>
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
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="table-container">
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>ITEMS</th>
                                <th>IN</th>
                                <th>OUT</th>
                                <th>CURRENTLY IN STOCK</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryItems as $item)
                                <tr>
                                    <td class="font-medium">{{ $item->menuItem->name ?? 'Unknown Item' }}</td>
                                    <td>{{ $item->maximum_stock }}</td>
                                    <td>{{ $item->maximum_stock - $item->current_stock }}</td>
                                    <td>{{ $item->current_stock }} {{ $item->unit }}</td>
                                    <td>
                                        <div class="status-indicator status-{{ $item->status }}"></div>
                                    </td>
                                </tr>
                            @endforeach
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

        <script>
            // Modal functions
            function openLogoutModal() {
                document.getElementById('logoutModal').classList.add('show');
            }

            function closeLogoutModal() {
                document.getElementById('logoutModal').classList.remove('show');
            }

            function confirmLogout() {
                // Redirect to logout URL
                window.location.href = 'http://127.0.0.1:8000';
            }

            // Close modal when clicking outside
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
                    if (this.textContent.trim() === 'SALES') {
                        window.location.href = '/sales';
                    } else if (this.textContent.trim() === 'PRODUCT') {
                        window.location.href = '/product';
                    }
                });
            });

            // Filter dropdown functionality
            document.querySelector('.filter-dropdown').addEventListener('change', function () {
                console.log('Filter changed to:', this.value);
                // Add your filter logic here
            });
        </script>
</body>

</html>