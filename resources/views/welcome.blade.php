<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Sip & Serve</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background:
                url('{{ asset('assets/bg1_sandwich.png') }}') no-repeat center center fixed,
                linear-gradient(to bottom, #fff7e6 0%, #c2a477 100%);
            background-size: contain, cover;
            background-blend-mode: normal;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .login-container {
            background-color: rgba(0, 0, 0, 0.65);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            width: 90%;
            max-width: 400px;
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
    <div class="login-container">
        <h1>L' PRIMERO CAFE</h1>
        <div class="welcome">Welcome Back Manager!</div>

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
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>

            <div class="password-container">
                <input type="password" name="password" placeholder="Password" id="password-field" required>
                <span class="toggle-password" onclick="togglePassword()" id="toggle-icon">👁️‍🗨️</span>
            </div>

            <!-- <a href="{{ route('password.request') }}" class="forgot">Forgot Password?</a> -->
            <div >
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

            // Optional: Reset loading state after a delay (in case of errors)
            setTimeout(() => {
                if (btn.disabled) {
                    btn.disabled = false;
                    buttonText.style.opacity = '1';
                    loadingSpinner.style.display = 'none';
                }
            }, 5000);
        });

        // Check if login was successful and redirect
        @if(session('login_success'))
            setTimeout(() => {
                window.location.href = '{{ route('dashboard') }}';
            }, 1500);
        @endif
    </script>
</body>

</html>