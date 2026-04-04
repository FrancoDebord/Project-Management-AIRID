/**
 * Service Worker — AIRID Project Tracking Sheet
 * Strategy: Cache-first for static assets, Network-first for HTML/API
 */

const CACHE_NAME   = 'airid-pms-v1';
const STATIC_CACHE = 'airid-static-v1';

// Static assets to pre-cache on install
const PRECACHE_URLS = [
    '/',
    '/offline.html',
];

// Patterns that should bypass the cache and always go to the network
const NETWORK_ONLY = [
    '/login',
    '/logout',
    '/project/',
    '/cpia/',
    '/admin/',
];

// ── Install ────────────────────────────────────────────────────────
self.addEventListener('install', function (event) {
    self.skipWaiting();
    event.waitUntil(
        caches.open(STATIC_CACHE).then(function (cache) {
            return cache.addAll(PRECACHE_URLS).catch(function () {
                // Ignore pre-cache failures (offline during install)
            });
        })
    );
});

// ── Activate — clean up old caches ────────────────────────────────
self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(
                keys.filter(k => k !== CACHE_NAME && k !== STATIC_CACHE)
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

    // CDN resources: cache-first
    if (url.origin.includes('cdn.jsdelivr') || url.origin.includes('cdnjs.cloudflare')) {
        event.respondWith(cacheFirst(event.request));
        return;
    }

    // Static assets (CSS, JS, images, fonts)
    if (/\.(css|js|woff2?|ttf|eot|png|jpg|jpeg|gif|svg|ico|webp)$/i.test(url.pathname)) {
        event.respondWith(cacheFirst(event.request));
        return;
    }

    // Network-only patterns (login, API writes)
    const isNetworkOnly = NETWORK_ONLY.some(p => url.pathname.startsWith(p));
    if (isNetworkOnly) {
        event.respondWith(networkOnly(event.request));
        return;
    }

    // HTML pages: network-first, fallback to cache, then offline page
    event.respondWith(networkFirst(event.request));
});

// ── Strategies ────────────────────────────────────────────────────

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;
    try {
        const response = await fetch(request);
        if (response && response.status === 200) {
            const cache = await caches.open(STATIC_CACHE);
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
        return offline || new Response('<h1>Hors ligne</h1><p>Vérifiez votre connexion.</p>', {
            headers: { 'Content-Type': 'text/html' },
        });
    }
}

async function networkOnly(request) {
    try {
        return await fetch(request);
    } catch {
        return new Response(JSON.stringify({ error: 'offline' }), {
            status: 503,
            headers: { 'Content-Type': 'application/json' },
        });
    }
}
