<template>
  <div class="space-y-6 font-khmer">
    <!-- Header with Extra Action Slot for Leave Request Link -->
    <PageHeader 
      :title="t('attendance.title')" 
      :breadcrumb="t('nav.attendance')" 
      :actionLabel="canTakeAttendance ? 'កត់ត្រាវត្តមានប្រចាំថ្ងៃ' : ''"
      @action="openMarkAttendanceModal"
    >
      <template #extraActions>
        <button 
          @click="openLeaveLinkModal" 
          type="button"
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-full shadow-md shadow-indigo-600/20 transition-all hover:shadow-lg hover:shadow-indigo-600/30 active:scale-95 cursor-pointer"
        >
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
          </svg>
          <span>🔗 តំណភ្ជាប់សុំច្បាប់</span>
        </button>
      </template>
    </PageHeader>

    <!-- Sub Tabs with Badge for Pending Leave Requests -->
    <TabNav 
      v-model="activeTab" 
      :tabs="availableTabs"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="t('attendance.totalPresent')" :value="stats.present" />
      <StatCard :title="t('attendance.totalLate')" :value="stats.late" />
      <StatCard :title="t('attendance.totalAbsent')" :value="stats.absent" />
      <StatCard :title="'ច្បាប់/សម្រាកសរុប'" :value="stats.holiday" />
    </div>

    <!-- TAB 1: Monthly Student Attendance Sheet (All students by days 1 to 28/29/30/31) -->
    <div v-if="activeTab === 'matrix'" class="space-y-4">
      <!-- Advanced Filter Bar / Toolbar (By Class, Academic Year, Month, and Date) -->
      <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs space-y-3">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 text-xs">
          
          <!-- Left: Legend -->
          <div class="flex flex-wrap items-center gap-3">
            <span class="font-medium text-slate-700">វត្តមាន: <span class="font-bold text-emerald-600 text-sm">✓</span></span>
            <span class="font-medium text-slate-700">ច្បាប់: <span class="font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">H</span></span>
            <span class="font-medium text-slate-700">អវត្តមាន: <span class="font-bold text-rose-600 bg-rose-100 px-1.5 py-0.5 rounded">A</span></span>
            <span class="font-medium text-slate-700">មកយឺត: <span class="font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded">L</span></span>
            <span class="font-medium text-slate-400">ថ្ងៃអាទិត្យ: <span class="font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">-</span></span>
          </div>

          <!-- Quick Actions & Toggle -->
          <div class="flex items-center gap-2 flex-wrap">
            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 cursor-pointer transition">
              <input type="checkbox" v-model="onlyShowAbsentStudents" class="rounded text-indigo-600 focus:ring-0">
              <span>បង្ហាញតែអ្នកមានឈប់/ច្បាប់</span>
            </label>

            <button 
              @click="activeTab = 'leaves'"
              type="button" 
              class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition cursor-pointer"
              title="ពិនិត្យសំណើសុំច្បាប់"
            >
              <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
              <span>សំណើសុំច្បាប់ ({{ pendingLeavesCount }})</span>
            </button>
          </div>
        </div>

        <!-- Filter Selectors Row: Class, Academic Year, Month -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-100 text-xs">
          <!-- 1. Class Dropdown -->
          <div>
            <label class="block font-bold text-slate-600 mb-1">ថ្នាក់រៀន:</label>
            <select 
              v-model="selectedClass" 
              @change="fetchAttendanceData" 
              :disabled="isStudent"
              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-indigo-700 font-bold rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 disabled:bg-slate-100 disabled:cursor-not-allowed"
            >
              <option v-for="c in classesList" :key="c" :value="c">ថ្នាក់ {{ c }}</option>
            </select>
          </div>

          <!-- 2. Academic Year Dropdown -->
          <div>
            <label class="block font-bold text-slate-600 mb-1">ឆ្នាំសិក្សា:</label>
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
              ខែសិក្សា ({{ daysInActiveMonth }} ថ្ងៃ):
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

      <!-- Master Monthly Student Attendance Roster Table -->
      <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
          <div>
            <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
              <span>បញ្ជីវត្តមានសិស្សប្រចាំខែ {{ getMonthKhmerLabel(activeMonthKey) }} • ថ្នាក់ {{ selectedClass }}</span>
              <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-700">
                សរុប {{ displayedStudentsList.length }} នាក់
              </span>
            </h4>
            <p class="text-[11px] text-slate-500 mt-0.5">
              គណនាពីថ្ងៃទី ១ ដល់ថ្ងៃទី {{ daysInActiveMonth }} • បើអត់ឈប់គូស «✓» • បើឈប់គូស «H» (ច្បាប់), «A» (អវត្តមាន), «L» (យឺត)
            </p>
          </div>

          <div class="text-[11px] text-slate-500">
            <span v-if="onlyShowAbsentStudents" class="text-rose-600 font-bold">
              កំពុងបង្ហាញតែសិស្សដែលមានច្បាប់/អវត្តមាន
            </span>
            <span v-else>
              បង្ហាញសិស្សទាំងអស់ក្នុងថ្នាក់
            </span>
          </div>
        </div>

        <!-- Loading State for Monthly Roster -->
        <div v-if="isLoading && studentsList.length === 0" class="py-16 text-center text-slate-500 font-khmer">
          <div class="flex flex-col items-center justify-center gap-3">
            <div class="relative w-9 h-9">
              <div class="w-9 h-9 rounded-full border-3 border-blue-100"></div>
              <div class="absolute inset-0 w-9 h-9 rounded-full border-3 border-blue-600 border-t-transparent animate-spin"></div>
            </div>
            <p class="text-xs font-bold text-slate-700">កំពុងទាញយកទិន្នន័យវត្តមានប្រចាំខែ...</p>
            <p class="text-[11px] text-slate-400">សូមរង់ចាំបន្តិច ប្រព័ន្ធកំពុងទាញយកទិន្នន័យពី Server...</p>
          </div>
        </div>

        <div v-else-if="displayedStudentsList.length === 0" class="py-16 text-center text-slate-400 text-xs">
          <div class="flex flex-col items-center justify-center gap-2">
            <span class="text-3xl">🎉</span>
            <span class="font-bold text-emerald-600 text-sm">ពុំមានសិស្សណាមានអវត្តមាន ឬសុំច្បាប់ក្នុងខែនេះឡើយ (វត្តមាន ១០០%)</span>
            <button @click="onlyShowAbsentStudents = false" class="text-indigo-600 font-bold hover:underline">
              ចុចទីនេះដើម្បីបង្ហាញឈ្មោះសិស្សទាំងអស់ក្នុងថ្នាក់
            </button>
          </div>
        </div>

        <div v-else class="overflow-x-auto max-h-[70vh]">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-100 text-slate-700 font-bold sticky top-0 z-20 border-b border-slate-200">
              <tr>
                <th class="py-2.5 px-3 w-10 text-center sticky left-0 bg-slate-100 z-30 shadow-xs border-r border-slate-200">#</th>
                <th class="py-2.5 px-3 min-w-[150px] sticky left-10 bg-slate-100 z-30 shadow-xs border-r border-slate-200">ឈ្មោះសិស្ស</th>
                <th class="py-2.5 px-2.5 w-14 text-center sticky left-[190px] bg-slate-100 z-30 shadow-xs border-r border-slate-200">ភេទ</th>

                <!-- Dynamic Days Columns: 1 to 28, 29, 30, or 31 -->
                <th 
                  v-for="d in monthDaysList" 
                  :key="d.day" 
                  class="py-2.5 px-1 min-w-[32px] text-center border-r border-slate-200/80 font-mono text-[11px]"
                  :class="[
                    d.isSunday ? 'bg-slate-200/80 text-rose-600 font-bold' : '',
                    d.dateStr === selectedDate ? 'bg-indigo-100 text-indigo-700 font-bold' : ''
                  ]"
                  :title="`${d.dateStr} (${d.isSunday ? 'អាទិត្យ' : 'ថ្ងៃសិក្សា'})`"
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
                v-for="(st, idx) in displayedStudentsList" 
                :key="st.student_id" 
                class="hover:bg-slate-50/80 transition-colors"
              >
                <!-- Sticky Info Columns -->
                <td class="py-2 px-3 text-center font-mono font-bold text-slate-400 sticky left-0 bg-white z-10 border-r border-slate-100">
                  {{ idx + 1 }}
                </td>
                <td class="py-2 px-3 font-bold text-slate-800 sticky left-10 bg-white z-10 border-r border-slate-100 whitespace-nowrap">
                  <div>{{ st.name }}</div>
                  <div class="text-[10px] font-mono text-slate-400 font-normal">{{ st.student_id_card }}</div>
                </td>
                <td class="py-2 px-2.5 text-center text-slate-600 sticky left-[190px] bg-white z-10 border-r border-slate-100 text-[11px]">
                  {{ st.gender === 'female' ? 'ស្រី' : 'ប្រុស' }}
                </td>

                <!-- Days Status Columns (✓ for present, H for leave, A for absent, L for late) -->
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
                      getStudentDayStatus(st.student_id, d.dateStr, d.isSunday).class
                    ]"
                    :title="getStudentDayStatus(st.student_id, d.dateStr, d.isSunday).remark || getStudentDayStatus(st.student_id, d.dateStr, d.isSunday).label"
                  >
                    {{ getStudentDayStatus(st.student_id, d.dateStr, d.isSunday).char }}
                  </span>
                </td>

                <!-- Summary Counts -->
                <td class="py-2 px-2 text-center font-bold font-mono text-emerald-700 bg-emerald-50/40 border-r border-slate-100">
                  {{ getStudentSummary(st.student_id).presentCount }}
                </td>
                <td class="py-2 px-2 text-center font-bold font-mono text-emerald-800 bg-emerald-100/40 border-r border-slate-100">
                  <span :class="{ 'font-black text-emerald-700 bg-emerald-100 px-1 rounded': getStudentSummary(st.student_id).leaveCount > 0 }">
                    {{ getStudentSummary(st.student_id).leaveCount }}
                  </span>
                </td>
                <td class="py-2 px-2 text-center font-bold font-mono text-rose-700 bg-rose-50/40 border-r border-slate-100">
                  <span :class="{ 'font-black text-rose-700 bg-rose-100 px-1 rounded': getStudentSummary(st.student_id).absentCount > 0 }">
                    {{ getStudentSummary(st.student_id).absentCount }}
                  </span>
                </td>
                <td class="py-2 px-2 text-center font-bold font-mono text-amber-700 bg-amber-50/40">
                  <span :class="{ 'font-black text-amber-700 bg-amber-100 px-1 rounded': getStudentSummary(st.student_id).lateCount > 0 }">
                    {{ getStudentSummary(st.student_id).lateCount }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 2: Daily Attendance Sheet for Class (Teachers & Admins Only) -->
    <div v-else-if="activeTab === 'daily' && canTakeAttendance" class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
        <div>
          <h3 class="font-bold text-slate-800 text-base">បញ្ជីវត្តមានសិស្សប្រចាំថ្ងៃ</h3>
          <p class="text-xs text-slate-500">ស្រង់វត្តមានសិស្សជាក់ស្តែងក្នុងថ្នាក់ {{ selectedClass }}</p>
        </div>
        <div class="flex items-center gap-2">
          <button @click="markAllStudents('present')" type="button" class="px-3.5 py-2 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-bold hover:bg-emerald-100 transition cursor-pointer">
            ✓ វត្តមានទាំងអស់
          </button>
          <button @click="submitSaveAttendance" :disabled="isSaving" type="button" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm disabled:opacity-50 cursor-pointer">
            <span v-if="isSaving">កំពុងរក្សាទុក...</span>
            <span v-else>💾 រក្សាទុកវត្តមាន</span>
          </button>
        </div>
      </div>

      <!-- Advanced Filters Row for Daily Sheet -->
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200/70 text-xs">
        <div>
          <label class="block font-bold text-slate-700 mb-1">ថ្នាក់រៀន</label>
          <select v-model="selectedClass" @change="fetchAttendanceData" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl font-bold text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            <option v-for="c in classesList" :key="c" :value="c">ថ្នាក់ {{ c }}</option>
          </select>
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">ឆ្នាំសិក្សា</label>
          <select v-model="selectedAcademicYear" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            <option v-for="y in academicYearsList" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">ខែ</label>
          <select v-model="selectedMonth" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            <option value="all">គ្រប់ខែ</option>
            <option v-for="m in monthOptions" :key="m.key" :value="m.key">{{ m.label }}</option>
          </select>
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">កាលបរិច្ឆេទ</label>
          <input v-model="selectedDate" @change="fetchAttendanceData" type="date" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
        </div>
      </div>

      <!-- Student Sheet Table -->
      <div v-if="isLoading && studentsList.length === 0" class="py-16 text-center text-slate-500 font-khmer">
        <div class="flex flex-col items-center justify-center gap-3">
          <div class="relative w-9 h-9">
            <div class="w-9 h-9 rounded-full border-3 border-blue-100"></div>
            <div class="absolute inset-0 w-9 h-9 rounded-full border-3 border-blue-600 border-t-transparent animate-spin"></div>
          </div>
          <p class="text-xs font-bold text-slate-700">កំពុងទាញយកទិន្នន័យវត្តមានសិស្ស...</p>
          <p class="text-[11px] text-slate-400">សូមរង់ចាំបន្តិច...</p>
        </div>
      </div>
      <div v-else-if="studentsList.length === 0" class="py-12 text-center text-slate-400">
        <div class="flex flex-col items-center justify-center gap-2">
          <span class="text-3xl">📋</span>
          <span>មិនទាន់មានសិស្សនៅក្នុងថ្នាក់ {{ selectedClass }} ឡើយ។</span>
        </div>
      </div>
      <div v-else class="overflow-x-auto border border-slate-200/80 rounded-2xl">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="bg-slate-100/90 text-slate-700 uppercase font-bold border-b border-slate-200">
              <th class="py-3 px-4">អត្តលេខ</th>
              <th class="py-3 px-4">ឈ្មោះសិស្ស</th>
              <th class="py-3 px-4">ភេទ</th>
              <th class="py-3 px-4 text-center">ស្ថានភាពវត្តមាន</th>
              <th class="py-3 px-4">សម្គាល់</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="st in studentsList" :key="st.student_id" class="hover:bg-slate-50 transition-colors">
              <td class="py-3 px-4 font-mono font-bold text-slate-600">{{ st.student_id_card }}</td>
              <td class="py-3 px-4 font-bold text-slate-800">{{ st.name }}</td>
              <td class="py-3 px-4 text-slate-600">{{ st.gender === 'female' ? 'ស្រី' : 'ប្រុស' }}</td>
              <td class="py-3 px-4 text-center">
                <div class="inline-flex rounded-xl bg-slate-100 p-0.5 border border-slate-200">
                  <button 
                    type="button"
                    @click="attendanceMap[st.student_id] = 'present'"
                    :class="[
                      'px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer',
                      attendanceMap[st.student_id] === 'present' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
                    ]"
                  >
                    វត្តមាន
                  </button>
                  <button 
                    type="button"
                    @click="attendanceMap[st.student_id] = 'late'"
                    :class="[
                      'px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer',
                      attendanceMap[st.student_id] === 'late' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
                    ]"
                  >
                    យឺត
                  </button>
                  <button 
                    type="button"
                    @click="attendanceMap[st.student_id] = 'absent'"
                    :class="[
                      'px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer',
                      attendanceMap[st.student_id] === 'absent' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
                    ]"
                  >
                    អវត្តមាន
                  </button>
                  <button 
                    type="button"
                    @click="attendanceMap[st.student_id] = 'excused'"
                    :class="[
                      'px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer',
                      attendanceMap[st.student_id] === 'excused' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
                    ]"
                  >
                    ច្បាប់
                  </button>
                </div>
              </td>
              <td class="py-3 px-4">
                <input 
                  type="text" 
                  v-model="remarksMap[st.student_id]" 
                  placeholder="មូលហេតុ (បើមាន)..." 
                  class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs w-52 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 3: Student Leave Requests (Pending Leaves Approval System) -->
    <div v-else-if="activeTab === 'leaves'" class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-5">
      <!-- Leave Requests Header & Filter Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
        <div>
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span>សំណើសុំច្បាប់ឈប់សម្រាក</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
              រង់ចាំអនុម័ត: {{ pendingLeavesCount }}
            </span>
          </h3>
          <p class="text-xs text-slate-500">ពិនិត្យ ឯកភាព ឬបដិសេធពាក្យសុំច្បាប់របស់សិស្សតាមអនឡាញ</p>
        </div>

        <div class="flex items-center gap-3">
          <button 
            @click="openLeaveLinkModal" 
            type="button" 
            class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer"
          >
            <span>🔗 ចែករំលែក Link សុំច្បាប់</span>
          </button>
          <button 
            @click="fetchLeaveRequests" 
            type="button" 
            class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer"
            title="Reload"
          >
            <i class="fa-solid fa-rotate-right"></i>
          </button>
        </div>
      </div>

      <!-- Filters Toolbar -->
      <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 text-xs">
        <div class="flex flex-wrap items-center gap-2">
          <span class="font-bold text-slate-700">ស្ថានភាព:</span>
          <button 
            v-for="st in [
              { id: 'all', label: 'ទាំងអស់' },
              { id: 'pending', label: 'រង់ចាំអនុម័ត' },
              { id: 'approved', label: 'បានអនុម័ត' },
              { id: 'rejected', label: 'បានបដិសេធ' }
            ]" 
            :key="st.id" 
            type="button"
            @click="leaveFilterStatus = st.id; fetchLeaveRequests()"
            :class="[
              'px-3 py-1.5 rounded-lg font-bold transition cursor-pointer',
              leaveFilterStatus === st.id 
                ? 'bg-indigo-600 text-white shadow-xs' 
                : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'
            ]"
          >
            {{ st.label }}
          </button>
        </div>

        <div class="flex items-center gap-2">
          <span class="font-bold text-slate-700">ថ្នាក់:</span>
          <select 
            v-model="leaveFilterClass" 
            @change="fetchLeaveRequests" 
            class="px-3 py-1.5 bg-white border border-slate-200 text-indigo-700 font-bold rounded-xl focus:outline-none"
          >
            <option value="all">-- គ្រប់ថ្នាក់ --</option>
            <option v-for="c in classesList" :key="c" :value="c">ថ្នាក់ {{ c }}</option>
          </select>
        </div>
      </div>

      <!-- Leave Requests Table -->
      <div v-if="isLoadingLeaves" class="py-12 text-center text-slate-500 text-xs">
        <div class="w-8 h-8 border-3 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
        <span>កំពុងទាញយកទិន្នន័យសំណើសុំច្បាប់...</span>
      </div>

      <div v-else-if="leaveRequestsList.length === 0" class="py-12 text-center text-slate-400 text-xs">
        <div class="flex flex-col items-center justify-center gap-2">
          <span class="text-3xl">📭</span>
          <span>ពុំមានសំណើសុំច្បាប់ក្នុងលក្ខខណ្ឌនេះឡើយ។</span>
        </div>
      </div>

      <div v-else class="overflow-x-auto border border-slate-200/80 rounded-2xl">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="bg-slate-100/90 text-slate-700 uppercase font-bold border-b border-slate-200">
              <th class="py-3 px-3 w-10 text-center">#</th>
              <th class="py-3 px-4 min-w-[140px]">ឈ្មោះសិស្ស</th>
              <th class="py-3 px-3 w-20 text-center">ថ្នាក់</th>
              <th class="py-3 px-4 min-w-[160px]">រយៈពេលសុំច្បាប់</th>
              <th class="py-3 px-4 min-w-[180px]">ប្រភេទ & មូលហេតុ</th>
              <th class="py-3 px-3 w-28">លេខទូរស័ព្ទ</th>
              <th class="py-3 px-3 w-24 text-center">ឯកសារ</th>
              <th class="py-3 px-3 w-28 text-center">ស្ថានភាព</th>
              <th class="py-3 px-4 w-36 text-center">សកម្មភាព</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="(req, idx) in leaveRequestsList" :key="req.id" class="hover:bg-slate-50 transition-colors">
              <td class="py-3 px-3 text-center font-mono font-bold text-slate-400">{{ idx + 1 }}</td>
              <td class="py-3 px-4">
                <div class="font-bold text-slate-800">{{ req.student_name }}</div>
                <div class="text-[11px] font-mono text-slate-400">{{ req.student_code || 'N/A' }}</div>
              </td>
              <td class="py-3 px-3 text-center">
                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                  {{ req.class_name }}
                </span>
              </td>
              <td class="py-3 px-4">
                <div class="font-semibold text-slate-800">
                  {{ formatDateKm(req.start_date) }} - {{ formatDateKm(req.end_date) }}
                </div>
                <div class="text-[11px] text-indigo-600 font-bold">
                  {{ req.days_count }} ថ្ងៃ
                </div>
              </td>
              <td class="py-3 px-4">
                <div class="font-bold text-slate-700">{{ req.leave_type }}</div>
                <div class="text-[11px] text-slate-500 truncate max-w-xs" :title="req.reason">{{ req.reason }}</div>
              </td>
              <td class="py-3 px-3 font-mono text-slate-600">{{ req.phone || '-' }}</td>
              <td class="py-3 px-3 text-center">
                <a 
                  v-if="req.attachment_url" 
                  :href="req.attachment_url" 
                  target="_blank" 
                  class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-[11px] font-bold transition"
                >
                  <i class="fa-solid fa-paperclip"></i>
                  <span>មើល</span>
                </a>
                <span v-else class="text-slate-300">-</span>
              </td>
              <td class="py-3 px-3 text-center">
                <span 
                  v-if="req.status === 'pending'" 
                  class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 inline-flex items-center gap-1"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                  រង់ចាំអនុម័ត
                </span>
                <span 
                  v-else-if="req.status === 'approved'" 
                  class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 inline-flex items-center gap-1"
                >
                  <i class="fa-solid fa-check text-[10px]"></i>
                  បានអនុម័ត
                </span>
                <span 
                  v-else-if="req.status === 'rejected'" 
                  class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 inline-flex items-center gap-1"
                >
                  <i class="fa-solid fa-xmark text-[10px]"></i>
                  បានបដិសេធ
                </span>
              </td>
              <td class="py-3 px-4 text-center">
                <div v-if="req.status === 'pending'" class="flex items-center justify-center gap-1.5">
                  <button 
                    @click="approveLeave(req)" 
                    type="button" 
                    class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold shadow-xs transition cursor-pointer"
                    title="អនុម័ត និងកត់ត្រាវត្តមាន E/H"
                  >
                    ✓ អនុម័ត
                  </button>
                  <button 
                    @click="rejectLeave(req)" 
                    type="button" 
                    class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition cursor-pointer"
                    title="បដិសេធ"
                  >
                    ✕ បដិសេធ
                  </button>
                </div>
                <div v-else class="flex items-center justify-center gap-2">
                  <span class="text-[11px] text-slate-400">
                    {{ req.status === 'approved' ? '✓ កត់ត្រារួច' : '✕ បដិសេធរួច' }}
                  </span>
                  <button 
                    @click="restoreLeave(req)" 
                    type="button" 
                    class="px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg text-[10px] font-bold transition cursor-pointer inline-flex items-center gap-1 shadow-2xs"
                    title="យកមកវិញ / ត្រឡប់មកជា Pending វិញ"
                  >
                    <span>⟲ យកមកវិញ</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal 1: Shareable Leave Link & QR Code Modal -->
    <div 
      v-if="showLeaveLinkModal" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs font-khmer transition-opacity"
      @click.self="showLeaveLinkModal = false"
    >
      <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-blue-600 p-6 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl shadow-inner">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
              </svg>
            </div>
            <div>
              <h3 class="font-bold text-base">Link / QR Code សុំច្បាប់ឈប់សម្រាក</h3>
              <p class="text-xs text-indigo-100">សម្រាប់ចែករំលែកទៅកាន់មាតាបិតា និងសិស្សតាម Telegram/Facebook</p>
            </div>
          </div>
          <button 
            @click="showLeaveLinkModal = false" 
            class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition cursor-pointer"
          >
            ✕
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 text-center space-y-5">

          <!-- Target Class Selector for Link -->
          <div class="flex items-center justify-between bg-slate-50 p-3 rounded-2xl border border-slate-200/80 text-xs">
            <span class="font-bold text-slate-700">តំណភ្ជាប់សម្រាប់ថ្នាក់:</span>
            <select 
              v-model="modalSelectedClass" 
              @change="generateQrCode" 
              class="px-3 py-1.5 bg-white border border-slate-300 rounded-xl font-bold text-indigo-700 focus:outline-none"
            >
              <option value="">-- គ្រប់ថ្នាក់ --</option>
              <option v-for="c in classesList" :key="c" :value="c">ថ្នាក់ {{ c }}</option>
            </select>
          </div>
          
          <!-- QR Code Display Box -->
          <div class="p-5 bg-slate-50 border border-slate-200/80 rounded-2xl inline-block mx-auto shadow-inner text-center">
            <div class="text-xs font-bold text-slate-800 mb-2 uppercase tracking-wide">
              សាលារៀន ជំនាន់ថ្មី - ស្កេនដើម្បីសុំច្បាប់
            </div>
            <div class="w-48 h-48 mx-auto bg-white p-2 rounded-xl border border-slate-200 shadow-xs flex items-center justify-center">
              <img v-if="qrDataUrl" :src="qrDataUrl" alt="QR Code" class="w-full h-full object-contain" />
              <div v-else class="text-xs text-slate-400">កំពុងបង្កើត QR Code...</div>
            </div>
            <div class="mt-2 text-[11px] text-slate-500">
              {{ modalSelectedClass ? `តម្រង់ទិសដោយស្វ័យប្រវត្តិចូលថ្នាក់ ${modalSelectedClass}` : 'សិស្សអាចជ្រើសរើសថ្នាក់ដោយខ្លួនឯង' }}
            </div>
          </div>

          <!-- Shareable URL Input with Copy Button -->
          <div class="space-y-1.5 text-left">
            <label class="text-xs font-bold text-slate-700">តំណភ្ជាប់ URL សាធារណៈ:</label>
            <div class="flex items-center gap-2">
              <input 
                type="text" 
                :value="publicLeaveUrl" 
                readonly 
                class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-700 focus:outline-none select-all"
              />
              <button 
                @click="copyShareLink" 
                type="button"
                :class="[
                  'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer',
                  isCopied ? 'bg-emerald-600 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs'
                ]"
              >
                <i v-if="isCopied" class="fa-solid fa-check"></i>
                <i v-else class="fa-regular fa-copy"></i>
                <span>{{ isCopied ? 'បានចម្លង!' : 'ចម្លង Link' }}</span>
              </button>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-center gap-3 pt-2">
            <a 
              :href="publicLeaveUrl" 
              target="_blank" 
              class="px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5"
            >
              <i class="fa-solid fa-arrow-up-right-from-square"></i>
              <span>សាកល្បងបើកមើលទម្រង់</span>
            </a>
            <button 
              @click="showLeaveLinkModal = false" 
              type="button" 
              class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition cursor-pointer"
            >
              បិទផ្ទាំង
            </button>
          </div>

        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onActivated, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';
import axios from 'axios';
import QRCode from 'qrcode';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import StatCard from '../components/StatCard.vue';
import DataTable from '../components/DataTable.vue';
import { sortClassesNaturally } from '../utils/classSort';

const { t } = useI18n();
const route = useRoute();

const currentUser = ref(window.__INITIAL_USER__ || {});
const isStudent = computed(() => {
  const r = (currentUser.value?.role || '').toLowerCase();
  return !currentUser.value?.is_super_admin && !r.includes('admin') && !r.includes('teacher') && !r.includes('គ្រូ');
});
const canTakeAttendance = computed(() => !isStudent.value);

const availableTabs = computed(() => {
  if (isStudent.value) {
    return [
      { id: 'matrix', label: 'ប្រវត្តិវត្តមានរបស់ខ្ញុំ' },
      { 
        id: 'leaves', 
        label: 'សំណើសុំច្បាប់', 
        badge: pendingLeavesCount.value, 
        badgeColor: 'bg-rose-100 text-rose-800' 
      }
    ];
  }
  return [
    { id: 'matrix', label: 'តារាងវត្តមានប្រចាំឆ្នាំ' },
    { id: 'daily', label: 'ស្រង់វត្តមានតាមថ្នាក់' },
    { 
      id: 'leaves', 
      label: 'សំណើសុំច្បាប់', 
      badge: pendingLeavesCount.value, 
      badgeColor: 'bg-rose-100 text-rose-800' 
    }
  ];
});

// Tab state: 'matrix', 'daily', or 'leaves'
const activeTab = ref(route.query?.tab || 'matrix');
watch(() => route.query?.tab, (tab) => {
  if (tab) activeTab.value = tab;
});
const filterStatus = ref('all');
const isLoading = ref(true);

// Advanced Filter Bar States
const selectedClass = ref(currentUser.value?.student_grade || '7A');
const selectedAcademicYear = ref(
  localStorage.getItem('pdt_current_academic_year') ||
  window.__INITIAL_SETTINGS__?.current_academic_year ||
  '2026-2027'
);
const selectedMonth = ref('november');
const selectedDate = ref(new Date().toISOString().slice(0, 10));

const classesList = ref(['7A', '7B', '8A', '8B', '9A', '9B', '10A', '10B', '10C', '10D', '11A', '11B', '12A', '12B', '12C', '12D']);
const academicYearsList = ref(['2025-2026', '2024-2025']);

const studentsList = ref([]);
const attendanceMap = ref({});
const remarksMap = ref({});
const studentAttendanceMap = ref({});
const onlyShowAbsentStudents = ref(false);
const matrixData = ref({});
const matrixViewMode = ref('list'); // 'list' (Absence Log) or 'matrix' (Annual Matrix)
const absenceRecords = ref([]);
const isSaving = ref(false);

const filteredAbsenceRecords = computed(() => {
  return absenceRecords.value.filter(rec => {
    if (filterStatus.value === 'H' && !['excused', 'holiday'].includes(rec.status)) return false;
    if (filterStatus.value === 'A' && rec.status !== 'absent') return false;
    if (filterStatus.value === 'L' && rec.status !== 'late') return false;
    if (filterStatus.value === 'P') return false;

    if (selectedMonth.value && selectedMonth.value !== 'all') {
      if (rec.month !== selectedMonth.value) return false;
    }

    return true;
  });
});

const stats = ref({
  present: 0,
  late: 0,
  absent: 0,
  holiday: 0
});

// Leave Link & QR Modal States
const showLeaveLinkModal = ref(false);
const modalSelectedClass = ref('');
const publicLeaveUrl = ref('');
const qrDataUrl = ref('');
const isCopied = ref(false);

// Leave Requests Management States
const leaveRequestsList = ref([]);
const pendingLeavesCount = ref(0);
const leaveFilterStatus = ref('pending');
const leaveFilterClass = ref('all');
const isLoadingLeaves = ref(false);

const monthOptions = [
  { key: 'january', label: 'មករា' },
  { key: 'february', label: 'កុម្ភៈ' },
  { key: 'march', label: 'មីនា' },
  { key: 'april', label: 'មេសា' },
  { key: 'may', label: 'ឧសភា' },
  { key: 'june', label: 'មិថុនា' },
  { key: 'july', label: 'កក្កដា' },
  { key: 'august', label: 'សីហា' },
  { key: 'september', label: 'កញ្ញា' },
  { key: 'october', label: 'តុលា' },
  { key: 'november', label: 'វិច្ឆិកា' },
  { key: 'december', label: 'ធ្នូ' },
];

const allMonthKeys = [
  'january', 'february', 'march', 'april', 'may', 'june',
  'july', 'august', 'september', 'october', 'november', 'december'
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
  if (selectedDate.value) {
    const mIdx = new Date(selectedDate.value).getMonth();
    return allMonthKeys[mIdx] || 'november';
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

const getStudentDayStatus = (studentId, dateStr, isSunday) => {
  if (isSunday) return { char: '-', label: 'ថ្ងៃអាទិត្យ', class: 'text-slate-300 font-bold' };
  const rec = studentAttendanceMap.value[studentId]?.[dateStr];
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

const getStudentSummary = (studentId) => {
  let presentCount = 0, leaveCount = 0, absentCount = 0, lateCount = 0;
  for (const d of monthDaysList.value) {
    const st = getStudentDayStatus(studentId, d.dateStr, d.isSunday);
    if (st.char === '✓') presentCount++;
    else if (st.char === 'H') leaveCount++;
    else if (st.char === 'A') absentCount++;
    else if (st.char === 'L') lateCount++;
  }
  return { presentCount, leaveCount, absentCount, lateCount };
};

const displayedStudentsList = computed(() => {
  let list = studentsList.value;
  if (isStudent.value) {
    const studentId = currentUser.value?.student_id;
    if (studentId) {
      const match = list.filter(st => st.student_id === studentId);
      if (match.length > 0) return match;
    }
  }
  if (onlyShowAbsentStudents.value) {
    list = list.filter(st => {
      const sum = getStudentSummary(st.student_id);
      return (sum.leaveCount + sum.absentCount + sum.lateCount) > 0;
    });
  }
  return [...list].sort((a, b) => {
    const nameA = a.name || a.name_khmer || '';
    const nameB = b.name || b.name_khmer || '';
    return nameA.localeCompare(nameB, 'km');
  });
});

const getMonthKhmerLabel = (key) => {
  const found = monthOptions.find(m => m.key === key);
  return found ? found.label : key;
};

const displayedMonthKeys = computed(() => {
  if (selectedMonth.value && selectedMonth.value !== 'all') {
    return [selectedMonth.value];
  }
  return allMonthKeys;
});

const getMatrixStatus = (day, monthKey) => {
  let val = '-';
  if (matrixData.value && matrixData.value[day] && matrixData.value[day][monthKey]) {
    val = matrixData.value[day][monthKey];
  }

  if (filterStatus.value !== 'all' && val !== filterStatus.value) {
    return '-';
  }
  return val;
};

const getStatusClass = (status) => {
  switch (status) {
    case 'P': return 'text-slate-600 font-semibold';
    case 'A': return 'text-rose-600 font-black';
    case 'L': return 'text-slate-900 font-black';
    case 'H': return 'text-emerald-600 font-black bg-emerald-50 px-1 py-0.5 rounded';
    case 'E': return 'text-emerald-600 font-black bg-emerald-50 px-1 py-0.5 rounded';
    case '-': return 'text-slate-200';
    default: return 'text-slate-600';
  }
};

let isFetchingAttendance = false;
let lastAttendanceFetchTime = 0;

const fetchAttendanceData = async (force = false) => {
  const now = Date.now();
  if (isFetchingAttendance || (!force && now - lastAttendanceFetchTime < 1500 && studentsList.value.length > 0)) {
    return;
  }
  isFetchingAttendance = true;
  lastAttendanceFetchTime = now;

  if (studentsList.value.length === 0) {
    isLoading.value = true;
  }
  try {
    const res = await axios.get('/api/attendance', {
      params: {
        class: selectedClass.value,
        date: selectedDate.value,
      }
    });
    if (res.data) {
      if (res.data.stats) stats.value = res.data.stats;
      if (res.data.classes && res.data.classes.length > 0) classesList.value = sortClassesNaturally(res.data.classes);
      if (res.data.currentAcademicYear) {
        selectedAcademicYear.value = res.data.currentAcademicYear;
        localStorage.setItem('pdt_current_academic_year', res.data.currentAcademicYear);
      }
      if (res.data.academicYears && res.data.academicYears.length > 0) academicYearsList.value = res.data.academicYears;
      if (res.data.matrix) matrixData.value = res.data.matrix;
      if (res.data.absence_records) absenceRecords.value = res.data.absence_records;
      if (res.data.student_attendance_map) studentAttendanceMap.value = res.data.student_attendance_map;
      const raw = res.data.students || [];
      studentsList.value = [...raw].sort((a, b) => {
        const nameA = a.name || a.name_khmer || '';
        const nameB = b.name || b.name_khmer || '';
        return nameA.localeCompare(nameB, 'km');
      });
      attendanceMap.value = {};
      remarksMap.value = {};
      studentsList.value.forEach(st => {
        attendanceMap.value[st.student_id] = st.status || 'present';
        remarksMap.value[st.student_id] = st.remark || '';
      });
    }
  } catch (err) {
    console.error('Failed to fetch attendance:', err);
  } finally {
    isFetchingAttendance = false;
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchAttendanceData();
});

onActivated(() => {
  fetchAttendanceData();
});

// Leave Link & QR Generator
const openLeaveLinkModal = async () => {
  modalSelectedClass.value = selectedClass.value || '';
  await generateQrCode();
  showLeaveLinkModal.value = true;
};

const generateQrCode = async () => {
  const origin = window.location.origin;
  const query = modalSelectedClass.value ? `?class=${encodeURIComponent(modalSelectedClass.value)}` : '';
  publicLeaveUrl.value = `${origin}/leave-request${query}`;
  isCopied.value = false;

  try {
    qrDataUrl.value = await QRCode.toDataURL(publicLeaveUrl.value, {
      width: 260,
      margin: 2,
      color: {
        dark: '#4338ca',
        light: '#ffffff'
      }
    });
  } catch (err) {
    console.error('Failed to generate QR:', err);
  }
};

const copyShareLink = async () => {
  try {
    await navigator.clipboard.writeText(publicLeaveUrl.value);
    isCopied.value = true;
    setTimeout(() => {
      isCopied.value = false;
    }, 2500);
  } catch (e) {
    alert('មិនអាចចម្លងតំណភ្ជាប់បានទេ។ សូមជ្រើសរើសចម្លងដោយដៃ។');
  }
};

// Fetch Leave Requests for Tab 3
const fetchLeaveRequests = async () => {
  isLoadingLeaves.value = true;
  try {
    const res = await axios.get('/api/leave-requests', {
      params: {
        class: leaveFilterClass.value,
        status: leaveFilterStatus.value,
      }
    });
    if (res.data) {
      leaveRequestsList.value = res.data.requests || [];
      pendingLeavesCount.value = res.data.pending_count || 0;
    }
  } catch (err) {
    console.error('Failed to fetch leave requests:', err);
  } finally {
    isLoadingLeaves.value = false;
  }
};

// Approve Leave Request
const approveLeave = async (req) => {
  if (!confirm(`តើអ្នកពិតជាចង់អនុម័តសំណើសុំច្បាប់របស់សិស្ស ${req.student_name} (${req.days_count} ថ្ងៃ) មែនទេ?`)) {
    return;
  }

  try {
    const res = await axios.post(`/api/leave-requests/${req.id}/approve`);
    alert(res.data.message || 'បានអនុម័តដោយជោគជ័យ!');
    await fetchLeaveRequests();
    await fetchAttendanceData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការអនុម័តសំណើ!');
  }
};

// Reject Leave Request
const rejectLeave = async (req) => {
  const reason = prompt('សូមបញ្ចូលមូលហេតុនៃការបដិសេធ:', 'ពុំមានមូលហេតុសមរម្យ');
  if (reason === null) return;

  try {
    const res = await axios.post(`/api/leave-requests/${req.id}/reject`, { reason });
    alert(res.data.message || 'បានបដិសេធសំណើសុំច្បាប់!');
    await fetchLeaveRequests();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការបដិសេធ!');
  }
};

// Restore Leave Request (Undo Reject/Approve)
const restoreLeave = async (req) => {
  if (!confirm(`តើអ្នកចង់ស្ដារសំណើសុំច្បាប់របស់សិស្ស ${req.student_name} មកជា «រង់ចាំពិនិត្យ» វិញមែនទេ?`)) return;

  try {
    const res = await axios.post(`/api/leave-requests/${req.id}/restore`);
    alert(res.data.message || 'បានស្ដារសំណើសុំច្បាប់ជោគជ័យ!');
    await fetchLeaveRequests();
    await fetchAttendanceData();
  } catch (err) {
    alert(err.response?.data?.message || 'មិនអាចស្ដារសំណើបានទេ សូមព្យាយាមម្តងទៀត!');
  }
};

const formatDateKm = (dStr) => {
  if (!dStr) return '';
  const d = new Date(dStr);
  return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`;
};

onMounted(() => {
  fetchAttendanceData();
  fetchLeaveRequests();
});

const openMarkAttendanceModal = () => {
  activeTab.value = 'daily';
};

const markAllStudents = (status) => {
  studentsList.value.forEach(st => {
    attendanceMap.value[st.student_id] = status;
  });
};

const submitSaveAttendance = async () => {
  if (studentsList.value.length === 0) return;
  isSaving.value = true;
  try {
    const res = await axios.post('/api/attendance', {
      class_name: selectedClass.value,
      date: selectedDate.value,
      attendance: attendanceMap.value,
      remarks: remarksMap.value,
    });
    alert(res.data.message || 'បានរក្សាទុកវត្តមានដោយជោគជ័យ!');
    await fetchAttendanceData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការរក្សាទុកវត្តមាន!');
  } finally {
    isSaving.value = false;
  }
};

const handlePrint = () => window.print();

const handleExport = () => {
  const csv = [
    ['អត្តលេខ', 'ឈ្មោះសិស្ស', 'ថ្នាក់', 'កាលបរិច្ឆេទ', 'ស្ថានភាពវត្តមាន', 'សម្គាល់'].join(','),
    ...studentsList.value.map(st => [
      `"${st.student_id_card}"`,
      `"${st.name}"`,
      `"${selectedClass.value}"`,
      `"${selectedDate.value}"`,
      `"${attendanceMap.value[st.student_id] || 'present'}"`,
      `"${remarksMap.value[st.student_id] || ''}"`
    ].join(','))
  ].join('\n');

  const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `Attendance_${selectedClass.value}_${selectedDate.value}.csv`;
  a.click();
};
</script>
