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
        <!-- 2. Clean Button (No Duplicate Icon) -->
        <button 
          v-if="isStudent"
          @click="openStudentLeaveForm" 
          type="button"
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-semibold rounded-full shadow-md shadow-blue-600/20 transition-all hover:shadow-lg hover:shadow-blue-600/30 active:scale-95 cursor-pointer"
        >
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
          <span>ស្នើសុំច្បាប់</span>
        </button>

        <button 
          v-else
          @click="openLeaveLinkModal" 
          type="button"
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-full shadow-md shadow-indigo-600/20 transition-all hover:shadow-lg hover:shadow-indigo-600/30 active:scale-95 cursor-pointer"
        >
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
          </svg>
          <span>តំណភ្ជាប់សុំច្បាប់</span>
        </button>
      </template>
    </PageHeader>

    <!-- 2. Segmented Tabs: Balanced 2-column Grid on Mobile/Desktop for Students (50% / 50%) -->
    <div v-if="isStudent" class="grid grid-cols-2 w-full p-1 bg-slate-100 rounded-full border border-slate-200/70 shadow-xs">
      <button
        v-for="tab in availableTabs"
        :key="tab.id"
        type="button"
        @click="activeTab = tab.id"
        :class="[
          'py-2 px-3 rounded-full text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer',
          activeTab === tab.id
            ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
            : 'text-slate-600 hover:text-slate-900 hover:bg-white/50'
        ]"
      >
        <span>{{ tab.label }}</span>
        <span 
          v-if="tab.badge !== undefined && tab.badge !== null && tab.badge > 0" 
          :class="[
            'px-2 py-0.5 rounded-full text-[10px] font-bold',
            activeTab === tab.id 
              ? 'bg-white/25 text-white' 
              : (tab.badgeColor || 'bg-rose-100 text-rose-800')
          ]"
        >
          {{ tab.badge }}
        </span>
      </button>
    </div>

    <!-- For Teachers / Admins: standard TabNav -->
    <TabNav 
      v-else
      v-model="activeTab" 
      :tabs="availableTabs"
    />

    <!-- 1. Compact 4-Column Summary Strip (Save Screen Space) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
      <!-- 1. វត្តមានសរុប -->
      <div class="bg-white rounded-xl sm:rounded-2xl p-2.5 sm:p-3.5 border border-emerald-100/90 bg-gradient-to-br from-emerald-50/40 to-white shadow-2xs flex items-center justify-between">
        <div class="min-w-0 pr-1">
          <p class="text-[10px] sm:text-xs font-semibold text-emerald-800 truncate">វត្តមានសរុប</p>
          <h3 class="text-base sm:text-xl font-extrabold text-emerald-700 mt-0.5 tracking-tight">{{ dynamicStats.present }} ថ្ងៃ</h3>
        </div>
        <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs sm:text-sm font-bold shrink-0">
          ✓
        </div>
      </div>

      <!-- 2. យឺតសរុប -->
      <div class="bg-white rounded-xl sm:rounded-2xl p-2.5 sm:p-3.5 border border-amber-100/90 bg-gradient-to-br from-amber-50/40 to-white shadow-2xs flex items-center justify-between">
        <div class="min-w-0 pr-1">
          <p class="text-[10px] sm:text-xs font-semibold text-amber-800 truncate">យឺតសរុប</p>
          <h3 class="text-base sm:text-xl font-extrabold text-amber-700 mt-0.5 tracking-tight">{{ dynamicStats.late }} ថ្ងៃ</h3>
        </div>
        <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xs sm:text-sm font-bold shrink-0">
          L
        </div>
      </div>

      <!-- 3. អវត្តមានសរុប -->
      <div class="bg-white rounded-xl sm:rounded-2xl p-2.5 sm:p-3.5 border border-rose-100/90 bg-gradient-to-br from-rose-50/40 to-white shadow-2xs flex items-center justify-between">
        <div class="min-w-0 pr-1">
          <p class="text-[10px] sm:text-xs font-semibold text-rose-800 truncate">អវត្តមានសរុប</p>
          <h3 class="text-base sm:text-xl font-extrabold text-rose-700 mt-0.5 tracking-tight">{{ dynamicStats.absent }} ថ្ងៃ</h3>
        </div>
        <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-xs sm:text-sm font-bold shrink-0">
          A
        </div>
      </div>

      <!-- 4. ច្បាប់/សម្រាកសរុប -->
      <div class="bg-white rounded-xl sm:rounded-2xl p-2.5 sm:p-3.5 border border-blue-100/90 bg-gradient-to-br from-blue-50/40 to-white shadow-2xs flex items-center justify-between">
        <div class="min-w-0 pr-1">
          <p class="text-[10px] sm:text-xs font-semibold text-blue-800 truncate">ច្បាប់/សម្រាក</p>
          <h3 class="text-base sm:text-xl font-extrabold text-blue-700 mt-0.5 tracking-tight">{{ dynamicStats.holiday }} ថ្ងៃ</h3>
        </div>
        <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xs sm:text-sm font-bold shrink-0">
          H
        </div>
      </div>
    </div>

    <!-- TAB 1: Monthly Student Attendance Sheet (All students by days 1 to 28/29/30/31) -->
    <div v-if="activeTab === 'matrix'" class="space-y-4">
      <!-- 3. Compact Filter Bar (Side-by-side Academic Year & Month on Mobile for Students) -->
      <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
        <!-- For Students: 2-column Grid (Side-by-Side to save vertical space) -->
        <div v-if="isStudent" class="grid grid-cols-2 gap-3 text-xs">
          <!-- 1. Academic Year Dropdown -->
          <div>
            <label class="block font-bold text-slate-600 mb-1">ឆ្នាំសិក្សា:</label>
            <select 
              v-model="selectedAcademicYear" 
              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-700 font-semibold rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
            >
              <option v-for="y in academicYearsList" :key="y" :value="y">{{ y }}</option>
            </select>
          </div>

          <!-- 2. Month Dropdown -->
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

        <!-- For Teachers / Admin: 3-column Grid (Class, Academic Year, Month) -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
          <!-- 1. Class Dropdown -->
          <div>
            <label class="block font-bold text-slate-600 mb-1">ថ្នាក់រៀន:</label>
            <select 
              v-model="selectedClass" 
              @change="fetchAttendanceData" 
              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-indigo-700 font-bold rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
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

          <!-- 3. Month Dropdown -->
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

      <!-- Master Monthly Student Attendance View (Integrated Legend, Mobile 7-Col Calendar Grid & Desktop Table) -->
      <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
        <!-- Attendance Card Header with Integrated Compact Legend -->
        <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2.5 text-xs">
          <div>
            <h4 class="font-bold text-slate-800 text-xs sm:text-sm flex items-center gap-2">
              <span>បញ្ជីវត្តមានខែ {{ getMonthKhmerLabel(activeMonthKey) }} • ថ្នាក់ {{ selectedClass }}</span>
              <span v-if="!isStudent" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700">
                សរុប {{ displayedStudentsList.length }} នាក់
              </span>
            </h4>
            <p v-if="isStudent && showOnlyMyAttendance" class="text-[11px] text-indigo-600 font-semibold mt-0.5">
              វត្តមានផ្ទាល់ខ្លួន៖ <strong>{{ currentStudent?.name || currentUser.name }}</strong> ({{ currentStudent?.student_id_card || '' }})
            </p>
          </div>

          <!-- Compact Inline Legend Bar (Merged directly in header) -->
          <div class="flex items-center gap-2 sm:gap-3 text-[11px] font-medium text-slate-600 flex-wrap">
            <span class="inline-flex items-center gap-1"><span class="font-bold text-emerald-600">✓</span> វត្តមាន</span>
            <span class="inline-flex items-center gap-1"><span class="font-bold text-blue-700 bg-blue-100 px-1 rounded text-[10px]">H</span> ច្បាប់</span>
            <span class="inline-flex items-center gap-1"><span class="font-bold text-rose-600 bg-rose-100 px-1 rounded text-[10px]">A</span> អវត្តមាន</span>
            <span class="inline-flex items-center gap-1"><span class="font-bold text-amber-700 bg-amber-100 px-1 rounded text-[10px]">L</span> យឺត</span>
            <span class="inline-flex items-center gap-1 text-slate-400"><span class="font-bold text-slate-400 bg-slate-200 px-1 rounded text-[10px]">-</span> អាទិត្យ</span>

            <!-- Privacy toggle for students -->
            <label v-if="isStudent" class="inline-flex items-center gap-1.5 ml-1 text-[11px] font-bold text-indigo-700 bg-indigo-50/80 px-2.5 py-1 rounded-lg border border-indigo-100 cursor-pointer transition hover:bg-indigo-100/60">
              <input type="checkbox" v-model="showOnlyMyAttendance" class="rounded text-indigo-600 focus:ring-0 w-3.5 h-3.5">
              <span>វត្តមានផ្ទាល់ខ្លួន</span>
            </label>

            <!-- Absent toggle for teachers -->
            <label v-else class="inline-flex items-center gap-1.5 ml-1 text-[11px] font-bold text-slate-700 bg-white px-2 py-0.5 rounded-lg border border-slate-200 cursor-pointer">
              <input type="checkbox" v-model="onlyShowAbsentStudents" class="rounded text-indigo-600 focus:ring-0 w-3 h-3">
              <span>តែអ្នកមានឈប់</span>
            </label>
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

        <template v-else>
          <!-- MOBILE VIEW: Clean 7-Column Calendar Grid (Mon - Sun) -->
          <div class="block md:hidden p-3 bg-white space-y-2.5 font-khmer">
            <!-- Calendar Day-of-Week Headers (Mon - Sun) -->
            <div class="grid grid-cols-7 gap-1 text-center font-bold text-[11px] pb-1.5 border-b border-slate-100">
              <span class="text-slate-600">ច័ន្ទ</span>
              <span class="text-slate-600">អង្គារ</span>
              <span class="text-slate-600">ពុធ</span>
              <span class="text-slate-600">ព្រហ</span>
              <span class="text-slate-600">សុក្រ</span>
              <span class="text-slate-600">សៅរ៍</span>
              <span class="text-rose-600">អាទិត្យ</span>
            </div>

            <!-- Calendar Days Grid -->
            <div class="grid grid-cols-7 gap-1 sm:gap-1.5">
              <template v-for="cell in calendarDaysGrid" :key="cell.key">
                <!-- Leading / Trailing Blank Cell -->
                <div v-if="cell.isBlank" class="min-h-[58px] rounded-xl bg-slate-50/40"></div>

                <!-- Active Month Day Cell -->
                <div 
                  v-else 
                  class="min-h-[58px] p-1 rounded-xl border flex flex-col items-center justify-between transition-all"
                  :class="[
                    cell.isSunday ? 'bg-slate-50/80 border-slate-100 text-slate-400' : 'bg-white border-slate-100 shadow-2xs',
                    cell.dateStr === todayDateStr ? 'ring-2 ring-blue-500/40 border-blue-300 bg-blue-50/20' : ''
                  ]"
                >
                  <!-- Day Number & Today Indicator -->
                  <div class="flex items-center justify-between w-full px-0.5">
                    <span 
                      class="text-[11px] font-mono font-bold leading-none"
                      :class="[
                        cell.isSunday ? 'text-rose-500' : 'text-slate-700',
                        cell.dateStr === todayDateStr ? 'text-blue-700' : ''
                      ]"
                    >
                      {{ cell.day }}
                    </span>
                    <span v-if="cell.dateStr === todayDateStr" class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                  </div>

                  <!-- Status Pill -->
                  <div class="w-full flex items-center justify-center my-0.5">
                    <span 
                      class="w-6 h-6 rounded-lg text-xs flex items-center justify-center shadow-2xs"
                      :class="getCalendarStatusBadge(getStudentDayStatus(currentStudent?.student_id, cell.dateStr, cell.isSunday))"
                      :title="getStudentDayStatus(currentStudent?.student_id, cell.dateStr, cell.isSunday).label"
                    >
                      {{ getStudentDayStatus(currentStudent?.student_id, cell.dateStr, cell.isSunday).char }}
                    </span>
                  </div>

                  <!-- Status Label / Sunday text -->
                  <span class="text-[8px] truncate leading-tight opacity-75 font-medium">
                    {{ cell.isSunday ? 'សម្រាក' : getStudentDayStatus(currentStudent?.student_id, cell.dateStr, cell.isSunday).label }}
                  </span>
                </div>
              </template>
            </div>
          </div>

          <!-- DESKTOP / TABLET VIEW: Wide 35-Column Table with Sticky Student Name -->
          <div class="hidden md:block overflow-x-auto max-h-[70vh]">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-100 text-slate-700 font-bold sticky top-0 z-20 border-b border-slate-200">
              <tr>
                <th class="py-2.5 px-3 min-w-[140px] sm:min-w-[160px] sticky left-0 bg-slate-100 z-30 shadow-xs border-r border-slate-200">
                  ឈ្មោះសិស្ស
                </th>
                <th class="py-2.5 px-2 text-center w-12 border-r border-slate-200 text-slate-600">
                  ភេទ
                </th>

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
                v-for="st in displayedStudentsList" 
                :key="st.student_id" 
                class="hover:bg-slate-50/80 transition-colors"
                :class="{ 'bg-indigo-50/50 font-semibold': isStudent && st.student_id === currentUser.student_id }"
              >
                <!-- 4. Sticky Student Name Column on Horizontal Scroll -->
                <td 
                  class="py-2 px-3 font-bold text-slate-800 sticky left-0 z-10 shadow-xs border-r border-slate-200 whitespace-nowrap"
                  :class="isStudent && st.student_id === currentUser.student_id ? 'bg-indigo-50' : 'bg-white'"
                >
                  <div class="flex items-center gap-1.5">
                    <span class="text-xs sm:text-sm font-bold text-slate-900">{{ st.name }}</span>
                    <span v-if="isStudent && st.student_id === currentUser.student_id" class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-indigo-600 text-white shadow-2xs">
                      ខ្ញុំ
                    </span>
                  </div>
                  <div class="text-[10px] font-mono text-slate-400 font-normal mt-0.5">{{ st.student_id_card }}</div>
                </td>
                <td class="py-2 px-2 text-center text-slate-600 border-r border-slate-100 text-[11px]">
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
        </template>
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
            <span>{{ isStudent ? 'ប្រវត្តិនៃការស្នើសុំច្បាប់របស់ខ្ញុំ' : 'សំណើសុំច្បាប់ឈប់សម្រាក' }}</span>
            <span v-if="!isStudent" class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
              រង់ចាំអនុម័ត: {{ pendingLeavesCount }}
            </span>
            <span v-else-if="pendingLeavesCount > 0" class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
              កំពុងរង់ចាំពិនិត្យ: {{ pendingLeavesCount }}
            </span>
          </h3>
          <p class="text-xs text-slate-500">
            {{ isStudent ? 'តាមដានស្ថានភាពពាក្យសុំច្បាប់ និងស្នើសុំច្បាប់ឈប់សម្រាកថ្មី' : 'ពិនិត្យ ឯកភាព ឬបដិសេធពាក្យសុំច្បាប់របស់សិស្សតាមអនឡាញ' }}
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button 
            v-if="isStudent"
            @click="openStudentLeaveRequestModal" 
            type="button" 
            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 transition flex items-center gap-1.5 cursor-pointer active:scale-95"
          >
            <i class="fa-solid fa-plus text-xs"></i>
            <span>ស្នើសុំច្បាប់ថ្មី</span>
          </button>
          <button 
            v-else
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

        <div v-if="!isStudent" class="flex items-center gap-2">
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
          <span>{{ isStudent ? 'លោកអ្នកមិនទាន់មានសំណើសុំច្បាប់ណាមួយឡើយ។' : 'ពុំមានសំណើសុំច្បាប់ក្នុងលក្ខខណ្ឌនេះឡើយ។' }}</span>
          <button 
            v-if="isStudent"
            @click="openStudentLeaveRequestModal"
            type="button"
            class="mt-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition cursor-pointer"
          >
            + ស្នើសុំច្បាប់ឥឡូវនេះ
          </button>
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
              <th v-if="!isStudent" class="py-3 px-4 w-36 text-center">សកម្មភាព</th>
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
              <td v-if="!isStudent" class="py-3 px-4 text-center">
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

    <!-- Modal 2: In-App Student Leave Request Modal (Pre-filled user info) -->
    <div 
      v-if="showStudentLeaveModal" 
      class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs font-khmer transition-opacity"
      @click.self="showStudentLeaveModal = false"
    >
      <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
        <!-- 4. Sticky Modal Header: Clean White Header with Soft Pastel Icon -->
        <div class="bg-white border-b border-slate-100 p-4 sm:p-5 flex items-center justify-between flex-shrink-0 sticky top-0 z-20">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg flex-shrink-0">
              <i class="fa-solid fa-file-pen"></i>
            </div>
            <div>
              <h3 class="font-bold text-base leading-tight text-slate-900">ទម្រង់ស្នើសុំច្បាប់ឈប់សម្រាក</h3>
              <p class="text-xs text-slate-500 mt-0.5">បំពេញព័ត៌មានដើម្បីផ្ញើសំណើជូនលោកគ្រូ/អ្នកគ្រូ</p>
            </div>
          </div>
          <button 
            @click="showStudentLeaveModal = false" 
            type="button"
            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition cursor-pointer text-sm font-semibold"
          >
            ✕
          </button>
        </div>

        <!-- Scrollable Form Body -->
        <form @submit.prevent="submitStudentLeaveRequest" class="flex flex-col flex-1 min-h-0 overflow-hidden">
          <div class="p-4 sm:p-6 space-y-4 max-h-[75vh] overflow-y-auto text-xs flex-1">
            
            <!-- Student Pre-filled Compact Info Bar (Single compact, borderless light-gray pill/bar) -->
            <div class="bg-slate-50 rounded-xl px-3.5 py-2.5 flex items-center justify-between text-xs">
              <div class="flex items-center gap-2 overflow-hidden flex-wrap">
                <span class="font-bold text-slate-900 text-sm">{{ studentLeaveForm.student_name }}</span>
                <span v-if="studentLeaveForm.student_id_card" class="text-slate-300">•</span>
                <span v-if="studentLeaveForm.student_id_card" class="font-mono text-slate-600">{{ studentLeaveForm.student_id_card }}</span>
                <span v-if="studentLeaveForm.class_name" class="text-slate-300">•</span>
                <span v-if="studentLeaveForm.class_name" class="font-medium text-slate-700">ថ្នាក់ {{ studentLeaveForm.class_name }}</span>
              </div>
              <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md flex items-center gap-1 flex-shrink-0">
                <i class="fa-solid fa-circle-check text-[9px]"></i>
                ស្វ័យប្រវត្តិ
              </span>
            </div>

            <!-- Session / Duration Selector (Full Day vs By Hours) -->
            <div>
              <label class="block font-bold text-slate-700 mb-1.5 text-xs">
                រយៈពេលសុំច្បាប់ <span class="text-rose-500">*</span>
              </label>
              <div class="grid grid-cols-2 gap-2.5">
                <button
                  type="button"
                  @click="setLeaveSession('full_day')"
                  :class="[
                    'py-2.5 px-3 rounded-2xl text-center border text-xs transition cursor-pointer active:scale-95 flex items-center justify-center gap-2.5',
                    studentLeaveForm.session === 'full_day'
                      ? 'bg-blue-50/80 border-2 border-blue-500 text-blue-700 font-bold'
                      : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50 font-medium'
                  ]"
                >
                  <i class="fa-solid fa-calendar-day text-base" :class="studentLeaveForm.session === 'full_day' ? 'text-blue-600' : 'text-slate-400'"></i>
                  <div class="text-left">
                    <div class="leading-tight text-xs sm:text-sm">ពេញមួយថ្ងៃ</div>
                    <div class="text-[10px] opacity-75">១ ថ្ងៃ ឬច្រើនថ្ងៃ</div>
                  </div>
                </button>

                <button
                  type="button"
                  @click="setLeaveSession('hourly')"
                  :class="[
                    'py-2.5 px-3 rounded-2xl text-center border text-xs transition cursor-pointer active:scale-95 flex items-center justify-center gap-2.5',
                    studentLeaveForm.session === 'hourly'
                      ? 'bg-blue-50/80 border-2 border-blue-500 text-blue-700 font-bold'
                      : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50 font-medium'
                  ]"
                >
                  <i class="fa-solid fa-clock text-base" :class="studentLeaveForm.session === 'hourly' ? 'text-blue-600' : 'text-slate-400'"></i>
                  <div class="text-left">
                    <div class="leading-tight text-xs sm:text-sm">សុំជាម៉ោង</div>
                    <div class="text-[10px] opacity-75">ជ្រើសរើសម៉ោងសិក្សា</div>
                  </div>
                </button>
              </div>
            </div>

            <!-- Date Selection Box (Khmer Format DD/MM/YYYY) -->
            <div class="space-y-3 bg-slate-50/70 p-3.5 rounded-2xl border border-slate-200/80">
              <!-- Full Day Date Range (From & To) -->
              <div v-if="studentLeaveForm.session === 'full_day'" class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block font-bold text-slate-700 mb-1 text-xs">
                    ពីថ្ងៃទី <span class="text-rose-500">*</span>
                  </label>
                  <div 
                    class="relative group cursor-pointer"
                    @click="openDatePicker('start')"
                  >
                    <input 
                      ref="startDateInputRef"
                      type="date" 
                      v-model="studentLeaveForm.start_date" 
                      required
                      @change="handleStartDateChange"
                      @click.stop="openDatePicker('start')"
                      class="custom-date-trigger opacity-0 absolute inset-0 w-full h-full cursor-pointer z-20"
                    />
                    <div class="w-full px-3 py-2 bg-white border border-slate-200 group-hover:border-blue-500 group-hover:ring-2 group-hover:ring-blue-500/20 rounded-xl font-bold text-slate-800 text-xs flex items-center justify-between shadow-2xs transition pointer-events-none">
                      <div class="flex items-center gap-1.5 overflow-hidden">
                        <i class="fa-regular fa-calendar-days text-blue-600 text-xs flex-shrink-0"></i>
                        <span class="tracking-wide text-xs truncate">{{ formatDateKmDisplay(studentLeaveForm.start_date) }}</span>
                      </div>
                      <span class="text-[9px] text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded font-semibold flex-shrink-0">ថ្ងៃ/ខែ/ឆ្នាំ</span>
                    </div>
                  </div>
                  <span class="text-[10px] text-slate-500 mt-1 block font-medium truncate">
                    {{ formatKhmerFullDate(studentLeaveForm.start_date) }}
                  </span>
                </div>

                <div>
                  <label class="block font-bold text-slate-700 mb-1 text-xs">
                    ដល់ថ្ងៃទី <span class="text-rose-500">*</span>
                  </label>
                  <div 
                    class="relative group cursor-pointer"
                    @click="openDatePicker('end')"
                  >
                    <input 
                      ref="endDateInputRef"
                      type="date" 
                      v-model="studentLeaveForm.end_date" 
                      :min="studentLeaveForm.start_date"
                      required
                      @click.stop="openDatePicker('end')"
                      class="custom-date-trigger opacity-0 absolute inset-0 w-full h-full cursor-pointer z-20"
                    />
                    <div class="w-full px-3 py-2 bg-white border border-slate-200 group-hover:border-blue-500 group-hover:ring-2 group-hover:ring-blue-500/20 rounded-xl font-bold text-slate-800 text-xs flex items-center justify-between shadow-2xs transition pointer-events-none">
                      <div class="flex items-center gap-1.5 overflow-hidden">
                        <i class="fa-regular fa-calendar-days text-blue-600 text-xs flex-shrink-0"></i>
                        <span class="tracking-wide text-xs truncate">{{ formatDateKmDisplay(studentLeaveForm.end_date) }}</span>
                      </div>
                      <span class="text-[9px] text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded font-semibold flex-shrink-0">ថ្ងៃ/ខែ/ឆ្នាំ</span>
                    </div>
                  </div>
                  <span class="text-[10px] text-slate-500 mt-1 block font-medium truncate">
                    {{ formatKhmerFullDate(studentLeaveForm.end_date) }}
                  </span>
                </div>
              </div>

              <!-- Hourly Single Date (Leave Date) -->
              <div v-else>
                <label class="block font-bold text-slate-700 mb-1 text-xs">
                  កាលបរិច្ឆេទសុំច្បាប់ <span class="text-rose-500">*</span>
                </label>
                <div 
                  class="relative group cursor-pointer"
                  @click="openDatePicker('hourly')"
                >
                  <input 
                    ref="hourlyDateInputRef"
                    type="date" 
                    v-model="studentLeaveForm.start_date" 
                    required
                    @change="handleStartDateChange"
                    @click.stop="openDatePicker('hourly')"
                    class="custom-date-trigger opacity-0 absolute inset-0 w-full h-full cursor-pointer z-20"
                  />
                  <div class="w-full px-3.5 py-2.5 bg-white border border-slate-200 group-hover:border-blue-500 group-hover:ring-2 group-hover:ring-blue-500/20 rounded-xl font-bold text-slate-800 text-xs flex items-center justify-between shadow-2xs transition pointer-events-none">
                    <div class="flex items-center gap-2">
                      <i class="fa-regular fa-calendar-days text-blue-600 text-sm"></i>
                      <span class="tracking-wide text-xs sm:text-sm text-slate-900">{{ formatDateKmDisplay(studentLeaveForm.start_date) }}</span>
                    </div>
                    <span class="text-[10px] text-blue-600 bg-blue-50 px-2 py-0.5 rounded font-semibold">ថ្ងៃ/ខែ/ឆ្នាំ</span>
                  </div>
                </div>
                <span class="text-[10px] text-slate-500 mt-1 block font-medium">
                  {{ formatKhmerFullDate(studentLeaveForm.start_date) }}
                </span>
              </div>

              <!-- Hourly Period Selection Buttons (07:00 - 11:00) -->
              <div v-if="studentLeaveForm.session === 'hourly'" class="p-3 bg-white rounded-2xl border border-slate-200/80 space-y-2.5">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-graduation-cap text-blue-600"></i>
                    ជ្រើសរើសម៉ោងសិក្សាដែលត្រូវសុំច្បាប់ (០៧:០០ - ១១:០០)៖
                  </span>
                  <span class="text-[11px] font-bold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-100">
                    {{ studentLeaveForm.selected_periods.length }} ម៉ោង
                  </span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                  <button
                    v-for="p in morningPeriods"
                    :key="p.id"
                    type="button"
                    @click="togglePeriod(p.id)"
                    :class="[
                      'p-2.5 rounded-xl border text-left transition cursor-pointer flex items-center justify-between active:scale-95',
                      isPeriodSelected(p.id)
                        ? 'bg-blue-50/80 border-2 border-blue-500 text-blue-700 shadow-2xs'
                        : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'
                    ]"
                  >
                    <div>
                      <div class="font-bold text-xs">{{ p.name }}</div>
                      <div class="text-[10px] font-mono opacity-75 mt-0.5">{{ p.time }}</div>
                    </div>
                    <div 
                      class="w-5 h-5 rounded-full flex items-center justify-center text-xs flex-shrink-0" 
                      :class="isPeriodSelected(p.id) ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400'"
                    >
                      <i class="fa-solid fa-check text-[10px]" v-if="isPeriodSelected(p.id)"></i>
                    </div>
                  </button>
                </div>
              </div>

              <!-- Confirmation & Calculated Badge -->
              <div class="bg-white border border-slate-200/80 px-3.5 py-2.5 rounded-xl flex items-center justify-between text-slate-800">
                <div class="flex items-center gap-2.5 overflow-hidden">
                  <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm flex-shrink-0">
                    <i :class="studentLeaveForm.session === 'hourly' ? 'fa-regular fa-clock' : 'fa-regular fa-calendar-days'"></i>
                  </div>
                  <div class="overflow-hidden">
                    <div class="text-xs font-bold leading-tight truncate text-slate-800">
                      សុំច្បាប់៖ {{ formatDateKmDisplay(studentLeaveForm.start_date) }}
                      <span v-if="studentLeaveForm.session === 'full_day' && studentLeaveForm.start_date !== studentLeaveForm.end_date">
                        ដល់ {{ formatDateKmDisplay(studentLeaveForm.end_date) }}
                      </span>
                    </div>
                    <div class="text-[10px] text-slate-500 mt-0.5 truncate font-medium">
                      {{ studentLeaveForm.session === 'hourly' ? `សុំជាម៉ោង៖ ${selectedPeriodsLabel}` : 'ពេញមួយថ្ងៃ' }}
                    </div>
                  </div>
                </div>
                <span class="font-semibold text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full flex-shrink-0 ml-2">
                  {{ studentLeaveForm.session === 'hourly' ? `${studentLeaveForm.selected_periods.length} ម៉ោង` : `${computedDaysCount} ថ្ងៃ` }}
                </span>
              </div>
            </div>

            <!-- Phone Number -->
            <div class="space-y-1">
              <label class="block font-bold text-slate-700 text-xs">
                លេខទូរស័ព្ទអាណាព្យាបាល / សិស្ស <span class="text-rose-500">*</span>
              </label>
              <input 
                type="tel" 
                v-model="studentLeaveForm.phone" 
                placeholder="ឧទាហរណ៍៖ 012 345 678" 
                required
                class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-medium text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition"
              />
            </div>

            <!-- Reason / Details -->
            <div class="space-y-1">
              <label class="block font-bold text-slate-700 text-xs">
                មូលហេតុនៃការសុំច្បាប់ <span class="text-rose-500">*</span>
              </label>
              <textarea 
                v-model="studentLeaveForm.reason" 
                rows="3" 
                required
                placeholder="សូមបញ្ជាក់មូលហេតុលម្អិត..."
                class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-normal text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition resize-none"
              ></textarea>
            </div>

            <!-- 3. Attachment: Custom Khmer Dropzone -->
            <div class="space-y-1">
              <label class="block font-bold text-slate-700 text-xs">
                ឯកសារភ្ជាប់ (បើមាន៖ លិខិតវេជ្ជបញ្ជា ឬលិខិតសុំច្បាប់)
              </label>

              <!-- Hidden native file input -->
              <input 
                type="file" 
                ref="fileInputRef"
                accept="image/jpeg,image/png,image/webp,application/pdf"
                @change="handleStudentLeaveFile"
                class="hidden"
              />

              <!-- Empty State: Modern Soft Dashed Upload Dropzone -->
              <div 
                v-if="!studentLeaveFile"
                @click="triggerFileInput"
                class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl p-4 sm:p-5 bg-slate-50/60 hover:bg-blue-50/30 text-center cursor-pointer transition flex flex-col items-center justify-center gap-2 group"
              >
                <div class="w-10 h-10 rounded-xl bg-blue-50 group-hover:bg-blue-100 text-blue-600 flex items-center justify-center text-lg transition">
                  <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
                <div>
                  <div class="text-xs font-semibold text-slate-700 group-hover:text-blue-700">
                    ចុចទីនេះដើម្បីជ្រើសរើសរូបភាព ឬថតរូបលិខិតសុំច្បាប់
                  </div>
                  <div class="text-[10px] text-slate-400 mt-0.5">
                    គាំទ្រឯកសារ JPG, PNG ឬ PDF (អតិបរមា 5MB)
                  </div>
                </div>
              </div>

              <!-- File Selected Preview Card with Remove Button -->
              <div 
                v-else
                class="border border-slate-200 rounded-2xl p-3 bg-white shadow-2xs flex items-center justify-between gap-3"
              >
                <div class="flex items-center gap-3 overflow-hidden">
                  <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                    <img 
                      v-if="filePreviewUrl" 
                      :src="filePreviewUrl" 
                      alt="Preview" 
                      class="w-full h-full object-cover"
                    />
                    <i v-else class="fa-solid fa-file-pdf text-red-500 text-2xl"></i>
                  </div>
                  <div class="min-w-0">
                    <div class="text-xs font-bold text-slate-800 truncate" :title="studentLeaveFile.name">
                      {{ studentLeaveFile.name }}
                    </div>
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                      {{ formatFileSize(studentLeaveFile.size) }}
                    </div>
                  </div>
                </div>

                <button 
                  type="button" 
                  @click="removeSelectedFile" 
                  class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-xs font-semibold flex items-center gap-1 transition flex-shrink-0 cursor-pointer active:scale-95"
                >
                  <i class="fa-solid fa-trash-can text-[11px]"></i>
                  <span>លុបចេញ</span>
                </button>
              </div>
            </div>

          </div>

          <!-- Sticky Full-Width Footer Action Buttons -->
          <div class="p-4 bg-white border-t border-slate-100 flex-shrink-0 sticky bottom-0 z-20">
            <div class="grid grid-cols-3 gap-3 w-full">
              <button 
                type="button" 
                @click="showStudentLeaveModal = false"
                class="col-span-1 px-3 py-3 rounded-2xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold transition text-xs sm:text-sm text-center cursor-pointer active:scale-95"
              >
                បោះបង់
              </button>
              <button 
                type="submit" 
                :disabled="isSubmittingLeave"
                class="col-span-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold shadow-md shadow-blue-600/25 transition text-xs sm:text-sm flex items-center justify-center gap-2 disabled:opacity-50 cursor-pointer active:scale-95"
              >
                <div v-if="isSubmittingLeave" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                <span>{{ isSubmittingLeave ? 'កំពុងបញ្ជូន...' : '✓ បញ្ជូនសំណើសុំច្បាប់' }}</span>
              </button>
            </div>
          </div>
        </form>
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
const leaveFilterStatus = ref('all');
const leaveFilterClass = ref('all');
const isLoadingLeaves = ref(false);

// Student Leave Request In-App Modal States
const showStudentLeaveModal = ref(false);
const isSubmittingLeave = ref(false);
const studentLeaveFile = ref(null);
const fileInputRef = ref(null);
const filePreviewUrl = ref(null);

const startDateInputRef = ref(null);
const endDateInputRef = ref(null);
const hourlyDateInputRef = ref(null);

const openDatePicker = (type) => {
  let el = null;
  if (type === 'start') el = startDateInputRef.value;
  else if (type === 'end') el = endDateInputRef.value;
  else if (type === 'hourly') el = hourlyDateInputRef.value;
  if (!el) return;

  try {
    if (typeof el.showPicker === 'function') {
      el.showPicker();
    } else {
      el.focus();
    }
  } catch (e) {
    el.focus();
  }
};

const morningPeriods = [
  { id: '1', name: 'ម៉ោងទី ១', time: '07:00 - 07:50' },
  { id: '2', name: 'ម៉ោងទី ២', time: '07:55 - 08:45' },
  { id: '3', name: 'ម៉ោងទី ៣', time: '09:15 - 10:05' },
  { id: '4', name: 'ម៉ោងទី ៤', time: '10:10 - 11:00' },
];

const studentLeaveForm = ref({
  student_id: null,
  student_name: '',
  student_id_card: '',
  class_name: '7A',
  leave_type: 'ច្បាប់',
  session: 'full_day',
  selected_periods: ['1'],
  start_date: new Date().toISOString().slice(0, 10),
  end_date: new Date().toISOString().slice(0, 10),
  phone: '',
  reason: '',
});

const setLeaveSession = (sess) => {
  studentLeaveForm.value.session = sess;
  if (sess === 'hourly') {
    studentLeaveForm.value.end_date = studentLeaveForm.value.start_date;
  }
};

const handleStartDateChange = () => {
  if (studentLeaveForm.value.session === 'hourly') {
    studentLeaveForm.value.end_date = studentLeaveForm.value.start_date;
  } else if (studentLeaveForm.value.end_date < studentLeaveForm.value.start_date) {
    studentLeaveForm.value.end_date = studentLeaveForm.value.start_date;
  }
};

const togglePeriod = (pId) => {
  const list = studentLeaveForm.value.selected_periods;
  const idx = list.indexOf(pId);
  if (idx > -1) {
    if (list.length > 1) {
      list.splice(idx, 1);
    }
  } else {
    list.push(pId);
    list.sort();
  }
};

const isPeriodSelected = (pId) => {
  return studentLeaveForm.value.selected_periods.includes(pId);
};

const selectedPeriodsLabel = computed(() => {
  if (studentLeaveForm.value.session !== 'hourly') return '';
  const selected = morningPeriods.filter(p => studentLeaveForm.value.selected_periods.includes(p.id));
  if (selected.length === 0) return 'ម៉ោងទី ១';
  const names = selected.map(p => p.name).join(', ');
  const firstTime = selected[0].time.split(' - ')[0];
  const lastTime = selected[selected.length - 1].time.split(' - ')[1];
  return `${names} (${firstTime} - ${lastTime})`;
});

const computedDaysCount = computed(() => {
  if (studentLeaveForm.value.session === 'hourly') {
    return Number((studentLeaveForm.value.selected_periods.length * 0.25).toFixed(2));
  }
  if (!studentLeaveForm.value.start_date || !studentLeaveForm.value.end_date) return 1;
  const start = new Date(studentLeaveForm.value.start_date);
  const end = new Date(studentLeaveForm.value.end_date);
  if (isNaN(start.getTime()) || isNaN(end.getTime()) || end < start) return 1;
  const diffTime = Math.abs(end - start);
  const fullDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
  return fullDays;
});

const formatDateKmDisplay = (dStr) => {
  if (!dStr) return '';
  const parts = dStr.split('-');
  if (parts.length === 3) {
    return `${parts[2]}/${parts[1]}/${parts[0]}`;
  }
  const d = new Date(dStr);
  if (isNaN(d.getTime())) return dStr;
  return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
};

const kmMonths = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
const toKhmerDigits = (num) => {
  const kmNums = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
  return String(num).replace(/[0-9]/g, w => kmNums[+w]);
};

const formatKhmerFullDate = (dStr) => {
  if (!dStr) return '';
  const parts = dStr.split('-');
  if (parts.length === 3) {
    const y = parseInt(parts[0], 10);
    const m = parseInt(parts[1], 10);
    const d = parseInt(parts[2], 10);
    return `ថ្ងៃទី ${toKhmerDigits(String(d).padStart(2, '0'))} ខែ${kmMonths[m - 1] || m} ឆ្នាំ${toKhmerDigits(y)}`;
  }
  return dStr;
};

const triggerFileInput = () => {
  fileInputRef.value?.click();
};

const handleStudentLeaveFile = (e) => {
  const file = e.target.files?.[0];
  if (!file) return;

  if (file.size > 5 * 1024 * 1024) {
    alert('ឯកសារភ្ជាប់មិនត្រូវលើសពី 5MB ឡើយ!');
    if (e.target) e.target.value = '';
    return;
  }

  studentLeaveFile.value = file;
  if (file.type.startsWith('image/')) {
    if (filePreviewUrl.value) {
      URL.revokeObjectURL(filePreviewUrl.value);
    }
    filePreviewUrl.value = URL.createObjectURL(file);
  } else {
    filePreviewUrl.value = null;
  }
};

const removeSelectedFile = () => {
  if (filePreviewUrl.value) {
    URL.revokeObjectURL(filePreviewUrl.value);
    filePreviewUrl.value = null;
  }
  studentLeaveFile.value = null;
  if (fileInputRef.value) {
    fileInputRef.value.value = '';
  }
};

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B';
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
};

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

const todayDateStr = computed(() => new Date().toISOString().slice(0, 10));

const getStudentDayStatus = (studentId, dateStr, isSunday) => {
  if (isSunday) return { char: '-', label: 'ថ្ងៃអាទិត្យ', class: 'text-slate-300 font-bold' };
  const rec = studentAttendanceMap.value[studentId]?.[dateStr];
  if (rec) {
    if (rec.status === 'excused' || rec.status === 'holiday') {
      return { char: 'H', label: 'សុំច្បាប់', class: 'bg-blue-100 text-blue-800 font-black rounded px-1 shadow-xs', remark: rec.remark };
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
  const todayStr = todayDateStr.value;
  if (dateStr <= todayStr) {
    return { char: '✓', label: 'វត្តមាន', class: 'text-emerald-600 font-bold' };
  }
  return { char: '·', label: 'មិនទាន់ដល់', class: 'text-slate-300' };
};

const getCalendarStatusBadge = (st) => {
  if (!st || !st.char) return 'text-slate-300';
  if (st.char === '✓') {
    return 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold';
  }
  if (st.char === 'L') {
    return 'bg-amber-50 text-amber-800 border border-amber-200 font-bold';
  }
  if (st.char === 'H') {
    return 'bg-blue-50 text-blue-700 border border-blue-200 font-bold';
  }
  if (st.char === 'A') {
    return 'bg-rose-50 text-rose-700 border border-rose-200 font-bold';
  }
  if (st.char === '-') {
    return 'bg-slate-100 text-slate-400 border border-slate-200 font-medium';
  }
  return 'text-slate-300';
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

// Default to Personal Attendance Only for student privacy
const showOnlyMyAttendance = ref(true);

const displayedStudentsList = computed(() => {
  let list = studentsList.value;
  if (isStudent.value && showOnlyMyAttendance.value) {
    const studentId = currentUser.value?.student_id;
    const studentCode = currentUser.value?.student_id_card;
    const studentName = currentUser.value?.student_name || currentUser.value?.name;
    const match = list.filter(st => 
      (studentId && st.student_id === studentId) ||
      (studentCode && st.student_id_card === studentCode) ||
      (studentName && st.name === studentName)
    );
    if (match.length > 0) return match;
    if (studentId) {
      const byId = list.filter(st => st.student_id === studentId);
      if (byId.length > 0) return byId;
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

const currentStudent = computed(() => {
  if (displayedStudentsList.value.length === 1) {
    return displayedStudentsList.value[0];
  }
  const studentId = currentUser.value?.student_id;
  const studentCode = currentUser.value?.student_id_card;
  const studentName = currentUser.value?.student_name || currentUser.value?.name;
  return displayedStudentsList.value.find(st => 
    (studentId && st.student_id === studentId) ||
    (studentCode && st.student_id_card === studentCode) ||
    (studentName && st.name === studentName)
  ) || displayedStudentsList.value[0] || null;
});

// 7-Column Calendar Grid for Mobile Viewports (Monday to Sunday)
const calendarDaysGrid = computed(() => {
  const mIdx = monthIndexMap[activeMonthKey.value] ?? 10;
  const yr = currentYearNumber.value;
  const count = daysInActiveMonth.value;

  // Day of week of 1st day of month: 0=Sun, 1=Mon, ..., 6=Sat
  // Convert to Mon=0, Tue=1, Wed=2, Thu=3, Fri=4, Sat=5, Sun=6
  const firstDayOfWeek = new Date(yr, mIdx, 1).getDay();
  const leadingBlankCount = (firstDayOfWeek + 6) % 7;

  const cells = [];
  // Leading blank cells
  for (let i = 0; i < leadingBlankCount; i++) {
    cells.push({ isBlank: true, key: `lead-${i}` });
  }

  // Active month days
  for (let d = 1; d <= count; d++) {
    const dateObj = new Date(yr, mIdx, d);
    const dayOfWeek = dateObj.getDay();
    const isSunday = (dayOfWeek === 0);
    const isSaturday = (dayOfWeek === 6);
    const dateStr = `${yr}-${String(mIdx + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    cells.push({
      isBlank: false,
      day: d,
      dateStr,
      isSunday,
      isSaturday,
      dayOfWeek,
      key: dateStr
    });
  }

  // Trailing blank cells to complete the 7-day grid row
  const remainder = cells.length % 7;
  if (remainder > 0) {
    const trailingBlankCount = 7 - remainder;
    for (let i = 0; i < trailingBlankCount; i++) {
      cells.push({ isBlank: true, key: `trail-${i}` });
    }
  }

  return cells;
});

// Dynamic Real-time Attendance Stats (Calculated from selected month records)
const dynamicStats = computed(() => {
  if (isStudent.value && currentUser.value?.student_id) {
    const sum = getStudentSummary(currentUser.value.student_id);
    return {
      present: sum.presentCount,
      late: sum.lateCount,
      absent: sum.absentCount,
      holiday: sum.leaveCount,
    };
  }

  let present = 0, late = 0, absent = 0, holiday = 0;
  if (displayedStudentsList.value.length > 0) {
    displayedStudentsList.value.forEach(st => {
      const sum = getStudentSummary(st.student_id);
      present += sum.presentCount;
      late += sum.lateCount;
      absent += sum.absentCount;
      holiday += sum.leaveCount;
    });
    return { present, late, absent, holiday };
  }
  return stats.value;
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

// Student Leave Request Action
const openStudentLeaveForm = () => {
  openStudentLeaveRequestModal();
};

const openStudentLeaveRequestModal = () => {
  const u = currentUser.value || {};
  studentLeaveForm.value = {
    student_id: u.student_id || null,
    student_name: u.student_name || u.name || '',
    student_id_card: u.student_id_card || '',
    class_name: u.student_grade || selectedClass.value || '7A',
    leave_type: 'ច្បាប់',
    session: 'full_day',
    selected_periods: ['1'],
    start_date: new Date().toISOString().slice(0, 10),
    end_date: new Date().toISOString().slice(0, 10),
    phone: u.student_phone || u.phone || '',
    reason: '',
  };
  removeSelectedFile();
  showStudentLeaveModal.value = true;
};

const submitStudentLeaveRequest = async () => {
  if (!studentLeaveForm.value.reason.trim()) {
    alert('សូមបញ្ជាក់មូលហេតុនៃការសុំច្បាប់!');
    return;
  }
  isSubmittingLeave.value = true;
  try {
    const isHourly = studentLeaveForm.value.session === 'hourly';
    const formData = new FormData();
    if (studentLeaveForm.value.student_id) {
      formData.append('student_id', studentLeaveForm.value.student_id);
    }
    formData.append('student_name', studentLeaveForm.value.student_name);
    formData.append('class_name', studentLeaveForm.value.class_name);
    formData.append('leave_type', 'ច្បាប់');
    formData.append('start_date', studentLeaveForm.value.start_date);
    formData.append('end_date', isHourly ? studentLeaveForm.value.start_date : studentLeaveForm.value.end_date);
    formData.append('days_count', computedDaysCount.value);

    const sessionNote = isHourly 
      ? ` [សុំច្បាប់ជាម៉ោង៖ ${selectedPeriodsLabel.value}]`
      : ' [ពេញមួយថ្ងៃ]';
    formData.append('reason', studentLeaveForm.value.reason.trim() + sessionNote);
    formData.append('phone', studentLeaveForm.value.phone);

    if (studentLeaveFile.value) {
      formData.append('attachment', studentLeaveFile.value);
    }

    const res = await axios.post('/api/leave-requests', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      }
    });

    alert(res.data?.message || 'សំណើសុំច្បាប់ត្រូវបានបញ្ជូនដោយជោគជ័យ!');
    showStudentLeaveModal.value = false;
    removeSelectedFile();
    activeTab.value = 'leaves';
    await fetchLeaveRequests();
  } catch (err) {
    const msg = err.response?.data?.message || err.response?.data?.error || 'មានបញ្ហាក្នុងការបញ្ជូនសំណើសុំច្បាប់ សូមពិនិត្យព័ត៌មានម្តងទៀត!';
    alert(msg);
  } finally {
    isSubmittingLeave.value = false;
  }
};

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

<style scoped>
.custom-date-trigger {
  cursor: pointer;
}
.custom-date-trigger::-webkit-calendar-picker-indicator {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
  opacity: 0;
  cursor: pointer;
}
</style>
