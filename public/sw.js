const CACHE_NAME = 'wisp-shell-v1';
const OFFLINE_URLS = [
  '/',
  '/login',
  '/manifest.json',
  '/img/logo.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(OFFLINE_URLS))
  );
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
  // Try network first, fallback to cache
  event.respondWith(
    fetch(event.request).then(resp => {
      if (!resp || resp.status !== 200 || resp.type === 'opaque') return resp;
      const clone = resp.clone();
      caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
      return resp;
    }).catch(() => caches.match(event.request).then(match => match || caches.match('/')))
  );
});
