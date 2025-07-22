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
            background: linear-gradient(135deg, #f5f1eb 0%, #e8ddd4 100%);
            min-height: 100vh;
        }
        
        .dashboard-container {
            background: white;
            min-height: 100vh;
        }
        
        .header-section {
            background: #F5E6D3;
            color: #5d4037;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-bottom: 2px solid #d4c5a9;
        }
        
        .sales-card {
            background: #f8f6f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 2px solid #d4c5a9;
        }
        
        .sales-summary {
            background: linear-gradient(135deg, #ffd54f 0%, #ffb74d 100%);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            font-weight: bold;
            color: #5d4037;
            margin-bottom: 20px;
        }
        
        .metric-card {
            background: #e8ddd4;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin-bottom: 10px;
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold">Sip & Serve</h1>
                    <div class="separator-line"></div>
                    <h2 class="text-3xl font-light">CAFE DASHBOARD</h2>
                </div>
                <div class="text-right">
                    <p class="text-sm">Manager ID: 10023</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 py-6">
            <!-- Sales Summary -->
            <div class="sales-summary">
                <h3 class="text-xl font-bold mb-4">SALES SUMMARY</h3>
            </div>
            <!-- Logout Modal -->
                <div id="logoutModal" class="modal-overlay">
                    <div class="modal-content">
                        <div class="modal-title">Logout Account?</div>
                        <button class="modal-btn modal-btn-logout" onclick="confirmLogout()">Logout</button>
                        <button class="modal-btn modal-btn-cancel" onclick="closeLogoutModal()">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="sales-card">
                    <div class="metric-card">
                        <h4 class="font-semibold text-lg mb-2">Today's Total:</h4>
                        <p class="text-2xl font-bold">PHP 10,520.00</p>
                    </div>
                </div>
                
                <div class="sales-card">
                    <div class="metric-card">
                        <h4 class="font-semibold text-lg mb-2">Orders Completed:</h4>
                        <p class="text-2xl font-bold">30</p>
                    </div>
                </div>
                
                <div class="sales-card">
                    <div class="metric-card">
                        <h4 class="font-semibold text-lg mb-2">Average Order:</h4>
                        <p class="text-2xl font-bold">PHP 350.67</p>
                    </div>
                </div>
            </div>

            <!-- Controls Section -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <select class="filter-dropdown">
                        <option>TODAY</option>
                        <option>THIS WEEK</option>
                        <option>THIS MONTH</option>
                        <option>CUSTOM</option>
                    </select>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button class="btn-primary">📊 Export Report</button>
                    <button class="btn-primary">📈 View Analytics</button>
                </div>
            </div>

            <!-- Top Selling Items -->
            <div class="sales-card mb-6">
                <h4 class="font-semibold text-lg mb-4 text-center">Top Selling Items:</h4>
                <div class="top-items">
                    <div class="item-card">
                        <div class="item-image bg-amber-100 flex items-center justify-center">
                            <span class="text-2xl">☕</span>
                        </div>
                        <h5 class="font-semibold">Espresso</h5>
                        <p class="text-sm text-gray-600">15 sold</p>
                    </div>
                    <div class="item-card">
                        <div class="item-image bg-green-100 flex items-center justify-center">
                            <span class="text-2xl">🍛</span>
                        </div>
                        <h5 class="font-semibold">Pad Thai</h5>
                        <p class="text-sm text-gray-600">12 sold</p>
                    </div>
                    <div class="item-card">
                        <div class="item-image bg-blue-100 flex items-center justify-center">
                            <span class="text-2xl">🥤</span>
                        </div>
                        <h5 class="font-semibold">Iced Coffee</h5>
                        <p class="text-sm text-gray-600">10 sold</p>
                    </div>
                    <div class="item-card">
                        <div class="item-image bg-red-100 flex items-center justify-center">
                            <span class="text-2xl">🥪</span>
                        </div>
                        <h5 class="font-semibold">Club Sandwich</h5>
                        <p class="text-sm text-gray-600">8 sold</p>
                    </div>
                </div>
            </div>

            <!-- Recent Sales Table -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <h4 class="font-semibold text-lg p-4 bg-d4c5a9 text-center" style="background-color: #d4c5a9; color: #5d4037;">RECENT SALES</h4>
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th>ORDER ID</th>
                            <th>TIME</th>
                            <th>ITEMS</th>
                            <th>QUANTITY</th>
                            <th>TOTAL</th>
                            <th>PAYMENT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-medium">#001</td>
                            <td>14:30</td>
                            <td>Espresso, Croissant</td>
                            <td>2</td>
                            <td>PHP 280.00</td>
                            <td>Cash</td>
                        </tr>
                        <tr>
                            <td class="font-medium">#002</td>
                            <td>14:25</td>
                            <td>Pad Thai</td>
                            <td>1</td>
                            <td>PHP 450.00</td>
                            <td>GCash</td>
                        </tr>
                        <tr>
                            <td class="font-medium">#003</td>
                            <td>14:20</td>
                            <td>Iced Coffee, Muffin</td>
                            <td>2</td>
                            <td>PHP 320.00</td>
                            <td>Card</td>
                        </tr>
                        <tr>
                            <td class="font-medium">#004</td>
                            <td>14:15</td>
                            <td>Club Sandwich, Latte</td>
                            <td>2</td>
                            <td>PHP 520.00</td>
                            <td>Cash</td>
                        </tr>
                        <tr>
                            <td class="font-medium">#005</td>
                            <td>14:10</td>
                            <td>Cappuccino</td>
                            <td>1</td>
                            <td>PHP 180.00</td>
                            <td>GCash</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Bottom Navigation -->
            <div class="flex justify-between items-center mt-8">
                <div class="flex space-x-5 absolute left-1/2 transform -translate-x-1/2">
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
        // Tab switching functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                // Handle navigation
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

        // Filter dropdown functionality
        document.querySelector('.filter-dropdown').addEventListener('change', function() {
            console.log('Filter changed to:', this.value);
            // Add your filter logic here
        });

        // Export report functionality
        document.querySelector('.btn-primary').addEventListener('click', function() {
            alert('Export functionality will be implemented here');
        });
    </script>
</body>
</html>