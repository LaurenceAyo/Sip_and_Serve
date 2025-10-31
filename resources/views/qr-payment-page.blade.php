<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Code Payment - L' Primero Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .payment-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }

        .payment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .payment-header {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .qr-container {
            padding: 40px;
            text-align: center;
        }

        .qr-code {
            border: 3px solid #f0f0f0;
            border-radius: 15px;
            padding: 20px;
            margin: 20px auto;
            background: white;
            display: inline-block;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            max-width: 300px;
        }

        .qr-code img {
            max-width: 100%;
            height: auto;
        }

        .order-details {
            background: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
        }

        .total-amount {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
            margin: 15px 0;
        }

        .countdown {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }

        .status-checking {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }

        .payment-instructions {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 20px 0;
        }

        .manual-payment {
            background: #f8f9fa;
            border: 2px solid #007bff;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .step {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            background: white;
            border-radius: 8px;
        }

        .step-number {
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .success-animation {
            color: #28a745;
            font-size: 3rem;
            animation: bounce 0.5s;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            60% {
                transform: translateY(-5px);
            }
        }
    </style>
</head>

<body>
    <div class="payment-container">
        <div class="payment-card">
            <div class="payment-header">
                <h2 class="mb-0">
                    <i class="fas fa-mobile-alt me-2"></i>
                    GCash Payment
                </h2>
                <p class="mb-0 mt-2">Manual payment instructions</p>
            </div>

            <div class="qr-container" id="qrContainer">
                <div class="loading-spinner mb-3"></div>
                <p>Generating QR Code...</p>
            </div>

            <div class="order-details">
                <h5>Order Summary</h5>
                <div class="d-flex justify-content-between">
                    <span>Order #{{ $order->id ?? '001' }}</span>
                    <span>{{ isset($order) ? $order->created_at->format('M d, Y h:i A') : now()->format('M d, Y h:i A') }}</span>
                </div>
                <div class="total-amount text-center">
                    ₱{{ isset($order) ? number_format((float) $order->total_amount, 2) : '150.00' }}
                </div>
            </div>

            <div class="payment-instructions">
                <h6><i class="fas fa-mobile-alt me-2"></i>Payment Steps:</h6>
                <div class="step">
                    <div class="step-number">1</div>
                    <div>Open GCash app → Send Money</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div>Enter the mobile number shown above</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div>Enter exact amount and add order reference</div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div>Show payment receipt to staff</div>
                </div>
            </div>

            <div class="countdown" id="countdown">
                <i class="fas fa-clock me-2"></i>
                Time remaining: <span id="timeLeft">15:00</span>
            </div>

            <div class="status-checking" id="statusCheck" style="display: none;">
                <div class="loading-spinner me-2"></div>
                Checking payment status...
            </div>

            <div class="text-center p-3">
                <button class="btn btn-outline-secondary me-2" onclick="refreshQR()">
                    <i class="fas fa-refresh me-2"></i>
                    Refresh QR
                </button>
                <button class="btn btn-outline-primary" onclick="checkStatus()">
                    <i class="fas fa-search me-2"></i>
                    Check Status
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let countdownTimer;
        let statusCheckInterval;
        let timeLeft = 900;
        const orderId = {{ $order->id ?? 1 }};

        document.addEventListener('DOMContentLoaded', function () {
            generateQRCode();
            startCountdown();
            startStatusChecking();
        });

        async function generateQRCode() {
            try {
                // Show static QR code instead of generating dynamic one
                document.getElementById('qrContainer').innerHTML = `
            <div class="qr-code">
                
            </div>
            <p class="mt-3 text-muted">Scan with Maya app to pay</p>
            
        `;
            } catch (error) {
                console.error('QR display error:', error);
                showError('Error loading payment information');
            }
        }

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

        function updateCountdownDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('timeLeft').textContent =
                `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        function startStatusChecking() {
            statusCheckInterval = setInterval(checkStatus, 5000);
        }

        async function checkStatus() {
            const statusElement = document.getElementById('statusCheck');
            statusElement.style.display = 'block';

            // Since this is manual, you could either:
            // 1. Always return false (staff confirms manually)
            // 2. Create an admin interface to mark payments as received
            // 3. Remove auto-checking entirely

            statusElement.innerHTML = `
        <div class="text-center p-3">
            <p>Payment confirmation is handled manually by staff.</p>
            <button class="btn btn-success" onclick="showSuccess()">Mark as Paid (Staff Only)</button>
        </div>
    `;
        }

        function showSuccess() {
            document.querySelector('.payment-card').innerHTML = `
                <div class="payment-header">
                    <div class="success-animation">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 class="mt-3">Payment Successful!</h2>
                </div>
                <div class="p-4 text-center">
                    <h4 class="text-success">₱{{ isset($order) ? number_format((float) $order->total_amount, 2) : '150.00' }}</h4>
                    <p class="text-muted">Your payment has been confirmed</p>
                    <div class="mt-4">
                        <a href="{{ route('kiosk.index') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>
                            Return to Menu
                        </a>
                    </div>
                </div>
            `;
        }

        function showExpired() {
            document.getElementById('countdown').innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                <span class="text-danger">QR Code Expired</span>
            `;

            document.getElementById('qrContainer').innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-clock text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">QR Code Expired</h5>
                    <p class="text-muted">Please generate a new QR code to continue</p>
                    <button class="btn btn-primary" onclick="refreshQR()">
                        Generate New QR Code
                    </button>
                </div>
            `;
        }

        function showError(message) {
            document.getElementById('qrContainer').innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Error</h5>
                    <p class="text-muted">${message}</p>
                    <button class="btn btn-primary" onclick="generateQRCode()">
                        Try Again
                    </button>
                </div>
            `;
        }

        function refreshQR() {
            timeLeft = 900;
            document.getElementById('countdown').innerHTML = `
                <i class="fas fa-clock me-2"></i>
                Time remaining: <span id="timeLeft">15:00</span>
            `;

            clearInterval(countdownTimer);
            clearInterval(statusCheckInterval);

            // Show loading state
            document.getElementById('qrContainer').innerHTML = `
                <div class="loading-spinner mb-3"></div>
                <p>Generating QR Code...</p>
            `;

            generateQRCode();
            startCountdown();
            startStatusChecking();
        }
    </script>
</body>

</html>