{{-- PWA Meta Tags --}}
<meta name="theme-color" content="#2563eb">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="POS System">

{{-- Icons --}}
<link rel="icon" type="image/png" sizes="32x32" href="/images/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/icons/favicon-16x16.png">
<link rel="apple-touch-icon" href="/images/icons/icon-180x180.png">

{{-- Manifest --}}
<link rel="manifest" href="{{ route('pwa.manifest') }}">

{{-- Preload critical resources --}}
<link rel="preload" href="{{ asset('css/filament.css') }}" as="style">
<link rel="preload" href="{{ asset('js/filament.js') }}" as="script">