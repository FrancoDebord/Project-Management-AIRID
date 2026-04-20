/**
 * Service Worker — AIRID Project Management System
 * Strategy: Cache-first for static assets, Network-first for HTML/API
 */

const CACHE_VERSION = 'v2';
const CACHE_NAME    = 'airid-pms-' + CACHE_VERSION;
const STATIC_CACHE  = 'airid-static-' + CACHE_VERSION;

// Static assets to pre-cache on install
const PRECACHE_URLS = [
    '/offline.html',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/apple-touch-icon.png',
    '/manifest.json',
];

// Patterns that should bypass the cache and always go to the network
const NETWORK_ONLY = [
    '/login',
    '/logout',
    '/ajax/',
    '/admin/',
];

// ── Install ────────────────────────────────────────────────────────
self.addEventListener('install', function (event) {
    self.skipWaiting();
    event.waitUntil(
        caches.open(STATIC_CACHE).then(function (cache) {
            return cache.addAll(PRECACHE_URLS).catch(function (err) {
                console.warn('[SW] Pre-cache partial failure:', err);
            });
        })
    );
});

// ── Activate — clean up old caches ────────────────────────────────
self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(
                keys
                    .filter(k => k !== CACHE_NAME && k !== STATIC_CACHE)
                    .map(k => caches.delete(k))
            );
        }).then(function () {
            return self.clients.claim();
        })
    );
});

// ── Fetch ─────────────────────────────────────────────────────────
self.addEventListener('fetch', function (event) {
    const url = new URL(event.request.url);

    // Only handle same-origin GET requests
    if (event.request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    // Network-only patterns (auth, AJAX writes)
    const isNetworkOnly = NETWORK_ONLY.some(p => url.pathname.startsWith(p));
    if (isNetworkOnly) {
        event.respondWith(networkOnly(event.request));
        return;
    }

    // CDN resources: cache-first
    if (url.origin.includes('cdn.jsdelivr') || url.origin.includes('cdnjs.cloudflare')) {
        event.respondWith(cacheFirst(event.request, STATIC_CACHE));
        return;
    }

    // Static assets (CSS, JS, images, fonts, icons)
    if (/\.(css|js|woff2?|ttf|eot|png|jpg|jpeg|gif|svg|ico|webp)$/i.test(url.pathname)) {
        event.respondWith(cacheFirst(event.request, STATIC_CACHE));
        return;
    }

    // HTML pages: network-first, fallback to cache, then offline page
    event.respondWith(networkFirst(event.request));
});

// ── Strategies ────────────────────────────────────────────────────

async function cacheFirst(request, cacheName) {
    const cached = await caches.match(request);
    if (cached) return cached;
    try {
        const response = await fetch(request);
        if (response && response.status === 200) {
            const cache = await caches.open(cacheName || STATIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('', { status: 503 });
    }
}

async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response && response.status === 200) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;
        const offline = await caches.match('/offline.html');
        return offline || new Response(
            '<!doctype html><html><body><h2>Hors ligne</h2><p>Vérifiez votre connexion.</p><a href="/">Réessayer</a></body></html>',
            { headers: { 'Content-Type': 'text/html' } }
        );
    }
}

async function networkOnly(request) {
    try {
        return await fetch(request);
    } catch {
        return new Response(JSON.stringify({ error: 'offline', code_erreur: 1 }), {
            status: 503,
            headers: { 'Content-Type': 'application/json' },
        });
    }
}
