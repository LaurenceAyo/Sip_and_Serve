<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Backup Settings - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            transform: translateX(5px);
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white !important;
        }

        .settings-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 12px;
        }

        .alert {
            border-radius: 8px;
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
            <a href="/admin/users" class="nav-link">
                <i class="fas fa-users me-2"></i>
                User Management
            </a>
            <a href="#" class="nav-link active">
                <i class="fas fa-cog me-2"></i>
                Backup Settings
            </a>
            <a href="/dashboard" class="nav-link">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Dashboard
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Backup Settings</h2>
                <p class="text-muted">Configure system backup preferences</p>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="settings-card">
                    <form action="<?php echo e(route('admin.backup-settings.update')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-server me-2 text-primary"></i>
                                Backup Location
                            </h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="backup_location" id="local"
                                    value="local" <?php echo e(($settings->backup_location ?? 'local') == 'local' ? 'checked' : 'checked'); ?>>
                                <label class="form-check-label" for="local">
                                    <strong>Local Storage</strong>
                                    <br>
                                    <small class="text-muted">Store backups on the local server</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="backup_location" id="server"
                                    value="server" disabled <?php echo e(($settings->backup_location ?? 'local') == 'server' ? 'checked' : ''); ?>>
                                <label class="form-check-label text-muted" for="server">
                                    <strong>Remote Server</strong>
                                    <br>
                                    <small class="text-muted">Store backups on a remote server (Coming Soon)</small>
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-clock me-2 text-primary"></i>
                                Backup Schedule
                            </h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="backup_schedule" id="weekly"
                                    value="weekly" <?php echo e(($settings->backup_schedule ?? 'weekly') == 'weekly' ? 'checked' : 'checked'); ?>>
                                <label class="form-check-label" for="weekly">
                                    <strong>Weekly</strong>
                                    <br>
                                    <small class="text-muted">Create backup every week</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="backup_schedule" id="monthly"
                                    value="monthly" <?php echo e(($settings->backup_schedule ?? 'weekly') == 'monthly' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="monthly">
                                    <strong>Monthly</strong>
                                    <br>
                                    <small class="text-muted">Create backup every month</small>
                                </label>
                            </div>
                        </div>
                        <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="fas fa-robot me-2 text-primary"></i>
                            Auto Backup
                        </h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="auto_backup_enabled"
                                id="auto_backup_enabled" value="1" <?php echo e(($settings->auto_backup_enabled ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="auto_backup_enabled">
                                <strong>Enable Automatic Backups</strong>
                                <br>
                                <small class="text-muted">Automatically create backups according to schedule</small>
                            </label>
                        </div>
                    </div>
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-upload me-2 text-primary"></i>
                                Restore Backup
                            </h5>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Your current data will be replaced with the backup. Proceed carefully.
                            </div>
                            <input type="file" id="backupFile" accept=".json" style="display: none;"
                                onchange="handleFileSelect(event)">
                            <button type="button" class="btn btn-outline-info"
                                onclick="document.getElementById('backupFile').click()">
                                <i class="fas fa-file-upload me-2"></i>
                                Choose Backup File
                            </button>
                            
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Save Settings
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="downloadBackup()">
                                <i class="fas fa-download me-2"></i>
                                Download Backup
                            </button>
                        </div>
                        
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="settings-card">
                    <h5 class="mb-3">
                        <i class="fas fa-info-circle me-2 text-info"></i>
                        Backup Information
                    </h5>
                    <div class="mb-3">
                        <strong>Current Location:</strong>
                        <span class="badge bg-primary ms-2">
                            <?php echo e(ucfirst($settings->backup_location ?? 'local')); ?>

                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Schedule:</strong>
                        <span class="badge bg-info ms-2">
                            <?php echo e(ucfirst($settings->backup_schedule ?? 'weekly')); ?>

                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Last Backup:</strong>
                        <br>
                        <small class="text-muted">
                            <?php echo e($settings->last_backup_at ? $settings->last_backup_at->format('M d, Y H:i') : 'Never'); ?>

                        </small>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Confirmation Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirm Restore
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-database text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h5>Are you sure you want to restore this backup?</h5>
                    <p class="text-muted">This will replace all current data with the backup data. This action cannot be
                        undone.</p>
                    <div class="alert alert-danger">
                        <strong>Warning:</strong> All existing users, orders, and settings will be replaced.
                    </div>
                    <div class="mb-2">
                        <strong>Selected file:</strong> <span id="selectedFileName" class="text-primary"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="confirmRestore()">
                        <i class="fas fa-upload me-2"></i>
                        Restore Backup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedFile = null;

        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.type !== 'application/json') {
                    alert('Please select a valid JSON backup file.');
                    return;
                }

                selectedFile = file;
                document.getElementById('selectedFileName').textContent = file.name;

                // Show confirmation modal
                const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
                modal.show();
            }
        }

        async function confirmRestore() {
            if (!selectedFile) return;

            const formData = new FormData();
            formData.append('backup_file', selectedFile);

            try {
                const response = await fetch('/admin/restore-backup', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('restoreModal')).hide();

                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        Backup restored successfully! The page will reload.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.querySelector('.main-content').insertBefore(alertDiv, document.querySelector('.main-content').firstChild);

                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    alert('Restore failed: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Restore failed:', error);
                alert('Restore failed. Please try again.');
            }

            // Reset file input
            document.getElementById('backupFile').value = '';
            selectedFile = null;
        }

        async function downloadBackup() {
            try {
                const response = await fetch('/admin/backup', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;

                    // Get filename from response headers or generate one
                    const contentDisposition = response.headers.get('content-disposition');
                    let filename = 'cafe_backup_' + new Date().toISOString().slice(0, 19).replace(/:/g, '_') + '.json';

                    if (contentDisposition) {
                        const filenameMatch = contentDisposition.match(/filename="(.+)"/);
                        if (filenameMatch) {
                            filename = filenameMatch[1];
                        }
                    }

                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);

                    // Show success message
                    showAlert('Backup downloaded successfully!', 'success');
                } else {
                    const error = await response.json();
                    showAlert('Backup failed: ' + (error.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Backup failed:', error);
                showAlert('Backup failed. Please try again.', 'error');
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
            }, 5000);
        }
    </script>
</body>

</html><?php /**PATH C:\Users\Laurence Ayo\sip_and_serve_final\resources\views/admin/backup-settings.blade.php ENDPATH**/ ?>