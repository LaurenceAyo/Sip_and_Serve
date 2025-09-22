<script>
class FilamentPWA {
    constructor() {
        this.installPrompt = null;
        this.isOnline = navigator.onLine;
        this.init();
    }
    
    init() {
        this.registerServiceWorker();
        this.setupInstallPrompt();
        this.setupOfflineDetection();
        this.setupFilamentIntegration();
    }
    
    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                console.log('SW registered:', registration);
                
                // Handle updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.showUpdateNotification();
                        }
                    });
                });
            } catch (error) {
                console.error('SW registration failed:', error);
            }
        }
    }
    
    setupInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.installPrompt = e;
            this.showInstallBanner();
        });
        
        document.getElementById('pwa-install-btn')?.addEventListener('click', () => {
            this.promptInstall();
        });
        
        document.getElementById('pwa-dismiss-btn')?.addEventListener('click', () => {
            this.hideInstallBanner();
        });
    }
    
    setupOfflineDetection() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.hideOfflineIndicator();
            // Sync offline data
            this.syncOfflineData();
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.showOfflineIndicator();
        });
        
        // Initial state
        if (!this.isOnline) {
            this.showOfflineIndicator();
        }
    }
    
    setupFilamentIntegration() {
        // Intercept Filament form submissions for offline handling
        document.addEventListener('submit', (e) => {
            if (!this.isOnline && e.target.closest('.filament-form')) {
                e.preventDefault();
                this.handleOfflineFormSubmission(e.target);
            }
        });
        
        // Add PWA-specific styling to Filament components
        this.enhanceFilamentForPWA();
    }
    
    showInstallBanner() {
        document.getElementById('pwa-install-banner').style.display = 'block';
    }
    
    hideInstallBanner() {
        document.getElementById('pwa-install-banner').style.display = 'none';
    }
    
    async promptInstall() {
        if (this.installPrompt) {
            this.installPrompt.prompt();
            const result = await this.installPrompt.userChoice;
            
            if (result.outcome === 'accepted') {
                console.log('PWA installed');
            }
            
            this.installPrompt = null;
            this.hideInstallBanner();
        }
    }
    
    showOfflineIndicator() {
        document.getElementById('offline-indicator').style.display = 'block';
    }
    
    hideOfflineIndicator() {
        document.getElementById('offline-indicator').style.display = 'none';
    }
    
    showUpdateNotification() {
        // Use Filament's notification system if available
        if (window.filament) {
            window.filament.notify('info', 'App update available! Refresh to update.');
        } else {
            alert('App update available! Please refresh the page.');
        }
    }
    
    async handleOfflineFormSubmission(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Store in IndexedDB for later sync
        await this.storeOfflineData('form_submissions', {
            url: form.action,
            method: form.method,
            data: data,
            timestamp: Date.now()
        });
        
        // Show offline message using Filament notification
        if (window.filament) {
            window.filament.notify('warning', 'Saved offline. Will sync when connection is restored.');
        }
    }
    
    async storeOfflineData(store, data) {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open('filament-pos-db', 1);
            
            request.onerror = () => reject(request.error);
            
            request.onsuccess = () => {
                const db = request.result;
                const transaction = db.transaction([store], 'readwrite');
                const objectStore = transaction.objectStore(store);
                
                data.id = Date.now() + Math.random();
                const addRequest = objectStore.add(data);
                
                addRequest.onsuccess = () => resolve(data.id);
                addRequest.onerror = () => reject(addRequest.error);
            };
            
            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                
                if (!db.objectStoreNames.contains('form_submissions')) {
                    db.createObjectStore('form_submissions', { keyPath: 'id' });
                }
                
                if (!db.objectStoreNames.contains('sales_data')) {
                    db.createObjectStore('sales_data', { keyPath: 'id' });
                }
            };
        });
    }
    
    async syncOfflineData() {
        // Sync offline form submissions
        const offlineSubmissions = await this.getOfflineData('form_submissions');
        
        for (const submission of offlineSubmissions) {
            try {
                const response = await fetch(submission.url, {
                    method: submission.method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(submission.data)
                });
                
                if (response.ok) {
                    await this.removeOfflineData('form_submissions', submission.id);
                }
            } catch (error) {
                console.error('Sync failed for submission:', submission.id);
            }
        }
        
        if (offlineSubmissions.length > 0) {
            window.filament?.notify('success', `Synced ${offlineSubmissions.length} offline submissions.`);
        }
    }
    
    async getOfflineData(store) {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open('filament-pos-db', 1);
            
            request.onsuccess = () => {
                const db = request.result;
                const transaction = db.transaction([store], 'readonly');
                const objectStore = transaction.objectStore(store);
                const getAll = objectStore.getAll();
                
                getAll.onsuccess = () => resolve(getAll.result);
                getAll.onerror = () => reject(getAll.error);
            };
        });
    }
    
    async removeOfflineData(store, id) {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open('filament-pos-db', 1);
            
            request.onsuccess = () => {
                const db = request.result;
                const transaction = db.transaction([store], 'readwrite');
                const objectStore = transaction.objectStore(store);
                const deleteRequest = objectStore.delete(id);
                
                deleteRequest.onsuccess = () => resolve();
                deleteRequest.onerror = () => reject(deleteRequest.error);
            };
        });
    }
    
    enhanceFilamentForPWA() {
        // Add touch-friendly improvements
        document.body.style.touchAction = 'manipulation';
        
        // Enhance Filament tables for mobile
        document.querySelectorAll('.filament-tables-table').forEach(table => {
            table.style.fontSize = '14px';
            table.style.minWidth = '100%';
        });
        
        // Add haptic feedback for buttons (if supported)
        document.querySelectorAll('.filament-button').forEach(button => {
            button.addEventListener('click', () => {
                if ('vibrate' in navigator) {
                    navigator.vibrate(50);
                }
            });
        });
    }
}

// Initialize PWA when page loads
document.addEventListener('DOMContentLoaded', () => {
    window.filamentPWA = new FilamentPWA();
});

// Livewire compatibility
document.addEventListener('livewire:load', () => {
    if (window.filamentPWA) {
        window.filamentPWA.setupFilamentIntegration();
    }
});
</script>