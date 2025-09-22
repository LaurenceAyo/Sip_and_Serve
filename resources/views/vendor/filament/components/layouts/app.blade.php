<x-filament::layouts.base :title="$title">
    {{-- PWA Meta Tags --}}
    <x-slot:head>
        @include('pwa.meta')
        
        {{-- Additional PWA specific CSS --}}
        <style>
            .pwa-install-banner {
                position: fixed;
                bottom: 20px;
                left: 20px;
                right: 20px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 15px;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                display: none;
                z-index: 9999;
            }
            
            .pwa-offline-indicator {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background: #f59e0b;
                color: white;
                padding: 8px;
                text-align: center;
                font-size: 14px;
                display: none;
                z-index: 9998;
            }
        </style>
    </x-slot:head>
    
    {{-- Main Content --}}
    <div class="filament-app-layout">
        {{-- Offline Indicator --}}
        <div id="offline-indicator" class="pwa-offline-indicator">
            📡 You're offline - Limited functionality available
        </div>
        
        {{-- Install Banner --}}
        <div id="pwa-install-banner" class="pwa-install-banner">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>📱 Install POS App</strong>
                    <br>
                    <small>Add to home screen for better experience</small>
                </div>
                <div>
                    <button id="pwa-install-btn" style="background: rgba(255,255,255,0.2); color: white; border: none; padding: 8px 12px; border-radius: 6px; margin-right: 10px;">
                        Install
                    </button>
                    <button id="pwa-dismiss-btn" style="background: transparent; color: white; border: 1px solid rgba(255,255,255,0.3); padding: 8px 12px; border-radius: 6px;">
                        Later
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Filament Content --}}
        {{ $slot }}
    </div>
    
    {{-- PWA JavaScript --}}
    <x-slot:scripts>
        @include('pwa.scripts')
    </x-slot:scripts>
</x-filament::layouts.base>