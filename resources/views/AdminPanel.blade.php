<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 320px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 1.5rem;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .main-content {
            margin-left: 320px;
            padding: 1.5rem;
            min-height: 100vh;
        }

        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            font-size: 15px;
        }

        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white !important;
            transform: translateX(5px);
        }

        .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white !important;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.3s ease;
        }

        .stats-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .user-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table-responsive {
            border-radius: 12px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .table {
            margin-bottom: 0;
            font-size: 13px;
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            white-space: nowrap;
            padding: 12px 6px;
            font-size: 12px;
        }

        .table td {
            vertical-align: middle;
            padding: 10px 6px;
            white-space: nowrap;
        }

        .table td:nth-child(1) { min-width: 180px; }
        .table td:nth-child(2) { max-width: 200px; overflow: hidden; text-overflow: ellipsis; font-size: 12px; }
        .table td:nth-child(3) { width: 120px; }
        .table td:nth-child(4) { min-width: 140px; font-size: 11px; }
        .table td:nth-child(5) { width: 180px; }

        .btn-action {
            padding: 4px 6px;
            margin: 0 1px;
            font-size: 11px;
        }

        .role-badge {
            font-size: 10px;
            padding: 4px 6px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            font-size: 12px;
        }

        .modal-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 15px;
        }

        .modal-body {
            background: white;
            color: #333;
            border-radius: 0 0 15px 15px;
        }

        .modal-footer {
            background: white;
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 15px 15px;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 12px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
        }
    </style>

    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <h4 class="mb-4">
                <i class="fas fa-shield-alt me-2"></i>
                Admin Panel
            </h4>
            <nav class="nav flex-column">
                <a href="#" class="nav-link active">
                    <i class="fas fa-users me-2"></i>
                    User Management
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-bar me-2"></i>
                    Analytics
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-cog me-2"></i>
                    Settings
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Dashboard
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">User Management</h2>
                <p class="text-muted">Manage system users and their permissions</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-user-plus me-2"></i>
                Add New User
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 id="totalUsers">0</h4>
                    <p class="text-muted mb-0">Total Users</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon text-success">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h4 id="activeUsers">0</h4>
                    <p class="text-muted mb-0">All Users</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon text-warning">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h4 id="adminUsers">0</h4>
                    <p class="text-muted mb-0">Admin Users</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon text-info">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <h4 id="recentUsers">0</h4>
                    <p class="text-muted mb-0">New This Month</p>
                </div>
            </div>
        </div>

        <!-- User Table - FIXED: Only 5 columns -->
        <div class="user-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="usersTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <!-- Users will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let allUsers = [];

        async function loadUsers() {
            try {
                const response = await fetch('/admin/users/data', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();
                console.log('Server response:', result);

                if (result.success) {
                    allUsers = result.data || [];
                    displayUsers(allUsers);
                    updateStats();
                } else {
                    console.error('Failed to load users:', result.message);
                }
            } catch (error) {
                console.error('Error loading users:', error);
            }
        }

        function displayUsers(users) {
            const tbody = document.getElementById('usersTableBody');
            tbody.innerHTML = '';

            if (!users || users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No users found</td></tr>';
                return;
            }

            users.forEach(user => {
                const userName = user.name || 'Unknown User';
                const userEmail = user.email || 'No Email';
                const userRole = user.role || 'cashier';

                const row = `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2 user-avatar" 
                                 style="color: white;">
                                ${getInitials(userName)}
                            </div>
                            <div>
                                <div class="fw-bold">${userName}</div>
                            </div>
                        </div>
                    </td>
                    <td title="${userEmail}">${userEmail}</td>
                    <td>
                        <span class="badge role-badge ${getRoleBadgeClass(userRole)}">
                            ${userRole.charAt(0).toUpperCase() + userRole.slice(1)}
                        </span>
                    </td>
                    <td>${user.last_login_at ? formatDate(user.last_login_at) : 'Never'}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary btn-action" onclick="editUser(${user.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteUser(${user.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                tbody.innerHTML += row;
            });
        }

        function getInitials(name) {
            return name.split(' ').map(part => part.charAt(0).toUpperCase()).slice(0, 2).join('');
        }

        function updateStats() {
            document.getElementById('totalUsers').textContent = allUsers.length;
            document.getElementById('activeUsers').textContent = allUsers.length;
            document.getElementById('adminUsers').textContent = allUsers.filter(u => u.role === 'admin').length;
            document.getElementById('recentUsers').textContent = allUsers.filter(u => 
                new Date(u.created_at) >= new Date(new Date().getFullYear(), new Date().getMonth(), 1)
            ).length;
        }

        function getRoleBadgeClass(role) {
            const classes = {
                'admin': 'bg-danger',
                'manager': 'bg-warning text-dark',
                'cashier': 'bg-info',
                'kitchen': 'bg-success'
            };
            return classes[role] || 'bg-secondary';
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function editUser(id) {
            console.log('Edit user:', id);
        }

        function deleteUser(id) {
            console.log('Delete user:', id);
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadUsers();
        });
    </script>
</body>
</html>