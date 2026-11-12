// School Portal High-Performance Service Worker for PWA (v4)
const CACHE_NAME = 'sala-portal-v4';

// Core assets to pre-cache immediately on install for instant offline shell
const PRECACHE_ASSETS = [
  '/manifest.json',
  '/images/icon-192.png',
  '/images/icon-512.png',
  '/images/apple-touch-icon.png',
  '/images/logo.png',
  '/images/moeys-logo.png'
];

// Install Event - Pre-cache core shell assets & activate immediately
self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(PRECACHE_ASSETS).catch(() => {});
    })
  );
});

// Activate Event - Clean old caches & claim clients immediately
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    }).then(() => self.clients.claim())
  );
});

// Message Event - Force skip waiting & clear caches on client request
self.addEventListener('message', (event) => {
  if (event.data && (event.data.type === 'SKIP_WAITING' || event.data.type === 'CLEAR_CACHE')) {
    self.skipWaiting();
    caches.keys().then((keys) => {
      return Promise.all(keys.map((key) => caches.delete(key)));
    });
  }
});

// Fetch Event - Instant Load Strategy
self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  // 1. ALWAYS BYPASS for non-GET, API, and Authentication endpoints (Never cache login/logout/portal)
  if (
    request.method !== 'GET' ||
    url.pathname.startsWith('/api/') ||
    url.pathname.startsWith('/login') ||
    url.pathname.startsWith('/logout') ||
    url.pathname.startsWith('/portal') ||
    url.pathname.startsWith('/dashboard') ||
    url.pathname.startsWith('/s') ||
    url.pathname.startsWith('/portal-sala-access') ||
    url.pathname.startsWith('/forgot-password') ||
    url.pathname.startsWith('/reset-password') ||
    url.pathname.startsWith('/verify-otp') ||
    url.pathname === '/csrf-token' ||
    url.pathname === '/up' ||
    url.pathname === '/health-db'
  ) {
    return;
  }

  // 2. CACHE-FIRST for Immutable Vite Bundles (/build/assets/*) & Static Fonts/Images
  // Vite assets have content hashes in their filename; they are completely immutable!
  const isViteAsset = url.pathname.startsWith('/build/assets/');
  const isStaticImage = url.pathname.match(/\.(png|jpg|jpeg|svg|webp|ico)$/i);
  const isFont = url.pathname.match(/\.(woff2|woff|ttf|otf)$/i) || url.hostname.includes('fonts.gstatic.com');
  const isCdn = url.hostname.includes('cdnjs.cloudflare.com') || url.hostname.includes('fonts.googleapis.com');

  if (isViteAsset || isStaticImage || isFont || isCdn) {
    event.respondWith(
      caches.match(request).then((cachedResponse) => {
        if (cachedResponse) {
          // If it's not an immutable Vite asset, revalidate in background
          if (!isViteAsset) {
            fetch(request).then((networkResponse) => {
              if (networkResponse && networkResponse.status === 200) {
                caches.open(CACHE_NAME).then((cache) => cache.put(request, networkResponse));
              }
            }).catch(() => {});
          }
          return cachedResponse;
        }

        // If not in cache, fetch from network and cache for next time
        return fetch(request).then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(request, responseClone);
            });
          }
          return networkResponse;
        });
      })
    );
    return;
  }

  // 3. NETWORK-FIRST for HTML Navigation with Fast Cache Fallback
  if (request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(
      fetch(request)
        .then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
          }
          return networkResponse;
        })
        .catch(() => {
          return caches.match(request).then((cachedHtml) => {
            return cachedHtml || caches.match('/');
          });
        })
    );
    return;
  }
});

// Push Notification Event (Background Push when app is closed)
self.addEventListener('push', (event) => {
  let data = { title: 'ដំណឹងថ្មីពីសាលារៀន', body: 'អ្នកមានសារជូនដំណឹងថ្មីមួយ។', url: '/' };
  if (event.data) {
    try {
      data = Object.assign(data, event.data.json());
    } catch (e) {
      data.body = event.data.text();
    }
  }

  const options = {
    body: data.body,
    icon: '/images/icon-192.png',
    badge: '/images/icon-192.png',
    vibrate: [100, 50, 100],
    data: {
      url: data.url || '/'
    }
  };

  if ('setAppBadge' in self.navigator && typeof data.badgeCount === 'number') {
    self.navigator.setAppBadge(data.badgeCount).catch(() => {});
  }

  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

// Notification Click Event (Opens the app or navigates to the URL)
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const targetUrl = event.notification.data?.url || '/';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
      for (let client of windowClients) {
        if (client.url.includes(targetUrl) && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});
