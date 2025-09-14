<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L' PRIMERO CAFE - Security Access</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .pin-dot {
            transition: all 0.3s ease;
            transform: scale(1);
        }
        .pin-dot.filled {
            background: #8b5cf6 !important;
            transform: scale(1.2);
        }
        .keypad-btn {
            transition: all 0.2s ease;
            font-family: 'Roboto', sans-serif;
        }
        .keypad-btn:hover {
            transform: scale(1.05);
            background: #e5e7eb;
        }
        .keypad-btn:active {
            transform: scale(0.95);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        }
        body {
            font-family: 'Poppins', sans-serif;
        }
        .time-display {
            font-family: 'Roboto', sans-serif;
            font-weight: 300;
        }
        
        /* Tablet optimizations */
        @media (min-width: 768px) and (max-width: 1024px) {
            .left-panel {
                width: 400px;
            }
            .keypad-btn {
                width: 80px;
                height: 80px;
                font-size: 1.5rem;
            }
            .pin-dot {
                width: 20px;
                height: 20px;
            }
            .main-title {
                font-size: 2.5rem;
            }
            .time-display {
                font-size: 3rem;
            }
        }
        
        /* Touch optimizations */
        .keypad-btn {
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex overflow-hidden">
    <!-- Left Panel -->
    <div class="gradient-bg text-white p-8 flex flex-col justify-between left-panel" style="width: 380px;">
        <div>
            <h1 class="main-title text-3xl font-bold mb-2">L' PRIMERO</h1>
            <h2 class="main-title text-3xl font-bold">CAFE</h2>
        </div>
        
        <div>
            <p class="text-sm opacity-90 mb-1" id="current-date"></p>
            <div class="time-display text-5xl font-light" id="current-time"></div>
        </div>
        
        <div class="text-xl font-light">
            Sip & Serve
        </div>
    </div>

    <!-- Right Panel -->
    <div class="flex-1 flex items-center justify-center p-8">
        <div class="max-w-md w-full">
            <!-- Header -->
            <div class="text-center mb-8">
                <h3 class="text-3xl font-semibold text-gray-800 mb-3">Hi Partner, enter your PIN</h3>
                <p class="text-gray-600 text-lg">Please enter your manager-issued 4-PIN code to verify it's you.</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ $errors->first('pin') }}
                </div>
            @endif

            @if (session('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('pin.authenticate') }}" id="pin-form">
                @csrf
                <input type="hidden" name="pin" id="pin-input" value="">
                
                <!-- PIN Dots -->
                <div class="flex justify-center space-x-6 mb-10">
                    <div class="pin-dot w-5 h-5 bg-gray-300 rounded-full" data-index="0"></div>
                    <div class="pin-dot w-5 h-5 bg-gray-300 rounded-full" data-index="1"></div>
                    <div class="pin-dot w-5 h-5 bg-gray-300 rounded-full" data-index="2"></div>
                    <div class="pin-dot w-5 h-5 bg-gray-300 rounded-full" data-index="3"></div>
                </div>

                <!-- Keypad -->
                <div class="grid grid-cols-3 gap-6 max-w-sm mx-auto">
                    <!-- Row 1 -->
                    <button type="button" class="keypad-btn w-20 h-20 bg-gray-200 rounded-full text-2xl font-semibold text-gray-700" data-digit="1">1</button>
                    <button type="button" class="keypad-btn w-20 h-20 bg-gray-200 rounded-full text-2xl font-semibold text-gray-700" data-digit="2">2</button>
                    <button type="button" class="keypad-btn w-20 h-20 bg-gray-200 rounded-full text-2xl font-semibold text-gray-700" data-digit="3">3</button>
                    
                    <!-- Row 2 -->
                    <button type="button" class="keypad-btn w-20 h-20 bg-gray-200 rounded-full text-2xl font-semibold text-gray-700" data-digit="4">4</button>
                    <button type="button" class="keypad-btn w-20 h-20 bg-gray-200 rounded-full text-2xl font-semibold text-gray-700" data-digit="5">5</button>
                    <button type="button" class="keypad-btn w-20 h-20 bg-gray-200 rounded-full text-2xl font-semibold text-gray-700" data-digit="6">6</button>
                    
                    <!-- Row 3 -->
                    <button type="button" class="keypad-btn w-20 h-20 bg-gray-200 rounded-full text-2xl font-semibold text-gray-700" data-digit="7">7</button>
                    <button type="button" class="keypad-btn w-20 h-20 bg-gray-200 rounded-full text-2xl font-semibold text-gray-700" data-digit="8">8</button>
                    <button type="button" class="keypad-btn w-20 h-20 bg-gray-200 rounded-full text-2xl font-semibold text-gray-700" data-digit="9">9</button>
                    
                    <!-- Row 4 -->
                    <div></div>
                    <button type="button" class="keypad-btn w-20 h-20 bg-gray-200 rounded-full text-2xl font-semibold text-gray-700" data-digit="0">0</button>
                    <button type="button" class="keypad-btn w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center text-gray-700" id="backspace-btn">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let pinValue = '';
        const maxPinLength = 4;
        
        // Update time and date
        function updateDateTime() {
            const now = new Date();
            
            // Update date
            const dateOptions = { weekday: 'long', month: 'short', day: 'numeric' };
            const dateString = now.toLocaleDateString('en-US', dateOptions);
            document.getElementById('current-date').textContent = dateString;
            
            // Update time
            const timeOptions = { 
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
            };
            const timeString = now.toLocaleTimeString('en-US', timeOptions);
            const [time, period] = timeString.split(' ');
            document.getElementById('current-time').innerHTML = `${time}<span class="text-2xl">${period}</span>`;
        }
        
        // Initialize and update every second
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Handle keypad clicks
        document.querySelectorAll('.keypad-btn[data-digit]').forEach(button => {
            button.addEventListener('click', function() {
                if (pinValue.length < maxPinLength) {
                    pinValue += this.dataset.digit;
                    updatePinDots();
                    
                    if (pinValue.length === maxPinLength) {
                        setTimeout(() => {
                            document.getElementById('pin-input').value = pinValue;
                            document.getElementById('pin-form').submit();
                        }, 300);
                    }
                }
            });
        });

        // Handle backspace
        document.getElementById('backspace-btn').addEventListener('click', function() {
            if (pinValue.length > 0) {
                pinValue = pinValue.slice(0, -1);
                updatePinDots();
            }
        });

        // Handle keyboard input
        document.addEventListener('keydown', function(e) {
            if (e.key >= '0' && e.key <= '9') {
                if (pinValue.length < maxPinLength) {
                    pinValue += e.key;
                    updatePinDots();
                    
                    if (pinValue.length === maxPinLength) {
                        setTimeout(() => {
                            document.getElementById('pin-input').value = pinValue;
                            document.getElementById('pin-form').submit();
                        }, 300);
                    }
                }
            } else if (e.key === 'Backspace') {
                if (pinValue.length > 0) {
                    pinValue = pinValue.slice(0, -1);
                    updatePinDots();
                }
            }
        });

        function updatePinDots() {
            const dots = document.querySelectorAll('.pin-dot');
            dots.forEach((dot, index) => {
                if (index < pinValue.length) {
                    dot.classList.add('filled');
                } else {
                    dot.classList.remove('filled');
                }
            });
        }

        // Prevent zoom on double tap for better tablet experience
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>
</body>
</html>