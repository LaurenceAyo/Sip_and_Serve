<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        }

        .cafe-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c1810;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Side Navigation */
        .side-nav {
            position: absolute;
            left: 0;
            top: 0;
            height: 100vh;
            width: 80px;
            background: rgba(44, 24, 16, 0.9);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            padding-top: 10vh; /* Move "Sip & Serve" higher */
            z-index: 5;
        }

        @media (max-width: 768px) {
            .main-content {
            padding-left: 60px;
            background-size: cover;
            min-height: 100vh;
            }
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
            background: url('{{ asset('assets/bg1_sandwich.png') }}') no-repeat center center fixed;
            background-size: cover;
            width: 100%;
            min-height: 100vh;
        }

        .content-wrapper {
            text-align: center;
            max-width: 600px;
            position: relative;
            width: 100%;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin-left: 40px; /* Account for side nav width */
        }

        .action-btn {
            width: 250px;
            padding: 20px 40px;
            font-size: 1.8rem;
            font-weight: bold;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
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
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin-left: 0; /* Remove offset for perfect centering */
        }
        .dine-in-btn {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .dine-in-btn:hover {
            background: linear-gradient(135deg, #229954, #27ae60);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(39, 174, 96, 0.4);
        }

        .take-out-btn {
            background: linear-gradient(135deg, #e67e22, #f39c12);
            color: white;
        }
        
        .take-out-btn:hover {
            background: linear-gradient(135deg, #d35400, #e67e22);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(230, 126, 34, 0.4);
        }

        .action-btn:active {
            transform: translateY(1px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
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
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .cafe-title {
                font-size: 2rem;
            }
            
            .action-btn {
                width: 200px;
                font-size: 1.5rem;
                padding: 15px 30px;
            }
            
            .action-buttons {
                margin-left: 20px; /* Adjust for smaller screens */
            }
            
            .main-content {
                padding-left: 60px; /* Reduce padding on mobile */
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
        <main class="main-content">
            <div class="content-wrapper">
                <!-- Success Message -->
                @if(session('message'))
                    <div style="background: rgba(46, 204, 113, 0.9); color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: bold;">
                        {{ session('message') }}
                    </div>
                @endif
                

                <!-- Action Buttons -->
                <div class="action-buttons">
                <form method="POST" action="{{ route('kiosk.main') }}" style="display: inline;">
                @csrf
                <input type="hidden" name="order_type" value="dine-in">
                    <button type="submit" class="action-btn dine-in-btn">
                        Dine In
                    </button>
                </form>
    
                    <form method="POST" action="{{ route('kiosk.main') }}" style="display: inline;">
                    @csrf
                        <input type="hidden" name="order_type" value="take-out">
                            <button type="submit" class="action-btn take-out-btn">
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
        // Add click sound effect (optional)
        document.querySelectorAll('.action-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Add haptic feedback for mobile devices
                if (navigator.vibrate) {
                    navigator.vibrate(50);
                }
                
                // Add visual feedback
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });

        // Auto-hide cursor after inactivity (kiosk mode)
        let cursorTimeout;
        document.addEventListener('mousemove', function() {
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
    </script>
</body>
</html>