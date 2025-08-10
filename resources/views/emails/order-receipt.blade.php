<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Sip & Serve Receipt</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: bold;
        }
        
        .header p {
            margin: 10px 0 0 0;
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .order-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #8b4513;
        }
        
        .order-info h3 {
            margin: 0 0 15px 0;
            color: #8b4513;
            font-size: 1.3em;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .info-value {
            color: #34495e;
        }
        
        .order-items {
            margin: 25px 0;
        }
        
        .order-items h3 {
            color: #8b4513;
            border-bottom: 2px solid #8b4513;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .item:last-child {
            border-bottom: none;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-weight: bold;
            color: #2c3e50;
            font-size: 1.1em;
            margin-bottom: 5px;
        }
        
        .item-meta {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 3px;
        }
        
        .item-note {
            color: #95a5a6;
            font-style: italic;
            font-size: 0.85em;
        }
        
        .item-price {
            font-weight: bold;
            color: #8b4513;
            font-size: 1.1em;
            margin-left: 20px;
        }
        
        .totals {
            background: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }
        
        .total-row.final {
            border-top: 2px solid #34495e;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 1.2em;
            font-weight: bold;
        }
        
        .payment-info {
            background: #e8f4fd;
            border: 1px solid #bde4ff;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .payment-info h4 {
            color: #2c3e50;
            margin: 0 0 15px 0;
        }
        
        .thank-you {
            text-align: center;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        
        .thank-you h3 {
            margin: 0 0 10px 0;
            color: #155724;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #6c757d;
            font-size: 0.9em;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        .contact-info {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }
        
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .content {
                padding: 20px;
            }
            
            .info-row,
            .total-row,
            .item {
                flex-direction: column;
                text-align: left;
            }
            
            .item-price {
                margin-left: 0;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍽️ Sip & Serve</h1>
            <p>Digital Receipt</p>
        </div>
        
        <div class="content">
            <div class="order-info">
                <h3>📋 Order Information</h3>
                <div class="info-row">
                    <span class="info-label">Order ID:</span>
                    <span class="info-value">#{{ $order->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Number:</span>
                    <span class="info-value">{{ $order->order_number ?? 'C' . str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date & Time:</span>
                    <span class="info-value">{{ $order->created_at->format('F d, Y - h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Type:</span>
                    <span class="info-value">{{ ucfirst($order->order_type ?? 'Dine-in') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">{{ ucfirst($order->status) }}</span>
                </div>
                @if($order->notes)
                    <div class="info-row">
                        <span class="info-label">Notes:</span>
                        <span class="info-value">{{ $order->notes }}</span>
                    </div>
                @endif
            </div>

            <div class="order-items">
                <h3>🛒 Order Items</h3>
                @if($order->orderItems && $order->orderItems->count() > 0)
                    @foreach($order->orderItems as $item)
                        <div class="item">
                            <div class="item-details">
                                <div class="item-name">{{ $item->name ?? $item->menuItem->name ?? 'Custom Item' }}</div>
                                <div class="item-meta">
                                    PHP {{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}
                                </div>
                                @if(isset($item->special_instructions) && $item->special_instructions)
                                    <div class="item-note">Note: {{ $item->special_instructions }}</div>
                                @endif
                                @if(isset($item->addons) && $item->addons)
                                    <div class="item-note">Add-ons: {{ $item->addons }}</div>
                                @endif
                            </div>
                            <div class="item-price">
                               PHP {{ number_format($item->total_price, 2) }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="item">
                        <div class="item-details">
                            <div class="item-name">Sample Item</div>
                            <div class="item-meta">PHP 150.00 × 1</div>
                        </div>
                        <div class="item-price">PHP 150.00</div>
                    </div>
                @endif
            </div>

            <div class="totals">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>PHP {{ number_format($order->subtotal ?? 0, 2) }}</span>
                </div>
                @if($order->tax_amount > 0)
                    <div class="total-row">
                        <span>Tax:</span>
                        <span>PHP {{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                @endif
                @if($order->discount_amount > 0)
                    <div class="total-row">
                        <span>Discount:</span>
                        <span>-PHP {{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="total-row final">
                    <span>Total Amount:</span>
                    <span>PHP {{ number_format($order->total_amount ?? 0, 2) }}</span>
                </div>
            </div>

            @if($order->payment_method)
                <div class="payment-info">
                    <h4>💳 Payment Information</h4>
                    <div class="info-row">
                        <span class="info-label">Payment Method:</span>
                        <span class="info-value">{{ ucfirst($order->payment_method) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment Status:</span>
                        <span class="info-value">{{ ucfirst($order->payment_status ?? 'Paid') }}</span>
                    </div>
                    @if($order->payment_method === 'cash' && $order->cash_amount)
                        <div class="info-row">
                            <span class="info-label">Cash Received:</span>
                            <span class="info-value">PHP {{ number_format($order->cash_amount, 2) }}</span>
                        </div>
                        @if($order->change_amount > 0)
                            <div class="info-row">
                                <span class="info-label">Change Given:</span>
                                <span class="info-value">PHP {{ number_format($order->change_amount, 2) }}</span>
                            </div>
                        @endif
                    @endif
                    @if($order->paid_at)
                        <div class="info-row">
                            <span class="info-label">Paid At:</span>
                            <span class="info-value">{{ $order->paid_at->format('F d, Y - h:i A') }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <div class="thank-you">
                <h3>🎉 Thank You for Your Order!</h3>
                <p>We hope you enjoyed your meal at L' PRIMERO_Sip & Serve system.</p>
                <p><strong>Hope to see you again soon! ☕</strong></p>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>L' PRIMERO Café</strong></p>
            <p>Brew better coffee, Brew coffee better</p>
            <div class="contact-info">
                <p>📧 Email: lprimerocoffee@gmail.com</p>
                <p>📞 Phone: +63 993 688 1248</p>
                <p>📍 Address: Diversion road, Macabog Sorsogon, Bicol Region, Philippines</p>
            </div>
            <p style="margin-top: 15px; font-size: 0.8em;">
                This is an automated receipt. Please keep this as your records.
            </p>
        </div>
    </div>
</body>
</html>