<template>
  <div class="space-y-4 font-khmer pb-16 max-w-2xl mx-auto">
    <!-- Page Header -->
    <PageHeader 
      :title="$t('accountSettings.language', 'ភាសា')" 
      :breadcrumb="$t('accountSettings.language', 'ភាសា')"
    />

    <!-- Alert Toast / Banner -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div 
        v-if="toast.show" 
        class="p-3.5 sm:p-4 rounded-xl border border-emerald-200 dark:border-emerald-800/60 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 flex items-start gap-3 shadow-xs"
      >
        <div class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
          <Check class="w-3.5 h-3.5" />
        </div>
        <div class="flex-1 text-xs sm:text-sm font-medium leading-relaxed">
          {{ toast.message }}
        </div>
        <button type="button" @click="toast.show = false" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200 p-0.5 cursor-pointer">
          <X class="w-4 h-4" />
        </button>
      </div>
    </transition>

    <!-- Language Selection Radio Group Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xs p-4 sm:p-5 space-y-3">
      <div class="px-0.5">
        <h2 class="text-sm font-bold text-gray-800 dark:text-gray-100">
          {{ $t('accountSettings.selectLanguage', 'ជ្រើសរើសភាសាប្រព័ន្ធ') }}
        </h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
          {{ currentLocale === 'km' ? 'ជ្រើសរើសភាសាដែលអ្នកចង់ប្រើប្រាស់ក្នុងកម្មវិធី' : 'Select your preferred interface display language' }}
        </p>
      </div>

      <!-- Compact Horizontal Radio Rows -->
      <div class="space-y-2.5" role="radiogroup">
        <label
          v-for="item in languages"
          :key="item.code"
          class="flex items-center justify-between p-4 rounded-xl cursor-pointer transition-all duration-200 select-none group"
          :class="[
            currentLocale === item.code
              ? 'border-2 border-blue-600 bg-blue-50/40 dark:bg-blue-950/20 dark:border-blue-500 shadow-xs'
              : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50/60 dark:hover:bg-gray-750'
          ]"
        >
          <!-- Visually hidden native radio button for accessibility -->
          <input 
            type="radio" 
            name="language" 
            :value="item.code" 
            :checked="currentLocale === item.code"
            @change="chooseLanguage(item.code)"
            class="sr-only"
          />

          <!-- Left side: Badge + Primary Title & Subtitle -->
          <div class="flex items-center gap-3.5">
            <div 
              class="w-10 h-10 rounded-xl flex items-center justify-center font-bold font-sans text-xs sm:text-sm shrink-0 transition-transform group-hover:scale-105 shadow-2xs"
              :class="currentLocale === item.code 
                ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700/50' 
                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600'"
            >
              {{ item.badge }}
            </div>
            <div>
              <h3 class="text-sm sm:text-base font-bold text-gray-800 dark:text-gray-100 leading-tight">
                {{ item.title }}
              </h3>
              <p class="text-xs text-gray-400 dark:text-gray-400 font-sans mt-0.5">
                {{ item.subtitle }}
              </p>
            </div>
          </div>

          <!-- Right side: True Radio Circle Indicator -->
          <div 
            class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 transition-all duration-200"
            :class="currentLocale === item.code
              ? 'border-2 border-blue-600 bg-blue-600 dark:border-blue-500 dark:bg-blue-500'
              : 'border-2 border-gray-300 dark:border-gray-600 bg-transparent group-hover:border-gray-400 dark:group-hover:border-gray-500'"
          >
            <span 
              v-if="currentLocale === item.code" 
              class="w-2 h-2 rounded-full bg-white"
            ></span>
          </div>
        </label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import { setLocale } from '../i18n';
import { Check, X } from 'lucide-vue-next';
import PageHeader from '../components/PageHeader.vue';

const { locale } = useI18n();
const currentLocale = computed(() => locale.value);

const toast = ref({
  show: false,
  message: ''
});

const languages = [
  {
    code: 'km',
    badge: 'KH',
    title: 'ភាសាខ្មែរ',
    subtitle: 'Khmer'
  },
  {
    code: 'en',
    badge: 'GB',
    title: 'English',
    subtitle: 'អង់គ្លេស'
  }
];

const chooseLanguage = async (lang) => {
  if (locale.value === lang) return;

  setLocale(lang);
  toast.value = {
    show: true,
    message: lang === 'km' 
      ? 'បានប្តូរភាសាប្រព័ន្ធទៅជា «ភាសាខ្មែរ» ដោយជោគជ័យ!' 
      : 'System language successfully switched to "English"!'
  };

  try {
    await axios.post('/api/user/language', { locale: lang });
  } catch (e) {
    // Session fallback is non-blocking since localStorage already persists it
  }

  setTimeout(() => {
    toast.value.show = false;
  }, 4000);
};
</script>
