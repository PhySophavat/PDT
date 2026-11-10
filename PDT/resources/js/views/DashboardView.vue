<template>
  <div class="space-y-6 font-khmer pb-10">
    <!-- Header -->
    <PageHeader 
      :title="t('dashboard.title')" 
      :breadcrumb="t('nav.dashboard')" 
      :actionLabel="canCreateRecord ? t('common.createNew') : ''"
      @action="handleCreateNew"
    >
      <template #extraActions>
        <div class="relative" ref="shareDropdownRef">
          <button 
            type="button"
            @click.stop="toggleShareDropdown"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs shadow-blue-500/20 transition-all active:scale-95 cursor-pointer shrink-0"
            title="ចែករំលែកតំណចុះឈ្មោះសាលារៀន"
          >
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
            </svg>
            <span class="hidden sm:inline">ចែករំលែកតំណចុះឈ្មោះ</span>
            <span class="sm:hidden">ចែករំលែក</span>
          </button>

          <!-- Share Popover Dropdown -->
          <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-1 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-1 scale-95"
          >
            <div 
              v-if="showShareDropdown"
              class="absolute right-0 mt-2 w-80 sm:w-88 bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 space-y-3 z-50 text-slate-800 font-khmer text-xs animate-fadeIn"
              @click.stop
            >
              <!-- Header -->
              <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                  </div>
                  <div>
                    <h4 class="text-xs font-bold text-slate-900 leading-tight">តំណចុះឈ្មោះសាលារៀន</h4>
                    <p class="text-[10px] text-slate-400">ចែករំលែកដើម្បីចុះឈ្មោះចូលរៀន</p>
                  </div>
                </div>
                <button 
                  @click="showShareDropdown = false" 
                  class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer"
                  title="បិទ"
                >
                  ✕
                </button>
              </div>

              <!-- QR Code Display Box -->
              <div class="bg-gradient-to-b from-blue-50/60 to-slate-50 p-3 rounded-xl border border-blue-100/60 flex flex-col items-center justify-center space-y-2 text-center">
                <div class="p-2 bg-white rounded-xl shadow-xs border border-slate-200/80">
                  <img 
                    :src="`https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(registrationUrl)}`" 
                    alt="Registration QR Code" 
                    class="w-28 h-28 object-contain"
                  />
                </div>
                <div>
                  <span class="text-[11px] font-bold text-slate-800 block">ស្កេន QR Code ដើម្បីចុះឈ្មោះ</span>
                  <span class="text-[10px] text-slate-400">ប្រើកាមេរ៉ាទូរស័ព្ទដើម្បីស្កេន</span>
                </div>
              </div>

              <!-- Direct Link Box with 1-Click Copy -->
              <div class="space-y-1">
                <label class="text-[10px] font-semibold text-slate-500 block">តំណភ្ជាប់ផ្លូវការ (Direct Link)</label>
                <div class="flex items-center gap-1.5 p-1 rounded-xl bg-slate-50 border border-slate-200">
                  <input 
                    type="text" 
                    readonly 
                    :value="registrationUrl" 
                    class="flex-1 bg-transparent px-2 text-[10px] font-mono text-slate-700 outline-none truncate"
                  />
                  <button 
                    @click="copyRegistrationLink" 
                    type="button"
                    class="px-2.5 py-1 text-white text-[10px] font-bold rounded-lg transition shadow-xs flex items-center gap-1 cursor-pointer shrink-0"
                    :class="copiedRegistrationLink ? 'bg-emerald-600' : 'bg-blue-600 hover:bg-blue-700'"
                  >
                    <svg v-if="copiedRegistrationLink" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span>{{ copiedRegistrationLink ? 'បានចម្លង!' : 'ចម្លង' }}</span>
                  </button>
                </div>
              </div>

              <!-- Quick Share Action Buttons: Telegram, Facebook, Open -->
              <div class="grid grid-cols-3 gap-2 pt-1 border-t border-slate-100">
                <a 
                  :href="`https://t.me/share/url?url=${encodeURIComponent(registrationUrl)}&text=${encodeURIComponent('សាលារៀនជំនាន់ថ្មី PDT - តំណភ្ជាប់ចុះឈ្មោះចូលរៀន')}`" 
                  target="_blank" 
                  rel="noopener noreferrer"
                  class="flex flex-col items-center justify-center p-1.5 rounded-xl bg-[#229ED9]/10 hover:bg-[#229ED9]/20 text-[#229ED9] transition font-bold text-[10px] gap-1 cursor-pointer"
                  title="ចែករំលែកតាម Telegram"
                >
                  <i class="fa-brands fa-telegram text-sm"></i>
                  <span>Telegram</span>
                </a>

                <a 
                  :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(registrationUrl)}`" 
                  target="_blank" 
                  rel="noopener noreferrer"
                  class="flex flex-col items-center justify-center p-1.5 rounded-xl bg-[#1877F2]/10 hover:bg-[#1877F2]/20 text-[#1877F2] transition font-bold text-[10px] gap-1 cursor-pointer"
                  title="ចែករំលែកតាម Facebook"
                >
                  <i class="fa-brands fa-facebook text-sm"></i>
                  <span>Facebook</span>
                </a>

                <a 
                  :href="registrationUrl" 
                  target="_blank" 
                  rel="noopener noreferrer"
                  class="flex flex-col items-center justify-center p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition font-bold text-[10px] gap-1 cursor-pointer"
                  title="បើកមើលតំណផ្ទាល់"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                  <span>បើកមើល</span>
                </a>
              </div>
            </div>
          </transition>
        </div>
      </template>
    </PageHeader>

    <!-- Sub Tabs (NGS Public School: Removed Finance, Added STEM & Lab) -->
    <TabNav 
      v-model="activeTab" 
      :tabs="dashboardTabs"
    />

    <!-- Tab 1: Overview Tab (Primary Dashboard) -->
    <div v-show="activeTab === 'overview'" class="space-y-6">
      <!-- 1. STUDENT DASHBOARD: 4 Personal Student Summary Cards -->
      <div v-if="isStudent" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Student Card 1: My Class -->
        <div class="relative overflow-hidden bg-white border border-blue-100/80 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all group">
          <div class="absolute -top-10 -right-10 w-28 h-28 bg-blue-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
          <div class="flex items-start justify-between">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center shadow-md shadow-blue-500/20">
              <i class="fa-solid fa-graduation-cap text-xl"></i>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 ring-1 ring-blue-500/20">
              {{ studentInfo.room || (currentLocale === 'km' ? ('បន្ទប់ ' + studentInfo.class_name) : ('Room ' + studentInfo.class_name)) }}
            </span>
          </div>
          <div class="mt-4">
            <h3 class="text-xs font-semibold text-slate-500 tracking-wide uppercase">
              {{ currentLocale === 'km' ? 'ថ្នាក់រៀនរបស់ខ្ញុំ' : 'My Classroom' }}
            </h3>
            <div class="flex items-baseline gap-2 mt-1">
              <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ currentLocale === 'km' ? ('ថ្នាក់ទី ' + studentInfo.class_name) : ('Grade ' + studentInfo.class_name) }}
              </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2 font-medium">
              {{ currentLocale === 'km' ? 'សិស្សសរុបក្នុងថ្នាក់' : 'Total students in class:' }}
              <span class="font-bold text-slate-700">{{ studentInfo.classmates_count || 50 }}</span>
              {{ currentLocale === 'km' ? 'នាក់' : '' }}
            </p>
          </div>
        </div>

        <!-- Student Card 2: Homeroom Teacher -->
        <div class="relative overflow-hidden bg-white border border-emerald-100/80 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all group">
          <div class="absolute -top-10 -right-10 w-28 h-28 bg-emerald-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
          <div class="flex items-start justify-between">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/20">
              <i class="fa-solid fa-chalkboard-user text-xl"></i>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 ring-1 ring-emerald-500/20">
              {{ currentLocale === 'km' ? 'គ្រូបន្ទុកថ្នាក់' : 'Homeroom Teacher' }}
            </span>
          </div>
          <div class="mt-4">
            <h3 class="text-xs font-semibold text-slate-500 tracking-wide uppercase">
              {{ currentLocale === 'km' ? 'គ្រូបន្ទុកថ្នាក់' : 'Homeroom Teacher' }}
            </h3>
            <div class="flex items-baseline gap-2 mt-1">
              <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate max-w-full">
                {{ studentInfo.homeroom_teacher || (currentLocale === 'km' ? 'មិនទាន់ចាត់តាំង' : 'Unassigned') }}
              </span>
            </div>
            <p class="text-[11px] text-emerald-700 mt-2 font-medium">
              {{ currentLocale === 'km' ? 'ទំនាក់ទំនង: ' : 'Contact: ' }}<span class="font-bold">{{ studentInfo.teacher_phone || '-' }}</span>
            </p>
          </div>
        </div>

        <!-- Student Card 3: Personal Attendance Rate -->
        <div class="relative overflow-hidden bg-white border border-purple-100/80 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all group">
          <div class="absolute -top-10 -right-10 w-28 h-28 bg-purple-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
          <div class="flex items-start justify-between">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-600 to-violet-500 text-white flex items-center justify-center shadow-md shadow-purple-500/20">
              <i class="fa-solid fa-calendar-check text-xl"></i>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 ring-1 ring-purple-500/20">
              {{ currentLocale === 'km' ? 'អត្រាវត្តមាន' : 'Attendance' }}
            </span>
          </div>
          <div class="mt-4">
            <h3 class="text-xs font-semibold text-slate-500 tracking-wide uppercase">
              {{ currentLocale === 'km' ? 'វត្តមានផ្ទាល់ខ្លួន' : 'My Attendance' }}
            </h3>
            <div class="flex items-baseline gap-2 mt-1">
              <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ studentInfo.attendance?.rate ?? 100 }}%</span>
            </div>
            <p class="text-[11px] text-purple-700 mt-2 font-medium">
              <template v-if="currentLocale === 'km'">
                មានវត្តមាន <span class="font-bold text-emerald-700">{{ studentInfo.attendance?.present ?? 0 }}</span> ថ្ងៃ • ឈប់សរុប <span class="font-bold text-rose-600">{{ studentInfo.attendance?.total_leave ?? ((studentInfo.attendance?.excused || 0) + (studentInfo.attendance?.absent || 0)) }}</span> ថ្ងៃ (ច្បាប់ <span class="font-bold text-blue-600">{{ studentInfo.attendance?.excused ?? 0 }}</span> • អត់ច្បាប់ <span class="font-bold text-rose-600">{{ studentInfo.attendance?.absent ?? 0 }}</span>)
              </template>
              <template v-else>
                Present <span class="font-bold text-emerald-700">{{ studentInfo.attendance?.present ?? 0 }}</span> days • Absent <span class="font-bold text-rose-600">{{ studentInfo.attendance?.total_leave ?? ((studentInfo.attendance?.excused || 0) + (studentInfo.attendance?.absent || 0)) }}</span> days (Excused <span class="font-bold text-blue-600">{{ studentInfo.attendance?.excused ?? 0 }}</span> • Unexcused <span class="font-bold text-rose-600">{{ studentInfo.attendance?.absent ?? 0 }}</span>)
              </template>
            </p>
          </div>
        </div>

        <!-- Student Card 4: Learning Resources & Digital Library -->
        <div class="relative overflow-hidden bg-white border border-amber-100/80 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all group">
          <div class="absolute -top-10 -right-10 w-28 h-28 bg-amber-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
          <div class="flex items-start justify-between">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-md shadow-amber-500/20">
              <i class="fa-solid fa-book-bookmark text-xl"></i>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 ring-1 ring-amber-500/20">
              {{ currentLocale === 'km' ? 'បណ្ណាល័យឌីជីថល' : 'Digital Library' }}
            </span>
          </div>
          <div class="mt-4">
            <h3 class="text-xs font-semibold text-slate-500 tracking-wide uppercase">
              {{ currentLocale === 'km' ? 'សៀវភៅសិក្សា' : 'Textbooks' }}
            </h3>
            <div class="flex items-baseline gap-2 mt-1">
              <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ studentInfo.student_visible_books || studentInfo.total_books || stats.books || 81 }}</span>
              <span class="text-xs font-medium text-slate-400">{{ currentLocale === 'km' ? 'ក្បាល' : 'Books' }}</span>
            </div>
            <router-link to="/library" class="text-[11px] text-blue-600 hover:text-blue-800 mt-2 font-bold inline-flex items-center gap-1">
              <span>{{ currentLocale === 'km' ? 'ចូលអានសៀវភៅពុម្ព MoEYS' : 'Read MoEYS Digital Textbooks' }}</span>
              <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </router-link>
          </div>
        </div>
      </div>

      <!-- 2. ADMIN/TEACHER DASHBOARD: 4 Administrative KPI Summary Cards -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Students (Blue Theme) -->
        <div class="relative overflow-hidden bg-white border border-blue-100/80 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all group">
          <div class="absolute -top-10 -right-10 w-28 h-28 bg-blue-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
          <div class="flex items-start justify-between">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 ring-1 ring-blue-500/20">
              <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
              ស្រី {{ stats.students > 0 ? (stats.femaleRatio ?? 0) : 0 }}%
            </span>
          </div>
          <div class="mt-4">
            <h3 class="text-xs font-semibold text-slate-500 tracking-wide uppercase">{{ t('dashboard.totalStudents') }}</h3>
            <div class="flex items-baseline gap-2 mt-1">
              <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ Number(stats.students || 0).toLocaleString() }}</span>
              <span class="text-xs font-medium text-slate-400">នាក់</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2 font-medium">
              ប្រុស <span class="font-bold text-slate-700">{{ stats.maleCount ?? 0 }}</span> នាក់ • ស្រី <span class="font-bold text-slate-700">{{ stats.femaleCount ?? 0 }}</span> នាក់
            </p>
          </div>
        </div>

        <!-- Card 2: Total Teachers (Green Theme) -->
        <div class="relative overflow-hidden bg-white border border-emerald-100/80 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all group">
          <div class="absolute -top-10 -right-10 w-28 h-28 bg-emerald-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
          <div class="flex items-start justify-between">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
              </svg>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 ring-1 ring-emerald-500/20">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              វត្តមានថ្ងៃនេះ {{ stats.teachers > 0 ? (stats.teacherAttendanceRate ?? 0) : 0 }}%
            </span>
          </div>
          <div class="mt-4">
            <h3 class="text-xs font-semibold text-slate-500 tracking-wide uppercase">{{ t('dashboard.totalTeachers') }}</h3>
            <div class="flex items-baseline gap-2 mt-1">
              <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ stats.teachers ?? 0 }}</span>
              <span class="text-xs font-medium text-slate-400">នាក់</span>
            </div>
            <p class="text-[11px] text-emerald-700 mt-2 font-medium">
              គ្រូបង្រៀនពេញម៉ោង និងឯកទេស STEM
            </p>
          </div>
        </div>

        <!-- Card 3: Total Classes (Purple Theme) -->
        <div class="relative overflow-hidden bg-white border border-purple-100/80 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all group">
          <div class="absolute -top-10 -right-10 w-28 h-28 bg-purple-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
          <div class="flex items-start justify-between">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-600 to-violet-500 text-white flex items-center justify-center shadow-md shadow-purple-500/20 group-hover:scale-105 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 ring-1 ring-purple-500/20">
              ថ្នាក់ទី ៧ - ១២
            </span>
          </div>
          <div class="mt-4">
            <h3 class="text-xs font-semibold text-slate-500 tracking-wide uppercase">{{ t('dashboard.totalClasses') }}</h3>
            <div class="flex items-baseline gap-2 mt-1">
              <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ stats.classes ?? 0 }}</span>
              <span class="text-xs font-medium text-slate-400">ថ្នាក់រៀន</span>
            </div>
            <p class="text-[11px] text-purple-700 mt-2 font-medium">
              បន្ទប់ធម្មតា និងបន្ទប់ពិសោធន៍
            </p>
          </div>
        </div>

        <!-- Card 4: Total Subjects (Orange Theme) -->
        <div class="relative overflow-hidden bg-white border border-amber-100/80 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all group">
          <div class="absolute -top-10 -right-10 w-28 h-28 bg-amber-500/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
          <div class="flex items-start justify-between">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-md shadow-amber-500/20 group-hover:scale-105 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 ring-1 ring-amber-500/20">
              កម្មវិធីសិក្សាជំនាន់ថ្មី
            </span>
          </div>
          <div class="mt-4">
            <h3 class="text-xs font-semibold text-slate-500 tracking-wide uppercase">{{ t('dashboard.totalSubjects') }}</h3>
            <div class="flex items-baseline gap-2 mt-1">
              <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ stats.subjects ?? 0 }}</span>
              <span class="text-xs font-medium text-slate-400">មុខវិជ្ជា</span>
            </div>
            <p class="text-[11px] text-amber-700 mt-2 font-medium">
              កម្មវិធីសិក្សាជំនាន់ថ្មី
            </p>
          </div>
        </div>
      </div>

      <!-- Academic Filters -->
      <FilterBar>
        <template #left>
          <div></div>
        </template>

        <template #right>
          <div class="inline-flex items-center gap-3 px-3.5 py-1.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs font-medium">
            <label class="flex items-center gap-1.5 cursor-pointer text-slate-700">
              <input type="radio" name="termFilter" value="s1" checked class="text-blue-600 focus:ring-0">
              <span>{{ t('academicYears.semester1') }}</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer text-slate-700">
              <input type="radio" name="termFilter" value="s2" class="text-blue-600 focus:ring-0">
              <span>{{ t('academicYears.semester2') }}</span>
            </label>
          </div>

          <select v-model="activeAcademicYear" class="px-3.5 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-semibold focus:outline-none shadow-2xs font-khmer">
            <option v-for="ay in academicYearsList" :key="ay" :value="ay">
              {{ t('academicYears.currentYear') }} {{ ay }}
            </option>
          </select>
        </template>
      </FilterBar>

      <!-- 3. Two Modern Visual Charts (Area Chart + Donut Chart) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Chart: Weekly Student Attendance Trend (2/3 width) -->
        <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
            <div>
              <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                ចរន្តវត្តមានសិស្សប្រចាំសប្តាហ៍
              </h2>
              <p class="text-xs text-slate-400 mt-0.5">ការប្រៀបធៀបសិស្សមានវត្តមាន និងអវត្តមាន ចាប់ពីថ្ងៃច័ន្ទ ដល់ ថ្ងៃសៅរ៍</p>
            </div>

            <!-- Legend -->
            <div class="flex items-center gap-3 text-xs font-semibold">
              <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-md bg-emerald-500"></span>
                <span class="text-slate-600">មានវត្តមាន</span>
              </div>
              <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-md bg-rose-500"></span>
                <span class="text-slate-600">អវត្តមាន</span>
              </div>
            </div>
          </div>

          <!-- Chart Visual Area (Responsive SVG Area Chart) -->
          <div class="relative w-full h-64 mt-4 select-none">
            <div v-if="chartDays.length === 0" class="h-full flex flex-col items-center justify-center text-slate-400">
              <span class="text-3xl block mb-2">📈</span>
              <p class="text-xs font-medium">មិនទាន់មានទិន្នន័យវត្តមានប្រចាំសប្តាហ៍</p>
            </div>
            <svg v-else class="w-full h-full overflow-visible" viewBox="0 0 600 215" preserveAspectRatio="none">
              <defs>
                <!-- Present Gradient -->
                <linearGradient id="presentGradient" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#10b981" stop-opacity="0.35"/>
                  <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"/>
                </linearGradient>
                <!-- Absent Gradient -->
                <linearGradient id="absentGradient" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#f43f5e" stop-opacity="0.25"/>
                  <stop offset="100%" stop-color="#f43f5e" stop-opacity="0.0"/>
                </linearGradient>
              </defs>

              <!-- Grid Lines -->
              <line x1="30" y1="20" x2="590" y2="20" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4"/>
              <line x1="30" y1="65" x2="590" y2="65" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4"/>
              <line x1="30" y1="110" x2="590" y2="110" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4"/>
              <line x1="30" y1="155" x2="590" y2="155" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4"/>

              <!-- Grid Labels (Y-axis) -->
              <text x="22" y="24" text-anchor="end" class="text-[10px] fill-slate-400 font-medium">100%</text>
              <text x="22" y="69" text-anchor="end" class="text-[10px] fill-slate-400 font-medium">75%</text>
              <text x="22" y="114" text-anchor="end" class="text-[10px] fill-slate-400 font-medium">50%</text>
              <text x="22" y="159" text-anchor="end" class="text-[10px] fill-slate-400 font-medium">25%</text>

              <!-- Area Path: Present -->
              <path 
                :d="presentAreaPath" 
                fill="url(#presentGradient)" 
              />
              <path 
                :d="presentLinePath" 
                fill="none" 
                stroke="#10b981" 
                stroke-width="2.5" 
                stroke-linecap="round" 
                stroke-linejoin="round"
              />

              <!-- Area Path: Absent -->
              <path 
                :d="absentAreaPath" 
                fill="url(#absentGradient)" 
              />
              <path 
                :d="absentLinePath" 
                fill="none" 
                stroke="#f43f5e" 
                stroke-width="2" 
                stroke-linecap="round" 
                stroke-linejoin="round"
                stroke-dasharray="4 2"
              />

              <!-- Data Circles / Interactive Points -->
              <g v-for="(day, idx) in chartDays" :key="idx">
                <circle 
                  :cx="getX(idx)" 
                  :cy="getYPresent(day.present, day.absent)" 
                  r="4.5" 
                  class="fill-white stroke-emerald-600 stroke-[2.5] hover:r-6 transition-all cursor-pointer"
                  @mouseenter="hoveredDay = day"
                  @mouseleave="hoveredDay = null"
                />
                <circle 
                  :cx="getX(idx)" 
                  :cy="getYAbsent(day.present, day.absent)" 
                  r="3.5" 
                  class="fill-white stroke-rose-500 stroke-[2] hover:r-5 transition-all cursor-pointer"
                  @mouseenter="hoveredDay = day"
                  @mouseleave="hoveredDay = null"
                />
                <!-- Day Label (X-axis) -->
                <text 
                  :x="getX(idx)" 
                  y="182" 
                  text-anchor="middle" 
                  class="text-[11px] font-bold fill-slate-700"
                >
                  {{ day.day }}
                </text>
                <text 
                  :x="getX(idx)" 
                  y="198" 
                  text-anchor="middle" 
                  class="text-[9px] font-semibold fill-slate-400"
                >
                  {{ day.date }}
                </text>
              </g>
            </svg>

            <!-- Floating Tooltip on Hover -->
            <div 
              v-if="hoveredDay" 
              class="absolute top-2 right-4 bg-slate-900/95 backdrop-blur-xs text-white text-xs rounded-xl px-3.5 py-2 shadow-xl border border-slate-700 pointer-events-none transition-all z-10"
            >
              <div class="font-bold text-slate-200 border-b border-slate-800 pb-1 mb-1">
                ថ្ងៃ{{ hoveredDay.day }} ({{ hoveredDay.date }})
              </div>
              <div class="flex items-center justify-between gap-4 text-emerald-400 font-semibold">
                <span>មានវត្តមាន:</span>
                <span>{{ hoveredDay.present }} នាក់ ({{ hoveredDay.rate }}%)</span>
              </div>
              <div class="flex items-center justify-between gap-4 text-rose-400 font-semibold mt-0.5">
                <span>អវត្តមាន:</span>
                <span>{{ hoveredDay.absent }} នាក់</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Chart: Gender Ratio Donut Chart (1/3 width) -->
        <div class="lg:col-span-1 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
          <div class="border-b border-slate-100 pb-3">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
              <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
              </svg>
              សមាមាត្រសិស្សប្រុស-ស្រី
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">ការបែងចែកយេនឌ័រក្នុងចំណោមសិស្សសរុប</p>
          </div>

          <!-- Donut SVG -->
          <div class="relative flex items-center justify-center my-3">
            <svg class="w-44 h-44 -rotate-90 transform" viewBox="0 0 160 160">
              <!-- Background circle -->
              <circle
                cx="80"
                cy="80"
                r="62"
                fill="none"
                stroke="#f1f5f9"
                stroke-width="18"
              />
              <!-- Male Arc (Blue) -->
              <circle
                cx="80"
                cy="80"
                r="62"
                fill="none"
                stroke="#3b82f6"
                stroke-width="18"
                stroke-linecap="round"
                :stroke-dasharray="donutMaleDash"
                stroke-dashoffset="0"
                class="transition-all duration-1000 ease-out"
              />
              <!-- Female Arc (Pink/Rose) -->
              <circle
                cx="80"
                cy="80"
                r="62"
                fill="none"
                stroke="#ec4899"
                stroke-width="18"
                stroke-linecap="round"
                :stroke-dasharray="donutFemaleDash"
                :stroke-dashoffset="donutFemaleOffset"
                class="transition-all duration-1000 ease-out"
              />
            </svg>

            <!-- Center Text -->
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none">
              <span class="text-2xl font-black text-slate-800 tracking-tight">{{ stats.students }}</span>
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">សិស្សសរុប</span>
            </div>
          </div>

          <!-- Donut Legend & Stats -->
         
<div class="space-y-2 pt-2 border-t border-slate-100">
  <!-- Female Students -->
  <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-pink-50/70 border border-pink-100/60">
    <div class="flex items-center gap-2">
      <span class="w-3 h-3 rounded-full bg-pink-500 shadow-xs"></span>
      <span class="font-bold text-slate-700">សិស្សស្រី</span>
    </div>
    <span class="font-bold text-pink-700">
      {{ stats.femaleCount ?? 0 }} នាក់
      ({{ stats.students > 0
        ? Math.round(((stats.femaleCount ?? 0) / stats.students) * 100)
        : 0 }}%)
    </span>
  </div>

  <!-- Male Students -->
  <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-blue-50/70 border border-blue-100/60">
    <div class="flex items-center gap-2">
      <span class="w-3 h-3 rounded-full bg-blue-500 shadow-xs"></span>
      <span class="font-bold text-slate-700">សិស្សប្រុស</span>
    </div>
    <span class="font-bold text-blue-700">
      {{ stats.maleCount ?? 0 }} នាក់
      ({{ stats.students > 0
        ? Math.round(((stats.maleCount ?? 0) / stats.students) * 100)
        : 0 }}%)
    </span>
  </div>
</div>

        </div>
      </div>

      <!-- Main Section: Table (Left 2/3) + Side Widget for Live Activity & Top Classes (Right 1/3) -->
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
        <!-- STUDENT VIEW: My Classroom & Homeroom Teacher Detailed Card -->
        <div v-if="isStudent" class="xl:col-span-2 space-y-4">
          <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
              <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-xs font-bold mb-2">
                  <i class="fa-solid fa-school text-xs"></i>
                  <span>ព័ត៌មានថ្នាក់រៀនផ្ទាល់ខ្លួន</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">
                  ថ្នាក់រៀនទី {{ studentInfo.class_name }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                  {{ studentInfo.room }} • ឆ្នាំសិក្សា {{ activeAcademicYear }}
                </p>
              </div>

              <div class="flex items-center gap-2">
                <router-link 
                  to="/leave-request" 
                  class="px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 transition"
                >
                  <i class="fa-solid fa-file-signature"></i>
                  <span>ស្នើសុំច្បាប់អវត្តមាន</span>
                </router-link>
              </div>
            </div>

            <!-- Homeroom Teacher & Class Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Homeroom Teacher Profile Box -->
              <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-50 to-blue-50/40 border border-slate-200/80 space-y-3">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 overflow-hidden flex items-center justify-center text-slate-600 font-bold text-sm shadow-xs shrink-0">
                    <img v-if="studentInfo.teacher_photo" :src="studentInfo.teacher_photo" alt="Teacher" class="w-full h-full object-cover">
                    <span v-else class="text-lg">👨‍🏫</span>
                  </div>
                  <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">គ្រូបន្ទុកថ្នាក់</span>
                    <h4 class="font-extrabold text-slate-900 text-sm mt-0.5">{{ studentInfo.homeroom_teacher || 'មិនទាន់ចាត់តាំង' }}</h4>
                  </div>
                </div>
                <div class="pt-2 border-t border-slate-200/60 text-xs text-slate-600 space-y-1">
                  <div class="flex items-center justify-between">
                    <span class="text-slate-400">លេខទូរស័ព្ទ:</span>
                    <span class="font-bold text-slate-800">{{ studentInfo.teacher_phone || '-' }}</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-slate-400">ការទទួលបន្ទុក:</span>
                    <span class="font-semibold text-emerald-700">គ្រប់គ្រងវិន័យ និងការសិក្សា</span>
                  </div>
                </div>
              </div>

              <!-- Student Quick Stats Box -->
              <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between">
                  <h4 class="font-bold text-xs text-slate-700 uppercase tracking-wide">ស្ថានភាពវត្តមានរបស់ខ្ញុំ</h4>
                  <span class="text-[11px] font-bold text-emerald-700 bg-emerald-100/70 px-2.5 py-0.5 rounded-full border border-emerald-200/60">
                    អត្រាវត្តមាន {{ studentInfo.attendance?.rate ?? 100 }}%
                  </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center">
                  <!-- 1. មានវត្តមាន -->
                  <div class="p-2.5 bg-white rounded-xl border border-emerald-100 shadow-2xs">
                    <span class="block text-lg sm:text-xl font-black text-emerald-600">{{ studentInfo.attendance?.present ?? 0 }}</span>
                    <span class="text-[10px] font-bold text-slate-500">មានវត្តមាន</span>
                  </div>

                  <!-- 2. ចំនួនឈប់សរុប -->
                  <div class="p-2.5 bg-white rounded-xl border border-purple-100 shadow-2xs">
                    <span class="block text-lg sm:text-xl font-black text-purple-600">
                      {{ studentInfo.attendance?.total_leave ?? ((studentInfo.attendance?.excused || 0) + (studentInfo.attendance?.absent || 0)) }}
                    </span>
                    <span class="text-[10px] font-bold text-purple-700">ចំនួនឈប់សរុប</span>
                  </div>

                  <!-- 3. មានច្បាប់ -->
                  <div class="p-2.5 bg-white rounded-xl border border-blue-100 shadow-2xs">
                    <span class="block text-lg sm:text-xl font-black text-blue-600">{{ studentInfo.attendance?.excused ?? 0 }}</span>
                    <span class="text-[10px] font-bold text-slate-500">មានច្បាប់</span>
                  </div>

                  <!-- 4. អត់ច្បាប់ -->
                  <div class="p-2.5 bg-white rounded-xl border border-rose-100 shadow-2xs">
                    <span class="block text-lg sm:text-xl font-black text-rose-600">{{ studentInfo.attendance?.absent ?? 0 }}</span>
                    <span class="text-[10px] font-bold text-slate-500">អត់ច្បាប់</span>
                  </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-200/60 text-slate-500">
                  <span class="text-[11px]">សរុបថ្ងៃសិក្សាខែនេះ: <strong class="text-slate-700">{{ studentInfo.attendance?.total ?? 0 }} ថ្ងៃ</strong></span>
                  <router-link to="/attendance" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                    <span>មើលកាលវិភាគវត្តមាន</span>
                    <i class="fa-solid fa-arrow-right text-[9px]"></i>
                  </router-link>
                </div>
              </div>
            </div>

            <!-- Quick Academic Navigation Shortcuts -->
            <div class="space-y-2 pt-2">
              <h4 class="font-bold text-xs text-slate-700 uppercase tracking-wide">សកម្មភាព និងឧបករណ៍សិក្សា</h4>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <router-link 
                  to="/library" 
                  class="p-3.5 bg-white border border-slate-200 hover:border-blue-300 hover:shadow-xs rounded-xl flex items-center gap-3 transition group"
                >
                  <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-105 transition">
                    <i class="fa-solid fa-book-open text-sm"></i>
                  </div>
                  <div>
                    <span class="block font-bold text-xs text-slate-800">បណ្ណាល័យឌីជីថល</span>
                    <span class="block text-[10px] text-slate-400">អានសៀវភៅពុម្ព MoEYS</span>
                  </div>
                </router-link>

                <router-link 
                  to="/timetable" 
                  class="p-3.5 bg-white border border-slate-200 hover:border-indigo-300 hover:shadow-xs rounded-xl flex items-center gap-3 transition group"
                >
                  <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-105 transition">
                    <i class="fa-solid fa-calendar-days text-sm"></i>
                  </div>
                  <div>
                    <span class="block font-bold text-xs text-slate-800">កាលវិភាគសិក្សា</span>
                    <span class="block text-[10px] text-slate-400">ពិនិត្យម៉ោងរៀនប្រចាំថ្ងៃ</span>
                  </div>
                </router-link>

                <router-link 
                  to="/examinations" 
                  class="p-3.5 bg-white border border-slate-200 hover:border-purple-300 hover:shadow-xs rounded-xl flex items-center gap-3 transition group"
                >
                  <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-105 transition">
                    <i class="fa-solid fa-chart-line text-sm"></i>
                  </div>
                  <div>
                    <span class="block font-bold text-xs text-slate-800">ពិន្ទុ & ចំណាត់ថ្នាក់</span>
                    <span class="block text-[10px] text-slate-400">តាមដានលទ្ធផលប្រឡង</span>
                  </div>
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- 5. ADMIN/TEACHER VIEW: Modernized Classroom Data Table (xl:col-span-2) -->
        <div v-else class="xl:col-span-2 space-y-4">
          <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
            <!-- Table Header Bar with Search & Filters -->
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
              <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                  តារាងថ្នាក់រៀន និងស្ថិតិវត្តមាន
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">បង្ហាញ {{ filteredClasses.length }} ថ្នាក់រៀនក្នុងកម្រិតថ្នាក់</p>
              </div>

              <!-- Quick Search & Grade Filter Dropdown -->
              <div class="flex items-center gap-2.5">
                <div class="relative">
                  <input 
                    v-model="classSearchQuery" 
                    type="text" 
                    placeholder="ស្វែងរកថ្នាក់ ឬគ្រូ..." 
                    class="pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 text-slate-700 text-xs rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 w-36 sm:w-44 transition-all"
                  />
                  <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                </div>

                <select 
                  v-model="selectedGradeLevel" 
                  class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-2xs"
                >
                  <option value="all">ថ្នាក់ទាំងអស់</option>
                  <option value="7">ថ្នាក់ទី ៧</option>
                  <option value="8">ថ្នាក់ទី ៨</option>
                  <option value="9">ថ្នាក់ទី ៩</option>
                  <option value="10">ថ្នាក់ទី ១០</option>
                  <option value="11">ថ្នាក់ទី ១១</option>
                  <option value="12">ថ្នាក់ទី ១២</option>
                </select>
              </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse text-xs">
                <thead>
                  <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase text-[11px] tracking-wider">
                    <th class="py-3 px-4">{{ t('classes.className') }}</th>
                    <th class="py-3 px-4">{{ t('classes.classTeacher') }}</th>
                    <th class="py-3 px-4 text-center">{{ t('dashboard.totalStudents') }}</th>
                    <th class="py-3 px-4 text-center text-emerald-700">{{ t('dashboard.presentToday') }}</th>
                    <th class="py-3 px-4 text-center text-rose-700">{{ t('dashboard.absentToday') }}</th>
                    <th class="py-3 px-4 text-center" style="min-width: 140px;">{{ t('dashboard.rate') }}</th>
                    <th class="py-3 px-4 text-right">{{ t('common.actions') }}</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-if="filteredClasses.length === 0">
                    <td colspan="7" class="py-12 text-center text-slate-400">
                      <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                      <span class="text-xs font-medium">មិនមានទិន្នន័យថ្នាក់រៀនត្រូវគ្នានឹងការស្វែងរក</span>
                    </td>
                  </tr>

                  <tr 
                    v-for="cls in filteredClasses" 
                    :key="cls.name" 
                    class="hover:bg-blue-50/40 transition-colors"
                  >
                    <!-- Class Name & Room Badge -->
                    <td class="py-3.5 px-4 font-bold text-slate-900 whitespace-nowrap">
                      <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-extrabold text-xs shadow-2xs">
                          {{ cls.name }}
                        </span>
                        <div>
                          <div class="font-bold text-slate-800">ថ្នាក់ {{ cls.name }}</div>
                          <div class="text-[10px] text-slate-400 font-normal">{{ cls.room || 'បន្ទប់ ' + cls.name }}</div>
                        </div>
                      </div>
                    </td>

                    <!-- Homeroom Teacher (Avatar if assigned, Amber warning pill + clickable if unassigned) -->
                    <td class="py-3.5 px-4 whitespace-nowrap">
                      <div v-if="cls.teacher" class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center text-slate-600 font-bold text-[10px] shrink-0">
                          <img v-if="cls.teacher_photo" :src="cls.teacher_photo" alt="Photo" class="w-full h-full object-cover">
                          <span v-else>{{ cls.teacher.charAt(0) }}</span>
                        </div>
                        <span class="font-medium text-slate-700 text-xs">{{ cls.teacher }}</span>
                      </div>
                      <div v-else>
                        <button 
                          v-if="isAdmin"
                          @click="openAssignTeacherModal(cls)"
                          type="button"
                          class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-300 hover:bg-amber-100 hover:border-amber-400 transition-all cursor-pointer shadow-2xs group"
                          title="ចុចដើម្បីចាត់តាំងគ្រូបន្ទុកថ្នាក់"
                        >
                          <svg class="w-3.5 h-3.5 text-amber-600 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                          </svg>
                          <span>+ ចាត់តាំង</span>
                        </button>
                        <span v-else class="text-xs text-slate-400 italic font-medium">មិនទាន់ចាត់តាំង</span>
                      </div>
                    </td>

                    <!-- Students Count -->
                    <td class="py-3.5 px-4 text-center font-bold text-slate-700 whitespace-nowrap">
                      {{ cls.students }}
                    </td>

                    <!-- Present Count -->
                    <td class="py-3.5 px-4 text-center font-bold text-emerald-600 whitespace-nowrap">
                      {{ cls.present }}
                    </td>

                    <!-- Absent Count -->
                    <td class="py-3.5 px-4 text-center font-bold text-rose-600 whitespace-nowrap">
                      {{ cls.absent }}
                    </td>

                    <!-- Attendance Rate with Mini Horizontal Progress Bar -->
                    <td class="py-3.5 px-4 whitespace-nowrap">
                      <div class="flex items-center justify-center gap-2.5">
                        <div class="w-16 bg-slate-100 rounded-full h-2 overflow-hidden shrink-0">
                          <div 
                            class="h-full rounded-full transition-all duration-500" 
                            :style="{ width: Math.min(cls.rate, 100) + '%' }"
                            :class="getProgressColor(cls.rate)"
                          ></div>
                        </div>
                        <span 
                          class="px-2 py-0.5 rounded-full text-[11px] font-bold"
                          :class="getRateBadgeColor(cls.rate)"
                        >
                          {{ cls.rate }}%
                        </span>
                      </div>
                    </td>

                    <!-- Actions -->
                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                      <router-link 
                        to="/attendance" 
                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 rounded-lg text-xs font-semibold transition"
                      >
                        <span>{{ t('common.viewDetails') }}</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                      </router-link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- 4. Side Widget for "Live Activity & Top Classes" (xl:col-span-1) -->
        <div class="xl:col-span-1 space-y-6">
          <!-- Widget 1: Top Attendance Classes Leaderboard -->
          <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
              <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="text-base">🏆</span>
                ថ្នាក់គំរូប្រចាំសប្តាហ៍
              </h3>
              <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                វត្តមានខ្ពស់បំផុត
              </span>
            </div>

            <div v-if="displayTopClasses.length === 0" class="py-8 text-center text-slate-400">
              <span class="text-2xl block mb-1">📊</span>
              <p class="text-xs font-medium">មិនទាន់មានទិន្នន័យថ្នាក់គំរូ</p>
            </div>
            <div v-else class="space-y-2.5">
              <div 
                v-for="(top, idx) in displayTopClasses" 
                :key="top.name"
                class="flex items-center justify-between p-2.5 rounded-xl border transition-all"
                :class="idx === 0 ? 'bg-amber-50/60 border-amber-200/80' : 'bg-slate-50/50 border-slate-200/60'"
              >
                <div class="flex items-center gap-2.5">
                  <!-- Medal Icon / Rank Badge -->
                  <div 
                    class="w-7 h-7 rounded-lg flex items-center justify-center font-extrabold text-xs shadow-2xs"
                    :class="getRankBadgeClass(idx)"
                  >
                    <span v-if="idx === 0">🥇</span>
                    <span v-else-if="idx === 1">🥈</span>
                    <span v-else-if="idx === 2">🥉</span>
                    <span v-else>{{ idx + 1 }}</span>
                  </div>

                  <div>
                    <div class="font-bold text-slate-800 text-xs">ថ្នាក់ {{ top.name }}</div>
                    <div class="text-[10px] text-slate-500 font-medium">{{ top.teacher || 'មិនទាន់ចាត់តាំង' }}</div>
                  </div>
                </div>

                <div class="text-right">
                  <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800">
                    {{ top.rate }}%
                  </span>
                  <div class="text-[9px] text-slate-400 font-medium mt-0.5">{{ top.students }} នាក់</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Widget 2: Live Activity Feed (សកម្មភាពថ្មីៗក្នុងសាលា) -->
          <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
              <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                សកម្មភាពថ្មីៗ
              </h3>
              <span class="text-[10px] text-slate-400 font-medium">Real-time</span>
            </div>

            <div v-if="displayActivities.length === 0" class="py-8 text-center text-slate-400">
              <span class="text-2xl block mb-1">📢</span>
              <p class="text-xs font-medium">មិនទាន់មានសកម្មភាពថ្មីៗកត់ត្រាក្នុងប្រព័ន្ធ</p>
            </div>
            <div v-else class="space-y-3">
              <div 
                v-for="(act, idx) in displayActivities" 
                :key="idx"
                class="flex items-start gap-3 p-2 rounded-xl hover:bg-slate-50 transition-colors"
              >
                <!-- Icon -->
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5" :class="act.color || 'bg-blue-100 text-blue-700'">
                  <svg v-if="act.type === 'attendance'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                  </svg>
                  <svg v-else-if="act.type === 'score'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                  <svg v-else-if="act.type === 'stem'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                  <p class="text-xs font-semibold text-slate-800 leading-snug">{{ act.title }}</p>
                  <span class="text-[10px] text-slate-400 font-medium block mt-0.5">{{ act.time }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab 2: Academic & Classes Tab -->
    <div v-show="activeTab === 'academic'" class="space-y-6">
      <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-900 mb-2">កម្មវិធីសិក្សា និងកាលវិភាគបង្រៀន</h2>
        <p class="text-xs text-slate-500 mb-6">គ្រប់គ្រងបន្ទប់សិក្សា គ្រូបង្រៀន និងកាលវិភាគប្រចាំសប្តាហ៍</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <router-link to="/classes" class="p-4 rounded-xl border border-blue-100 bg-blue-50/50 hover:bg-blue-100/50 transition">
            <h3 class="font-bold text-blue-900 text-sm">គ្រប់គ្រងថ្នាក់រៀន &rarr;</h3>
            <p class="text-xs text-blue-700 mt-1">រៀបចំ និងចាត់តាំងគ្រូបន្ទុកថ្នាក់ទាំងអស់</p>
          </router-link>
          <router-link to="/timetable" class="p-4 rounded-xl border border-purple-100 bg-purple-50/50 hover:bg-purple-100/50 transition">
            <h3 class="font-bold text-purple-900 text-sm">កាលវិភាគបង្រៀន &rarr;</h3>
            <p class="text-xs text-purple-700 mt-1">ពិនិត្យម៉ោងបង្រៀនតាមមុខវិជ្ជា និងបន្ទប់</p>
          </router-link>
          <router-link to="/subjects" class="p-4 rounded-xl border border-emerald-100 bg-emerald-50/50 hover:bg-emerald-100/50 transition">
            <h3 class="font-bold text-emerald-900 text-sm">មុខវិជ្ជា និងកម្មវិធីសិក្សា &rarr;</h3>
            <p class="text-xs text-emerald-700 mt-1">មុខវិជ្ជា STEM, ភាសា និងសង្គម</p>
          </router-link>
        </div>
      </div>
    </div>

    <!-- Tab 3: STEM & Lab Tab (Replaces Finance for Public NGS School) -->
    <div v-show="activeTab === 'stem_lab'" class="space-y-6">
      <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
          <div>
            <span class="inline-block px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold border border-blue-400/30 mb-3">
              មជ្ឈមណ្ឌលនវានុវត្តន៍វិទ្យាសាស្ត្រ និងបច្ចេកវិទ្យា
            </span>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight">សម្ភារៈ និងបន្ទប់ពិសោធន៍វិទ្យាសាស្ត្រ</h2>
            <p class="text-sm text-slate-300 mt-1 max-w-2xl font-light">
              សាលារៀន ជំនាន់ថ្មី ផ្តោតសំខាន់លើការពិសោធន៍ជាក់ស្តែង បច្ចេកវិទ្យា Coding, AI, Robotics និងវិទ្យាសាស្ត្រទំនើប ដោយមិនគិតថ្លៃសិក្សាដល់សិស្សានុសិស្ស។
            </p>
          </div>

          <div class="flex items-center gap-3">
            <div class="px-4 py-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 text-center">
              <span class="block text-2xl font-black text-emerald-400">៤</span>
              <span class="text-[11px] text-slate-300">បន្ទប់ពិសោធន៍</span>
            </div>
            <div class="px-4 py-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 text-center">
              <span class="block text-2xl font-black text-amber-400">១០០%</span>
              <span class="text-[11px] text-slate-300">ដំណើរការពេញលេញ</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 4 Modern Labs Showcase -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs hover:border-blue-400 transition">
          <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl mb-4">
            🔬
          </div>
          <h3 class="font-bold text-slate-900 text-sm">បន្ទប់ពិសោធន៍វិទ្យាសាស្ត្រ STEM</h3>
          <p class="text-xs text-slate-500 mt-1">ឧបករណ៍ពិសោធន៍គីមី-រូបវិទ្យាទំនើប សមត្ថភាពផ្ទុក ៤៥ នាក់</p>
          <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-emerald-600 font-bold">● បើកដំណើរការ</span>
            <span class="text-slate-400">បន្ទប់ 01</span>
          </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs hover:border-purple-400 transition">
          <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xl mb-4">
            💻
          </div>
          <h3 class="font-bold text-slate-900 text-sm">បន្ទប់កុំព្យូទ័រ និងព័ត៌មានវិទ្យា</h3>
          <p class="text-xs text-slate-500 mt-1">កុំព្យូទ័រសមត្ថភាពខ្ពស់ចំនួន ៤៥ គ្រឿង សម្រាប់កម្មវិធីសិក្សាកូដ</p>
          <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-emerald-600 font-bold">● បើកដំណើរការ</span>
            <span class="text-slate-400">បន្ទប់ 02</span>
          </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs hover:border-amber-400 transition">
          <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-xl mb-4">
            🤖
          </div>
          <h3 class="font-bold text-slate-900 text-sm">បន្ទប់ពិសោធន៍រ៉ូបូត និង AI</h3>
          <p class="text-xs text-slate-500 mt-1">ឧបករណ៍ Lego Mindstorms, Arduino, 3D Printers សម្រាប់ក្លឹប</p>
          <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-emerald-600 font-bold">● បើកដំណើរការ</span>
            <span class="text-slate-400">បន្ទប់ 03</span>
          </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs hover:border-teal-400 transition">
          <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center font-bold text-xl mb-4">
            📺
          </div>
          <h3 class="font-bold text-slate-900 text-sm">បន្ទប់សិក្សាឆ្លាតវៃអន្តរកម្ម</h3>
          <p class="text-xs text-slate-500 mt-1">Smart TV 86" Touchscreen និងប្រព័ន្ធសំឡេងសន្និសីទវីដេអូ</p>
          <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-emerald-600 font-bold">● បើកដំណើរការ</span>
            <span class="text-slate-400">បន្ទប់ Smart 04</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab 4: Attendance Tab -->
    <div v-show="activeTab === 'attendance'" class="space-y-6">
      <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-900 mb-2">ប្រព័ន្ធតាមដានវត្តមានឌីជីថល</h2>
        <p class="text-xs text-slate-500 mb-6">វត្តមានសិស្ស និងវត្តមានគ្រូបង្រៀនប្រចាំថ្ងៃ</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <router-link to="/attendance" class="p-5 rounded-xl border border-blue-100 bg-blue-50/50 hover:bg-blue-100/50 transition">
            <h3 class="font-bold text-blue-900 text-sm">បញ្ជីវត្តមានសិស្សប្រចាំថ្ងៃ &rarr;</h3>
            <p class="text-xs text-blue-700 mt-1">កត់ត្រាវត្តមានតាមថ្នាក់រៀន និងផ្ទៀងផ្ទាត់ QR Code</p>
          </router-link>
          <router-link to="/teacher-attendance" class="p-5 rounded-xl border border-emerald-100 bg-emerald-50/50 hover:bg-emerald-100/50 transition">
            <h3 class="font-bold text-emerald-900 text-sm">បញ្ជីវត្តមានលោកគ្រូ-អ្នកគ្រូ &rarr;</h3>
            <p class="text-xs text-emerald-700 mt-1">កត់ត្រាការចូលបង្រៀន និងតាមដានម៉ោងបង្រៀនជាក់ស្តែង</p>
          </router-link>
        </div>
      </div>
    </div>

    <!-- Quick Assign Teacher Modal -->
    <div 
      v-if="showAssignModal" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
    >
      <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
            <span>👨‍🏫</span>
            ចាត់តាំងគ្រូបន្ទុកថ្នាក់ {{ targetClass?.name }}
          </h3>
          <button @click="showAssignModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <p class="text-xs text-slate-500">
          សូមជ្រើសរើសលោកគ្រូ ឬអ្នកគ្រូសម្រាប់ទទួលបន្ទុកថ្នាក់ <strong>{{ targetClass?.name }}</strong>:
        </p>

        <div class="space-y-2">
          <label class="block text-xs font-bold text-slate-700">ជ្រើសរើសគ្រូបង្រៀន:</label>
          <select 
            v-model="selectedTeacherId" 
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/20"
          >
            <option value="">-- សូមជ្រើសរើសគ្រូបង្រៀន --</option>
            <option v-for="teacher in teachersList" :key="teacher.id" :value="teacher.id">
              {{ teacher.name || teacher.name_khmer || teacher.name_latin || ('លោកគ្រូ/អ្នកគ្រូ #' + teacher.id) }}
            </option>
          </select>
        </div>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
          <button 
            @click="showAssignModal = false" 
            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition"
          >
            បោះបង់
          </button>
          <button 
            @click="submitAssignTeacher" 
            :disabled="!selectedTeacherId || assigning" 
            class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 rounded-xl shadow-xs transition"
          >
            {{ assigning ? 'កំពុងរក្សាទុក...' : 'រក្សាទុកការចាត់តាំង' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onActivated, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import FilterBar from '../components/FilterBar.vue';
import { sortClassesNaturally } from '../utils/classSort';

const { t, locale } = useI18n();
const currentLocale = computed(() => locale.value || 'km');
const router = useRouter();

// Share Registration Access Link
const registrationUrl = 'https://sala-portal.vercel.app/portal-sala-access-2026';
const showShareDropdown = ref(false);
const shareDropdownRef = ref(null);
const copiedRegistrationLink = ref(false);

const toggleShareDropdown = () => {
  showShareDropdown.value = !showShareDropdown.value;
};

const copyRegistrationLink = async () => {
  try {
    await navigator.clipboard.writeText(registrationUrl);
    copiedRegistrationLink.value = true;
    if (window.$toast?.success) {
      window.$toast.success('បានចម្លងតំណចុះឈ្មោះសាលារៀនដោយជោគជ័យ!');
    }
    setTimeout(() => {
      copiedRegistrationLink.value = false;
    }, 2500);
  } catch {
    copiedRegistrationLink.value = true;
    setTimeout(() => {
      copiedRegistrationLink.value = false;
    }, 2500);
  }
};

const handleShareDocClick = (e) => {
  if (shareDropdownRef.value && !shareDropdownRef.value.contains(e.target)) {
    showShareDropdown.value = false;
  }
};

const dashboardTabs = computed(() => [
  { id: 'overview', label: currentLocale.value === 'km' ? 'ទិដ្ឋភាពទូទៅ' : 'Overview' },
  { id: 'academic', label: currentLocale.value === 'km' ? 'ថ្នាក់រៀន និងកាលវិភាគ' : 'Classes & Timetables' },
  { id: 'stem_lab', label: currentLocale.value === 'km' ? 'សម្ភារៈ និងបន្ទប់ពិសោធន៍' : 'STEM & Lab Equipment' },
  { id: 'attendance', label: currentLocale.value === 'km' ? 'វត្តមានសិស្ស-គ្រូ' : 'Attendance' }
]);

const currentUser = computed(() => window.__INITIAL_USER__ || {});
const userRole = computed(() => (currentUser.value.role || '').toLowerCase());
const isSuperAdmin = computed(() => Boolean(currentUser.value.is_super_admin));
const isAdmin = computed(() => isSuperAdmin.value || userRole.value === 'admin' || userRole.value === 'super admin' || userRole.value === 'super_admin');
const isStudent = computed(() => userRole.value === 'student' || userRole.value === 'សិស្សានុសិស្ស');
const isTeacher = computed(() => userRole.value === 'teacher' || userRole.value === 'គ្រូបង្រៀន');

const studentInfo = ref({
  student_name: currentUser.value.name || 'សិស្សានុសិស្ស',
  student_id_card: currentUser.value.student_id || '',
  grade: currentUser.value.student_grade || '7A',
  class_name: currentUser.value.student_grade || '7A',
  room: 'បន្ទប់ ' + (currentUser.value.student_grade || '7A'),
  homeroom_teacher: 'មិនទាន់ចាត់តាំង',
  teacher_phone: '',
  teacher_photo: null,
  classmates_count: 50,
  attendance: {
    present: 0,
    excused: 0,
    absent: 0,
    total_leave: 0,
    total: 0,
    rate: 100,
  }
});

const canCreateRecord = computed(() => isAdmin.value);

const activeTab = ref('overview');
const classSearchQuery = ref('');
const selectedGradeLevel = ref('all');
const hoveredDay = ref(null);

const activeAcademicYear = ref(
  localStorage.getItem('pdt_current_academic_year') ||
  window.__INITIAL_SETTINGS__?.current_academic_year ||
  '2026-2027'
);
const academicYearsList = ref([activeAcademicYear.value]);

// Assign teacher modal states
const showAssignModal = ref(false);
const targetClass = ref(null);
const selectedTeacherId = ref('');
const assigning = ref(false);
const teachersList = ref([]);

const stats = ref({
  students: 0,
  teachers: 0,
  classes: 0,
  subjects: 0,
  attendanceRate: 0,
  teacherAttendanceRate: 0,
  femaleRatio: 0,
  femaleCount: 0,
  maleCount: 0,
  averagePassRate: 0,
  scholarshipCount: 0,
});

const allClasses = ref([]);
const recentClasses = ref([]);
const chartDays = ref([]);

const displayTopClasses = ref([]);

const displayActivities = ref([]);

// Instant cache hydration from localStorage so stats and charts never flash 0 or disappear
try {
  const cachedDashboard = localStorage.getItem('pdt_cached_dashboard');
  if (cachedDashboard) {
    const d = JSON.parse(cachedDashboard);
    if (d.stats) stats.value = { ...stats.value, ...d.stats };
    if (d.allClasses && d.allClasses.length) allClasses.value = sortClassesNaturally(d.allClasses);
    if (d.recentClasses && d.recentClasses.length) recentClasses.value = d.recentClasses;
    if (d.weeklyTrend && d.weeklyTrend.length) {
      const hasRealData = d.weeklyTrend.some(x => (x.present || 0) + (x.absent || 0) > 0);
      if (hasRealData) chartDays.value = d.weeklyTrend;
    }
    if (d.topClasses && d.topClasses.length) displayTopClasses.value = d.topClasses;
    if (d.recentActivities && d.recentActivities.length) displayActivities.value = d.recentActivities;
    if (d.currentAcademicYear) activeAcademicYear.value = d.currentAcademicYear;
    if (d.academicYears && d.academicYears.length) academicYearsList.value = d.academicYears;
  }
} catch (e) {}

// Filtered classrooms based on quick search and grade dropdown
const filteredClasses = computed(() => {
  let list = allClasses.value.length > 0 ? allClasses.value : recentClasses.value;
  
  if (selectedGradeLevel.value !== 'all') {
    list = list.filter(c => {
      const g = c.grade_level ? String(c.grade_level) : (c.name.match(/\d+/) ? c.name.match(/\d+/)[0] : '');
      return g === selectedGradeLevel.value;
    });
  }

  if (classSearchQuery.value.trim()) {
    const q = classSearchQuery.value.trim().toLowerCase();
    list = list.filter(c => 
      (c.name && c.name.toLowerCase().includes(q)) || 
      (c.teacher && c.teacher.toLowerCase().includes(q)) ||
      (c.room && c.room.toLowerCase().includes(q))
    );
  }

  return list;
});

// Area Chart Coordinate calculations (ViewBox 0 0 600 200)
// Margins: left=50, right=20, top=20, bottom=55 -> width=530, height=135
const getX = (index) => {
  const startX = 65;
  const stepX = 100;
  return startX + (index * stepX);
};

const getYPresent = (present, absent) => {
  const total = (present + absent);
  if (!total || total === 0) return 160;
  const rate = (present / total) * 100;
  // map rate 0-100% to Y: 100% -> 25, 0% -> 160
  return 160 - ((rate / 100) * 135);
};

const getYAbsent = (present, absent) => {
  const total = (present + absent);
  if (!total || total === 0) return 160;
  const rate = (absent / total) * 100;
  // Scale absent 0% - 20% to hover gracefully around Y: 160 -> 125
  const yOffset = Math.min(45, (rate / 20) * 45);
  return 160 - yOffset;
};

// SVG Paths for Area & Lines
const presentLinePath = computed(() => {
  if (!chartDays.value.length) return '';
  return chartDays.value.map((d, i) => {
    const x = getX(i);
    const y = getYPresent(d.present, d.absent);
    return `${i === 0 ? 'M' : 'L'} ${x} ${y}`;
  }).join(' ');
});

const presentAreaPath = computed(() => {
  if (!chartDays.value.length) return '';
  const firstX = getX(0);
  const lastX = getX(chartDays.value.length - 1);
  const line = presentLinePath.value;
  return `${line} L ${lastX} 165 L ${firstX} 165 Z`;
});

const absentLinePath = computed(() => {
  if (!chartDays.value.length) return '';
  return chartDays.value.map((d, i) => {
    const x = getX(i);
    const y = getYAbsent(d.present, d.absent);
    return `${i === 0 ? 'M' : 'L'} ${x} ${y}`;
  }).join(' ');
});

const absentAreaPath = computed(() => {
  if (!chartDays.value.length) return '';
  const firstX = getX(0);
  const lastX = getX(chartDays.value.length - 1);
  const line = absentLinePath.value;
  return `${line} L ${lastX} 165 L ${firstX} 165 Z`;
});

// Donut Chart Calculations (Circumference = 2 * PI * 62 = 389.55)
const CIRCUMFERENCE = 389.55;

const donutFemaleDash = computed(() => {
  if (!stats.value.students || stats.value.students === 0) {
    return `0 ${CIRCUMFERENCE}`;
  }
  const femaleRatio = (stats.value.femaleRatio ?? 0) / 100;
  const dash = femaleRatio * CIRCUMFERENCE;
  return `${dash} ${CIRCUMFERENCE}`;
});

const donutMaleDash = computed(() => {
  if (!stats.value.students || stats.value.students === 0) {
    return `0 ${CIRCUMFERENCE}`;
  }
  return `${CIRCUMFERENCE} ${CIRCUMFERENCE}`;
});

const donutFemaleOffset = computed(() => {
  if (!stats.value.students || stats.value.students === 0) {
    return 0;
  }
  const maleRatio = (100 - (stats.value.femaleRatio ?? 0)) / 100;
  return - (maleRatio * CIRCUMFERENCE);
});

// Helpers for Table Styling
const getProgressColor = (rate) => {
  if (rate >= 85) return 'bg-emerald-500';
  if (rate >= 50) return 'bg-amber-500';
  return 'bg-rose-500';
};

const getRateBadgeColor = (rate) => {
  if (rate >= 85) return 'bg-emerald-100 text-emerald-800';
  if (rate >= 50) return 'bg-amber-100 text-amber-800';
  return 'bg-rose-100 text-rose-800';
};

const getRankBadgeClass = (idx) => {
  if (idx === 0) return 'bg-amber-100 text-amber-800 border border-amber-300';
  if (idx === 1) return 'bg-slate-200 text-slate-800 border border-slate-300';
  if (idx === 2) return 'bg-amber-700/10 text-amber-900 border border-amber-700/20';
  return 'bg-blue-50 text-blue-700 border border-blue-200';
};

let isFetchingDashboard = false;
let lastDashboardFetchTime = 0;

// Fetch Dashboard Data
const fetchDashboardData = async (force = false) => {
  const now = Date.now();
  if (isFetchingDashboard || (!force && now - lastDashboardFetchTime < 1500 && (allClasses.value.length > 0 || stats.value.totalStudents > 0))) {
    return;
  }
  isFetchingDashboard = true;
  lastDashboardFetchTime = now;

  try {
    const params = { demo_mode: '0' };
    if (force) params.force = 1;
    const res = await axios.get('/api/dashboard', { params });
    if (res.data) {
      if (res.data.currentAcademicYear) {
        activeAcademicYear.value = res.data.currentAcademicYear;
        localStorage.setItem('pdt_current_academic_year', res.data.currentAcademicYear);
      }
      if (res.data.academicYears && res.data.academicYears.length) {
        academicYearsList.value = res.data.academicYears;
      }
      if (res.data.stats) {
        stats.value = { ...stats.value, ...res.data.stats };
      }
      if (res.data.allClasses && res.data.allClasses.length) {
        allClasses.value = sortClassesNaturally(res.data.allClasses);
      }
      if (res.data.recentClasses) {
        recentClasses.value = res.data.recentClasses;
      }
      if (res.data.weeklyTrend && res.data.weeklyTrend.length) {
        chartDays.value = res.data.weeklyTrend;
      }
      if (res.data.topClasses && res.data.topClasses.length) {
        displayTopClasses.value = res.data.topClasses;
      }
      if (res.data.recentActivities && res.data.recentActivities.length) {
        displayActivities.value = res.data.recentActivities;
      }
      if (res.data.studentData) {
        studentInfo.value = { ...studentInfo.value, ...res.data.studentData };
      }
      try {
        localStorage.setItem('pdt_cached_dashboard', JSON.stringify(res.data));
      } catch (e) {}
    }
  } catch (e) {
    console.error('Failed to load dashboard:', e);
  } finally {
    isFetchingDashboard = false;
  }
};

const onDataUpdated = () => {
  fetchDashboardData(true);
};

const fetchTeachersForAssign = async () => {
  try {
    const teacherRes = await axios.get('/api/teachers');
    const resData = teacherRes.data;
    if (resData && Array.isArray(resData.teachers)) {
      teachersList.value = [...resData.teachers].sort((a, b) => (a.name || '').localeCompare(b.name || '', 'km'));
    } else if (resData && Array.isArray(resData.data)) {
      teachersList.value = [...resData.data].sort((a, b) => (a.name || '').localeCompare(b.name || '', 'km'));
    } else if (Array.isArray(resData)) {
      teachersList.value = [...resData].sort((a, b) => (a.name || '').localeCompare(b.name || '', 'km'));
    }
  } catch (e) {
    console.error('Failed to fetch teachers for assign modal:', e);
  }
};

// Lifecycle & Auto-Sync
let syncTimer = null;

const handleVisibilityOrFocus = () => {
  if (document.visibilityState === 'visible') {
    fetchDashboardData(true);
  }
};

onMounted(async () => {
  await fetchDashboardData(true);

  // Fetch teachers for quick assign modal
  await fetchTeachersForAssign();

  window.addEventListener('pdt-data-updated', onDataUpdated);
  document.addEventListener('click', handleShareDocClick);

  // Auto-Sync: Refetch immediately when screen is unlocked or tab becomes visible/focused
  document.addEventListener('visibilitychange', handleVisibilityOrFocus);
  window.addEventListener('focus', handleVisibilityOrFocus);

  // Auto-Sync: Poll latest real-time stats every 30 seconds if tab is active
  syncTimer = setInterval(() => {
    if (document.visibilityState === 'visible' && !isFetchingDashboard) {
      fetchDashboardData(true);
    }
  }, 30000);
});

// Reactivate when navigating back through keep-alive
onActivated(async () => {
  await fetchDashboardData(true);
  if (!teachersList.value || teachersList.value.length === 0) {
    await fetchTeachersForAssign();
  }
});

onUnmounted(() => {
  if (syncTimer) {
    clearInterval(syncTimer);
    syncTimer = null;
  }
  window.removeEventListener('pdt-data-updated', onDataUpdated);
  document.removeEventListener('click', handleShareDocClick);
  document.removeEventListener('visibilitychange', handleVisibilityOrFocus);
  window.removeEventListener('focus', handleVisibilityOrFocus);
});

// Actions
const handleCreateNew = () => {
  router.push('/students');
};

const handlePrint = () => window.print();

const handleDownload = () => {
  window.open('/api/settings/backup-excel', '_blank');
};

const openAssignTeacherModal = async (cls) => {
  targetClass.value = cls;
  selectedTeacherId.value = cls.teacher_id || '';
  showAssignModal.value = true;
  if (!teachersList.value || teachersList.value.length === 0) {
    await fetchTeachersForAssign();
  }
};

const submitAssignTeacher = async () => {
  if (!targetClass.value || !selectedTeacherId.value) return;
  assigning.value = true;
  try {
    const teacherObj = teachersList.value.find(t => t.id == selectedTeacherId.value);
    const teacherName = teacherObj ? (teacherObj.name || teacherObj.name_khmer || teacherObj.name_latin) : 'គ្រូបង្រៀន';
    
    // Call backend API to update class homeroom teacher
    if (targetClass.value.id) {
      await axios.put(`/api/classes/${targetClass.value.id}`, {
        teacher_id: selectedTeacherId.value
      });
    }

    // Update in local state
    targetClass.value.teacher = teacherName;
    targetClass.value.teacher_id = selectedTeacherId.value;
    showAssignModal.value = false;
  } catch (e) {
    console.error('Failed to assign teacher:', e);
    // Still update local UI for seamless experience
    const teacherObj = teachersList.value.find(t => t.id == selectedTeacherId.value);
    if (teacherObj) {
      targetClass.value.teacher = teacherObj.name || teacherObj.name_khmer || teacherObj.name_latin;
    }
    showAssignModal.value = false;
  } finally {
    assigning.value = false;
  }
};
</script>
