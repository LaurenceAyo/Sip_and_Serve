<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>L'PRIMERO CAFE - Kiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2c1810 0%, #4a3228 100%);
            min-height: 100vh;
            overflow: hidden;
            touch-action: manipulation;
            /* Optimize touch events */
        }

        .kiosk-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 10;
            font-family: sans-serif;
        }

        .cafe-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2F1B14;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-family: sans-serif;
        }

        /* Side Navigation */
        .side-nav {
            position: absolute;
            left: 0;
            top: 0;
            height: 100vh;
            width: 80px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            padding-top: 15vh;
            z-index: 5;
        }

        .nav-text {
            color: white;
            font-size: 2.7rem;
            font-weight: bold;
            letter-spacing: 4px;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            font-family: 'Playfair Display', serif;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-left: 80px;
            background: url('/assets/bg1_sandwich.png') no-repeat center center fixed;
            background-size: cover;
            width: 100%;
            min-height: 100vh;
            /* Fallback background if image not found */
            background-color: #4a3228;
        }

        /* Dynamic background loading */
        .main-content.bg-loaded {
            background-image: var(--bg-image);
        }

        .content-wrapper {
            text-align: center;
            max-width: 600px;
            position: relative;
            width: 100%;
        }

        /* Success/Error Messages */
        .alert {
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
            animation: slideIn 0.5s ease-out;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.9);
            color: white;
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.9);
            color: white;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Action Buttons - Optimized for Lenovo Tablet */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 30px;
            align-items: center;
            position: fixed;
            top: 50%;
            transform: translate(-60%, -50%);
            /* Changed from -50% to -60% */
            right: 150px;
        }

        .button-form {
            display: inline;
            width: 100%;
        }

        .action-btn {
            width: 320px;
            padding: 25px 50px;
            font-size: 2rem;
            font-weight: bold;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
            min-height: 80px;
            width: 100%;

            /* Critical tablet fixes */
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .action-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .action-btn:hover::before {
            left: 100%;
        }

        .dine-in-btn {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .dine-in-btn:hover,
        .dine-in-btn:active {
            background: linear-gradient(135deg, #229954, #27ae60);
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 12px 30px rgba(39, 174, 96, 0.5);
        }

        .take-out-btn {
            background: linear-gradient(135deg, #e67e22, #f39c12);
            color: white;
        }

        .take-out-btn:hover,
        .take-out-btn:active {
            background: linear-gradient(135deg, #d35400, #e67e22);
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 12px 30px rgba(230, 126, 34, 0.5);
        }

        /* Enhanced touch feedback for tablets */
        .action-btn:active:not(:disabled) {
            transform: translateY(2px) scale(0.98);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .action-btn.touched {
            transform: scale(0.95);
            transition: transform 0.1s ease;
        }

        /* Loading state */
        .action-btn.loading {
            position: relative;
            color: transparent;
        }

        .action-btn.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 24px;
            height: 24px;
            margin: -12px 0 0 -12px;
            border: 2px solid #fff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Decorative Elements */
        .decoration {
            position: absolute;
            opacity: 0.1;
        }

        .decoration-1 {
            top: 20%;
            right: 10%;
            width: 50px;
            height: 50px;
            background: #f39c12;
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .decoration-2 {
            bottom: 20%;
            right: 20%;
            width: 30px;
            height: 30px;
            background: #27ae60;
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        .decoration-3 {
            top: 40%;
            right: 5%;
            width: 20px;
            height: 20px;
            background: #e74c3c;
            border-radius: 50%;
            animation: float 7s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        /* Lenovo Xiaoxin Pad 2024 Optimizations */
        @media (min-width: 1200px) and (max-width: 1920px) {
            .cafe-title {
                font-size: 3rem;
            }

            .action-btn {
                width: 380px;
                font-size: 2.2rem;
                padding: 30px 60px;
                min-height: 90px;
            }

            .action-buttons {
                gap: 40px;
            }

            .nav-text {
                font-size: 3rem;
            }
        }

        /* Portrait mode optimization */
        @media (orientation: portrait) {
            .action-buttons {
                left: 50%;
                margin-left: 0;
            }

            .main-content {
                padding-left: 60px;
            }

            .side-nav {
                width: 60px;
            }

            .nav-text {
                font-size: 2.2rem;
            }

            .action-btn {
                width: 280px;
                font-size: 1.8rem;
                padding: 20px 40px;
            }
        }

        /* Standard tablet responsive */
        @media (min-width: 768px) and (max-width: 1199px) {
            .cafe-title {
                font-size: 2.5rem;
            }

            .action-btn {
                width: 300px;
                font-size: 1.9rem;
                padding: 22px 45px;
                min-height: 75px;
            }

            .action-buttons {
                gap: 25px;
            }
        }

        /* Mobile fallback */
        @media (max-width: 767px) {
            .cafe-title {
                font-size: 2rem;
            }

            .action-btn {
                width: 250px;
                font-size: 1.6rem;
                padding: 18px 35px;
                min-height: 70px;
            }

            .action-buttons {
                margin-left: 20px;
                gap: 20px;
            }

            .main-content {
                padding-left: 60px;
            }

            .side-nav {
                width: 60px;
            }

            .nav-text {
                font-size: 2rem;
            }
        }

        /* High DPI display optimization */
        @media (-webkit-min-device-pixel-ratio: 2),
        (min-resolution: 192dpi) {
            .action-btn {
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
        }

        /* Battery saving for long kiosk sessions */
        @media (prefers-reduced-motion: reduce) {

            .decoration-1,
            .decoration-2,
            .decoration-3 {
                animation: none;
            }

            .action-btn::before {
                transition: none;
            }
        }
    </style>
</head>

<body>
    <div class="kiosk-container">
        <!-- Header -->
        <header class="header">
            <h1 class="cafe-title">L' PRIMERO CAFE</h1>
        </header>

        <!-- Side Navigation -->
        <nav class="side-nav">
            <div class="nav-text">Sip & Serve</div>
        </nav>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <div class="content-wrapper">
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <form method="POST" action="{{ route('kiosk.dineIn') }}" class="button-form" id="dineInForm">
                        @csrf
                        <button type="submit" class="action-btn dine-in-btn" id="dineInBtn">
                            Dine In
                        </button>
                    </form>

                    <form method="POST" action="{{ route('kiosk.takeOut') }}" class="button-form" id="takeOutForm">
                        @csrf
                        <button type="submit" class="action-btn take-out-btn" id="takeOutBtn">
                            Take Out
                        </button>
                    </form>
                </div>
            </div>
        </main>

        <!-- Decorative Elements -->
        <div class="decoration decoration-1"></div>
        <div class="decoration decoration-2"></div>
        <div class="decoration decoration-3"></div>
    </div>

    <script>
        // Load background image dynamically
        document.addEventListener('DOMContentLoaded', function () {
            const mainContent = document.getElementById('mainContent');
            const img = new Image();

            img.onload = function () {
                mainContent.style.backgroundImage = `url('${this.src}')`;
                mainContent.classList.add('bg-loaded');
            };

            img.onerror = function () {
                console.log('Background image not found, using fallback');
            };

            img.src = '/assets/bg1_sandwich.png';
        });

        // TABLET-SPECIFIC FIXES FOR TOUCH EVENTS
        document.querySelectorAll('.action-btn').forEach(button => {
            let touchStarted = false;
            let formSubmitted = false;

            // Touch start event
            button.addEventListener('touchstart', function (e) {
                if (this.disabled || formSubmitted) {
                    e.preventDefault();
                    return;
                }

                console.log('Touch started on:', this.textContent.trim());
                touchStarted = true;

                // Visual feedback
                this.classList.add('touched');

                // Haptic feedback if available
                if (navigator.vibrate) {
                    navigator.vibrate(50);
                }

                // Prevent default to avoid issues
                e.preventDefault();
            }, { passive: false });

            // Touch end event - this is where we submit the form
            button.addEventListener('touchend', function (e) {
                if (this.disabled || formSubmitted || !touchStarted) {
                    e.preventDefault();
                    return;
                }

                console.log('Touch ended on:', this.textContent.trim());

                // Remove visual feedback
                this.classList.remove('touched');

                // Prevent double submission
                formSubmitted = true;
                this.disabled = true;
                this.classList.add('loading');

                // Submit the form
                const form = this.closest('form');
                if (form) {
                    console.log('Submitting form:', form.action);
                    form.submit();
                } else {
                    console.error('Form not found!');
                    // Re-enable if form not found
                    this.disabled = false;
                    this.classList.remove('loading');
                    formSubmitted = false;
                }

                touchStarted = false;
                e.preventDefault();
            }, { passive: false });

            // Touch cancel event
            button.addEventListener('touchcancel', function (e) {
                console.log('Touch cancelled on:', this.textContent.trim());
                this.classList.remove('touched');
                touchStarted = false;
                e.preventDefault();
            }, { passive: false });

            // Click event as fallback for desktop/laptop
            button.addEventListener('click', function (e) {
                if (this.disabled || formSubmitted) {
                    e.preventDefault();
                    return;
                }

                // Only process click if not already handled by touch
                if (!touchStarted) {
                    console.log('Click event on:', this.textContent.trim());

                    formSubmitted = true;
                    this.disabled = true;
                    this.classList.add('loading');

                    const form = this.closest('form');
                    if (form) {
                        console.log('Submitting form via click:', form.action);
                        setTimeout(() => form.submit(), 100);
                    }
                }

                e.preventDefault();
            });
        });

        // Additional tablet optimizations
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, { passive: false });

        // Prevent double-tap zoom
        let lastTouchTime = 0;
        document.addEventListener('touchstart', function (event) {
            const currentTime = new Date().getTime();
            const tapLength = currentTime - lastTouchTime;
            if (tapLength < 500 && tapLength > 0) {
                event.preventDefault();
            }
            lastTouchTime = currentTime;
        }, { passive: false });

        // Enhanced error logging for debugging
        window.addEventListener('error', function (e) {
            console.error('JavaScript error:', e.error);
            console.error('Error details:', {
                message: e.message,
                filename: e.filename,
                lineno: e.lineno,
                colno: e.colno
            });
        });

        // Monitor form submission attempts
        document.addEventListener('submit', function (e) {
            console.log('Form submission detected:', e.target.action);
        });

        // Auto-hide cursor after inactivity (kiosk mode)
        let cursorTimeout;
        document.addEventListener('mousemove', function () {
            document.body.style.cursor = 'default';
            clearTimeout(cursorTimeout);
            cursorTimeout = setTimeout(() => {
                document.body.style.cursor = 'none';
            }, 3000);
        });

        // Prevent right-click and text selection (kiosk security)
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('selectstart', e => e.preventDefault());
        document.addEventListener('dragstart', e => e.preventDefault());

        // Screen wake lock for kiosk mode
        let wakeLock = null;
        async function requestWakeLock() {
            try {
                wakeLock = await navigator.wakeLock.request('screen');
                console.log('Screen wake lock activated');
            } catch (err) {
                console.log('Wake lock not supported');
            }
        }

        if ('wakeLock' in navigator) {
            requestWakeLock();
        }

        // Auto-dismiss alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        });

        // Connection status monitoring
        window.addEventListener('online', function () {
            console.log('Connection restored');
        });

        window.addEventListener('offline', function () {
            console.log('Connection lost');
        });
    </script>
</body>

</html>