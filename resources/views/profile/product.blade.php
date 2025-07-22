<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - L' PRIMERO CAFE</title>
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
        
        .menu-container {
            background: #f8f6f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .search-bar {
            background: #e8ddd4;
            border: 2px solid #d4c5a9;
            border-radius: 8px;
            padding: 10px 15px;
            width: 300px;
            color: #5d4037;
            font-weight: 500;
        }
        
        .search-bar:focus {
            outline: none;
            border-color: #8b4513;
        }
        
        .menu-table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .menu-table th {
            background: #d4c5a9;
            color: #5d4037;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #b8a082;
        }
        
        .menu-table td {
            padding: 15px;
            border-bottom: 1px solid #e0d4c3;
            color: #5d4037;
        }
        
        .menu-table tr:hover {
            background: #f9f7f4;
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

        .menu-table .price-cell {
            font-weight: 600;
            color: #8b4513;
        }

        .scrollbar {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        
        .scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .scrollbar::-webkit-scrollbar-thumb {
            background: #d4c5a9;
            border-radius: 4px;
        }
        
        .scrollbar::-webkit-scrollbar-thumb:hover {
            background: #b8a082;
        }
        
        .actions-container {
            background: #f8f6f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .btn-add {
            background: #4caf50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 10px;
        }
        
        .btn-add:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        
        .btn-edit {
            background: #ffc107;
            color: #5d4037;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 10px;
        }
        
        .btn-edit:hover {
            background: #ffb300;
            transform: translateY(-2px);
        }
        
        .btn-delete {
            background: #f44336;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-delete:hover {
            background: #d32f2f;
            transform: translateY(-2px);
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
        
        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #8b4513;
            cursor: pointer;
        }
        
        .search-container {
            position: relative;
            display: inline-block;
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
            <!-- Menu Item List Section -->
            <div class="menu-container">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-5d4037" style="color: #5d4037;">Menu Item List</h3>
                    <div class="search-container">
                        <input type="text" placeholder="Search Menu" class="search-bar" id="searchInput">
                        <span class="search-icon">🔍</span>
                    </div>
                </div>
                
                <div class="scrollbar">
                    <table class="menu-table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody id="menuTableBody">
                            <tr>
                                <td class="font-medium">Aglio e Olio</td>
                                <td class="price-cell">PHP 250.00</td>
                            </tr>
                            <tr>
                                <td class="font-medium">Affogato</td>
                                <td class="price-cell">PHP 150.00</td>
                            </tr>
                            <tr>
                                <td class="font-medium">Ban Mian</td>
                                <td class="price-cell">PHP 350.00</td>
                            </tr>
                            <tr>
                                <td class="font-medium">Cappuccino</td>
                                <td class="price-cell">PHP 150.00</td>
                            </tr>
                            <tr>
                                <td class="font-medium">Cold Brew</td>
                                <td class="price-cell">PHP 150.00</td>
                            </tr>
                            <tr>
                                <td class="font-medium">Doppio</td>
                                <td class="price-cell">PHP 150.00</td>
                            </tr>
                            <tr>
                                <td class="font-medium">Pad Thai</td>
                                <td class="price-cell">PHP 250.00</td>
                            </tr>
                            <tr>
                                <td class="font-medium">Vangole</td>
                                <td class="price-cell">PHP 350.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions Section -->
            <div class="actions-container">
                <h3 class="text-lg font-bold text-5d4037 mb-4" style="color: #5d4037;">Quick Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <button class="btn-add" onclick="addNewItem()">Add New Item</button>
                    <button class="btn-edit" onclick="editPrice()">Edit Price</button>
                    <button class="btn-delete" onclick="deleteItem()">Delete Item</button>
                </div>
            </div>

            <!-- Bottom Navigation -->
            <div class="flex justify-between items-center mt-8">
                <div class="flex space-x-5 absolute left-1/2 transform -translate-x-1/2">
                    <button class="tab-button">INVENTORY</button>
                    <button class="tab-button">SALES</button>
                    <button class="tab-button active">PRODUCTS</button>
                </div>
                
                <button class="logout-btn">
                    🚪 LOG OUT
                </button>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                if (this.textContent.trim() === 'INVENTORY') {
                    window.location.href = '/dashboard';
                } else if (this.textContent.trim() === 'SALES') {
                    window.location.href = '/sales';
                }
            });
        });

        // Logout functionality
        document.querySelector('.logout-btn').addEventListener('click', function() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = '/logout';
            }
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#menuTableBody tr');
            
            tableRows.forEach(row => {
                const itemName = row.querySelector('td:first-child').textContent.toLowerCase();
                if (itemName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Quick Actions
        function addNewItem() {
            const itemName = prompt('Enter item name:');
            const itemPrice = prompt('Enter item price (PHP):');
            
            if (itemName && itemPrice) {
                const tableBody = document.getElementById('menuTableBody');
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td class="font-medium">${itemName}</td>
                    <td class="price-cell">PHP ${parseFloat(itemPrice).toFixed(2)}</td>
                `;
                tableBody.appendChild(newRow);
                alert('Item added successfully!');
            }
        }

        function editPrice() {
            const itemName = prompt('Enter item name to edit:');
            if (itemName) {
                const rows = document.querySelectorAll('#menuTableBody tr');
                let found = false;
                
                rows.forEach(row => {
                    const currentItemName = row.querySelector('td:first-child').textContent;
                    if (currentItemName.toLowerCase() === itemName.toLowerCase()) {
                        const newPrice = prompt(`Enter new price for ${currentItemName}:`);
                        if (newPrice) {
                            row.querySelector('.price-cell').textContent = `PHP ${parseFloat(newPrice).toFixed(2)}`;
                            alert('Price updated successfully!');
                            found = true;
                        }
                    }
                });
                
                if (!found) {
                    alert('Item not found!');
                }
            }
        }

        function deleteItem() {
            const itemName = prompt('Enter item name to delete:');
            if (itemName) {
                const rows = document.querySelectorAll('#menuTableBody tr');
                let found = false;
                
                rows.forEach(row => {
                    const currentItemName = row.querySelector('td:first-child').textContent;
                    if (currentItemName.toLowerCase() === itemName.toLowerCase()) {
                        if (confirm(`Are you sure you want to delete ${currentItemName}?`)) {
                            row.remove();
                            alert('Item deleted successfully!');
                            found = true;
                        }
                    }
                });
                
                if (!found) {
                    alert('Item not found!');
                }
            }
        }
    </script>
</body>
</html>