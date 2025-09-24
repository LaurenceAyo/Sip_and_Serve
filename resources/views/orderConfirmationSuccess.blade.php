<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            background: #e74c3c;
            color: white;
        }

        .btn-secondary:hover {
            background: #c0392b;
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

        /* Email Modal Styles */
        .email-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .email-modal-overlay.show {
            display: flex;
        }

        .email-modal {
            background: white;
            width: 90%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .email-modal-header {
            background: #e74c3c;
            color: white;
            padding: 25px;
            text-align: center;
        }

        .email-modal-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .email-modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .email-modal-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }

        .email-modal-content {
            padding: 30px 25px;
        }

        .email-input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .email-input-label {
            display: block;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .email-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .email-input:focus {
            outline: none;
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
        }

        .email-input.error {
            border-color: #e74c3c;
            background-color: #fdf2f2;
        }

        .email-error-message {
            color: #e74c3c;
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
        }

        .email-info-box {
            background: #e8f4fd;
            border: 1px solid #bde4ff;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
        }

        .email-info-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .email-info-text {
            color: #34495e;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .email-modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
        }

        .email-modal-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 120px;
        }

        .email-modal-btn-cancel {
            background: #95a5a6;
            color: white;
        }

        .email-modal-btn-cancel:hover {
            background: #7f8c8d;
        }

        .email-modal-btn-send {
            background: #e74c3c;
            color: white;
        }

        .email-modal-btn-send:hover {
            background: #c0392b;
        }

        .email-modal-btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }

        /* Loading state */
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Success modal */
        .success-modal {
            background: white;
            width: 90%;
            max-width: 450px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideIn 0.3s ease-out;
            text-align: center;
        }

        .success-modal-header {
            background: #27ae60;
            color: white;
            padding: 30px 25px;
        }

        .success-modal-icon {
            font-size: 4rem;
            margin-bottom: 15px;
        }

        .success-modal-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .success-modal-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }

        .success-modal-content {
            padding: 25px;
        }

        .success-modal-message {
            font-size: 1.1rem;
            color: #2c3e50;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .success-modal-actions {
            margin-top: 25px;
        }

        /* Auto-redirect Modal Styles */
        .auto-redirect-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(44, 24, 16, 0.85);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 5000;
            backdrop-filter: blur(8px);
            animation: fadeInBackdrop 0.4s ease-out;
        }

        .auto-redirect-modal.show {
            display: flex;
        }

        @keyframes fadeInBackdrop {
            from {
                opacity: 0;
                backdrop-filter: blur(0px);
            }
            to {
                opacity: 1;
                backdrop-filter: blur(8px);
            }
        }

        .redirect-modal-container {
            background: white;
            width: 550px;
            max-width: 90vw;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(139, 69, 19, 0.4);
            overflow: hidden;
            animation: slideInModal 0.5s ease-out;
            border: 3px solid #d4c4a8;
        }

        @keyframes slideInModal {
            from {
                opacity: 0;
                transform: translateY(-40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .redirect-modal-header {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 30px 25px;
            text-align: center;
            position: relative;
        }

        .redirect-modal-icon {
            font-size: 3.5rem;
            margin-bottom: 15px;
            opacity: 0.95;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        .redirect-modal-title {
            font-family: 'Arial', sans-serif;
            font-size: 1.9rem;
            font-weight: 700;
            margin: 0 0 10px 0;
            letter-spacing: 1px;
        }

        .redirect-modal-subtitle {
            font-size: 1rem;
            opacity: 0.95;
            font-weight: 400;
            line-height: 1.4;
        }

        .redirect-modal-content {
            padding: 35px 30px;
            text-align: center;
            background: #f5f1e8;
        }

        .redirect-message {
            font-size: 1.2rem;
            color: #2c1810;
            font-weight: 500;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .redirect-message strong {
            color: #27ae60;
            font-weight: 700;
        }

        .countdown-section {
            background: white;
            border: 3px solid #27ae60;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .countdown-label {
            font-size: 1rem;
            color: #666;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .countdown-display {
            font-family: 'Arial', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            color: #27ae60;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .countdown-text {
            font-size: 0.9rem;
            color: #7f8c8d;
            font-style: italic;
        }

        .progress-bar-container {
            background: #e9ecef;
            border-radius: 10px;
            height: 8px;
            margin: 20px 0;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            background: linear-gradient(90deg, #27ae60, #2ecc71);
            height: 100%;
            width: 100%;
            border-radius: 10px;
            transition: width 0.1s ease-out;
        }

        .redirect-options-info {
            background: #e8f4fd;
            border: 2px solid #bde4ff;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            text-align: left;
        }

        .redirect-options-title {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.1rem;
        }

        .redirect-options-list {
            color: #34495e;
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 0;
            padding-left: 20px;
        }

        .redirect-options-list li {
            margin-bottom: 8px;
        }

        .redirect-modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .redirect-modal-btn {
            flex: 1;
            max-width: 200px;
            padding: 18px 25px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .redirect-modal-btn-stay {
            background: #6c757d;
            color: white;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .redirect-modal-btn-stay:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }

        .redirect-modal-btn-continue {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }

        .redirect-modal-btn-continue:hover {
            background: linear-gradient(135deg, #229954, #27ae60);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }

        .redirect-modal-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .redirect-modal-btn:hover::before {
            left: 100%;
        }

        /* Pulse animation for urgent countdown */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                max-width: calc(100% - 20px);
            }

            .email-modal {
                width: 95%;
                margin: 20px;
            }

            .email-modal-content {
                padding: 20px 15px;
            }

            .email-modal-actions {
                flex-direction: column;
                gap: 10px;
            }

            .email-modal-btn {
                width: 100%;
            }

            .redirect-modal-container {
                width: 95vw;
                margin: 20px;
            }
            
            .redirect-modal-content {
                padding: 25px 20px;
            }
            
            .redirect-modal-actions {
                flex-direction: column;
                gap: 12px;
            }
            
            .redirect-modal-btn {
                max-width: none;
            }
            
            .redirect-modal-title {
                font-size: 1.6rem;
            }
            
            .countdown-display {
                font-size: 2rem;
            }
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
                    <span>#{{ $order->id ?? '001' }}</span>
                </div>
                <div class="info-row">
                    <span><strong>Order Date:</strong></span>
                    <span>{{ isset($order) ? $order->created_at->format('M d, Y h:i A') : date('M d, Y h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span><strong>Status:</strong></span>
                    <span>{{ isset($order) ? ucfirst($order->status) : 'Confirmed' }}</span>
                </div>
                @if(isset($order) && $order->notes)
                    <div class="info-row">
                        <span><strong>Notes:</strong></span>
                        <span>{{ $order->notes }}</span>
                    </div>
                @endif
            </div>

            <div class="order-details">
                <h3>Order Items</h3>
                @if(isset($order) && $order->orderItems)
                    @foreach($order->orderItems as $orderItem)
                        <div class="order-item">
                            <div>
                                <strong>{{ $orderItem->menuItem->name ?? $orderItem->name ?? 'Custom Item' }}</strong>
                                <br>
                                <small>PHP {{ number_format($orderItem->unit_price ?? $orderItem->price ?? 0, 2) }} x {{ $orderItem->quantity }}</small>
                                @if(isset($orderItem->special_instructions) && $orderItem->special_instructions)
                                    <br>
                                    <small><em>Note: {{ $orderItem->special_instructions }}</em></small>
                                @endif
                            </div>
                            <div>
                                <strong>PHP {{ number_format($orderItem->total_price ?? ($orderItem->price * $orderItem->quantity) ?? 0, 2) }}</strong>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="total-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>PHP {{ isset($order) ? number_format((float) $order->subtotal, 2) : '300.00' }}</span>
                </div>
                <div class="total-row">
                    <span>Tax:</span>
                    <span>PHP {{ isset($order) ? number_format((float) $order->tax_amount, 2) : '0.00' }}</span>
                </div>
                @if(isset($order) && $order->discount_amount > 0)
                    <div class="total-row">
                        <span>Discount:</span>
                        <span>-PHP {{ number_format((float) $order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="total-row final">
                    <span>Total:</span>
                    <span>PHP {{ isset($order) ? number_format((float) $order->total_amount, 2) : '300.00' }}</span>
                </div>
            </div>

            <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4>What's Next?</h4>
                <p>Your order is now in the kitchen queue. Please wait for your order to be served, Thank you.</p>
                <p><strong>Estimated preparation time: 15-30 minutes</strong></p>
            </div>

            <div class="action-buttons">
                <a href="{{ route('kiosk.index') }}" class="btn btn-primary">Place Another Order</a>
                <button onclick="showEmailModal()" class="btn btn-secondary">Get Receipt</button>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div class="email-modal-overlay" id="emailModal">
        <div class="email-modal">
            <div class="email-modal-header">
                <div class="email-modal-icon">📧</div>
                <h2 class="email-modal-title">Email Receipt</h2>
                <p class="email-modal-subtitle">Get your digital receipt sent to your email</p>
            </div>
            
            <div class="email-modal-content">
                <div class="email-input-group">
                    <label for="customerEmail" class="email-input-label">Email Address</label>
                    <input 
                        type="email" 
                        id="customerEmail" 
                        class="email-input" 
                        placeholder="Enter your email address"
                        autocomplete="email"
                    >
                    <div class="email-error-message" id="emailError">Please enter a valid email address</div>
                </div>

                <div class="email-info-box">
                    <div class="email-info-title">
                        <span>📋</span>
                        What you'll receive:
                    </div>
                    <div class="email-info-text">
                        • Complete order details and itemized receipt<br>
                        • Order number and payment information<br>
                        • Digital copy for your records<br>
                        • Delivery typically within 2-3 minutes
                    </div>
                </div>

                <div class="email-modal-actions">
                    <button class="email-modal-btn email-modal-btn-cancel" onclick="hideEmailModal()">
                        Cancel
                    </button>
                    <button class="email-modal-btn email-modal-btn-send" onclick="sendReceipt()" id="sendBtn">
                        <span class="loading-spinner" id="loadingSpinner"></span>
                        <span id="sendBtnText">Send Receipt</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="email-modal-overlay" id="successModal">
        <div class="success-modal">
            <div class="success-modal-header">
                <div class="success-modal-icon">✅</div>
                <h2 class="success-modal-title">Receipt Sent!</h2>
                <p class="success-modal-subtitle">Check your email inbox</p>
            </div>
            
            <div class="success-modal-content">
                <div class="success-modal-message">
                    Your digital receipt has been successfully sent to <strong id="sentEmailAddress"></strong>
                    <br><br>
                    Please check your inbox (and spam folder) for the receipt email.
                </div>

                <div class="success-modal-actions">
                    <button class="email-modal-btn email-modal-btn-send" onclick="hideSuccessModal()">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-redirect Modal -->
    <div class="auto-redirect-modal" id="autoRedirectModal">
        <div class="redirect-modal-container">
            <div class="redirect-modal-header">
                <div class="redirect-modal-icon">⏰</div>
                <h2 class="redirect-modal-title">Return to Main Screen?</h2>
                <p class="redirect-modal-subtitle">Thank you for using Sip & Serve!</p>
            </div>
            
            <div class="redirect-modal-content">
                <div class="redirect-message">
                    Your order has been successfully placed! <strong>Would you like to return to the main screen</strong> to place another order?
                </div>

                <div class="countdown-section">
                    <div class="countdown-label">Auto-redirect in:</div>
                    <div class="countdown-display" id="countdownDisplay">30</div>
                    <div class="countdown-text">seconds</div>
                    
                    <div class="progress-bar-container">
                        <div class="progress-bar" id="progressBar"></div>
                    </div>
                </div>

                <div class="redirect-options-info">
                    <div class="redirect-options-title">
                        <span>🎯</span>
                        What happens next:
                    </div>
                    <ul class="redirect-options-list">
                        <li><strong>Continue:</strong> Return to the main kiosk screen to place another order</li>
                        <li><strong>Stay Here:</strong> Keep this confirmation page open for your records</li>
                        <li><strong>Auto-redirect:</strong> Automatically returns to main screen after countdown</li>
                    </ul>
                </div>

                <div class="redirect-modal-actions">
                    <button class="redirect-modal-btn redirect-modal-btn-stay" onclick="stayOnPage()">
                        Stay Here
                    </button>
                    <button class="redirect-modal-btn redirect-modal-btn-continue" onclick="returnToMain()">
                        Return to Main
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let countdownTimer;
        let countdownSeconds = 30;

        function showEmailModal() {
            document.getElementById('emailModal').classList.add('show');
            document.body.style.overflow = 'hidden';
            
            setTimeout(() => {
                document.getElementById('customerEmail').focus();
            }, 300);
        }

        function hideEmailModal() {
            document.getElementById('emailModal').classList.remove('show');
            document.body.style.overflow = '';
            
            // Reset form
            document.getElementById('customerEmail').value = '';
            document.getElementById('customerEmail').classList.remove('error');
            document.getElementById('emailError').style.display = 'none';
            resetSendButton();
        }

        function hideSuccessModal() {
            document.getElementById('successModal').classList.remove('show');
            document.body.style.overflow = '';
        }

        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function resetSendButton() {
            const sendBtn = document.getElementById('sendBtn');
            const sendBtnText = document.getElementById('sendBtnText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            sendBtn.disabled = false;
            sendBtnText.textContent = 'Send Receipt';
            loadingSpinner.style.display = 'none';
        }

        function sendReceipt() {
            const emailInput = document.getElementById('customerEmail');
            const emailError = document.getElementById('emailError');
            const sendBtn = document.getElementById('sendBtn');
            const sendBtnText = document.getElementById('sendBtnText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            const email = emailInput.value.trim();
            
            // Reset previous error states
            emailInput.classList.remove('error');
            emailError.style.display = 'none';
            
            // Validate email
            if (!email) {
                emailInput.classList.add('error');
                emailError.textContent = 'Email address is required';
                emailError.style.display = 'block';
                emailInput.focus();
                return;
            }
            
            if (!validateEmail(email)) {
                emailInput.classList.add('error');
                emailError.textContent = 'Please enter a valid email address';
                emailError.style.display = 'block';
                emailInput.focus();
                return;
            }
            
            // Show loading state
            sendBtn.disabled = true;
            sendBtnText.textContent = 'Sending...';
            loadingSpinner.style.display = 'inline-block';
            
            // Send receipt via AJAX
            fetch('{{ route("send.receipt") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    email: email,
                    order_id: {{ $order->id ?? 1 }}
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide email modal and show success modal
                    document.getElementById('emailModal').classList.remove('show');
                    document.getElementById('sentEmailAddress').textContent = email;
                    document.getElementById('successModal').classList.add('show');
                    
                    // Reset form
                    emailInput.value = '';
                } else {
                    throw new Error(data.message || 'Failed to send receipt');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                emailInput.classList.add('error');
                emailError.textContent = 'Failed to send receipt. Please try again.';
                emailError.style.display = 'block';
            })
            .finally(() => {
                resetSendButton();
            });
        }

        // Auto-redirect Modal Functions
        function showRedirectModal() {
            const modal = document.getElementById('autoRedirectModal');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            
            startCountdown();
        }

        function hideRedirectModal() {
            const modal = document.getElementById('autoRedirectModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
            
            clearCountdown();
        }

        function startCountdown() {
            countdownSeconds = 30;
            updateCountdownDisplay();
            updateProgressBar();
            
            countdownTimer = setInterval(() => {
                countdownSeconds--;
                updateCountdownDisplay();
                updateProgressBar();
                
                if (countdownSeconds <= 0) {
                    returnToMain();
                }
            }, 1000);
        }

        function clearCountdown() {
            if (countdownTimer) {
                clearInterval(countdownTimer);
                countdownTimer = null;
            }
        }

        function updateCountdownDisplay() {
            const display = document.getElementById('countdownDisplay');
            display.textContent = countdownSeconds;
            
            if (countdownSeconds <= 10) {
                display.style.color = '#e74c3c';
                display.style.animation = 'pulse 1s infinite';
            } else {
                display.style.color = '#27ae60';
                display.style.animation = 'none';
            }
        }

        function updateProgressBar() {
            const progressBar = document.getElementById('progressBar');
            const percentage = (countdownSeconds / 30) * 100;
            progressBar.style.width = percentage + '%';
            
            if (percentage <= 33) {
                progressBar.style.background = 'linear-gradient(90deg, #e74c3c, #c0392b)';
            } else if (percentage <= 66) {
                progressBar.style.background = 'linear-gradient(90deg, #f39c12, #e67e22)';
            } else {
                progressBar.style.background = 'linear-gradient(90deg, #27ae60, #2ecc71)';
            }
        }

        function stayOnPage() {
            hideRedirectModal();
        }

        function returnToMain() {
            clearCountdown();
            window.location.href = '{{ route("kiosk.index") }}';
        }

        // Handle Enter key in email input
        document.getElementById('customerEmail').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendReceipt();
            }
        });

        // Close modal when clicking outside
        document.getElementById('emailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideEmailModal();
            }
        });

        document.getElementById('successModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideSuccessModal();
            }
        });

        // Auto-redirect with custom modal after 2 minutes
        setTimeout(function() {
            showRedirectModal();
        }, 120000);
    </script>
</body>
</html>