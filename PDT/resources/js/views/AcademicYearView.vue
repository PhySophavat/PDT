<template>
  <div class="space-y-6 font-khmer">
    <!-- Header -->
    <PageHeader 
      :title="$t('academicYears.title')" 
      :breadcrumb="$t('nav.academics')" 
      :actionLabel="$t('academicYears.addYear')"
      @action="openCreateModal"
    />

    <!-- Sub Tabs -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'all', label: $t('academicYears.title') },
        { id: 'semesters', label: $t('academicYears.semester1') + ' & ' + $t('academicYears.semester2') }
      ]"
    />

    <!-- 4 Statistics Summary Cards (Dynamic from Database) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="$t('academicYears.title')" :value="stats.total || years.length" />
      <StatCard :title="$t('academicYears.currentYear')" :value="stats.currentYear || '2026-2027'" />
      <StatCard :title="$t('settings.currentSemester')" :value="stats.currentSemester || $t('academicYears.semester1')" />
      <StatCard :title="$t('common.status')" :value="stats.status || $t('common.active')" />
    </div>

    <!-- Data Table -->
    <DataTable>
      <template #header>
        <tr>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('academicYears.yearName') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('academicYears.startDate') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('academicYears.endDate') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('academicYears.semester1') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('academicYears.semester2') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('common.status') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('common.actions') }}</th>
        </tr>
      </template>

      <template #body>
        <tr v-if="years.length === 0">
          <td colspan="7" class="py-10 text-center text-slate-400 font-khmer">មិនមានទិន្នន័យឆ្នាំសិក្សានៅឡើយទេ</td>
        </tr>
        <tr v-for="y in years" :key="y.id" class="hover:bg-slate-50/70 transition-colors">
          <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ y.name }}</td>
          <td class="py-3 px-4 text-slate-600">{{ y.start }}</td>
          <td class="py-3 px-4 text-slate-600">{{ y.end }}</td>
          <td class="py-3 px-4 text-center text-slate-600 font-mono text-xs bg-slate-50/50">{{ y.sem1 }}</td>
          <td class="py-3 px-4 text-center text-slate-600 font-mono text-xs bg-slate-50/50">{{ y.sem2 }}</td>
          <td class="py-3 px-4 text-center">
            <span :class="[
              'px-2.5 py-1 rounded-full text-[11px] font-bold',
              y.is_current || y.isCurrent ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'
            ]">
              {{ (y.is_current || y.isCurrent) ? $t('academicYears.current') : $t('academicYears.history') }}
            </span>
          </td>
          <td class="py-3 px-4 text-right space-x-1.5">
            <button 
              @click="openEditModal(y)" 
              type="button"
              class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs hover:bg-slate-200 transition font-medium"
            >
              {{ $t('common.edit') }}
            </button>
            <button 
              v-if="!(y.is_current || y.isCurrent)" 
              @click="setAsCurrent(y.id)" 
              type="button"
              class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition inline-flex items-center gap-1"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
              <span>{{ $t('academicYears.setAsCurrent') }}</span>
            </button>
            <span v-else class="text-xs text-emerald-600 font-bold px-2 py-0.5 bg-emerald-50 rounded-md">
              ✓ {{ $t('common.active') }}
            </span>
          </td>
        </tr>
      </template>
    </DataTable>

    <!-- Modal: Add / Edit Academic Year -->
    <div 
      v-if="showModal" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs font-khmer transition-opacity"
      @click.self="showModal = false"
    >
      <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-5 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
              <i class="fa-solid fa-calendar-plus"></i>
            </div>
            <div>
              <h3 class="font-bold text-base">
                {{ isEditMode ? 'កែប្រែឆ្នាំសិក្សា (Edit Academic Year)' : 'បន្ថែមឆ្នាំសិក្សាថ្មី (Add Academic Year)' }}
              </h3>
              <p class="text-xs text-blue-100">កំណត់ឈ្មោះឆ្នាំសិក្សា កាលបរិច្ឆេទ និងឆមាស</p>
            </div>
          </div>
          <button 
            @click="showModal = false" 
            class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition"
          >
            ✕
          </button>
        </div>

        <!-- Modal Form Body -->
        <form @submit.prevent="submitForm" class="p-6 space-y-4 text-xs text-slate-700">
          
          <!-- Year Name -->
          <div>
            <label class="block font-semibold text-slate-800 mb-1.5">
              ឈ្មោះឆ្នាំសិក្សា (Academic Year) <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <i class="fa-regular fa-calendar-days text-xs"></i>
              </span>
              <input 
                type="text" 
                v-model="form.name" 
                required 
                placeholder="ឧ. 2026-2027" 
                class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
              />
            </div>
            <p class="text-[10px] text-slate-400 mt-1">ទម្រង់ឆ្នាំ៖ YYYY-YYYY (ឧ. 2025-2026, 2026-2027)</p>
          </div>

          <!-- Start & End Date -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-slate-800 mb-1.5">
                កាលបរិច្ឆេទចាប់ផ្តើម <span class="text-red-500">*</span>
              </label>
              <input 
                type="date" 
                v-model="form.start_date" 
                required 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-800 mb-1.5">
                កាលបរិច្ឆេទបញ្ចប់ <span class="text-red-500">*</span>
              </label>
              <input 
                type="date" 
                v-model="form.end_date" 
                required 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
              />
            </div>
          </div>

          <!-- Semesters -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-slate-800 mb-1.5">
                ឆមាសទី ១ (Semester 1)
              </label>
              <input 
                type="text" 
                v-model="form.semester_1" 
                placeholder="Oct-Feb" 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-800 mb-1.5">
                ឆមាសទី ២ (Semester 2)
              </label>
              <input 
                type="text" 
                v-model="form.semester_2" 
                placeholder="Mar-Jul" 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
              />
            </div>
          </div>

          <!-- Current Year / Active Checkbox -->
          <div class="p-3.5 bg-blue-50/70 border border-blue-200/80 rounded-2xl space-y-2">
            <label class="flex items-center gap-2.5 cursor-pointer">
              <input 
                type="checkbox" 
                v-model="form.is_current" 
                class="w-4 h-4 text-blue-600 rounded-md focus:ring-0 cursor-pointer"
              />
              <span class="font-bold text-slate-900">កំណត់ជាឆ្នាំសិក្សាបច្ចុប្បន្ន (សកម្ម)</span>
            </label>
            <p class="text-[11px] text-slate-500 pl-6.5">
              ប្រសិនបើបើក ឆ្នាំសិក្សានេះនឹងក្លាយជាឆ្នាំសិក្សាគោលសម្រាប់កត់ត្រាវត្តមាន ពិន្ទុ និងការចុះឈ្មោះសិស្ស។
            </p>
          </div>

          <!-- Modal Action Buttons -->
          <div class="p-5 bg-slate-50 border-t border-slate-200 -mx-6 -mb-6 mt-4 flex items-center justify-end gap-3">
            <button 
              @click="showModal = false" 
              type="button" 
              class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl font-semibold transition"
            >
              បោះបង់ (Cancel)
            </button>
            <button 
              :disabled="isSubmitting" 
              type="submit" 
              class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md shadow-blue-600/30 transition flex items-center gap-2 disabled:opacity-50"
            >
              <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
              </svg>
              <i v-else class="fa-solid fa-floppy-disk"></i>
              <span>{{ isSubmitting ? 'កំពុងរក្សាទុក...' : (isEditMode ? 'រក្សាទុកការកែប្រែ' : 'បង្កើតឆ្នាំសិក្សា') }}</span>
            </button>
          </div>

        </form>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import StatCard from '../components/StatCard.vue';
import DataTable from '../components/DataTable.vue';

const { t } = useI18n();
const activeTab = ref('all');

const years = ref([]);
const stats = ref({
  total: 0,
  currentYear: localStorage.getItem('pdt_current_academic_year') || window.__INITIAL_SETTINGS__?.current_academic_year || '2026-2027',
  currentSemester: 'ឆមាសទី ១',
  status: 'សកម្ម (Active)'
});

// Modal State
const showModal = ref(false);
const isEditMode = ref(false);
const editingId = ref(null);
const isSubmitting = ref(false);

const form = ref({
  name: '',
  start_date: '',
  end_date: '',
  semester_1: 'Oct-Feb',
  semester_2: 'Mar-Jul',
  is_current: false,
});

const fetchAcademicYears = async () => {
  try {
    const res = await axios.get('/api/academic-years');
    if (res.data) {
      if (res.data.years) {
        years.value = res.data.years;
      }
      if (res.data.stats) {
        stats.value = res.data.stats;
        if (res.data.stats.currentYear) {
          localStorage.setItem('pdt_current_academic_year', res.data.stats.currentYear);
        }
      }
    }
  } catch (err) {
    console.error('Failed to load academic years:', err);
  }
};

onMounted(() => {
  fetchAcademicYears();
});

// Open Create Modal
const openCreateModal = () => {
  isEditMode.value = false;
  editingId.value = null;

  const currentYr = new Date().getFullYear();
  form.value = {
    name: `${currentYr}-${currentYr + 1}`,
    start_date: `${currentYr}-10-01`,
    end_date: `${currentYr + 1}-07-31`,
    semester_1: 'Oct-Feb',
    semester_2: 'Mar-Jul',
    is_current: false,
  };
  showModal.value = true;
};

// Open Edit Modal
const openEditModal = (year) => {
  isEditMode.value = true;
  editingId.value = year.id;

  form.value = {
    name: year.name,
    start_date: year.start_date || '',
    end_date: year.end_date || '',
    semester_1: year.sem1 || 'Oct-Feb',
    semester_2: year.sem2 || 'Mar-Jul',
    is_current: Boolean(year.is_current || year.isCurrent),
  };
  showModal.value = true;
};

// Submit Create or Update
const submitForm = async () => {
  isSubmitting.value = true;
  try {
    let res;
    if (isEditMode.value && editingId.value) {
      res = await axios.put(`/api/academic-years/${editingId.value}`, form.value);
    } else {
      res = await axios.post('/api/academic-years', form.value);
    }

    if (res.data && res.data.success) {
      alert(res.data.message || 'បានរក្សាទុកឆ្នាំសិក្សាដោយជោគជ័យ!');
      showModal.value = false;
      await fetchAcademicYears();
    }
  } catch (err) {
    console.error('Failed to save academic year:', err);
    alert(err.response?.data?.message || 'ការរក្សាទុកឆ្នាំសិក្សាបានបរាជ័យ សូមពិនិត្យព័ត៌មានម្តងទៀត។');
  } finally {
    isSubmitting.value = false;
  }
};

// Set as Current Academic Year
const setAsCurrent = async (id) => {
  try {
    const res = await axios.post(`/api/academic-years/${id}/set-current`);
    if (res.data && res.data.success) {
      if (res.data.year?.name) {
        localStorage.setItem('pdt_current_academic_year', res.data.year.name);
        window.dispatchEvent(new CustomEvent('pdt-settings-saved', {
          detail: {
            current_academic_year: res.data.year.name
          }
        }));
      }
      alert(res.data.message || 'បានកំណត់ជាឆ្នាំសិក្សាបច្ចុប្បន្នដោយជោគជ័យ!');
      await fetchAcademicYears();
    }
  } catch (err) {
    console.error('Failed to set current academic year:', err);
    alert('ការកំណត់ឆ្នាំសិក្សាបច្ចុប្បន្នបានបរាជ័យ');
  }
};

const handlePrint = () => window.print();
const handleDownload = () => window.location.href = '/api/settings/backup';
</script>
