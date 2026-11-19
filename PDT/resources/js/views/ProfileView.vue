<template>
  <div class="space-y-4 font-khmer pb-16 max-w-7xl mx-auto">
    <!-- Compact Page Header -->
    <PageHeader 
      :title="t('accountSettings.personalInfo', 'ព័ត៌មានផ្ទាល់ខ្លួន')" 
      :breadcrumb="t('accountSettings.personalInfo', 'ព័ត៌មានផ្ទាល់ខ្លួន')"
    >
      <template #extraActions>
        <button 
          v-if="profile?.student?.student_id_card"
          @click="showIdCardModal = true"
          type="button"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all active:scale-95 cursor-pointer"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
          </svg>
          <span>កាតសិស្ស (ID Card)</span>
        </button>

        <button 
          @click="fetchProfile"
          type="button"
          :disabled="loading"
          class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium rounded-xl border border-slate-200 shadow-2xs transition active:scale-95 cursor-pointer"
          title="ផ្ទុកទិន្នន័យឡើងវិញ"
        >
          <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          <span class="hidden sm:inline">ផ្ទុកឡើងវិញ</span>
        </button>
      </template>
    </PageHeader>

    <!-- Loading State Skeleton -->
    <div v-if="loading && !profile" class="animate-pulse space-y-4">
      <div class="h-28 bg-slate-200 rounded-2xl"></div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="h-64 bg-slate-200 rounded-2xl"></div>
        <div class="h-64 bg-slate-200 rounded-2xl"></div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="errorMsg" class="p-5 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-center space-y-2.5">
      <p class="font-medium text-xs sm:text-sm">{{ errorMsg }}</p>
      <button @click="fetchProfile" class="px-3.5 py-1.5 bg-rose-600 text-white rounded-xl text-xs font-semibold">ព្យាយាមម្តងទៀត</button>
    </div>

    <!-- Profile Content -->
    <div v-else class="space-y-4">
      <!-- 1. CENTERED CIRCULAR USER PROFILE AVATAR ONLY (No background banner, No title/text, Only Avatar) -->
      <div class="py-4 sm:py-6 flex flex-col items-center justify-center text-center">
        <!-- Hidden File Input for Avatar Upload -->
        <input 
          type="file" 
          ref="avatarFileInput" 
          accept="image/png,image/jpeg,image/webp,image/jpg" 
          class="hidden" 
          @change="onAvatarFileChange"
        />

        <!-- Centered Circular Avatar with Camera Action -->
        <div class="relative group">
          <div 
            @click="triggerAvatarUpload"
            class="w-28 h-28 sm:w-32 sm:h-32 rounded-full overflow-hidden ring-4 ring-blue-500/20 hover:ring-blue-500/50 shadow-xl bg-slate-100 flex items-center justify-center relative cursor-pointer transition-all duration-300 hover:scale-105 active:scale-95 mx-auto"
            title="ចុចដើម្បីប្តូររូបភាព Profile"
          >
            <!-- Avatar Image -->
            <img 
              v-if="displayAvatar"
              :src="displayAvatar" 
              alt="Profile Avatar" 
              class="w-full h-full object-cover"
              @error="onAvatarError"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-blue-600 text-white text-3xl font-black">
              {{ (displayName || 'U').charAt(0) }}
            </div>

            <!-- Hover Camera Overlay -->
            <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-xs flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200">
              <svg class="w-7 h-7 text-white mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <span class="text-[10px] font-bold text-white">ប្តូររូប</span>
            </div>

            <!-- Uploading Spinner -->
            <div v-if="uploadingAvatar" class="absolute inset-0 bg-slate-950/70 backdrop-blur-xs flex flex-col items-center justify-center text-white z-20">
              <svg class="w-7 h-7 animate-spin text-white mb-1" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
              </svg>
              <span class="text-[9px] font-bold text-blue-200">កំពុងរក្សាទុក...</span>
            </div>
          </div>

          <!-- Quick Action Camera Button at Bottom-Right of Avatar -->
          <button 
            type="button" 
            @click.stop="triggerAvatarUpload"
            class="absolute bottom-1 right-1 w-9 h-9 rounded-full bg-blue-600 hover:bg-blue-700 text-white shadow-lg ring-3 ring-white flex items-center justify-center transition-transform active:scale-90 cursor-pointer"
            title="ចុចដើម្បីប្តូររូបភាព Profile"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- 2. MAIN BENTO GRID (Clean Modern 2-Column Responsive Layout) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- COLUMN 1: OFFICIAL IDENTITY & ACADEMIC DETAILS -->
        <div>
          
          <!-- Card A: Information Details (សិស្ស ឬ គ្រូ ឬ អ្នកគ្រប់គ្រង) -->
          <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-2xs space-y-3">
            <!-- Header -->
            <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xs sm:text-sm font-bold text-slate-800 leading-tight">
                    {{ isStudent ? 'ព័ត៌មានអត្តសញ្ញាណសិស្ស' : isTeacher ? 'ព័ត៌មានគ្រូបង្រៀន' : 'ព័ត៌មានគណនីគ្រប់គ្រង' }}
                  </h3>
                  <p class="text-[10px] text-slate-400">ទិន្នន័យផ្លូវការក្នុងប្រព័ន្ធសាលា</p>
                </div>
              </div>

              <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                {{ formatStatus(profile?.student?.status) }}
              </span>
            </div>

            <!-- Student Detail List Rows (Label Left, Value Right, Light Divider) -->
            <div v-if="isStudent && profile?.student" class="divide-y divide-slate-100 text-xs">
              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">ឈ្មោះជាភាសាខ្មែរ</span>
                <span class="font-bold text-slate-800 text-right truncate pl-3">{{ profile.student.name_khmer || '-' }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">ឈ្មោះជាអក្សរឡាតាំង</span>
                <span class="font-bold text-slate-800 font-sans text-right truncate pl-3">{{ profile.student.name_latin || '-' }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">អត្តលេខសិស្ស (ID)</span>
                <div class="flex items-center gap-1.5 pl-3">
                  <span class="font-mono font-bold text-blue-600">{{ profile.student.student_id_card || '-' }}</span>
                  <button 
                    v-if="profile.student.student_id_card" 
                    @click="copyText(profile.student.student_id_card, 'អត្តលេខ')"
                    class="p-1 text-slate-400 hover:text-blue-600 transition cursor-pointer"
                    title="ចម្លងអត្តលេខ"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                  </button>
                </div>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">ភេទ</span>
                <span class="font-bold text-slate-800">{{ formatGender(profile.student.gender) }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">ថ្ងៃខែឆ្នាំកំណើត</span>
                <span class="font-bold text-slate-800">{{ formatDate(profile.student.date_of_birth) }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">ថ្នាក់រៀន</span>
                <span class="font-bold text-indigo-600">ថ្នាក់ទី {{ profile.student.grade || '-' }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">គ្រូបន្ទុកថ្នាក់</span>
                <span class="font-bold text-slate-800 text-right truncate pl-3">{{ profile.student.homeroom_teacher || '-' }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">កាលបរិច្ឆេទចុះឈ្មោះ</span>
                <span class="font-bold text-slate-800">{{ formatDate(profile.student.enrollment_date) }}</span>
              </div>
            </div>

            <!-- Teacher Detail List Rows -->
            <div v-else-if="isTeacher && profile?.teacher" class="divide-y divide-slate-100 text-xs">
              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">ឈ្មោះគ្រូបង្រៀន</span>
                <span class="font-bold text-slate-800 text-right truncate pl-3">{{ profile.teacher.name_khmer || profile.teacher.name_latin }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">កូដសម្គាល់គ្រូ (Teacher Code)</span>
                <span class="font-mono font-bold text-blue-600">{{ profile.teacher.teacher_id_card || '-' }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">មុខវិជ្ជាបង្រៀន</span>
                <span class="font-bold text-indigo-600">{{ profile.teacher.subject || '-' }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">ឯកទេស</span>
                <span class="font-bold text-slate-800">{{ profile.teacher.specialization || '-' }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">កម្រិតវប្បធម៌ / សញ្ញាបត្រ</span>
                <span class="font-bold text-slate-800 text-right truncate pl-3">{{ profile.teacher.qualification || '-' }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">ថ្នាក់ទទួលបន្ទុក</span>
                <span class="font-bold text-slate-800">{{ profile.teacher.class_assigned || '-' }}</span>
              </div>
            </div>

            <!-- Admin Detail List Rows -->
            <div v-else class="divide-y divide-slate-100 text-xs">
              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">ឈ្មោះអ្នកគ្រប់គ្រង</span>
                <span class="font-bold text-slate-800 text-right truncate pl-3">{{ profile?.user?.name }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">តួនាទីក្នុងប្រព័ន្ធ</span>
                <span class="font-bold text-indigo-600">{{ profile?.user?.role_khmer || profile?.user?.role }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">គណនីអ៊ីមែល</span>
                <span class="font-bold text-slate-800 font-sans text-right truncate pl-3">{{ profile?.user?.email }}</span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">កាលបរិច្ឆេទបង្កើត</span>
                <span class="font-bold text-slate-800">{{ profile?.user?.created_at || '-' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- COLUMN 2: CONTACT, ADDRESS & FAMILY INFORMATION -->
        <div class="space-y-4">
          <!-- Card B: Contact & Address Information (Clean List Rows) -->
          <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-2xs space-y-3">
            <div class="flex items-center gap-2 pb-2.5 border-b border-slate-100">
              <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 leading-tight">ព័ត៌មានទំនាក់ទំនង & អាសយដ្ឋាន</h3>
                <p class="text-[10px] text-slate-400">លេខទូរស័ព្ទ អ៊ីមែល និងទីលំនៅបច្ចុប្បន្ន</p>
              </div>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">លេខទូរស័ព្ទ</span>
                <div class="flex items-center gap-2 pl-3">
                  <span class="font-mono font-bold text-slate-800">{{ displayPhone || '-' }}</span>
                  <a 
                    v-if="displayPhone" 
                    :href="`tel:${displayPhone}`" 
                    class="px-2 py-0.5 text-[10px] font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md transition"
                  >
                    ហៅចេញ
                  </a>
                </div>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">អ៊ីមែល</span>
                <span class="font-bold text-slate-800 font-sans text-right truncate pl-3">{{ profile?.user?.email || '-' }}</span>
              </div>

              <div class="flex items-start justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0 pt-0.5">អាសយដ្ឋានបច្ចុប្បន្ន</span>
                <span class="font-medium text-slate-800 text-right leading-relaxed pl-3 max-w-[65%]">
                  {{ displayAddress || 'មិនទាន់បញ្ចូលអាសយដ្ឋាន' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Card C: Parent & Family Information (For Students Only) -->
          <div v-if="isStudent && profile?.student" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-2xs space-y-3">
            <div class="flex items-center gap-2 pb-2.5 border-b border-slate-100">
              <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 leading-tight">ព័ត៌មានអាណាព្យាបាល & គ្រួសារ</h3>
                <p class="text-[10px] text-slate-400">ព័ត៌មានឪពុកម្តាយ ឬអាណាព្យាបាល</p>
              </div>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">អាណាព្យាបាលចម្បង</span>
                <span class="font-bold text-slate-800 text-right truncate pl-3">
                  {{ profile.student.parent_name || '-' }}
                  <span v-if="profile.student.parent_relationship" class="text-[11px] font-normal text-slate-500">
                    ({{ profile.student.parent_relationship }})
                  </span>
                </span>
              </div>

              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">ទូរស័ព្ទអាណាព្យាបាល</span>
                <div class="flex items-center gap-2 pl-3">
                  <span class="font-mono font-bold text-slate-800">{{ profile.student.parent_phone || '-' }}</span>
                  <a 
                    v-if="profile.student.parent_phone" 
                    :href="`tel:${profile.student.parent_phone}`" 
                    class="px-2 py-0.5 text-[10px] font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md transition"
                  >
                    ហៅ
                  </a>
                </div>
              </div>

              <!-- Telegram Notification Status -->
              <div class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0 flex items-center gap-1.5">
                  <i class="fa-brands fa-telegram text-sky-500"></i>
                  <span>ដំណឹង Telegram</span>
                </span>
                <div class="flex items-center gap-2 pl-3">
                  <span 
                    v-if="profile.student.telegram_chat_id" 
                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>បានភ្ជាប់រួចរាល់</span>
                  </span>
                  <a 
                    v-else
                    :href="profile.student.telegram_deep_link || 'https://t.me/SalaParentAlertBot'" 
                    target="_blank"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-white bg-sky-500 hover:bg-sky-600 rounded-lg shadow-xs transition"
                  >
                    <i class="fa-brands fa-telegram"></i>
                    <span>ភ្ជាប់ Telegram</span>
                  </a>
                </div>
              </div>

              <div v-if="profile.student.father_name" class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">ឈ្មោះឪពុក</span>
                <div class="text-right pl-3">
                  <span class="font-bold text-slate-800">{{ profile.student.father_name }}</span>
                  <span v-if="profile.student.father_phone" class="block text-[11px] text-slate-500 font-mono">{{ profile.student.father_phone }}</span>
                </div>
              </div>

              <div v-if="profile.student.mother_name" class="flex items-center justify-between py-2 sm:py-2.5">
                <span class="text-slate-400 font-medium shrink-0">ឈ្មោះម្តាយ</span>
                <div class="text-right pl-3">
                  <span class="font-bold text-slate-800">{{ profile.student.mother_name }}</span>
                  <span v-if="profile.student.mother_phone" class="block text-[11px] text-slate-500 font-mono">{{ profile.student.mother_phone }}</span>
                </div>
              </div>

              <div v-if="profile.student.emergency_contact" class="flex items-center justify-between py-2 sm:py-2.5 text-rose-700 bg-rose-50/50 -mx-4 px-4 sm:-mx-5 sm:px-5">
                <span class="font-semibold flex items-center gap-1 shrink-0">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                  ទំនាក់ទំនងបន្ទាន់
                </span>
                <span class="font-mono font-bold">{{ profile.student.emergency_contact }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ID CARD MODAL / PREVIEW FOR STUDENTS -->
    <div 
      v-if="showIdCardModal && profile?.student"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-2 sm:p-4 animate-fadeIn"
      @click.self="showIdCardModal = false"
    >
      <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-100 flex flex-col">
        <!-- Modal Header -->
        <div class="px-4 sm:px-5 py-2.5 sm:py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
            ប័ណ្ណសម្គាល់ខ្លួនសិស្ស (Digital ID Card)
          </h3>
          <button @click="showIdCardModal = false" class="text-slate-400 hover:text-slate-600 text-sm p-1 rounded-lg">✕</button>
        </div>

        <!-- ID Card Body (Official MoEYS Vertical Style strictly matching reference) -->
        <div class="p-2 sm:p-5 flex justify-center bg-slate-100/80 overflow-y-auto overflow-x-auto max-h-[85vh]">
          <div 
            id="student-id-card-element" 
            class="relative w-full max-w-[390px] min-w-[315px] sm:min-w-[360px] bg-white border-[3px] border-[#1f4e79] text-black shadow-xl select-none print:shadow-none print:m-0 font-battambang overflow-hidden mx-auto"
          >
            <!-- Top Header (with bottom border connecting edge-to-edge) -->
            <div class="px-3 sm:px-4 pt-2.5 sm:pt-3.5 pb-2 sm:pb-2.5 border-b-[3px] border-[#1f4e79] relative z-10 bg-white">
              <!-- Top-left Logo + Top-center Kingdom Heading -->
              <div class="flex items-start justify-between gap-1.5 sm:gap-2">
                <!-- MoEYS Logo cleanly placed without square border box -->
                <div class="w-13 h-13 sm:w-16 sm:h-16 shrink-0 flex items-center justify-center">
                  <img :src="'/images/moeys-logo.png'" alt="MoEYS Logo" class="w-12 h-12 sm:w-15 sm:h-15 object-contain" />
                </div>

                <!-- Top-center Kingdom Header -->
                <div class="text-center flex-1 pr-1">
                  <h4 class="font-moul text-[12.5px] sm:text-[14px] text-black tracking-wide leading-tight whitespace-nowrap">ព្រះរាជាណាចក្រកម្ពុជា</h4>
                  <h5 class="font-moul text-[10.5px] sm:text-[12px] text-black tracking-wide leading-tight mt-0.5 sm:mt-1 whitespace-nowrap">ជាតិ សាសនា ព្រះមហាក្សត្រ</h5>
                  <!-- Decorative Khmer Divider (៚ / ༺༻) -->
                  <div class="flex items-center justify-center gap-1.5 my-0.5 sm:my-1">
                    <span class="h-[1px] w-7 sm:w-9 bg-[#1f4e79]"></span>
                    <span class="text-[9px] sm:text-[10px] text-[#1f4e79] leading-none">༺ ៚ ༻</span>
                    <span class="h-[1px] w-7 sm:w-9 bg-[#1f4e79]"></span>
                  </div>
                </div>
              </div>

              <!-- Bottom of Header: Centered in blue Moul font -->
              <div class="mt-1 sm:mt-1.5 text-center">
                <p class="font-moul text-[11px] sm:text-[12.5px] text-[#1f4e79] leading-snug tracking-tight whitespace-nowrap">
                  មន្ទីរអប់រំ យុវជន និង កីឡា រាជធានីភ្នំពេញ
                </p>
              </div>
            </div>

            <!-- Main Card Body Section -->
            <div class="px-3.5 sm:px-4.5 pt-2.5 sm:pt-3 pb-3.5 sm:pb-4 relative z-10">
              <!-- Center Background Watermark (MoEYS emblem at subtle ~8% opacity) -->
              <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.08] z-0 overflow-hidden">
                <img :src="'/images/moeys-logo.png'" alt="MoEYS Watermark" class="w-68 h-68 object-contain" />
              </div>

              <!-- Title Block -->
              <div class="relative z-10 text-center my-1 sm:my-1.5">
                <h2 class="card-main-title text-lg sm:text-[23px] tracking-wide leading-tight">
                  ប័ណ្ណសម្គាល់ខ្លួនសិស្ស
                </h2>
                <p class="font-bold text-[11.5px] sm:text-[13px] text-black mt-1 sm:mt-1.5 font-battambang">
                  ឆ្នាំសិក្សា {{ formatAcademicYear(profile.student.academic_year) }}
                </p>
                <p class="font-moul text-[11.5px] sm:text-[13.5px] text-black mt-0.5">
                  ថ្នាក់ទី {{ toKhmerNum(profile.student.grade || '៧A') }}
                </p>
              </div>

              <!-- Student Details: Sized large, bold and clearly legible matching reference -->
              <div class="relative z-10 mt-2 sm:mt-3 text-[12px] sm:text-[13.5px] space-y-1.5 sm:space-y-2 leading-relaxed text-black font-battambang">
                <!-- Name & Gender (Two Columns balanced) -->
                <div class="flex items-center justify-between gap-2 sm:gap-4">
                  <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                    <span class="font-bold text-black shrink-0 whitespace-nowrap">គោត្តនាម នាម :</span>
                    <span class="font-bold text-[#154689] text-[13px] sm:text-[14.5px] truncate">{{ profile.student.name_khmer || displayName }}</span>
                  </div>
                  <div class="flex items-center gap-1 sm:gap-1.5 shrink-0 pr-0.5">
                    <span class="font-bold text-black shrink-0 whitespace-nowrap">ភេទ :</span>
                    <span class="font-bold text-[#154689] text-[12.5px] sm:text-[14px] whitespace-nowrap">{{ formatGender(profile.student.gender) }}</span>
                  </div>
                </div>

                <!-- Date of Birth (Single Line) -->
                <div class="flex items-center gap-1 sm:gap-1.5">
                  <span class="font-bold text-black shrink-0 whitespace-nowrap">ថ្ងៃខែឆ្នាំកំណើត :</span>
                  <span class="font-bold text-[#154689] text-[12.5px] sm:text-[14px] whitespace-nowrap">{{ toKhmerDate(profile.student.date_of_birth) }}</span>
                </div>

                <!-- Place of Birth (Single Line) -->
                <div class="flex items-start gap-1 sm:gap-1.5">
                  <span class="font-bold text-black shrink-0 whitespace-nowrap">ទីកន្លែងកំណើត :</span>
                  <span class="font-bold text-[#154689] text-[12.5px] sm:text-[14px] truncate">{{ studentPlaceOfBirth }}</span>
                </div>

                <!-- Father & Mother Names (Two Columns balanced) -->
                <div class="flex items-center justify-between gap-2 sm:gap-4">
                  <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                    <span class="font-bold text-black shrink-0 whitespace-nowrap">ឪពុកឈ្មោះ :</span>
                    <span class="font-bold text-[#154689] text-[12.5px] sm:text-[14px] truncate">{{ profile.student.father_name || 'គ្មានទិន្នន័យ' }}</span>
                  </div>
                  <div class="flex items-center gap-1 sm:gap-1.5 shrink-0 pr-0.5">
                    <span class="font-bold text-black shrink-0 whitespace-nowrap">ម្ដាយឈ្មោះ :</span>
                    <span class="font-bold text-[#154689] text-[12.5px] sm:text-[14px] truncate">{{ profile.student.mother_name || 'គ្មានទិន្នន័យ' }}</span>
                  </div>
                </div>
              </div>

              <!-- Bottom Section (Left: Photo Container | Right: School Affiliation, Dates, Principal & Stamp) -->
              <div class="relative z-10 mt-3 sm:mt-3.5 flex items-end justify-between gap-2 sm:gap-3">
                <!-- Left Side: Rectangular Photo Container with thin 1px black border -->
                <div class="border border-black bg-white shrink-0 w-[102px] sm:w-[112px] shadow-2xs">
                  <!-- Top Header Bar with ID in blue separated by 1px solid black line -->
                  <div class="border-b border-black bg-white text-center py-0.5 sm:py-1 px-0.5 text-[8.5px] sm:text-[9.5px] font-mono font-bold text-[#154689] tracking-tight whitespace-nowrap overflow-hidden">
                    ID: {{ profile.student.student_id_card || '0011001' }}
                  </div>
                  <!-- Photo Area: Empty clean framed box with subtle 3x4 indicator -->
                  <div class="w-full h-32 sm:h-36 bg-[#f4f7fb] flex items-center justify-center text-[11px] text-slate-400 font-sans tracking-widest font-semibold select-none">
                    3 x 4
                  </div>
                </div>

                <!-- Right Side: Affiliation, Dates, Director, Stamp & Signature -->
                <div class="flex-1 flex flex-col items-center text-center text-black pb-0.5 min-w-0">
                  <!-- Top Line: 'ជាសិស្ស' (black) followed by School Name in green Moul font (#008000) -->
                  <div class="text-center mb-1 w-full flex items-center justify-center gap-1 sm:gap-1.5 whitespace-nowrap">
                    <span class="font-bold text-black text-[11px] sm:text-[12.5px] shrink-0 whitespace-nowrap">ជាសិស្ស</span>
                    <span class="font-moul text-[#008000] text-[10.5px] sm:text-[12.5px] tracking-tight shrink-0 whitespace-nowrap">សាលារៀន ជំនាន់ថ្មី PDT</span>
                  </div>

                  <!-- Lunar Date & Gregorian Date in clear text -->
                  <p class="text-[8.5px] sm:text-[9.5px] leading-tight text-slate-900 font-battambang whitespace-nowrap">
                    ថ្ងៃសុក្រ ១០រោច ខែចេត្រ ឆ្នាំរោង ឆស័ក ព.ស.២៥៦៨
                  </p>
                  <p class="font-bold text-[9px] sm:text-[10.5px] leading-tight text-slate-900 mt-0.5 font-battambang whitespace-nowrap">
                    រាជធានីភ្នំពេញ ថ្ងៃទី {{ toKhmerNum(currentDay) }} ខែ {{ currentMonthName }} ឆ្នាំ {{ toKhmerNum(currentYear) }}
                  </p>

                  <!-- Director Title in Moul font -->
                  <p class="font-moul text-[11px] sm:text-[12.5px] text-slate-900 mt-0.5 sm:mt-1 whitespace-nowrap">
                    នាយកសាលា
                  </p>

                  <!-- Circular Red School Stamp and Blue Signature (Authentic Official Circular Seal) -->
                  <div class="relative w-26 h-17 sm:w-30 sm:h-20 my-0.5 flex items-center justify-center">
                    <!-- Red Official Circular Stamp -->
                    <svg class="absolute inset-0 w-22 h-22 sm:w-26 sm:h-26 text-red-600/90 pointer-events-none transform -rotate-6 mx-auto -top-2.5 sm:-top-3" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" r="46" fill="none" stroke="currentColor" stroke-width="1.8" />
                      <circle cx="50" cy="50" r="42" fill="none" stroke="currentColor" stroke-width="1" />
                      <path id="curve-top-modal" d="M 16,50 A 34,34 0 0,1 84,50" fill="none" />
                      <path id="curve-bottom-modal" d="M 84,50 A 34,34 0 0,1 16,50" fill="none" />
                      <text font-size="5.8" font-family="'Battambang', sans-serif" font-weight="bold" fill="currentColor">
                        <textPath href="#curve-top-modal" startOffset="50%" text-anchor="middle">
                          ★ សាលារៀន ជំនាន់ថ្មី PDT ★
                        </textPath>
                      </text>
                      <text font-size="5.2" font-family="'Battambang', sans-serif" font-weight="bold" fill="currentColor">
                        <textPath href="#curve-bottom-modal" startOffset="50%" text-anchor="middle">
                          ★ ក្រសួងអប់រំ យុវជន និងកីឡា ★
                        </textPath>
                      </text>
                      <circle cx="50" cy="50" r="21" fill="none" stroke="currentColor" stroke-width="0.8" />
                      <!-- Center Seal Emblem -->
                      <polygon points="50,35 53,43 61,43 55,49 57,57 50,52 43,57 45,49 39,43 47,43" fill="currentColor" opacity="0.85" />
                    </svg>
                    
                    <!-- Blue Ink Cursive Signature (smooth realistic pen stroke) -->
                    <svg class="absolute inset-0 w-24 h-15 sm:w-28 sm:h-18 text-[#002b80] pointer-events-none transform rotate-2 mx-auto -top-1" viewBox="0 0 100 60">
                      <path d="M 16,36 C 24,18 34,44 42,26 C 48,16 54,36 62,22 C 70,16 80,32 86,20 M 22,33 L 84,29" fill="none" stroke="#002b80" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </div>

                  <!-- Director's Name in Red Moul font #d92626 -->
                  <p class="font-moul text-[11px] sm:text-[12.5px] text-[#d92626] font-bold tracking-tight mt-0.5 whitespace-nowrap">
                    សុខ ប៊ុនហុង
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
          <button 
            @click="printCard" 
            class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer shadow-xs"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>បោះពុម្ព (Print)</span>
          </button>
          <button 
            @click="saveCard" 
            :disabled="isSavingCard"
            class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer shadow-xs"
          >
            <svg v-if="isSavingCard" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            <span>{{ isSavingCard ? 'កំពុងរក្សាទុក...' : 'រក្សាទុក (Save)' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';
import { useToast } from '../composables/useToast';

const { t } = useI18n();
const { success, error, info } = useToast();

const loading = ref(true);
const errorMsg = ref('');
const profile = ref(null);
const showIdCardModal = ref(false);

const isStudent = computed(() => {
  const role = (profile.value?.user?.role || '').toLowerCase();
  return role.includes('student') || Boolean(profile.value?.student);
});

const isTeacher = computed(() => {
  const role = (profile.value?.user?.role || '').toLowerCase();
  return role.includes('teacher') || Boolean(profile.value?.teacher);
});

const displayName = computed(() => {
  if (profile.value?.student?.name_khmer) return profile.value.student.name_khmer;
  if (profile.value?.student?.name_latin) return profile.value.student.name_latin;
  if (profile.value?.teacher?.name_khmer) return profile.value.teacher.name_khmer;
  if (profile.value?.teacher?.name_latin) return profile.value.teacher.name_latin;
  return profile.value?.user?.name || 'អ្នកប្រើប្រាស់';
});

const displayAvatar = computed(() => {
  return profile.value?.student?.photo || 
         profile.value?.teacher?.photo || 
         profile.value?.user?.avatar || 
         'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&h=200&fit=crop&crop=faces';
});

const displayPhone = computed(() => {
  return profile.value?.student?.phone || 
         profile.value?.teacher?.phone || 
         profile.value?.user?.phone || 
         '';
});

const displayAddress = computed(() => {
  if (profile.value?.student?.address) return profile.value.student.address;
  if (profile.value?.teacher?.address) return profile.value.teacher.address;
  const p = profile.value?.student;
  if (p && (p.village || p.commune || p.district || p.province)) {
    return [p.village, p.commune, p.district, p.province].filter(Boolean).join(' ');
  }
  return '';
});


const avatarFileInput = ref(null);
const uploadingAvatar = ref(false);

const triggerAvatarUpload = () => {
  if (avatarFileInput.value) {
    avatarFileInput.value.click();
  }
};

const onAvatarFileChange = async (event) => {
  const file = event.target?.files?.[0];
  if (!file) return;

  if (file.size > 10 * 1024 * 1024) {
    error('ទំហំរូបភាពធំពេក! សូមជ្រើសរើសរូបភាពក្រោម 10MB');
    return;
  }

  uploadingAvatar.value = true;
  try {
    const formData = new FormData();
    formData.append('avatar', file);

    const res = await axios.post('/api/user/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    if (res.data?.success) {
      success(res.data.message || 'បានប្តូររូបភាព Profile ដោយជោគជ័យ!');
      if (res.data.avatar) {
        if (!profile.value.user) profile.value.user = {};
        profile.value.user.avatar = res.data.avatar;
        if (profile.value.student) profile.value.student.photo = res.data.avatar;
        if (profile.value.teacher) profile.value.teacher.photo = res.data.avatar;

        // Broadcast to navbar and dashboard
        window.dispatchEvent(new CustomEvent('pdt-user-avatar-updated', {
          detail: { avatar: res.data.avatar }
        }));
      }
    }
  } catch (err) {
    console.error('Avatar upload failed:', err);
    error(err.response?.data?.message || 'មិនអាចប្តូររូបភាព Profile បានទេ');
  } finally {
    uploadingAvatar.value = false;
    if (event.target) event.target.value = '';
  }
};

const onAvatarError = (e) => {
  e.target.src = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&h=200&fit=crop&crop=faces';
};

const toKhmerNum = (num) => {
  if (num === null || num === undefined) return '';
  const khmerDigits = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
  return String(num).replace(/[0-9]/g, d => khmerDigits[d]);
};

const toKhmerDate = (dateStr) => {
  if (!dateStr) return '១២ ឧសភា ២០០៨';
  try {
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    const day = toKhmerNum(String(d.getDate()).padStart(2, '0'));
    const months = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
    const month = months[d.getMonth()];
    const year = toKhmerNum(d.getFullYear());
    return `${day} ${month} ${year}`;
  } catch {
    return dateStr;
  }
};

const studentPlaceOfBirth = computed(() => {
  const p = profile.value?.student;
  if (!p) return 'សំរោងពូន កោះធំ កណ្ដាល';
  if (p.address && p.address !== '-') return p.address;
  const parts = [p.village, p.commune, p.district, p.province].filter(Boolean);
  if (parts.length > 0) return parts.join(' ');
  return 'សំរោងពូន កោះធំ កណ្ដាល';
});

const formatAcademicYear = (year) => {
  if (!year) return '២០២៥ ~ ២០២៦';
  return toKhmerNum(year).replace(/[-–]/g, ' ~ ');
};

const today = new Date();
const currentDay = today.getDate();
const currentMonthName = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'][today.getMonth()];
const currentYear = today.getFullYear();

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  try {
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    const day = String(d.getDate()).padStart(2, '0');
    const months = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
    const month = months[d.getMonth()];
    const year = d.getFullYear();
    return `${day} ${month} ${year}`;
  } catch {
    return dateStr;
  }
};

const formatGender = (gender) => {
  if (!gender) return '-';
  const g = String(gender).toLowerCase();
  if (g === 'male' || g === 'm' || g === 'ប្រុស') return 'ប្រុស';
  if (g === 'female' || g === 'f' || g === 'ស្រី') return 'ស្រី';
  return gender;
};

const formatStatus = (status) => {
  if (!status) return 'សកម្ម';
  const s = String(status).toLowerCase();
  if (s === 'enrolled' || s === 'active' || s === 'កំពុងសិក្សា') return 'មានសុពលភាព';
  if (s === 'graduated') return 'បានបញ្ចប់ការសិក្សា';
  if (s === 'suspended') return 'ផ្អាកការសិក្សា';
  return status;
};

const copyText = async (text, label) => {
  if (!text) return;
  try {
    await navigator.clipboard.writeText(text);
    success(`បានចម្លង${label}៖ ${text}`);
  } catch {
    info(`អត្តលេខ៖ ${text}`);
  }
};

const isSavingCard = ref(false);

const printCard = () => {
  window.print();
};

const saveCard = async () => {
  if (isSavingCard.value) return;
  isSavingCard.value = true;
  
  try {
    if (document.fonts?.ready) {
      await document.fonts.ready;
    }

    const scale = 2;
    const width = 390;
    const height = 528;

    const canvas = document.createElement('canvas');
    canvas.width = width * scale;
    canvas.height = height * scale;
    const ctx = canvas.getContext('2d');
    ctx.scale(scale, scale);

    // 1. Crisp White Background
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, width, height);

    // 2. Outer Border (3px solid #1f4e79)
    ctx.strokeStyle = '#1f4e79';
    ctx.lineWidth = 3;
    ctx.strokeRect(1.5, 1.5, width - 3, height - 3);

    // 3. Header bottom border line (edge-to-edge)
    ctx.beginPath();
    ctx.moveTo(0, 98);
    ctx.lineTo(width, 98);
    ctx.stroke();

    // 4. Load MoEYS Logo
    const loadImg = (src) => new Promise((resolve) => {
      const img = new Image();
      img.crossOrigin = 'anonymous';
      img.onload = () => resolve(img);
      img.onerror = () => resolve(null);
      img.src = src;
    });

    const logo = await loadImg('/images/moeys-logo.png');
    if (logo) {
      // Top-left logo in header
      ctx.drawImage(logo, 16, 14, 56, 56);

      // Center background watermark in body
      ctx.save();
      ctx.globalAlpha = 0.08;
      ctx.drawImage(logo, (width - 230) / 2, 160, 230, 230);
      ctx.restore();
    }

    // 5. Top Header Text
    ctx.fillStyle = '#000000';
    ctx.textAlign = 'center';
    ctx.font = "bold 13.5px 'Moul', cursive";
    ctx.fillText('ព្រះរាជាណាចក្រកម្ពុជា', 234, 32);

    ctx.font = "bold 11px 'Moul', cursive";
    ctx.fillText('ជាតិ សាសនា ព្រះមហាក្សត្រ', 234, 52);

    // Decorative flourish & line
    ctx.strokeStyle = '#1f4e79';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(170, 65);
    ctx.lineTo(202, 65);
    ctx.moveTo(266, 65);
    ctx.lineTo(298, 65);
    ctx.stroke();

    ctx.font = "10px 'Battambang', sans-serif";
    ctx.fillStyle = '#1f4e79';
    ctx.fillText('༺ ៚ ༻', 234, 68);

    ctx.font = "bold 11.5px 'Moul', cursive";
    ctx.fillText('មន្ទីរអប់រំ យុវជន និង កីឡា រាជធានីភ្នំពេញ', 195, 90);

    // 6. Title Block
    ctx.textAlign = 'center';
    ctx.font = "bold 23px 'Moul', cursive";
    ctx.strokeStyle = '#1f4e79';
    ctx.lineWidth = 1;
    ctx.strokeText('ប័ណ្ណសម្គាល់ខ្លួនសិស្ស', 195, 136);
    ctx.fillStyle = '#5b9bd5';
    ctx.fillText('ប័ណ្ណសម្គាល់ខ្លួនសិស្ស', 195, 136);

    ctx.fillStyle = '#000000';
    ctx.font = "bold 12px 'Battambang', sans-serif";
    ctx.fillText('ឆ្នាំសិក្សា ' + formatAcademicYear(profile.value?.student?.academic_year), 195, 159);

    ctx.font = "bold 12.5px 'Moul', cursive";
    ctx.fillText('ថ្នាក់ទី ' + toKhmerNum(profile.value?.student?.grade || '៧A'), 195, 180);

    // 7. Student Data (Two-column layout matching DOM)
    const sName = profile.value?.student?.name_khmer || displayName.value || 'ឌុច សែត';
    const sGender = formatGender(profile.value?.student?.gender);
    const sDob = toKhmerDate(profile.value?.student?.date_of_birth);
    const sPob = studentPlaceOfBirth.value;
    const sFather = profile.value?.student?.father_name || 'គ្មានទិន្នន័យ';
    const sMother = profile.value?.student?.mother_name || 'គ្មានទិន្នន័យ';
    const sId = profile.value?.student?.student_id_card || '0011001';

    // Line 1: Name & Gender
    ctx.textAlign = 'left';
    ctx.font = "bold 13.5px 'Battambang', sans-serif";
    ctx.fillStyle = '#000000';
    ctx.fillText('គោត្តនាម នាម :', 22, 214);
    ctx.font = "bold 14px 'Battambang', sans-serif";
    ctx.fillStyle = '#154689';
    ctx.fillText(sName, 122, 214);

    ctx.textAlign = 'right';
    ctx.font = "bold 14px 'Battambang', sans-serif";
    ctx.fillStyle = '#154689';
    ctx.fillText(sGender, 368, 214);
    const gWidth = ctx.measureText(sGender).width;
    ctx.font = "bold 13.5px 'Battambang', sans-serif";
    ctx.fillStyle = '#000000';
    ctx.fillText('ភេទ :', 368 - gWidth - 6, 214);

    // Line 2: DOB
    ctx.textAlign = 'left';
    ctx.font = "bold 13.5px 'Battambang', sans-serif";
    ctx.fillStyle = '#000000';
    ctx.fillText('ថ្ងៃខែឆ្នាំកំណើត :', 22, 242);
    ctx.font = "bold 14px 'Battambang', sans-serif";
    ctx.fillStyle = '#154689';
    ctx.fillText(sDob, 134, 242);

    // Line 3: POB
    ctx.font = "bold 13.5px 'Battambang', sans-serif";
    ctx.fillStyle = '#000000';
    ctx.fillText('ទីកន្លែងកំណើត :', 22, 270);
    ctx.font = "bold 14px 'Battambang', sans-serif";
    ctx.fillStyle = '#154689';
    ctx.fillText(sPob, 132, 270);

    // Line 4: Father & Mother
    ctx.font = "bold 13.5px 'Battambang', sans-serif";
    ctx.fillStyle = '#000000';
    ctx.fillText('ឪពុកឈ្មោះ :', 22, 298);
    ctx.font = "bold 14px 'Battambang', sans-serif";
    ctx.fillStyle = '#154689';
    ctx.fillText(sFather, 112, 298);

    ctx.textAlign = 'right';
    ctx.font = "bold 14px 'Battambang', sans-serif";
    ctx.fillStyle = '#154689';
    ctx.fillText(sMother, 368, 298);
    const mWidth = ctx.measureText(sMother).width;
    ctx.font = "bold 13.5px 'Battambang', sans-serif";
    ctx.fillStyle = '#000000';
    ctx.fillText('ម្ដាយឈ្មោះ :', 368 - mWidth - 6, 298);

    // 8. Photo Box (Left)
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 1;
    ctx.strokeRect(22, 334, 110, 164);

    // Photo ID Header Bar
    ctx.beginPath();
    ctx.moveTo(22, 358);
    ctx.lineTo(132, 358);
    ctx.stroke();

    ctx.font = "bold 9.5px monospace";
    ctx.fillStyle = '#154689';
    ctx.textAlign = 'center';
    ctx.fillText('ID: ' + sId, 77, 351);

    // Photo Area
    ctx.fillStyle = '#f4f7fb';
    ctx.fillRect(23, 359, 108, 138);
    ctx.font = "600 11px sans-serif";
    ctx.fillStyle = '#94a3b8';
    ctx.fillText('3 x 4', 77, 434);

    // 9. Right Section
    const rightCenterX = 258;
    // 'ជាសិស្ស' + 'សាលារៀន ជំនាន់ថ្មី PDT'
    ctx.font = "bold 12px 'Battambang', sans-serif";
    const wAsStudent = ctx.measureText('ជាសិស្ស ').width;
    ctx.font = "bold 11px 'Moul', cursive";
    const wSchool = ctx.measureText('សាលារៀន ជំនាន់ថ្មី PDT').width;
    const startSchoolX = rightCenterX - (wAsStudent + wSchool) / 2;

    ctx.textAlign = 'left';
    ctx.font = "bold 12px 'Battambang', sans-serif";
    ctx.fillStyle = '#000000';
    ctx.fillText('ជាសិស្ស ', startSchoolX, 352);

    ctx.font = "bold 11px 'Moul', cursive";
    ctx.fillStyle = '#008000';
    ctx.fillText('សាលារៀន ជំនាន់ថ្មី PDT', startSchoolX + wAsStudent, 352);

    // Dates
    ctx.textAlign = 'center';
    ctx.font = "9px 'Battambang', sans-serif";
    ctx.fillStyle = '#0f172a';
    ctx.fillText('ថ្ងៃសុក្រ ១០រោច ខែចេត្រ ឆ្នាំរោង ឆស័ក ព.ស.២៥៦៨', rightCenterX, 370);

    ctx.font = "bold 9.5px 'Battambang', sans-serif";
    ctx.fillText(`រាជធានីភ្នំពេញ ថ្ងៃទី ${toKhmerNum(currentDay)} ខែ ${currentMonthName} ឆ្នាំ ${toKhmerNum(currentYear)}`, rightCenterX, 387);

    // Director Title
    ctx.font = "bold 12px 'Moul', cursive";
    ctx.fillStyle = '#0f172a';
    ctx.fillText('នាយកសាលា', rightCenterX, 407);

    // Stamp & Signature SVG
    const stampSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
      <circle cx="50" cy="50" r="46" fill="none" stroke="#dc2626" stroke-width="1.8" />
      <circle cx="50" cy="50" r="42" fill="none" stroke="#dc2626" stroke-width="1" />
      <path id="ct" d="M 16,50 A 34,34 0 0,1 84,50" fill="none" />
      <path id="cb" d="M 84,50 A 34,34 0 0,1 16,50" fill="none" />
      <text font-size="5.8" font-family="'Battambang', sans-serif" font-weight="bold" fill="#dc2626">
        <textPath href="#ct" startOffset="50%" text-anchor="middle">★ សាលារៀន ជំនាន់ថ្មី PDT ★</textPath>
      </text>
      <text font-size="5.2" font-family="'Battambang', sans-serif" font-weight="bold" fill="#dc2626">
        <textPath href="#cb" startOffset="50%" text-anchor="middle">★ ក្រសួងអប់រំ យុវជន និងកីឡា ★</textPath>
      </text>
      <circle cx="50" cy="50" r="21" fill="none" stroke="#dc2626" stroke-width="0.8" />
      <polygon points="50,35 53,43 61,43 55,49 57,57 50,52 43,57 45,49 39,43 47,43" fill="#dc2626" opacity="0.85" />
    </svg>`;

    const sigSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="100" height="60" viewBox="0 0 100 60">
      <path d="M 16,36 C 24,18 34,44 42,26 C 48,16 54,36 62,22 C 70,16 80,32 86,20 M 22,33 L 84,29" fill="none" stroke="#002b80" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>`;

    const loadSvg = (svgStr) => new Promise((resolve) => {
      const blob = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
      const u = URL.createObjectURL(blob);
      const i = new Image();
      i.onload = () => { URL.revokeObjectURL(u); resolve(i); };
      i.onerror = () => { URL.revokeObjectURL(u); resolve(null); };
      i.src = u;
    });

    const [stampImg, sigImg] = await Promise.all([loadSvg(stampSvg), loadSvg(sigSvg)]);
    if (stampImg) {
      ctx.save();
      ctx.translate(rightCenterX, 444);
      ctx.rotate(-6 * Math.PI / 180);
      ctx.drawImage(stampImg, -46, -46, 92, 92);
      ctx.restore();
    }
    if (sigImg) {
      ctx.save();
      ctx.translate(rightCenterX, 442);
      ctx.rotate(2 * Math.PI / 180);
      ctx.drawImage(sigImg, -48, -28, 96, 56);
      ctx.restore();
    }

    // Director Name
    ctx.textAlign = 'center';
    ctx.font = "bold 12px 'Moul', cursive";
    ctx.fillStyle = '#d92626';
    ctx.fillText('សុខ ប៊ុនហុង', rightCenterX, 494);

    // 10. Direct Auto Download to Device as PNG
    canvas.toBlob((blob) => {
      if (!blob) {
        window.print();
        return;
      }
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `Student_ID_Card_${sId}.png`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      setTimeout(() => URL.revokeObjectURL(url), 1000);
      success('បានទាញយកកាតសិស្សចូលក្នុងម៉ាស៊ីន (Auto Downloaded to Device) ដោយជោគជ័យ!');
    }, 'image/png');
  } catch (err) {
    console.error('Failed to auto-download card image, falling back to print:', err);
    window.print();
  } finally {
    isSavingCard.value = false;
  }
};

const fetchProfile = async () => {
  loading.value = true;
  errorMsg.value = '';
  try {
    const res = await axios.get('/api/user/profile');
    if (res.data) {
      profile.value = res.data;
    }
  } catch (err) {
    console.error('Failed to load profile:', err);
    errorMsg.value = err.response?.data?.message || 'មិនអាចទាញយកព័ត៌មានផ្ទាល់ខ្លួនបានទេ';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchProfile();
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Battambang:wght@400;700;900&family=Moul&display=swap');

.font-moul {
  font-family: 'Moul', cursive, serif;
}

.font-battambang {
  font-family: 'Battambang', sans-serif;
}

.card-main-title {
  font-family: 'Moul', cursive, serif;
  color: #1a56db;
  -webkit-text-stroke: 0.8px #0f3460;
  text-shadow: 0 1px 2px rgba(15, 52, 96, 0.25);
  letter-spacing: 0.5px;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.97); }
  to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
  animation: fadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@media print {
  body * {
    visibility: hidden;
  }
  #student-id-card-element, #student-id-card-element * {
    visibility: visible;
  }
  #student-id-card-element {
    position: fixed;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 360px !important;
    max-width: 360px !important;
    box-shadow: none !important;
    border: 3px solid #1f4e79 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
}
</style>
