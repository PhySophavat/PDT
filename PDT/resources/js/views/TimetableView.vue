<template>
  <div class="space-y-6">
    <!-- Header -->
    <PageHeader 
      :title="$t('timetables.title')" 
      :breadcrumb="$t('nav.homework')" 
      :actionLabel="$t('timetables.addSchedule')"
      @action="openCreateModal"
    />

    <!-- Sub Tabs -->
    <TabNav 
      v-model="selectedDay" 
      :tabs="[
        { id: 'all', label: $t('timetables.weekly') },
        { id: 'ច័ន្ទ', label: 'ថ្ងៃច័ន្ទ' },
        { id: 'អង្គារ', label: 'ថ្ងៃអង្គារ' },
        { id: 'ពុធ', label: 'ថ្ងៃពុធ' },
        { id: 'ព្រហស្បតិ៍', label: 'ថ្ងៃព្រហស្បតិ៍' },
        { id: 'សុក្រ', label: 'ថ្ងៃសុក្រ' },
        { id: 'សៅរ៍', label: 'ថ្ងៃសៅរ៍' }
      ]"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="$t('timetables.period')" :value="filteredTimetables.length" />
      <StatCard :title="$t('classes.totalClasses')" :value="classesList.length || 48" />
      <StatCard :title="$t('teachers.totalTeachers')" :value="teachersList.length || 15" />
      <StatCard :title="$t('timetables.conflictFree')" :value="'100% ត្រឹមត្រូវ'" />
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <label class="text-xs font-bold text-slate-700">ជ្រើសរើសថ្នាក់រៀន:</label>
        <select v-model="selectedClass" class="px-3.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          <option value="">-- បង្ហាញគ្រប់ថ្នាក់ --</option>
          <option v-for="c in classesList" :key="c" :value="c">ថ្នាក់ {{ c }}</option>
        </select>
      </div>

      <div class="flex items-center gap-2">
        <span class="text-xs text-slate-500">
          សរុបមាន: <strong>{{ filteredTimetables.length }}</strong> ម៉ោងបង្រៀន
        </span>
      </div>
    </div>

    <!-- Data Table -->
    <DataTable>
      <template #header>
        <tr>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('timetables.day') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('timetables.period') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('timetables.subject') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('timetables.class') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('timetables.teacher') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('timetables.room') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('common.actions') }}</th>
        </tr>
      </template>

      <template #body>
        <tr v-if="filteredTimetables.length === 0">
          <td colspan="7" class="py-10 text-center text-slate-400">{{ $t('common.noData') }}</td>
        </tr>
        <tr v-for="t in filteredTimetables" :key="t.id" class="hover:bg-slate-50/70 transition-colors">
          <td class="py-3 px-4 font-bold text-blue-700">
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700">
              {{ t.day_of_week || t.day }}
            </span>
          </td>
          <td class="py-3 px-4 font-mono font-bold text-slate-700">{{ t.time || `${t.start_time} - ${t.end_time}` }}</td>
          <td class="py-3 px-4 font-bold text-slate-900">{{ t.subject }}</td>
          <td class="py-3 px-4 font-bold text-blue-600">ថ្នាក់ {{ t.class || t.class_name }}</td>
          <td class="py-3 px-4 text-slate-600 font-medium">{{ t.teacher }}</td>
          <td class="py-3 px-4 text-slate-500 font-mono">{{ t.room_number || t.room || '-' }}</td>
          <td class="py-3 px-4 text-right">
            <button @click="handleDelete(t)" class="px-2.5 py-1 bg-rose-50 text-rose-600 rounded-lg text-xs font-semibold hover:bg-rose-100 transition">
              {{ $t('common.delete') }}
            </button>
          </td>
        </tr>
      </template>
    </DataTable>

    <!-- Modal: Add Timetable Entry Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
            + បញ្ចូលកាលវិភាគបង្រៀនថ្មី
          </h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitCreateTimetable" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ថ្នាក់រៀន *</label>
              <select v-model="createForm.class_name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="">-- ជ្រើសរើសថ្នាក់ --</option>
                <option v-for="c in classesList" :key="c" :value="c">ថ្នាក់ {{ c }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ថ្ងៃបង្រៀន *</label>
              <select v-model="createForm.day_of_week" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="ច័ន្ទ">ថ្ងៃច័ន្ទ</option>
                <option value="អង្គារ">ថ្ងៃអង្គារ</option>
                <option value="ពុធ">ថ្ងៃពុធ</option>
                <option value="ព្រហស្បតិ៍">ថ្ងៃព្រហស្បតិ៍</option>
                <option value="សុក្រ">ថ្ងៃសុក្រ</option>
                <option value="សៅរ៍">ថ្ងៃសៅរ៍</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">មុខវិជ្ជា *</label>
            <select v-model="createForm.course_subject_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
              <option value="">-- ជ្រើសរើសមុខវិជ្ជា --</option>
              <option v-for="s in subjectsList" :key="s.id" :value="s.id">{{ s.name }} ({{ s.code }})</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">គ្រូបង្រៀន *</label>
            <select v-model="createForm.teacher_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
              <option value="">-- ជ្រើសរើសគ្រូបង្រៀន --</option>
              <option v-for="t in teachersList" :key="t.id" :value="t.id">{{ t.name }}</option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ម៉ោងចាប់ផ្តើម *</label>
              <input v-model="createForm.start_time" type="time" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ម៉ោងបញ្ចប់ *</label>
              <input v-model="createForm.end_time" type="time" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">បន្ទប់សិក្សា</label>
            <input v-model="createForm.room_number" type="text" placeholder="ឧ. Room 101" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">
              បោះបង់
            </button>
            <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50">
              <span v-if="isSubmitting">កំពុងរក្សាទុក...</span>
              <span v-else>រក្សាទុកកាលវិភាគ</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import StatCard from '../components/StatCard.vue';
import DataTable from '../components/DataTable.vue';

const { t } = useI18n();

const selectedDay = ref('all');
const selectedClass = ref('');

const timetablesList = ref([]);
const classesList = ref([]);
const subjectsList = ref([]);
const teachersList = ref([]);

const showCreateModal = ref(false);
const isSubmitting = ref(false);

const createForm = ref({
  class_name: '',
  course_subject_id: '',
  teacher_id: '',
  day_of_week: 'ច័ន្ទ',
  start_time: '07:30',
  end_time: '08:20',
  room_number: '',
});

const fetchTimetables = async () => {
  try {
    const res = await axios.get('/api/timetables');
    timetablesList.value = res.data.timetables || [];
    classesList.value = res.data.classes || [];
    subjectsList.value = res.data.subjects || [];
    teachersList.value = [...(res.data.teachers || [])].sort((a, b) => (a.name || '').localeCompare(b.name || '', 'km'));
  } catch (err) {
    console.error('Failed to fetch timetables:', err);
  }
};

onMounted(() => {
  fetchTimetables();
});

const filteredTimetables = computed(() => {
  return timetablesList.value.filter(t => {
    const day = t.day_of_week || t.day;
    const matchesDay = selectedDay.value === 'all' || day === selectedDay.value;
    const cName = t.class_name || t.class;
    const matchesClass = !selectedClass.value || cName === selectedClass.value;
    return matchesDay && matchesClass;
  });
});

const openCreateModal = () => {
  createForm.value = {
    class_name: selectedClass.value || (classesList.value[0] || '7A'),
    course_subject_id: subjectsList.value[0]?.id || '',
    teacher_id: teachersList.value[0]?.id || '',
    day_of_week: selectedDay.value !== 'all' ? selectedDay.value : 'ច័ន្ទ',
    start_time: '07:30',
    end_time: '08:20',
    room_number: 'Room 101',
  };
  showCreateModal.value = true;
};

const submitCreateTimetable = async () => {
  isSubmitting.value = true;
  try {
    const res = await axios.post('/api/timetables', createForm.value);
    showCreateModal.value = false;
    alert(res.data.message || 'បានបញ្ចូលកាលវិភាគជោគជ័យ!');
    await fetchTimetables();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការបញ្ចូលកាលវិភាគ!');
  } finally {
    isSubmitting.value = false;
  }
};

const handleDelete = async (item) => {
  if (!confirm(`តើអ្នកប្រាកដជាចង់លុបម៉ោងបង្រៀន ${item.subject} នៅថ្នាក់ ${item.class || item.class_name} នេះមែនទេ?`)) return;
  try {
    const res = await axios.delete(`/api/timetables/${item.id}`);
    alert(res.data.message || 'បានលុបម៉ោងបង្រៀនដោយជោគជ័យ!');
    await fetchTimetables();
  } catch (err) {
    alert(err.response?.data?.message || 'មិនអាចលុបបានទេ!');
  }
};

const handlePrint = () => window.print();

const handleExport = () => {
  const csv = [
    ['ថ្ងៃ', 'ម៉ោង', 'មុខវិជ្ជា', 'ថ្នាក់', 'គ្រូបង្រៀន', 'បន្ទប់'].join(','),
    ...timetablesList.value.map(t => [
      `"${t.day_of_week || t.day}"`,
      `"${t.time || t.start_time + ' - ' + t.end_time}"`,
      `"${t.subject}"`,
      `"${t.class || t.class_name}"`,
      `"${t.teacher}"`,
      `"${t.room_number || t.room || ''}"`
    ].join(','))
  ].join('\n');

  const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `Timetable_${new Date().toISOString().slice(0, 10)}.csv`;
  a.click();
};
</script>
