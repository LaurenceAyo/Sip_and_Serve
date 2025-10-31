<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Maya Payment - L PRIMERO</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .payment-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .payment-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 32px 24px;
            text-align: center;
        }

        .payment-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .payment-header .subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        .qr-section {
            padding: 40px 24px;
            text-align: center;
            background: #f8f9fa;
        }

        .qr-code {
            background: white;
            padding: 24px;
            border-radius: 16px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }

        .qr-code img {
            display: block;
            max-width: 300px;
            height: auto;
            border-radius: 8px;
        }

        .business-name {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .amount-display {
            font-size: 36px;
            font-weight: 800;
            color: #667eea;
            margin-bottom: 16px;
        }

        .instruction {
            font-size: 16px;
            color: #718096;
            margin-bottom: 24px;
        }

        .order-details {
            background: white;
            padding: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .order-details h5 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #2d3748;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }

        .detail-label {
            color: #718096;
        }

        .detail-value {
            font-weight: 600;
            color: #2d3748;
        }

        .countdown {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 16px;
            margin: 24px;
            border-radius: 12px;
            text-align: center;
            font-size: 14px;
            color: #856404;
        }

        .countdown-time {
            font-size: 20px;
            font-weight: 700;
            margin-top: 8px;
        }

        .status-checking {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            padding: 16px;
            margin: 24px;
            border-radius: 12px;
            text-align: center;
            display: none;
        }

        .status-checking.active {
            display: block;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #2196f3;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .payment-steps {
            background: white;
            padding: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .payment-steps h6 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #2d3748;
        }

        .step {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            align-items: flex-start;
        }

        .step-number {
            background: #667eea;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
        }

        .step-text {
            color: #4a5568;
            font-size: 14px;
            line-height: 1.6;
        }

        .action-buttons {
            padding: 24px;
            display: flex;
            gap: 12px;
            background: #f8f9fa;
        }

        .btn {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .success-screen {
            display: none;
            padding: 60px 24px;
            text-align: center;
        }

        .success-screen.active {
            display: block;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #48bb78;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 48px;
        }

        .success-title {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 12px;
        }

        .success-message {
            font-size: 16px;
            color: #718096;
            margin-bottom: 32px;
        }

        @media (max-width: 480px) {
            .payment-container {
                border-radius: 16px;
            }
            
            .qr-code img {
                max-width: 250px;
            }
            
            .amount-display {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div id="qrScreen">
            <div class="payment-header">
                <h2>📱 Scan to Pay</h2>
                <p class="subtitle">Use your Maya app to complete payment</p>
            </div>

            <div class="qr-section">
                <div class="qr-code">
                    {{-- Display QR code generated by Endroid --}}
                    <img src="{{ $qrCodeDataUri }}" alt="Maya QR Code">
                </div>
                <p class="business-name">L PRIMERO</p>
                <p class="amount-display">₱{{ number_format($order->total_amount, 2) }}</p>
                <p class="instruction">Open Maya app and scan the QR code above</p>
            </div>

            <div class="order-details">
                <h5>Order Summary</h5>
                <div class="detail-row">
                    <span class="detail-label">Order Number:</span>
                    <span class="detail-value">#{{ $order->order_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Order Type:</span>
                    <span class="detail-value">{{ ucfirst($order->order_type) }}</span>
                </div>
                @if($order->table_number)
                <div class="detail-row">
                    <span class="detail-label">Table:</span>
                    <span class="detail-value">{{ $order->table_number }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Items:</span>
                    <span class="detail-value">{{ $order->orderItems->count() }} item(s)</span>
                </div>
                <div class="detail-row" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e2e8f0;">
                    <span class="detail-label" style="font-weight: 600;">Total Amount:</span>
                    <span class="detail-value" style="font-size: 18px; color: #667eea;">₱{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            <div class="countdown">
                <div>⏰ QR Code expires in:</div>
                <div class="countdown-time" id="timeLeft">15:00</div>
            </div>

            <div class="status-checking" id="statusCheck">
                <div class="loading-spinner"></div>
                <span style="margin-left: 12px;">Checking payment status...</span>
            </div>

            <div class="payment-steps">
                <h6>💡 How to pay:</h6>
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-text">Open your Maya app on your phone</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-text">Tap "Scan QR" or use your camera</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-text">Scan the QR code displayed above</div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-text">Confirm the amount and complete payment</div>
                </div>
            </div>

            <div class="action-buttons">
                <button class="btn btn-secondary" onclick="cancelPayment()">Cancel</button>
                <button class="btn btn-primary" onclick="refreshQR()">Refresh QR</button>
            </div>
        </div>

        <div class="success-screen" id="successScreen">
            <div class="success-icon">✓</div>
            <h3 class="success-title">Payment Successful!</h3>
            <p class="success-message">Your payment of ₱{{ number_format($order->total_amount, 2) }} has been confirmed</p>
            <p style="color: #718096; margin-bottom: 32px;">Please proceed to the counter to collect your order</p>
            <button class="btn btn-primary" onclick="returnToMenu()" style="width: 100%;">Return to Menu</button>
        </div>
    </div>

    <script>
        let countdownTimer;
        let statusCheckInterval;
        let timeLeft = {{ $expiresIn }}; // 15 minutes in seconds
        const orderId = {{ $order->id }};

        // Start countdown timer
        function startCountdown() {
            countdownTimer = setInterval(() => {
                timeLeft--;
                updateCountdownDisplay();

                if (timeLeft <= 0) {
                    clearInterval(countdownTimer);
                    showExpired();
                }
            }, 1000);
        }

        // Update countdown display
        function updateCountdownDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('timeLeft').textContent = 
                `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        // Check payment status via API
        async function checkPaymentStatus() {
            try {
                const response = await fetch(`/maya/qr/status/${orderId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success && data.status === 'paid') {
                    // Payment successful!
                    clearInterval(statusCheckInterval);
                    clearInterval(countdownTimer);
                    showSuccess();
                    
                    // Auto-redirect after 5 seconds
                    setTimeout(() => {
                        window.location.href = '{{ route("kiosk.paymentSuccess") }}';
                    }, 5000);
                }
            } catch (error) {
                console.error('Error checking payment status:', error);
            }
        }

        // Start status checking
        function startStatusChecking() {
            // Show checking indicator
            document.getElementById('statusCheck').classList.add('active');
            
            // Check every 3 seconds
            statusCheckInterval = setInterval(checkPaymentStatus, 3000);
            
            // Also check immediately
            checkPaymentStatus();
        }

        // Show success screen
        function showSuccess() {
            document.getElementById('qrScreen').style.display = 'none';
            document.getElementById('successScreen').classList.add('active');
        }

        // Show expired screen
        function showExpired() {
            alert('QR code has expired. Please generate a new one.');
            window.location.href = '{{ route("kiosk.reviewOrder") }}';
        }

        // Refresh QR code
        function refreshQR() {
            window.location.reload();
        }

        // Cancel payment
        function cancelPayment() {
            if (confirm('Are you sure you want to cancel this payment?')) {
                clearInterval(countdownTimer);
                clearInterval(statusCheckInterval);
                window.location.href = '{{ route("kiosk.main") }}';
            }
        }

        // Return to menu
        function returnToMenu() {
            window.location.href = '{{ route("kiosk.index") }}';
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Maya QR Payment page loaded');
            console.log('Order ID:', orderId);
            
            startCountdown();
            startStatusChecking();
        });

        // Cleanup intervals on page unload
        window.addEventListener('beforeunload', function() {
            clearInterval(countdownTimer);
            clearInterval(statusCheckInterval);
        });
    </script>
</body>
</html>