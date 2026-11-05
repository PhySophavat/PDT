import { ref } from 'vue';

const toasts = ref([]);
let toastIdCounter = 0;

export function useToast() {
  const removeToast = (id) => {
    const idx = toasts.value.findIndex(t => t.id === id);
    if (idx !== -1) {
      toasts.value.splice(idx, 1);
    }
  };

  const showToast = (message, type = 'success', duration = 3000) => {
    const id = ++toastIdCounter;
    const toast = {
      id,
      message: String(message || ''),
      type, // 'success' | 'error' | 'warning' | 'info'
      duration,
      createdAt: Date.now()
    };

    toasts.value.push(toast);

    if (duration > 0) {
      setTimeout(() => {
        removeToast(id);
      }, duration);
    }

    return id;
  };

  return {
    toasts,
    showToast,
    removeToast,
    success: (msg, dur) => showToast(msg, 'success', dur),
    error: (msg, dur) => showToast(msg, 'error', dur),
    warning: (msg, dur) => showToast(msg, 'warning', dur),
    info: (msg, dur) => showToast(msg, 'info', dur),
  };
}

// Global instance for window / non-setup access
const globalToast = useToast();
export { globalToast };

if (typeof window !== 'undefined') {
  window.$toast = globalToast;
  window.showToast = globalToast.showToast;

  // Intercept window.alert so all legacy alert(...) calls become non-blocking toasts!
  window.alert = (message) => {
    const text = String(message || '');
    if (!text.trim()) return;

    let type = 'success';
    const lower = text.toLowerCase();
    if (
      text.includes('បរាជ័យ') || 
      text.includes('មិនអាច') || 
      text.includes('មានបញ្ហា') || 
      text.includes('កំហុស') || 
      lower.includes('error') || 
      lower.includes('failed') ||
      lower.includes('exception')
    ) {
      type = 'error';
    } else if (
      text.includes('សូម') || 
      text.includes('ប្រយ័ត្ន') || 
      lower.includes('warning')
    ) {
      type = 'warning';
    } else if (
      text.includes('បាន') || 
      text.includes('ជោគជ័យ') || 
      lower.includes('success') ||
      lower.includes('completed')
    ) {
      type = 'success';
    } else {
      type = 'info';
    }

    globalToast.showToast(text, type, 3000);
  };
}

