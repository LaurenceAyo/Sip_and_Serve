<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>User Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .main-content {
            padding: 20px;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
        }

        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .user-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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
            padding: 5px 10px;
            font-size: 0.875rem;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .role-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
        }

        .password-toggle {
            cursor: pointer;
            color: #6c757d;
        }

        .password-toggle:hover {
            color: #495057;
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
                        <p class="text-muted">Manage system users and their permissions </p>
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
                                    <label class="form-label">First Name *</label>
                                    <input type="text" class="form-control" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" name="last_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" class="form-control" name="email" required>
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
                                    <label class="form-label">First Name *</label>
                                    <input type="text" class="form-control" name="first_name" id="edit_first_name"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" name="last_name" id="edit_last_name"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variable to store users
        let allUsers = [];

        // Load users from the server
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

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    allUsers = result.data;
                    displayUsers(allUsers);
                    updateStats();
                } else {
                    console.error('Failed to load users:', result.message);
                    showAlert('Failed to load users: ' + (result.message || 'Unknown error'), 'danger');
                }
            } catch (error) {
                console.error('Error loading users:', error);
                showAlert('Error loading users. Please refresh the page.', 'danger');
            }
        }

        function displayUsers(users) {
            const tbody = document.getElementById('usersTableBody');
            tbody.innerHTML = '';

            if (!users || users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No users found</td></tr>';
                return;
            }

            users.forEach(user => {
                const row = `
            <tr>
                <td>#${user.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px; font-size: 14px; color: white;">
                            ${getInitials(user.name)}
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
                <td>${user.last_login_at ? formatDate(user.last_login_at) : 'Never'}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="viewUser(${user.id})" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success btn-action" onclick="editUser(${user.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning btn-action" onclick="resetUserPassword(${user.id})" title="Reset Password">
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

        function getInitials(name) {
            return name.split(' ')
                .map(part => part.charAt(0).toUpperCase())
                .slice(0, 2)
                .join('');
        }

        function updateStats() {
            document.getElementById('totalUsers').textContent = allUsers.length;
            document.getElementById('activeUsers').textContent = allUsers.filter(u => u.status === 'active').length;
            document.getElementById('adminUsers').textContent = allUsers.filter(u => u.role === 'admin').length;

            const currentMonth = new Date();
            currentMonth.setDate(1);
            document.getElementById('recentUsers').textContent = allUsers.filter(u =>
                new Date(u.created_at) >= currentMonth
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

        async function saveUser() {
            const form = document.getElementById('addUserForm');
            const formData = new FormData(form);

            // Validate form
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            // Check password match
            if (formData.get('password') !== formData.get('password_confirmation')) {
                showAlert('Passwords do not match!', 'danger');
                return;
            }

            // Collect permissions
            const permissions = [];
            document.querySelectorAll('input[name="permissions[]"]:checked').forEach(checkbox => {
                permissions.push(checkbox.value);
            });

            // Create user object
            const userData = {
                name: formData.get('first_name') + ' ' + formData.get('last_name'),
                email: formData.get('email'),
                role: formData.get('role'),
                status: formData.get('status'),
                password: formData.get('password'),
                password_confirmation: formData.get('password_confirmation'),
                permissions: permissions.join(',')
            };

            try {
                const response = await fetch('/admin/users', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(userData)
                });

                const result = await response.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
                    showAlert('User created successfully!', 'success');

                    // Reset form
                    form.reset();
                    form.classList.remove('was-validated');

                    // Reload users
                    loadUsers();
                } else {
                    showAlert('Failed to create user: ' + (result.message || 'Unknown error'), 'danger');
                }
            } catch (error) {
                console.error('Error creating user:', error);
                showAlert('Error creating user. Please try again.', 'danger');
            }
        }

        function editUser(id) {
            const user = allUsers.find(u => u.id === id);
            if (!user) return;

            // Split name into first and last name (simple approach)
            const nameParts = user.name.split(' ');
            const firstName = nameParts[0] || '';
            const lastName = nameParts.slice(1).join(' ') || '';

            // Populate edit form
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_first_name').value = firstName;
            document.getElementById('edit_last_name').value = lastName;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_status').value = user.status;

            // Show modal
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }

        async function updateUser() {
            const form = document.getElementById('editUserForm');
            const formData = new FormData(form);

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            const userId = formData.get('user_id');
            const userData = {
                name: formData.get('first_name') + ' ' + formData.get('last_name'),
                email: formData.get('email'),
                role: formData.get('role'),
                status: formData.get('status'),
                permissions: ''
            };

            if (formData.get('password')) {
                userData.password = formData.get('password');
            }

            try {
                const response = await fetch(`/admin/users/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(userData)
                });

                const result = await response.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                    showAlert('User updated successfully!', 'success');
                    loadUsers();
                } else {
                    showAlert('Failed to update user: ' + (result.message || 'Unknown error'), 'danger');
                }
            } catch (error) {
                console.error('Error updating user:', error);
                showAlert('Error updating user. Please try again.', 'danger');
            }
        }

        function viewUser(id) {
            const user = allUsers.find(u => u.id === id);
            if (!user) return;

            const content = `
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px; font-size: 24px; color: white;">
                    ${getInitials(user.name)}
                </div>
                <h5>${user.name}</h5>
                <span class="badge ${getRoleBadgeClass(user.role)} mb-2">
                    ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                </span>
            </div>
            <div class="col-md-8">
                <table class="table table-borderless">
                    <tr>
                        <th>Email:</th>
                        <td>${user.email}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge ${user.status === 'active' ? 'bg-success' : 'bg-warning'}">
                                ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td>${formatDate(user.created_at)}</td>
                    </tr>
                    <tr>
                        <th>Last Login:</th>
                        <td>${user.last_login_at ? formatDate(user.last_login_at) : 'Never'}</td>
                    </tr>
                    <tr>
                        <th>Permissions:</th>
                        <td>${user.permissions || 'None'}</td>
                    </tr>
                </table>
            </div>
        </div>
    `;

            document.getElementById('userDetailsContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('viewUserModal')).show();
        }

        async function resetUserPassword(id) {
            const user = allUsers.find(u => u.id === id);
            if (!user) return;

            if (confirm(`Reset password for ${user.name}? A new temporary password will be generated.`)) {
                try {
                    const response = await fetch(`/admin/users/${id}/reset-password`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        showAlert(`Password reset successfully! New temporary password: ${result.temp_password}`, 'success');
                    } else {
                        showAlert('Failed to reset password: ' + (result.message || 'Unknown error'), 'danger');
                    }
                } catch (error) {
                    console.error('Error resetting password:', error);
                    showAlert('Error resetting password. Please try again.', 'danger');
                }
            }
        }

        async function deleteUser(id) {
            const user = allUsers.find(u => u.id === id);
            if (!user) return;

            if (confirm(`Are you sure you want to delete ${user.name}? This action cannot be undone.`)) {
                try {
                    const response = await fetch(`/admin/users/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        showAlert('User deleted successfully!', 'success');
                        loadUsers();
                    } else {
                        showAlert('Failed to delete user: ' + (result.message || 'Unknown error'), 'danger');
                    }
                } catch (error) {
                    console.error('Error deleting user:', error);
                    showAlert('Error deleting user. Please try again.', 'danger');
                }
            }
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
            alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 10000);
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function () {
            // Add CSRF token meta tag if it doesn't exist
            if (!document.querySelector('meta[name="csrf-token"]')) {
                const meta = document.createElement('meta');
                meta.name = 'csrf-token';
                meta.content = '{{ csrf_token() }}';
                document.getElementsByTagName('head')[0].appendChild(meta);
            }

            loadUsers();
        });
    </script>
</body>

</html>