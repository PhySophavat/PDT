import { createI18n } from 'vue-i18n';
import km from './locales/km.json';
import en from './locales/en.json';

const savedLocale = localStorage.getItem('locale') || 'km';

const i18n = createI18n({
  legacy: false, // Composition API mode
  locale: savedLocale,
  fallbackLocale: 'en',
  messages: {
    km,
    en
  }
});

export const setLocale = (locale) => {
  i18n.global.locale.value = locale;
  localStorage.setItem('locale', locale);
  document.documentElement.lang = locale;
};

export const getLocale = () => {
  return i18n.global.locale.value;
};

export default i18n;
