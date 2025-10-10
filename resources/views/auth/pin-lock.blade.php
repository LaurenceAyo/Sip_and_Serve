<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Enter PIN</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #8b4513, #a0522d);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .pin-container {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: #2c1810;
            margin-bottom: 10px;
            font-size: 2rem;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }

        .pin-display {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .pin-digit {
            width: 50px;
            height: 50px;
            border: 2px solid #8b4513;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            color: #8b4513;
            background: #f8f9fa;
        }

        .pin-digit.filled {
            background: #8b4513;
            color: white;
        }

        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .key {
            padding: 20px;
            font-size: 1.5rem;
            font-weight: 600;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }

        .key:hover {
            background: #8b4513;
            color: white;
            transform: scale(1.05);
        }

        .key:active {
            transform: scale(0.95);
        }

        .key.clear {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        .key.clear:hover {
            background: #c82333;
        }

        .error-message {
            color: #dc3545;
            margin-top: 15px;
            font-weight: 600;
            min-height: 20px;
        }
    </style>
</head>

<body>
    <div class="pin-container">
        <h1>🔒 Security Lock</h1>
        <p class="subtitle">Enter your 4-digit PIN</p>

        <div class="pin-display">
            <div class="pin-digit" id="digit1"></div>
            <div class="pin-digit" id="digit2"></div>
            <div class="pin-digit" id="digit3"></div>
            <div class="pin-digit" id="digit4"></div>
        </div>

        <div class="keypad">
            <button class="key" onclick="addDigit('1')">1</button>
            <button class="key" onclick="addDigit('2')">2</button>
            <button class="key" onclick="addDigit('3')">3</button>
            <button class="key" onclick="addDigit('4')">4</button>
            <button class="key" onclick="addDigit('5')">5</button>
            <button class="key" onclick="addDigit('6')">6</button>
            <button class="key" onclick="addDigit('7')">7</button>
            <button class="key" onclick="addDigit('8')">8</button>
            <button class="key" onclick="addDigit('9')">9</button>
            <button class="key clear" onclick="clearPin()">Clear</button>
            <button class="key" onclick="addDigit('0')">0</button>
            <button class="key" onclick="submitPin()">✓</button>
        </div>

        <div class="error-message" id="error"></div>
    </div>

    <script>
        let pin = '';

        function addDigit(digit) {
            if (pin.length < 4) {
                pin += digit;
                updateDisplay();

                if (pin.length === 4) {
                    setTimeout(submitPin, 300);
                }
            }
        }

        function clearPin() {
            pin = '';
            updateDisplay();
            document.getElementById('error').textContent = '';
        }

        function updateDisplay() {
            for (let i = 1; i <= 4; i++) {
                const digit = document.getElementById(`digit${i}`);
                if (i <= pin.length) {
                    digit.textContent = '●';
                    digit.classList.add('filled');
                } else {
                    digit.textContent = '';
                    digit.classList.remove('filled');
                }
            }
        }

        function submitPin() {
            if (pin.length !== 4) {
                document.getElementById('error').textContent = 'Please enter 4 digits';
                return;
            }

            fetch('{{ route("pin.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ pin: pin })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to dashboard or intended page
                        window.location.href = data.redirect || '/dashboard';
                    } else {
                        document.getElementById('error').textContent = data.message || 'Incorrect PIN';
                        pin = '';
                        updateDisplay();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('error').textContent = 'Connection error';
                    pin = '';
                    updateDisplay();
                });
        }

        // Allow keyboard input
        document.addEventListener('keydown', (e) => {
            if (e.key >= '0' && e.key <= '9') {
                addDigit(e.key);
            } else if (e.key === 'Backspace' || e.key === 'Escape') {
                clearPin();
            } else if (e.key === 'Enter') {
                submitPin();
            }
        });
    </script>
</body>

</html>