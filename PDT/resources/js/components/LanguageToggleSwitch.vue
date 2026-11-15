<template>
  <!-- Sub-item Mode (Inside sidebar dropdown, e.g. "គណនី និងការកំណត់") -->
  <div 
    v-if="isSubItem"
    class="flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-neutral-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-neutral-800 transition-all select-none group"
  >
    <!-- Left: Translate / Globe Icon + Label 'ភាសា (Language)' -->
    <div class="flex items-center gap-2.5 min-w-0 pr-2">
      <svg 
        class="w-4 h-4 text-slate-400 dark:text-neutral-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors shrink-0" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
        aria-hidden="true"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          stroke-width="1.8" 
          d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9c-1.636-2.92-3.116-6.023-4.088-9m11.588 5h-7.176m7.176 0a17.986 17.986 0 01-2.088 5.5m2.088-5.5a18.04 18.04 0 00-2.088-5.5m-5.088 0h7.176" 
        />
      </svg>
      <span class="truncate">
        {{ currentLocale === 'km' ? 'ភាសា (Language)' : 'Language (ភាសា)' }}
      </span>
    </div>

    <!-- Right: Compact Pill-Shaped Toggle Switch ('ខ្មែរ' / 'EN') -->
    <div 
      class="relative inline-flex items-center p-0.5 bg-slate-100 dark:bg-neutral-800 rounded-full border border-slate-200/80 dark:border-neutral-700 w-[78px] h-6 shrink-0 select-none overflow-hidden"
      role="radiogroup"
      aria-label="Language selection"
    >
      <!-- Smoothly sliding toggle thumb -->
      <span 
        class="absolute top-0.5 bottom-0.5 left-0.5 w-[calc(50%-2px)] rounded-full bg-white dark:bg-neutral-700 shadow-xs border border-slate-200/50 dark:border-neutral-600 transition-transform duration-200 ease-out pointer-events-none"
        :class="currentLocale === 'en' ? 'translate-x-full' : 'translate-x-0'"
      ></span>

      <!-- ខ្មែរ Option Button -->
      <button 
        type="button"
        @click.stop="handleSelect('km')"
        class="relative z-10 w-1/2 h-full flex items-center justify-center text-[11px] font-bold rounded-full transition-colors duration-150 cursor-pointer outline-none focus:outline-none focus-visible:outline-none focus:ring-0 select-none"
        :class="currentLocale === 'km' ? 'text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-400 dark:text-neutral-400 hover:text-slate-600 dark:hover:text-neutral-200'"
        :aria-checked="currentLocale === 'km'"
        role="radio"
        title="ប្តូរទៅភាសាខ្មែរ"
      >
        ខ្មែរ
      </button>

      <!-- EN Option Button -->
      <button 
        type="button"
        @click.stop="handleSelect('en')"
        class="relative z-10 w-1/2 h-full flex items-center justify-center text-[11px] font-bold rounded-full transition-colors duration-150 cursor-pointer font-sans outline-none focus:outline-none focus-visible:outline-none focus:ring-0 select-none"
        :class="currentLocale === 'en' ? 'text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-400 dark:text-neutral-400 hover:text-slate-600 dark:hover:text-neutral-200'"
        :aria-checked="currentLocale === 'en'"
        role="radio"
        title="Switch to English"
      >
        EN
      </button>
    </div>
  </div>

  <!-- Standard Full Row Mode -->
  <div 
    v-else-if="!collapsed" 
    class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl hover:bg-slate-50 transition-colors select-none group"
  >
    <!-- Left: Translate / Globe Icon + Label 'ភាសា (Language)' -->
    <div class="flex items-center gap-3 min-w-0">
      <svg 
        class="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors shrink-0" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
        aria-hidden="true"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          stroke-width="1.8" 
          d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9c-1.636-2.92-3.116-6.023-4.088-9m11.588 5h-7.176m7.176 0a17.986 17.986 0 01-2.088 5.5m2.088-5.5a18.04 18.04 0 00-2.088-5.5m-5.088 0h7.176" 
        />
      </svg>
      <span class="text-[13px] font-medium text-slate-700 truncate">
        ភាសា (Language)
      </span>
    </div>

    <!-- Right: Compact Pill-Shaped Toggle Switch ('ខ្មែរ' / 'EN') -->
    <div 
      class="relative inline-flex items-center p-0.5 bg-slate-100 rounded-full border border-slate-200/80 w-[86px] h-7 shrink-0 select-none overflow-hidden"
      role="radiogroup"
      aria-label="Language selection"
    >
      <!-- Smoothly sliding toggle thumb -->
      <span 
        class="absolute top-0.5 bottom-0.5 left-0.5 w-[calc(50%-2px)] rounded-full bg-white shadow-xs border border-slate-200/50 transition-transform duration-200 ease-out pointer-events-none"
        :class="currentLocale === 'en' ? 'translate-x-full' : 'translate-x-0'"
      ></span>

      <!-- ខ្មែរ Option Button -->
      <button 
        type="button"
        @click.stop="handleSelect('km')"
        class="relative z-10 w-1/2 h-full flex items-center justify-center text-xs font-bold rounded-full transition-colors duration-150 cursor-pointer outline-none focus:outline-none focus-visible:outline-none focus:ring-0 select-none"
        :class="currentLocale === 'km' ? 'text-blue-600 font-extrabold' : 'text-slate-400 hover:text-slate-600'"
        :aria-checked="currentLocale === 'km'"
        role="radio"
        title="ប្តូរទៅភាសាខ្មែរ"
      >
        ខ្មែរ
      </button>

      <!-- EN Option Button -->
      <button 
        type="button"
        @click.stop="handleSelect('en')"
        class="relative z-10 w-1/2 h-full flex items-center justify-center text-xs font-bold rounded-full transition-colors duration-150 cursor-pointer font-sans outline-none focus:outline-none focus-visible:outline-none focus:ring-0 select-none"
        :class="currentLocale === 'en' ? 'text-blue-600 font-extrabold' : 'text-slate-400 hover:text-slate-600'"
        :aria-checked="currentLocale === 'en'"
        role="radio"
        title="Switch to English"
      >
        EN
      </button>
    </div>
  </div>

  <!-- Collapsed Icon Rail (Desktop rail when sidebar is collapsed) -->
  <button 
    v-else
    type="button"
    @click.stop="handleToggle"
    class="w-full flex items-center justify-center p-2.5 rounded-xl text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors group cursor-pointer outline-none focus:outline-none focus-visible:outline-none focus:ring-0"
    :title="currentLocale === 'km' ? 'ភាសា៖ ខ្មែរ (ចុចដើម្បីប្តូរទៅ EN)' : 'Language: English (Click to switch to ខ្មែរ)'"
    aria-label="Toggle language"
  >
    <div class="relative flex items-center justify-center">
      <svg 
        class="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          stroke-width="1.8" 
          d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9c-1.636-2.92-3.116-6.023-4.088-9m11.588 5h-7.176m7.176 0a17.986 17.986 0 01-2.088 5.5m2.088-5.5a18.04 18.04 0 00-2.088-5.5m-5.088 0h7.176" 
        />
      </svg>
      <span class="absolute -bottom-1 -right-1 text-[9px] font-bold font-sans bg-blue-100 text-blue-700 px-1 rounded-sm leading-none border border-blue-200">
        {{ currentLocale.toUpperCase() }}
      </span>
    </div>
  </button>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import { setLocale } from '../i18n';

const props = defineProps({
  collapsed: {
    type: Boolean,
    default: false
  },
  isSubItem: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['change']);

const { locale } = useI18n();

const currentLocale = computed(() => locale.value || 'km');

const handleSelect = (lang) => {
  if (locale.value === lang) return;
  setLocale(lang);
  axios.post('/api/user/language', { locale: lang }).catch(() => {});
  emit('change', lang);
};

const handleToggle = () => {
  const next = currentLocale.value === 'km' ? 'en' : 'km';
  setLocale(next);
  axios.post('/api/user/language', { locale: next }).catch(() => {});
  emit('change', next);
};
</script>
