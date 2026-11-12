<template>
  <div class="space-y-4 font-khmer pb-16 max-w-5xl mx-auto">
    <!-- Page Header (Clean title without redundant badge) -->
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
        class="p-4 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 flex items-start gap-3 shadow-sm"
      >
        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
          <Check class="w-4 h-4" />
        </div>
        <div class="flex-1 text-xs sm:text-sm font-medium leading-relaxed">
          {{ toast.message }}
        </div>
        <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-slate-600 p-0.5">
          <X class="w-4 h-4" />
        </button>
      </div>
    </transition>

    <!-- Language Selector Cards (Compact & Consistent KH/GB Badges) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
      <!-- 1. Khmer Option Card -->
      <div
        @click="chooseLanguage('km')"
        :class="[
          'relative p-4 sm:p-5 rounded-2xl border-2 transition-all duration-200 cursor-pointer flex flex-col justify-between gap-3 group hover:shadow-md active:scale-98 select-none',
          currentLocale === 'km'
            ? 'border-blue-600 bg-blue-50/40 shadow-xs ring-2 ring-blue-500/20'
            : 'border-slate-200/80 bg-white hover:border-slate-300'
        ]"
      >
        <!-- Top row: Consistent KH Badge + Title + Radio -->
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center font-bold font-sans text-xs sm:text-sm text-blue-700 shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
              KH
            </div>
            <div>
              <h3 class="text-sm sm:text-base font-bold text-slate-800 group-hover:text-blue-700 transition-colors leading-tight">
                ភាសាខ្មែរ
              </h3>
              <p class="text-xs text-slate-400 font-sans mt-0.5">Khmer</p>
            </div>
          </div>

          <div
            class="w-5 h-5 sm:w-6 sm:h-6 rounded-full border-2 flex items-center justify-center transition-colors shrink-0"
            :class="currentLocale === 'km' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 bg-white'"
          >
            <Check v-if="currentLocale === 'km'" class="w-3.5 h-3.5" />
          </div>
        </div>

        <!-- Description -->
        <p class="text-xs text-slate-500 leading-relaxed">
          កំណត់ភាសាប្រព័ន្ធទាំងមូលជាភាសាខ្មែរ រួមទាំងប្រតិទិន តារាងពិន្ទុ បញ្ជីវត្តមាន និងប័ណ្ណសិស្ស។
        </p>

        <!-- Status Pill -->
        <div class="pt-2 border-t border-slate-100/80 flex items-center justify-between text-xs">
          <span v-if="currentLocale === 'km'" class="text-blue-700 font-bold flex items-center gap-1.5 text-[11px]">
            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
            កំពុងប្រើប្រាស់ជាភាសាសកម្ម
          </span>
          <span v-else class="text-slate-400 font-medium text-[11px] group-hover:text-blue-600">
            ចុចដើម្បីប្តូរជាភាសាខ្មែរ →
          </span>
        </div>
      </div>

      <!-- 2. English Option Card -->
      <div
        @click="chooseLanguage('en')"
        :class="[
          'relative p-4 sm:p-5 rounded-2xl border-2 transition-all duration-200 cursor-pointer flex flex-col justify-between gap-3 group hover:shadow-md active:scale-98 select-none',
          currentLocale === 'en'
            ? 'border-blue-600 bg-blue-50/40 shadow-xs ring-2 ring-blue-500/20'
            : 'border-slate-200/80 bg-white hover:border-slate-300'
        ]"
      >
        <!-- Top row: Consistent GB Badge + Title + Radio -->
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center font-bold font-sans text-xs sm:text-sm text-blue-700 shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
              GB
            </div>
            <div>
              <h3 class="text-sm sm:text-base font-bold text-slate-800 group-hover:text-blue-700 transition-colors leading-tight">
                English
              </h3>
              <p class="text-xs text-slate-400 font-sans mt-0.5">អង់គ្លេស</p>
            </div>
          </div>

          <div
            class="w-5 h-5 sm:w-6 sm:h-6 rounded-full border-2 flex items-center justify-center transition-colors shrink-0"
            :class="currentLocale === 'en' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 bg-white'"
          >
            <Check v-if="currentLocale === 'en'" class="w-3.5 h-3.5" />
          </div>
        </div>

        <!-- Description -->
        <p class="text-xs text-slate-500 leading-relaxed">
          Set full application interface to English for administrative workflows, international reporting, and system settings.
        </p>

        <!-- Status Pill -->
        <div class="pt-2 border-t border-slate-100/80 flex items-center justify-between text-xs">
          <span v-if="currentLocale === 'en'" class="text-blue-700 font-bold flex items-center gap-1.5 text-[11px]">
            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
            Currently Active Language
          </span>
          <span v-else class="text-slate-400 font-medium text-[11px] group-hover:text-blue-600">
            Click to switch to English →
          </span>
        </div>
      </div>
    </div>

    <!-- Live Preview Card (Accurate Localized Format) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-4 sm:p-5 space-y-3.5">
      <div class="flex items-center gap-2 pb-2.5 border-b border-slate-100">
        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
          <Eye class="w-4 h-4" />
        </div>
        <div>
          <h4 class="text-xs sm:text-sm font-bold text-slate-800">
            {{ currentLocale === 'km' ? 'គំរូទម្រង់បង្ហាញផ្ទាល់ (Live Preview)' : 'Live Language Preview' }}
          </h4>
          <p class="text-[11px] text-slate-400">
            {{ currentLocale === 'km' ? 'របៀបដែលទិន្នន័យត្រូវបានបង្ហាញលើប្រព័ន្ធ' : 'How elements appear in the selected language' }}
          </p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
        <div class="p-3 bg-slate-50 rounded-xl space-y-1">
          <span class="text-[10px] text-slate-400 block font-medium">
            {{ currentLocale === 'km' ? 'ម៉ឺនុយផ្ទាំងគ្រប់គ្រង' : 'Dashboard Menu' }}
          </span>
          <strong class="text-slate-800 block text-xs sm:text-sm">
            {{ currentLocale === 'km' ? 'ផ្ទាំងគ្រប់គ្រង' : 'Dashboard' }}
          </strong>
        </div>

        <div class="p-3 bg-slate-50 rounded-xl space-y-1">
          <span class="text-[10px] text-slate-400 block font-medium">
            {{ currentLocale === 'km' ? 'កាលបរិច្ឆេទថ្ងៃនេះ' : 'Current Date' }}
          </span>
          <strong class="text-slate-800 block text-xs sm:text-sm">
            {{ localizedDatePreview }}
          </strong>
        </div>

        <div class="p-3 bg-slate-50 rounded-xl space-y-1">
          <span class="text-[10px] text-slate-400 block font-medium">
            {{ currentLocale === 'km' ? 'ប៊ូតុងរក្សាទុក' : 'Action Buttons' }}
          </span>
          <strong class="text-slate-800 block text-xs sm:text-sm">
            {{ currentLocale === 'km' ? 'រក្សាទុក / បោះបង់' : 'Save / Cancel' }}
          </strong>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import { setLocale } from '../i18n';
import { Check, Eye, X } from 'lucide-vue-next';
import PageHeader from '../components/PageHeader.vue';

const { locale } = useI18n();
const currentLocale = computed(() => locale.value);

const toast = ref({
  show: false,
  message: ''
});

// Convert Arabic digits to Khmer numerals (e.g. 12 -> ១២, 2026 -> ២០២៦)
const toKhmerNumber = (num) => {
  const khmerDigits = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
  return String(num).replace(/[0-9]/g, d => khmerDigits[d]);
};

// Accurate Localized Live Preview Date Format
const localizedDatePreview = computed(() => {
  const now = new Date();
  if (currentLocale.value === 'km') {
    const days = ['អាទិត្យ', 'ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍'];
    const months = [
      'មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា',
      'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'
    ];
    const dayName = days[now.getDay()];
    const dayNum = toKhmerNumber(now.getDate());
    const monthName = months[now.getMonth()];
    const yearNum = toKhmerNumber(now.getFullYear());
    return `ថ្ងៃ${dayName} ទី${dayNum} ខែ${monthName} ឆ្នាំ${yearNum}`;
  }

  // English format: Thursday, November 12, 2026
  return now.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
});

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
