<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sip & Serve - Cashier</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f1e8, #F5E6D3);
            height: 100vh;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            text-align: center;
            padding: 25px;
            font-size: 2.2rem;
            font-weight: bold;
            letter-spacing: 3px;
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
            position: relative;
        }

        .header::before {
            content: '☕';
            position: absolute;
            left: 30px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2rem;
        }

        .header::after {
            content: '🍽️';
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2rem;
        }

        .refresh-button {
            position: absolute;
            right: 100px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .refresh-button:hover {
            background: white;
            color: #8b4513;
        }

        .container {
            display: flex;
            height: calc(100vh - 100px);
        }

        .left-panel {
            flex: 1;
            background: #f5f1e8;
            padding: 25px;
            border-right: 3px solid #d4c4a8;
            overflow-y: auto;
        }

        .right-panel {
            flex: 1;
            background: #F5E6D3;
            padding: 25px;
            overflow-y: auto;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: bold;
            color: #2c1810;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding-bottom: 10px;
            border-bottom: 3px solid #8b4513;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::before {
            content: '📋';
            font-size: 1.2rem;
        }

        .order-card {
            background: white;
            border: 3px solid #d4c4a8;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.15);
            transition: all 0.3s ease;
            position: relative;
        }

        .order-card:hover {
            border-color: #8b4513;
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.25);
            transform: translateY(-2px);
        }

        .order-card.selected {
            border-color: #27ae60;
            background: linear-gradient(135deg, #f8fff9, #e8f5e8);
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.3);
        }

        .order-header {
            font-weight: bold;
            font-size: 1.3rem;
            margin-bottom: 8px;
            color: #2c1810;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-number {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .order-time {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .order-time::before {
            content: '⏰';
            font-size: 1rem;
        }

        .order-type {
            background: #e8f4fd;
            color: #2c3e50;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-items {
            margin: 15px 0;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 8px 0;
            padding: 8px 0;
            color: #2c1810;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-name {
            flex: 1;
            font-weight: 500;
        }

        .item-price {
            font-weight: bold;
            color: #8b4513;
            font-size: 1.05rem;
        }

        .order-total {
            border-top: 2px solid #d4c4a8;
            padding-top: 15px;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            color: #2c1810;
            font-size: 1.1rem;
        }

        .order-total .total-amount {
            color: #8b4513;
            font-size: 1.2rem;
        }

        .cash-details {
            background: #fff3cd;
            border: 2px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
        }

        .cash-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            color: #856404;
            font-weight: 600;
        }

        .cash-row.change {
            border-top: 1px solid #ffeaa7;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 1.1rem;
        }

        .order-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            flex: 1;
            min-width: 90px;
        }

        .btn-accept {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }

        .btn-accept:hover {
            background: linear-gradient(135deg, #229954, #27ae60);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }

        .btn-edit {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #e67e22, #d35400);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4);
        }

        .btn-cancel {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .btn-cancel:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
        }

        .processing-section {
            background: white;
            border: 3px solid #d4c4a8;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            color: #666;
            font-size: 1.1rem;
            line-height: 1.8;
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.1);
            min-height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .processing-section.has-order {
            text-align: left;
            padding: 30px;
        }

        .processing-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.6;
        }

        .processing-order-header {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
        }

        .processing-order-number {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .processing-order-time {
            font-size: 1rem;
            opacity: 0.9;
        }

        .processing-items {
            margin-bottom: 25px;
        }

        .processing-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            color: #2c1810;
        }

        .processing-item:last-child {
            border-bottom: none;
        }

        .processing-total {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            margin-bottom: 25px;
        }

        .processing-total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .processing-total-final {
            border-top: 2px solid #d4c4a8;
            padding-top: 15px;
            margin-top: 15px;
            font-weight: bold;
            font-size: 1.3rem;
            color: #8b4513;
        }

        .processing-actions {
            display: flex;
            gap: 15px;
        }

        .btn-large {
            padding: 15px 25px;
            font-size: 1.1rem;
            flex: 1;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background: #cce5ff;
            color: #0066cc;
        }

        .empty-state {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 40px 20px;
            background: #f8f9fa;
            border: 2px dashed #ddd;
            border-radius: 15px;
            margin-top: 20px;
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* Enhanced Payment Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(44, 24, 16, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal {
            background: white;
            width: 90%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 25px 50px rgba(139, 69, 19, 0.4);
            overflow: hidden;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .modal-header.cancel {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .modal-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .modal-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }

        .modal-content {
            padding: 30px 25px;
        }

        .payment-form {
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: bold;
            color: #2c1810;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            border: 3px solid #d4c4a8;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #8b4513;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        .form-input.error {
            border-color: #e74c3c;
            background: #fdf2f2;
        }

        .payment-summary {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .summary-row.total {
            border-top: 2px solid #d4c4a8;
            padding-top: 15px;
            margin-top: 15px;
            font-weight: bold;
            font-size: 1.3rem;
            color: #8b4513;
        }

        .summary-row.change {
            background: #d4edda;
            border: 2px solid #c3e6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 15px -5px 10px;
            font-weight: bold;
            font-size: 1.4rem;
            color: #155724;
        }

        .summary-row.change.negative {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .modal-message {
            font-size: 1.1rem;
            color: #2c1810;
            line-height: 1.5;
            margin-bottom: 25px;
            text-align: center;
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .modal-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            min-width: 120px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .modal-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .modal-btn-cancel {
            background: #6c757d;
            color: white;
        }

        .modal-btn-cancel:hover:not(:disabled) {
            background: #5a6268;
        }

        .modal-btn-confirm {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .modal-btn-confirm:hover:not(:disabled) {
            background: linear-gradient(135deg, #229954, #27ae60);
            transform: translateY(-1px);
        }

        .modal-btn-confirm.cancel-style {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .modal-btn-confirm.cancel-style:hover:not(:disabled) {
            background: linear-gradient(135deg, #c0392b, #a93226);
        }

        .customer-payment-info {
            background: #e3f2fd;
            border: 2px solid #90caf9;
            border-radius: 10px;
            padding: 12px 15px;
            margin: 15px 0;
            color: #0d47a1;
        }

        .payment-info-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .payment-info-row.expected-change {
            border-top: 1px solid #90caf9;
            padding-top: 8px;
            margin-top: 8px;
            font-size: 1rem;
            color: #1565c0;
        }

        .payment-info-label {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .edit-amount-note {
            background: #fff3e0;
            border: 1px solid #ffcc02;
            border-radius: 6px;
            padding: 8px 12px;
            margin-top: 10px;
            font-size: 0.85rem;
            color: #e65100;
            text-align: center;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .loading.show {
            display: block;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #8b4513;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Quick cash buttons */
        .quick-cash-buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        .quick-cash-btn {
            padding: 10px;
            border: 2px solid #d4c4a8;
            background: #f8f9fa;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            color: #2c1810;
            transition: all 0.3s ease;
        }

        .quick-cash-btn:hover {
            background: #8b4513;
            color: white;
            border-color: #8b4513;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
                height: auto;
            }

            .left-panel,
            .right-panel {
                flex: none;
                height: auto;
            }

            .order-actions {
                flex-direction: column;
            }

            .btn {
                flex: none;
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 15px;
                font-size: 1.8rem;
            }

            .header::before,
            .header::after {
                display: none;
            }

            .left-panel,
            .right-panel {
                padding: 15px;
            }

            .processing-actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        SIP & SERVE - CASHIER
        <button class="refresh-button" onclick="refreshOrders()">🔄 Refresh</button>
    </div>

    <div class="container">
        <!-- Left Panel - Pending Cash Orders -->
        <div class="left-panel">
            <h2 class="section-title">Pending Cash Orders</h2>

            <div class="loading" id="loading">
                <div class="loading-spinner"></div>
                <p>Loading orders...</p>
            </div>

            <div id="ordersContainer">
                @if(isset($pendingOrders) && count($pendingOrders) > 0)
                    @foreach($pendingOrders as $order)
                        <div class="order-card" id="order-{{ $order['id'] }}" data-order-id="{{ $order['id'] }}">
                            <div class="order-header">
                                <span>Order</span>
                                <span class="order-number">#{{ $order['id'] }}</span>
                            </div>
                            <div class="order-time">
                                Placed at {{ $order['time'] }}
                                <span class="order-type">{{ ucfirst($order['order_type'] ?? 'dine-in') }}</span>
                            </div>
                            <div class="status-badge status-pending">Pending Payment</div>

                            <div class="order-items">
                                @foreach($order['items'] as $item)
                                    <div class="order-item">
                                        <span class="item-name">{{ $item['name'] }}</span>
                                        <span class="item-price">PHP {{ number_format($item['price'], 2) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            @if(isset($order['cash_amount']) && $order['cash_amount'] > 0)
                                <div class="customer-payment-info">
                                    <div
                                        style="font-weight: 700; margin-bottom: 8px; color: #1565c0; display: flex; align-items: center; gap: 8px;">
                                        💰 Customer's Payment Plan
                                    </div>
                                    <div class="payment-info-row">
                                        <span class="payment-info-label">🏷️ Order Total:</span>
                                        <span>PHP {{ number_format($order['total'], 2) }}</span>
                                    </div>
                                    <div class="payment-info-row">
                                        <span class="payment-info-label">💵 Will Bring:</span>
                                        <span>PHP {{ number_format($order['cash_amount'], 2) }}</span>
                                    </div>
                                    @if($order['expected_change'] > 0)
                                        <div class="payment-info-row expected-change">
                                            <span class="payment-info-label">💸 Expected Change:</span>
                                            <span>PHP {{ number_format($order['expected_change'], 2) }}</span>
                                        </div>
                                    @endif
                                    <div class="edit-amount-note">
                                        💡 You can edit the cash amount during payment processing
                                    </div>
                                </div>
                            @endif

                            <div class="order-total">
                                <span>Total Amount:</span>
                                <span class="total-amount">PHP {{ number_format($order['total'], 2) }}</span>
                            </div>

                            <div class="order-actions">
                                <button class="btn btn-accept"
                                    onclick="acceptOrder('{{ $order['id'] }}', {{ $order['total'] }}, '{{ $order['id'] }}', {{ $order['cash_amount'] ?? 0 }})">
                                    ✅ Accept
                                </button>
                                <button class="btn btn-edit" onclick="editOrder('{{ $order['id'] }}')">
                                    ✏️ Edit
                                </button>
                                <button class="btn btn-cancel" onclick="cancelOrder('{{ $order['id'] }}')">
                                    ❌ Cancel
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Empty state when no orders -->
                    <div class="empty-state" id="emptyState">
                        <div class="empty-state-icon">📭</div>
                        <p>No pending cash orders</p>
                        <small>Orders will appear here when customers place cash orders</small>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Panel - Processing Order Section -->
        <div class="right-panel">
            <h2 class="section-title">Processing Payment</h2>

            <div class="processing-section" id="processingSection">
                <div class="processing-icon">💳</div>
                <p><strong>SELECT AN ORDER</strong></p>
                <p>FROM THE LEFT PANEL TO</p>
                <p>BEGIN PROCESSING PAYMENT</p>
                <br>
                <p>Use the <strong>Accept</strong> button to process payment</p>
                <p>Use the <strong>Edit</strong> button to modify orders</p>
                <p>Use the <strong>Cancel</strong> button to cancel orders</p>
            </div>
        </div>
    </div>

    <!-- Payment Processing Modal -->
    <div class="modal-overlay" id="paymentModal">
        <div class="modal">
            <div class="modal-header" id="paymentModalHeader">
                <div class="modal-icon">💰</div>
                <h2 class="modal-title">Process Payment</h2>
                <p class="modal-subtitle">Calculate change and complete transaction</p>
            </div>

            <div class="modal-content">
                <div class="payment-form">
                    <div class="form-group">
                        <label class="form-label">Cash Received (PHP):</label>
                        <input type="number" id="cashAmount" class="form-input" step="0.01" min="0" placeholder="0.00"
                            oninput="calculateChange()">

                        <!-- Quick cash amount buttons -->
                        <div class="quick-cash-buttons" id="quickCashButtons">
                            <!-- Will be populated dynamically based on order total -->
                        </div>
                    </div>
                </div>

                <div class="payment-summary">
                    <div class="summary-row">
                        <span>Order Total:</span>
                        <span id="orderTotalDisplay">PHP 0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Cash Received:</span>
                        <span id="cashReceivedDisplay">PHP 0.00</span>
                    </div>
                    <div class="summary-row change" id="changeDisplay">
                        <span>💰 Change:</span>
                        <span id="changeAmount">PHP 0.00</span>
                    </div>
                </div>

                <div class="modal-actions">
                    <button class="modal-btn modal-btn-cancel" onclick="hidePaymentModal()">
                        Cancel
                    </button>
                    <button class="modal-btn modal-btn-confirm" id="confirmPaymentBtn" onclick="confirmPayment()"
                        disabled>
                        Process Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal-overlay" id="confirmModal">
        <div class="modal">
            <div class="modal-header" id="modalHeader">
                <div class="modal-icon" id="modalIcon">⚠️</div>
                <h2 class="modal-title" id="modalTitle">Confirm Action</h2>
                <p class="modal-subtitle" id="modalSubtitle">Please confirm your action</p>
            </div>

            <div class="modal-content">
                <div class="modal-message" id="modalMessage">
                    Are you sure you want to perform this action?
                </div>

                <div class="modal-actions">
                    <button class="modal-btn modal-btn-cancel" onclick="hideModal()">
                        Cancel
                    </button>
                    <button class="modal-btn modal-btn-confirm" id="confirmBtn" onclick="confirmAction()">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Cashier System with Order Processing Workflow - Fixed Amount Display Bug
        let currentAction = null;
        let currentOrderId = null;
        let currentAmount = 0;
        let currentOrderNumber = '';
        let orderTotal = 0;
        let cashReceived = 0;
        let changeAmount = 0;
        let processingOrders = new Map();

        function debugLog(message, data = null) {
            console.log(`[Cashier Debug] ${message}`, data);
        }

        function getCSRFToken() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) {
                console.error('CSRF token not found in page');
                return null;
            }
            return token.getAttribute('content');
        }

        // Fixed: Calculate total amount from order items if not provided
        function calculateOrderTotal(order) {
            // First try to use the provided total_amount
            let total = parseFloat(order.total_amount);

            // If total_amount is null, undefined, NaN, or 0, calculate from items
            if (!total || isNaN(total) || total <= 0) {
                total = 0;
                if (order.order_items && Array.isArray(order.order_items)) {
                    order.order_items.forEach(item => {
                        const itemTotal = parseFloat(item.total_price || item.price || 0);
                        if (!isNaN(itemTotal)) {
                            total += itemTotal;
                        }
                    });
                }
            }

            debugLog('Calculated order total', {
                originalTotal: order.total_amount,
                calculatedTotal: total,
                orderItems: order.order_items
            });

            return total;
        }

        async function fetchWithErrorHandling(url, options = {}) {
            const csrfToken = getCSRFToken();
            if (!csrfToken) {
                throw new Error('CSRF token not available');
            }

            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            };

            const finalOptions = {
                ...defaultOptions,
                ...options,
                headers: {
                    ...defaultOptions.headers,
                    ...options.headers
                }
            };

            debugLog(`Making request to: ${url}`, finalOptions);

            try {
                const response = await fetch(url, finalOptions);

                debugLog(`Response status: ${response.status}`, {
                    ok: response.ok,
                    statusText: response.statusText
                });

                if (!response.ok) {
                    let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
                    try {
                        const errorData = await response.json();
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                        if (errorData.errors) {
                            errorMessage += ' - ' + JSON.stringify(errorData.errors);
                        }
                    } catch (e) {
                        // If we can't parse JSON, use default message
                    }
                    throw new Error(errorMessage);
                }

                const data = await response.json();
                debugLog('Response data:', data);
                return data;
            } catch (error) {
                debugLog('Fetch error:', error);
                throw error;
            }
        }

        function refreshOrders() {
            const loading = document.getElementById('loading');
            const refreshBtn = document.querySelector('.refresh-button');

            if (loading) loading.classList.add('show');
            if (refreshBtn) {
                refreshBtn.disabled = true;
                refreshBtn.textContent = '⏳ Refreshing...';
            }

            fetchWithErrorHandling('/cashier/refresh', {
                method: 'GET'
            })
                .then(data => {
                    if (data.success) {
                        updateOrdersDisplay(data.orders);
                        debugLog('Orders refreshed successfully', data.orders);
                    } else {
                        throw new Error(data.message || 'Failed to refresh orders');
                    }
                })
                .catch(error => {
                    console.error('Error refreshing orders:', error);
                    showErrorMessage('Failed to refresh orders: ' + error.message);
                })
                .finally(() => {
                    if (loading) loading.classList.remove('show');
                    if (refreshBtn) {
                        refreshBtn.disabled = false;
                        refreshBtn.textContent = '🔄 Refresh';
                    }
                });
        }

        function updateOrdersDisplay(orders) {
            const container = document.getElementById('ordersContainer');
            const emptyState = document.getElementById('emptyState');

            if (!container) {
                console.error('Orders container not found');
                return;
            }

            container.innerHTML = '';

            if (!orders || orders.length === 0) {
                if (emptyState) {
                    container.appendChild(emptyState);
                } else {
                    container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <p>No pending cash orders</p>
                    <small>Orders will appear here when customers place cash orders</small>
                </div>
            `;
                }
            } else {
                let hasPendingOrders = false;

                orders.forEach(order => {
                    if (order.payment_status === 'paid' && order.status === 'preparing') {
                        // Move to processing panel automatically
                        const orderNumber = order.order_number || order.id.toString().padStart(4, '0');
                        const orderTotal = calculateOrderTotal(order);
                        const actualChange = parseFloat(order.cash_amount || 0) - orderTotal;
                        showProcessingOrder(order.id, orderNumber, actualChange, true);
                    } else if (order.payment_status === 'pending') {
                        // Show pending orders in left panel
                        const orderCard = createOrderCard(order);
                        container.appendChild(orderCard);
                        hasPendingOrders = true;
                    }
                });

                // If no pending orders were added, show empty state
                if (!hasPendingOrders) {
                    container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <p>No pending cash orders</p>
                    <small>Orders will appear here when customers place cash orders</small>
                </div>
            `;
                }
            }
        }

        function createOrderCard(order) {
            const orderCard = document.createElement('div');
            orderCard.className = 'order-card';
            orderCard.id = `order-${order.id}`;
            orderCard.setAttribute('data-order-id', order.id);

            let itemsHtml = '';
            let itemsTotal = 0;

            if (order.order_items && Array.isArray(order.order_items)) {
                order.order_items.forEach(item => {
                    // Clean the item name to remove any existing quantity info
                    let itemName = item.name || 'Custom Item';

                    // Remove any existing quantity patterns like "x1", "x2", " x1", " x2", etc.
                    itemName = itemName.replace(/\s*x\d+\s*$/, '').trim();

                    const quantity = parseInt(item.quantity) || 1;
                    const totalPrice = parseFloat(item.total_price || item.price || 0);

                    // Add to running total for verification
                    itemsTotal += totalPrice;

                    itemsHtml += `
                <div class="order-item">
                    <span class="item-name">${itemName} x${quantity}</span>
                    <span class="item-price">PHP ${totalPrice.toFixed(2)}</span>
                </div>
            `;
                });
            }

            // Fixed: Use calculateOrderTotal function for accurate total
            const totalAmount = calculateOrderTotal(order);
            const cashAmount = parseFloat(order.cash_amount || 0);

            // Customer payment information section
            let customerPaymentHtml = '';
            if (order.payment_method === 'cash' && cashAmount > 0) {
                const expectedChange = cashAmount - totalAmount;

                customerPaymentHtml = `
        <div class="customer-payment-info">
            <div style="font-weight: 700; margin-bottom: 8px; color: #1565c0; display: flex; align-items: center; gap: 8px;">
                💰 Customer's Payment Plan
            </div>
            <div class="payment-info-row">
                <span class="payment-info-label">🏷️ Order Total:</span>
                <span>PHP ${totalAmount.toFixed(2)}</span>
            </div>
            <div class="payment-info-row">
                <span class="payment-info-label">💵 Will Bring:</span>
                <span>PHP ${cashAmount.toFixed(2)}</span>
            </div>
            ${expectedChange > 0 ? `
                <div class="payment-info-row expected-change">
                    <span class="payment-info-label">💸 Expected Change:</span>
                    <span>PHP ${expectedChange.toFixed(2)}</span>
                </div>
            ` : ''}
            <div class="edit-amount-note">
                💡 You can edit the cash amount during payment processing
            </div>
        </div>
    `;
            }

            const orderNumber = order.order_number || order.id.toString().padStart(4, '0');
            const createdAt = order.created_at ? new Date(order.created_at) : new Date();
            const timeString = createdAt.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });

            const orderType = order.order_type || 'dine-in';

            orderCard.innerHTML = `
    <div class="order-header">
        <span>Order</span>
        <span class="order-number">#${orderNumber}</span>
    </div>
    <div class="order-time">
        Placed at ${timeString}
        <span class="order-type">${orderType.charAt(0).toUpperCase() + orderType.slice(1)}</span>
    </div>
    <div class="status-badge status-pending">Pending Payment</div>
    
    <div class="order-items">
        ${itemsHtml}
    </div>

    ${customerPaymentHtml}
    
    <div class="order-total">
        <span>Total Amount:</span>
        <span class="total-amount">PHP ${totalAmount.toFixed(2)}</span>
    </div>
    
    <div class="order-actions">
        <button class="btn btn-accept" onclick="acceptOrder(${order.id}, ${totalAmount}, '${orderNumber}', ${cashAmount})">
            ✅ Accept
        </button>
        <button class="btn btn-edit" onclick="editOrder(${order.id})">
            ✏️ Edit
        </button>
        <button class="btn btn-cancel" onclick="cancelOrder(${order.id})">
            ❌ Cancel
        </button>
    </div>
`;

            return orderCard;
        }

        function acceptOrder(orderId, amount, orderNumber, cashAmount = null) {
            debugLog('Accept order called', { orderId, amount, orderNumber, cashAmount });

            currentOrderId = parseInt(orderId);
            currentAmount = parseFloat(amount) || 0;
            currentOrderNumber = orderNumber || orderId.toString().padStart(4, '0');
            orderTotal = currentAmount;

            if (cashAmount && parseFloat(cashAmount) > 0) {
                cashReceived = parseFloat(cashAmount);
            } else {
                cashReceived = 0;
            }

            showPaymentModal();
        }

        function showPaymentModal() {
            const modal = document.getElementById('paymentModal');
            const orderTotalDisplay = document.getElementById('orderTotalDisplay');
            const cashInput = document.getElementById('cashAmount');

            if (!modal || !orderTotalDisplay || !cashInput) {
                console.error('Payment modal elements not found');
                return;
            }

            orderTotalDisplay.textContent = `PHP ${orderTotal.toFixed(2)}`;

            if (cashReceived > 0) {
                cashInput.value = cashReceived.toFixed(2);
            } else {
                cashInput.value = '';
                cashReceived = 0;
            }

            changeAmount = 0;
            generateQuickCashButtons();
            updatePaymentDisplays();

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';

            setTimeout(() => {
                if (cashInput) cashInput.focus();
            }, 300);
        }

        function generateQuickCashButtons() {
            const container = document.getElementById('quickCashButtons');
            if (!container) return;

            const total = orderTotal;
            const quickAmounts = [
                Math.ceil(total),
                Math.ceil(total / 50) * 50,
                Math.ceil(total / 100) * 100,
                Math.ceil(total / 500) * 500,
                1000,
                500
            ];

            const uniqueAmounts = [...new Set(quickAmounts)]
                .filter(amount => amount >= total)
                .sort((a, b) => a - b)
                .slice(0, 6);

            container.innerHTML = '';
            uniqueAmounts.forEach(amount => {
                const button = document.createElement('button');
                button.className = 'quick-cash-btn';
                button.textContent = `PHP ${amount}`;
                button.onclick = () => setQuickCash(amount);
                container.appendChild(button);
            });
        }

        function setQuickCash(amount) {
            const cashInput = document.getElementById('cashAmount');
            if (cashInput) {
                cashInput.value = amount.toFixed(2);
                calculateChange();
            }
        }

        function calculateChange() {
            const cashInput = document.getElementById('cashAmount');
            if (!cashInput) return;

            cashReceived = parseFloat(cashInput.value) || 0;
            changeAmount = cashReceived - orderTotal;

            updatePaymentDisplays();

            const confirmBtn = document.getElementById('confirmPaymentBtn');
            if (confirmBtn) {
                if (cashReceived >= orderTotal) {
                    confirmBtn.disabled = false;
                    cashInput.classList.remove('error');
                } else {
                    confirmBtn.disabled = true;
                    if (cashReceived > 0) {
                        cashInput.classList.add('error');
                    } else {
                        cashInput.classList.remove('error');
                    }
                }
            }
        }

        function updatePaymentDisplays() {
            const cashReceivedDisplay = document.getElementById('cashReceivedDisplay');
            const changeAmountDisplay = document.getElementById('changeAmount');
            const changeDisplay = document.getElementById('changeDisplay');

            if (cashReceivedDisplay) {
                cashReceivedDisplay.textContent = `PHP ${cashReceived.toFixed(2)}`;
            }

            if (changeAmountDisplay) {
                changeAmountDisplay.textContent = `PHP ${Math.max(0, changeAmount).toFixed(2)}`;
            }

            console.log('Change calculation debug:', {
                cashReceived,
                orderTotal,
                changeAmount: cashReceived - orderTotal
            });

            if (changeDisplay) {
                if (changeAmount < 0) {
                    changeDisplay.classList.add('negative');
                    const firstSpan = changeDisplay.querySelector('span:first-child');
                    if (firstSpan) firstSpan.textContent = '⚠️ Insufficient:';
                } else {
                    changeDisplay.classList.remove('negative');
                    const firstSpan = changeDisplay.querySelector('span:first-child');
                    if (firstSpan) firstSpan.textContent = '💰 Change:';
                }
            }
        }

        function hidePaymentModal() {
            const modal = document.getElementById('paymentModal');
            if (modal) {
                modal.classList.remove('show');
            }
            document.body.style.overflow = '';

            currentOrderId = null;
            currentAmount = 0;
            currentOrderNumber = '';
            orderTotal = 0;
            cashReceived = 0;
            changeAmount = 0;
        }

        async function confirmPayment() {
            debugLog('Confirm payment called', {
                orderId: currentOrderId,
                cashReceived,
                orderTotal,
                changeAmount
            });

            if (cashReceived < orderTotal) {
                showErrorMessage('Insufficient cash amount');
                return;
            }

            if (!currentOrderId) {
                showErrorMessage('No order selected');
                return;
            }

            const requestData = {
                order_id: parseInt(currentOrderId),
                cash_amount: parseFloat(cashReceived),
                print_receipt: true
            };

            debugLog('Request data being sent:', requestData);

            // Calculate the actual change before hiding modal
            const actualChange = cashReceived - orderTotal;

            // Store current order data before hiding modal
            const processingOrderId = currentOrderId;
            const processingOrderNumber = currentOrderNumber;

            // Remove card instantly when payment is confirmed
            const orderCard = document.getElementById(`order-${currentOrderId}`);
            if (orderCard) {
                orderCard.remove();
                checkEmptyState();
            }

            hidePaymentModal();

            try {
                const data = await fetchWithErrorHandling('/cashier/accept-order', {
                    method: 'POST',
                    body: JSON.stringify(requestData)
                });

                if (data.success) {
                    moveOrderToProcessing(processingOrderId, processingOrderNumber, actualChange, data.receipt_printed);
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            } catch (error) {
                console.error('Payment processing error:', error);
                refreshOrders(); // Re-add card on error
                showErrorMessage('Payment processing failed: ' + error.message);
            }
        }

        function moveOrderToProcessing(orderId, orderNumber, change, receiptPrinted) {
            // Card removal moved to confirmPayment() for instant disappearance

            processingOrders.set(orderId, {
                orderNumber,
                change: change, // Use the calculated change passed from confirmPayment
                receiptPrinted,
                startTime: new Date(),
                status: 'preparing'
            });

            showProcessingOrder(orderId, orderNumber, change, receiptPrinted);
        }

        function showProcessingOrder(orderId, orderNumber, actualChangeGiven, receiptPrinted) {
            const processingSection = document.getElementById('processingSection');
            if (!processingSection) {
                console.error('Processing section not found');
                return;
            }

            // Ensure we have a valid order number
            const displayOrderNumber = orderNumber || orderId.toString().padStart(4, '0');

            debugLog('Showing processing order', {
                orderId,
                orderNumber: displayOrderNumber,
                actualChangeGiven,
                receiptPrinted
            });

            processingSection.className = 'processing-section has-order';

            const receiptStatus = receiptPrinted ?
                '✅ Receipt printed successfully!' :
                '⚠️ Order processed (receipt printing failed - please check printer)';

            const startTime = new Date();
            const estimatedTime = new Date(startTime.getTime() + (15 * 60000)); // 15 minutes estimated

            processingSection.innerHTML = `
        <div class="processing-order-header">
            <div class="processing-order-number">🏷️ Order #${displayOrderNumber}</div>
            <div class="processing-order-time">💳 Payment completed at ${startTime.toLocaleTimeString()}</div>
        </div>
        
        <div class="payment-details-box" style="background: #d4edda; border: 2px solid #c3e6cb; border-radius: 10px; padding: 20px; margin: 20px 0;">
            <div style="font-size: 1.3rem; font-weight: 700; color: #155724; margin-bottom: 15px; text-align: center;">
                💰 Payment Completed Successfully!
            </div>
            <div class="processing-total-row" style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 1.1rem;">
                <span style="color: #155724;">💸 Change Given:</span>
                <span style="font-weight: 700; color: #155724; font-size: 1.2rem;">PHP ${actualChangeGiven.toFixed(2)}</span>
            </div>
            <div style="margin-top: 15px; padding: 12px; background: rgba(21, 87, 36, 0.1); border-radius: 8px; text-align: center; color: #155724; font-weight: 600;">
                ${receiptStatus}
            </div>
        </div>

        <div style="background: #fff3cd; border: 2px solid #ffeaa7; border-radius: 10px; padding: 20px; margin: 20px 0;">
            <div style="font-weight: 600; margin-bottom: 15px; color: #856404; text-align: center; font-size: 1.1rem;">⏱️ Order Status</div>
            <div style="color: #856404; line-height: 1.8; text-align: center;">
                <div style="font-size: 1.2rem; font-weight: 600; margin-bottom: 10px;">🍳 Currently Preparing</div>
                <div style="font-size: 1rem;">📅 Estimated completion: ${estimatedTime.toLocaleTimeString()}</div>
                <div style="margin-top: 10px; font-size: 0.9rem; opacity: 0.8;">⏰ Average wait time: 15 minutes</div>
            </div>
        </div>
        
        <div class="processing-actions" style="display: flex; gap: 15px; margin-top: 25px;">
            <button class="btn-large" style="
                background: #28a745; 
                color: white; 
                border: none; 
                border-radius: 8px; 
                cursor: pointer; 
                padding: 15px 25px; 
                font-size: 1.1rem; 
                flex: 1;
                font-weight: 600;
                transition: background-color 0.3s ease;
            " onclick="completeOrder(${orderId})" onmouseover="this.style.backgroundColor='#218838'" onmouseout="this.style.backgroundColor='#28a745'">
                ✅ Mark as Complete
            </button>
            <button class="btn-large" style="
                background: #6c757d; 
                color: white; 
                border: none; 
                border-radius: 8px; 
                cursor: pointer; 
                padding: 15px 25px; 
                font-size: 1.1rem; 
                flex: 1;
                font-weight: 600;
                transition: background-color 0.3s ease;
            " onclick="resetProcessingPanel()" onmouseover="this.style.backgroundColor='#5a6268'" onmouseout="this.style.backgroundColor='#6c757d'">
                📋 Process Next
            </button>
        </div>
    `;
        }

        async function completeOrder(orderId) {
            const processingOrder = processingOrders.get(orderId);
            if (!processingOrder) {
                showErrorMessage('Order not found in processing');
                return;
            }

            try {
                const salesData = {
                    order_id: parseInt(orderId),
                    order_number: processingOrder.orderNumber,
                    completion_time: new Date().toISOString(),
                    change_given: processingOrder.change,
                    receipt_printed: processingOrder.receiptPrinted
                };

                debugLog('Completing order', salesData);

                const data = await fetchWithErrorHandling('/cashier/complete-order', {
                    method: 'POST',
                    body: JSON.stringify(salesData)
                });

                if (data.success) {
                    processingOrders.delete(orderId);
                    showOrderCompletionMessage(processingOrder.orderNumber);

                    setTimeout(() => {
                        resetProcessingPanel();
                    }, 3000);
                } else {
                    throw new Error(data.message || 'Failed to complete order');
                }
            } catch (error) {
                console.error('Error completing order:', error);
                showErrorMessage('Failed to complete order: ' + error.message);
            }
        }

        function showOrderCompletionMessage(orderNumber) {
            const processingSection = document.getElementById('processingSection');
            if (!processingSection) return;

            const displayOrderNumber = orderNumber || 'Unknown';

            processingSection.innerHTML = `
        <div style="text-align: center; padding: 40px;">
            <div style="font-size: 4rem; margin-bottom: 20px;">✅</div>
            <h2 style="color: #155724; margin-bottom: 15px; font-size: 1.8rem;">Order #${displayOrderNumber} Complete!</h2>
            <p style="color: #155724; font-size: 1.1rem; margin-bottom: 10px;">Order has been completed and saved to sales database.</p>
            <p style="color: #666; font-size: 0.9rem;">Redirecting to next order in 3 seconds...</p>
        </div>
    `;
        }

        function resetProcessingPanel() {
            const processingSection = document.getElementById('processingSection');
            if (!processingSection) return;

            processingSection.className = 'processing-section';
            processingSection.innerHTML = `
        <div class="processing-icon">💳</div>
        <p><strong>SELECT AN ORDER</strong></p>
        <p>FROM THE LEFT PANEL TO</p>
        <p>BEGIN PROCESSING PAYMENT</p>
        <br>
        <p>Use the <strong>Accept</strong> button to process payment</p>
        <p>Use the <strong>Edit</strong> button to modify orders</p>
        <p>Use the <strong>Cancel</strong> button to cancel orders</p>
    `;
        }

        function editOrder(orderId) {
            currentAction = 'edit';
            currentOrderId = orderId;

            showModal(
                'warning',
                '✏️',
                'Edit Order',
                'Modify order items or details',
                `Edit Order #${orderId}?<br><br>This will redirect you to the order editing interface.`,
                'Edit Order'
            );
        }

        function cancelOrder(orderId) {
            currentAction = 'cancel';
            currentOrderId = orderId;

            showModal(
                'cancel',
                '❌',
                'Cancel Order',
                'This action cannot be undone',
                `Cancel Order #${orderId}?<br><br><strong>Warning:</strong> This will permanently cancel the order and cannot be undone.`,
                'Cancel Order'
            );
        }

        function showModal(type, icon, title, subtitle, message, confirmText) {
            const modal = document.getElementById('confirmModal');
            const header = document.getElementById('modalHeader');
            const modalIcon = document.getElementById('modalIcon');
            const modalTitle = document.getElementById('modalTitle');
            const modalSubtitle = document.getElementById('modalSubtitle');
            const modalMessage = document.getElementById('modalMessage');
            const confirmBtn = document.getElementById('confirmBtn');

            if (!modal) {
                console.error('Confirm modal not found');
                return;
            }

            if (header) header.className = 'modal-header';
            if (confirmBtn) confirmBtn.className = 'modal-btn modal-btn-confirm';

            if (modalIcon) modalIcon.textContent = icon;
            if (modalTitle) modalTitle.textContent = title;
            if (modalSubtitle) modalSubtitle.textContent = subtitle;
            if (modalMessage) modalMessage.innerHTML = message;
            if (confirmBtn) confirmBtn.textContent = confirmText;

            if (type === 'cancel') {
                if (header) header.classList.add('cancel');
                if (confirmBtn) confirmBtn.classList.add('cancel-style');
            }

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function hideModal() {
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.classList.remove('show');
            }
            document.body.style.overflow = '';

            currentAction = null;
            currentOrderId = null;
        }

        function confirmAction() {
            if (!currentAction || !currentOrderId) return;

            switch (currentAction) {
                case 'edit':
                    processEditOrder();
                    break;
                case 'cancel':
                    processCancelOrder();
                    break;
            }

            hideModal();
        }

        function processEditOrder() {
            window.location.href = `/cashier/edit-order/${currentOrderId}`;
        }

        async function processCancelOrder() {
            const orderCard = document.getElementById(`order-${currentOrderId}`);
            if (!orderCard) {
                showErrorMessage('Order card not found');
                return;
            }

            try {
                const data = await fetchWithErrorHandling('/cashier/cancel-order', {
                    method: 'POST',
                    body: JSON.stringify({
                        order_id: parseInt(currentOrderId),
                        reason: 'Cancelled by cashier'
                    })
                });

                if (data.success) {
                    orderCard.style.transition = 'all 0.5s ease';
                    orderCard.style.transform = 'translateX(-100%)';
                    orderCard.style.opacity = '0';

                    setTimeout(() => {
                        orderCard.remove();
                        checkEmptyState();
                    }, 500);
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                showErrorMessage('Failed to cancel order: ' + error.message);
            }
        }

        function checkEmptyState() {
            const orderCards = document.querySelectorAll('.order-card');
            const emptyState = document.getElementById('emptyState');
            const ordersContainer = document.getElementById('ordersContainer');

            if (orderCards.length === 0 && ordersContainer) {
                if (!ordersContainer.contains(emptyState) && emptyState) {
                    ordersContainer.appendChild(emptyState);
                } else if (!emptyState) {
                    ordersContainer.innerHTML = `
                <div class="empty-state" id="emptyState">
                    <div class="empty-state-icon">📭</div>
                    <p>No pending cash orders</p>
                    <small>Orders will appear here when customers place cash orders</small>
                </div>
            `;
                }
            }
        }

        function showErrorMessage(message) {
            let errorAlert = document.getElementById('errorAlert');
            if (!errorAlert) {
                errorAlert = document.createElement('div');
                errorAlert.id = 'errorAlert';
                errorAlert.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px 20px;
            box-shadow: 0 4px 15px rgba(114, 28, 36, 0.2);
            z-index: 2000;
            max-width: 400px;
            animation: slideIn 0.3s ease-out;
        `;
                document.body.appendChild(errorAlert);
            }

            errorAlert.innerHTML = `
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.2em;">⚠️</span>
            <div>
                <strong>Error</strong><br>
                ${message}
            </div>
            <button onclick="this.parentElement.parentElement.remove()" style="
                background: none; 
                border: none; 
                color: #721c24; 
                font-size: 1.2em; 
                cursor: pointer;
                margin-left: auto;
            ">×</button>
        </div>
    `;

            setTimeout(() => {
                if (errorAlert && errorAlert.parentElement) {
                    errorAlert.remove();
                }
            }, 10000);
        }

        // Event listeners
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('modal-overlay')) {
                if (e.target.id === 'paymentModal') {
                    hidePaymentModal();
                } else {
                    hideModal();
                }
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const paymentModal = document.getElementById('paymentModal');
                const confirmModal = document.getElementById('confirmModal');

                if (paymentModal && paymentModal.classList.contains('show')) {
                    hidePaymentModal();
                } else if (confirmModal && confirmModal.classList.contains('show')) {
                    hideModal();
                }
            }

            if (e.key === 'Enter') {
                const paymentModal = document.getElementById('paymentModal');
                if (paymentModal && paymentModal.classList.contains('show')) {
                    const confirmBtn = document.getElementById('confirmPaymentBtn');
                    if (confirmBtn && !confirmBtn.disabled) {
                        confirmPayment();
                    }
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            debugLog('Cashier page loaded, initializing...');
            checkEmptyState();
        });

        // Global function exports
        window.refreshOrders = refreshOrders;
        window.acceptOrder = acceptOrder;
        window.editOrder = editOrder;
        window.cancelOrder = cancelOrder;
        window.hidePaymentModal = hidePaymentModal;
        window.hideModal = hideModal;
        window.confirmPayment = confirmPayment;
        window.confirmAction = confirmAction;
        window.calculateChange = calculateChange;
        window.setQuickCash = setQuickCash;
        window.resetProcessingPanel = resetProcessingPanel;
        window.completeOrder = completeOrder;
    </script>
</body>

</html>