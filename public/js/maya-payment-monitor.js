/**
 * Maya Payment Monitor
 * Real-time payment notification system for cashier interface
 * 
 * This script connects to Pusher/Laravel Echo to receive
 * real-time notifications when Maya payments are received
 */

(function() {
    'use strict';

    // Configuration
    const config = {
        notificationDuration: 5000, // 5 seconds
        soundEnabled: true,
        autoRefresh: true,
        refreshDelay: 2000 // 2 seconds after notification
    };

    // Initialize Pusher connection
    function initializePusher() {
        try {
            // Get Pusher credentials from meta tags or config
            const pusherKey = document.querySelector('meta[name="pusher-key"]')?.content;
            const pusherCluster = document.querySelector('meta[name="pusher-cluster"]')?.content || 'ap1';

            if (!pusherKey) {
                console.warn('Pusher key not found. Real-time notifications disabled.');
                return null;
            }

            const pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
                encrypted: true,
                authEndpoint: '/broadcasting/auth'
            });

            console.log('Pusher initialized successfully');
            return pusher;

        } catch (error) {
            console.error('Error initializing Pusher:', error);
            return null;
        }
    }

    // Subscribe to orders channel
    function subscribeToOrders(pusher) {
        if (!pusher) return;

        const channel = pusher.subscribe('orders');
        
        channel.bind('pusher:subscription_succeeded', function() {
            console.log('Successfully subscribed to orders channel');
        });

        channel.bind('pusher:subscription_error', function(error) {
            console.error('Subscription error:', error);
        });

        // Listen for payment-received event
        channel.bind('payment-received', function(data) {
            console.log('Payment received event:', data);
            handlePaymentReceived(data);
        });
    }

    // Handle payment received notification
    function handlePaymentReceived(data) {
        const order = data.order;
        
        console.log('Processing payment notification:', order);

        // Show notification
        showPaymentNotification(order);

        // Play notification sound
        if (config.soundEnabled) {
            playNotificationSound();
        }

        // Auto-refresh orders list
        if (config.autoRefresh) {
            setTimeout(() => {
                refreshOrdersList();
            }, config.refreshDelay);
        }
    }

    // Show notification toast
    function showPaymentNotification(order) {
        const notification = document.createElement('div');
        notification.className = 'maya-payment-notification';
        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-header">
                    <span class="notification-icon">💳</span>
                    <span class="notification-title">Maya Payment Received!</span>
                    <button class="notification-close" onclick="this.parentElement.parentElement.parentElement.remove()">×</button>
                </div>
                <div class="notification-body">
                    <p class="order-number">Order #${order.order_number}</p>
                    <p class="order-amount">₱${parseFloat(order.total_amount).toFixed(2)}</p>
                    <p class="order-status">✅ Payment Verified</p>
                </div>
            </div>
        `;

        // Add styles if not already present
        if (!document.getElementById('maya-notification-styles')) {
            addNotificationStyles();
        }

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => notification.classList.add('show'), 10);

        // Auto-remove after duration
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, config.notificationDuration);
    }

    // Add notification styles
    function addNotificationStyles() {
        const style = document.createElement('style');
        style.id = 'maya-notification-styles';
        style.textContent = `
            .maya-payment-notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background: white;
                border-radius: 12px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
                padding: 0;
                min-width: 320px;
                max-width: 400px;
                z-index: 10000;
                opacity: 0;
                transform: translateX(400px);
                transition: all 0.3s ease-out;
                overflow: hidden;
            }

            .maya-payment-notification.show {
                opacity: 1;
                transform: translateX(0);
            }

            .maya-payment-notification::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            }

            .notification-content {
                padding: 16px;
            }

            .notification-header {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 12px;
            }

            .notification-icon {
                font-size: 24px;
            }

            .notification-title {
                flex: 1;
                font-weight: 600;
                font-size: 16px;
                color: #2d3748;
            }

            .notification-close {
                background: none;
                border: none;
                font-size: 24px;
                color: #a0aec0;
                cursor: pointer;
                padding: 0;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 4px;
                transition: all 0.2s;
            }

            .notification-close:hover {
                background: #f7fafc;
                color: #2d3748;
            }

            .notification-body {
                padding-left: 34px;
            }

            .notification-body p {
                margin: 4px 0;
            }

            .order-number {
                font-size: 14px;
                color: #4a5568;
                font-weight: 500;
            }

            .order-amount {
                font-size: 20px;
                font-weight: 700;
                color: #667eea;
                margin: 8px 0;
            }

            .order-status {
                font-size: 13px;
                color: #48bb78;
                font-weight: 600;
            }

            @media (max-width: 480px) {
                .maya-payment-notification {
                    right: 10px;
                    left: 10px;
                    min-width: auto;
                    max-width: none;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Play notification sound
    function playNotificationSound() {
        try {
            // Create audio context for notification sound
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            // Configure sound
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            gainNode.gain.value = 0.3;

            // Play sound
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);

            // Second beep
            setTimeout(() => {
                const oscillator2 = audioContext.createOscillator();
                const gainNode2 = audioContext.createGain();
                
                oscillator2.connect(gainNode2);
                gainNode2.connect(audioContext.destination);
                
                oscillator2.frequency.value = 1000;
                oscillator2.type = 'sine';
                gainNode2.gain.value = 0.3;
                
                oscillator2.start(audioContext.currentTime);
                oscillator2.stop(audioContext.currentTime + 0.1);
            }, 150);

        } catch (error) {
            console.warn('Could not play notification sound:', error);
        }
    }

    // Refresh orders list
    function refreshOrdersList() {
        // Check if autoRefreshOrders function exists (from cashier page)
        if (typeof window.autoRefreshOrders === 'function') {
            console.log('Auto-refreshing orders list...');
            window.autoRefreshOrders();
        } else {
            // Fallback: reload page
            console.log('Reloading page to show new order...');
            window.location.reload();
        }
    }

    // Initialize on page load
    function init() {
        console.log('Maya Payment Monitor initializing...');
        
        const pusher = initializePusher();
        if (pusher) {
            subscribeToOrders(pusher);
            console.log('Maya Payment Monitor ready');
        } else {
            console.warn('Maya Payment Monitor could not start - Pusher not available');
        }
    }

    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Export functions for external use
    window.MayaPaymentMonitor = {
        showNotification: showPaymentNotification,
        playSound: playNotificationSound,
        refresh: refreshOrdersList,
        config: config
    };

})();