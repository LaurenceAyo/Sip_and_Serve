<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - L' Primero Cafe</title>
</head>

<body>
    <style>
        body {
            background: linear-gradient(135deg, #8B4513, #A0522D, #CD853F);
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .contact-container {
            max-width: 500px;
            margin: 40px auto;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .contact-content {
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .cafe-title {
            font-size: 32px;
            font-weight: bold;
            color: white;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            letter-spacing: 2px;
        }

        .welcome-text {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 30px;
            font-weight: 300;
        }

        .image-section {
            margin-bottom: 30px;
        }

        .contact-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center 80%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            margin: 0 auto;
        }

        .contact-title {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 25px;
            font-weight: 400;
            line-height: 1.5;
        }

        .contact-item {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 16px;
            color: white;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .contact-icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            opacity: 0.9;
            fill: #4CAF50;
        }

        .contact-text {
            font-weight: 500;
        }

        .email-section {
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .email-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }

        .email-content {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .email-icon {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            fill: #4CAF50;
        }

        .email-address {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
        }

        .email-address:hover {
            text-decoration: underline;
            color: #66BB6A;
        }

        .back-to-login {
            text-align: center;
            margin-top: 30px;
            padding-bottom: 20px;
        }

        .back-to-login a {
            display: inline-block;
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            padding: 12px 40px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .back-to-login a:hover {
            background: linear-gradient(45deg, #45a049, #4CAF50);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        /* Portrait tablet optimizations */
        @media (max-width: 768px) and (orientation: portrait) {
            .contact-container {
                margin: 20px;
                max-width: none;
            }

            .contact-content {
                padding: 30px 20px;
            }

            .cafe-title {
                font-size: 28px;
            }

            .contact-image {
                width: 100px;
                height: 100px;
            }
        }

        @media (max-width: 480px) {
            .cafe-title {
                font-size: 24px;
            }

            .welcome-text {
                font-size: 16px;
            }

            .contact-item {
                font-size: 14px;
            }
        }

        .back-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        .contact-container {
            position: relative;
            /* Add this line to existing .contact-container */
        }
    </style>

    <div>
        <div class="contact-container">
            <a href="javascript:history.back()" class="back-button">← Back</a>
            <div class="contact-content">


                <div class="image-section">
                    <img src="{{ asset('images/LaurenceAyo_admin1.png') }}" alt="Admin Profile" class="contact-image">
                </div>

                <div class="contact-title">
                    For any concerns regarding this POS system kindly contact us at:
                </div>

                <div class="contact-item">
                    <svg class="contact-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                    </svg>
                    <span class="contact-text">0993-688-1248</span>
                </div>

                <div class="contact-item">
                    <svg class="contact-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                    </svg>
                    <span class="contact-text">0912-231-5838</span>
                </div>

                <div class="email-section">
                    <span class="email-label">Email:</span>
                    <div class="email-content">
                        <svg class="email-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                        </svg>
                        <a href="https://mail.google.com/mail/u/0/#inbox?compose=new&to=laurenceayo7@gmail.com"
                            target="_blank" class="email-address">laurenceayo7@gmail.com</a>
                    </div>
                </div>

                @guest
                    <div class="back-to-login">
                        <a href="http://127.0.0.1:8000/#">Back to Login</a>
                    </div>
                @endguest
            </div>
        </div>
</body>

</html>