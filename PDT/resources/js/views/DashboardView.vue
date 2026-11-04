<template>
  <div class="space-y-6 font-khmer">
    <!-- Header -->
    <PageHeader 
      :title="t('dashboard.title')" 
      :breadcrumb="t('nav.dashboard')" 
      :actionLabel="'+ ' + t('common.createNew')"
      @action="handleCreateNew"
    />

    <!-- Sub Tabs -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'overview', label: t('common.all') },
        { id: 'academic', label: t('classes.title') },
        { id: 'financial', label: t('finance.title') },
        { id: 'attendance', label: t('attendance.title') }
      ]"
    />

    <!-- Entity / School Banner (Attendance style) -->
    <EntityBanner 
      name="សាលារៀន ជំនាន់ថ្មី PDT - School Management Suite"
      :subtitle="`${t('dashboard.subtitle')} | ${t('academicYears.currentYear')}: 2025-2026`"
      metaId="SCH-2026-001"
      @print="handlePrint"
      @download="handleDownload"
    />

    <!-- 4 Statistics Summary Cards (Exact Attendance reference style) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="t('dashboard.totalStudents')" :value="stats.students" />
      <StatCard :title="t('dashboard.totalTeachers')" :value="stats.teachers" />
      <StatCard :title="t('dashboard.totalClasses')" :value="stats.classes" />
      <StatCard :title="t('dashboard.totalSubjects')" :value="stats.subjects" />
    </div>

    <!-- Filter Bar / Legend -->
    <FilterBar>
      <template #left>
        <span class="font-medium text-slate-700">{{ t('students.status') }}: <span class="font-bold text-[#1e40af]">{{ t('common.active') }}</span></span>
        <span class="font-medium text-slate-700 ml-2">{{ t('dashboard.attendanceToday') }}: <span class="font-bold text-emerald-600">96.8%</span></span>
        <span class="font-medium text-slate-700 ml-2">{{ t('dashboard.totalRevenue') }}: <span class="font-bold text-slate-900">$14,250.00</span></span>
      </template>

      <template #right>
        <div class="inline-flex items-center gap-3 px-3.5 py-1.5 bg-slate-50 border border-slate-200/80 rounded-xl">
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
            <input type="radio" name="termFilter" value="s1" checked class="text-blue-600 focus:ring-0">
            <span>{{ t('academicYears.semester1') }}</span>
          </label>
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
            <input type="radio" name="termFilter" value="s2" class="text-blue-600 focus:ring-0">
            <span>{{ t('academicYears.semester2') }}</span>
          </label>
        </div>

        <select class="px-3.5 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-medium focus:outline-none shadow-2xs font-khmer">
          <option>{{ t('academicYears.currentYear') }} 2025-2026</option>
        </select>
      </template>
    </FilterBar>

    <!-- Data Table (Attendance Master Table Style) -->
    <DataTable>
      <template #header>
        <tr>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('classes.className') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('classes.classTeacher') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ t('dashboard.totalStudents') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ t('dashboard.presentToday') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ t('dashboard.absentToday') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ t('dashboard.rate') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ t('common.actions') }}</th>
        </tr>
      </template>

      <template #body>
        <tr v-for="cls in recentClasses" :key="cls.name" class="hover:bg-slate-50/70 transition-colors">
          <td class="py-3 px-4 font-bold text-slate-800">
            <router-link :to="`/classes`" class="text-blue-600 hover:underline">
              {{ t('classes.class') }} {{ cls.name }}
            </router-link>
          </td>
          <td class="py-3 px-4 text-slate-600">{{ cls.teacher }}</td>
          <td class="py-3 px-4 text-center font-semibold text-slate-700">{{ cls.students }}</td>
          <td class="py-3 px-4 text-center font-bold text-emerald-600">{{ cls.present }}</td>
          <td class="py-3 px-4 text-center font-bold text-rose-600">{{ cls.absent }}</td>
          <td class="py-3 px-4 text-center">
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
              {{ cls.rate }}%
            </span>
          </td>
          <td class="py-3 px-4 text-right">
            <router-link to="/attendance" class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition">
              {{ t('common.viewDetails') }} &rarr;
            </router-link>
          </td>
        </tr>
      </template>
    </DataTable>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import EntityBanner from '../components/EntityBanner.vue';
import StatCard from '../components/StatCard.vue';
import FilterBar from '../components/FilterBar.vue';
import DataTable from '../components/DataTable.vue';

const { t } = useI18n();

const activeTab = ref('overview');

const stats = ref({
  students: 120,
  teachers: 15,
  classes: 48,
  subjects: 11
});

const recentClasses = ref([
  { name: '7A', teacher: 'លោកគ្រូ សុខ ចាន់ដារ៉ា', students: 35, present: 34, absent: 1, rate: 97.1 },
  { name: '8A', teacher: 'អ្នកគ្រូ កែវ មុនីរ័ត្ន', students: 36, present: 35, absent: 1, rate: 97.2 },
  { name: '9A', teacher: 'លោកគ្រូ ជា វណ្ណៈ', students: 34, present: 32, absent: 2, rate: 94.1 },
  { name: '10A', teacher: 'លោកគ្រូ ហេង បូរ៉ា', students: 38, present: 37, absent: 1, rate: 97.4 },
  { name: '11A', teacher: 'អ្នកគ្រូ អ៊ុក សោភា', students: 32, present: 31, absent: 1, rate: 96.9 },
  { name: '12A', teacher: 'លោកគ្រូ សេង វិបុល', students: 35, present: 35, absent: 0, rate: 100.0 }
]);

onMounted(async () => {
  try {
    const res = await axios.get('/api/dashboard');
    if (res.data && res.data.stats) {
      stats.value = res.data.stats;
    }
  } catch (e) {
    // Keep defaults
  }
});

const handleCreateNew = () => {
  alert(t('common.createNew'));
};
const handlePrint = () => window.print();
const handleDownload = () => alert(t('common.download'));
</script>
