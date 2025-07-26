<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Display</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .kitchen-header {
            background-color: #B8651A;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .kitchen-container {
            display: flex;
            flex: 1;
            height: calc(100vh - 88px);
        }

        .section {
            flex: 1;
            padding: 20px;
            border-right: 2px solid #333;
        }

        .section:last-child {
            border-right: none;
        }

        .section-header {
            background-color: #e0e0e0;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .order-card {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .order-number {
            background-color: #ff9800;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 16px;
        }

        .order-type {
            background-color: #4CAF50;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .order-info {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .order-items {
            margin-bottom: 15px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-quantity {
            font-weight: bold;
            color: #333;
            margin-right: 10px;
        }

        .item-name {
            flex: 1;
            color: #333;
        }

        .start-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .start-button:hover {
            background-color: #45a049;
        }

        .complete-button {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .complete-button:hover {
            background-color: #1976D2;
        }

        .processing-card {
            border-left: 4px solid #ff9800;
        }

        .empty-section {
            text-align: center;
            color: #999;
            font-style: italic;
            margin-top: 50px;
        }

        .special-instructions {
            color: #ff6b35;
            font-size: 12px;
            font-style: italic;
            display: block;
            margin-top: 2px;
        }

        .order-type.takeout {
            background-color: #2196F3;
        }

        .order-type.dine-in {
            background-color: #4CAF50;
        }

        .new-order {
            animation: highlightNew 2s ease-in-out;
        }

        @keyframes highlightNew {
            0% { background-color: #fff3e0; }
            50% { background-color: #ffcc80; }
            100% { background-color: white; }
        }

        .urgent-order {
            border-left: 4px solid #f44336;
        }

        .urgent-order .timer {
            background-color: #d32f2f;
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="kitchen-header">
        KITCHEN
    </div>

    <div class="kitchen-container">
        <!-- Pending Orders Section -->
        <div class="section">
            <div class="section-header">PENDING ORDERS</div>
            
            @forelse($pendingOrders as $order)
                <div class="order-card" data-order-id="{{ $order->id }}">
                    <div class="order-header">
                        <div class="order-number">Order#{{ $order->id }}</div>
                        <div class="order-type">{{ ucfirst($order->order_type) }}</div>
                    </div>
                    
                    <div class="order-info">
                        <span>{{ $order->created_at->format('g:i a') }}</span>
                        @if($order->order_type === 'dine-in' && $order->table_number)
                            <span>Table {{ $order->table_number }}</span>
                        @elseif($order->order_type === 'takeout')
                            <span>Takeout</span>
                        @endif
                        <span>{{ $order->estimated_prep_time ?? '30' }} min</span>
                    </div>
                    
                    <div class="order-items">
                        @foreach($order->orderItems as $item)
                            <div class="order-item">
                                <span class="item-quantity">{{ $item->quantity }}x</span>
                                <span class="item-name">
                                    {{ $item->menuItem->name }}
                                    @if($item->special_instructions)
                                        <small style="color: #ff6b35;">{{ $item->special_instructions }}</small>
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                    
                    <form action="{{ route('kitchen.start', $order->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="start-button">Start</button>
                    </form>
                </div>
            @empty
                <!-- Sample order card when no orders exist -->
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-number">Order#1</div>
                        <div class="order-type">Dine-in</div>
                    </div>
                    
                    <div class="order-info">
                        <span>8:09 am</span>
                        <span>Table 1</span>
                        <span>30 min</span>
                    </div>
                    
                    <div class="order-items">
                        <div class="order-item">
                            <span class="item-quantity">2x</span>
                            <span class="item-name">Espresso.milkFoam</span>
                        </div>
                        <div class="order-item">
                            <span class="item-quantity">1x</span>
                            <span class="item-name">Pad Thai.noPrawn</span>
                        </div>
                    </div>
                    
                    <button class="start-button">Start</button>
                </div>
            @endforelse
        </div>

        <!-- Processing Section -->
        <div class="section">
            <div class="section-header">PROCESSING</div>
            
            @forelse($processingOrders as $order)
                <div class="order-card processing-card" data-order-id="{{ $order->id }}">
                    <div class="order-header">
                        <div class="order-number">Order#{{ $order->id }}</div>
                        <div class="timer" data-start-time="{{ $order->started_at }}">
                            {{ $order->processing_time ?? '00:00' }}
                        </div>
                    </div>
                    
                    <div class="order-info">
                        <span>{{ $order->created_at->format('g:i a') }}</span>
                        @if($order->order_type === 'dine-in' && $order->table_number)
                            <span>Table {{ $order->table_number }}</span>
                        @elseif($order->order_type === 'takeout')
                            <span>Takeout</span>
                        @endif
                        <span>{{ ucfirst($order->order_type) }}</span>
                    </div>
                    
                    <div class="order-items">
                        @foreach($order->orderItems as $item)
                            <div class="order-item">
                                <span class="item-quantity">{{ $item->quantity }}x</span>
                                <span class="item-name">
                                    {{ $item->menuItem->name }}
                                    @if($item->special_instructions)
                                        <small style="color: #ff6b35;">{{ $item->special_instructions }}</small>
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                    
                    <form action="{{ route('kitchen.complete', $order->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="complete-button">Complete</button>
                    </form>
                </div>
            @empty
                <div class="empty-section">
                    No orders currently being processed
                </div>
            @endforelse
        </div>
    </div>

    <script>
        // Auto-refresh the page every 10 seconds to get new orders from kiosk
        setInterval(function() {
            // Use AJAX to refresh data without full page reload
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Update only the content sections
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');
                
                // Update pending orders
                const currentPending = document.querySelector('.section:first-child');
                const newPending = newDoc.querySelector('.section:first-child');
                if (newPending) {
                    currentPending.innerHTML = newPending.innerHTML;
                }
                
                // Update processing orders
                const currentProcessing = document.querySelector('.section:last-child');
                const newProcessing = newDoc.querySelector('.section:last-child');
                if (newProcessing) {
                    currentProcessing.innerHTML = newProcessing.innerHTML;
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
                const startTime = new Date(timer.getAttribute('data-start-time'));
                const now = new Date();
                const diffMs = now - startTime;
                const diffSeconds = Math.floor(diffMs / 1000);
                
                const minutes = Math.floor(diffSeconds / 60);
                const seconds = diffSeconds % 60;
                
                timer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                // Change color based on time
                if (diffSeconds > 1800) { // 30 minutes
                    timer.style.backgroundColor = '#d32f2f';
                } else if (diffSeconds > 900) { // 15 minutes
                    timer.style.backgroundColor = '#f57c00';
                } else {
                    timer.style.backgroundColor = '#f44336';
                }
            });
        }

        // Update timers every second
        setInterval(updateTimers, 1000);

        // Play notification sound when new order arrives
        let lastOrderCount = {{ ($pendingOrders->count() + $processingOrders->count()) ?? 0 }};
        
        function checkForNewOrders() {
            const currentOrderCount = document.querySelectorAll('.order-card').length;
            if (currentOrderCount > lastOrderCount) {
                // New order detected - you can add sound notification here
                console.log('New order received!');
                // playNotificationSound();
            }
            lastOrderCount = currentOrderCount;
        }

        // Check for new orders every time we refresh
        setInterval(checkForNewOrders, 10000);
    </script>
</body>
</html>