<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Sip & Serve</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        /* Background slideshow container */
        .background-slideshow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .background-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            background-color: #c2a477;
            opacity: 0;
            transition: opacity 6s ease-in-out;
        }

        .background-slide.active {
            opacity: 1;
        }

        .background-slide::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(255, 247, 230, 0.3) 0%, rgba(194, 164, 119, 0.5) 100%);
        }

        .login-container {
            opacity: 0.95;
            background-color: rgba(0, 0, 0, 0.25);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            width: 90%;
            max-width: 400px;
            z-index: 1;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .welcome {
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 0.9rem;
            border: none;
            border-radius: 10px;
            margin-bottom: 0.8rem;
            font-size: 1rem;
            outline: none;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 40%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #ccc;
            font-size: 1.2rem;
        }

        .forgot {
            display: block;
            text-align: right;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #fff;
            text-decoration: underline;
        }

        button {
            width: 100%;
            background-color: #26a269;
            color: white;
            font-size: 1.1rem;
            padding: 0.9rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        button:hover {
            background-color: #208856;
        }

        button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .button-text {
            transition: opacity 0.3s ease;
        }

        .loading-spinner {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .spinner {
            border: 2px solid #ffffff40;
            border-top: 2px solid #ffffff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
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

        .register {
            margin-top: 1rem;
            font-size: 0.95rem;
        }

        .register a {
            color: #a1f0c4;
            text-decoration: none;
            font-weight: bold;
        }

        .error-messages {
            background-color: rgba(220, 53, 69, 0.9);
            border: 1px solid #dc3545;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: left;
        }

        .error-message {
            color: #fff;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .error-message:last-child {
            margin-bottom: 0;
        }

        .success-message {
            background-color: rgba(40, 167, 69, 0.9);
            border: 1px solid #28a745;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            color: #fff;
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Background slideshow -->
    <div class="background-slideshow">
        <div class="background-slide active" style="background-image: url('{{ asset('assets/background_main_2.jpg') }}');"></div>
        <div class="background-slide" style="background-image: url('{{ asset('assets/background_main_3.jpg') }}');"></div>
        <div class="background-slide" style="background-image: url('{{ asset('assets/background_main_4.jpg') }}');"></div>
    </div>

    <div class="login-container">
        <h2 style="opacity: 0.7;">L' PRIMERO CAFE</h2>
        <div class="welcome">Welcome Back!</div>

        <!-- Display error messages -->
        @if ($errors->any())
            <div class="error-messages">
                @foreach ($errors->all() as $error)
                    <div class="error-message">{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- Display session errors -->
        @if (session('error'))
            <div class="error-messages">
                <div class="error-message">{{ session('error') }}</div>
            </div>
        @endif

        <!-- Display success message -->
        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" autocomplete="off" required>

            <div class="password-container">
                <input type="password" name="password" placeholder="Password" id="password-field" autocomplete="off" required>
                <span class="toggle-password" onclick="togglePassword()" id="toggle-icon">👁️‍🗨️</span>
            </div>

            <!-- <a href="{{ route('password.request') }}" class="forgot">Forgot Password?</a> -->
            <div>
                &nbsp;
            </div>
            <button type="submit" id="loginBtn">
                <span class="button-text">LOGIN</span>
                <div class="loading-spinner" id="loadingSpinner">
                    <div class="spinner"></div>
                </div>
            </button>

            <!-- Hidden field to specify redirect after login -->
            <input type="hidden" name="redirect_to" value="{{ route('dashboard') }}">
        </form>

        <div class="register">
            Don't Have an Account? <a href="{{ route('admin.contact') }}">Ask Admin</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passField = document.getElementById("password-field");
            const toggleIcon = document.getElementById("toggle-icon");

            if (passField.type === "password") {
                passField.type = "text";
                toggleIcon.textContent = "👁️";
            } else {
                passField.type = "password";
                toggleIcon.textContent = "👁️‍🗨️";
            }
        }

        // Handle form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const btn = document.getElementById('loginBtn');
            const buttonText = btn.querySelector('.button-text');
            const loadingSpinner = document.getElementById('loadingSpinner');

            // Show loading state
            btn.disabled = true;
            buttonText.style.opacity = '0';
            loadingSpinner.style.display = 'block';
        });

        // Background slideshow functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.background-slide');
        const totalSlides = slides.length;

        function changeBackground() {
            // Remove active class from current slide
            slides[currentSlide].classList.remove('active');
            
            // Move to next slide
            currentSlide = (currentSlide + 1) % totalSlides;
            
            // Add active class to new slide
            slides[currentSlide].classList.add('active');
        }

        // Change background every seconds
        setInterval(changeBackground, 10000);
    </script>
</body>

</html>