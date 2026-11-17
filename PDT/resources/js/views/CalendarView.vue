<template>
  <div class="space-y-4 sm:space-y-6 font-khmer pb-10 select-none">
    <!-- Clean Minimal Header Card -->
    <div class="bg-white dark:bg-[#0f172a] border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
      <div class="flex items-center justify-between gap-3">
        <!-- Left: Month/Year & Lunar Subtitle -->
        <div class="min-w-0">
          <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight leading-tight truncate">
            {{ currentSolarMonthKhmer }} {{ currentYear }}
          </h1>
          <p class="text-xs text-slate-500 dark:text-slate-400 font-medium truncate mt-0.5">
            {{ currentLunarSpan.headerText }} • ព.ស. {{ currentLunarSpan.beYear }}
          </p>
        </div>

        <!-- Right: Action Group (ថ្ងៃនេះ, <, >) -->
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
          <!-- Today Jump Button -->
          <button
            type="button"
            @click="jumpToToday"
            class="px-3 py-1.5 sm:py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-500 text-white shadow-xs transition cursor-pointer active:scale-95 flex items-center gap-1.5"
          >
            <i class="fa-solid fa-rotate-right text-[10px]"></i>
            <span>ថ្ងៃនេះ</span>
          </button>

          <!-- Prev Month Arrow (<) -->
          <button
            type="button"
            @click="prevMonth"
            class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-100 dark:bg-slate-800/90 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 flex items-center justify-center transition cursor-pointer active:scale-90"
            title="ខែមុន"
          >
            <i class="fa-solid fa-chevron-left text-xs"></i>
          </button>

          <!-- Next Month Arrow (>) -->
          <button
            type="button"
            @click="nextMonth"
            class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-100 dark:bg-slate-800/90 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 flex items-center justify-center transition cursor-pointer active:scale-90"
            title="ខែបន្ទាប់"
          >
            <i class="fa-solid fa-chevron-right text-xs"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Main Split View: Minimal 7-Column Calendar Grid & Events Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-start">
      <!-- LEFT: Clean, Classic Khmer Calendar Grid Cells -->
      <div class="lg:col-span-2 bg-white dark:bg-[#0f172a] border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 sm:p-6 shadow-sm flex flex-col">
        <!-- Weekday Headers (អាទិ to សៅរ៍) -->
        <div class="grid grid-cols-7 gap-1.5 sm:gap-2 mb-3 text-center border-b border-slate-100 dark:border-slate-800/80 pb-2.5">
          <div 
            v-for="(dayShort, idx) in ['អាទិ', 'ចន្ទ', 'អង្គ', 'ពុធ', 'ព្រហ', 'សុក្រ', 'សៅរ៍']" 
            :key="dayShort"
            :class="[
              'py-1 text-[11px] sm:text-xs font-bold rounded',
              idx === 0 ? 'text-rose-600 dark:text-rose-400' : '',
              idx === 6 ? 'text-blue-600 dark:text-blue-400' : 'text-slate-500 dark:text-slate-400'
            ]"
          >
            {{ dayShort }}
          </div>
        </div>

        <!-- 7-Column Calendar Grid Cells -->
        <div class="grid grid-cols-7 gap-1.5 sm:gap-2.5 select-none">
          <div
            v-for="(cell, index) in calendarCells"
            :key="index"
            @click="handleDateClick(cell)"
            :class="[
              'min-h-[64px] sm:min-h-[96px] p-1.5 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between cursor-pointer relative group',
              cell.isCurrentMonth 
                ? 'bg-slate-50/60 dark:bg-slate-900/60 border-slate-200/70 dark:border-slate-800/80 hover:border-blue-500/50 hover:bg-blue-50/20 dark:hover:bg-blue-500/5' 
                : 'bg-transparent border-transparent opacity-25 hover:opacity-50',
              cell.isToday 
                ? 'ring-2 ring-blue-500 dark:ring-blue-500 bg-blue-500/10 dark:bg-blue-600/15 !border-blue-500 shadow-2xs' 
                : '',
              selectedCellDate === cell.dateString 
                ? 'ring-2 ring-amber-400 dark:ring-amber-400' 
                : ''
            ]"
          >
            <!-- Top: Gregorian Date Number & Holy Day Icon -->
            <div class="flex items-center justify-between w-full leading-none">
              <span
                :class="[
                  'text-xs sm:text-base font-extrabold font-mono',
                  cell.isToday
                    ? 'text-blue-600 dark:text-blue-400'
                    : cell.dayOfWeek === 0
                      ? 'text-rose-600 dark:text-rose-400'
                      : cell.dayOfWeek === 6
                        ? 'text-blue-600 dark:text-blue-400'
                        : cell.isCurrentMonth
                          ? 'text-slate-900 dark:text-slate-100 group-hover:text-blue-500'
                          : 'text-slate-400 dark:text-slate-600'
                ]"
              >
                {{ cell.solarDay }}
              </span>

              <!-- 🪷 Holy Day (ថ្ងៃសីល) Icon -->
              <span v-if="cell.lunar.isHolyDay" class="text-[11px] sm:text-xs" title="ថ្ងៃសីល">
                🪷
              </span>
              <span v-else-if="cell.lunar.isShavingDay" class="w-1.5 h-1.5 rounded-full bg-indigo-400" title="ថ្ងៃកោរ"></span>
            </div>

            <!-- Directly Underneath: Khmer Lunar Phase (10px–11px) -->
            <div class="text-left mt-0.5">
              <span 
                :class="[
                  'text-[10px] sm:text-[11px] font-medium block leading-tight truncate',
                  cell.lunar.isHolyDay
                    ? 'text-amber-600 dark:text-amber-400 font-bold'
                    : cell.lunar.isShavingDay
                      ? 'text-indigo-600 dark:text-indigo-400 font-semibold'
                      : cell.dayOfWeek === 0
                        ? 'text-rose-500/90 dark:text-rose-400/80'
                        : cell.dayOfWeek === 6
                          ? 'text-blue-500/90 dark:text-blue-400/80'
                          : 'text-slate-500 dark:text-slate-400'
                ]"
              >
                {{ cell.lunar.cellLunarText }}
              </span>
            </div>

            <!-- Event Representation: -->
            <!-- Mobile View (Dots only to stay neat, balanced and airy) -->
            <div v-if="cell.events.length > 0" class="sm:hidden flex items-center justify-center gap-1 mt-0.5">
              <span
                v-for="evt in cell.events.slice(0, 3)"
                :key="evt.id"
                class="w-1.5 h-1.5 rounded-full shrink-0"
                :class="getEventDotStyle(evt.category)"
              ></span>
            </div>

            <!-- Desktop View (Neat Pill Badge) -->
            <div v-if="cell.events.length > 0" class="hidden sm:block mt-1 space-y-0.5 overflow-hidden">
              <div
                v-for="evt in cell.events.slice(0, 1)"
                :key="evt.id"
                @click.stop="openEventModal(evt)"
                :class="[
                  'px-1.5 py-0.5 rounded text-[9px] font-medium truncate border flex items-center gap-1 transition cursor-pointer hover:scale-[1.02]',
                  getEventPillStyle(evt.category)
                ]"
                :title="evt.title"
              >
                <span class="w-1 h-1 rounded-full shrink-0" :class="getEventDotStyle(evt.category)"></span>
                <span class="truncate">{{ evt.title }}</span>
              </div>
              <span v-if="cell.events.length > 1" class="text-[8px] font-bold text-slate-400 dark:text-slate-500 block leading-none">
                +{{ cell.events.length - 1 }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT: Chronological Events List -->
      <div class="lg:col-span-1 bg-white dark:bg-[#0f172a] border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-sm flex flex-col">
        <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-slate-100 dark:border-slate-800">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center text-xs">
              <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
              <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white">ព្រឹត្តិការណ៍ និងថ្ងៃឈប់សម្រាក</h3>
            </div>
          </div>
          <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
            {{ filteredUpcomingEvents.length }} កម្មវិធី
          </span>
        </div>

        <!-- Chronological List of Events -->
        <div v-if="filteredUpcomingEvents.length > 0" class="space-y-2.5 max-h-[560px] overflow-y-auto pr-1">
          <div
            v-for="evt in filteredUpcomingEvents"
            :key="evt.id"
            @click="openEventModal(evt)"
            class="p-2.5 sm:p-3 rounded-xl border border-slate-200/80 dark:border-slate-800/90 bg-slate-50/60 dark:bg-slate-900/40 hover:bg-blue-50/30 dark:hover:bg-slate-800/50 hover:border-blue-500/40 transition cursor-pointer group"
          >
            <div class="flex items-start gap-2.5">
              <!-- Date Badge -->
              <div 
                :class="[
                  'w-11 h-11 rounded-xl flex flex-col items-center justify-center shrink-0 border text-center shadow-2xs',
                  getEventBadgeStyle(evt.category)
                ]"
              >
                <span class="text-[9px] font-bold leading-tight">{{ evt.dayOfWeekKm }}</span>
                <span class="text-base font-extrabold font-mono leading-none mt-0.5">{{ evt.dayNumber }}</span>
              </div>

              <!-- Content -->
              <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-1 mb-0.5">
                  <span 
                    :class="[
                      'px-1.5 py-0.5 rounded text-[9px] font-semibold border inline-block',
                      getEventCategoryTagStyle(evt.category)
                    ]"
                  >
                    {{ getCategoryLabel(evt.category) }}
                  </span>
                  <span v-if="evt.isHolyDay" class="text-[9px] font-bold text-amber-500 flex items-center gap-0.5">
                    <span>🪷</span>
                    <span>ថ្ងៃសីល</span>
                  </span>
                </div>

                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition leading-snug">
                  {{ evt.title }}
                </h4>

                <div class="mt-1 flex items-center gap-2 text-[10px] text-slate-500 dark:text-slate-400">
                  <span class="truncate">{{ evt.time || evt.dateRangeKm }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-10 px-3 flex flex-col items-center justify-center">
          <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 text-sm mb-2">
            <i class="fa-regular fa-calendar-xmark"></i>
          </div>
          <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">ពុំមានព្រឹត្តិការណ៍ក្នុងខែនេះទេ</p>
        </div>
      </div>
    </div>

    <!-- 6. Event & Lunar Detail Modal -->
    <Teleport to="body">
      <div 
        v-if="selectedEvent || selectedDayDetails" 
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs font-khmer transition-opacity"
        @click.self="closeDetailModal"
      >
        <div class="bg-white dark:bg-[#0f172a] w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200/90 dark:border-slate-800 overflow-hidden transform transition-all flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-200">
          <!-- Modal Header -->
          <div class="bg-white dark:bg-[#0f172a] border-b border-slate-100 dark:border-slate-800 p-4 sm:p-5 flex items-start justify-between shrink-0">
            <div class="flex items-start gap-3">
              <div 
                :class="[
                  'w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 border shadow-xs',
                  modalHeaderBadgeStyle
                ]"
              >
                <i :class="modalHeaderIcon"></i>
              </div>
              <div>
                <span 
                  :class="[
                    'px-2 py-0.5 rounded text-[10px] font-semibold border inline-block mb-1',
                    modalCategoryTagStyle
                  ]"
                >
                  {{ modalCategoryLabel }}
                </span>
                <h3 class="font-bold text-sm sm:text-base leading-snug text-slate-900 dark:text-slate-100">
                  {{ modalTitle }}
                </h3>
              </div>
            </div>

            <button 
              @click="closeDetailModal" 
              type="button"
              class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center transition cursor-pointer text-xs font-semibold shrink-0 ml-2"
            >
              ✕
            </button>
          </div>

          <!-- Modal Body -->
          <div class="p-4 sm:p-5 space-y-3.5 overflow-y-auto text-xs flex-1">
            <!-- Full Khmer Lunar Calculation Box -->
            <div class="bg-gradient-to-br from-amber-500/10 via-slate-50 to-blue-500/10 dark:from-amber-950/20 dark:via-slate-900/60 dark:to-blue-950/20 rounded-xl p-3.5 border border-amber-500/20 dark:border-slate-700/60 space-y-1.5">
              <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                  <span>🪷</span>
                  <span>កាលបរិច្ឆេទចន្ទគតិខ្មែរ (Khmer Lunar Calendar)</span>
                </span>
                <div class="flex items-center gap-1.5">
                  <span v-if="activeLunarDetails.isHolyDay" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white shadow-xs">
                    ថ្ងៃសីល
                  </span>
                  <span v-else-if="activeLunarDetails.isShavingDay" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500 text-white shadow-xs">
                    ថ្ងៃកោរ
                  </span>
                  <button
                    type="button"
                    @click="copyActiveModalDate"
                    class="w-6 h-6 rounded-lg bg-white/80 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700 flex items-center justify-center transition cursor-pointer active:scale-90 ml-0.5"
                    :title="modalCopied ? 'បានចម្លងរួចរាល់' : 'ចម្លងកាលបរិច្ឆេទ'"
                  >
                    <i :class="modalCopied ? 'fa-solid fa-check text-emerald-500 text-[10px]' : 'fa-regular fa-copy text-[10px]'"></i>
                  </button>
                </div>
              </div>
              <p class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 leading-relaxed">
                {{ activeLunarDetails.formattedKhmerFull }}
              </p>
              <div class="flex items-center gap-2.5 pt-1.5 border-t border-slate-200/50 dark:border-slate-800 text-[11px] text-slate-600 dark:text-slate-300 flex-wrap">
                <span>ឆ្នាំ{{ activeLunarDetails.animalYearName }} {{ activeLunarDetails.animalEmoji }}</span>
                <span>•</span>
                <span>{{ activeLunarDetails.sakName }}</span>
                <span>•</span>
                <span>ព.ស. {{ activeLunarDetails.beYearKm }}</span>
              </div>
            </div>

            <!-- Quick Action: View Today Timetable ("មើលកាលវិភាគសិក្សាថ្ងៃនេះ") -->
            <router-link
              to="/timetables"
              class="w-full py-2.5 px-4 rounded-xl bg-blue-600/10 hover:bg-blue-600/20 text-blue-600 dark:text-blue-400 border border-blue-500/25 font-bold flex items-center justify-center gap-2 transition active:scale-95 shadow-2xs group cursor-pointer"
            >
              <i class="fa-solid fa-clock-rotate-left group-hover:-rotate-45 transition-transform duration-200"></i>
              <span>មើលកាលវិភាគសិក្សាថ្ងៃនេះ (View Timetable)</span>
              <i class="fa-solid fa-arrow-right text-[11px] group-hover:translate-x-1 transition-transform"></i>
            </router-link>

            <!-- Traditional & Zodiac Info Grid (2-Column clean grid on all screen sizes) -->
            <div class="grid grid-cols-2 gap-3">
              <!-- Card 1: Western Zodiac Sign (តារានិករ) -->
              <div class="bg-slate-50 dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-800 rounded-xl p-3 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">តារានិករ (ZODIAC)</span>
                <div class="flex items-center gap-2">
                  <span class="text-xl shrink-0">{{ activeLunarDetails.zodiac?.symbol || '♏' }}</span>
                  <div class="min-w-0">
                    <span class="font-bold text-slate-800 dark:text-slate-100 text-xs block truncate leading-tight">
                      {{ activeLunarDetails.zodiac?.nameKm || 'វិច្ឆិករាសី ឬ ព្យាឃ្រ' }}
                    </span>
                    <span class="text-[10px] text-slate-400 block truncate font-medium">
                      {{ activeLunarDetails.zodiac?.nameEn || 'Scorpio' }}
                    </span>
                  </div>
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-200/50 dark:border-slate-800/80 leading-normal truncate">
                  {{ activeLunarDetails.zodiac?.note }}
                </p>
              </div>

              <!-- Card 2: Conflict Animal & Auspicious Direction (ឆ្នាំឆុង / ហុងស៊ុយ) -->
              <div class="bg-slate-50 dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-800 rounded-xl p-3 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">ឆ្នាំឆុង / ហុងស៊ុយ</span>
                <div class="flex items-center gap-2">
                  <span class="text-xl shrink-0">{{ activeLunarDetails.fengShui?.conflictEmoji || '🐀' }}</span>
                  <div class="min-w-0">
                    <span class="font-bold text-rose-600 dark:text-rose-400 text-xs block truncate leading-tight">
                      ឆុងឆ្នាំ {{ activeLunarDetails.fengShui?.conflictAnimal }}
                    </span>
                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 block truncate font-medium">
                      {{ activeLunarDetails.fengShui?.directionCode }}
                    </span>
                  </div>
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-200/50 dark:border-slate-800/80 leading-normal truncate" :title="activeLunarDetails.fengShui?.directionName">
                  {{ activeLunarDetails.fengShui?.directionName }}
                </p>
              </div>
            </div>

            <!-- Event Details (if viewing an event) -->
            <div v-if="selectedEvent" class="bg-slate-50 dark:bg-slate-800/40 rounded-xl p-3.5 border border-slate-200/70 dark:border-slate-700/60 space-y-2">
              <div class="flex items-start gap-2">
                <i class="fa-regular fa-calendar-check text-blue-500 text-xs mt-0.5"></i>
                <div>
                  <span class="block text-[10px] font-medium text-slate-400">កាលបរិច្ឆេទកម្មវិធី</span>
                  <span class="font-bold text-slate-800 dark:text-slate-200 text-xs">
                    {{ selectedEvent.dateRangeKm }}
                  </span>
                  <span v-if="selectedEvent.durationDays" class="text-[10px] text-blue-600 dark:text-blue-400 font-semibold ml-1.5">
                    ({{ selectedEvent.durationDays }} ថ្ងៃ)
                  </span>
                </div>
              </div>

              <div v-if="selectedEvent.time" class="flex items-start gap-2 pt-1.5 border-t border-slate-200/50 dark:border-slate-700/50">
                <i class="fa-regular fa-clock text-amber-500 text-xs mt-0.5"></i>
                <div>
                  <span class="block text-[10px] font-medium text-slate-400">ពេលវេលា</span>
                  <span class="font-semibold text-slate-700 dark:text-slate-300">
                    {{ selectedEvent.time }}
                  </span>
                </div>
              </div>

              <div v-if="selectedEvent.location" class="flex items-start gap-2 pt-1.5 border-t border-slate-200/50 dark:border-slate-700/50">
                <i class="fa-solid fa-location-dot text-rose-500 text-xs mt-0.5"></i>
                <div>
                  <span class="block text-[10px] font-medium text-slate-400">ទីតាំង</span>
                  <span class="font-semibold text-slate-700 dark:text-slate-300">
                    {{ selectedEvent.location }}
                  </span>
                </div>
              </div>

              <div v-if="selectedEvent.description" class="pt-1.5 border-t border-slate-200/50 dark:border-slate-700/50">
                <span class="block text-[10px] font-medium text-slate-400 mb-1">ការណែនាំ</span>
                <p class="text-slate-700 dark:text-slate-300 leading-relaxed text-[11px]">
                  {{ selectedEvent.description }}
                </p>
              </div>
            </div>
          </div>

          <!-- Modal Footer with 3 Balanced Navigation Buttons -->
          <div class="bg-white dark:bg-[#0f172a] border-t border-slate-100 dark:border-slate-800 p-3 sm:p-4 grid grid-cols-3 gap-2.5 shrink-0">
            <!-- Previous Day Button (< ថ្ងៃមុន) -->
            <button
              type="button"
              @click="goToPrevDay"
              class="w-full py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-slate-700 transition flex items-center justify-center gap-1.5 cursor-pointer active:scale-95 shadow-2xs"
            >
              <i class="fa-solid fa-chevron-left text-[10px]"></i>
              <span>ថ្ងៃមុន</span>
            </button>

            <!-- Close Button (បិទផ្ទាំង) -->
            <button
              type="button"
              @click="closeDetailModal"
              class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition cursor-pointer active:scale-95 text-center"
            >
              បិទផ្ទាំង
            </button>

            <!-- Next Day Button (ថ្ងៃបន្ទាប់ >) -->
            <button
              type="button"
              @click="goToNextDay"
              class="w-full py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-slate-700 transition flex items-center justify-center gap-1.5 cursor-pointer active:scale-95 shadow-2xs"
            >
              <span>ថ្ងៃបន្ទាប់</span>
              <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { 
  toKhmerNumber, 
  getKhmerLunarDetails, 
  getSolarMonthLunarSpan, 
  SOLAR_MONTHS_KM, 
  SOLAR_MONTHS_EN,
  WEEKDAYS_SHORT_KM
} from '../utils/khmerCalendar';

// State
const selectedCategory = ref('all');
const searchQuery = ref('');
const showSearchInput = ref(false);
const searchInputRef = ref(null);
const selectedCellDate = ref(null);
const selectedEvent = ref(null);
const selectedDayDetails = ref(null);
const copied = ref(false);
const modalCopied = ref(false);

const toggleSearch = async () => {
  showSearchInput.value = !showSearchInput.value;
  if (showSearchInput.value) {
    await nextTick();
    searchInputRef.value?.focus();
  } else {
    searchQuery.value = '';
  }
};

// Starting date: November 2026 (Academic Year 2026-2027)
const currentYear = ref(2026);
const currentMonth = ref(10); // 10 = November (0-indexed)

const todayYear = 2026;
const todayMonth = 10;
const todayDay = 17;
const todayJsDate = new Date(todayYear, todayMonth, todayDay);

// Active currently viewed Date object in Modal
const currentModalJsDate = ref(new Date(todayYear, todayMonth, todayDay));

// Real-time Khmer lunar calculation for today
const todayLunarInfo = computed(() => {
  return getKhmerLunarDetails(todayJsDate);
});

// Month header calculations
const currentSolarMonthKhmer = computed(() => SOLAR_MONTHS_KM[currentMonth.value]);
const currentSolarMonthEn = computed(() => SOLAR_MONTHS_EN[currentMonth.value]);
const currentLunarSpan = computed(() => getSolarMonthLunarSpan(currentYear.value, currentMonth.value));

// Copy formal date to clipboard
const copyFormalDate = async () => {
  try {
    await navigator.clipboard.writeText(todayLunarInfo.value.formattedKhmerFull);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2500);
  } catch (e) {
    console.warn('Copy failed:', e);
  }
};

const copyActiveModalDate = async () => {
  try {
    const text = activeLunarDetails.value?.formattedKhmerFull || '';
    if (text) {
      await navigator.clipboard.writeText(text);
      modalCopied.value = true;
      setTimeout(() => { modalCopied.value = false; }, 2500);
    }
  } catch (e) {
    console.warn('Copy failed:', e);
  }
};

// Academic Events & Public Holidays for Academic Year 2026-2027
const academicEvents = ref([
  // --- November 2026 ---
  {
    id: 1,
    title: 'បើកបវេសនកាលឆ្នាំសិក្សាថ្មី ២០២៦-២០២៧',
    category: 'event',
    startDate: '2026-11-02',
    endDate: '2026-11-02',
    dayNumber: 2,
    dayOfWeekKm: 'ចន្ទ',
    time: 'ម៉ោង ០៧:០០ - ០៩:៣០ ព្រឹក',
    location: 'ទីធ្លាសាលារៀន ជំនាន់ថ្មី PDT',
    target: 'សិស្សានុសិស្ស លោកគ្រូ-អ្នកគ្រូ និងមាតាបិតា',
    description: 'ពិធីបើកបវេសនកាលផ្លូវការ ការថ្លែងសុន្ទរកថាស្វាគមន៍ដោយគណៈគ្រប់គ្រងសាលា និងការណែនាំពីបទបញ្ជាផ្ទៃក្នុង។',
    dateRangeKm: 'ថ្ងៃទី ០២ ខែវិច្ឆិកា ឆ្នាំ២០២៦',
    durationDays: 1,
  },
  {
    id: 2,
    title: 'របាំព្រះរាជទ្រព្យ ត្រូវបានចុះក្នុងបញ្ជីបេតិកភណ្ឌវប្បធម៌អរូបីនៃមនុស្សជាតិ ខួបលើកទី២៣',
    category: 'event',
    startDate: '2026-11-07',
    endDate: '2026-11-07',
    dayNumber: 7,
    dayOfWeekKm: 'សៅរ៍',
    time: 'ពេញមួយថ្ងៃ',
    location: 'ទូទាំងប្រទេស',
    target: 'សាធារណៈជនទូទៅ',
    description: 'ទិវារំលឹកខួបនៃការដាក់បញ្ចូលរបាំព្រះរាជទ្រព្យខ្មែរក្នុងបញ្ជីបេតិកភណ្ឌវប្បធម៌អរូបីពិភពលោករបស់អង្គការយូណេស្កូ (UNESCO)។',
    dateRangeKm: 'ថ្ងៃទី ០៧ ខែវិច្ឆិកា ឆ្នាំ២០២៦',
    durationDays: 1,
  },
  {
    id: 3,
    title: 'ពិធីបុណ្យឯករាជ្យជាតិ ខួបលើកទី ៧៣ (Independence Day)',
    category: 'holiday',
    startDate: '2026-11-09',
    endDate: '2026-11-09',
    dayNumber: 9,
    dayOfWeekKm: 'ចន្ទ',
    time: 'ពេញមួយថ្ងៃ',
    location: 'ទូទាំងប្រទេស',
    target: 'សាធារណៈជនទូទៅ',
    description: 'ថ្ងៃឈប់សម្រាកជាផ្លូវការដើម្បីរំលឹកខួបនៃការទទួលបានឯករាជ្យជាតិពេញលេញពីអាណានិគមបារាំង (៩ វិច្ឆិកា ១៩៥៣ - ៩ វិច្ឆិកា ២០២៦)។',
    dateRangeKm: 'ថ្ងៃទី ០៩ ខែវិច្ឆិកា ឆ្នាំ២០២៦',
    durationDays: 1,
  },
  {
    id: 4,
    title: 'ការប្រឡងប្រចាំខែវិច្ឆិកា (Monthly Test)',
    category: 'exam',
    startDate: '2026-11-16',
    endDate: '2026-11-20',
    dayNumber: 16,
    dayOfWeekKm: 'ចន្ទ',
    time: 'ម៉ោង ០៧:៣០ ព្រឹក - ១១:៣០ ព្រឹក',
    location: 'តាមបន្ទប់សិក្សាថ្នាក់នីមួយៗ',
    target: 'សិស្សគ្រប់កម្រិតថ្នាក់ទី ៧ ដល់ ១២',
    description: 'ការប្រឡងវាស់ស្ទង់ចំណេះដឹងប្រចាំខែទី១ នៃឆ្នាំសិក្សាថ្មី ដើម្បីវាយតម្លៃការយល់ដឹងលើមុខវិជ្ជាស្នូល។',
    dateRangeKm: 'ថ្ងៃទី ១៦ ដល់ ២០ ខែវិច្ឆិកា ឆ្នាំ២០២៦',
    durationDays: 5,
  },
  {
    id: 5,
    title: 'ព្រះរាជពិធីបុណ្យអុំទូក បណ្តែតប្រទីប និងសំពះព្រះខែ អកអំបុក (Water Festival)',
    category: 'holiday',
    startDate: '2026-11-23',
    endDate: '2026-11-25',
    dayNumber: 23,
    dayOfWeekKm: 'ចន្ទ',
    time: 'ពេញមួយថ្ងៃ (៣ ថ្ងៃ)',
    location: 'ទូទាំងព្រះរាជាណាចក្រកម្ពុជា',
    target: 'សិស្សានុសិស្ស និងបុគ្គលិកអប់រំទាំងអស់',
    description: 'ពិធីបុណ្យប្រពៃណីជាតិខ្មែរដ៏ធំប្រចាំឆ្នាំ ឈប់សម្រាកចំនួន ៣ ថ្ងៃ ដើម្បីចូលរួមទស្សនាការប្រណាំងទូកង និងសំពះព្រះខែ។',
    dateRangeKm: 'ថ្ងៃទី ២៣ ដល់ ២៥ ខែវិច្ឆិកា ឆ្នាំ២០២៦',
    durationDays: 3,
  },
  {
    id: 6,
    title: 'ទិវាបរិស្ថាន និងអនាម័យសាលារៀន (School Cleanliness & Eco Day)',
    category: 'event',
    startDate: '2026-11-28',
    endDate: '2026-11-28',
    dayNumber: 28,
    dayOfWeekKm: 'សៅរ៍',
    time: 'ម៉ោង ០៨:០០ - ១០:៣០ ព្រឹក',
    location: 'បរិវេណសាលា និងសួនជីវចម្រុះ',
    target: 'យុវជនកាកបាទក្រហម និងសិស្សានុសិស្សទាំងអស់',
    description: 'សកម្មភាពដាំដើមឈើ រៀបចំសួនបៃតង និងសម្អាតបរិវេណសាលា ដើម្បីលើកកម្ពស់សុខុមាលភាព និងបរិស្ថានស្អាត។',
    dateRangeKm: 'ថ្ងៃទី ២៨ ខែវិច្ឆិកា ឆ្នាំ២០២៦',
    durationDays: 1,
  },

  // --- December 2026 ---
  {
    id: 7,
    title: 'ទិវាសិទ្ធិមនុស្សអន្តរជាតិ (International Human Rights Day)',
    category: 'holiday',
    startDate: '2026-12-10',
    endDate: '2026-12-10',
    dayNumber: 10,
    dayOfWeekKm: 'ព្រហ',
    time: 'ពេញមួយថ្ងៃ',
    location: 'ទូទាំងប្រទេស',
    target: 'សាធារណៈជនទូទៅ',
    description: 'ទិវាសិទ្ធិមនុស្សអន្តរជាតិ - សាលារៀនឈប់សម្រាកមួយថ្ងៃ។',
    dateRangeKm: 'ថ្ងៃទី ១០ ខែធ្នូ ឆ្នាំ២០២៦',
    durationDays: 1,
  },
  {
    id: 8,
    title: 'ពិព័រណ៍វិទ្យាសាស្ត្រ និងបច្ចេកវិទ្យា STEM (Science Fair 2026)',
    category: 'event',
    startDate: '2026-12-18',
    endDate: '2026-12-18',
    dayNumber: 18,
    dayOfWeekKm: 'សុក្រ',
    time: 'ម៉ោង ០៨:០០ ព្រឹក - ០៤:០០ រសៀល',
    location: 'សាលប្រជុំធំ និងបន្ទប់ពិសោធន៍វិទ្យាសាស្ត្រ',
    target: 'សិស្សានុសិស្សគ្រប់កម្រិត និងមាតាបិតា',
    description: 'ការតាំងបង្ហាញស្នាដៃស្រាវជ្រាវ គំរូរ៉ូបូត គម្រោង AI និងការពិសោធន៍រូបវិទ្យា-គីមីវិទ្យារបស់សិស្សានុសិស្ស។',
    dateRangeKm: 'ថ្ងៃទី ១៨ ខែធ្នូ ឆ្នាំ២០២៦',
    durationDays: 1,
  },
  {
    id: 9,
    title: 'ការប្រឡងបញ្ចប់ឆមាសលើកទី ១ (First Semester Exam)',
    category: 'exam',
    startDate: '2026-12-21',
    endDate: '2026-12-26',
    dayNumber: 21,
    dayOfWeekKm: 'ចន្ទ',
    time: 'ម៉ោង ០៧:៣០ ព្រឹក - ០៤:៣០ រសៀល',
    location: 'មណ្ឌលប្រឡងសាលារៀន ជំនាន់ថ្មី PDT',
    target: 'សិស្សានុសិស្សថ្នាក់ទី ៧ ដល់ ទី ១២',
    description: 'ការប្រឡងផ្លូវការបញ្ចប់ឆមាសទី១ គ្រប់មុខវិជ្ជាស្នូល និងភាសាបរទេស តាមស្តង់ដារក្រសួងអប់រំ។',
    dateRangeKm: 'ថ្ងៃទី ២១ ដល់ ២៦ ខែធ្នូ ឆ្នាំ២០២៦',
    durationDays: 6,
  },

  // --- January 2027 ---
  {
    id: 10,
    title: 'ទិវាចូលឆ្នាំសកល (International New Year Day)',
    category: 'holiday',
    startDate: '2027-01-01',
    endDate: '2027-01-01',
    dayNumber: 1,
    dayOfWeekKm: 'សុក្រ',
    time: 'ពេញមួយថ្ងៃ',
    location: 'ទូទាំងពិភពលោក',
    target: 'បុគ្គលិក និងសិស្សានុសិស្សទាំងអស់',
    description: 'ទិវាបុណ្យចូលឆ្នាំសកល ២០២៧ - សាលាឈប់សម្រាក។',
    dateRangeKm: 'ថ្ងៃទី ០១ ខែមករា ឆ្នាំ២០២៧',
    durationDays: 1,
  },
  {
    id: 11,
    title: 'ទិវាជ័យជម្នះលើរបបប្រល័យពូជសាសន៍ (Victory Day)',
    category: 'holiday',
    startDate: '2027-01-07',
    endDate: '2027-01-07',
    dayNumber: 7,
    dayOfWeekKm: 'ព្រហ',
    time: 'ពេញមួយថ្ងៃ',
    location: 'ទូទាំងប្រទេស',
    target: 'សាធារណៈជនទូទៅ',
    description: 'រំលឹកទិវាជ័យជម្នះ ៧ មករា - ឈប់សម្រាកផ្លូវការ។',
    dateRangeKm: 'ថ្ងៃទី ០៧ ខែមករា ឆ្នាំ២០២៧',
    durationDays: 1,
  },

  // --- April 2027 ---
  {
    id: 12,
    title: 'ពិធីបុណ្យចូលឆ្នាំថ្មីប្រពៃណីជាតិខ្មែរ (Khmer New Year)',
    category: 'holiday',
    startDate: '2027-04-14',
    endDate: '2027-04-16',
    dayNumber: 14,
    dayOfWeekKm: 'ពុធ',
    time: 'ពេញមួយថ្ងៃ (៣ ថ្ងៃ)',
    location: 'ទូទាំងប្រទេស',
    target: 'សិស្សានុសិស្ស និងលោកគ្រូ-អ្នកគ្រូទាំងអស់',
    description: 'ពិធីបុណ្យចូលឆ្នាំថ្មីប្រពៃណីជាតិខ្មែរ - ឈប់សម្រាកប្រពៃណីជាតិ។',
    dateRangeKm: 'ថ្ងៃទី ១៤ ដល់ ១៦ ខែមេសា ឆ្នាំ២០២៧',
    durationDays: 3,
  }
]);

// Events matcher for date
const getEventsForDate = (dateStr) => {
  return academicEvents.value.filter(evt => {
    if (selectedCategory.value !== 'all' && selectedCategory.value !== 'holy_day' && evt.category !== selectedCategory.value) {
      return false;
    }
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase();
      const match = evt.title.toLowerCase().includes(q) || 
                    (evt.description && evt.description.toLowerCase().includes(q)) ||
                    (evt.location && evt.location.toLowerCase().includes(q));
      if (!match) return false;
    }
    return dateStr >= evt.startDate && dateStr <= evt.endDate;
  });
};

// Compute calendar cells with momentkh lunar calculations (Sunday to Saturday)
const calendarCells = computed(() => {
  const year = currentYear.value;
  const month = currentMonth.value;

  const firstDayOfMonth = new Date(year, month, 1);
  const totalDaysInMonth = new Date(year, month + 1, 0).getDate();
  const totalDaysInPrevMonth = new Date(year, month, 0).getDate();

  const firstDayOfWeek = firstDayOfMonth.getDay();

  const cells = [];

  // Leading days from previous month
  for (let i = firstDayOfWeek; i > 0; i--) {
    const dayNum = totalDaysInPrevMonth - i + 1;
    const prevMonthIdx = month === 0 ? 11 : month - 1;
    const prevYear = month === 0 ? year - 1 : year;
    const cellDate = new Date(prevYear, prevMonthIdx, dayNum);
    const dateStr = `${prevYear}-${String(prevMonthIdx + 1).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
    const lunar = getKhmerLunarDetails(cellDate);

    cells.push({
      solarDay: dayNum,
      isCurrentMonth: false,
      dateString: dateStr,
      jsDate: cellDate,
      isToday: false,
      dayOfWeek: cellDate.getDay(),
      lunar,
      events: getEventsForDate(dateStr)
    });
  }

  // Days in current month
  for (let day = 1; day <= totalDaysInMonth; day++) {
    const cellDate = new Date(year, month, day);
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const isToday = (year === todayYear && month === todayMonth && day === todayDay);
    const lunar = getKhmerLunarDetails(cellDate);

    cells.push({
      solarDay: day,
      isCurrentMonth: true,
      dateString: dateStr,
      jsDate: cellDate,
      isToday: isToday,
      dayOfWeek: cellDate.getDay(),
      lunar,
      events: getEventsForDate(dateStr)
    });
  }

  // Trailing days from next month to complete 7-day rows
  const remainingCells = (7 - (cells.length % 7)) % 7;
  for (let j = 1; j <= remainingCells; j++) {
    const nextMonthIdx = month === 11 ? 0 : month + 1;
    const nextYear = month === 11 ? year + 1 : year;
    const cellDate = new Date(nextYear, nextMonthIdx, j);
    const dateStr = `${nextYear}-${String(nextMonthIdx + 1).padStart(2, '0')}-${String(j).padStart(2, '0')}`;
    const lunar = getKhmerLunarDetails(cellDate);

    cells.push({
      solarDay: j,
      isCurrentMonth: false,
      dateString: dateStr,
      jsDate: cellDate,
      isToday: false,
      dayOfWeek: cellDate.getDay(),
      lunar,
      events: getEventsForDate(dateStr)
    });
  }

  return cells;
});

// Upcoming Events for right sidebar
const filteredUpcomingEvents = computed(() => {
  const eventsList = [];

  // 1. Add all Holy Days for this month
  const totalDays = new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
  for (let day = 1; day <= totalDays; day++) {
    const d = new Date(currentYear.value, currentMonth.value, day);
    const l = getKhmerLunarDetails(d);
    if (l.isHolyDay) {
      eventsList.push({
        id: `holy-${day}`,
        title: `ថ្ងៃសីល (${l.cellLunarText} ខែ${l.monthName})`,
        category: 'holy_day',
        startDate: `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`,
        endDate: `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`,
        dayNumber: day,
        dayOfWeekKm: WEEKDAYS_SHORT_KM[d.getDay()],
        time: 'ពេញមួយថ្ងៃ',
        location: 'ទីអារាម និងគេហដ្ឋាន',
        target: 'ពុទ្ធបរិស័ទគ្រប់រូប',
        description: `ថ្ងៃសីល ជាថ្ងៃឧបោសថសម្រាប់បំពេញកុសល ថែរក្សាសីល និងស្តាប់ព្រះធម៌៖ ${l.formattedKhmerFull}។`,
        dateRangeKm: `ថ្ងៃទី ${toKhmerNumber(day)} ខែ${SOLAR_MONTHS_KM[currentMonth.value]} ឆ្នាំ${toKhmerNumber(currentYear.value)}`,
        isHolyDay: true
      });
    }
  }

  // 2. Add regular academic events
  academicEvents.value.forEach(evt => {
    const evtYear = parseInt(evt.startDate.split('-')[0], 10);
    const evtMonth = parseInt(evt.startDate.split('-')[1], 10) - 1;
    if (evtYear === currentYear.value && evtMonth === currentMonth.value) {
      eventsList.push(evt);
    }
  });

  // Filter according to category & search
  return eventsList
    .filter(evt => {
      if (selectedCategory.value !== 'all' && evt.category !== selectedCategory.value) {
        return false;
      }
      if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase();
        const match = evt.title.toLowerCase().includes(q) || 
                      (evt.description && evt.description.toLowerCase().includes(q)) ||
                      (evt.location && evt.location.toLowerCase().includes(q));
        if (!match) return false;
      }
      return true;
    })
    .sort((a, b) => a.startDate.localeCompare(b.startDate));
});

// Month navigation
const prevMonth = () => {
  if (currentMonth.value === 0) {
    currentMonth.value = 11;
    currentYear.value -= 1;
  } else {
    currentMonth.value -= 1;
  }
};

const nextMonth = () => {
  if (currentMonth.value === 11) {
    currentMonth.value = 0;
    currentYear.value += 1;
  } else {
    currentMonth.value += 1;
  }
};

const jumpToToday = () => {
  currentYear.value = todayYear;
  currentMonth.value = todayMonth;
  selectedCellDate.value = `${todayYear}-${String(todayMonth + 1).padStart(2, '0')}-${String(todayDay).padStart(2, '0')}`;
  selectedDayDetails.value = todayLunarInfo.value;
};

// Date Cell Click
const handleDateClick = (cell) => {
  currentModalJsDate.value = cell.jsDate;
  selectedCellDate.value = cell.dateString;
  selectedDayDetails.value = cell.lunar;
  if (cell.events && cell.events.length > 0) {
    selectedEvent.value = cell.events[0];
  } else {
    selectedEvent.value = null;
  }
};

const openEventModal = (evt) => {
  selectedEvent.value = evt;
  if (evt.startDate) {
    const parts = evt.startDate.split('-').map(Number);
    const d = new Date(parts[0], parts[1] - 1, parts[2]);
    currentModalJsDate.value = d;
    selectedDayDetails.value = getKhmerLunarDetails(d);
  }
};

const goToPrevDay = () => {
  const prev = new Date(currentModalJsDate.value.getTime() - 86400000);
  currentModalJsDate.value = prev;
  selectedDayDetails.value = getKhmerLunarDetails(prev);
  const dateStr = `${prev.getFullYear()}-${String(prev.getMonth() + 1).padStart(2, '0')}-${String(prev.getDate()).padStart(2, '0')}`;
  selectedCellDate.value = dateStr;
  const evts = getEventsForDate(dateStr);
  selectedEvent.value = evts.length > 0 ? evts[0] : null;
  modalCopied.value = false;
};

const goToNextDay = () => {
  const next = new Date(currentModalJsDate.value.getTime() + 86400000);
  currentModalJsDate.value = next;
  selectedDayDetails.value = getKhmerLunarDetails(next);
  const dateStr = `${next.getFullYear()}-${String(next.getMonth() + 1).padStart(2, '0')}-${String(next.getDate()).padStart(2, '0')}`;
  selectedCellDate.value = dateStr;
  const evts = getEventsForDate(dateStr);
  selectedEvent.value = evts.length > 0 ? evts[0] : null;
  modalCopied.value = false;
};

const closeDetailModal = () => {
  selectedEvent.value = null;
  selectedDayDetails.value = null;
  modalCopied.value = false;
};

// Computed properties for Modal
const activeLunarDetails = computed(() => {
  return selectedDayDetails.value || todayLunarInfo.value;
});

const modalTitle = computed(() => {
  if (selectedEvent.value) return selectedEvent.value.title;
  return `កាលបរិច្ឆេទ ${activeLunarDetails.value?.solarDayKm} ខែ${activeLunarDetails.value?.solarMonthNameKm} ឆ្នាំ${activeLunarDetails.value?.solarYearKm}`;
});

const modalCategoryLabel = computed(() => {
  if (selectedEvent.value) return getCategoryLabel(selectedEvent.value.category);
  if (activeLunarDetails.value?.isHolyDay) return 'ថ្ងៃសីល (Buddhist Holy Day)';
  return 'ប្រតិទិនចន្ទគតិខ្មែរ';
});

const modalCategoryTagStyle = computed(() => {
  if (selectedEvent.value) return getEventCategoryTagStyle(selectedEvent.value.category);
  if (activeLunarDetails.value?.isHolyDay) return 'bg-amber-500/20 text-amber-500 border-amber-500/30';
  return 'bg-blue-500/15 text-blue-400 border-blue-500/30';
});

const modalHeaderBadgeStyle = computed(() => {
  if (selectedEvent.value) return getEventBadgeStyle(selectedEvent.value.category);
  if (activeLunarDetails.value?.isHolyDay) return 'bg-amber-500/20 text-amber-500 border-amber-500/30';
  return 'bg-blue-500/15 text-blue-400 border-blue-500/30';
});

const modalHeaderIcon = computed(() => {
  if (selectedEvent.value) return getCategoryIcon(selectedEvent.value.category);
  if (activeLunarDetails.value?.isHolyDay) return 'fa-solid fa-dharmachakra';
  return 'fa-regular fa-calendar-days';
});

// Category helper utilities
const getCategoryLabel = (category) => {
  switch (category) {
    case 'holy_day': return 'ថ្ងៃសីល';
    case 'holiday': return 'ថ្ងៃឈប់សម្រាក';
    case 'exam': return 'ការប្រឡង';
    case 'event': return 'សាលា';
    default: return 'ទូទៅ';
  }
};

const getCategoryIcon = (category) => {
  switch (category) {
    case 'holy_day': return 'fa-solid fa-dharmachakra text-amber-500';
    case 'holiday': return 'fa-solid fa-umbrella-beach text-rose-500';
    case 'exam': return 'fa-solid fa-file-signature text-indigo-500';
    case 'event': return 'fa-solid fa-star text-blue-500';
    default: return 'fa-solid fa-calendar text-slate-400';
  }
};

const getEventBadgeStyle = (category) => {
  switch (category) {
    case 'holy_day':
      return 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-500/30';
    case 'holiday':
      return 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border-rose-500/30';
    case 'exam':
      return 'bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border-indigo-500/30';
    case 'event':
      return 'bg-blue-500/15 text-blue-600 dark:text-blue-400 border-blue-500/30';
    default:
      return 'bg-slate-500/15 text-slate-600 border-slate-500/30';
  }
};

const getEventPillStyle = (category) => {
  switch (category) {
    case 'holy_day':
      return 'bg-amber-500/15 text-amber-800 dark:text-amber-300 border-amber-500/25';
    case 'holiday':
      return 'bg-rose-500/15 text-rose-700 dark:text-rose-300 border-rose-500/25';
    case 'exam':
      return 'bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border-indigo-500/25';
    case 'event':
      return 'bg-blue-500/15 text-blue-700 dark:text-blue-300 border-blue-500/25';
    default:
      return 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20';
  }
};

const getEventDotStyle = (category) => {
  switch (category) {
    case 'holiday': return 'bg-rose-500';
    case 'exam': return 'bg-amber-500';
    case 'event': return 'bg-blue-500';
    case 'holy_day': return 'bg-amber-400';
    default: return 'bg-slate-400';
  }
};

const getEventCategoryTagStyle = (category) => {
  switch (category) {
    case 'holy_day':
      return 'bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-500/30';
    case 'holiday':
      return 'bg-rose-50 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-500/30';
    case 'exam':
      return 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-500/30';
    case 'event':
      return 'bg-blue-50 dark:bg-blue-500/15 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-500/30';
    default:
      return 'bg-slate-100 text-slate-600 border-slate-200';
  }
};
</script>
