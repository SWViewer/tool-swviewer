const FILES_TO_CACHE = [
    'offline.html',
    'index.php',
    'css/variables.css',
    'css/base.css',
    'css/modules.css',
    'css/swv-raw.css',
    'js/swv.js'
];

const CACHE_NAME = 'SWV-cache';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[ServiceWorker] Pre-caching offline page.');
            return cache.addAll(FILES_TO_CACHE);
        })
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keyList) => {
            return Promise.all(keyList.map((key) => {
                if (key !== CACHE_NAME) {
                    console.log('[ServiceWorker] Removing old cache', key);
                    return caches.delete(key);
                }
            }));
        })
    )
});

self.addEventListener('fetch', (event) => {
    if (event.request.destination !== "document" || event.request.mode !== 'navigate') {
        return;
    }
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.open(CACHE_NAME)
                .then((cache) => {
                    return cache.match('offline.html');
                });
            })
    );
});