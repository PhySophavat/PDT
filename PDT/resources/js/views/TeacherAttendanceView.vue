<template>
  <div class="space-y-6 font-khmer">
    <!-- Header -->
    <PageHeader 
      title="វត្តមានលោកគ្រូ-អ្នកគ្រូ (Teacher Attendance)" 
      breadcrumb="វត្តមាន / វត្តមានគ្រូបង្រៀន" 
      actionLabel="កត់ត្រាវត្តមានគ្រូប្រចាំថ្ងៃ"
      @action="activeTab = 'daily'"
    />

    <!-- Sub Tabs -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'matrix', label: 'តារាងវត្តមានគ្រូប្រចាំខែ (Monthly Matrix)' },
        { id: 'daily', label: 'ស្រង់វត្តមានគ្រូប្រចាំថ្ងៃ (Daily Sheet)' }
      ]"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard title="វត្តមានគ្រូសរុប (Present)" :value="stats.present" />
      <StatCard title="មកយឺត (Late)" :value="stats.late" />
      <StatCard title="អវត្តមាន (Absent)" :value="stats.absent" />
      <StatCard title="ច្បាប់/សម្រាកសរុប (Leave)" :value="stats.holiday" />
    </div>

    <!-- TAB 1: Monthly Teacher Attendance Sheet (Days 1 to 28/29/30/31) -->
    <div v-if="activeTab === 'matrix'" class="space-y-4">
      <!-- Advanced Filter Bar -->
      <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs space-y-3">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 text-xs">
          <!-- Legend -->
          <div class="flex flex-wrap items-center gap-3">
            <span class="font-medium text-slate-700">វត្តមាន (Present): <span class="font-bold text-emerald-600 text-sm">✓</span></span>
            <span class="font-medium text-slate-700">ច្បាប់ (Leave): <span class="font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">H</span></span>
            <span class="font-medium text-slate-700">អវត្តមាន (Absent): <span class="font-bold text-rose-600 bg-rose-100 px-1.5 py-0.5 rounded">A</span></span>
            <span class="font-medium text-slate-700">មកយឺត (Late): <span class="font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded">L</span></span>
            <span class="font-medium text-slate-400">ថ្ងៃអាទិត្យ: <span class="font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">-</span></span>
          </div>

          <!-- Quick Toggle & Search -->
          <div class="flex items-center gap-2 flex-wrap">
            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 cursor-pointer transition">
              <input type="checkbox" v-model="onlyShowAbsentTeachers" class="rounded text-indigo-600 focus:ring-0">
              <span>បង្ហាញតែអ្នកមានឈប់/ច្បាប់</span>
            </label>
          </div>
        </div>

        <!-- Filter Selectors Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-100 text-xs">
          <!-- 1. Search Teacher -->
          <div>
            <label class="block font-bold text-slate-600 mb-1">ស្វែងរកគ្រូបង្រៀន:</label>
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="ឈ្មោះគ្រូ ឬលេខទូរស័ព្ទ..." 
              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
            />
          </div>

          <!-- 2. Academic Year Dropdown -->
          <div>
            <label class="block font-bold text-slate-600 mb-1">ឆ្នាំសិក្សា (Academic Year):</label>
            <select 
              v-model="selectedAcademicYear" 
              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-700 font-semibold rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
            >
              <option v-for="y in academicYearsList" :key="y" :value="y">{{ y }}</option>
            </select>
          </div>

          <!-- 3. Month Dropdown (Determines 28, 29, 30, or 31 days) -->
          <div>
            <label class="block font-bold text-slate-600 mb-1">
              ខែសិក្សា (Month - {{ daysInActiveMonth }} ថ្ងៃ):
            </label>
            <select 
              v-model="selectedMonth" 
              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-800 font-bold rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
            >
              <option v-for="m in monthOptions" :key="m.key" :value="m.key">{{ m.label }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Master Monthly Teacher Attendance Table -->
      <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
          <div>
            <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
              <span>បញ្ជីវត្តមានលោកគ្រូ-អ្នកគ្រូប្រចាំខែ {{ getMonthKhmerLabel(activeMonthKey) }}</span>
              <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-700">
                សរុប {{ displayedTeachersList.length }} នាក់
              </span>
            </h4>
            <p class="text-[11px] text-slate-500 mt-0.5">
              គណនាពីថ្ងៃទី ១ ដល់ថ្ងៃទី {{ daysInActiveMonth }} • បើអត់ឈប់គូស «✓» • បើឈប់គូស «H» (ច្បាប់), «A» (អវត្តមាន), «L» (យឺត)
            </p>
          </div>

          <div class="text-[11px] text-slate-500">
            <span v-if="onlyShowAbsentTeachers" class="text-rose-600 font-bold">
              កំពុងបង្ហាញតែគ្រូដែលមានច្បាប់/អវត្តមាន
            </span>
            <span v-else>
              បង្ហាញគ្រូបង្រៀនទាំងអស់
            </span>
          </div>
        </div>

        <div v-if="displayedTeachersList.length === 0" class="py-16 text-center text-slate-400 text-xs">
          <div class="flex flex-col items-center justify-center gap-2">
            <span class="text-3xl">🎉</span>
            <span class="font-bold text-emerald-600 text-sm">ពុំមានគ្រូណាមានអវត្តមាន ឬសុំច្បាប់ក្នុងខែនេះឡើយ (វត្តមាន ១០០%)</span>
            <button @click="onlyShowAbsentTeachers = false; searchQuery = ''" class="text-indigo-600 font-bold hover:underline">
              ចុចទីនេះដើម្បីបង្ហាញឈ្មោះគ្រូទាំងអស់
            </button>
          </div>
        </div>

        <div v-else class="overflow-x-auto max-h-[70vh]">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-100 text-slate-700 font-bold sticky top-0 z-20 border-b border-slate-200">
              <tr>
                <th class="py-2.5 px-3 w-10 text-center sticky left-0 bg-slate-100 z-30 shadow-xs border-r border-slate-200">#</th>
                <th class="py-2.5 px-3 min-w-[150px] sticky left-10 bg-slate-100 z-30 shadow-xs border-r border-slate-200">ឈ្មោះលោកគ្រូ-អ្នកគ្រូ</th>
                <th class="py-2.5 px-2.5 w-14 text-center sticky left-[190px] bg-slate-100 z-30 shadow-xs border-r border-slate-200">ភេទ</th>
                <th class="py-2.5 px-2.5 min-w-[110px] sticky left-[246px] bg-slate-100 z-30 shadow-xs border-r border-slate-200">ឯកទេស</th>

                <!-- Dynamic Days Columns -->
                <th 
                  v-for="d in monthDaysList" 
                  :key="d.day" 
                  class="py-2.5 px-1 min-w-[32px] text-center border-r border-slate-200/80 font-mono text-[11px]"
                  :class="[
                    d.isSunday ? 'bg-slate-200/80 text-rose-600 font-bold' : '',
                    d.dateStr === selectedDate ? 'bg-indigo-100 text-indigo-700 font-bold' : ''
                  ]"
                  :title="`${d.dateStr} (${d.isSunday ? 'អាទិត្យ' : 'ថ្ងៃបង្រៀន'})`"
                >
                  {{ d.day }}
                </th>

                <!-- Summary Columns -->
                <th class="py-2.5 px-2 min-w-[36px] text-center bg-emerald-50 text-emerald-800 border-r border-slate-200" title="វត្តមានសរុប">✓</th>
                <th class="py-2.5 px-2 min-w-[36px] text-center bg-emerald-100 text-emerald-900 border-r border-slate-200" title="ច្បាប់សរុប">H</th>
                <th class="py-2.5 px-2 min-w-[36px] text-center bg-rose-50 text-rose-800 border-r border-slate-200" title="អវត្តមានសរុប">A</th>
                <th class="py-2.5 px-2 min-w-[36px] text-center bg-amber-50 text-amber-800" title="យឺតសរុប">L</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
              <tr 
                v-for="(tc, idx) in displayedTeachersList" 
                :key="tc.teacher_id" 
                class="hover:bg-slate-50/80 transition-colors"
              >
                <!-- Sticky Info Columns -->
                <td class="py-2 px-3 text-center font-mono font-bold text-slate-400 sticky left-0 bg-white z-10 border-r border-slate-100">
                  {{ idx + 1 }}
                </td>
                <td class="py-2 px-3 font-bold text-slate-800 sticky left-10 bg-white z-10 border-r border-slate-100 whitespace-nowrap">
                  <div>{{ tc.name }}</div>
                  <div class="text-[10px] font-mono text-slate-400 font-normal">{{ tc.teacher_id_card }}</div>
                </td>
                <td class="py-2 px-2.5 text-center text-slate-600 sticky left-[190px] bg-white z-10 border-r border-slate-100 text-[11px]">
                  {{ tc.gender === 'female' ? 'ស្រី' : 'ប្រុស' }}
                </td>
                <td class="py-2 px-2.5 text-slate-600 sticky left-[246px] bg-white z-10 border-r border-slate-100 text-[11px] truncate max-w-[120px]">
                  <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">
                    {{ tc.specialization || 'ទូទៅ' }}
                  </span>
                </td>

                <!-- Days Status Columns -->
                <td 
                  v-for="d in monthDaysList" 
                  :key="d.day" 
                  class="py-2 px-1 text-center border-r border-slate-100 text-xs font-mono"
                  :class="[
                    d.isSunday ? 'bg-slate-100/60' : '',
                    d.dateStr === selectedDate ? 'bg-indigo-50/50' : ''
                  ]"
                >
                  <span 
                    :class="[
                      'inline-block px-1 py-0.5 text-center text-[11px]',
                      getTeacherDayStatus(tc.teacher_id, d.dateStr, d.isSunday).class
                    ]"
                    :title="getTeacherDayStatus(tc.teacher_id, d.dateStr, d.isSunday).remark || getTeacherDayStatus(tc.teacher_id, d.dateStr, d.isSunday).label"
                  >
                    {{ getTeacherDayStatus(tc.teacher_id, d.dateStr, d.isSunday).char }}
                  </span>
                </td>

                <!-- Summary Counts -->
                <td class="py-2 px-2 text-center font-bold font-mono text-emerald-700 bg-emerald-50/40 border-r border-slate-100">
                  {{ getTeacherSummary(tc.teacher_id).presentCount }}
                </td>
                <td class="py-2 px-2 text-center font-bold font-mono text-emerald-800 bg-emerald-100/40 border-r border-slate-100">
                  <span :class="{ 'font-black text-emerald-700 bg-emerald-100 px-1 rounded': getTeacherSummary(tc.teacher_id).leaveCount > 0 }">
                    {{ getTeacherSummary(tc.teacher_id).leaveCount }}
                  </span>
                </td>
                <td class="py-2 px-2 text-center font-bold font-mono text-rose-700 bg-rose-50/40 border-r border-slate-100">
                  <span :class="{ 'font-black text-rose-700 bg-rose-100 px-1 rounded': getTeacherSummary(tc.teacher_id).absentCount > 0 }">
                    {{ getTeacherSummary(tc.teacher_id).absentCount }}
                  </span>
                </td>
                <td class="py-2 px-2 text-center font-bold font-mono text-amber-700 bg-amber-50/40">
                  <span :class="{ 'font-black text-amber-700 bg-amber-100 px-1 rounded': getTeacherSummary(tc.teacher_id).lateCount > 0 }">
                    {{ getTeacherSummary(tc.teacher_id).lateCount }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 2: Daily Teacher Attendance Sheet -->
    <div v-else-if="activeTab === 'daily'" class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
        <div>
          <h3 class="font-bold text-slate-800 text-base">បញ្ជីវត្តមានលោកគ្រូ-អ្នកគ្រូប្រចាំថ្ងៃ (Daily Teacher Sheet)</h3>
          <p class="text-xs text-slate-500">ស្រង់វត្តមានជាក់ស្តែងប្រចាំថ្ងៃទី {{ selectedDate }}</p>
        </div>
        <div class="flex items-center gap-2">
          <button @click="markAllTeachers('present')" type="button" class="px-3.5 py-2 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-bold hover:bg-emerald-100 transition cursor-pointer">
            ✓ វត្តមានទាំងអស់
          </button>
          <button @click="submitSaveAttendance" :disabled="isSaving" type="button" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm disabled:opacity-50 cursor-pointer">
            <span v-if="isSaving">កំពុងរក្សាទុក...</span>
            <span v-else>💾 រក្សាទុកវត្តមាន</span>
          </button>
        </div>
      </div>

      <!-- Date selector row -->
      <div class="flex items-center gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200/70 text-xs">
        <label class="font-bold text-slate-700">កាលបរិច្ឆេទ (Date):</label>
        <input 
          type="date" 
          v-model="selectedDate" 
          @change="fetchTeacherAttendance" 
          class="px-3 py-2 bg-white border border-slate-200 rounded-xl font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
        />
      </div>

      <!-- Teachers Daily List Table -->
      <div class="overflow-x-auto border border-slate-200/80 rounded-2xl">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="bg-slate-100/90 text-slate-700 font-bold border-b border-slate-200">
              <th class="py-3 px-3 w-10 text-center">#</th>
              <th class="py-3 px-4 min-w-[160px]">ឈ្មោះគ្រូបង្រៀន</th>
              <th class="py-3 px-3 w-16 text-center">ភេទ</th>
              <th class="py-3 px-4 min-w-[130px]">ឯកទេស</th>
              <th class="py-3 px-3 w-28">ទូរស័ព្ទ</th>
              <th class="py-3 px-4 min-w-[280px]">ស្ថានភាពវត្តមាន</th>
              <th class="py-3 px-4 min-w-[200px]">សម្គាល់ (Remark)</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="(tc, idx) in teachersList" :key="tc.teacher_id" class="hover:bg-slate-50 transition-colors">
              <td class="py-3 px-3 text-center font-mono font-bold text-slate-400">{{ idx + 1 }}</td>
              <td class="py-3 px-4">
                <div class="font-bold text-slate-800">{{ tc.name }}</div>
                <div class="text-[11px] font-mono text-slate-400">{{ tc.teacher_id_card }}</div>
              </td>
              <td class="py-3 px-3 text-center text-slate-600">
                {{ tc.gender === 'female' ? 'ស្រី' : 'ប្រុស' }}
              </td>
              <td class="py-3 px-4">
                <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700">
                  {{ tc.specialization || 'ទូទៅ' }}
                </span>
              </td>
              <td class="py-3 px-3 font-mono text-slate-600">{{ tc.phone || '-' }}</td>
              <td class="py-3 px-4">
                <div class="inline-flex rounded-xl bg-slate-100 p-1 gap-1 text-[11px]">
                  <button 
                    type="button" 
                    @click="attendanceMap[tc.teacher_id] = 'present'"
                    :class="[
                      'px-2.5 py-1 rounded-lg font-bold transition cursor-pointer',
                      attendanceMap[tc.teacher_id] === 'present' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
                    ]"
                  >
                    ✓ វត្តមាន
                  </button>
                  <button 
                    type="button" 
                    @click="attendanceMap[tc.teacher_id] = 'late'"
                    :class="[
                      'px-2.5 py-1 rounded-lg font-bold transition cursor-pointer',
                      attendanceMap[tc.teacher_id] === 'late' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
                    ]"
                  >
                    L យឺត
                  </button>
                  <button 
                    type="button" 
                    @click="attendanceMap[tc.teacher_id] = 'excused'"
                    :class="[
                      'px-2.5 py-1 rounded-lg font-bold transition cursor-pointer',
                      attendanceMap[tc.teacher_id] === 'excused' ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
                    ]"
                  >
                    H ច្បាប់
                  </button>
                  <button 
                    type="button" 
                    @click="attendanceMap[tc.teacher_id] = 'absent'"
                    :class="[
                      'px-2.5 py-1 rounded-lg font-bold transition cursor-pointer',
                      attendanceMap[tc.teacher_id] === 'absent' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
                    ]"
                  >
                    A អវត្តមាន
                  </button>
                </div>
              </td>
              <td class="py-3 px-4">
                <input 
                  type="text" 
                  v-model="remarksMap[tc.teacher_id]" 
                  placeholder="សម្គាល់ (បើមាន)..." 
                  class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import StatCard from '../components/StatCard.vue';

const activeTab = ref('matrix');
const selectedAcademicYear = ref(
  localStorage.getItem('pdt_current_academic_year') ||
  window.__INITIAL_SETTINGS__?.current_academic_year ||
  '2026-2027'
);
const selectedMonth = ref('november');
const selectedDate = ref(new Date().toISOString().slice(0, 10));
const searchQuery = ref('');
const onlyShowAbsentTeachers = ref(false);
const isSaving = ref(false);

const academicYearsList = ref(['2025-2026', '2024-2025']);
const teachersList = ref([]);
const attendanceMap = ref({});
const remarksMap = ref({});
const teacherAttendanceMap = ref({});

const stats = ref({
  present: 0,
  late: 0,
  absent: 0,
  holiday: 0
});

const monthOptions = [
  { key: 'january', label: 'មករា (January)' },
  { key: 'february', label: 'កុម្ភៈ (February)' },
  { key: 'march', label: 'មីនា (March)' },
  { key: 'april', label: 'មេសា (April)' },
  { key: 'may', label: 'ឧសភា (May)' },
  { key: 'june', label: 'មិថុនា (June)' },
  { key: 'july', label: 'កក្កដា (July)' },
  { key: 'august', label: 'សីហា (August)' },
  { key: 'september', label: 'កញ្ញា (September)' },
  { key: 'october', label: 'តុលា (October)' },
  { key: 'november', label: 'វិច្ឆិកា (November)' },
  { key: 'december', label: 'ធ្នូ (December)' },
];

const monthIndexMap = {
  january: 0, february: 1, march: 2, april: 3, may: 4, june: 5,
  july: 6, august: 7, september: 8, october: 9, november: 10, december: 11
};

const currentYearNumber = computed(() => {
  if (selectedAcademicYear.value) {
    const parts = selectedAcademicYear.value.split('-');
    if (parts.length > 0 && !isNaN(parseInt(parts[0]))) {
      return parseInt(parts[0]);
    }
  }
  if (selectedDate.value) {
    const y = new Date(selectedDate.value).getFullYear();
    if (!isNaN(y)) return y;
  }
  return 2026;
});

const activeMonthKey = computed(() => {
  if (selectedMonth.value && selectedMonth.value !== 'all') {
    return selectedMonth.value;
  }
  return 'november';
});

const daysInActiveMonth = computed(() => {
  const mIdx = monthIndexMap[activeMonthKey.value] ?? 10;
  return new Date(currentYearNumber.value, mIdx + 1, 0).getDate();
});

const monthDaysList = computed(() => {
  const count = daysInActiveMonth.value;
  const arr = [];
  const mIdx = monthIndexMap[activeMonthKey.value] ?? 10;
  const yr = currentYearNumber.value;
  for (let d = 1; d <= count; d++) {
    const dateObj = new Date(yr, mIdx, d);
    const dayOfWeek = dateObj.getDay();
    const isSunday = (dayOfWeek === 0);
    const isSaturday = (dayOfWeek === 6);
    const dateStr = `${yr}-${String(mIdx + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    arr.push({ day: d, dateStr, isSunday, isSaturday });
  }
  return arr;
});

const getTeacherDayStatus = (teacherId, dateStr, isSunday) => {
  if (isSunday) return { char: '-', label: 'ថ្ងៃអាទិត្យ', class: 'text-slate-300 font-bold' };
  const rec = teacherAttendanceMap.value[teacherId]?.[dateStr];
  if (rec) {
    if (rec.status === 'excused' || rec.status === 'holiday') {
      return { char: 'H', label: 'សុំច្បាប់', class: 'bg-emerald-100 text-emerald-800 font-black rounded px-1 shadow-xs', remark: rec.remark };
    }
    if (rec.status === 'absent') {
      return { char: 'A', label: 'អវត្តមាន', class: 'bg-rose-100 text-rose-700 font-black rounded px-1 shadow-xs', remark: rec.remark };
    }
    if (rec.status === 'late') {
      return { char: 'L', label: 'មកយឺត', class: 'bg-amber-100 text-amber-800 font-black rounded px-1 shadow-xs', remark: rec.remark };
    }
    if (rec.status === 'present') {
      return { char: '✓', label: 'វត្តមាន', class: 'text-emerald-600 font-bold' };
    }
  }
  const todayStr = new Date().toISOString().slice(0, 10);
  if (dateStr <= todayStr) {
    return { char: '✓', label: 'វត្តមាន', class: 'text-emerald-600 font-bold' };
  }
  return { char: '·', label: 'មិនទាន់ដល់', class: 'text-slate-300' };
};

const getTeacherSummary = (teacherId) => {
  let presentCount = 0, leaveCount = 0, absentCount = 0, lateCount = 0;
  for (const d of monthDaysList.value) {
    const st = getTeacherDayStatus(teacherId, d.dateStr, d.isSunday);
    if (st.char === '✓') presentCount++;
    else if (st.char === 'H') leaveCount++;
    else if (st.char === 'A') absentCount++;
    else if (st.char === 'L') lateCount++;
  }
  return { presentCount, leaveCount, absentCount, lateCount };
};

const displayedTeachersList = computed(() => {
  let list = teachersList.value;
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.trim().toLowerCase();
    list = list.filter(tc => 
      (tc.name && tc.name.toLowerCase().includes(q)) ||
      (tc.teacher_id_card && tc.teacher_id_card.toLowerCase().includes(q)) ||
      (tc.phone && tc.phone.includes(q))
    );
  }
  if (onlyShowAbsentTeachers.value) {
    list = list.filter(tc => {
      const sum = getTeacherSummary(tc.teacher_id);
      return (sum.leaveCount + sum.absentCount + sum.lateCount) > 0;
    });
  }

  // Sort strictly by Khmer Alphabetical Order (អក្ខរក្រមខ្មែរ)
  return [...list].sort((a, b) => {
    const nameA = a.name_khmer || a.name || '';
    const nameB = b.name_khmer || b.name || '';
    return nameA.localeCompare(nameB, 'km');
  });
});

const getMonthKhmerLabel = (key) => {
  const found = monthOptions.find(m => m.key === key);
  return found ? found.label : key;
};

const fetchTeacherAttendance = async () => {
  try {
    const res = await axios.get('/api/teacher-attendance', {
      params: {
        date: selectedDate.value,
      }
    });
    if (res.data) {
      if (res.data.stats) stats.value = res.data.stats;
      if (res.data.currentAcademicYear) {
        selectedAcademicYear.value = res.data.currentAcademicYear;
        localStorage.setItem('pdt_current_academic_year', res.data.currentAcademicYear);
      }
      if (res.data.academicYears) academicYearsList.value = res.data.academicYears;
      if (res.data.teacher_attendance_map) teacherAttendanceMap.value = res.data.teacher_attendance_map;
      teachersList.value = res.data.teachers || [];
      attendanceMap.value = {};
      remarksMap.value = {};
      teachersList.value.forEach(tc => {
        attendanceMap.value[tc.teacher_id] = tc.status || 'present';
        remarksMap.value[tc.teacher_id] = tc.remark || '';
      });
    }
  } catch (err) {
    console.error('Failed to fetch teacher attendance:', err);
  }
};

const markAllTeachers = (status) => {
  teachersList.value.forEach(tc => {
    attendanceMap.value[tc.teacher_id] = status;
  });
};

const submitSaveAttendance = async () => {
  if (teachersList.value.length === 0) return;
  isSaving.value = true;
  try {
    const res = await axios.post('/api/teacher-attendance', {
      date: selectedDate.value,
      attendance: attendanceMap.value,
      remarks: remarksMap.value,
    });
    alert(res.data.message || 'បានរក្សាទុកវត្តមានលោកគ្រូ-អ្នកគ្រូដោយជោគជ័យ!');
    await fetchTeacherAttendance();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការរក្សាទុកវត្តមាន!');
  } finally {
    isSaving.value = false;
  }
};

const handlePrint = () => window.print();

const handleExport = () => {
  const csv = [
    ['អត្តលេខ', 'ឈ្មោះគ្រូបង្រៀន', 'ភេទ', 'ឯកទេស', 'កាលបរិច្ឆេទ', 'ស្ថានភាពវត្តមាន', 'សម្គាល់'].join(','),
    ...teachersList.value.map(tc => [
      `"${tc.teacher_id_card}"`,
      `"${tc.name}"`,
      `"${tc.gender}"`,
      `"${tc.specialization}"`,
      `"${selectedDate.value}"`,
      `"${attendanceMap.value[tc.teacher_id] || 'present'}"`,
      `"${remarksMap.value[tc.teacher_id] || ''}"`
    ].join(','))
  ].join('\n');

  const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `LMSTICK_Teacher_Attendance_${selectedDate.value}.csv`;
  a.click();
};

onMounted(() => {
  fetchTeacherAttendance();
});
</script>

