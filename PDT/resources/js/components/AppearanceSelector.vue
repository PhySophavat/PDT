<template>
  <div class="space-y-3 font-khmer select-none">
    <!-- Section Label 'Appearance' -->
    <div class="px-1 flex items-center justify-between">
      <span class="text-xs font-semibold text-slate-500 dark:text-neutral-400 tracking-wide uppercase">
        {{ currentLocale === 'km' ? 'រូបរាង (Appearance)' : 'Appearance' }}
      </span>
      <span v-if="useDeviceSettings" class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-full border border-emerald-200/60 dark:border-emerald-800/60">
        {{ currentLocale === 'km' ? 'តាមប្រព័ន្ធឧបករណ៍' : 'Auto (Device)' }}
      </span>
    </div>

    <!-- TikTok-Style Appearance Card Container -->
    <div class="bg-white dark:bg-[#18181b] rounded-2xl sm:rounded-3xl border border-slate-200/90 dark:border-neutral-800 p-5 sm:p-6 shadow-xs transition-colors duration-200">
      <!-- Side-by-Side Visual Phone/Screen Previews -->
      <div class="grid grid-cols-2 gap-4 sm:gap-8 pt-2 pb-5">
        <!-- 1. LIGHT THEME CARD -->
        <div 
          @click="selectTheme('light')"
          class="flex flex-col items-center cursor-pointer group focus:outline-none"
          role="radio"
          :aria-checked="activeSelection === 'light'"
          tabindex="0"
          @keydown.space.prevent="selectTheme('light')"
          @keydown.enter.prevent="selectTheme('light')"
        >
          <!-- Phone/Screen Miniature Mockup (Light) -->
          <div 
            class="relative w-[116px] sm:w-[134px] h-[175px] sm:h-[200px] rounded-2xl p-2.5 flex flex-col justify-between shadow-xs transition-all duration-200 border-2 overflow-hidden bg-[#f8f9fc]"
            :class="[
              activeSelection === 'light'
                ? 'border-blue-500 dark:border-blue-400 shadow-md ring-2 ring-blue-500/20'
                : 'border-slate-200 dark:border-neutral-700 hover:border-slate-300 dark:hover:border-neutral-600'
            ]"
          >
            <!-- Miniature Screen Header & Logo / Book Icon -->
            <div class="flex flex-col items-center gap-1.5 pt-0.5">
              <!-- School Logo / Book Icon Circle -->
              <div class="w-6.5 h-6.5 rounded-full bg-white border border-blue-200/80 p-0.5 flex items-center justify-center shadow-2xs overflow-hidden">
                <img 
                  :src="'/images/logo.png'" 
                  alt="School Logo" 
                  class="w-full h-full object-contain"
                  @error="logoFailed = true"
                  v-if="!logoFailed"
                />
                <svg v-else class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
              </div>
              <!-- Skeleton Bar Lines -->
              <div class="w-12 h-1.5 rounded-full bg-slate-300"></div>
              <div class="w-7 h-1 rounded-full bg-slate-200"></div>
            </div>

            <!-- Miniature 2x3 Content Grid: Miniature School Books -->
            <div class="grid grid-cols-3 gap-1 pt-1 pb-0.5">
              <!-- Book 1: Math (Blue) -->
              <div class="h-8.5 rounded-sm bg-gradient-to-br from-blue-500 to-indigo-600 p-0.5 flex flex-col justify-between shadow-2xs text-white">
                <div class="w-2.5 h-0.5 bg-white/80 rounded-full"></div>
                <div class="flex justify-end">
                  <svg class="w-2.5 h-2.5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
              </div>
              <!-- Book 2: Physics (Amber) -->
              <div class="h-8.5 rounded-sm bg-gradient-to-br from-amber-400 to-amber-600 p-0.5 flex flex-col justify-between shadow-2xs text-white">
                <div class="w-2.5 h-0.5 bg-white/80 rounded-full"></div>
                <div class="flex justify-end">
                  <svg class="w-2.5 h-2.5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
              </div>
              <!-- Book 3: Chemistry (Emerald) -->
              <div class="h-8.5 rounded-sm bg-gradient-to-br from-emerald-500 to-teal-600 p-0.5 flex flex-col justify-between shadow-2xs text-white">
                <div class="w-2.5 h-0.5 bg-white/80 rounded-full"></div>
                <div class="flex justify-end">
                  <svg class="w-2.5 h-2.5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
              </div>
              <!-- Book 4: Biology (Rose) -->
              <div class="h-8.5 rounded-sm bg-gradient-to-br from-rose-500 to-red-600 p-0.5 flex flex-col justify-between shadow-2xs text-white">
                <div class="w-2.5 h-0.5 bg-white/80 rounded-full"></div>
                <div class="flex justify-end">
                  <svg class="w-2.5 h-2.5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
              </div>
              <!-- Book 5: Literature (Purple) -->
              <div class="h-8.5 rounded-sm bg-gradient-to-br from-purple-500 to-indigo-700 p-0.5 flex flex-col justify-between shadow-2xs text-white">
                <div class="w-2.5 h-0.5 bg-white/80 rounded-full"></div>
                <div class="flex justify-end">
                  <svg class="w-2.5 h-2.5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
              </div>
              <!-- Book 6: English (Sky) -->
              <div class="h-8.5 rounded-sm bg-gradient-to-br from-sky-400 to-blue-500 p-0.5 flex flex-col justify-between shadow-2xs text-white">
                <div class="w-2.5 h-0.5 bg-white/80 rounded-full"></div>
                <div class="flex justify-end">
                  <svg class="w-2.5 h-2.5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Label Underneath -->
          <span 
            class="mt-3 text-xs sm:text-sm font-semibold transition-colors"
            :class="activeSelection === 'light' ? 'text-slate-900 dark:text-white' : 'text-slate-500 dark:text-neutral-400'"
          >
            {{ currentLocale === 'km' ? 'ពន្លឺ (Light)' : 'Light' }}
          </span>

          <!-- Circular Radio Button Below Label -->
          <div 
            class="mt-2.5 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200"
            :class="[
              activeSelection === 'light'
                ? 'border-[#fe2c55] bg-[#fe2c55] shadow-xs'
                : 'border-neutral-400 dark:border-neutral-600 bg-transparent group-hover:border-slate-500'
            ]"
          >
            <span 
              v-if="activeSelection === 'light'" 
              class="w-2 h-2 rounded-full bg-white"
            ></span>
          </div>
        </div>

        <!-- 2. DARK THEME CARD -->
        <div 
          @click="selectTheme('dark')"
          class="flex flex-col items-center cursor-pointer group focus:outline-none"
          role="radio"
          :aria-checked="activeSelection === 'dark'"
          tabindex="0"
          @keydown.space.prevent="selectTheme('dark')"
          @keydown.enter.prevent="selectTheme('dark')"
        >
          <!-- Phone/Screen Miniature Mockup (Dark) -->
          <div 
            class="relative w-[116px] sm:w-[134px] h-[175px] sm:h-[200px] rounded-2xl p-2.5 flex flex-col justify-between shadow-xs transition-all duration-200 border-2 overflow-hidden bg-[#18181b]"
            :class="[
              activeSelection === 'dark'
                ? 'border-blue-500 dark:border-blue-400 shadow-md ring-2 ring-blue-500/20'
                : 'border-slate-200 dark:border-neutral-700 hover:border-slate-300 dark:hover:border-neutral-600'
            ]"
          >
            <!-- Miniature Screen Header & Logo / Book Icon -->
            <div class="flex flex-col items-center gap-1.5 pt-0.5">
              <!-- School Logo / Book Icon in Dark -->
              <div class="w-6.5 h-6.5 rounded-full bg-neutral-800 border border-neutral-700 p-0.5 flex items-center justify-center shadow-2xs overflow-hidden">
                <img 
                  :src="'/images/logo.png'" 
                  alt="School Logo" 
                  class="w-full h-full object-contain"
                  @error="logoFailed = true"
                  v-if="!logoFailed"
                />
                <svg v-else class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
              </div>
              <!-- Skeleton Bar Lines -->
              <div class="w-12 h-1.5 rounded-full bg-neutral-700"></div>
              <div class="w-7 h-1 rounded-full bg-neutral-800"></div>
            </div>

            <!-- Miniature 2x3 Content Grid: Miniature School Books in Dark -->
            <div class="grid grid-cols-3 gap-1 pt-1 pb-0.5">
              <!-- Book 1: Math (Blue) -->
              <div class="h-8.5 rounded-sm bg-gradient-to-br from-blue-900/90 to-indigo-950 p-0.5 flex flex-col justify-between shadow-2xs border border-blue-800/40 text-blue-300">
                <div class="w-2.5 h-0.5 bg-blue-300/40 rounded-full"></div>
                <div class="flex justify-end">
                  <svg class="w-2.5 h-2.5 text-blue-300/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
              </div>
              <!-- Book 2: Physics (Amber) -->
              <div class="h-8.5 rounded-sm bg-gradient-to-br from-amber-900/90 to-amber-950 p-0.5 flex flex-col justify-between shadow-2xs border border-amber-800/40 text-amber-300">
                <div class="w-2.5 h-0.5 bg-amber-300/40 rounded-full"></div>
                <div class="flex justify-end">
                  <svg class="w-2.5 h-2.5 text-amber-300/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
              </div>
              <!-- Book 3: Chemistry (Emerald) -->
              <div class="h-8.5 rounded-sm bg-gradient-to-br from-emerald-900/90 to-teal-950 p-0.5 flex flex-col justify-between shadow-2xs border border-emerald-800/40 text-emerald-300">
                <div class="w-2.5 h-0.5 bg-emerald-300/40 rounded-full"></div>
                <div class="flex justify-end">
                  <svg class="w-2.5 h-2.5 text-emerald-300/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
              </div>
              <!-- Book 4: Biology (Rose) -->
              <div class="h-8.5 rounded-sm bg-gradient-to-br from-rose-900/90 to-red-950 p-0.5 flex flex-col justify-between shadow-2xs border border-rose-800/40 text-rose-300">
                <div class="w-2.5 h-0.5 bg-rose-300/40 rounded-full"></div>
                <div class="flex justify-end">
                  <svg class="w-2.5 h-2.5 text-rose-300/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
              </div>
              <!-- Book 5: Literature (Purple) -->
              <div class="h-8.5 rounded-sm bg-gradient-to-br from-purple-900/90 to-indigo-950 p-0.5 flex flex-col justify-between shadow-2xs border border-purple-800/40 text-purple-300">
                <div class="w-2.5 h-0.5 bg-purple-300/40 rounded-full"></div>
                <div class="flex justify-end">
                  <svg class="w-2.5 h-2.5 text-purple-300/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
              </div>
              <!-- Book 6: English (Sky) -->
              <div class="h-8.5 rounded-sm bg-gradient-to-br from-sky-900/90 to-blue-950 p-0.5 flex flex-col justify-between shadow-2xs border border-sky-800/40 text-sky-300">
                <div class="w-2.5 h-0.5 bg-sky-300/40 rounded-full"></div>
                <div class="flex justify-end">
                  <svg class="w-2.5 h-2.5 text-sky-300/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Label Underneath -->
          <span 
            class="mt-3 text-xs sm:text-sm font-semibold transition-colors"
            :class="activeSelection === 'dark' ? 'text-slate-900 dark:text-white' : 'text-slate-500 dark:text-neutral-400'"
          >
            {{ currentLocale === 'km' ? 'ងងឹត (Dark)' : 'Dark' }}
          </span>

          <!-- Circular Radio Button Below Label -->
          <div 
            class="mt-2.5 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200"
            :class="[
              activeSelection === 'dark'
                ? 'border-[#fe2c55] bg-[#fe2c55] shadow-xs'
                : 'border-neutral-400 dark:border-neutral-600 bg-transparent group-hover:border-slate-500'
            ]"
          >
            <span 
              v-if="activeSelection === 'dark'" 
              class="w-2 h-2 rounded-full bg-white"
            ></span>
          </div>
        </div>
      </div>

      <!-- Divider -->
      <div class="border-t border-slate-100 dark:border-neutral-800/90 my-2"></div>

      <!-- 'Use device settings' Row with Toggle Switch -->
      <div 
        @click="toggleDevice(!useDeviceSettings)"
        class="flex items-center justify-between pt-3 pb-1 cursor-pointer select-none group"
      >
        <div class="pr-4 min-w-0">
          <h4 class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-neutral-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
            {{ currentLocale === 'km' ? 'ប្រើការកំណត់ឧបករណ៍' : 'Use device settings' }}
          </h4>
          <p class="text-[11px] sm:text-xs text-slate-500 dark:text-neutral-400 mt-0.5 leading-relaxed">
            {{ currentLocale === 'km' ? 'ផ្គូផ្គងផ្ទៃបង្ហាញទៅតាមការកំណត់ពន្លឺ និងផ្ទៃបង្ហាញនៃឧបករណ៍របស់អ្នក។' : "Match appearance to your device's Display & Brightness settings." }}
          </p>
        </div>

        <!-- iOS-Style Toggle Switch -->
        <button
          type="button"
          @click.stop="toggleDevice(!useDeviceSettings)"
          class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-0 select-none"
          :class="useDeviceSettings ? 'bg-[#34c759]' : 'bg-slate-300 dark:bg-neutral-700'"
          role="switch"
          :aria-checked="useDeviceSettings"
          :title="useDeviceSettings ? 'Use device settings enabled' : 'Use device settings disabled'"
        >
          <span class="sr-only">Use device settings</span>
          <span
            class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out transform"
            :class="useDeviceSettings ? 'translate-x-5' : 'translate-x-0'"
          ></span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useTheme } from '../composables/useTheme';

const logoFailed = ref(false);

const { locale } = useI18n();
const currentLocale = computed(() => locale.value || 'km');

const {
  themeMode,
  isDark,
  useDeviceSettings,
  activeSelection,
  setThemeMode,
  toggleDeviceSettings
} = useTheme();

const selectTheme = (mode) => {
  setThemeMode(mode);
};

const toggleDevice = (val) => {
  toggleDeviceSettings(val);
};
</script>
