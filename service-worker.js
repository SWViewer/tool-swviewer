const FILES_TO_CACHE = [
    'offline.html',
    'css/base/variables.css',
    'css/base/fonts.css',
    'css/base/base.css',
    'css/components/comp.css',
    'css/components/dialog.css',
    'css/components/header.css',
    'css/components/pw-po.css',
    'css/layouts/logs.css',
    'css/layouts/talk.css',
    'css/index.css',
    'js/swv.js',
    'js/index-noncritical.js',
    'js/modules/bakeEl.min.js',
    'js/frame/frameKeys.js'
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