import './bootstrap';
import './composables/useToast';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import i18n from './i18n';
import App from './App.vue';

try {
  const appMount = document.getElementById('app');
  if (appMount) {
    const app = createApp(App);
    app.use(createPinia());
    app.use(router);
    app.use(i18n);
    app.mount('#app');
  }
} catch (err) {
  console.error('Fatal Vue App mount error:', err);
}
