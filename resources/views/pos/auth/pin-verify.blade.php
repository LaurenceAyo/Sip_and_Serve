<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Your PIN - L' PRIMERO CAFE</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            display: flex;
            width: 800px;
            height: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .left-panel {
            background: linear-gradient(135deg, #8B5CF6 0%, #A855F7 100%);
            color: white;
            padding: 40px 30px;
            width: 280px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .cafe-name {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .time-display {
            margin-top: auto;
        }

        .day {
            font-size: 16px;
            opacity: 0.8;
            margin-bottom: 5px;
        }

        .time {
            font-size: 48px;
            font-weight: 300;
        }

        .am-pm {
            font-size: 18px;
            margin-left: 5px;
        }

        .tagline {
            font-size: 18px;
            font-weight: 500;
            margin-top: 30px;
        }

        .right-panel {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .setup-title {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .setup-subtitle {
            font-size: 16px;
            color: #6b7280;
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.4;
        }

        .pin-display {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
        }

        .pin-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #e5e7eb;
            transition: all 0.2s ease;
        }

        .pin-dot.filled {
            background: #8B5CF6;
            transform: scale(1.2);
        }

        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            width: 240px;
        }

        .key {
            width: 70px;
            height: 70px;
            border: none;
            border-radius: 50%;
            background: #f3f4f6;
            color: #374151;
            font-size: 24px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .key:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }

        .key:active {
            transform: translateY(0);
        }

        .key.zero {
            grid-column: 2;
        }

        .key.delete {
            background: #fee2e2;
            color: #dc2626;
            font-size: 20px;
        }

        .key.delete:hover {
            background: #fecaca;
        }

        .confirm-section {
            margin-top: 30px;
            text-align: center;
            width: 100%;
        }

        .confirm-btn {
            background: #8B5CF6;
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            opacity: 0.5;
            pointer-events: none;
        }

        .confirm-btn.active {
            opacity: 1;
            pointer-events: all;
        }

        .confirm-btn.active:hover {
            background: #7C3AED;
            transform: translateY(-2px);
        }

        .step-indicator {
            font-size: 14px;
            color: #6b7280;
            margin-top: 20px;
        }

        .error-message {
            color: #dc2626;
            font-size: 14px;
            margin-top: 10px;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .error-message.show {
            opacity: 1;
        }

        .success-message {
            color: #059669;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div>
                <h1 class="cafe-name">L' PRIMERO<br>CAFE</h1>
            </div>

            <div class="time-display">
                <div class="day" id="current-day"></div>
                <div class="time">
                    <span id="current-time"></span>
                    <span class="am-pm" id="am-pm"></span>
                </div>
            </div>

            <div class="tagline">Sip & Serve</div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <form id="pin-verify-form" action="{{ route('pos.pin.verify') }}" method="POST">
                @csrf
                <h2 class="setup-title" id="form-title">Enter Your PIN</h2>
                <p class="setup-subtitle" id="form-subtitle">
                    Welcome back, {{ Auth::user()->name }}. Enter your 4-digit PIN to continue.
                </p>

                <!-- PIN Display Dots -->
                <div class="pin-display">
                    <div class="pin-dot" id="dot-1"></div>
                    <div class="pin-dot" id="dot-2"></div>
                    <div class="pin-dot" id="dot-3"></div>
                    <div class="pin-dot" id="dot-4"></div>
                </div>

                <!-- Number Keypad -->
                <div class="keypad">
                    <button type="button" class="key" data-number="1">1</button>
                    <button type="button" class="key" data-number="2">2</button>
                    <button type="button" class="key" data-number="3">3</button>
                    <button type="button" class="key" data-number="4">4</button>
                    <button type="button" class="key" data-number="5">5</button>
                    <button type="button" class="key" data-number="6">6</button>
                    <button type="button" class="key" data-number="7">7</button>
                    <button type="button" class="key" data-number="8">8</button>
                    <button type="button" class="key" data-number="9">9</button>
                    <button type="button" class="key zero" data-number="0">0</button>
                    <button type="button" class="key delete" id="delete-btn">⌫</button>
                </div>

                <!-- Confirm Section -->
                <div class="confirm-section">
                    <button type="submit" class="confirm-btn" id="confirm-btn">Verify PIN</button>
                    <div class="step-indicator" id="step-indicator">Enter your PIN to access POS</div>
                    <div class="error-message" id="error-message"></div>
                </div>

                <!-- Hidden inputs -->
                <input type="hidden" name="pin" id="pin-input">
            </form>
        </div>
    </div>

    <script>
        let currentPin = '';

        // Time display
        function updateTime() {
            const now = new Date();
            const options = { weekday: 'long', month: 'long', day: 'numeric' };
            const day = now.toLocaleDateString('en-US', options);
            const time = now.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: false
            });

            document.getElementById('current-day').textContent = day;
            document.getElementById('current-time').textContent = time;
        }

        // Update time every second
        updateTime();
        setInterval(updateTime, 1000);

        // PIN handling
        const dots = document.querySelectorAll('.pin-dot');
        const keys = document.querySelectorAll('.key[data-number]');
        const deleteBtn = document.getElementById('delete-btn');
        const confirmBtn = document.getElementById('confirm-btn');
        const errorMessage = document.getElementById('error-message');
        const stepIndicator = document.getElementById('step-indicator');
        const formTitle = document.getElementById('form-title');
        const formSubtitle = document.getElementById('form-subtitle');

        

        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.classList.add('show');
            setTimeout(() => {
                errorMessage.classList.remove('show');
            }, 3000);
        }

        // Number key clicks
        keys.forEach(key => {
            key.addEventListener('click', (e) => {
                e.preventDefault();
                const number = key.dataset.number;

                
            });
        });
        

        // Confirm button
        confirmBtn.addEventListener('click', (e) => {
            e.preventDefault();

            

        // Keyboard support
        document.addEventListener('keydown', (e) => {
            if (e.key >= '0' && e.key <= '9') {
                e.preventDefault();
                const number = e.key;     
            } else if (e.key === 'Enter') {
                e.preventDefault();
                confirmBtn.click();
            }
        });

        // Handle Laravel validation errors
        @if($errors->any())
            showError('{{ $errors->first() }}');
        @endif
    </script>
</body>

</html>