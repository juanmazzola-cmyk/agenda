const CACHE = 'agenda-v15';
const STATIC = ['./manifest.json', './icon-192.png', './icon-512.png'];
const DYNAMIC = ['./', './index.html', './app.js'];

self.addEventListener('install', e => {
    e.waitUntil(caches.open(CACHE).then(c => c.addAll([...STATIC, ...DYNAMIC])));
    self.skipWaiting();
});

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', e => {
    const path = new URL(e.request.url).pathname;
    const isAppFile = path.endsWith('/') || path.endsWith('index.html') || path.endsWith('app.js');

    if (isAppFile) {
        // network-first: siempre trae la versión más nueva cuando hay red
        e.respondWith(
            fetch(e.request).then(res => {
                if (res.ok) {
                    const clone = res.clone();
                    caches.open(CACHE).then(c => c.put(e.request, clone));
                }
                return res;
            }).catch(() => caches.match(e.request))
        );
    } else {
        // cache-first para íconos y manifest
        e.respondWith(
            caches.match(e.request).then(cached => {
                if (cached) return cached;
                return fetch(e.request).then(res => {
                    if (res.ok) {
                        const clone = res.clone();
                        caches.open(CACHE).then(c => c.put(e.request, clone));
                    }
                    return res;
                }).catch(() => cached);
            })
        );
    }
});
