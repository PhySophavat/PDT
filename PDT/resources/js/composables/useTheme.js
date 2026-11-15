import { ref, computed } from 'vue';

// Theme modes: 'system' | 'light' | 'dark'
const STORAGE_KEY = 'pdt_theme';

const getInitialTheme = () => {
  if (typeof window === 'undefined') return 'system';
  try {
    return localStorage.getItem(STORAGE_KEY) || 'system';
  } catch (e) {
    return 'system';
  }
};

const themeMode = ref(getInitialTheme());
const isDark = ref(false);

const mediaQuery = typeof window !== 'undefined' && window.matchMedia 
  ? window.matchMedia('(prefers-color-scheme: dark)') 
  : null;

function computeIsDark(mode) {
  if (mode === 'dark') return true;
  if (mode === 'light') return false;
  return mediaQuery ? mediaQuery.matches : false;
}

function applyHtmlClass(dark) {
  isDark.value = dark;
  if (typeof document === 'undefined') return;
  
  const root = document.documentElement;
  if (dark) {
    root.classList.add('dark');
    root.setAttribute('data-theme', 'dark');
  } else {
    root.classList.remove('dark');
    root.setAttribute('data-theme', 'light');
  }
}

export function setThemeMode(mode) {
  themeMode.value = mode;
  try {
    localStorage.setItem(STORAGE_KEY, mode);
  } catch (e) {
    console.error('Failed to save theme in localStorage:', e);
  }
  const dark = computeIsDark(mode);
  applyHtmlClass(dark);
}

export function toggleDeviceSettings(enable) {
  if (enable) {
    setThemeMode('system');
  } else {
    // Pin to current active theme (light or dark)
    const currentActive = isDark.value ? 'dark' : 'light';
    setThemeMode(currentActive);
  }
}

// Listen to system changes in real time
if (mediaQuery) {
  const handleMediaChange = (e) => {
    if (themeMode.value === 'system') {
      applyHtmlClass(e.matches);
    }
  };

  if (typeof mediaQuery.addEventListener === 'function') {
    mediaQuery.addEventListener('change', handleMediaChange);
  } else if (typeof mediaQuery.addListener === 'function') {
    mediaQuery.addListener(handleMediaChange);
  }
}

// Initial theme application
if (typeof window !== 'undefined') {
  applyHtmlClass(computeIsDark(themeMode.value));
}

export function useTheme() {
  const useDeviceSettings = computed(() => themeMode.value === 'system');

  // The active radio indicator (either 'light' or 'dark')
  // Even when 'Use device settings' is on, it reflects the system theme!
  const activeSelection = computed(() => (isDark.value ? 'dark' : 'light'));

  return {
    themeMode,
    isDark,
    useDeviceSettings,
    activeSelection,
    setThemeMode,
    toggleDeviceSettings
  };
}
