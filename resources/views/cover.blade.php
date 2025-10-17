<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>L' PRIMERO CAFE - Select Mode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        -webkit-tap-highlight-color: transparent;
    }

    body {
        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        color: #fff;
        position: relative;
        overflow: hidden;
        padding: 20px;
    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        transform: translateY(30%);
    }

    /* Background slideshow container */
    .background-slideshow {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        background-color: #c2a477;
    }

    .background-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        opacity: 0;
        transition: opacity 2s ease-in-out;
        display: none;
    }

    .background-slide.active {
        opacity: 1;
    }

    /* Show portrait images in portrait mode */
    @media (orientation: portrait) {
        .background-slide.portrait {
            display: block;
        }
        .background-slide.landscape {
            display: none !important;
        }
    }

    /* Show landscape images in landscape mode */
    @media (orientation: landscape) {
        .background-slide.portrait {
            display: none !important;
        }
        .background-slide.landscape {
            display: block;
        }
    }

    .background-slide::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom,
                rgba(0, 0, 0, 0.15) 0%,
                rgba(0, 0, 0, 0.25) 40%,
                rgba(0, 0, 0, 0.6) 100%);
    }

    .cover-container {
        background: rgba(0, 0, 0, 0.325);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        padding: 1.6rem 1.3rem;
        border-radius: 24px;
        text-align: center;
        width: 100%;
        max-width: 312px;
        z-index: 1;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transform: translateY(25%) translateX(-8%);
    }

    h1 {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
    }

    .tagline {
        font-size: 0.8rem;
        margin-bottom: 1.6rem;
        opacity: 0.95;
        font-style: italic;
        color: #f0e6d2;
        text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.8);
    }

    .button-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 1.3rem;
    }

    .mode-button {
        background: linear-gradient(135deg, rgba(38, 162, 105, 0.95) 0%, rgba(32, 136, 86, 0.95) 100%);
        color: white;
        font-size: 0.8rem;
        padding: 1.2rem 1.3rem;
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.4rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        touch-action: manipulation;
        user-select: none;
        -webkit-user-select: none;
    }

    .mode-button::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        transform: translate(-50%, -50%);
        transition: width 0.6s ease, height 0.6s ease;
    }

    .mode-button:active::before {
        width: 400px;
        height: 400px;
    }

    .mode-button:active {
        transform: scale(0.98);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
    }

    .mode-button:hover {
        background: linear-gradient(135deg, rgba(32, 136, 86, 1) 0%, rgba(25, 110, 70, 1) 100%);
        border-color: rgba(255, 255, 255, 0.4);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
    }

    .button-icon {
        font-size: 1.6rem;
        z-index: 1;
        filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.3));
    }

    .button-text {
        font-weight: 600;
        z-index: 1;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
    }

    .button-description {
        font-size: 0.65rem;
        opacity: 0.95;
        z-index: 1;
        color: rgba(255, 255, 255, 0.9);
    }

    .footer-text {
        font-size: 0.65rem;
        opacity: 0.9;
        margin-top: 1rem;
        color: #f0e6d2;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
    }

    /* Lenovo Xiaoxin Tablet optimizations (portrait) */
    @media (min-width: 768px) and (max-width: 1280px) and (orientation: portrait) {
        body {
            padding: 30px;
        }

        .cover-container {
            max-width: 450px;
            padding: 1.6rem 1.3rem;
            transform: translateY(35%) translateX(-2%);;
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .tagline {
            font-size: 0.8rem;
            margin-bottom: 1.6rem;
        }

        .button-container {
            gap: 1rem;
        }

        .mode-button {
            padding: 1.2rem 1.3rem;
            font-size: 0.8rem;
            border-radius: 16px;
        }

        .button-icon {
            font-size: 1.6rem;
        }

        .button-text {
            font-size: 0.85rem;
        }

        .button-description {
            font-size: 0.65rem;
        }

        .footer-text {
            font-size: 0.65rem;
            margin-top: 1rem;
        }
    }

    /* Tablet landscape mode */
    @media (min-width: 768px) and (max-width: 1366px) and (orientation: landscape) {
        body {
            padding: 30px;
        }

        .cover-container {
            max-width: 380px;
            padding: 1.6rem 1.4rem;
            transform: translateY(65%);
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .tagline {
            font-size: 0.75rem;
            margin-bottom: 1.3rem;
        }

        .button-container {
            flex-direction: row;
            gap: 1rem;
        }

        .mode-button {
            flex: 1;
            padding: 1.1rem 1rem;
            min-height: 140px;
            justify-content: center;
            font-size: 0.75rem;
        }

        .button-icon {
            font-size: 1.5rem;
        }

        .button-text {
            font-size: 0.8rem;
        }

        .button-description {
            font-size: 0.6rem;
        }

        .footer-text {
            font-size: 0.6rem;
            margin-top: 0.8rem;
        }
    }

    /* Large desktop */
    @media (min-width: 1367px) {
        .cover-container {
            max-width: 700px;
            padding: 3.5rem 3rem;
        }

        h1 {
            font-size: 3.2rem;
        }

        .tagline {
            font-size: 1.4rem;
            margin-bottom: 3rem;
        }

        .button-container {
            flex-direction: row;
            gap: 2rem;
        }

        .mode-button {
            flex: 1;
            padding: 2.5rem 2rem;
            min-height: 260px;
            justify-content: center;
        }

        .button-icon {
            font-size: 3.5rem;
        }

        .button-text {
            font-size: 1.4rem;
        }
    }

    /* Small tablets and large phones */
    @media (max-width: 767px) {
        body {
            padding: 15px;
        }

        .cover-container {
            padding: 2rem 1.5rem;
        }

        h1 {
            font-size: 2.2rem;
        }

        .tagline {
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .button-container {
            gap: 1.2rem;
        }

        .mode-button {
            padding: 1.5rem 1.5rem;
            font-size: 1.1rem;
        }

        .button-icon {
            font-size: 2.2rem;
        }

        .button-text {
            font-size: 1.1rem;
        }

        .button-description {
            font-size: 0.9rem;
        }

        .footer-text {
            font-size: 0.9rem;
        }
    }

    /* Small phones */
    @media (max-width: 480px) {
        .cover-container {
            padding: 1.8rem 1.2rem;
        }

        h1 {
            font-size: 1.9rem;
        }

        .tagline {
            font-size: 0.95rem;
            margin-bottom: 1.8rem;
        }

        .mode-button {
            padding: 1.4rem 1.2rem;
            font-size: 1rem;
        }

        .button-icon {
            font-size: 2rem;
        }

        .button-text {
            font-size: 1rem;
        }

        .button-description {
            font-size: 0.85rem;
        }
    }

    /* Animation for page load */
    @keyframes fadeInUp {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .cover-container {
        animation: fadeInUp 0.6s ease-out;
    }
</style>
</head>

<body>
    <!-- Background slideshow -->
    <div class="background-slideshow">
        <div class="background-slide active portrait"
            style="background-image: url('assets/background_main_2_portrait.jpg');"></div>
        <div class="background-slide active landscape"
            style="background-image: url('assets/background_main_2_landscape.jpg');"></div>

        <div class="background-slide portrait" style="background-image: url('assets/background_main_3_portrait.jpg');">
        </div>
        <div class="background-slide landscape"
            style="background-image: url('assets/background_main_3_landscape.jpg');"></div>

        <div class="background-slide portrait" style="background-image: url('assets/background_main_4_portrait.jpg');">
        </div>
        <div class="background-slide landscape"
            style="background-image: url('assets/background_main_4_landscape.jpg');"></div>
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
        // Background slideshow functionality with orientation support
        let currentSlide = 0;
        const totalSlides = 3; // Number of image sets

        function getActiveSlides() {
            // Get either portrait or landscape slides based on current orientation
            const isPortrait = window.matchMedia("(orientation: portrait)").matches;
            const orientation = isPortrait ? '.portrait' : '.landscape';
            return document.querySelectorAll('.background-slide' + orientation);
        }

        function changeBackground() {
            const slides = getActiveSlides();

            // Remove active class from current slide
            slides[currentSlide].classList.remove('active');

            // Move to next slide
            currentSlide = (currentSlide + 1) % totalSlides;

            // Add active class to new slide
            slides[currentSlide].classList.add('active');
        }

        // Change background every 8 seconds
        setInterval(changeBackground, 8000);

        // Handle orientation change
        window.addEventListener('orientationchange', function () {
            // Reset to first slide when orientation changes
            const allSlides = document.querySelectorAll('.background-slide');
            allSlides.forEach(slide => slide.classList.remove('active'));

            currentSlide = 0;
            const slides = getActiveSlides();
            slides[0].classList.add('active');
        });

        // Touch feedback for buttons
        const buttons = document.querySelectorAll('.mode-button');
        buttons.forEach(button => {
            button.addEventListener('touchstart', function () {
                this.style.transform = 'scale(0.98)';
            });

            button.addEventListener('touchend', function () {
                this.style.transform = '';
            });
        });

        // Prevent double-tap zoom on buttons
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
            const now = Date.now();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>
</body>

</html>