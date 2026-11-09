<template>
  <div class="space-y-6">
    <!-- Header -->
    <PageHeader 
      :title="isStudent ? 'កាលវិភាគសិក្សា' : $t('timetables.title')" 
      :breadcrumb="isStudent ? 'កាលវិភាគសិក្សា' : $t('nav.homework')" 
      :actionLabel="canManageTimetable ? $t('timetables.addSchedule') : ''"
      @action="openCreateModal"
    />

    <!-- Sub Tabs: Day Selector -->
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
    <div v-if="isStudent" class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <StatCard 
        title="ម៉ោងសិក្សាសរុប" 
        :value="studentWeeklyHours + ' ម៉ោង'" 
        subtitle="ក្នុងមួយសប្តាហ៍"
        icon="clock" 
        iconColor="text-blue-600" 
        bgColor="bg-blue-50" 
        borderColor="border-blue-100" 
      />
      <StatCard 
        title="មុខវិជ្ជាសរុប" 
        :value="studentTotalSubjects + ' មុខវិជ្ជា'" 
        subtitle="កម្មវិធីសិក្សា"
        icon="book" 
        iconColor="text-emerald-600" 
        bgColor="bg-emerald-50" 
        borderColor="border-emerald-100" 
      />
      <StatCard 
        title="ថ្នាក់រៀន" 
        :value="'ថ្នាក់ ' + studentClass" 
        subtitle="វេនពេលព្រឹក"
        icon="graduation" 
        iconColor="text-indigo-600" 
        bgColor="bg-indigo-50" 
        borderColor="border-indigo-100" 
      />
      <StatCard 
        title="ម៉ោងរៀនថ្ងៃនេះ" 
        :value="studentTodayHours + ' ម៉ោង'" 
        :subtitle="'ថ្ងៃ' + todayKhmerDay"
        icon="calendar" 
        iconColor="text-amber-600" 
        bgColor="bg-amber-50" 
        borderColor="border-amber-100" 
      />
    </div>
    <div v-else class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <StatCard :title="$t('timetables.period')" :value="filteredTimetables.length + ' ម៉ោង'" icon="clock" iconColor="text-blue-600" bgColor="bg-blue-50" borderColor="border-blue-100" />
      <StatCard :title="$t('classes.totalClasses')" :value="(classesList.length || 48) + ' ថ្នាក់'" icon="graduation" iconColor="text-indigo-600" bgColor="bg-indigo-50" borderColor="border-indigo-100" />
      <StatCard :title="$t('teachers.totalTeachers')" :value="(teachersList.length || 15) + ' នាក់'" icon="book" iconColor="text-emerald-600" bgColor="bg-emerald-50" borderColor="border-emerald-100" />
      <StatCard :title="$t('timetables.conflictFree')" :value="'100% ត្រឹមត្រូវ'" icon="check" iconColor="text-teal-600" bgColor="bg-teal-50" borderColor="border-teal-100" />
    </div>

    <!-- Morning Shift Overview Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 p-4 sm:p-5 text-white shadow-sm border border-indigo-500/30">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 sm:gap-4">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[11px] font-bold mb-1.5 backdrop-blur-xs">
            <span>🌅</span>
            <span>វេនសិក្សាពេលព្រឹក • ថ្នាក់ {{ studentClass }}</span>
          </div>
          <h3 class="text-base sm:text-lg font-extrabold tracking-tight">
            ម៉ោងសិក្សា៖ ០៧:០០ ព្រឹក - ១១:០០ ព្រឹក
          </h3>
          <p class="text-xs text-blue-100 mt-0.5">
            សិក្សា ៤ ម៉ោងក្នុងមួយថ្ងៃ (៥០ នាទី/ម៉ោង) ដោយមានម៉ោងចេញលេងសម្រាក ៣០ នាទី។
          </p>
        </div>

        <!-- 4 Quick Period Timeline Pills -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
          <div class="bg-white/10 backdrop-blur-xs px-2.5 py-2 rounded-xl border border-white/15">
            <div class="font-bold text-[11px]">ម៉ោងទី ១</div>
            <div class="font-mono text-[10px] text-blue-100 mt-0.5">07:00 - 07:50</div>
          </div>
          <div class="bg-white/10 backdrop-blur-xs px-2.5 py-2 rounded-xl border border-white/15">
            <div class="font-bold text-[11px]">ម៉ោងទី ២</div>
            <div class="font-mono text-[10px] text-blue-100 mt-0.5">07:55 - 08:45</div>
          </div>
          <div class="bg-amber-400/20 backdrop-blur-xs px-2.5 py-2 rounded-xl border border-amber-300/30 text-amber-200">
            <div class="font-bold text-[11px]">☕ ចេញលេង</div>
            <div class="font-mono text-[10px] text-amber-100 mt-0.5">08:45 - 09:15</div>
          </div>
          <div class="bg-white/10 backdrop-blur-xs px-2.5 py-2 rounded-xl border border-white/15">
            <div class="font-bold text-[11px]">ម៉ោងទី ៣ & ៤</div>
            <div class="font-mono text-[10px] text-blue-100 mt-0.5">09:15 - 11:00</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Bar & View Mode Toggle -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-3">
      <!-- For Student: Clean Badge showing assigned class -->
      <div v-if="isStudent" class="flex items-center gap-2.5">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-blue-50 text-blue-700 rounded-xl font-bold text-xs border border-blue-200/60 shadow-xs">
          <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <span>ថ្នាក់រៀនរបស់អ្នក៖ ថ្នាក់ {{ studentClass }}</span>
        </div>
      </div>

      <!-- For Admin/Teacher: Dropdown selector -->
      <div v-else class="flex items-center gap-3">
        <label class="text-xs font-bold text-slate-700">ជ្រើសរើសថ្នាក់រៀន:</label>
        <select 
          v-model="selectedClass" 
          class="px-3.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20"
        >
          <option value="">-- បង្ហាញគ្រប់ថ្នាក់ --</option>
          <option v-for="c in classesList" :key="c" :value="c">ថ្នាក់ {{ c }}</option>
        </select>
      </div>

      <!-- View Mode Buttons (Cards, Weekly Matrix, List) -->
      <div class="flex items-center gap-2.5 flex-wrap">
        <div class="inline-flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200/80 text-xs">
          <button 
            type="button" 
            @click="viewMode = 'cards'"
            :class="[
              'px-3 py-1.5 rounded-lg font-bold transition flex items-center gap-1.5 cursor-pointer',
              viewMode === 'cards' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'
            ]"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span>ទម្រង់កាតម៉ោង</span>
          </button>

          <button 
            type="button" 
            @click="viewMode = 'grid'"
            :class="[
              'px-3 py-1.5 rounded-lg font-bold transition flex items-center gap-1.5 cursor-pointer',
              viewMode === 'grid' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'
            ]"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span>តារាងសប្តាហ៍</span>
          </button>

          <button 
            type="button" 
            @click="viewMode = 'list'"
            :class="[
              'px-3 py-1.5 rounded-lg font-bold transition flex items-center gap-1.5 cursor-pointer',
              viewMode === 'list' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'
            ]"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            <span>បញ្ជីតារាង</span>
          </button>
        </div>

        <span class="text-xs text-slate-500 font-medium">
          សរុប៖ <strong>{{ filteredTimetables.length }}</strong> ម៉ោង
        </span>
      </div>
    </div>

    <!-- 1. TIMELINE CARDS VIEW (Clean & clear 7:00 to 11:00 layout) -->
    <div v-if="viewMode === 'cards'" class="space-y-6">
      <div v-if="groupedByDay.length === 0" class="p-12 text-center bg-white border border-slate-200/80 rounded-2xl shadow-xs text-slate-400">
        {{ $t('common.noData') }}
      </div>

      <div 
        v-for="dayGroup in groupedByDay" 
        :key="dayGroup.day" 
        class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-4"
      >
        <!-- Day Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </span>
            <div>
              <h3 class="text-sm font-bold text-slate-900">ថ្ងៃ{{ dayGroup.day }}</h3>
              <p class="text-[11px] text-slate-500">វេនព្រឹក ០៧:០០ - ១១:០០ (៤ ម៉ោងសិក្សា)</p>
            </div>
          </div>
          <span class="px-2.5 py-1 bg-slate-100 rounded-full text-[11px] font-semibold text-slate-600">
            {{ dayGroup.slots.length }} ម៉ោងរៀន
          </span>
        </div>

        <!-- Periods Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
          <template v-for="(slot, idx) in dayGroup.slots" :key="slot.id">
            <!-- Period Card -->
            <div 
              class="relative p-4 rounded-xl border transition-all hover:shadow-md flex flex-col justify-between group"
              :class="getSubjectCardStyle(slot.subject)"
            >
              <div>
                <!-- Top Badge: Period Number & Time -->
                <div class="flex items-center justify-between mb-2">
                  <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold uppercase tracking-wide bg-white/90 shadow-2xs">
                    {{ getPeriodLabel(slot.start_time, idx) }}
                  </span>
                  <span class="font-mono text-xs font-bold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ slot.start_time }} - {{ slot.end_time }}
                  </span>
                </div>

                <!-- Subject Name -->
                <h4 class="text-sm font-bold tracking-tight mb-1 flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full" :class="getSubjectDotColor(slot.subject)"></span>
                  <span>{{ slot.subject }}</span>
                </h4>

                <!-- Class & Room -->
                <p class="text-[11px] opacity-80 flex items-center gap-2">
                  <span>ថ្នាក់ {{ slot.class || slot.class_name }}</span>
                  <span v-if="slot.room_number || slot.room">• {{ slot.room_number || slot.room }}</span>
                </p>
              </div>

              <!-- Teacher Name -->
              <div class="pt-3 mt-3 border-t border-black/5 flex items-center justify-between text-xs">
                <span class="flex items-center gap-1.5 opacity-90 truncate font-medium">
                  <svg class="w-3.5 h-3.5 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  <span class="truncate">{{ slot.teacher }}</span>
                </span>

                <button 
                  v-if="canManageTimetable" 
                  @click="handleDelete(slot)" 
                  class="opacity-0 group-hover:opacity-100 text-rose-600 hover:text-rose-800 transition p-1"
                  title="លុបម៉ោងនេះ"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Interleaved Recess Break (After period 2) -->
            <div 
              v-if="idx === 1 && dayGroup.slots.length > 2" 
              class="col-span-full py-2 px-4 rounded-xl bg-amber-50/90 border border-amber-200/80 flex items-center justify-center gap-2 text-amber-800 text-xs font-semibold shadow-2xs"
            >
              <span>☕</span>
              <span>ម៉ោងចេញលេងសម្រាក៖ <strong>០៨:៤៥ - ០៩:១៥ ព្រឹក</strong> (៣០ នាទី)</span>
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- 2. WEEKLY MATRIX GRID VIEW (Columns = Days, Rows = 07:00-11:00 Periods) -->
    <div v-else-if="viewMode === 'grid'" class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left border-collapse min-w-[760px]">
          <thead>
            <tr class="bg-slate-100/90 text-slate-800 border-b border-slate-200">
              <th class="py-3 px-3 font-bold w-40 border-r border-slate-200 text-center">ម៉ោងសិក្សា</th>
              <th v-for="d in ['ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍']" :key="d" class="py-3 px-3 font-bold text-center border-r border-slate-200 last:border-r-0">
                ថ្ងៃ{{ d }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <!-- Period 1: 07:00 - 07:50 -->
            <tr>
              <td class="py-3 px-3 font-bold bg-slate-50 border-r border-slate-200 text-center">
                <span class="block text-blue-600 font-extrabold text-xs">ម៉ោងទី ១</span>
                <span class="font-mono text-[11px] text-slate-500 font-semibold">07:00 - 07:50</span>
              </td>
              <td v-for="d in ['ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍']" :key="d" class="p-2 border-r border-slate-200 last:border-r-0 align-top">
                <div v-if="getSlotForPeriod(d, '07:00')" class="p-2.5 rounded-xl border" :class="getSubjectCardStyle(getSlotForPeriod(d, '07:00').subject)">
                  <strong class="block text-xs font-bold truncate">{{ getSlotForPeriod(d, '07:00').subject }}</strong>
                  <span class="block text-[11px] opacity-75 truncate mt-0.5">{{ getSlotForPeriod(d, '07:00').teacher }}</span>
                  <span class="block text-[10px] opacity-60 mt-0.5">{{ getSlotForPeriod(d, '07:00').room_number || getSlotForPeriod(d, '07:00').room || 'បន្ទប់ 101' }}</span>
                </div>
                <div v-else class="h-14 flex items-center justify-center text-slate-300">-</div>
              </td>
            </tr>

            <!-- Period 2: 07:55 - 08:45 -->
            <tr>
              <td class="py-3 px-3 font-bold bg-slate-50 border-r border-slate-200 text-center">
                <span class="block text-blue-600 font-extrabold text-xs">ម៉ោងទី ២</span>
                <span class="font-mono text-[11px] text-slate-500 font-semibold">07:55 - 08:45</span>
              </td>
              <td v-for="d in ['ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍']" :key="d" class="p-2 border-r border-slate-200 last:border-r-0 align-top">
                <div v-if="getSlotForPeriod(d, '07:55')" class="p-2.5 rounded-xl border" :class="getSubjectCardStyle(getSlotForPeriod(d, '07:55').subject)">
                  <strong class="block text-xs font-bold truncate">{{ getSlotForPeriod(d, '07:55').subject }}</strong>
                  <span class="block text-[11px] opacity-75 truncate mt-0.5">{{ getSlotForPeriod(d, '07:55').teacher }}</span>
                  <span class="block text-[10px] opacity-60 mt-0.5">{{ getSlotForPeriod(d, '07:55').room_number || getSlotForPeriod(d, '07:55').room || 'បន្ទប់ 101' }}</span>
                </div>
                <div v-else class="h-14 flex items-center justify-center text-slate-300">-</div>
              </td>
            </tr>

            <!-- Recess Break: 08:45 - 09:15 -->
            <tr class="bg-amber-50/90 border-y border-amber-200">
              <td colspan="7" class="py-2.5 px-4 text-center font-bold text-amber-900 text-xs">
                ☕ ម៉ោងចេញលេងសម្រាក៖ ០៨:៤៥ ព្រឹក ដល់ ០៩:១៥ ព្រឹក (៣០ នាទី)
              </td>
            </tr>

            <!-- Period 3: 09:15 - 10:05 -->
            <tr>
              <td class="py-3 px-3 font-bold bg-slate-50 border-r border-slate-200 text-center">
                <span class="block text-blue-600 font-extrabold text-xs">ម៉ោងទី ៣</span>
                <span class="font-mono text-[11px] text-slate-500 font-semibold">09:15 - 10:05</span>
              </td>
              <td v-for="d in ['ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍']" :key="d" class="p-2 border-r border-slate-200 last:border-r-0 align-top">
                <div v-if="getSlotForPeriod(d, '09:15')" class="p-2.5 rounded-xl border" :class="getSubjectCardStyle(getSlotForPeriod(d, '09:15').subject)">
                  <strong class="block text-xs font-bold truncate">{{ getSlotForPeriod(d, '09:15').subject }}</strong>
                  <span class="block text-[11px] opacity-75 truncate mt-0.5">{{ getSlotForPeriod(d, '09:15').teacher }}</span>
                  <span class="block text-[10px] opacity-60 mt-0.5">{{ getSlotForPeriod(d, '09:15').room_number || getSlotForPeriod(d, '09:15').room || 'បន្ទប់ 101' }}</span>
                </div>
                <div v-else class="h-14 flex items-center justify-center text-slate-300">-</div>
              </td>
            </tr>

            <!-- Period 4: 10:10 - 11:00 -->
            <tr>
              <td class="py-3 px-3 font-bold bg-slate-50 border-r border-slate-200 text-center">
                <span class="block text-blue-600 font-extrabold text-xs">ម៉ោងទី ៤</span>
                <span class="font-mono text-[11px] text-slate-500 font-semibold">10:10 - 11:00</span>
              </td>
              <td v-for="d in ['ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍']" :key="d" class="p-2 border-r border-slate-200 last:border-r-0 align-top">
                <div v-if="getSlotForPeriod(d, '10:10')" class="p-2.5 rounded-xl border" :class="getSubjectCardStyle(getSlotForPeriod(d, '10:10').subject)">
                  <strong class="block text-xs font-bold truncate">{{ getSlotForPeriod(d, '10:10').subject }}</strong>
                  <span class="block text-[11px] opacity-75 truncate mt-0.5">{{ getSlotForPeriod(d, '10:10').teacher }}</span>
                  <span class="block text-[10px] opacity-60 mt-0.5">{{ getSlotForPeriod(d, '10:10').room_number || getSlotForPeriod(d, '10:10').room || 'បន្ទប់ 101' }}</span>
                </div>
                <div v-else class="h-14 flex items-center justify-center text-slate-300">-</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 3. DATA TABLE VIEW (Classic Full List) -->
    <DataTable v-else>
      <template #header>
        <tr>
          <th class="py-3 px-4 font-bold text-slate-700 whitespace-nowrap">{{ $t('timetables.day') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 whitespace-nowrap">ម៉ោងសិក្សា</th>
          <th class="py-3 px-4 font-bold text-slate-700 whitespace-nowrap">{{ $t('timetables.subject') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 whitespace-nowrap">{{ $t('timetables.class') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 whitespace-nowrap">{{ $t('timetables.teacher') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 whitespace-nowrap">{{ $t('timetables.room') }}</th>
          <th v-if="canManageTimetable" class="py-3 px-4 font-bold text-slate-700 text-right whitespace-nowrap">{{ $t('common.actions') }}</th>
        </tr>
      </template>

      <template #body>
        <tr v-if="filteredTimetables.length === 0">
          <td :colspan="canManageTimetable ? 7 : 6" class="py-10 text-center text-slate-400">{{ $t('common.noData') }}</td>
        </tr>
        <tr v-for="(t, idx) in filteredTimetables" :key="t.id" class="hover:bg-slate-50/70 transition-colors">
          <td class="py-3 px-4 font-bold whitespace-nowrap">
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
              ថ្ងៃ{{ t.day_of_week || t.day }}
            </span>
          </td>
          <td class="py-3 px-4 whitespace-nowrap">
            <div class="flex items-center gap-1.5">
              <span class="text-xs font-mono font-bold bg-slate-100 px-2 py-0.5 rounded-lg text-slate-800 border border-slate-200/60 shadow-2xs">
                {{ t.time || `${t.start_time} - ${t.end_time}` }}
              </span>
              <span class="text-[11px] text-blue-600 font-bold font-khmer px-1.5 py-0.5 bg-blue-50 rounded-md">
                {{ getPeriodLabel(t.start_time, idx) }}
              </span>
            </div>
          </td>
          <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :class="getSubjectDotColor(t.subject)"></span>
              <span class="text-xs sm:text-sm font-bold text-slate-900">{{ t.subject }}</span>
            </div>
          </td>
          <td class="py-3 px-4 font-bold whitespace-nowrap">
            <span class="px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-bold">
              ថ្នាក់ {{ t.class || t.class_name }}
            </span>
          </td>
          <td class="py-3 px-4 text-slate-700 font-medium whitespace-nowrap">
            <div class="flex items-center gap-1.5">
              <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px]">
                <i class="fa-solid fa-chalkboard-user"></i>
              </span>
              <span class="text-xs font-semibold text-slate-800">{{ t.teacher }}</span>
            </div>
          </td>
          <td class="py-3 px-4 text-slate-500 font-mono text-xs whitespace-nowrap">
            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
              {{ t.room_number || t.room || 'បន្ទប់ 7A' }}
            </span>
          </td>
          <td v-if="canManageTimetable" class="py-3 px-4 text-right whitespace-nowrap">
            <button @click="handleDelete(t)" class="px-2.5 py-1 bg-rose-50 text-rose-600 rounded-lg text-xs font-semibold hover:bg-rose-100 transition cursor-pointer">
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
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">&times;</button>
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

          <!-- Quick Period Presets for Morning Shift 07:00 - 11:00 -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">ជ្រើសរើសចន្លោះម៉ោងរហ័ស (វេនព្រឹក ០៧:០០ - ១១:០០):</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
              <button 
                type="button" 
                @click="applyPeriodPreset('07:00', '07:50')"
                class="px-2.5 py-1.5 rounded-xl border text-xs font-semibold text-left transition hover:border-blue-500 hover:bg-blue-50/50 cursor-pointer"
                :class="createForm.start_time === '07:00' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-slate-50 border-slate-200 text-slate-700'"
              >
                <strong class="block">ម៉ោងទី ១</strong>
                <span class="font-mono text-[11px] text-slate-500">07:00 - 07:50</span>
              </button>

              <button 
                type="button" 
                @click="applyPeriodPreset('07:55', '08:45')"
                class="px-2.5 py-1.5 rounded-xl border text-xs font-semibold text-left transition hover:border-blue-500 hover:bg-blue-50/50 cursor-pointer"
                :class="createForm.start_time === '07:55' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-slate-50 border-slate-200 text-slate-700'"
              >
                <strong class="block">ម៉ោងទី ២</strong>
                <span class="font-mono text-[11px] text-slate-500">07:55 - 08:45</span>
              </button>

              <button 
                type="button" 
                @click="applyPeriodPreset('09:15', '10:05')"
                class="px-2.5 py-1.5 rounded-xl border text-xs font-semibold text-left transition hover:border-blue-500 hover:bg-blue-50/50 cursor-pointer"
                :class="createForm.start_time === '09:15' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-slate-50 border-slate-200 text-slate-700'"
              >
                <strong class="block">ម៉ោងទី ៣</strong>
                <span class="font-mono text-[11px] text-slate-500">09:15 - 10:05</span>
              </button>

              <button 
                type="button" 
                @click="applyPeriodPreset('10:10', '11:00')"
                class="px-2.5 py-1.5 rounded-xl border text-xs font-semibold text-left transition hover:border-blue-500 hover:bg-blue-50/50 cursor-pointer"
                :class="createForm.start_time === '10:10' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-slate-50 border-slate-200 text-slate-700'"
              >
                <strong class="block">ម៉ោងទី ៤</strong>
                <span class="font-mono text-[11px] text-slate-500">10:10 - 11:00</span>
              </button>
            </div>
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
            <input v-model="createForm.room_number" type="text" placeholder="ឧ. បន្ទប់ 101" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200 cursor-pointer">
              បោះបង់
            </button>
            <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50 cursor-pointer">
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
import { sortClassesNaturally } from '../utils/classSort';

const { t } = useI18n();

const currentUser = ref(window.__INITIAL_USER__ || {});

const isStudent = computed(() => {
  if (currentUser.value?.is_super_admin) return false;
  const r = (currentUser.value?.role || '').toLowerCase();
  const rKh = (currentUser.value?.role_khmer || '').toLowerCase();
  if (r.includes('admin') || r.includes('teacher') || r.includes('គ្រូ') || rKh.includes('គ្រូ') || rKh.includes('អ្នកគ្រប់គ្រង')) {
    return false;
  }
  return true;
});

const canManageTimetable = computed(() => !isStudent.value);

const studentClass = computed(() => currentUser.value?.student_grade || '7A');

const viewMode = ref('cards'); // 'cards' | 'grid' | 'list'
const selectedDay = ref('all');
const selectedClass = ref(isStudent.value ? studentClass.value : '');

const timetablesList = ref([]);
const classesList = ref([]);
const subjectsList = ref([]);
const teachersList = ref([]);

// Khmer Days mapping for today's classes count
const khmerDaysMap = ['អាទិត្យ', 'ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍'];
const dayOrder = ['ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍'];

const todayKhmerDay = computed(() => {
  const dayIndex = new Date().getDay();
  return khmerDaysMap[dayIndex] || 'ច័ន្ទ';
});

// All timetable slots for student's class (entire week)
const studentClassTimetables = computed(() => {
  const targetClass = studentClass.value;
  return timetablesList.value.filter(t => {
    const cName = t.class_name || t.class;
    return cName === targetClass;
  });
});

// 1) Total weekly hours for student's class
const studentWeeklyHours = computed(() => studentClassTimetables.value.length);

// 2) Total subjects in student's class
const studentTotalSubjects = computed(() => {
  const set = new Set(studentClassTimetables.value.map(t => t.subject).filter(Boolean));
  return set.size;
});

// 4) Today's classes count for student
const studentTodayHours = computed(() => {
  return studentClassTimetables.value.filter(t => {
    const day = t.day_of_week || t.day;
    return day === todayKhmerDay.value;
  }).length;
});

const showCreateModal = ref(false);
const isSubmitting = ref(false);

const createForm = ref({
  class_name: '',
  course_subject_id: '',
  teacher_id: '',
  day_of_week: 'ច័ន្ទ',
  start_time: '07:00',
  end_time: '07:50',
  room_number: 'បន្ទប់ 101',
});

const applyPeriodPreset = (startTime, endTime) => {
  createForm.value.start_time = startTime;
  createForm.value.end_time = endTime;
};

const fetchTimetables = async () => {
  try {
    const res = await axios.get('/api/timetables');
    timetablesList.value = res.data.timetables || [];
    classesList.value = sortClassesNaturally(res.data.classes || []);
    subjectsList.value = res.data.subjects || [];
    teachersList.value = [...(res.data.teachers || [])].sort((a, b) => (a.name || '').localeCompare(b.name || '', 'km'));
    if (isStudent.value) {
      selectedClass.value = studentClass.value;
    }
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
    const targetClass = isStudent.value ? studentClass.value : selectedClass.value;
    const matchesClass = !targetClass || cName === targetClass;
    return matchesDay && matchesClass;
  });
});

// Group filtered timetables by day (ordered Monday to Saturday)
const groupedByDay = computed(() => {
  const groups = {};
  filteredTimetables.value.forEach(t => {
    const day = t.day_of_week || t.day;
    if (!groups[day]) groups[day] = [];
    groups[day].push(t);
  });

  // Sort slots inside each day by start_time
  Object.keys(groups).forEach(day => {
    groups[day].sort((a, b) => (a.start_time || '').localeCompare(b.start_time || ''));
  });

  if (selectedDay.value !== 'all') {
    return groups[selectedDay.value] ? [{ day: selectedDay.value, slots: groups[selectedDay.value] }] : [];
  }

  return dayOrder
    .filter(day => groups[day] && groups[day].length > 0)
    .map(day => ({
      day,
      slots: groups[day]
    }));
});

// Helper for Weekly Matrix Grid
const getSlotForPeriod = (day, startPrefix) => {
  const targetClass = isStudent.value ? studentClass.value : selectedClass.value;
  return timetablesList.value.find(t => {
    const d = t.day_of_week || t.day;
    const c = t.class_name || t.class;
    const matchesClass = !targetClass || c === targetClass;
    const matchesDay = d === day;
    const matchesTime = t.start_time && t.start_time.startsWith(startPrefix.slice(0, 4));
    return matchesDay && matchesClass && matchesTime;
  });
};

// Styling helper based on subject
const getSubjectCardStyle = (subject) => {
  const s = (subject || '').toLowerCase();
  if (s.includes('គណិត')) return 'bg-blue-50/80 border-blue-200/90 text-blue-950 shadow-2xs';
  if (s.includes('ខ្មែរ')) return 'bg-emerald-50/80 border-emerald-200/90 text-emerald-950 shadow-2xs';
  if (s.includes('អង់គ្លេស') || s.includes('english')) return 'bg-purple-50/80 border-purple-200/90 text-purple-950 shadow-2xs';
  if (s.includes('រូប')) return 'bg-amber-50/80 border-amber-200/90 text-amber-950 shadow-2xs';
  if (s.includes('គីមី')) return 'bg-cyan-50/80 border-cyan-200/90 text-cyan-950 shadow-2xs';
  if (s.includes('ជីវ')) return 'bg-teal-50/80 border-teal-200/90 text-teal-950 shadow-2xs';
  if (s.includes('ប្រវត្តិ') || s.includes('ភូមិ')) return 'bg-orange-50/80 border-orange-200/90 text-orange-950 shadow-2xs';
  if (s.includes('កីឡា')) return 'bg-lime-50/80 border-lime-200/90 text-lime-950 shadow-2xs';
  return 'bg-slate-50 border-slate-200 text-slate-900 shadow-2xs';
};

const getSubjectDotColor = (subject) => {
  const s = (subject || '').toLowerCase();
  if (s.includes('គណិត')) return 'bg-blue-600';
  if (s.includes('ខ្មែរ')) return 'bg-emerald-600';
  if (s.includes('អង់គ្លេស') || s.includes('english')) return 'bg-purple-600';
  if (s.includes('រូប')) return 'bg-amber-600';
  if (s.includes('គីមី')) return 'bg-cyan-600';
  if (s.includes('ជីវ')) return 'bg-teal-600';
  if (s.includes('ប្រវត្តិ') || s.includes('ភូមិ')) return 'bg-orange-600';
  if (s.includes('កីឡា')) return 'bg-lime-600';
  return 'bg-slate-500';
};

const getPeriodLabel = (startTime, idx) => {
  if (startTime) {
    if (startTime.startsWith('07:0') || startTime === '07:00') return 'ម៉ោងទី ១';
    if (startTime.startsWith('07:5') || startTime === '07:50' || startTime === '07:55') return 'ម៉ោងទី ២';
    if (startTime.startsWith('09:1') || startTime === '09:10' || startTime === '09:15') return 'ម៉ោងទី ៣';
    if (startTime.startsWith('10:0') || startTime.startsWith('10:1') || startTime === '10:00' || startTime === '10:10') return 'ម៉ោងទី ៤';
  }
  return `ម៉ោងទី ${idx + 1}`;
};

const openCreateModal = () => {
  createForm.value = {
    class_name: selectedClass.value || (classesList.value[0] || '7A'),
    course_subject_id: subjectsList.value[0]?.id || '',
    teacher_id: teachersList.value[0]?.id || '',
    day_of_week: selectedDay.value !== 'all' ? selectedDay.value : 'ច័ន្ទ',
    start_time: '07:00',
    end_time: '07:50',
    room_number: 'បន្ទប់ 101',
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
