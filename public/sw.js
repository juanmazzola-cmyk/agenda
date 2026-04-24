self.addEventListener('install', () => self.skipWaiting());

self.addEventListener('activate', (event) => {
    event.waitUntil(
        self.clients.claim().then(() => {
            return self.clients.matchAll({ type: 'window' });
        }).then(clients => {
            clients.forEach(client => client.navigate(client.url));
        })
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.mode === 'navigate') {
        event.respondWith(fetch(event.request, { cache: 'no-store' }));
    } else {
        event.respondWith(fetch(event.request));
    }
});
