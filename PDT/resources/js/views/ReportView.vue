<template>
  <div class="space-y-6">
    <!-- Header -->
    <PageHeader 
      :title="$t('reports.title')" 
      :breadcrumb="$t('nav.rolesPermissions')" 
      :actionLabel="$t('reports.printReport')"
      @action="handlePrint"
    />

    <!-- Sub Tabs -->
    <TabNav 
      v-model="activeReportType" 
      :tabs="[
        { id: 'students', label: $t('students.title') },
        { id: 'teachers', label: $t('teachers.title') },
        { id: 'attendance', label: $t('attendance.title') },
        { id: 'finance', label: $t('finance.title') },
        { id: 'examinations', label: $t('examinations.title') },
        { id: 'library', label: $t('library.title') }
      ]"
    />

    <!-- Entity Banner -->
    <EntityBanner 
      :name="$t('reports.title')"
      :subtitle="$t('nav.rolesPermissions') + ' - ប្រព័ន្ធស្ថិតិ និងរបាយការណ៍សាលារៀន'"
      metaId="ADM-RPT-SYS"
      @print="handlePrint"
      @download="handleDownload"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="$t('reports.filterCategory')" :value="activeReportType.toUpperCase()" />
      <StatCard :title="$t('students.academicYear')" :value="'2025-2026'" />
      <StatCard :title="$t('common.status')" :value="$t('common.active')" />
      <StatCard title="Export Ready" :value="'PDF / Excel'" />
    </div>

    <!-- Data Table -->
    <DataTable>
      <template #header>
        <tr v-if="activeReportType === 'students'">
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('students.studentId') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('students.studentName') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('students.gender') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('students.class') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('students.phone') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('common.status') }}</th>
        </tr>
        <tr v-else-if="activeReportType === 'teachers'">
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('teachers.teacherId') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('teachers.name') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('teachers.qualification') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('teachers.specialization') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('teachers.salary') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('common.status') }}</th>
        </tr>
        <tr v-else>
          <th class="py-3 px-4 font-bold text-slate-700">Metric / Indicator</th>
          <th class="py-3 px-4 font-bold text-slate-700">Recorded Period</th>
          <th class="py-3 px-4 font-bold text-slate-700">Department</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">Result</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">Performance</th>
        </tr>
      </template>

      <template #body>
        <template v-if="activeReportType === 'students'">
          <tr v-for="st in students" :key="st.id" class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ st.idCard }}</td>
            <td class="py-3 px-4 font-semibold text-slate-800">{{ st.name }}</td>
            <td class="py-3 px-4 capitalize text-slate-600">{{ st.gender }}</td>
            <td class="py-3 px-4 font-bold text-slate-700">ថ្នាក់ {{ st.grade }}</td>
            <td class="py-3 px-4 text-slate-600">{{ st.phone }}</td>
            <td class="py-3 px-4 text-center">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
                {{ $t('common.active') }}
              </span>
            </td>
          </tr>
        </template>
        <template v-else-if="activeReportType === 'teachers'">
          <tr v-for="t in teachers" :key="t.id" class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ t.idCard }}</td>
            <td class="py-3 px-4 font-semibold text-slate-800">{{ t.name }}</td>
            <td class="py-3 px-4 text-slate-600">{{ t.qualification }}</td>
            <td class="py-3 px-4 font-medium text-slate-700">{{ t.specialization }}</td>
            <td class="py-3 px-4 text-right font-bold text-emerald-600">${{ t.salary.toFixed(2) }}</td>
            <td class="py-3 px-4 text-center">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
                {{ $t('common.active') }}
              </span>
            </td>
          </tr>
        </template>
        <template v-else>
          <tr v-for="item in generalMetrics" :key="item.name" class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3 px-4 font-semibold text-slate-800">{{ item.name }}</td>
            <td class="py-3 px-4 text-slate-600">Semester 1 (2025-2026)</td>
            <td class="py-3 px-4 text-slate-600">{{ item.dept }}</td>
            <td class="py-3 px-4 text-right font-bold text-slate-900">{{ item.value }}</td>
            <td class="py-3 px-4 text-center">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
                Optimal
              </span>
            </td>
          </tr>
        </template>
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
import DataTable from '../components/DataTable.vue';

const { t } = useI18n();
const activeReportType = ref('students');

const students = ref([]);
const teachers = ref([]);
const generalMetrics = ref([]);

const fetchReports = async () => {
  try {
    const res = await axios.get('/api/reports');
    if (res.data) {
      students.value = res.data.students || [];
      teachers.value = res.data.teachers || [];
      generalMetrics.value = res.data.generalMetrics || [];
    }
  } catch (err) {
    console.error('Failed to load reports:', err);
  }
};

onMounted(() => {
  fetchReports();
});

const handlePrint = () => window.print();
const handleDownload = () => alert(t('common.download'));
</script>
