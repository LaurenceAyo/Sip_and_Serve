<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>L' PRIMERO CAFE - Select Mode</title>
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

        .cover-container {
            margin-top: 100px;
            opacity: 0.95;
            background-color: rgba(0, 0, 0, 0.35);
            padding: 2rem 1.5rem;
            border-radius: 20px;
            text-align: center;
            width: 55%;
            max-width: 50px;
            z-index: 1;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .tagline {
            font-size: 1rem;
            margin-bottom: 2rem;
            opacity: 0.8;
            font-style: italic;
        }

        .button-container {
            opacity: 0.75;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .mode-button {
            background-color: rgba(38, 162, 105, 0.9);
            color: white;
            font-size: 1.1rem;
            padding: 1.2rem 1.5rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.4rem;
            position: relative;
            overflow: hidden;
        }

        .mode-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .mode-button:hover::before {
            width: 300px;
            height: 300px;
        }

        .mode-button:hover {
            background-color: rgba(32, 136, 86, 0.95);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .button-icon {
            font-size: 2rem;
            z-index: 1;
        }

        .button-text {
            font-weight: bold;
            z-index: 1;
            font-size: 1.1rem;
        }

        .button-description {
            font-size: 0.85rem;
            opacity: 0.9;
            z-index: 1;
        }

        .footer-text {
            font-size: 0.85rem;
            opacity: 0.7;
            margin-top: 1.5rem;
        }

        /* Tablet-specific adjustments */
        @media (min-width: 600px) and (max-width: 1024px) {
            .cover-container {
                max-width: 450px;
                padding: 1.8rem 1.3rem;
            }

            h1 {
                font-size: 1.8rem;
            }

            .tagline {
                font-size: 0.95rem;
                margin-bottom: 1.8rem;
            }

            .mode-button {
                padding: 1rem 1.3rem;
                font-size: 1rem;
            }

            .button-icon {
                font-size: 1.8rem;
            }

            .button-text {
                font-size: 1rem;
            }

            .button-description {
                font-size: 0.8rem;
            }
        }

        /* Responsive design for desktop */
        @media (min-width: 1025px) {
            .cover-container {
                max-width: 550px;
                padding: 2.5rem 2rem;
            }

            h1 {
                font-size: 2.3rem;
            }

            .tagline {
                font-size: 1.1rem;
                margin-bottom: 2.5rem;
            }

            .button-container {
                flex-direction: row;
                gap: 1.5rem;
            }

            .mode-button {
                flex: 1;
                padding: 1.5rem 1.8rem;
            }

            .button-icon {
                font-size: 2.3rem;
            }
        }

        /* Mobile phones */
        @media (max-width: 480px) {
            .cover-container {
                padding: 1.5rem 1rem;
            }

            h1 {
                font-size: 1.6rem;
            }

            .tagline {
                font-size: 0.9rem;
                margin-bottom: 1.5rem;
            }

            .mode-button {
                font-size: 1rem;
                padding: 1rem 1.2rem;
            }

            .button-icon {
                font-size: 1.6rem;
            }

            .button-text {
                font-size: 1rem;
            }

            .button-description {
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <!-- Background slideshow -->
    <div class="background-slideshow">
        <div class="background-slide active"
            style="background-image: url('{{ asset('assets/background_main_2.jpg') }}');"></div>
        <div class="background-slide" style="background-image: url('{{ asset('assets/background_main_3.jpg') }}');">
        </div>
        <div class="background-slide" style="background-image: url('{{ asset('assets/background_main_4.jpg') }}');">
        </div>
    </div>

    <div class="cover-container">
        <h1>L' PRIMERO CAFE</h1>
        <div class="tagline">Brew better coffee, Brew coffee better</div>

        <div class="button-container">
            <!-- Staff/Admin Login -->
            <a href="{{ route('login') }}?from=staff" class="mode-button">
                <span class="button-icon">👤</span>
                <span class="button-text">Staff Login & POS System</span>
                <span class="button-description">(Staff Only)</span>
            </a>

            <!-- Kiosk Mode -->
            <a href="{{ route('kiosk.index') }}" class="mode-button">
                <span class="button-icon">🛒</span>
                <span class="button-text">Order Here</span>
                <span class="button-description">Self-service kiosk ordering</span>
            </a>
        </div>

        <div class="footer-text">
            Select your preferred mode to continue
        </div>
    </div>

    <script>
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

        // Change background every 10 seconds
        setInterval(changeBackground, 10000);
    </script>
</body>

</html>