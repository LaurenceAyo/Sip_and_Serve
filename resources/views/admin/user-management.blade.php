<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            margin-left: 320px;
            padding: 1.5rem;
            min-height: 100vh;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            font-size: 15px;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white !important;
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white !important;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .user-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-responsive {
            border-radius: 12px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            min-width: 700px;
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            padding: 8px 6px;
            font-size: 11px;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
            padding: 8px 6px;
            font-size: 12px;
        }

        .table td:nth-child(1) {
            width: 200px;
        }

        .table td:nth-child(2) {
            width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table td:nth-child(3) {
            width: 100px;
        }

        .table td:nth-child(4) {
            width: 120px;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            font-size: 11px;
        }

        .btn-action {
            padding: 3px 5px;
            margin: 0 1px;
            font-size: 10px;
        }

        .role-badge {
            font-size: 9px;
            padding: 3px 5px;
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

        .form-control,
        .form-select {
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

        /* Delete confirmation modal styling */
        .delete-modal .modal-content {
            background: white;
            color: #333;
        }

        .delete-modal .modal-header {
            background-color: #dc3545;
            color: white;
            border-bottom: none;
        }

        .delete-modal .modal-body {
            background: white;
            color: #333;
            text-align: center;
            padding: 2rem;
        }

        .delete-modal .modal-footer {
            background: white;
            border-top: 1px solid #dee2e6;
            justify-content: center;
        }

        .delete-icon {
            font-size: 3rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                padding: 1rem;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .table {
                min-width: 600px;
                font-size: 11px;
            }

            .table th,
            .table td {
                padding: 6px 4px;
            }

            .stats-card {
                padding: 1rem;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="mb-4">
            <i class="fas fa-shield-alt me-2"></i>
            Admin Panel
        </h4>
        <nav class="nav flex-column">
            <a href="#" class="nav-link active">
                <i class="fas fa-users me-2"></i>
                User Management
            </a>
            <a href="/admin/backup-settings" class="nav-link">
                <i class="fas fa-cog me-2"></i>
                Backup Settings
            </a>
            <a href="{{ route('pin.change.form') }}" class="btn btn-warning me-2">
                <i class="fas fa-key me-2"></i>
                Change PIN
            </a>
            <a href="#" class="nav-link" onclick="goToDashboard()">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Dashboard
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">User Management</h2>
                <p class="text-muted">Manage System Users and their Permissions</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-user-plus me-2"></i>
                Add New User
            </button>
        </div>

        <!-- Stats Cards - REMOVED "All Users" card -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="text-primary"><i class="fas fa-users" style="font-size: 2rem;"></i></div>
                    <h4 id="totalUsers">0</h4>
                    <p class="text-muted mb-0">Total Users</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="text-warning"><i class="fas fa-user-shield" style="font-size: 2rem;"></i></div>
                    <h4 id="adminUsers">0</h4>
                    <p class="text-muted mb-0">Admin Users</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="text-info"><i class="fas fa-user-clock" style="font-size: 2rem;"></i></div>
                    <h4 id="recentUsers">0</h4>
                    <p class="text-muted mb-0">New This Month</p>
                </div>
            </div>
        </div>

        <!-- User Table -->
        <div class="user-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
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
                        <div class="mb-3">
                            <label class="form-label">Role *</label>
                            <select class="form-select" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin">Administrator</option>
                                <option value="manager">Manager</option>
                                <option value="cashier">Cashier</option>
                                <option value="kitchen">Kitchen Staff</option>
                                <option value="customer">Customer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
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
                        <input type="hidden" id="edit_user_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="edit_first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="edit_last_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="edit_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role *</label>
                            <select class="form-select" id="edit_role" required>
                                <option value="admin">Administrator</option>
                                <option value="manager">Manager</option>
                                <option value="cashier">Cashier</option>
                                <option value="kitchen">Kitchen Staff</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="edit_password">
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade delete-modal" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="delete-icon">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <h4>Delete User?</h4>
                    <p class="mb-0">Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
                    <p class="text-muted">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash me-2"></i>
                        Delete User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let allUsers = [];
        let userToDelete = null;

        // Function to go back to dashboard
        function goToDashboard() {
            window.location.href = '/dashboard';
        }

        async function loadUsers() {
            try {
                console.log('Loading users...');
                const response = await fetch('/admin/users/data', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Cache-Control': 'no-cache'
                    }
                });

                const result = await response.json();
                console.log('Users loaded:', result);

                if (result.success && result.data) {
                    allUsers = result.data;
                    console.log('Individual user roles:', allUsers.map(u => ({ id: u.id, name: u.name, role: u.role })));
                    console.log('All users after update:', allUsers);
                    displayUsers(allUsers);
                    updateStats();
                } else {
                    console.error('Failed to load users:', result);
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
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No users found</td></tr>';
                return;
            }

            users.forEach(user => {
                const userName = user.name || 'Unknown';
                const userEmail = user.email || 'No Email';
                const userRole = user.role || 'cashier';

                const row = document.createElement('tr');
                row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2 user-avatar" style="color: white;">
                        ${getInitials(userName)}
                    </div>
                    <div class="fw-bold">${userName}</div>
                </div>
            </td>
            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">${userEmail}</td>
            <td>
                <span class="badge role-badge ${getRoleBadgeClass(userRole)}">
                    ${userRole.charAt(0).toUpperCase() + userRole.slice(1)}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-outline-success btn-action" onclick="editUser(${user.id})" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                ${userEmail !== 'laurenceayo7@gmail.com' ? `
                <button class="btn btn-sm btn-outline-danger btn-action" onclick="showDeleteModal(${user.id})" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
                ` : ''}
            </td>
        `;
                tbody.appendChild(row);
            });
        }

        // Show delete confirmation modal
        function showDeleteModal(id) {
            const user = allUsers.find(u => u.id === id);
            if (!user) return;

            userToDelete = user;
            document.getElementById('deleteUserName').textContent = user.name;
            new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
        }

        // Confirm delete action
        async function confirmDelete() {
            if (!userToDelete) return;

            try {
                const response = await fetch(`/admin/users/${userToDelete.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('deleteUserModal')).hide();
                    showAlert('User deleted successfully!', 'success');
                    loadUsers();
                } else {
                    showAlert('Failed to delete user: ' + (result.message || 'Unknown error'), 'danger');
                }
            } catch (error) {
                console.error('Error deleting user:', error);
                showAlert('Error deleting user. Please try again.', 'danger');
            }

            userToDelete = null;
        }

        function editUser(id) {
            const user = allUsers.find(u => u.id === id);
            if (!user) return;

            const nameParts = user.name.split(' ');
            const firstName = nameParts[0] || '';
            const lastName = nameParts.slice(1).join(' ') || '';

            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_first_name').value = firstName;
            document.getElementById('edit_last_name').value = lastName;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role').value = user.role;

            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }

        async function updateUser() {
            const userId = document.getElementById('edit_user_id').value;
            const firstName = document.getElementById('edit_first_name').value;
            const lastName = document.getElementById('edit_last_name').value;
            const email = document.getElementById('edit_email').value;
            const role = document.getElementById('edit_role').value;
            const password = document.getElementById('edit_password').value;

            const userData = {
                name: firstName + ' ' + lastName,
                email: email,
                role: role,
                permissions: ''
            };

            if (password) {
                userData.password = password;
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

        async function saveUser() {
            const form = document.getElementById('addUserForm');
            const formData = new FormData(form);

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            if (formData.get('password') !== formData.get('password_confirmation')) {
                showAlert('Passwords do not match!', 'danger');
                return;
            }

            const userData = {
                name: formData.get('first_name') + ' ' + formData.get('last_name'),
                email: formData.get('email'),
                role: formData.get('role'),
                password: formData.get('password'),
                password_confirmation: formData.get('password_confirmation'),
                status: 'active',
                permissions: ''
                // DO NOT include password_reset_required - it doesn't exist in your table
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
                    form.reset();
                    loadUsers();
                } else {
                    showAlert('Failed to create user: ' + (result.message || 'Unknown error'), 'danger');
                }
            } catch (error) {
                console.error('Error creating user:', error);
                showAlert('Error creating user. Please try again.', 'danger');
            }
        }

        function getInitials(name) {
            return name.split(' ').map(part => part.charAt(0).toUpperCase()).slice(0, 2).join('');
        }

        function updateStats() {
            document.getElementById('totalUsers').textContent = allUsers.length;
            document.getElementById('adminUsers').textContent = allUsers.filter(u => u.role === 'admin').length;

            const thisMonth = new Date();
            thisMonth.setDate(1);
            document.getElementById('recentUsers').textContent = allUsers.filter(u =>
                new Date(u.created_at) >= thisMonth
            ).length;
        }

        function getRoleBadgeClass(role) {
            return {
                'admin': 'bg-danger',
                'manager': 'bg-warning text-dark',
                'cashier': 'bg-info',
                'kitchen': 'bg-success',
                'customer': 'bg-primary'
            }[role] || 'bg-secondary';
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
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', loadUsers);
    </script>
</body>

</html>