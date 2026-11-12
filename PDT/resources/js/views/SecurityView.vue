<template>
  <div class="space-y-4 font-khmer pb-16 max-w-5xl mx-auto">
    <!-- Page Header -->
    <PageHeader 
      :title="$t('accountSettings.security', 'សុវត្ថិភាព')" 
      :breadcrumb="$t('accountSettings.security', 'សុវត្ថិភាព')"
    >
      <template #extraActions>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>គណនីមានសុវត្ថិភាព</span>
        </span>
      </template>
    </PageHeader>

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
        :class="[
          'p-4 rounded-2xl border flex items-start gap-3 shadow-sm',
          toast.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800'
        ]"
      >
        <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5" :class="toast.type === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'">
          <Check v-if="toast.type === 'success'" class="w-4 h-4" />
          <AlertCircle v-else class="w-4 h-4" />
        </div>
        <div class="flex-1 text-xs sm:text-sm font-medium leading-relaxed">
          {{ toast.message }}
        </div>
        <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-slate-600 p-0.5">
          <X class="w-4 h-4" />
        </button>
      </div>
    </transition>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
      <!-- Left 2 Cols: Change Password Form -->
      <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5 sm:p-6 space-y-5">
          <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
              <LockKeyhole class="w-4 h-4" />
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-sm sm:text-base font-bold text-slate-800">
                  {{ $t('accountSettings.changePassword', 'ប្តូរពាក្យសម្ងាត់') }}
                </h3>
                <span v-if="currentUserEmail" class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-mono border border-slate-200">
                  {{ currentUserEmail }}
                </span>
              </div>
              <p class="text-[11px] text-slate-400 mt-0.5">
                បញ្ចូលពាក្យសម្ងាត់បច្ចុប្បន្ន ដើម្បីកំណត់ពាក្យសម្ងាត់ថ្មីសម្រាប់គណនីនេះ
              </p>
            </div>
          </div>

          <form @submit.prevent="handleSubmitPassword" class="space-y-4">
            <!-- 1. Current Password Field -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700">
                {{ $t('accountSettings.currentPassword', 'ពាក្យសម្ងាត់បច្ចុប្បន្ន') }} <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <input
                  v-model="form.current_password"
                  :type="showPassword.current ? 'text' : 'password'"
                  autocomplete="current-password"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition pr-10 font-sans"
                  :class="{ 'border-rose-300 focus:ring-rose-500 focus:border-rose-500': fieldErrors.current_password }"
                  placeholder="••••••••"
                  required
                />
                <button
                  type="button"
                  @click="showPassword.current = !showPassword.current"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition p-1"
                  :title="showPassword.current ? 'លាក់' : 'បង្ហាញ'"
                >
                  <EyeOff v-if="showPassword.current" class="w-4 h-4" />
                  <Eye v-else class="w-4 h-4" />
                </button>
              </div>
              <p v-if="fieldErrors.current_password" class="text-[11px] text-rose-600 font-medium">
                {{ fieldErrors.current_password }}
              </p>
            </div>

            <!-- 2. New Password Field -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700">
                {{ $t('accountSettings.newPassword', 'ពាក្យសម្ងាត់ថ្មី') }} <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <input
                  v-model="form.new_password"
                  :type="showPassword.new ? 'text' : 'password'"
                  autocomplete="new-password"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition pr-10 font-sans"
                  :class="{ 'border-rose-300 focus:ring-rose-500 focus:border-rose-500': fieldErrors.new_password }"
                  placeholder="••••••••"
                  required
                  minlength="6"
                />
                <button
                  type="button"
                  @click="showPassword.new = !showPassword.new"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition p-1"
                  :title="showPassword.new ? 'លាក់' : 'បង្ហាញ'"
                >
                  <EyeOff v-if="showPassword.new" class="w-4 h-4" />
                  <Eye v-else class="w-4 h-4" />
                </button>
              </div>

              <!-- Password Strength Meter -->
              <div v-if="form.new_password" class="pt-1.5 space-y-1.5">
                <div class="flex items-center justify-between text-[11px]">
                  <span class="text-slate-500">{{ $t('accountSettings.passwordStrength', 'កម្រិតសុវត្ថិភាព') }}:</span>
                  <span class="font-bold" :class="strengthTextColor">{{ strengthText }}</span>
                </div>
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden flex gap-1">
                  <div 
                    v-for="step in 4" 
                    :key="step" 
                    class="h-full flex-1 rounded-full transition-all duration-300"
                    :class="step <= strengthScore ? strengthBarColor : 'bg-slate-200'"
                  ></div>
                </div>
                <!-- Strength Tips -->
                <div class="grid grid-cols-2 gap-1 text-[10px] text-slate-500 pt-1">
                  <div class="flex items-center gap-1" :class="form.new_password.length >= 6 ? 'text-emerald-600 font-semibold' : ''">
                    <Check v-if="form.new_password.length >= 6" class="w-3 h-3 text-emerald-500" />
                    <span v-else class="w-1.5 h-1.5 rounded-full bg-slate-300 inline-block"></span>
                    <span>យ៉ាងតិច ៦ តួអក្សរ</span>
                  </div>
                  <div class="flex items-center gap-1" :class="/[A-Z]/.test(form.new_password) ? 'text-emerald-600 font-semibold' : ''">
                    <Check v-if="/[A-Z]/.test(form.new_password)" class="w-3 h-3 text-emerald-500" />
                    <span v-else class="w-1.5 h-1.5 rounded-full bg-slate-300 inline-block"></span>
                    <span>អក្សរធំ (A-Z)</span>
                  </div>
                  <div class="flex items-center gap-1" :class="/[0-9]/.test(form.new_password) ? 'text-emerald-600 font-semibold' : ''">
                    <Check v-if="/[0-9]/.test(form.new_password)" class="w-3 h-3 text-emerald-500" />
                    <span v-else class="w-1.5 h-1.5 rounded-full bg-slate-300 inline-block"></span>
                    <span>មានលេខ (0-9)</span>
                  </div>
                  <div class="flex items-center gap-1" :class="/[^A-Za-z0-9]/.test(form.new_password) ? 'text-emerald-600 font-semibold' : ''">
                    <Check v-if="/[^A-Za-z0-9]/.test(form.new_password)" class="w-3 h-3 text-emerald-500" />
                    <span v-else class="w-1.5 h-1.5 rounded-full bg-slate-300 inline-block"></span>
                    <span>សញ្ញាពិសេស (@#$%)</span>
                  </div>
                </div>
              </div>
              <p v-if="fieldErrors.new_password" class="text-[11px] text-rose-600 font-medium">
                {{ fieldErrors.new_password }}
              </p>
            </div>

            <!-- 3. Confirm New Password Field -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700">
                {{ $t('accountSettings.confirmPassword', 'ផ្ទៀងផ្ទាត់ពាក្យសម្ងាត់ថ្មី') }} <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <input
                  v-model="form.new_password_confirmation"
                  :type="showPassword.confirm ? 'text' : 'password'"
                  autocomplete="new-password"
                  class="w-full px-3.5 py-2.5 rounded-xl border text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 transition pr-10 font-sans"
                  :class="[
                    passwordMismatch ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500' : 'border-slate-200 focus:border-blue-500',
                    passwordMatchSuccess ? 'border-emerald-300 bg-emerald-50/20' : ''
                  ]"
                  placeholder="••••••••"
                  required
                />
                <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1">
                  <Check v-if="passwordMatchSuccess" class="w-4 h-4 text-emerald-500" />
                  <button
                    type="button"
                    @click="showPassword.confirm = !showPassword.confirm"
                    class="text-slate-400 hover:text-slate-600 transition p-1"
                    :title="showPassword.confirm ? 'លាក់' : 'បង្ហាញ'"
                  >
                    <EyeOff v-if="showPassword.confirm" class="w-4 h-4" />
                    <Eye v-else class="w-4 h-4" />
                  </button>
                </div>
              </div>
              <p v-if="passwordMismatch" class="text-[11px] text-rose-600 font-medium">
                ពាក្យសម្ងាត់ផ្ទៀងផ្ទាត់មិនត្រូវគ្នាជាមួយពាក្យសម្ងាត់ថ្មីឡើយ
              </p>
            </div>

            <!-- Form Action Buttons -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
              <button
                type="button"
                @click="resetForm"
                :disabled="submitting"
                class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 transition active:scale-95 cursor-pointer disabled:opacity-50"
              >
                {{ $t('accountSettings.cancel', 'បោះបង់') }}
              </button>

              <button
                type="submit"
                :disabled="submitting || !canSubmit"
                class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 transition-all active:scale-95 flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="submitting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                <Check v-else class="w-4 h-4" />
                <span>{{ submitting ? 'កំពុងរក្សាទុក...' : $t('accountSettings.saveChanges', 'រក្សាទុកការកែប្រែ') }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Right Column: Security Status & Tips -->
      <div class="space-y-4">
        <!-- Card 1: Security Highlights -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5 space-y-3.5">
          <div class="flex items-center gap-2 pb-2.5 border-b border-slate-100">
            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
              <ShieldCheck class="w-4 h-4" />
            </div>
            <h4 class="text-xs sm:text-sm font-bold text-slate-800">ប្រព័ន្ធការពារគណនី</h4>
          </div>

          <div class="space-y-2.5 text-xs text-slate-600">
            <div class="flex items-start gap-2">
              <Check class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
              <div>
                <strong class="text-slate-800 block">Hash Encryption</strong>
                <p class="text-[11px] text-slate-400">ពាក្យសម្ងាត់ត្រូវបានបម្លែងជាកូដសុវត្ថិភាព មិនអាចមើលឃើញជាអក្សរផ្ទាល់បានឡើយ</p>
              </div>
            </div>

            <div class="flex items-start gap-2">
              <Check class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
              <div>
                <strong class="text-slate-800 block">ការពារការទាយពាក្យសម្ងាត់</strong>
                <p class="text-[11px] text-slate-400">បញ្ចូលខុស ៥ ដង ប្រព័ន្ធនឹងចាក់សោបណ្តោះអាសន្ន ៥ នាទី</p>
              </div>
            </div>

            <div class="flex items-start gap-2">
              <Check class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
              <div>
                <strong class="text-slate-800 block">Session Timeout សុវត្ថិភាព</strong>
                <p class="text-[11px] text-slate-400">រក្សាទុកសុវត្ថិភាពទិន្នន័យពេលមិនមានសកម្មភាពយូរ</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 2: Password Best Practices Tip -->
        <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50/60 border border-blue-200/80 text-xs text-blue-950 space-y-2">
          <div class="flex items-center gap-1.5 font-bold text-blue-800">
            <LockKeyhole class="w-4 h-4 text-blue-600 shrink-0" />
            <span>ការណែនាំសុវត្ថិភាព</span>
          </div>
          <p class="text-[11px] leading-relaxed text-blue-900/80">
            ដើម្បីការពារគណនីរបស់អ្នក សូមកុំចែករំលែកពាក្យសម្ងាត់ជាមួយអ្នកដទៃ។ ជ្រើសរើសពាក្យសម្ងាត់ដែលមានទាំងអក្សរធំ អក្សរតូច លេខ និងសញ្ញាពិសេស។
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import { 
  ShieldCheck, 
  LockKeyhole, 
  Check, 
  AlertCircle, 
  X, 
  Eye, 
  EyeOff 
} from 'lucide-vue-next';
import PageHeader from '../components/PageHeader.vue';

const currentUser = computed(() => window.__INITIAL_USER__ || {});
const currentUserEmail = computed(() => currentUser.value?.email || '');

const form = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
});

const showPassword = ref({
  current: false,
  new: false,
  confirm: false
});

const submitting = ref(false);
const fieldErrors = ref({});
const toast = ref({
  show: false,
  type: 'success',
  message: ''
});

// Password Strength Calculation
const strengthScore = computed(() => {
  const p = form.value.new_password;
  if (!p) return 0;
  let score = 0;
  if (p.length >= 6) score++;
  if (p.length >= 10 || (/[A-Z]/.test(p) && /[a-z]/.test(p))) score++;
  if (/[0-9]/.test(p)) score++;
  if (/[^A-Za-z0-9]/.test(p)) score++;
  return score;
});

const strengthText = computed(() => {
  switch (strengthScore.value) {
    case 1: return 'ខ្សោយ (Weak)';
    case 2: return 'មធ្យម (Medium)';
    case 3: return 'រឹងមាំ (Strong)';
    case 4: return 'រឹងមាំខ្លាំង (Very Strong)';
    default: return 'ខ្សោយខ្លាំង (Too Weak)';
  }
});

const strengthTextColor = computed(() => {
  switch (strengthScore.value) {
    case 1: return 'text-rose-500';
    case 2: return 'text-amber-500';
    case 3: return 'text-emerald-600';
    case 4: return 'text-blue-600';
    default: return 'text-slate-400';
  }
});

const strengthBarColor = computed(() => {
  switch (strengthScore.value) {
    case 1: return 'bg-rose-500';
    case 2: return 'bg-amber-500';
    case 3: return 'bg-emerald-500';
    case 4: return 'bg-blue-600';
    default: return 'bg-slate-300';
  }
});

const passwordMismatch = computed(() => {
  return form.value.new_password && 
         form.value.new_password_confirmation && 
         form.value.new_password !== form.value.new_password_confirmation;
});

const passwordMatchSuccess = computed(() => {
  return form.value.new_password && 
         form.value.new_password_confirmation && 
         form.value.new_password === form.value.new_password_confirmation;
});

const canSubmit = computed(() => {
  return form.value.current_password &&
         form.value.new_password &&
         form.value.new_password.length >= 6 &&
         form.value.new_password === form.value.new_password_confirmation;
});

const resetForm = () => {
  form.value.current_password = '';
  form.value.new_password = '';
  form.value.new_password_confirmation = '';
  fieldErrors.value = {};
};

const showToast = (message, type = 'success') => {
  toast.value = { show: true, type, message };
  setTimeout(() => {
    toast.value.show = false;
  }, 5000);
};

const handleSubmitPassword = async () => {
  fieldErrors.value = {};

  if (passwordMismatch.value) {
    fieldErrors.value.new_password = 'ពាក្យសម្ងាត់ផ្ទៀងផ្ទាត់មិនត្រូវគ្នាឡើយ';
    return;
  }

  submitting.value = true;
  try {
    const res = await axios.post('/api/user/password', {
      current_password: form.value.current_password,
      new_password: form.value.new_password,
      new_password_confirmation: form.value.new_password_confirmation
    });

    if (res.data?.success) {
      showToast(res.data.message || 'បានផ្លាស់ប្តូរពាក្យសម្ងាត់ដោយជោគជ័យ!', 'success');
      resetForm();
    }
  } catch (err) {
    const data = err.response?.data;
    if (data?.errors) {
      if (data.errors.current_password) {
        fieldErrors.value.current_password = data.errors.current_password[0];
      }
      if (data.errors.new_password) {
        fieldErrors.value.new_password = data.errors.new_password[0];
      }
    }
    showToast(data?.message || 'ការប្តូរពាក្យសម្ងាត់បានបរាជ័យ! សូមពិនិត្យព័ត៌មានម្តងទៀត', 'error');
  } finally {
    submitting.value = false;
  }
};
</script>
