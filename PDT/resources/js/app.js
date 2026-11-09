import './bootstrap';
import './composables/useToast';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import i18n from './i18n';
import App from './App.vue';
import { initAutoUpdater } from './services/autoUpdater';

try {
  const appMount = document.getElementById('app');
  if (appMount) {
    const app = createApp(App);
    app.use(createPinia());
    app.use(router);
    app.use(i18n);
    app.mount('#app');

    // Start background Auto-Updater for Production deployments
    initAutoUpdater(router);
  }
} catch (err) {
  console.error('Fatal Vue App mount error:', err);
}
