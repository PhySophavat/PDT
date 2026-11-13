import axios from 'axios';
import { ref } from 'vue';

export const isUpdateAvailable = ref(false);
export const newVersionInfo = ref(null);
export const isUpdatingNow = ref(false);

let currentVersion = typeof window !== 'undefined' && window.__APP_VERSION__ 
  ? window.__APP_VERSION__ 
  : null;
let currentDataHash = null;

let isChecking = false;
let checkInterval = null;
let lastCheckTime = 0;

/**
 * Clean up all service worker caches and session storage
 */
export async function clearAllCaches() {
  try {
    if (typeof window !== 'undefined') {
      try {
        localStorage.removeItem('pdt_cached_dashboard');
      } catch (e) {}
      if ('caches' in window) {
        const keys = await window.caches.keys();
        await Promise.all(keys.map(k => window.caches.delete(k)));
      }
    }
  } catch (e) {
    console.warn('Cache clearing error:', e);
  }
}

const LAST_RELOAD_KEY = 'pdt_last_auto_reload_ts';

/**
 * Trigger smooth reload to load latest version
 */
export async function applyUpdateNow() {
  if (isUpdatingNow.value) return;

  // Safeguard: Prevent reload loop within 30 seconds
  try {
    const lastReload = sessionStorage.getItem(LAST_RELOAD_KEY);
    if (lastReload && Date.now() - parseInt(lastReload, 10) < 30000) {
      console.warn('[AutoUpdater] Loop blocked: already updated less than 30s ago.');
      isUpdateAvailable.value = false;
      return;
    }
    sessionStorage.setItem(LAST_RELOAD_KEY, Date.now().toString());
  } catch (e) {}

  isUpdatingNow.value = true;

  try {
    await clearAllCaches();
  } catch (e) {}

  // Reload from server bypassing browser cache
  setTimeout(() => {
    window.location.reload(true);
  }, 200);
}

/**
 * Check if the user is currently typing in an input or textarea
 */
function isUserActivelyEditing() {
  if (typeof document === 'undefined') return false;
  const activeEl = document.activeElement;
  if (!activeEl) return false;
  const tag = (activeEl.tagName || '').toUpperCase();
  if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') {
    return true;
  }
  return Boolean(activeEl.isContentEditable);
}

/**
 * Check for new build version from server
 */
export async function checkForAppUpdate(silent = true) {
  if (typeof window === 'undefined') return;
  if (isChecking) return;

  const now = Date.now();
  // Throttle checks to at most once every 15 seconds
  if (now - lastCheckTime < 15000 && silent) return;
  lastCheckTime = now;

  isChecking = true;
  try {
    const res = await axios.get('/api/app-version', {
      params: { _t: now },
      headers: {
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache'
      }
    });

    if (res.data && res.data.version) {
      const latestVersion = res.data.version;

      // If this is our very first version initialization
      if (!currentVersion) {
        currentVersion = latestVersion;
        return;
      }

      // If versions match -> everything is up to date!
      if (latestVersion === currentVersion) {
        isUpdateAvailable.value = false;
        return;
      }

      // If server version is DIFFERENT from running client version -> NEW DEPLOYMENT DETECTED!
      if (latestVersion !== currentVersion) {
        console.log(`[AutoUpdater] New deployment detected: ${latestVersion} (current: ${currentVersion})`);
        newVersionInfo.value = res.data;
        isUpdateAvailable.value = true;

        window.dispatchEvent(new CustomEvent('pdt-app-update-available', {
          detail: { currentVersion, latestVersion, resData: res.data }
        }));

        // If user is NOT editing, auto-reload once smoothly
        if (!isUserActivelyEditing()) {
          console.log('[AutoUpdater] Applying new deployment update in 2s...');
          setTimeout(() => {
            applyUpdateNow();
          }, 2000);
        }
      }
    }
  } catch (err) {
    // Fail silently on network offline
  } finally {
    isChecking = false;
  }
}

/**
 * Initialize background auto-updater service
 */
export function initAutoUpdater(router) {
  if (typeof window === 'undefined') return;

  // 1. Initial check 5s after app mounts
  setTimeout(() => {
    checkForAppUpdate();
  }, 5000);

  // 2. Periodic background check every 30 seconds for new deployments
  if (checkInterval) clearInterval(checkInterval);
  checkInterval = setInterval(() => {
    if (document.visibilityState === 'visible') {
      checkForAppUpdate();
    }
  }, 30000);

  // 3. Check immediately when user switches back to this tab
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
      checkForAppUpdate();
    }
  });

  // 4. Check on route change
  if (router && typeof router.afterEach === 'function') {
    router.afterEach(() => {
      // Delay slightly so route transition finishes first
      setTimeout(() => {
        checkForAppUpdate();
      }, 500);
    });
  }

  // 5. Clean up stale retry tags
  try {
    sessionStorage.removeItem('chunk_retry_' + window.location.pathname);
  } catch (e) {}
}

export default {
  isUpdateAvailable,
  newVersionInfo,
  isUpdatingNow,
  checkForAppUpdate,
  applyUpdateNow,
  clearAllCaches,
  initAutoUpdater
};

