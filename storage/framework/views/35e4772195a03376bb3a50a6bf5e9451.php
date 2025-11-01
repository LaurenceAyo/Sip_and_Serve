<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Change PIN</title>
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
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 450px;
            width: 90%;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: #2c1810;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }

        .subtitle {
            color: #666;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c1810;
        }

        .pin-input-group {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .pin-input {
            width: 50px;
            height: 50px;
            border: 2px solid #8b4513;
            border-radius: 10px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            background: #f8f9fa;
        }

        .pin-input:focus {
            outline: none;
            background: #fff;
            border-color: #5a2d0c;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            padding: 15px 25px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            flex: 1;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #8b4513;
            color: white;
        }

        .btn-primary:hover {
            background: #5a2d0c;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            font-weight: 600;
            min-height: 20px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Enhanced Styles for Forgot PIN Section */
        .forgot-pin-section {
            margin-top: 30px;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            background: #f8f9fa;
        }

        .forgot-pin-header {
            background: #dc3545;
            color: white;
            padding: 12px 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .forgot-pin-content {
            padding: 20px;
        }

        .security-notice {
            color: #856404;
            background: #fff3cd;
            padding: 12px;
            border-radius: 6px;
            border-left: 4px solid #ffc107;
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .security-notice i {
            color: #856404;
            margin-top: 2px;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .contact-item i {
            color: #8b4513;
            width: 16px;
        }

        /* Enhanced existing styles */
        .pin-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 450px;
            width: 90%;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            padding: 15px 25px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            flex: 1;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #8b4513;
            color: white;
        }

        .btn-primary:hover {
            background: #5a2d0c;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            font-weight: 600;
            min-height: 20px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="pin-container">
        <h1>🔒 Change PIN</h1>
        <p class="subtitle">Set a new 4-digit security PIN</p>

        <form id="changePinForm">
            <div class="form-group">
                <label for="currentPin">Current PIN</label>
                <div class="pin-input-group">
                    <input type="password" class="pin-input" maxlength="1" data-index="0">
                    <input type="password" class="pin-input" maxlength="1" data-index="1">
                    <input type="password" class="pin-input" maxlength="1" data-index="2">
                    <input type="password" class="pin-input" maxlength="1" data-index="3">
                </div>
                <input type="hidden" id="currentPin" name="current_pin">
            </div>

            <div class="form-group">
                <label for="newPin">New PIN</label>
                <div class="pin-input-group">
                    <input type="password" class="pin-input" maxlength="1" data-index="4">
                    <input type="password" class="pin-input" maxlength="1" data-index="5">
                    <input type="password" class="pin-input" maxlength="1" data-index="6">
                    <input type="password" class="pin-input" maxlength="1" data-index="7">
                </div>
                <input type="hidden" id="newPin" name="new_pin">
            </div>

            <div class="form-group">
                <label for="confirmPin">Confirm New PIN</label>
                <div class="pin-input-group">
                    <input type="password" class="pin-input" maxlength="1" data-index="8">
                    <input type="password" class="pin-input" maxlength="1" data-index="9">
                    <input type="password" class="pin-input" maxlength="1" data-index="10">
                    <input type="password" class="pin-input" maxlength="1" data-index="11">
                </div>
                <input type="hidden" id="confirmPin" name="confirm_pin">
            </div>

            <div class="button-group">
                <button type="button" class="btn btn-secondary"
                    onclick="window.location.href='/dashboard'">Cancel</button>
                <button type="submit" class="btn btn-primary">Change PIN</button>
            </div>
        </form>

        <!-- Forgot PIN Section - Much Cleaner -->
        <div class="forgot-pin-section">
            <div class="forgot-pin-header">
                <i class="fas fa-question-circle"></i>
                <span>Forgot PIN?</span>
            </div>
            <div class="forgot-pin-content">
                <p class="security-notice">
                    <i class="fas fa-shield-alt"></i>
                    For security reasons, please contact your system administrator to reset the PIN.
                </p>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>laurenceayo7@gmail.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>0993-688-1248</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="message" class="message"></div>
    </div>

    <script>
        const pinInputs = document.querySelectorAll('.pin-input');

        // Auto-focus and navigation between PIN inputs
        pinInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1) {
                    const nextIndex = parseInt(e.target.dataset.index) + 1;
                    if (nextIndex < pinInputs.length) {
                        pinInputs[nextIndex].focus();
                    }
                }

                updateHiddenFields();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value === '') {
                    const prevIndex = parseInt(e.target.dataset.index) - 1;
                    if (prevIndex >= 0) {
                        pinInputs[prevIndex].focus();
                    }
                }
            });
        });

        function updateHiddenFields() {
            // Current PIN (first 4 digits)
            const currentPin = Array.from(pinInputs)
                .slice(0, 4)
                .map(input => input.value)
                .join('');
            document.getElementById('currentPin').value = currentPin;

            // New PIN (next 4 digits)
            const newPin = Array.from(pinInputs)
                .slice(4, 8)
                .map(input => input.value)
                .join('');
            document.getElementById('newPin').value = newPin;

            // Confirm PIN (last 4 digits)
            const confirmPin = Array.from(pinInputs)
                .slice(8, 12)
                .map(input => input.value)
                .join('');
            document.getElementById('confirmPin').value = confirmPin;
        }

        // Form submission
        document.getElementById('changePinForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const currentPin = formData.get('current_pin');
            const newPin = formData.get('new_pin');
            const confirmPin = formData.get('confirm_pin');

            const messageEl = document.getElementById('message');
            messageEl.className = 'message';

            // Validation
            if (currentPin.length !== 4 || newPin.length !== 4 || confirmPin.length !== 4) {
                showMessage('Please complete all PIN fields', 'error');
                return;
            }

            if (newPin !== confirmPin) {
                showMessage('New PINs do not match', 'error');
                return;
            }

            if (newPin === currentPin) {
                showMessage('New PIN cannot be the same as current PIN', 'error');
                return;
            }

            // Submit to server
            fetch('<?php echo e(route("pin.change")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    current_pin: currentPin,
                    new_pin: newPin,
                    confirm_pin: confirmPin
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('PIN changed successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = data.redirect || '/dashboard';
                        }, 1500);
                    } else {
                        showMessage(data.message || 'Failed to change PIN', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Connection error', 'error');
                });
        });

        function showMessage(message, type) {
            const messageEl = document.getElementById('message');
            messageEl.textContent = message;
            messageEl.className = `message ${type}`;
        }
    </script>
</body>

</html><?php /**PATH C:\Users\Laurence Ayo\sip_and_serve_final\resources\views/auth/change-pin.blade.php ENDPATH**/ ?>