// School Portal Service Worker for PWA
const CACHE_NAME = 'sala-portal-v1';
const STATIC_ASSETS = [
  '/',
  '/manifest.json',
  '/site.webmanifest',
  '/icon-192.png',
  '/icon-512.png',
  '/apple-touch-icon.png',
  '/images/logo.png'
];

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS).catch(() => {
        // Continue even if some optional asset fails
      });
    }).then(() => self.skipWaiting())
  );
});

// Activate Event - Clean old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch Event - Network First for APIs & Live data, Cache Fallback for static assets
self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  // Skip non-GET and sensitive endpoints (Auth, APIs, CSRF)
  if (request.method !== 'GET' || 
      url.pathname.startsWith('/api/') || 
      url.pathname.startsWith('/login') || 
      url.pathname.startsWith('/logout') || 
      url.pathname === '/csrf-token') {
    return;
  }

  // Network first strategy
  event.respondWith(
    fetch(request)
      .then((response) => {
        // Clone and cache successful static asset responses
        if (response && response.status === 200 && response.type === 'basic') {
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            // Only cache images, css, js, fonts
            if (url.pathname.match(/\.(png|jpg|jpeg|svg|css|js|woff2|woff|ttf)$/)) {
              cache.put(request, responseToCache);
            }
          });
        }
        return response;
      })
      .catch(() => {
        // Fallback to cache if network fails (offline support)
        return caches.match(request);
      })
  );
});
