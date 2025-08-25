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
    </div>

    <div class="kitchen-container">
        <!-- Pending Orders Section -->
        <div class="section">
            <div class="section-header">PENDING ORDERS</div>
            @forelse($pendingOrders as $order)
                <div class="order-card" data-order-id="{{ $order->id }}">
                    <div class="order-header">
                        <div class="order-number">
                            Order#{{ $order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</div>
                        <div class="order-type">{{ ucfirst($order->order_type) }}</div>
                    </div>
                    <div class="order-info">
                        <span>Ordered: {{ $order->created_at->format('g:i A') }}</span>
                        @if($order->order_type === 'dine-in' && $order->table_number)
                            <span>Table {{ $order->table_number }}</span>
                        @elseif($order->order_type === 'takeout')
                            <span>Takeout</span>
                        @endif
                        @if($order->estimated_prep_time)
                            <span>Est: {{ $order->estimated_prep_time }} min</span>
                        @endif
                    </div>
                    <div class="order-items">
                        @foreach($order->orderItems as $item)
                            <div class="order-item">
                                <span class="item-quantity">{{ $item->quantity }}x</span>
                                <span class="item-name">
                                    {{ $item->name ?? $item->menuItem->name }}
                                    @if($item->special_instructions)
                                        <small style="color: #ff6b35;">{{ $item->special_instructions }}</small>
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="order-total">
                        Total: ₱{{ number_format($order->total_amount, 2) }}
                        @if($order->payment_method === 'cash')
                            <small>(Cash: ₱{{ number_format($order->cash_amount, 2) }}, Change:
                                ₱{{ number_format($order->change_amount, 2) }})</small>
                        @else
                            <small>({{ strtoupper($order->payment_method) }})</small>
                        @endif
                    </div>
                    <form action="{{ route('kitchen.start', $order->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="start-button">Start Cooking</button>
                    </form>
                </div>
            @empty
                <div class="empty-section">
                    No pending orders at the moment
                </div>
            @endforelse
        </div>

        <!-- Processing Section - Update this part -->
        <div class="section">
            <div class="section-header">PREPARING</div>
            @forelse($processingOrders as $order)
                <div class="order-card processing-card {{ ($order->is_overdue_calculated ?? false) ? 'overdue-card' : '' }}"
                    data-order-id="{{ $order->id }}">
                    <div class="order-header">
                        <div class="order-number">
                            Order#{{ $order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="timer"
                            data-start-time="{{ $order->started_at ? $order->started_at->toISOString() : '' }}"
                            data-estimated-minutes="{{ $order->estimated_prep_time ?? 30 }}">
                            {{ $order->processing_time_display ?? '00:00' }}
                        </div>
                    </div>

                    <div class="order-info">
                        <span>Started: {{ $order->started_at ? $order->started_at->format('g:i A') : 'Not started' }}</span>
                        @if($order->order_type === 'dine-in' && $order->table_number)
                            <span>Table {{ $order->table_number }}</span>
                        @elseif($order->order_type === 'takeout')
                            <span>Takeout</span>
                        @endif
                        <span>{{ ucfirst($order->order_type) }}</span>
                    </div>

                    @if($order->estimated_completion_time)
                        <div class="estimated-time">
                            Target completion: {{ $order->estimated_completion_time->format('g:i A') }}
                            @if($order->is_overdue_calculated ?? false)
                                <span style="color: #d32f2f; font-weight: bold;">OVERDUE</span>
                            @endif
                        </div>
                    @endif

                    <div class="order-items">
                        @foreach($order->orderItems as $item)
                            <div class="order-item">
                                <span class="item-quantity">{{ $item->quantity }}x</span>
                                <span class="item-name">
                                    {{ $item->name ?? $item->menuItem->name }}
                                    @if($item->special_instructions)
                                        <small style="color: #ff6b35;">{{ $item->special_instructions }}</small>
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <form action="{{ route('kitchen.completeOrder', $order->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="complete-button">Complete Order</button>
                    </form>
                </div>
            @empty
                <div class="empty-section">
                    No orders currently being processed
                </div>
            @endforelse
        </div>

        <!-- Completed Orders Section - Update this part -->
        <div class="section">
            <div class="section-header">RECENTLY COMPLETED</div>
            @forelse($completedOrders as $order)
                <div class="order-card completed-card" data-order-id="{{ $order->id }}">
                    <div class="order-header">
                        <div class="order-number">
                            Order#{{ $order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="completed-time">
                            ✓ {{ $order->total_prep_time_calculated ?? 'N/A' }}min
                        </div>
                    </div>

                    <div class="order-info">
                        <span>Completed: {{ $order->completed_at ? $order->completed_at->format('g:i A') : 'N/A' }}</span>
                        @if($order->order_type === 'dine-in' && $order->table_number)
                            <span>Table {{ $order->table_number }}</span>
                        @elseif($order->order_type === 'takeout')
                            <span>Takeout</span>
                        @endif
                    </div>

                    <div class="order-items">
                        @foreach($order->orderItems as $item)
                            <div class="order-item">
                                <span class="item-quantity">{{ $item->quantity }}x</span>
                                <span class="item-name">
                                    {{ $item->name ?? $item->menuItem->name }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if($order->estimated_prep_time && $order->total_prep_time_calculated)
                        <div class="estimated-time">
                            Estimated: {{ $order->estimated_prep_time }}min |
                            Actual: {{ $order->total_prep_time_calculated }}min
                            @php
                                $variance = $order->total_prep_time_calculated - $order->estimated_prep_time;
                            @endphp
                            @if($variance > 5)
                                <span style="color: #d32f2f;">(+{{ $variance }}min)</span>
                            @elseif($variance < -5)
                                <span style="color: #4caf50;">({{ $variance }}min)</span>
                            @else
                                <span style="color: #4caf50;">(On time)</span>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="empty-section">
                    No recently completed orders
                </div>
            @endforelse
            
        </div>

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

                        // Check if date is valid
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

                        // Change color based on estimated time
                        const halfEstimated = estimatedMinutes * 30; // 50% of estimated time in seconds
                        const fullEstimated = estimatedMinutes * 60; // 100% of estimated time in seconds

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

            // Update timers every second
            setInterval(updateTimers, 1000);

            // Initial timer update
            updateTimers();

            // Play notification when new order arrives
            let lastOrderCount = {{ ($pendingOrders->count() + $processingOrders->count()) ?? 0 }};

            function checkForNewOrders() {
                const currentOrderCount = document.querySelectorAll('.order-card').length;
                if (currentOrderCount > lastOrderCount) {
                    console.log('New order received!');
        
                }
                lastOrderCount = currentOrderCount;
            }

            // Check for new orders every time we refresh
            setInterval(checkForNewOrders, 10000);
        </script>
</body>

</html>