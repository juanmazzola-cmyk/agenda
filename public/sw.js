const CACHE = 'agenda-v1';

const PRECACHE = [
    '/',
    '/agenda',
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE).then(cache => cache.addAll(PRECACHE))
    );
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    // Solo manejamos GETs; dejamos pasar el resto (POST de Livewire, etc.)
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Assets compilados por Vite: cache-first (tienen hash en el nombre)
    if (url.pathname.startsWith('/build/')) {
        event.respondWith(
            caches.match(event.request).then(cached => {
                if (cached) return cached;
                return fetch(event.request).then(response => {
                    const clone = response.clone();
                    caches.open(CACHE).then(cache => cache.put(event.request, clone));
                    return response;
                });
            })
        );
        return;
    }

    // Páginas: network-first con fallback a caché
    event.respondWith(
        fetch(event.request)
            .then(response => {
                const clone = response.clone();
                caches.open(CACHE).then(cache => cache.put(event.request, clone));
                return response;
            })
            .catch(() => caches.match(event.request))
    );
});
