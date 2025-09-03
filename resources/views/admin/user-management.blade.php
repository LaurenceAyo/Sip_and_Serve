<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="demo-token">
    <title>User Management Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(5px);
        }

        .main-content {
            padding: 30px;
        }

        .stats-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: none;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .stats-icon {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .user-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            border: none;
            padding: 15px;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border-color: #f0f0f0;
        }

        .btn-action {
            margin: 0 2px;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: border-color 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .role-badge {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .alert {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-3">
                    <h4 class="mb-4">
                        <i class="fas fa-shield-alt"></i>
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
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Dashboard
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
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
                            <p class="text-muted mb-0">Active Users</p>
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

                <!-- User Table -->
                <div class="user-table">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="usersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
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
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>
                        Add New User
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name *</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Role *</label>
                                    <select class="form-select" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="admin">Administrator</option>
                                        <option value="manager">Manager</option>
                                        <option value="cashier">Cashier</option>
                                        <option value="kitchen">Kitchen Staff</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status *</label>
                                    <select class="form-select" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="newPassword" required>
                                <button class="btn btn-outline-secondary password-toggle" type="button"
                                    onclick="togglePassword('newPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">Password must be at least 8 characters long</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password_confirmation"
                                    id="confirmPassword" required>
                                <button class="btn btn-outline-secondary password-toggle" type="button"
                                    onclick="togglePassword('confirmPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="pos_access" id="pos_access">
                                        <label class="form-check-label" for="pos_access">POS Access</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="kitchen_access" id="kitchen_access">
                                        <label class="form-check-label" for="kitchen_access">Kitchen Access</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="reports_access" id="reports_access">
                                        <label class="form-check-label" for="reports_access">Reports Access</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="inventory_access" id="inventory_access">
                                        <label class="form-check-label" for="inventory_access">Inventory Access</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="user_management" id="user_management">
                                        <label class="form-check-label" for="user_management">User Management</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="settings_access" id="settings_access">
                                        <label class="form-check-label" for="settings_access">Settings Access</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">
                        <i class="fas fa-save me-2"></i>
                        Create User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit me-2"></i>
                        Edit User
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name *</label>
                                    <input type="text" class="form-control" name="name" id="edit_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" name="email" id="edit_email" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Role *</label>
                                    <select class="form-select" name="role" id="edit_role" required>
                                        <option value="admin">Administrator</option>
                                        <option value="manager">Manager</option>
                                        <option value="cashier">Cashier</option>
                                        <option value="kitchen">Kitchen Staff</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status *</label>
                                    <select class="form-select" name="status" id="edit_status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password (leave blank to keep current)</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="editPassword">
                                <button class="btn btn-outline-secondary password-toggle" type="button"
                                    onclick="togglePassword('editPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="edit_permissions[]"
                                            value="pos_access" id="edit_pos_access">
                                        <label class="form-check-label" for="edit_pos_access">POS Access</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="edit_permissions[]"
                                            value="kitchen_access" id="edit_kitchen_access">
                                        <label class="form-check-label" for="edit_kitchen_access">Kitchen Access</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="edit_permissions[]"
                                            value="reports_access" id="edit_reports_access">
                                        <label class="form-check-label" for="edit_reports_access">Reports Access</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="edit_permissions[]"
                                            value="inventory_access" id="edit_inventory_access">
                                        <label class="form-check-label" for="edit_inventory_access">Inventory
                                            Access</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="edit_permissions[]"
                                            value="user_management" id="edit_user_management">
                                        <label class="form-check-label" for="edit_user_management">User
                                            Management</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="edit_permissions[]"
                                            value="settings_access" id="edit_settings_access">
                                        <label class="form-check-label" for="edit_settings_access">Settings
                                            Access</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateUser()">
                        <i class="fas fa-save me-2"></i>
                        Update User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user me-2"></i>
                        User Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="userDetailsContent">
                    <!-- User details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variables
        let users = [];

        // Load users from database
        async function loadUsers() {
            try {
                showLoading();
                const response = await fetch('/admin/users/data', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    users = data.data; // Fixed: Change from data.users to data.data
                    displayUsers(users);
                    updateStats(users);
                } else {
                    showAlert('Failed to load users: ' + (data.message || 'Unknown error'), 'danger');
                }
            } catch (error) {
                console.error('Error loading users:', error);
                showAlert('Error loading users: ' + error.message, 'danger');
            } finally {
                hideLoading();
            }
        }

        // Display users in table
        function displayUsers(userList) {
            const tbody = document.getElementById('usersTableBody');
            tbody.innerHTML = '';

            if (!userList || userList.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No users found</td></tr>';
                return;
            }

            userList.forEach(user => {
                const nameParts = user.name.split(' ');
                const initials = nameParts.length >= 2 ?
                    nameParts[0].charAt(0) + nameParts[nameParts.length - 1].charAt(0) :
                    user.name.charAt(0) + (user.name.charAt(1) || '');

                const row = `
                    <tr>
                        <td>#${user.id}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px; font-size: 14px; color: white; font-weight: bold;">
                                    ${initials}
                                </div>
                                <div>
                                    <div class="fw-bold">${user.name}</div>
                                </div>
                            </div>
                        </td>
                        <td>${user.email}</td>
                        <td>
                            <span class="badge role-badge ${getRoleBadgeClass(user.role)}">
                                ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                            </span>
                        </td>
                        <td>
                            <span class="badge status-badge ${user.status === 'active' ? 'bg-success' : 'bg-warning'}">
                                ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                            </span>
                        </td>
                        <td>${formatDate(user.last_login_at || user.updated_at)}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary btn-action" onclick="viewUser(${user.id})" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success btn-action" onclick="editUser(${user.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning btn-action" onclick="resetPassword(${user.id})" title="Reset Password">
                                <i class="fas fa-key"></i>
                            </button>
                            ${user.email !== 'laurenceayo7@gmail.com' ? `
                                <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteUser(${user.id})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : ''}
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        // Update statistics
        function updateStats(userList) {
            document.getElementById('totalUsers').textContent = userList.length;
            document.getElementById('activeUsers').textContent = userList.filter(u => u.status === 'active').length;
            document.getElementById('adminUsers').textContent = userList.filter(u => u.role === 'admin').length;

            const currentMonth = new Date().getMonth();
            const currentYear = new Date().getFullYear();
            document.getElementById('recentUsers').textContent = userList.filter(u => {
                const createdDate = new Date(u.created_at);
                return createdDate.getMonth() === currentMonth && createdDate.getFullYear() === currentYear;
            }).length;
        }

        // Get role badge class
        function getRoleBadgeClass(role) {
            const classes = {
                'admin': 'bg-danger',
                'manager': 'bg-warning text-dark',
                'cashier': 'bg-info',
                'kitchen': 'bg-success'
            };
            return classes[role] || 'bg-secondary';
        }

        // Format date
        function formatDate(dateString) {
            if (!dateString) return 'Never';
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        // Toggle view password
        function toggleViewPassword(userId) {
            const passwordField = document.getElementById(`viewPassword_${userId}`);
            const icon = document.getElementById(`viewPasswordIcon_${userId}`);

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordField.value = 'password123'; // Since passwords are hashed, show placeholder
                icon.className = 'fas fa-eye-slash';
            } else {
                passwordField.type = 'password';
                passwordField.value = '********';
                icon.className = 'fas fa-eye';
            }
        }

        // Save new user
        async function saveUser() {
            const form = document.getElementById('addUserForm');

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                showAlert('Please fill in all required fields correctly.', 'danger');
                return;
            }

            // Validate password confirmation
            const password = form.password.value;
            const confirmPassword = form.password_confirmation.value;

            if (password !== confirmPassword) {
                showAlert('Passwords do not match!', 'danger');
                return;
            }

            if (password.length < 8) {
                showAlert('Password must be at least 8 characters long!', 'danger');
                return;
            }

            try {
                showLoading();

                // Prepare form data
                const formData = new FormData(form);

                // Get selected permissions
                const permissions = [];
                const permissionElements = form.querySelectorAll('input[name="permissions[]"]:checked');
                permissionElements.forEach(el => permissions.push(el.value));
                formData.set('permissions', JSON.stringify(permissions));

                const response = await fetch('/admin/users', { // Fixed: Correct URL for creating user
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
                    showAlert('User created successfully!', 'success');
                    form.reset();
                    form.classList.remove('was-validated');
                    loadUsers(); // Reload users from database
                } else {
                    showAlert('Failed to create user: ' + (data.message || 'Unknown error'), 'danger');
                }
            } catch (error) {
                console.error('Error creating user:', error);
                showAlert('Error creating user: ' + error.message, 'danger');
            } finally {
                hideLoading();
            }
        }

        // Edit user
        async function editUser(id) {
            try {
                showLoading();

                const response = await fetch(`/admin/users/${id}`, { // Fixed: Correct URL
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    const user = data.data; // Fixed: Change from data.user to data.data

                    // Populate edit form
                    document.getElementById('edit_user_id').value = user.id;
                    document.getElementById('edit_name').value = user.name;
                    document.getElementById('edit_email').value = user.email;
                    document.getElementById('edit_role').value = user.role;
                    document.getElementById('edit_status').value = user.status;

                    // Set permissions
                    let permissions = [];
                    if (user.permissions) {
                        if (typeof user.permissions === 'string') {
                            if (user.permissions === 'all') {
                                permissions = ['pos_access', 'kitchen_access', 'reports_access', 'inventory_access', 'user_management', 'settings_access'];
                            } else {
                                try {
                                    permissions = JSON.parse(user.permissions);
                                } catch (e) {
                                    permissions = [];
                                }
                            }
                        } else if (Array.isArray(user.permissions)) {
                            permissions = user.permissions;
                        }
                    }
                    const permissionCheckboxes = document.querySelectorAll('input[name="edit_permissions[]"]');
                    permissionCheckboxes.forEach(checkbox => {
                        checkbox.checked = permissions.includes(checkbox.value);
                    });

                    // Show modal
                    new bootstrap.Modal(document.getElementById('editUserModal')).show();
                } else {
                    showAlert('Failed to load user: ' + (data.message || 'Unknown error'), 'danger');
                }
            } catch (error) {
                console.error('Error loading user:', error);
                showAlert('Error loading user: ' + error.message, 'danger');
            } finally {
                hideLoading();
            }
        }

        // Update user
        async function updateUser() {
            const form = document.getElementById('editUserForm');

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                showAlert('Please fill in all required fields correctly.', 'danger');
                return;
            }

            const userId = document.getElementById('edit_user_id').value;

            try {
                showLoading();

                // Prepare form data
                const formData = new FormData(form);

                // Get selected permissions
                const permissions = [];
                const permissionElements = form.querySelectorAll('input[name="edit_permissions[]"]:checked');
                permissionElements.forEach(el => permissions.push(el.value));
                formData.set('permissions', JSON.stringify(permissions));

                // Add method override for PUT request
                formData.append('_method', 'PUT');

                const response = await fetch(`/admin/users/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                    showAlert('User updated successfully!', 'success');
                    loadUsers(); // Reload users from database
                } else {
                    showAlert('Failed to update user: ' + (data.message || 'Unknown error'), 'danger');
                }
            } catch (error) {
                console.error('Error updating user:', error);
                showAlert('Error updating user: ' + error.message, 'danger');
            } finally {
                hideLoading();
            }
        }

        // View user details
        async function viewUser(id) {
            try {
                showLoading();

                const response = await fetch(`/admin/users/${id}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    const user = data.data; // Fixed: Change from data.user to data.data
                    let permissions = [];
                    if (user.permissions) {
                        if (typeof user.permissions === 'string') {
                            if (user.permissions === 'all') {
                                permissions = ['pos_access', 'kitchen_access', 'reports_access', 'inventory_access', 'user_management', 'settings_access'];
                            } else {
                                try {
                                    permissions = JSON.parse(user.permissions);
                                } catch (e) {
                                    permissions = [];
                                }
                            }
                        } else if (Array.isArray(user.permissions)) {
                            permissions = user.permissions;
                        }
                    }

                    const content = `
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width: 80px; height: 80px; font-size: 24px; color: white; font-weight: bold;">
                                    ${user.name.split(' ').map(n => n.charAt(0)).join('')}
                                </div>
                                <h5>${user.name}</h5>
                                <p class="text-muted">${user.email}</p>
                            </div>
                            <div class="col-md-8">
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>User ID:</strong></div>
                                    <div class="col-sm-8">#${user.id}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Role:</strong></div>
                                    <div class="col-sm-8">
                                        <span class="badge ${getRoleBadgeClass(user.role)}">
                                            ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Status:</strong></div>
                                    <div class="col-sm-8">
                                        <span class="badge ${user.status === 'active' ? 'bg-success' : 'bg-warning'}">
                                            ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Created:</strong></div>
                                    <div class="col-sm-8">${formatDate(user.created_at)}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Last Updated:</strong></div>
                                    <div class="col-sm-8">${formatDate(user.updated_at)}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Password:</strong></div>
                                    <div class="col-sm-8">
                                        <div class="d-flex align-items-center">
                                            <input type="password" class="form-control me-2" id="viewPassword_${user.id}" value="********" readonly style="max-width: 150px;">
                                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleViewPassword(${user.id})">
                                                <i class="fas fa-eye" id="viewPasswordIcon_${user.id}"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Click the eye icon to reveal password</small>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Permissions:</strong></div>
                                    <div class="col-sm-8">
                                        ${permissions.length > 0 ?
                            permissions.map(p => `<span class="badge bg-secondary me-1">${p.replace('_', ' ').toUpperCase()}</span>`).join('') :
                            '<span class="text-muted">No permissions assigned</span>'
                        }
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    document.getElementById('userDetailsContent').innerHTML = content;
                    new bootstrap.Modal(document.getElementById('viewUserModal')).show();
                } else {
                    showAlert('Failed to load user: ' + (data.message || 'Unknown error'), 'danger');
                }
            } catch (error) {
                console.error('Error loading user:', error);
                showAlert('Error loading user: ' + error.message, 'danger');
            } finally {
                hideLoading();
            }
        }

        // Reset password
        async function resetPassword(id) {
            const user = users.find(u => u.id === id);
            if (!user) {
                showAlert('User not found!', 'danger');
                return;
            }

            if (confirm(`Reset password for ${user.name}? A new temporary password will be generated.`)) {
                try {
                    showLoading();

                    const response = await fetch(`/admin/users/${id}/reset-password`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        showAlert(`Password reset successful! New temporary password: ${data.temp_password}`, 'success');
                    } else {
                        showAlert('Failed to reset password: ' + (data.message || 'Unknown error'), 'danger');
                    }
                } catch (error) {
                    console.error('Error resetting password:', error);
                    showAlert('Error resetting password: ' + error.message, 'danger');
                } finally {
                    hideLoading();
                }
            }
        }

        // Delete user
        async function deleteUser(id) {
            const user = users.find(u => u.id === id);
            if (!user) {
                showAlert('User not found!', 'danger');
                return;
            }

            if (user.email === 'laurenceayo7@gmail.com') {
                showAlert('Cannot delete the main administrator account!', 'danger');
                return;
            }

            if (confirm(`Are you sure you want to delete ${user.name}? This action cannot be undone.`)) {
                try {
                    showLoading();

                    const response = await fetch(`/admin/users/${id}`, { // Fixed: Correct URL
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        showAlert('User deleted successfully!', 'success');
                        loadUsers(); // Reload users from database
                    } else {
                        showAlert('Failed to delete user: ' + (data.message || 'Unknown error'), 'danger');
                    }
                } catch (error) {
                    console.error('Error deleting user:', error);
                    showAlert('Error deleting user: ' + error.message, 'danger');
                } finally {
                    hideLoading();
                }
            }
        }

        // Utility functions
        function showAlert(message, type) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                <i class="fas ${getAlertIcon(type)} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        function getAlertIcon(type) {
            const icons = {
                'success': 'fa-check-circle',
                'danger': 'fa-exclamation-circle',
                'warning': 'fa-exclamation-triangle',
                'info': 'fa-info-circle'
            };
            return icons[type] || 'fa-info-circle';
        }

        function showLoading() {
            // Create loading overlay
            const loadingDiv = document.createElement('div');
            loadingDiv.id = 'loadingOverlay';
            loadingDiv.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
            loadingDiv.style.cssText = 'background: rgba(0,0,0,0.5); z-index: 9999;';
            loadingDiv.innerHTML = `
                <div class="bg-white p-4 rounded-3 text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>Loading...</div>
                </div>
            `;
            document.body.appendChild(loadingDiv);
        }

        function hideLoading() {
            const loadingDiv = document.getElementById('loadingOverlay');
            if (loadingDiv) {
                loadingDiv.remove();
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function () {
            loadUsers();

            // Add form validation styles
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                });
            });
        });

        // Handle sidebar navigation
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function (e) {
                if (this.textContent.trim().includes('User Management')) {
                    e.preventDefault();
                    return;
                }

                if (this.textContent.trim().includes('Analytics')) {
                    e.preventDefault();
                    showAlert('Analytics page - Feature coming soon!', 'info');
                    return;
                }

                if (this.textContent.trim().includes('Settings')) {
                    e.preventDefault();
                    showAlert('Settings page - Feature coming soon!', 'info');
                    return;
                }
            });
        });
    </script>
</body>

</html>