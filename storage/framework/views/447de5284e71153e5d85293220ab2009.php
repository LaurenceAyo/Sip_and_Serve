<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Display System</title>
    <style>
        .kitchen-header {
            background-color: #cb8711;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        .kitchen-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            padding: 20px;
            min-height: 100vh;
        }

        .section {
            background-color: #f5f5f5;
            border-radius: 8px;
            padding: 15px;
        }

        .section-header {
            background-color: #666;
            color: white;
            text-align: center;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            border-radius: 8px 8px 0 0;
            font-weight: bold;
        }
        .archive-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #d9b41d 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .archive-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(118, 220, 164, 0.6);
            background: linear-gradient(135deg, #a7f8bf 0%, #39c88f 100%);
        }

        .archive-btn:active {
            transform: translateY(0);
        }

        .archive-btn svg {
            flex-shrink: 0;
        }
        
        .order-card {
            background-color: white;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .processing-card {
            border-color: #ff6b35;
            background-color: #fff3f0;
        }

        .completed-card {
            border-color: #4caf50;
            background-color: #f1f8e9;
        }

        .overdue-card {
            border-color: #d32f2f;
            background-color: #ffebee;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }

            100% {
                opacity: 1;
            }
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .order-number {
            font-weight: bold;
            color: #d84315;
        }

        .order-type {
            background-color: #e0e0e0;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .timer {
            background-color: #f44336;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-family: monospace;
        }

        .timer.warning {
            background-color: #f57c00;
        }

        .timer.danger {
            background-color: #d32f2f;
        }

        .order-info {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            font-size: 14px;
            color: #666;
        }

        .order-items {
            margin-bottom: 15px;
        }

        .order-item {
            display: flex;
            margin-bottom: 5px;
        }

        .item-quantity {
            font-weight: bold;
            color: #d84315;
            margin-right: 10px;
            min-width: 30px;
        }

        .item-name {
            flex: 1;
        }

        .order-total {
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .estimated-time {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .completed-time {
            font-size: 12px;
            color: #4caf50;
            font-weight: bold;
        }

        .start-button,
        .complete-button {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .start-button:hover,
        .complete-button:hover {
            background-color: #45a049;
        }

        .logout-section {
            position: absolute;
            right: 20px;
            top: 43px;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            color: white;
            font-size: 0.9rem;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .logout-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .logout-modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .logout-modal {
            background: white;
            border-radius: 10px;
            padding: 25px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            transform: scale(0.8);
            transition: transform 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .logout-modal-overlay.show .logout-modal {
            transform: scale(1);
        }

        .logout-modal h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .logout-modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .logout-modal-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .logout-modal-btn-cancel {
            background: #6c757d;
            color: white;
        }

        .logout-modal-btn-confirm {
            background: #dc3545;
            color: white;
        }

        .complete-button {
            background-color: #2196f3;
        }

        .complete-button:hover {
            background-color: #1976d2;
        }

        .empty-section {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="kitchen-header">
        KITCHEN DISPLAY SYSTEM
        <div class="logout-section">
            <button class="logout-btn" onclick="logout()">Logout</button>
        </div>
    </div>

    <div class="kitchen-container">
        <!-- Pending Orders Section -->
        <div class="section">
            <div class="section-header">PENDING ORDERS</div>
            <?php $__empty_1 = true; $__currentLoopData = $pendingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="order-card" data-order-id="<?php echo e($order->id); ?>">
                    <div class="order-header">
                        <div class="order-number">
                            Order#<?php echo e($order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT)); ?></div>
                        <div class="order-type"><?php echo e(ucfirst($order->order_type)); ?></div>
                    </div>
                    <div class="order-info">
                        <span>Ordered: <?php echo e($order->created_at->format('g:i A')); ?></span>
                        <?php if($order->order_type === 'dine-in' && $order->table_number): ?>
                            <span>Table <?php echo e($order->table_number); ?></span>
                        <?php elseif($order->order_type === 'takeout'): ?>
                            <span>Takeout</span>
                        <?php endif; ?>
                        <?php if($order->estimated_prep_time): ?>
                            <span>Est: <?php echo e($order->estimated_prep_time); ?> min</span>
                        <?php endif; ?>
                    </div>
                    <div class="order-items">
                        <?php $__currentLoopData = $order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="order-item">
                                <span class="item-quantity"><?php echo e($item->quantity); ?>x</span>
                                <span class="item-name">
                                    <?php echo e($item->name ?? $item->menuItem->name); ?>

                                    <?php if($item->special_instructions): ?>
                                        <small style="color: #ff6b35;"><?php echo e($item->special_instructions); ?></small>
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="order-total">
                        Total: ₱<?php echo e(number_format($order->total_amount, 2)); ?>

                        <?php if($order->payment_method === 'cash'): ?>
                            <small>(Cash: ₱<?php echo e(number_format($order->cash_amount, 2)); ?>, Change:
                                ₱<?php echo e(number_format($order->change_amount, 2)); ?>)</small>
                        <?php else: ?>
                            <small>(<?php echo e(strtoupper($order->payment_method)); ?>)</small>
                        <?php endif; ?>
                    </div>
                    <form action="<?php echo e(route('kitchen.start', $order->id)); ?>" method="POST" style="display: inline;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="start-button">Start Cooking</button>
                    </form>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-section">
                    No pending orders at the moment
                </div>
            <?php endif; ?>
        </div>

        <!-- Processing Section -->
        <div class="section">
            <div class="section-header">PREPARING</div>
            <?php $__empty_1 = true; $__currentLoopData = $processingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="order-card processing-card <?php echo e(($order->is_overdue_calculated ?? false) ? 'overdue-card' : ''); ?>"
                    data-order-id="<?php echo e($order->id); ?>">
                    <div class="order-header">
                        <div class="order-number">
                            Order#<?php echo e($order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT)); ?>

                        </div>
                        <div class="timer"
                            data-start-time="<?php echo e($order->started_at ? $order->started_at->toISOString() : ''); ?>"
                            data-estimated-minutes="<?php echo e($order->estimated_prep_time ?? 30); ?>">
                            <?php echo e($order->processing_time_display ?? '00:00'); ?>

                        </div>
                    </div>

                    <div class="order-info">
                        <span>Started: <?php echo e($order->started_at ? $order->started_at->format('g:i A') : 'Not started'); ?></span>
                        <?php if($order->order_type === 'dine-in' && $order->table_number): ?>
                            <span>Table <?php echo e($order->table_number); ?></span>
                        <?php elseif($order->order_type === 'takeout'): ?>
                            <span>Takeout</span>
                        <?php endif; ?>
                        <span><?php echo e(ucfirst($order->order_type)); ?></span>
                    </div>

                    <?php if($order->estimated_completion_time): ?>
                        <div class="estimated-time">
                            Target completion: <?php echo e($order->estimated_completion_time->format('g:i A')); ?>

                            <?php if($order->is_overdue_calculated ?? false): ?>
                                <span style="color: #d32f2f; font-weight: bold;">OVERDUE</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="order-items">
                        <?php $__currentLoopData = $order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="order-item">
                                <span class="item-quantity"><?php echo e($item->quantity); ?>x</span>
                                <span class="item-name">
                                    <?php echo e($item->name ?? $item->menuItem->name); ?>

                                    <?php if($item->special_instructions): ?>
                                        <small style="color: #ff6b35;"><?php echo e($item->special_instructions); ?></small>
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <form action="<?php echo e(route('kitchen.completeOrder', $order->id)); ?>" method="POST" style="display: inline;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="complete-button">Complete Order</button>
                    </form>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-section">
                    No orders currently being processed
                </div>
            <?php endif; ?>
        </div>

        <!-- Completed Orders Section -->
        <div class="section">
            <div class="section-header">RECENTLY COMPLETED</div>
            <?php $__empty_1 = true; $__currentLoopData = $completedOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="order-card completed-card" data-order-id="<?php echo e($order->id); ?>">
                    <div class="order-header">
                        <div class="order-number">
                            Order#<?php echo e($order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT)); ?>

                        </div>
                    </div>

                    <div class="order-info">
                        <span>Completed: <?php echo e($order->completed_at ? $order->completed_at->format('g:i A') : 'N/A'); ?></span>
                        <?php if($order->order_type === 'dine-in' && $order->table_number): ?>
                            <span>Table <?php echo e($order->table_number); ?></span>
                        <?php elseif($order->order_type === 'takeout'): ?>
                            <span>Takeout</span>
                        <?php endif; ?>
                    </div>

                    <div class="order-items">
                        <?php $__currentLoopData = $order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="order-item">
                                <span class="item-quantity"><?php echo e($item->quantity); ?>x</span>
                                <span class="item-name">
                                    <?php echo e($item->name ?? $item->menuItem->name); ?>

                                </span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <?php if($order->estimated_prep_time && $order->total_prep_time_calculated): ?>
                        <div class="estimated-time">
                            Estimated: <?php echo e($order->estimated_prep_time); ?>min |
                            Actual: <?php echo e($order->total_prep_time_calculated); ?>min
                            <?php
                                $variance = $order->total_prep_time_calculated - $order->estimated_prep_time;
                            ?>
                            <?php if($variance > 5): ?>
                                <span style="color: #d32f2f;">(+<?php echo e($variance); ?>min)</span>
                            <?php elseif($variance < -5): ?>
                                <span style="color: #4caf50;">(<?php echo e($variance); ?>min)</span>
                            <?php else: ?>
                                <span style="color: #4caf50;">(On time)</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-section">
                    No recently completed orders
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Archive Button (Fixed Bottom Right) -->
    <?php if($completedOrders->count() > 0): ?>
        <button class="archive-btn" onclick="showArchiveModal()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 8v13H3V8M1 3h22v5H1zM10 12h4"></path>
            </svg>
            Move to Database
        </button>
    <?php endif; ?>

    <script>
        // Auto-refresh the page every 10 seconds to get new orders from the cashier
        setInterval(function () {
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const newDoc = parser.parseFromString(html, 'text/html');

                    // Update all sections
                    const sections = document.querySelectorAll('.section');
                    const newSections = newDoc.querySelectorAll('.section');

                    sections.forEach((section, index) => {
                        if (newSections[index]) {
                            section.innerHTML = newSections[index].innerHTML;
                        }
                    });

                    // Update archive button visibility
                    const archiveBtn = document.querySelector('.archive-btn');
                    const newArchiveBtn = newDoc.querySelector('.archive-btn');
                    if (archiveBtn && !newArchiveBtn) {
                        archiveBtn.style.display = 'none';
                    } else if (!archiveBtn && newArchiveBtn) {
                        document.body.appendChild(newArchiveBtn.cloneNode(true));
                    }
                })
                .catch(error => {
                    console.log('Refresh failed, will try again:', error);
                });
        }, 10000); // Refresh every 10 seconds

        // Update processing timers every second
        function updateTimers() {
            const timers = document.querySelectorAll('.timer[data-start-time]');
            timers.forEach(timer => {
                const startTimeStr = timer.getAttribute('data-start-time');
                const estimatedMinutes = parseInt(timer.getAttribute('data-estimated-minutes')) || 30;

                if (!startTimeStr || startTimeStr === '') {
                    timer.textContent = '00:00';
                    return;
                }

                try {
                    const startTime = new Date(startTimeStr);

                    if (isNaN(startTime.getTime())) {
                        timer.textContent = '00:00';
                        return;
                    }

                    const now = new Date();
                    const diffMs = now.getTime() - startTime.getTime();
                    const diffSeconds = Math.floor(diffMs / 1000);

                    const minutes = Math.floor(diffSeconds / 60);
                    const seconds = diffSeconds % 60;

                    timer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                    const halfEstimated = estimatedMinutes * 30;
                    const fullEstimated = estimatedMinutes * 60;

                    timer.className = 'timer';
                    if (diffSeconds > fullEstimated) {
                        timer.classList.add('danger');
                    } else if (diffSeconds > halfEstimated) {
                        timer.classList.add('warning');
                    }
                } catch (error) {
                    console.error('Error parsing date:', startTimeStr, error);
                    timer.textContent = '00:00';
                }
            });
        }

        setInterval(updateTimers, 1000);
        updateTimers();

        let lastOrderCount = <?php echo e(($pendingOrders->count() + $processingOrders->count()) ?? 0); ?>;

        function checkForNewOrders() {
            const currentOrderCount = document.querySelectorAll('.order-card').length;
            if (currentOrderCount > lastOrderCount) {
                console.log('New order received!');
            }
            lastOrderCount = currentOrderCount;
        }

        setInterval(checkForNewOrders, 10000);

        // Logout Functions
        function logout() {
            document.getElementById('logoutModal').classList.add('show');
        }

        function hideLogoutModal() {
            document.getElementById('logoutModal').classList.remove('show');
        }

        function confirmLogout() {
            fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' } })
                .then(() => window.location.href = '/')
                .catch(() => window.location.href = '/');
        }

        // Archive Functions
        function showArchiveModal() {
            document.getElementById('archiveModal').classList.add('show');
        }

        function hideArchiveModal() {
            document.getElementById('archiveModal').classList.remove('show');
        }

        function confirmArchive() {
            const form = document.getElementById('archiveForm');
            form.submit();
        }
    </script>

    <!-- Logout Modal -->
    <div class="logout-modal-overlay" id="logoutModal">
        <div class="logout-modal">
            <h3>Confirm Logout</h3>
            <p>Are you sure you want to logout?</p>
            <div class="logout-modal-actions">
                <button class="logout-modal-btn logout-modal-btn-cancel" onclick="hideLogoutModal()">Cancel</button>
                <button class="logout-modal-btn logout-modal-btn-confirm" onclick="confirmLogout()">Logout</button>
            </div>
        </div>
    </div>

    <!-- Archive Modal -->
    <div class="logout-modal-overlay" id="archiveModal">
        <div class="logout-modal">
            <h3>Archive Completed Orders</h3>
            <p>This will move all completed orders to the database archive. Continue?</p>
            <div class="logout-modal-actions">
                <button class="logout-modal-btn logout-modal-btn-cancel" onclick="hideArchiveModal()">Cancel</button>
                <button class="logout-modal-btn logout-modal-btn-confirm" onclick="confirmArchive()">Archive</button>
            </div>
        </div>
    </div>

    <!-- Hidden Archive Form -->
    <form id="archiveForm" action="<?php echo e(route('kitchen.archive')); ?>" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
    </form>
    
</body>

</html><?php /**PATH C:\Users\Laurence Ayo\sip_and_serve_final\resources\views/kitchen.blade.php ENDPATH**/ ?>