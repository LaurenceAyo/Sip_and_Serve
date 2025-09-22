const CACHE_NAME = 'laravel-filament-pos-v1.0.0';
const STATIC_CACHE = 'static-cache-v1.0.0';
const DYNAMIC_CACHE = 'dynamic-cache-v1.0.0';

// Laravel/Filament specific assets to cache
const STATIC_ASSETS = [
    '/admin',
    '/css/filament.css',
    '/js/filament.js',
    '/offline',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png'
];

// API routes that should work offline
const OFFLINE_FALLBACKS = {
    '/admin/api/products': '/offline-data/products.json',
    '/admin/api/customers': '/offline-data/customers.json'
};

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cache => {
                        if (cache !== STATIC_CACHE && cache !== DYNAMIC_CACHE) {
                            return caches.delete(cache);
                        }
                    })
                );
            })
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') return;
    
    // Handle admin panel routes
    if (url.pathname.startsWith('/admin')) {
        event.respondWith(handleAdminRoute(request));
        return;
    }
    
    // Handle API requests
    if (url.pathname.includes('/api/')) {
        event.respondWith(handleAPIRequest(request));
        return;
    }
    
    // Default caching strategy
    event.respondWith(
        caches.match(request)
            .then(response => response || fetch(request))
            .catch(() => caches.match('/offline'))
    );
});

async function handleAdminRoute(request) {
    try {
        // Try network first for admin routes
        const networkResponse = await fetch(request);
        
        // Cache successful responses
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        // Fallback to cache
        const cachedResponse = await caches.match(request);
        return cachedResponse || caches.match('/offline');
    }
}

async function handleAPIRequest(request) {
    const url = new URL(request.url);
    
    try {
        const networkResponse = await fetch(request);
        
        // Cache API responses for offline use
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        // Check for offline fallbacks
        const fallbackPath = OFFLINE_FALLBACKS[url.pathname];
        if (fallbackPath) {
            return caches.match(fallbackPath);
        }
        
        // Try cached version
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return offline API response
        return new Response(JSON.stringify({
            message: 'Offline - cached data may be available',
            offline: true
        }), {
            status: 503,
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

// Background sync for Laravel queues
self.addEventListener('sync', event => {
    if (event.tag === 'laravel-queue-sync') {
        event.waitUntil(syncLaravelQueue());
    }
});

async function syncLaravelQueue() {
    // Sync offline Laravel operations
    try {
        await fetch('/admin/api/sync-offline', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });
    } catch (error) {
        console.error('Laravel queue sync failed:', error);
    }
}