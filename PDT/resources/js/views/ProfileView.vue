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
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 animate-fadeIn"
      @click.self="showIdCardModal = false"
    >
      <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-100">
        <!-- Modal Header -->
        <div class="px-5 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
            ប័ណ្ណសម្គាល់ខ្លួនសិស្ស (Digital ID Card)
          </h3>
          <button @click="showIdCardModal = false" class="text-slate-400 hover:text-slate-600 text-sm p-1 rounded-lg">✕</button>
        </div>

        <!-- ID Card Body (MoEYS Official Style) -->
        <div class="p-5">
          <div id="student-id-card-element" class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-blue-900 via-indigo-900 to-blue-950 text-white p-4 sm:p-5 shadow-lg border border-blue-400/20 space-y-3.5">
            <!-- Header with logo and school title -->
            <div class="flex items-center gap-3 border-b border-white/10 pb-2.5">
              <img :src="'/images/logo.png'" alt="MoEYS" class="w-10 h-10 object-contain rounded-full bg-white p-1" />
              <div class="flex-1">
                <h4 class="text-xs font-black tracking-tight text-amber-300">សាលារៀន ជំនាន់ថ្មី</h4>
                <p class="text-[9px] text-blue-200">NEW GENERATION SCHOOL</p>
                <span class="inline-block mt-0.5 text-[8px] font-bold px-1.5 py-0.2 bg-white/20 rounded text-white tracking-widest uppercase">
                  STUDENT ID CARD
                </span>
              </div>
            </div>

            <!-- Content: Photo & Details -->
            <div class="flex items-start gap-3.5">
              <div class="w-18 h-22 rounded-xl overflow-hidden bg-white/10 border-2 border-white/30 shrink-0">
                <img 
                  :src="displayAvatar" 
                  alt="Student" 
                  class="w-full h-full object-cover"
                  @error="onAvatarError"
                />
              </div>

              <div class="flex-1 space-y-1 text-xs">
                <div>
                  <span class="text-[9px] text-blue-300 block">ឈ្មោះសិស្ស / Name</span>
                  <p class="font-bold text-white text-sm leading-tight">{{ profile.student.name_khmer || displayName }}</p>
                  <p class="text-[10px] text-blue-200 font-sans">{{ profile.student.name_latin }}</p>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-1 border-t border-white/10 text-[10px]">
                  <div>
                    <span class="text-blue-300 block text-[8px]">អត្តលេខ / ID</span>
                    <strong class="font-mono text-amber-300">{{ profile.student.student_id_card }}</strong>
                  </div>
                  <div>
                    <span class="text-blue-300 block text-[8px]">ថ្នាក់ / Grade</span>
                    <strong>{{ profile.student.grade }}</strong>
                  </div>
                  <div>
                    <span class="text-blue-300 block text-[8px]">ភេទ / Gender</span>
                    <span>{{ formatGender(profile.student.gender) }}</span>
                  </div>
                  <div>
                    <span class="text-blue-300 block text-[8px]">ឆ្នាំសិក្សា / Year</span>
                    <span>2026-2027</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer: Barcode stripe & disclaimer -->
            <div class="pt-2 border-t border-white/10 flex items-center justify-between text-[9px] text-blue-300">
              <span>MoEYS Digital Education</span>
              <span class="font-mono text-[8px] tracking-widest text-slate-300">||| | |||| | ||||| ||</span>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
          <button 
            @click="printCard" 
            class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>បោះពុម្ព (Print)</span>
          </button>
          <button 
            @click="showIdCardModal = false" 
            class="px-3.5 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer"
          >
            បិទ
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

const printCard = () => {
  window.print();
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
    width: 380px !important;
  }
}
</style>
