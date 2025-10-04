<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard - L' PRIMERO CAFE</title>
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

            .filter-dropdown {
                width: 200px;
                padding: 18px 24px;
                font-size: 1.2rem;
                text-align: center;
                border: 3px solid #d4c5a9;
                border-radius: 8px;
            }

            .button-group {
                display: flex;
                gap: 1.5rem;
                width: auto;
            }

            .btn-primary {
                padding: 18px 28px;
                font-size: 1.2rem;
                width: auto;
                min-height: 60px;
                min-width: 200px;
                border-radius: 8px;
                font-weight: 600;
            }

            .metrics-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 2rem;
                margin-bottom: 2rem;
            }

            .sales-card {
                padding: 2rem;
                border-width: 20px;
                border-radius: 16px;
            }

            .metric-card {
                padding: 2rem;
                margin-bottom: 0;
                border-radius: 8px;
            }

            .metric-card h4 {
                font-size: 1.4rem;
                margin-bottom: 1.5rem;
                font-weight: 600;
            }

            .metric-card p {
                font-size: 2rem;
                font-weight: 700;
            }

            .top-items {
                grid-template-columns: repeat(4, 1fr);
                gap: 1.5rem;
            }

            .item-card {
                padding: 2rem;
                border-radius: 12px;
            }

            .item-image {
                width: 90px;
                height: 90px;
                margin-bottom: 1.5rem;
                border-radius: 12px;
            }

            .item-card h5 {
                font-size: 1.2rem;
                margin-bottom: 0.8rem;
                font-weight: 600;
            }

            .item-card p {
                font-size: 1rem;
                color: #6b7280;
            }

            .bottom-nav {
                flex-direction: row;
                gap: 0;
                margin-top: 9rem;
                padding: 0;
                background: none;
                border-radius: 0;
                justify-content: space-between;
                align-items: center;
            }

            .tab-section {
                bottom: 20px;
                position: fixed;
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
                position: fixed;
                right: 17px;
                bottom: 22px;
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

        /* Export Report Modal Styles */
        .export-modal-content {
            background: white;
            padding: 3rem;
            border-radius: 16px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            max-height: 80vh;
            overflow-y: auto;
        }

        .export-modal-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #5d4037;
            margin-bottom: 2rem;
            text-align: center;
            border-bottom: 3px solid #d4c5a9;
            padding-bottom: 1rem;
        }

        .report-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8f6f0;
            border-radius: 12px;
            border: 2px solid #d4c5a9;
        }

        .report-section h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #8b4513;
            margin-bottom: 1rem;
            border-bottom: 2px solid #d4c5a9;
            padding-bottom: 0.5rem;
        }

        .report-data {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .report-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #d4c5a9;
            text-align: center;
        }

        .report-item-label {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .report-item-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: #5d4037;
        }

        .top-items-list {
            list-style: none;
            padding: 0;
        }

        .top-items-list li {
            background: white;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            border: 1px solid #d4c5a9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-items-list li:last-child {
            margin-bottom: 0;
        }

        .item-rank {
            background: #8b4513;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 1rem;
        }

        .item-details {
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-name {
            font-weight: 600;
            color: #5d4037;
        }

        .item-sales {
            color: #8b4513;
            font-weight: 500;
        }

        .export-modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 2px solid #d4c5a9;
        }

        .btn-print {
            background: #059669;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1.1rem;
            min-width: 120px;
        }

        .btn-print:hover {
            background: #047857;
        }

        .btn-close {
            background: #6b7280;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1.1rem;
            min-width: 120px;
        }

        .btn-close:hover {
            background: #4b5563;
        }

        .report-date {
            text-align: center;
            font-size: 1.1rem;
            color: #6b7280;
            margin-bottom: 2rem;
            font-style: italic;
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
                        <p class="text-sm font-medium">Manager ID: <?php echo e(Auth::user()->manager_id ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 py-6">

            <!-- Export Report Modal -->
            <div id="exportReportModal" class="modal-overlay">
                <div class="export-modal-content">
                    <h2 class="export-modal-title">📊 DAILY SALES REPORT</h2>
                    <div class="report-date" id="reportDate"></div>

                    <!-- Sales Summary Section -->
                    <div class="report-section">
                        <h3>📈 Sales Summary</h3>
                        <div class="report-data">
                            <div class="report-item">
                                <div class="report-item-label">Today's Total</div>
                                <div class="report-item-value" id="reportTotalSales">PHP
                                    <?php echo e(number_format($todaysSales->total_sales, 2)); ?>

                                </div>
                            </div>
                            <div class="report-item">
                                <div class="report-item-label">Orders Completed</div>
                                <div class="report-item-value" id="reportOrdersCompleted">
                                    <?php echo e($todaysSales->total_orders); ?>

                                </div>
                            </div>
                        </div>
                        <div class="report-data">
                            <div class="report-item">
                                <div class="report-item-label">Items Sold</div>
                                <div class="report-item-value" id="reportItemsSold">
                                    <?php echo e($TopItems->sum('quantity')); ?>

                                </div>
                            </div>
                            <div class="report-item">
                                <div class="report-item-label">Average Order</div>
                                <div class="report-item-value" id="reportAverageOrder">PHP
                                    <?php echo e(number_format($averageOrder, 2)); ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Selling Items Section in Export Modal -->
                    <div class="report-section">
                        <h3>🏆 Top Selling Items</h3>
                        <ul class="top-items-list">
                            <?php $__currentLoopData = $TopItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <div class="item-rank"><?php echo e($index + 1); ?></div>
                                    <div class="item-details">
                                        <span class="item-name"><?php echo e($item->name); ?></span>
                                        <span class="item-sales"><?php echo e($item->quantity); ?> sold - PHP
                                            <?php echo e(number_format($item->revenue, 2)); ?></span>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>

                    <div class="export-modal-actions">
                        <button class="btn-print" onclick="printReport()">🖨️ PRINT</button>
                        <button class="btn-close" onclick="closeExportModal()">✖️ CLOSE</button>
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

            <!-- Metrics Grid -->
            <div class="metrics-grid grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="sales-card">
                    <div class="metric-card">
                        <h4 class="font-semibold text-lg mb-2">Today's Total:</h4>
                        <p class="text-2xl font-bold">PHP <?php echo e(number_format($todaysSales->total_sales, 2)); ?></p>
                    </div>
                </div>

                <div class="sales-card">
                    <div class="metric-card">
                        <h4 class="font-semibold text-lg mb-2">Orders Completed:</h4>
                        <p class="text-2xl font-bold"><?php echo e($todaysSales->total_orders); ?></p>
                    </div>
                </div>

                <div class="sales-card">
                    <div class="metric-card">
                        <h4 class="font-semibold text-lg mb-2">Average Order:</h4>
                        <p class="text-2xl font-bold">PHP <?php echo e(number_format($averageOrder, 2)); ?></p>
                    </div>
                </div>
            </div>

            <!-- Controls Section -->
            <div class="controls-section flex justify-between items-center mb-6">
                <div class="filter-section flex items-center space-x-4">
                    <select class="filter-dropdown" id="filterDropdown">
                        <option value="TODAY" <?php echo e(($filter ?? 'TODAY') == 'TODAY' ? 'selected' : ''); ?>>TODAY</option>
                        <option value="THIS WEEK" <?php echo e(($filter ?? 'TODAY') == 'THIS WEEK' ? 'selected' : ''); ?>>THIS WEEK
                        </option>
                        <option value="THIS MONTH" <?php echo e(($filter ?? 'TODAY') == 'THIS MONTH' ? 'selected' : ''); ?>>THIS MONTH
                        </option>
                    </select>
                </div>

                <div class="button-group flex items-center space-x-4">
                    <button class="btn-primary" onclick="openExportModal()">📊 Export Report</button>
                </div>
            </div>

            <!-- Top Selling Items Section -->
            <div class="report-section">
                <h3>🏆 Top Selling Items</h3>
                <ul class="top-items-list">
                    <?php $__currentLoopData = $TopItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <div class="item-rank"><?php echo e($index + 1); ?></div>
                            <div class="item-details">
                                <span class="item-name"><?php echo e($item->name); ?></span>
                                <span class="item-sales"><?php echo e($item->quantity); ?> sold - PHP
                                    <?php echo e(number_format($item->revenue, 2)); ?></span>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>


            <!-- Bottom Navigation -->
            <div class="bottom-nav flex justify-between items-center" style="margin-top: 2px;">
                <div class="tab-section flex space-x-5">
                    <button class="tab-button">INVENTORY</button>
                    <button class="tab-button active">SALES</button>
                    <button class="tab-button">PRODUCT</button>
                </div>

                <button class="logout-btn" onclick="openLogoutModal()">
                    🚪 LOG OUT
                </button>
            </div>
        </div>
    </div>

    <script>
        const TopItems = <?php echo json_encode($TopItems, 15, 512) ?>;
        // Define helper functions first
        function debugLog(message, data = null) {
            console.log('[DEBUG]', message, data !== null ? data : '');
        }
        function showPrinterStatus(message, type = 'info') {
            console.log('[PRINTER]', type.toUpperCase(), ':', message);

            // Optional: Add a visual notification
            // For now, we'll just use console.log
            // You can integrate with your existing notification system
        }


        // Export Report Modal functions
        function openExportModal() {
            const today = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('reportDate').textContent =
                `Report generated on ${today.toLocaleDateString('en-US', options)}`;

            document.getElementById('exportReportModal').classList.add('show');
        }

        function closeExportModal() {
            document.getElementById('exportReportModal').classList.remove('show');
        }

        function printReport() {
            console.log('🖨️ Print Report clicked');

            try {
                // Extract data from the report modal
                const reportContent = document.querySelector('.export-modal-content');
                if (!reportContent) {
                    console.error('❌ Report content not found');
                    alert('Error: Report content not found');
                    return;
                }

                // Get report date
                const dateElement = reportContent.querySelector('.report-date');
                const reportDate = dateElement ? dateElement.textContent.trim() : new Date().toLocaleDateString();

                // Extract sales data
                let totalRevenue = '0.00';
                let totalOrders = '0';
                let totalItems = '0';
                let avgOrderValue = '0.00';

                // Debug: log all report items to see what we're working with
                const allReportItems = reportContent.querySelectorAll('.report-item');
                console.log('Total report items found:', allReportItems.length);

                allReportItems.forEach((item, index) => {
                    const label = item.querySelector('.report-item-label')?.textContent || '';
                    const value = item.querySelector('.report-item-value')?.textContent || '';

                    const labelLower = label.toLowerCase().trim();
                    const valueTrimmed = value.trim();

                    console.log(`Item ${index}:`, {
                        label: label.trim(),
                        labelLower: labelLower,
                        value: valueTrimmed
                    });

                    // FIXED: More specific matching to avoid confusion
                    if (labelLower.includes("today's total")) {
                        totalRevenue = valueTrimmed.replace(/[PHP₱,\s]/g, '');
                        console.log('✅ Found revenue:', totalRevenue);
                    } else if (labelLower.includes('orders completed')) {
                        totalOrders = valueTrimmed.replace(/[^\d]/g, '');
                        console.log('✅ Found orders:', totalOrders);
                    } else if (labelLower.includes('items sold')) {
                        // Match specifically "Items Sold" to avoid confusion with other labels
                        totalItems = valueTrimmed.replace(/[^\d]/g, '');
                        console.log('✅ Found items sold:', totalItems);
                    } else if (labelLower.includes('average order')) {
                        avgOrderValue = valueTrimmed.replace(/[PHP₱,\s]/g, '');
                        console.log('✅ Found average:', avgOrderValue);
                    }
                });

                console.log('Final extracted data:', { totalRevenue, totalOrders, totalItems, avgOrderValue });

                // If totalItems is still 0, try an alternative approach
                if (totalItems === '0') {
                    console.warn('⚠️ Items still 0, trying alternative selector...');

                    // Try to find it differently - look for any element containing "Items Sold" or similar
                    const allElements = reportContent.querySelectorAll('.report-item-label');
                    allElements.forEach((el, idx) => {
                        const text = el.textContent.trim();
                        console.log(`Label ${idx}: "${text}"`);
                        if (text.toLowerCase().includes('item') || text.toLowerCase().includes('sold')) {
                            const valueEl = el.parentElement.querySelector('.report-item-value');
                            if (valueEl) {
                                totalItems = valueEl.textContent.trim().replace(/[^\d]/g, '');
                                console.log('✅ Found items via alternative method:', totalItems);
                            }
                        }
                    });
                }

                // Build the URL with parameters
                const params = new URLSearchParams({
                    date: reportDate,
                    revenue: totalRevenue,
                    orders: totalOrders,
                    items: totalItems,
                    avg: avgOrderValue,
                    top_items: JSON.stringify(TopItems)
                });

                const reportUrl = `${window.location.origin}/thermer-report.php?${params.toString()}`;
                const thermerUrl = `my.bluetoothprint.scheme://${reportUrl}`;

                console.log('📄 Report URL:', reportUrl);
                console.log('🖨️ Thermer URL:', thermerUrl);

                // Create a temporary link element for Thermer
                const link = document.createElement('a');
                link.href = thermerUrl;
                link.style.display = 'none';
                document.body.appendChild(link);

                // Click the link to trigger Thermer
                link.click();

                // Clean up
                setTimeout(() => {
                    document.body.removeChild(link);
                }, 100);

                console.log('✅ Report sent to Thermer app');
                alert('Report sent to Thermer app for printing');

            } catch (error) {
                console.error('❌ Error in printReport:', error);
                alert('Error: ' + error.message);
            }
        }

        // Tab switching functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function () {
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                if (this.textContent.trim() === 'INVENTORY') {
                    window.location.href = '/dashboard';
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
            window.location.href = 'http://127.0.0.1:8000';
        }

        // Close modal if background clicked
        document.getElementById('logoutModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeLogoutModal();
            }
        });

        // Close export modal when clicking outside
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('exportReportModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeExportModal();
                }
            });

            // ✅ WORKING FILTER DROPDOWN
            const filterDropdown = document.getElementById('filterDropdown');
            if (filterDropdown) {
                filterDropdown.addEventListener('change', function () {
                    const filter = this.value;
                    window.location.href = `/sales?filter=${encodeURIComponent(filter)}`;
                });
            }

            // Touch-friendly enhancements for tablets
            const buttons = document.querySelectorAll('button, .filter-dropdown');
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

</html><?php /**PATH C:\Users\Laurence Ayo\sip_and_serve_final\resources\views/sales.blade.php ENDPATH**/ ?>