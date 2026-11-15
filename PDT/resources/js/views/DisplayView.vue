<template>
  <div class="space-y-4 font-khmer pb-16 max-w-2xl mx-auto">
    <!-- Sub-page Header with Back Button (<) and Centered Title 'Display' -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200/80 dark:border-neutral-800 transition-colors">
      <div class="flex items-center gap-3">
        <!-- Back Button (<) -->
        <button 
          type="button" 
          @click="goBack" 
          class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-neutral-200 flex items-center justify-center transition-colors shadow-2xs cursor-pointer focus:outline-none"
          :title="currentLocale === 'km' ? 'ត្រឡប់ក្រោយ' : 'Go back'"
          aria-label="Back"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>

        <div>
          <h1 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-tight">
            {{ currentLocale === 'km' ? 'ផ្ទៃបង្ហាញ (Display)' : 'Display' }}
          </h1>
          <p class="text-xs text-slate-400 dark:text-neutral-400">
            {{ currentLocale === 'km' ? 'ការកំណត់រូបរាង និងពន្លឺប្រព័ន្ធ' : 'Appearance and Theme Settings' }}
          </p>
        </div>
      </div>

      <!-- Quick Reset or Indicator Pill -->
      <div class="flex items-center gap-2">
        <span 
          class="text-xs font-semibold px-2.5 py-1 rounded-full border transition-colors"
          :class="isDark 
            ? 'bg-neutral-900 text-neutral-200 border-neutral-700' 
            : 'bg-blue-50 text-blue-700 border-blue-200'"
        >
          {{ isDark ? (currentLocale === 'km' ? '🌙 ងងឹត' : '🌙 Dark') : (currentLocale === 'km' ? '☀️ ពន្លឺ' : '☀️ Light') }}
        </span>
      </div>
    </div>

    <!-- Main TikTok-Style Appearance Selector Card -->
    <AppearanceSelector />

    <!-- Helpful Preview Card: Demonstrating Live Theme Switch across UI Elements -->
    <div class="bg-white dark:bg-[#18181b] rounded-2xl border border-slate-200/80 dark:border-neutral-800 p-4 sm:p-5 shadow-xs space-y-3 transition-colors duration-200">
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-neutral-400 uppercase tracking-wide">
        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ currentLocale === 'km' ? 'ទិដ្ឋភាពជាក់ស្តែងនៃប្រព័ន្ធ' : 'Live System Preview' }}</span>
      </div>

      <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-neutral-900 border border-slate-200/60 dark:border-neutral-800 flex items-center justify-between gap-3 transition-colors">
        <div class="flex items-center gap-3">
          <!-- School Logo / Book Icon -->
          <div class="w-10 h-10 rounded-xl bg-white dark:bg-neutral-800 border border-slate-200/80 dark:border-neutral-700 p-1 flex items-center justify-center shadow-2xs shrink-0 overflow-hidden">
            <img :src="'/images/logo.png'" alt="Logo" class="w-full h-full object-contain" />
          </div>
          <div>
            <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-neutral-100 flex items-center gap-1.5">
              <span>{{ currentLocale === 'km' ? 'សាលារៀន ជំនាន់ថ្មី PDT' : 'New Generation School PDT' }}</span>
              <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
            </h4>
            <p class="text-[11px] text-slate-400 dark:text-neutral-400">
              {{ currentLocale === 'km' ? 'បណ្ណាល័យឌីជីថល និងប្រព័ន្ធគ្រប់គ្រងសាលារៀន' : 'Digital Library & School Management' }}
            </p>
          </div>
        </div>

        <button 
          type="button" 
          class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition-colors shrink-0"
        >
          {{ currentLocale === 'km' ? 'ឧទាហរណ៍' : 'Sample' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import AppearanceSelector from '../components/AppearanceSelector.vue';
import { useTheme } from '../composables/useTheme';

const router = useRouter();
const { locale } = useI18n();
const currentLocale = computed(() => locale.value || 'km');
const { isDark } = useTheme();

const goBack = () => {
  if (window.history.length > 1) {
    router.back();
  } else {
    router.push('/profile');
  }
};
</script>
