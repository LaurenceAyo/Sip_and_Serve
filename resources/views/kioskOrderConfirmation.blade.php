<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Kiosk</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            text-align: center;
        }

        .header {
            background: #27ae60;
            color: white;
            padding: 30px 20px;
        }

        .checkmark {
            font-size: 60px;
            margin-bottom: 15px;
        }

        .content {
            padding: 30px 20px;
        }

        .order-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ecf0f1;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .total-section {
            background: #2c3e50;
            color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total-row.final {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #34495e;
            padding-top: 10px;
            margin-top: 10px;
        }

        .action-buttons {
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .order-info {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }

        .order-info h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="checkmark">✓</div>
            <h1>Order Confirmed!</h1>
            <p>Your order has been successfully placed</p>
        </div>

        <div class="content">
            <div class="order-info">
                <h4>Order Information</h4>
                <div class="info-row">
                    <span><strong>Order ID:</strong></span>
                    <span>#{{ $order->id }}</span>
                </div>
                <div class="info-row">
                    <span><strong>Order Date:</strong></span>
                    <span>{{ $order->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span><strong>Status:</strong></span>
                    <span>{{ ucfirst($order->status) }}</span>
                </div>
                @if($order->notes)
                    <div class="info-row">
                        <span><strong>Notes:</strong></span>
                        <span>{{ $order->notes }}</span>
                    </div>
                @endif
            </div>

            <div class="order-details">
                <h3>Order Items</h3>
                @foreach($order->orderItems as $orderItem)
                    <div class="order-item">
                        <div>
                            <strong>{{ $orderItem->menuItem->name }}</strong>
                            <br>
                            <small>₱{{ number_format($orderItem->unit_price, 2) }} x {{ $orderItem->quantity }}</small>
                            @if($orderItem->special_instructions)
                                <br>
                                <small><em>Note: {{ $orderItem->special_instructions }}</em></small>
                            @endif
                        </div>
                        <div>
                            <strong>₱{{ number_format($orderItem->total_price, 2) }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="total-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>₱{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="total-row">
                    <span>Tax:</span>
                    <span>₱{{ number_format($order->tax_amount, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="total-row">
                        <span>Discount:</span>
                        <span>-₱{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="total-row final">
                    <span>Total:</span>
                    <span>₱{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4>What's Next?</h4>
                <p>Your order is now in the kitchen queue. Please wait for your order to be prepared.</p>
                <p><strong>Estimated preparation time: 15-30 minutes</strong></p>
            </div>

            <div class="action-buttons">
                <a href="{{ route('kiosk.index') }}" class="btn btn-primary">Place Another Order</a>
                <button onclick="window.print()" class="btn btn-secondary">Print Receipt</button>
            </div>
        </div>
    </div>

    <script>
        // Auto-redirect after 30 seconds
        setTimeout(function() {
            if (confirm('Would you like to return to the main screen?')) {
                window.location.href = '{{ route("kiosk.index") }}';
            }
        }, 30000);
    </script>
</body>
</html>