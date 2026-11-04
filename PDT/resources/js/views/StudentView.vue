<template>
  <div class="space-y-6 font-khmer">
    <!-- Header with Extra Actions Slot for Link / QR Code -->
    <PageHeader 
      :title="t('students.title')" 
      :breadcrumb="t('nav.hostel')" 
      :actionLabel="t('students.addStudent')"
      @action="openCreateModal"
    >
      <template #extraActions>
        <button 
          @click="openBulkScanModal" 
          type="button"
          class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full px-4 py-2 text-xs font-semibold shadow-md shadow-indigo-600/20 transition-all hover:shadow-lg hover:shadow-indigo-600/30 active:scale-95 cursor-pointer"
        >
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <span>📄 ស្កេនបញ្ជីឈ្មោះសិស្ស</span>
        </button>

        <button 
          @click="openQrModal" 
          type="button"
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-full shadow-md shadow-emerald-600/20 transition-all hover:shadow-lg hover:shadow-emerald-600/30 active:scale-95"
        >
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
          </svg>
          <span>🔗 Link / QR ចុះឈ្មោះសិស្ស</span>
        </button>
      </template>
    </PageHeader>

    <!-- Sub Tabs including Pending Status with Badge -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'all', label: t('common.all') },
        { id: 'pending', label: 'រង់ចាំអនុម័ត', badge: pendingCount, badgeColor: 'bg-amber-100 text-amber-800' },
        { id: 'active', label: t('students.enrolled') },
        { id: 'graduated', label: t('students.graduated') },
        { id: 'transferred', label: t('students.transferred') }
      ]"
    />

    <!-- Entity Banner -->
    <EntityBanner 
      name="បញ្ជីគ្រប់គ្រងសិស្សានុសិស្សទូទាំងសាលា (Students Directory)"
      :subtitle="`${t('students.studentList')} | ${t('students.totalStudents')}: ${students.length}`"
      metaId="ADM-STU-DIR"
      @print="handlePrint"
      @download="handleDownload"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="t('students.totalStudents')" :value="students.length" />
      <StatCard :title="t('students.activeStudents')" :value="activeCount" />
      <StatCard title="រង់ចាំអនុម័ត" :value="pendingCount" />
      <StatCard :title="t('students.graduated')" :value="graduatedCount" />
    </div>

    <!-- Filter Bar -->
    <FilterBar>
      <template #left>
        <div class="flex items-center gap-2">
          <input 
            v-model="searchQuery"
            type="text" 
            :placeholder="t('common.searchPlaceholder')" 
            class="px-3.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs w-64 focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-khmer"
          />
        </div>
      </template>

      <template #right>
        <div class="inline-flex items-center gap-3 px-3.5 py-1.5 bg-slate-50 border border-slate-200/80 rounded-xl">
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
            <input type="radio" v-model="genderFilter" value="all" class="text-blue-600 focus:ring-0">
            <span>{{ t('common.all') }}</span>
          </label>
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
            <input type="radio" v-model="genderFilter" value="male" class="text-blue-600 focus:ring-0">
            <span>{{ t('students.male') }}</span>
          </label>
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
            <input type="radio" v-model="genderFilter" value="female" class="text-blue-600 focus:ring-0">
            <span>{{ t('students.female') }}</span>
          </label>
        </div>

        <!-- Class Filter: Defaults to "" (All Classes) -->
        <select 
          v-model="gradeFilter" 
          class="px-3.5 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-medium focus:outline-none shadow-2xs font-khmer"
        >
          <option value="">{{ t('common.all') }} {{ t('classes.className') }}</option>
          <option v-for="cl in availableClasses" :key="cl" :value="cl">
            ថ្នាក់ {{ cl }}
          </option>
        </select>
      </template>
    </FilterBar>

    <!-- Data Table -->
    <DataTable>
      <template #header>
        <tr>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('students.studentId') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('students.studentName') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('students.gender') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('students.class') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('students.phone') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ t('common.status') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ t('common.actions') }}</th>
        </tr>
      </template>

      <template #body>
        <tr v-if="filteredStudents.length === 0">
          <td colspan="7" class="py-10 text-center text-slate-400 font-khmer">{{ t('common.noData') }}</td>
        </tr>
        <tr v-for="st in filteredStudents" :key="st.id" class="hover:bg-slate-50/70 transition-colors">
          <td class="py-3 px-4 font-mono font-bold text-blue-600">
            {{ st.student_id_card }}
          </td>
          <td class="py-3 px-4 font-semibold text-slate-800">
            <div class="flex items-center gap-2.5">
              <div v-if="st.photo" class="w-8 h-8 rounded-full overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0">
                <img :src="st.photo" alt="" class="w-full h-full object-cover">
              </div>
              <div v-else class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                {{ st.name ? st.name.charAt(0) : 'S' }}
              </div>
              <div>
                <div>{{ st.name }}</div>
                <div v-if="st.name_latin" class="text-[11px] text-slate-400 font-mono uppercase">{{ st.name_latin }}</div>
              </div>
            </div>
          </td>
          <td class="py-3 px-4 text-slate-600">
            {{ st.gender === 'male' || st.gender === 'ប្រុស' ? t('students.male') : t('students.female') }}
          </td>
          <td class="py-3 px-4">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
              {{ st.grade || 'មិនទាន់ចាត់តាំង' }}
            </span>
          </td>
          <td class="py-3 px-4 font-mono text-slate-600">
            {{ st.phone || '-' }}
          </td>
          <td class="py-3 px-4 text-center">
            <!-- Pending Status Badge -->
            <span 
              v-if="st.status === 'pending'"
              class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 inline-flex items-center gap-1 animate-pulse border border-amber-300"
            >
              <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
              រង់ចាំអនុម័ត
            </span>
            <!-- Active / Enrolled Status Badge -->
            <span 
              v-else-if="st.status === 'active' || st.status === 'enrolled'"
              class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700"
            >
              {{ t('students.enrolled') }}
            </span>
            <!-- Other Status Badges -->
            <span 
              v-else
              class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700"
            >
              {{ st.status }}
            </span>
          </td>
          <td class="py-3 px-4 text-right space-x-1.5">
            <!-- Action buttons for Pending Students -->
            <template v-if="st.status === 'pending'">
              <button 
                @click="openApproveModal(st)"
                class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs transition inline-flex items-center gap-1"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>អនុម័ត (Approve)</span>
              </button>
            </template>
            <!-- Normal Active Students Action buttons -->
            <template v-else>
              <button @click="openProfileModal(st)" class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs hover:bg-slate-200 transition">
                {{ t('attendance.tabs.profile') }}
              </button>
              <button @click="openEditModal(st)" class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs hover:bg-blue-100 transition font-semibold">
                {{ t('common.edit') }}
              </button>
              <button @click="confirmDeleteStudent(st)" class="px-2 py-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg text-xs transition" title="លុប">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </template>
          </td>
        </tr>
      </template>
    </DataTable>

    <!-- Modal 1: Shareable Link & QR Code Modal -->
    <div 
      v-if="showQrModal" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs font-khmer transition-opacity"
      @click.self="showQrModal = false"
    >
      <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
              </svg>
            </div>
            <div>
              <h3 class="font-bold text-base">Link / QR Code ចុះឈ្មោះសិស្សថ្មី</h3>
              <p class="text-xs text-blue-100">សម្រាប់ចែករំលែកទៅកាន់មាតាបិតា និងសិស្ស</p>
            </div>
          </div>
          <button 
            @click="showQrModal = false" 
            class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition"
          >
            ✕
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 text-center space-y-6">
          
          <!-- QR Code Display Box -->
          <div class="p-6 bg-slate-50 border border-slate-200/80 rounded-2xl inline-block mx-auto shadow-inner text-center">
            <div class="text-xs font-bold text-slate-800 mb-2 uppercase tracking-wide">
              LMSTICK SCHOOL - ស្កេនដើម្បីចុះឈ្មោះ
            </div>
            <div class="w-48 h-48 mx-auto bg-white p-2 rounded-xl border border-slate-200 shadow-xs flex items-center justify-center">
              <img v-if="qrDataUrl" :src="qrDataUrl" alt="QR Code Registration" class="w-full h-full object-contain" />
              <div v-else class="text-slate-400 text-xs">កំពុងបង្កើត QR Code...</div>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">ប្រើទូរស័ព្ទដៃដើម្បីស្កេនបំពេញទម្រង់ចុះឈ្មោះ</p>
          </div>

          <!-- Shareable URL Input with Copy Button -->
          <div class="text-left space-y-1.5">
            <label class="text-xs font-semibold text-slate-700">តំណភ្ជាប់ទម្រង់ចុះឈ្មោះ (Registration Link):</label>
            <div class="flex items-center gap-2">
              <input 
                type="text" 
                readonly 
                :value="publicRegistrationUrl" 
                class="flex-1 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-blue-700 select-all focus:outline-none"
              />
              <button 
                @click="copyLink"
                type="button"
                :class="[
                  'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5',
                  isCopied 
                    ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' 
                    : 'bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-600/20'
                ]"
              >
                <svg v-if="!isCopied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10a2 2 0 00-2 2v3a2 2 0 002 2h8a2 2 0 002-2v-3a2 2 0 00-2-2z"/>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ isCopied ? 'បានចម្លង!' : 'ចម្លង Link' }}</span>
              </button>
            </div>
            <p class="text-[11px] text-slate-400">អ្នកអាចផ្ញើតំណភ្ជាប់នេះតាមរយៈ Telegram, Messenger ឬ Facebook។</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
            <button 
              @click="printQrSheet" 
              type="button"
              class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
              </svg>
              <span>បោះពុម្ព QR (Print QR)</span>
            </button>
            <button 
              @click="showQrModal = false" 
              type="button"
              class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition"
            >
              បិទផ្ទាំង
            </button>
          </div>

        </div>

      </div>
    </div>

    <!-- Modal 2: Admin Quick Create Student Modal -->
    <div 
      v-if="showCreateModal" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs font-khmer transition-opacity"
      @click.self="showCreateModal = false"
    >
      <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-5 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
              <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
              <h3 class="font-bold text-base">បន្ថែមសិស្សថ្មី (Add New Student)</h3>
              <p class="text-xs text-blue-100">បញ្ចូលព័ត៌មានផ្ទាល់ និងបង្កើតគណនីស្វ័យប្រវត្តិ</p>
            </div>
          </div>
          <button @click="showCreateModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">✕</button>
        </div>

        <form @submit.prevent="submitCreateStudent" class="p-6 space-y-4 text-xs text-slate-700">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះពេញជាភាសាខ្មែរ <span class="text-red-500">*</span></label>
              <input type="text" v-model="createForm.name_khmer" required placeholder="ឧ. សុខ សុភក្ត្រ" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះជាអក្សរឡាតាំង (Latin Name)</label>
              <input type="text" v-model="createForm.name_latin" placeholder="e.g. SOK SOPHAK" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ភេទ <span class="text-red-500">*</span></label>
              <select v-model="createForm.gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="male">ប្រុស (Male)</option>
                <option value="female">ស្រី (Female)</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">ថ្នាក់រៀន <span class="text-red-500">*</span></label>
              <select v-model="createForm.grade" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-bold text-slate-800">
                <option v-for="cl in availableClasses" :key="cl" :value="cl">{{ cl }}</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">ថ្ងៃខែឆ្នាំកំណើត</label>
              <input type="date" v-model="createForm.date_of_birth" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">លេខទូរស័ព្ទសិស្ស</label>
              <input type="tel" v-model="createForm.phone" placeholder="012 111 222" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះអាណាព្យាបាល</label>
              <input type="text" v-model="createForm.parent_name" placeholder="ឧ. សុខ វិបុល" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">លេខទូរស័ព្ទអាណាព្យាបាល</label>
              <input type="tel" v-model="createForm.parent_phone" placeholder="012 345 678" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div class="sm:col-span-2">
              <label class="block font-semibold mb-1">អាសយដ្ឋានបច្ចុប្បន្ន</label>
              <input type="text" v-model="createForm.address" placeholder="ភូមិ/ឃុំ/ស្រុក/ខេត្ត..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <div class="p-5 bg-slate-50 border-t border-slate-200 -mx-6 -mb-6 mt-4 flex items-center justify-end gap-3">
            <button @click="showCreateModal = false" type="button" class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl font-semibold transition">បោះបង់</button>
            <button :disabled="isSubmittingCreate" type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md shadow-blue-600/30 transition flex items-center gap-2 disabled:opacity-50">
              <i v-if="!isSubmittingCreate" class="fa-solid fa-check"></i>
              <span>{{ isSubmittingCreate ? 'កំពុងបន្ថែម...' : 'បន្ថែមសិស្ស' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal 3: Approve & Enroll Student Modal -->
    <div 
      v-if="showApproveModal && selectedStudent" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs font-khmer transition-opacity"
      @click.self="showApproveModal = false"
    >
      <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <!-- Approve Header -->
        <div class="bg-emerald-600 p-5 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <h3 class="font-bold text-base">ត្រួតពិនិត្យ និងអនុម័តសិស្ស (Approve & Enroll)</h3>
              <p class="text-xs text-emerald-100">ពិនិត្យព័ត៌មាន ចាត់ថ្នាក់ និងផ្ដល់អត្តលេខផ្លូវការ</p>
            </div>
          </div>
          <button @click="showApproveModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">✕</button>
        </div>

        <!-- Approve Body -->
        <div class="p-6 space-y-4 text-xs text-slate-700">
          <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 overflow-hidden shadow-xs flex-shrink-0">
              <img v-if="selectedStudent.photo" :src="selectedStudent.photo" class="w-full h-full object-cover">
              <i v-else class="fa-solid fa-graduation-cap text-2xl text-slate-400"></i>
            </div>
            <div class="flex-1">
              <h4 class="font-bold text-sm text-slate-900">{{ selectedStudent.name }}</h4>
              <p v-if="selectedStudent.name_latin" class="text-[11px] text-slate-500 font-mono">{{ selectedStudent.name_latin }}</p>
              <div class="flex items-center gap-3 text-slate-500 mt-1">
                <span>ភេទ: {{ selectedStudent.gender === 'male' ? 'ប្រុស' : 'ស្រី' }}</span>
                <span>ទូរស័ព្ទ: {{ selectedStudent.phone || '-' }}</span>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3 pt-2">
            <div>
              <label class="block font-semibold mb-1">អត្តលេខសិស្សផ្លូវការ (Student ID) <span class="text-red-500">*</span></label>
              <input type="text" v-model="approveForm.student_id_card" class="w-full px-3.5 py-2.5 bg-emerald-50/50 border border-emerald-300 rounded-xl font-mono font-bold text-emerald-800">
            </div>
            <div>
              <label class="block font-semibold mb-1">ថ្នាក់រៀនដែលត្រូវចាត់តាំង <span class="text-red-500">*</span></label>
              <select v-model="approveForm.grade" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                <option v-for="cl in availableClasses" :key="cl" :value="cl">{{ cl }}</option>
              </select>
            </div>
          </div>

          <div class="p-5 bg-slate-50 border-t border-slate-200 -mx-6 -mb-6 mt-4 flex items-center justify-end gap-3">
            <button @click="showApproveModal = false" type="button" class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl font-semibold transition">បោះបង់</button>
            <button :disabled="isSubmittingApproval" @click="submitApproval" type="button" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md shadow-emerald-600/30 transition flex items-center gap-2 disabled:opacity-50">
              <i v-if="!isSubmittingApproval" class="fa-solid fa-check"></i>
              <span>{{ isSubmittingApproval ? 'កំពុងអនុម័ត...' : 'យល់ព្រមអនុម័ត' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal 4: Student Profile Modal -->
    <div 
      v-if="showProfileModal && selectedStudent" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs font-khmer transition-opacity"
      @click.self="showProfileModal = false"
    >
      <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-blue-700 to-indigo-700 p-6 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-2xl">
              <i class="fa-solid fa-id-card"></i>
            </div>
            <div>
              <h3 class="font-bold text-base">ប្រវត្តិរូបសិស្ស (Student Profile)</h3>
              <p class="text-xs text-blue-100">ព័ត៌មានលម្អិត និងកាតសម្គាល់ខ្លួនសិស្ស</p>
            </div>
          </div>
          <button @click="showProfileModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">✕</button>
        </div>

        <div class="p-6 space-y-5 text-xs text-slate-700">
          <!-- Top Profile Banner -->
          <div class="flex items-center gap-4 p-4 bg-slate-50 border border-slate-200/80 rounded-2xl">
            <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 overflow-hidden shadow-xs flex-shrink-0">
              <img v-if="selectedStudent.photo" :src="selectedStudent.photo" class="w-full h-full object-cover">
              <i v-else class="fa-solid fa-user-graduate text-3xl text-blue-500"></i>
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <h4 class="font-bold text-base text-slate-900">{{ selectedStudent.name }}</h4>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">
                  ថ្នាក់ {{ selectedStudent.grade || '-' }}
                </span>
              </div>
              <p v-if="selectedStudent.name_latin" class="text-xs text-slate-500 font-mono uppercase">{{ selectedStudent.name_latin }}</p>
              <div class="mt-1 font-mono text-xs font-bold text-blue-600">{{ selectedStudent.student_id_card }}</div>
            </div>
          </div>

          <!-- Info Details Grid -->
          <div class="grid grid-cols-2 gap-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
            <div>
              <span class="text-slate-400 block text-[11px]">ភេទ:</span>
              <span class="font-semibold text-slate-800">{{ selectedStudent.gender === 'male' || selectedStudent.gender === 'ប្រុស' ? 'ប្រុស (Male)' : 'ស្រី (Female)' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">ថ្ងៃខែឆ្នាំកំណើត:</span>
              <span class="font-semibold text-slate-800">{{ selectedStudent.date_of_birth || '-' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">លេខទូរស័ព្ទ:</span>
              <span class="font-semibold text-slate-800 font-mono">{{ selectedStudent.phone || '-' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">ស្ថានភាព:</span>
              <span class="font-bold text-emerald-600">{{ selectedStudent.status }}</span>
            </div>
            <div class="col-span-2">
              <span class="text-slate-400 block text-[11px]">អាសយដ្ឋាន:</span>
              <span class="font-semibold text-slate-800">{{ selectedStudent.address || '-' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">អាណាព្យាបាល:</span>
              <span class="font-semibold text-slate-800">{{ selectedStudent.parent_name || '-' }} ({{ selectedStudent.parent_relationship || 'អាណាព្យាបាល' }})</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">ទូរស័ព្ទអាណាព្យាបាល:</span>
              <span class="font-semibold text-slate-800 font-mono">{{ selectedStudent.parent_phone || '-' }}</span>
            </div>
          </div>

          <!-- Bottom Profile Actions -->
          <div class="flex items-center justify-between pt-3 border-t border-slate-100">
            <a 
              :href="`/students/${selectedStudent.id}/id-card`" 
              target="_blank"
              class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md shadow-blue-600/20 transition inline-flex items-center gap-2"
            >
              <i class="fa-solid fa-print"></i>
              <span>បោះពុម្ពកាតសិស្ស (Print ID Card)</span>
            </a>
            <div class="flex items-center gap-2">
              <button 
                @click="showProfileModal = false; openEditModal(selectedStudent)" 
                type="button" 
                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition"
              >
                កែប្រែ
              </button>
              <button 
                @click="showProfileModal = false" 
                type="button" 
                class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-semibold transition"
              >
                បិទ
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal 5: Edit Student Modal -->
    <div 
      v-if="showEditModal && selectedStudent" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs font-khmer transition-opacity"
      @click.self="showEditModal = false"
    >
      <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-5 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
              <i class="fa-solid fa-user-pen"></i>
            </div>
            <div>
              <h3 class="font-bold text-base">កែប្រែព័ត៌មានសិស្ស (Edit Student)</h3>
              <p class="text-xs text-blue-100">{{ selectedStudent.name }} ({{ selectedStudent.student_id_card }})</p>
            </div>
          </div>
          <button @click="showEditModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">✕</button>
        </div>

        <form @submit.prevent="submitEditStudent" class="p-6 space-y-4 text-xs text-slate-700">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះពេញជាភាសាខ្មែរ <span class="text-red-500">*</span></label>
              <input type="text" v-model="editForm.name_khmer" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះជាអក្សរឡាតាំង (Latin Name)</label>
              <input type="text" v-model="editForm.name_latin" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ភេទ <span class="text-red-500">*</span></label>
              <select v-model="editForm.gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="male">ប្រុស (Male)</option>
                <option value="female">ស្រី (Female)</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">ថ្នាក់រៀន <span class="text-red-500">*</span></label>
              <select v-model="editForm.grade" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-bold text-slate-800">
                <option v-for="cl in availableClasses" :key="cl" :value="cl">{{ cl }}</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">ថ្ងៃខែឆ្នាំកំណើត</label>
              <input type="date" v-model="editForm.date_of_birth" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">លេខទូរស័ព្ទសិស្ស</label>
              <input type="tel" v-model="editForm.phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ស្ថានភាព</label>
              <select v-model="editForm.status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                <option value="enrolled">កំពុងរៀន (Enrolled)</option>
                <option value="active">សកម្ម (Active)</option>
                <option value="graduated">បានបញ្ចប់ការសិក្សា (Graduated)</option>
                <option value="transferred">ផ្ទេរការសិក្សា (Transferred)</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះអាណាព្យាបាល</label>
              <input type="text" v-model="editForm.parent_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
            </div>
            <div class="sm:col-span-2">
              <label class="block font-semibold mb-1">អាសយដ្ឋានបច្ចុប្បន្ន</label>
              <input type="text" v-model="editForm.address" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
            </div>
          </div>

          <div class="p-5 bg-slate-50 border-t border-slate-200 -mx-6 -mb-6 mt-4 flex items-center justify-end gap-3">
            <button @click="showEditModal = false" type="button" class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl font-semibold transition">បោះបង់</button>
            <button :disabled="isSubmittingEdit" type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md shadow-blue-600/30 transition flex items-center gap-2 disabled:opacity-50">
              <i v-if="!isSubmittingEdit" class="fa-solid fa-check"></i>
              <span>{{ isSubmittingEdit ? 'កំពុងរក្សាទុក...' : 'រក្សាទុកការកែប្រែ' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal 6: Bulk Import Students Modal (Redesigned) -->
    <div 
      v-if="showBulkModal" 
      class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-900/60 backdrop-blur-xs font-khmer transition-opacity"
      @click.self="showBulkModal = false"
    >
      <div class="bg-white w-full max-w-5xl max-h-[92vh] flex flex-col rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-blue-600 px-6 py-4.5 text-white flex items-center justify-between flex-shrink-0">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl shadow-inner">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div>
              <h3 class="font-bold text-base leading-tight">បញ្ចូលសិស្សជាក្រុម (Bulk Import)</h3>
              <p class="text-xs text-indigo-100">Excel/CSV ឬវាយបញ្ចូលឈ្មោះសិស្សច្រើននាក់ក្នុងពេលតែមួយ</p>
            </div>
          </div>
          <button 
            @click="showBulkModal = false" 
            type="button"
            class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition cursor-pointer"
          >
            ✕
          </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="p-6 overflow-y-auto space-y-5 flex-1">
          
          <!-- Top Configuration Bar: Target Class Selector & Import Modes -->
          <div class="grid grid-cols-1 md:grid-cols-12 gap-4 bg-slate-50 p-4.5 rounded-2xl border border-slate-200/80 items-center">
            
            <!-- Target Class Selector -->
            <div class="md:col-span-4 flex items-center gap-3">
              <label class="text-xs font-bold text-slate-700 whitespace-nowrap">
                ថ្នាក់គោលដៅ <span class="text-rose-500">*</span>:
              </label>
              <div class="flex items-center gap-2 flex-1">
                <select 
                  v-model="bulkTargetClass" 
                  class="flex-1 px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-2xs font-khmer"
                >
                  <option v-for="cl in availableClasses" :key="cl" :value="cl">ថ្នាក់ {{ cl }}</option>
                </select>
                <button 
                  v-if="bulkStudents.length > 0"
                  @click="applyClassToAll" 
                  type="button" 
                  class="px-2.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[11px] font-bold rounded-xl transition cursor-pointer whitespace-nowrap"
                  title="កំណត់ថ្នាក់នេះទៅគ្រប់ជួរក្នុងតារាង"
                >
                  កំណត់គ្រប់ជួរ
                </button>
              </div>
            </div>

            <!-- Import Source Tabs (3 tabs) -->
            <div class="md:col-span-8 flex items-center justify-end gap-2 flex-wrap">
              <button 
                type="button"
                @click="bulkImportTab = 'file'" 
                :class="[
                  'px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer',
                  bulkImportTab === 'file' 
                    ? 'bg-indigo-600 text-white shadow-sm' 
                    : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'
                ]"
              >
                📊 Excel / CSV
              </button>

              <button 
                type="button"
                @click="bulkImportTab = 'quick'" 
                :class="[
                  'px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer',
                  bulkImportTab === 'quick' 
                    ? 'bg-emerald-600 text-white shadow-sm' 
                    : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'
                ]"
              >
                ⚡ វាយបញ្ចូលរហ័ស
              </button>

              <button 
                type="button"
                @click="bulkImportTab = 'paste'" 
                :class="[
                  'px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer',
                  bulkImportTab === 'paste' 
                    ? 'bg-indigo-600 text-white shadow-sm' 
                    : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'
                ]"
              >
                📋 បិទភ្ជាប់អត្ថបទ
              </button>

              <button 
                type="button"
                @click="bulkImportTab = 'image'" 
                :class="[
                  'px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer',
                  bulkImportTab === 'image' 
                    ? 'bg-amber-600 text-white shadow-sm' 
                    : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'
                ]"
              >
                📷 មើលរូប + វាយ
              </button>
            </div>

          </div>

          <!-- Tab 1: Excel/CSV File Upload -->
          <div v-if="bulkImportTab === 'file'" class="space-y-3">
            <div 
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="handleDrop"
              :class="[
                'border-2 border-dashed rounded-2xl p-6 text-center transition-all relative',
                isDragging ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-300 hover:border-indigo-400 bg-slate-50/60'
              ]"
            >
              <input 
                ref="fileInputRef"
                type="file" 
                accept=".xlsx,.xls,.csv" 
                @change="handleFileUpload" 
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                :disabled="isScanning"
              />

              <div v-if="isScanning" class="py-5 flex flex-col items-center justify-center gap-2.5">
                <div class="w-9 h-9 border-3 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                <div class="text-xs font-bold text-indigo-700">
                  {{ ocrStatusText || 'កំពុងអានទិន្នន័យពីឯកសារ...' }}
                </div>
              </div>

              <div v-else class="flex flex-col items-center justify-center gap-2">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shadow-2xs">
                  📊
                </div>
                <div>
                  <span class="text-xs font-bold text-indigo-700 hover:underline">ចុចដើម្បីជ្រើសរើសឯកសារ Excel / CSV</span>
                  <span class="text-xs text-slate-500"> ឬទម្លាក់ឯកសារមកទីនេះ</span>
                </div>
                <div class="flex items-center flex-wrap justify-center gap-2 text-[11px] text-slate-500 mt-1">
                  <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-semibold rounded-md border border-emerald-200">📊 Excel (.xlsx, .xls)</span>
                  <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-semibold rounded-md border border-emerald-200">📑 CSV (.csv)</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">✅ របៀបល្អបំផុតសម្រាប់បញ្ចូលសិស្សច្រើននាក់ (30+ នាក់)</p>
              </div>
            </div>
          </div>

          <!-- Tab 2: Quick Entry (Fast typing mode) -->
          <div v-if="bulkImportTab === 'quick'" class="space-y-3">
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4">
              <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 text-lg">⚡</div>
                <div class="space-y-1.5 flex-1">
                  <h4 class="text-xs font-bold text-emerald-800">របៀបវាយបញ្ចូលរហ័ស — មួយបន្ទាត់ = សិស្សម្នាក់</h4>
                  <p class="text-[11px] text-emerald-700 leading-relaxed">
                    វាយឈ្មោះសិស្សម្នាក់ក្នុងមួយបន្ទាត់។ សម្រាប់ភេទ សូមដាក់ <strong>ស្រី</strong> ឬ <strong>ស</strong> បន្ទាប់ពីឈ្មោះ (ចន្លោះឃ្លា)។ បើមិនដាក់ នឹងកំណត់ជា <strong>ប្រុស</strong> ដោយស្វ័យប្រវត្តិ។
                  </p>
                  <div class="bg-white rounded-xl p-2.5 border border-emerald-200 text-[11px] font-mono text-slate-600 space-y-0.5">
                    <div>គង់ សុភ័ក្រ្ត</div>
                    <div>ចាន់ សុជាតា <span class="text-rose-600 font-bold">ស្រី</span></div>
                    <div>ឡុង វិបុល</div>
                    <div>សុខ ចន្ទា <span class="text-rose-600 font-bold">ស</span></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-slate-700">វាយបញ្ចូលឈ្មោះសិស្ស (មួយបន្ទាត់ = សិស្សម្នាក់):</label>
              </div>
              <textarea 
                v-model="quickEntryText"
                rows="8" 
                placeholder="គង់ សុភ័ក្រ្ត&#10;ចាន់ សុជាតា ស្រី&#10;ឡុង វិបុល&#10;សុខ ចន្ទា ស&#10;ហុង សុគន្ធា ស្រី" 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-khmer focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 leading-relaxed"
                @keydown.ctrl.enter="parseQuickEntry"
              ></textarea>
            </div>
            <div class="flex items-center justify-between">
              <p class="text-[11px] text-slate-400">💡 Ctrl+Enter ដើម្បីទាញយកឈ្មោះរហ័ស</p>
              <button 
                @click="parseQuickEntry" 
                type="button" 
                :disabled="!quickEntryText.trim()"
                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-1.5 disabled:opacity-50 cursor-pointer"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span>⚡ ទាញយកបញ្ជីឈ្មោះ</span>
              </button>
            </div>
          </div>

          <!-- Tab 3: Paste Text Zone -->
          <div v-if="bulkImportTab === 'paste'" class="space-y-3">
            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-slate-700">បិទភ្ជាប់បញ្ជីឈ្មោះសិស្ស (មួយជួរ = សិស្សម្នាក់):</label>
                <span class="text-[11px] text-slate-400">គំរូ: ១. គង់ សុភ័ក្រ្ត ប្រុស 012345678</span>
              </div>
              <textarea 
                v-model="pasteTextContent"
                rows="5" 
                placeholder="១. គង់ សុភ័ក្រ្ត ប្រុស 012345678&#10;២. ចាន់ សុជាតា ស្រី 098765432&#10;៣. ឡុង វិបុល ប្រុស 011223344" 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-khmer focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
              ></textarea>
            </div>
            <div class="flex justify-end">
              <button 
                @click="parsePastedText" 
                type="button" 
                :disabled="!pasteTextContent.trim()"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-1.5 disabled:opacity-50 cursor-pointer"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span>ទាញយកបញ្ជីឈ្មោះ (Extract)</span>
              </button>
            </div>
          </div>

          <!-- Tab 4: Reference Image + Quick Entry (Side by Side) -->
          <div v-if="bulkImportTab === 'image'" class="space-y-3">
            <!-- Upload Image Area (if no image yet) -->
            <div v-if="!referenceImageUrl" class="space-y-3">
              <div 
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleImageDrop"
                :class="[
                  'border-2 border-dashed rounded-2xl p-6 text-center transition-all relative',
                  isDragging ? 'border-amber-500 bg-amber-50/50' : 'border-slate-300 hover:border-amber-400 bg-slate-50/60'
                ]"
              >
                <input 
                  type="file" 
                  accept=".jpg,.jpeg,.png,.pdf,.webp,.bmp" 
                  @change="handleImageUpload" 
                  class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                />
                <div class="flex flex-col items-center justify-center gap-2">
                  <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shadow-2xs">
                    📷
                  </div>
                  <div>
                    <span class="text-xs font-bold text-amber-700 hover:underline">ផ្ទុករូបភាពបញ្ជីឈ្មោះសិស្ស</span>
                    <span class="text-xs text-slate-500"> ដើម្បីមើលជាឯកសារយោង</span>
                  </div>
                  <div class="flex items-center flex-wrap justify-center gap-2 text-[11px] text-slate-500 mt-1">
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-semibold rounded-md border border-blue-200">📷 JPG, PNG</span>
                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 font-semibold rounded-md border border-rose-200">📕 PDF</span>
                  </div>
                  <p class="text-[11px] text-amber-600 mt-1 font-semibold">💡 រូបភាពនឹងបង្ហាញជាឯកសារយោង ហើយអ្នកអាចវាយឈ្មោះសិស្សនៅខាងក្រោម</p>
                </div>
              </div>
            </div>
            
            <!-- Side-by-side: Image Reference + Quick Entry -->
            <div v-else class="space-y-3">
              <!-- Image Header -->
              <div class="flex items-center justify-between bg-amber-50 p-3 rounded-xl border border-amber-200">
                <div class="flex items-center gap-2">
                  <span class="text-lg">📷</span>
                  <span class="text-xs font-bold text-amber-800">ឯកសារយោង (Reference Image)</span>
                </div>
                <div class="flex items-center gap-2">
                  <button 
                    @click="toggleImageExpand" 
                    type="button" 
                    class="px-2.5 py-1.5 bg-white hover:bg-amber-100 text-amber-700 text-[11px] font-bold rounded-lg transition cursor-pointer border border-amber-200"
                  >
                    {{ imageExpanded ? '🔽 បង្រួម' : '🔼 ពង្រីក' }}
                  </button>
                  <button 
                    @click="referenceImageUrl = ''" 
                    type="button" 
                    class="px-2.5 py-1.5 bg-white hover:bg-rose-100 text-rose-600 text-[11px] font-bold rounded-lg transition cursor-pointer border border-rose-200"
                  >
                    ✕ ដកចេញ
                  </button>
                </div>
              </div>
              
              <!-- Reference Image Display -->
              <div :class="['border border-slate-200 rounded-2xl overflow-hidden bg-slate-100', imageExpanded ? 'max-h-[500px]' : 'max-h-64']" class="transition-all duration-300">
                <img 
                  :src="referenceImageUrl" 
                  alt="Student list reference" 
                  class="w-full h-full object-contain"
                />
              </div>

              <!-- Quick Entry below the image -->
              <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3">
                <p class="text-[11px] text-emerald-700 font-semibold">
                  ⬇️ សូមវាយឈ្មោះសិស្សខាងក្រោមដោយមើលរូបភាពខាងលើជាឯកសារយោង (មួយបន្ទាត់ = សិស្សម្នាក់ | ដាក់ <strong>ស្រី</strong> ឬ <strong>ស</strong> បន្ទាប់ពីឈ្មោះ សម្រាប់ភេទស្រី)
                </p>
              </div>
              <textarea 
                v-model="quickEntryText"
                rows="8" 
                placeholder="គង់ សុភ័ក្រ្ត&#10;ចាន់ សុជាតា ស្រី&#10;ឡុង វិបុល&#10;សុខ ចន្ទា ស" 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-khmer focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 leading-relaxed"
                @keydown.ctrl.enter="parseQuickEntry"
              ></textarea>
              <div class="flex items-center justify-between">
                <p class="text-[11px] text-slate-400">💡 Ctrl+Enter ដើម្បីទាញយកឈ្មោះ</p>
                <button 
                  @click="parseQuickEntry" 
                  type="button" 
                  :disabled="!quickEntryText.trim()"
                  class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-1.5 disabled:opacity-50 cursor-pointer"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                  <span>⚡ ទាញយកបញ្ជីឈ្មោះ</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Error Alert Banner if any -->
          <div v-if="scanError" class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-2.5 text-xs text-rose-700">
            <svg class="w-4 h-4 text-rose-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">{{ scanError }}</div>
            <button @click="scanError = ''" class="text-rose-400 hover:text-rose-600 font-bold cursor-pointer">✕</button>
          </div>

          <!-- Interactive Preview & Edit Table Section -->
          <div class="space-y-3">
            <div class="flex items-center justify-between flex-wrap gap-2">
              <div class="flex items-center gap-2">
                <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wide">តារាងត្រួតពិនិត្យ និងកែសម្រួលទិន្នន័យ:</h4>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                  {{ bulkStudents.length }} នាក់
                </span>
              </div>
              <div class="flex items-center gap-2">
                <button 
                  @click="addBulkRow" 
                  type="button" 
                  class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition flex items-center gap-1 cursor-pointer"
                >
                  <span>+ បន្ថែមជួរថ្មី</span>
                </button>
                <button 
                  v-if="bulkStudents.length > 0"
                  @click="clearBulkRows" 
                  type="button" 
                  class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold rounded-xl transition cursor-pointer"
                >
                  <span>🗑️ សម្អាតទាំងអស់</span>
                </button>
              </div>
            </div>

            <!-- Table Container -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs">
              <div class="max-h-72 overflow-y-auto">
                <table class="w-full text-left text-xs text-slate-700 border-collapse">
                  <thead class="bg-slate-100 text-slate-700 font-bold sticky top-0 z-10 border-b border-slate-200">
                    <tr>
                      <th class="py-2.5 px-3 w-10 text-center">#</th>
                      <th class="py-2.5 px-3 min-w-[180px]">ឈ្មោះសិស្ស <span class="text-rose-500">*</span></th>
                      <th class="py-2.5 px-3 w-28">ភេទ</th>
                      <th class="py-2.5 px-3 w-28">ថ្នាក់</th>
                      <th class="py-2.5 px-3 w-14 text-center">លុប</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-if="bulkStudents.length === 0">
                      <td colspan="5" class="py-10 text-center text-slate-400 font-khmer">
                        <div class="flex flex-col items-center justify-center gap-1">
                          <span class="text-2xl">📋</span>
                          <span>មិនទាន់មានទិន្នន័យសិស្សនៅឡើយទេ។</span>
                          <span class="text-[11px]">សូមប្រើ "⚡ វាយបញ្ចូលរហ័ស" ឬ "📊 Excel/CSV" ដើម្បីបន្ថែមសិស្ស</span>
                        </div>
                      </td>
                    </tr>
                    <tr v-for="(row, idx) in bulkStudents" :key="idx" class="hover:bg-slate-50/70 transition-colors">
                      <td class="py-2 px-3 text-center font-mono font-bold text-slate-400">
                        {{ idx + 1 }}
                      </td>
                      <td class="py-1.5 px-2">
                        <input 
                          type="text" 
                          v-model="row.name" 
                          required 
                          placeholder="ឧ. គង់ សុភ័ក្រ្ត"
                          class="w-full px-2.5 py-1.5 bg-slate-50 focus:bg-white border border-slate-200 focus:border-indigo-500 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        />
                      </td>
                      <td class="py-1.5 px-2">
                        <select 
                          v-model="row.gender" 
                          class="w-full px-2 py-1.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none"
                        >
                          <option value="ប្រុស">ប្រុស</option>
                          <option value="ស្រី">ស្រី</option>
                        </select>
                      </td>
                      <td class="py-1.5 px-2">
                        <select 
                          v-model="row.grade" 
                          class="w-full px-2 py-1.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-lg text-xs font-bold text-indigo-700 focus:outline-none"
                        >
                          <option v-for="cl in availableClasses" :key="cl" :value="cl">{{ cl }}</option>
                        </select>
                      </td>
                      <td class="py-1.5 px-2 text-center">
                        <button 
                          @click="removeBulkRow(idx)" 
                          type="button" 
                          class="w-7 h-7 inline-flex items-center justify-center text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition cursor-pointer"
                          title="លុបជួរនេះ"
                        >
                          <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between flex-shrink-0">
          <div class="text-xs text-slate-500 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
            <span>អត្តលេខសិស្ស (STU-2026-{ថ្នាក់}-{លេខ}) នឹងត្រូវបង្កើតដោយស្វ័យប្រវត្តិ</span>
          </div>

          <div class="flex items-center gap-3">
            <button 
              @click="showBulkModal = false" 
              type="button" 
              class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer"
            >
              បោះបង់
            </button>
            <button 
              @click="submitBulkStudents" 
              :disabled="bulkStudents.length === 0 || isSubmittingBulk" 
              type="button" 
              class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/30 transition flex items-center gap-2 disabled:opacity-50 cursor-pointer"
            >
              <svg v-if="!isSubmittingBulk" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
              <div v-else class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              <span>{{ isSubmittingBulk ? 'កំពុងរក្សាទុក...' : `✅ រក្សាទុកសិស្សទាំងអស់ (${bulkStudents.length} នាក់)` }}</span>
            </button>
          </div>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import axios from 'axios';
import QRCode from 'qrcode';
import * as XLSX from 'xlsx';
// Note: Tesseract.js removed — Khmer OCR is unreliable. Using Reference Image + Quick Entry instead.
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import EntityBanner from '../components/EntityBanner.vue';
import StatCard from '../components/StatCard.vue';
import FilterBar from '../components/FilterBar.vue';
import DataTable from '../components/DataTable.vue';

const { t } = useI18n();
const router = useRouter();

const activeTab = ref('all');
const searchQuery = ref('');
const genderFilter = ref('all');
const gradeFilter = ref('');

const students = ref([]);
const pendingCount = ref(0);
const activeCount = ref(0);
const graduatedCount = ref(0);
const transferredCount = ref(0);

const availableClasses = ref([
  '7A', '7B', '7C', '7D',
  '8A', '8B', '8C', '8D',
  '9A', '9B', '9C', '9D',
  '10A', '10B', '10C', '10D',
  '11A', '11B', '11C', '11D',
  '12A', '12B', '12C', '12D'
]);

// Modal States
const showQrModal = ref(false);
const qrDataUrl = ref('');
const isCopied = ref(false);
const publicRegistrationUrl = ref('');

// Bulk Scan & Import Modal States
const showBulkModal = ref(false);
const bulkTargetClass = ref('10A');
const bulkImportTab = ref('file'); // 'file', 'paste', or 'quick'
const isScanning = ref(false);
const isDragging = ref(false);
const scanError = ref('');
const pasteTextContent = ref('');
const bulkStudents = ref([]);
const isSubmittingBulk = ref(false);
const fileInputRef = ref(null);
const scanProgress = ref(0);
const ocrStatusText = ref('');
// Reference Image + Quick Entry states
const referenceImageUrl = ref('');
const imageExpanded = ref(false);
const quickEntryText = ref('');
const quickEntryNameRefs = ref([]);

const showCreateModal = ref(false);
const isSubmittingCreate = ref(false);
const createForm = ref({
  name_khmer: '',
  name_latin: '',
  gender: 'male',
  grade: '7A',
  date_of_birth: '',
  phone: '',
  address: '',
  parent_name: '',
  parent_phone: '',
  parent_relationship: 'ឪពុក',
});

const showApproveModal = ref(false);
const selectedStudent = ref(null);
const approveForm = ref({
  grade: '',
  student_id_card: ''
});
const isSubmittingApproval = ref(false);

const showProfileModal = ref(false);

const showEditModal = ref(false);
const isSubmittingEdit = ref(false);
const editForm = ref({
  name_khmer: '',
  name_latin: '',
  gender: 'male',
  grade: '',
  date_of_birth: '',
  phone: '',
  address: '',
  parent_name: '',
  parent_phone: '',
  parent_relationship: 'ឪពុក',
  status: 'enrolled',
});

const fetchStudents = async () => {
  try {
    const res = await axios.get('/api/students');
    if (res.data) {
      if (res.data.students) {
        students.value = res.data.students;
      }
      if (res.data.counts) {
        pendingCount.value = res.data.counts.pending || 0;
        activeCount.value = res.data.counts.active || 0;
        graduatedCount.value = res.data.counts.graduated || 0;
        transferredCount.value = res.data.counts.transferred || 0;
      } else {
        pendingCount.value = students.value.filter(st => st.status === 'pending').length;
        activeCount.value = students.value.filter(st => st.status === 'active' || st.status === 'enrolled').length;
        graduatedCount.value = students.value.filter(st => st.status === 'graduated').length;
        transferredCount.value = students.value.filter(st => st.status === 'transferred').length;
      }
    }
  } catch (e) {
    console.error('Failed to load students:', e);
  }
};

onMounted(() => {
  fetchStudents();
  publicRegistrationUrl.value = window.location.origin + '/register-student';
});

const filteredStudents = computed(() => {
  return students.value.filter(st => {
    let matchesTab = true;
    if (activeTab.value === 'all') {
      matchesTab = true;
    } else if (activeTab.value === 'pending') {
      matchesTab = st.status === 'pending';
    } else if (activeTab.value === 'active') {
      matchesTab = st.status === 'active' || st.status === 'enrolled';
    } else if (activeTab.value === 'graduated') {
      matchesTab = st.status === 'graduated';
    } else if (activeTab.value === 'transferred') {
      matchesTab = st.status === 'transferred';
    }

    const s = searchQuery.value.toLowerCase().trim();
    const matchesSearch = !s || 
      (st.name && st.name.toLowerCase().includes(s)) ||
      (st.name_latin && st.name_latin.toLowerCase().includes(s)) ||
      (st.student_id_card && st.student_id_card.toLowerCase().includes(s)) ||
      (st.phone && st.phone.includes(s));

    const matchesGender = genderFilter.value === 'all' || 
      (genderFilter.value === 'male' && (st.gender === 'male' || st.gender === 'ប្រុស')) ||
      (genderFilter.value === 'female' && (st.gender === 'female' || st.gender === 'ស្រី'));

    const matchesGrade = !gradeFilter.value || st.grade === gradeFilter.value;

    return matchesTab && matchesSearch && matchesGender && matchesGrade;
  });
});

// QR Code Operations
const openQrModal = async () => {
  publicRegistrationUrl.value = window.location.origin + '/register-student';
  showQrModal.value = true;
  isCopied.value = false;
  try {
    qrDataUrl.value = await QRCode.toDataURL(publicRegistrationUrl.value, {
      width: 280,
      margin: 2,
      color: {
        dark: '#1e40af',
        light: '#ffffff'
      }
    });
  } catch (err) {
    console.error('Failed to generate QR code:', err);
  }
};

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(publicRegistrationUrl.value);
    isCopied.value = true;
    setTimeout(() => { isCopied.value = false; }, 3000);
  } catch (err) {
    const input = document.createElement('input');
    input.value = publicRegistrationUrl.value;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    isCopied.value = true;
    setTimeout(() => { isCopied.value = false; }, 3000);
  }
};

const printQrSheet = () => {
  const printWindow = window.open('', '_blank');
  if (!printWindow) return;

  printWindow.document.write(`
    <!DOCTYPE html>
    <html lang="km">
    <head>
      <meta charset="UTF-8">
      <title>បោះពុម្ព QR Code ចុះឈ្មោះសិស្ស</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600;700&display=swap" rel="stylesheet">
      <style>
        body { font-family: 'Kantumruy Pro', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #f8fafc; }
        .card { border: 2px dashed #2563eb; padding: 40px; text-align: center; border-radius: 24px; background: white; max-width: 420px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h2 { margin: 0 0 8px 0; color: #0f172a; font-size: 20px; }
        p { color: #64748b; font-size: 13px; margin: 0 0 24px 0; }
        img { width: 220px; height: 220px; border-radius: 12px; }
        .url { margin-top: 20px; padding: 10px 14px; background: #eff6ff; border-radius: 10px; font-size: 11px; font-family: monospace; color: #1d4ed8; word-break: break-all; }
      </style>
    </head>
    <body>
      <div class="card">
        <h2>ទម្រង់ចុះឈ្មោះសិស្សថ្មីតាមអនឡាញ</h2>
        <p>ស្កេន QR Code ខាងក្រោម ដើម្បីបំពេញព័ត៌មានចុះឈ្មោះចូលរៀន</p>
        <img src="${qrDataUrl.value}" alt="QR Code" />
        <div class="url">${publicRegistrationUrl.value}</div>
      </div>
      <script>window.onload = function() { window.print(); };<\/script>
    </body>
    </html>
  `);
  printWindow.document.close();
};

// Create Student
const openCreateModal = () => {
  createForm.value = {
    name_khmer: '',
    name_latin: '',
    gender: 'male',
    grade: availableClasses.value[0] || '7A',
    date_of_birth: '',
    phone: '',
    address: '',
    parent_name: '',
    parent_phone: '',
    parent_relationship: 'ឪពុក',
  };
  showCreateModal.value = true;
};

const submitCreateStudent = async () => {
  isSubmittingCreate.value = true;
  try {
    const res = await axios.post('/api/students', createForm.value);
    if (res.data && res.data.success) {
      alert(res.data.message);
      showCreateModal.value = false;
      await fetchStudents();
    }
  } catch (err) {
    console.error('Error creating student:', err);
    alert('មានបញ្ហាក្នុងការបន្ថែមសិស្ស សូមពិនិត្យព័ត៌មានឡើងវិញ។');
  } finally {
    isSubmittingCreate.value = false;
  }
};

// Approve Student
const openApproveModal = (student) => {
  selectedStudent.value = student;
  const year = new Date().getFullYear();
  const nextNum = String(students.value.length + 1).padStart(3, '0');
  const generatedId = `STU-${year}-${nextNum}`;

  approveForm.value = {
    grade: student.grade || '7A',
    student_id_card: student.student_id_card && student.student_id_card !== 'STU-រង់ចាំ' ? student.student_id_card : generatedId
  };
  showApproveModal.value = true;
};

const submitApproval = async () => {
  if (!selectedStudent.value) return;
  isSubmittingApproval.value = true;
  try {
    const res = await axios.post(`/api/students/${selectedStudent.value.id}/approve`, approveForm.value);
    if (res.data && res.data.success) {
      alert(res.data.message);
      showApproveModal.value = false;
      await fetchStudents();
    }
  } catch (err) {
    console.error('Approval failed:', err);
    alert('ការអនុម័តបានបរាជ័យ សូមព្យាយាមម្តងទៀត។');
  } finally {
    isSubmittingApproval.value = false;
  }
};

// Profile Modal
const openProfileModal = (st) => {
  selectedStudent.value = st;
  showProfileModal.value = true;
};

// Edit Student
const openEditModal = (st) => {
  selectedStudent.value = st;
  editForm.value = {
    name_khmer: st.name_khmer || st.name,
    name_latin: st.name_latin || '',
    gender: st.gender === 'female' || st.gender === 'ស្រី' ? 'female' : 'male',
    grade: st.grade || '7A',
    date_of_birth: st.date_of_birth || '',
    phone: st.phone !== '-' ? st.phone : '',
    address: st.address !== '-' ? st.address : '',
    parent_name: st.parent_name || '',
    parent_phone: st.parent_phone || '',
    parent_relationship: st.parent_relationship || 'ឪពុក',
    status: st.status || 'enrolled',
  };
  showEditModal.value = true;
};

const submitEditStudent = async () => {
  if (!selectedStudent.value) return;
  isSubmittingEdit.value = true;
  try {
    const res = await axios.put(`/api/students/${selectedStudent.value.id}`, editForm.value);
    if (res.data && res.data.success) {
      alert(res.data.message);
      showEditModal.value = false;
      await fetchStudents();
    }
  } catch (err) {
    console.error('Error editing student:', err);
    alert('មានបញ្ហាក្នុងការកែប្រែព័ត៌មានសិស្ស។');
  } finally {
    isSubmittingEdit.value = false;
  }
};

// Delete Student
const confirmDeleteStudent = async (st) => {
  if (!confirm(`តើអ្នកពិតជាចង់លុបសិស្ស "${st.name}" (អត្តលេខ: ${st.student_id_card}) មែនទេ?`)) return;
  try {
    const res = await axios.delete(`/api/students/${st.id}`);
    if (res.data && res.data.success) {
      alert(res.data.message);
      await fetchStudents();
    }
  } catch (err) {
    console.error('Delete failed:', err);
    alert('មិនអាចលុបទិន្នន័យបានទេ។');
  }
};

// Bulk Scan & Import Methods
const openBulkScanModal = () => {
  bulkTargetClass.value = gradeFilter.value || availableClasses.value[0] || '10A';
  bulkImportTab.value = 'file';
  scanError.value = '';
  pasteTextContent.value = '';
  showBulkModal.value = true;
};

const addBulkRow = () => {
  bulkStudents.value.push({
    name: '',
    english_name: '',
    gender: 'ប្រុស',
    grade: bulkTargetClass.value,
    phone: '',
  });
};

const removeBulkRow = (index) => {
  bulkStudents.value.splice(index, 1);
};

const clearBulkRows = () => {
  if (confirm('តើអ្នកពិតជាចង់សម្អាតតារាងបញ្ជីឈ្មោះនេះមែនទេ?')) {
    bulkStudents.value = [];
    scanError.value = '';
  }
};

const applyClassToAll = () => {
  bulkStudents.value.forEach(s => {
    s.grade = bulkTargetClass.value;
  });
};

const handleDrop = (e) => {
  isDragging.value = false;
  const file = e.dataTransfer?.files?.[0];
  if (file) {
    processFile(file);
  }
};

const handleFileUpload = (event) => {
  const file = event.target.files?.[0];
  if (!file) return;
  processFile(file);
  event.target.value = '';
};

const processFile = (file) => {
  const ext = file.name.split('.').pop().toLowerCase();
  if (['xlsx', 'xls', 'csv'].includes(ext)) {
    parseExcelOrCsv(file);
  } else if (['jpg', 'jpeg', 'png', 'webp', 'bmp', 'pdf'].includes(ext)) {
    handleImageFileOcr(file);
  } else {
    scanError.value = 'ប្រភេទឯកសារមិនត្រូវបានគាំទ្រ។ សូមប្រើប្រាស់ .xlsx, .csv, .jpg, .png ឬ .pdf';
  }
};

const parseExcelOrCsv = async (file) => {
  try {
    isScanning.value = true;
    scanError.value = '';
    const buffer = await file.arrayBuffer();
    const workbook = XLSX.read(buffer, { type: 'array' });
    const sheetName = workbook.SheetNames[0];
    const sheet = workbook.Sheets[sheetName];
    const rows = XLSX.utils.sheet_to_json(sheet, { header: 1, defval: '' });

    if (!rows || rows.length === 0) {
      scanError.value = 'ឯកសារគ្មានទិន្នន័យទេ។';
      return;
    }

    // Try finding header row
    let headerRowIdx = -1;
    let nameIdx = -1;
    let enNameIdx = -1;
    let genderIdx = -1;
    let gradeIdx = -1;
    let phoneIdx = -1;

    for (let i = 0; i < Math.min(rows.length, 10); i++) {
      const r = rows[i].map(c => String(c).trim().toLowerCase());
      const hasName = r.some(c => c.includes('ឈ្មោះ') || c.includes('name'));
      if (hasName) {
        headerRowIdx = i;
        r.forEach((col, idx) => {
          if (col.includes('ឈ្មោះ') || col.includes('name') || col.includes('khmer')) {
            if (col.includes('អង់គ្លេស') || col.includes('english') || col.includes('latin') || col.includes('សម្គាល់')) {
              enNameIdx = idx;
            } else if (nameIdx === -1) {
              nameIdx = idx;
            }
          }
          if (col.includes('ភេទ') || col.includes('gender') || col.includes('sex')) {
            genderIdx = idx;
          }
          if (col.includes('ថ្នាក់') || col.includes('class') || col.includes('grade')) {
            gradeIdx = idx;
          }
          if (col.includes('ទូរស័ព្ទ') || col.includes('phone') || col.includes('tel')) {
            phoneIdx = idx;
          }
        });
        break;
      }
    }

    const startRow = headerRowIdx >= 0 ? headerRowIdx + 1 : 0;
    const extracted = [];

    for (let i = startRow; i < rows.length; i++) {
      const row = rows[i];
      if (!row || row.length === 0 || !row.some(cell => String(cell).trim() !== '')) {
        continue;
      }

      let name = '';
      let enName = '';
      let gender = 'ប្រុស';
      let grade = bulkTargetClass.value;
      let phone = '';

      if (headerRowIdx >= 0 && nameIdx >= 0) {
        name = String(row[nameIdx] || '').trim();
        enName = enNameIdx >= 0 ? String(row[enNameIdx] || '').trim() : '';
        const gRaw = genderIdx >= 0 ? String(row[genderIdx] || '').trim() : '';
        if (gRaw) {
          gender = (gRaw.includes('ស្រី') || gRaw.includes('ស') || gRaw.toLowerCase() === 'female' || gRaw.toLowerCase() === 'f') ? 'ស្រី' : 'ប្រុស';
        }
        if (gradeIdx >= 0 && row[gradeIdx]) {
          const matchedClass = availableClasses.value.find(c => c.toLowerCase() === String(row[gradeIdx]).trim().toLowerCase());
          if (matchedClass) grade = matchedClass;
        }
        if (phoneIdx >= 0) {
          phone = String(row[phoneIdx] || '').trim();
        }
      } else {
        const nonBlank = row.filter(c => String(c).trim() !== '');
        if (nonBlank.length >= 1) {
          const firstIsNum = /^\d+$/.test(String(row[0]).trim());
          if (firstIsNum && row[1]) {
            name = String(row[1]).trim();
            const gRaw = String(row[2] || '');
            gender = (gRaw.includes('ស្រី') || gRaw.includes('ស') || gRaw.toLowerCase() === 'f') ? 'ស្រី' : 'ប្រុស';
            phone = String(row[3] || '').trim();
          } else {
            name = String(row[0]).trim();
            const gRaw = String(row[1] || '');
            gender = (gRaw.includes('ស្រី') || gRaw.includes('ស') || gRaw.toLowerCase() === 'f') ? 'ស្រី' : 'ប្រុស';
            phone = String(row[2] || '').trim();
          }
        }
      }

      if (name && !name.toLowerCase().includes('ឈ្មោះ') && !name.toLowerCase().includes('name')) {
        extracted.push({
          name,
          english_name: enName,
          gender,
          grade,
          phone
        });
      }
    }

    if (extracted.length > 0) {
      bulkStudents.value = [...bulkStudents.value, ...extracted];
    } else {
      scanError.value = 'មិនអាចទាញយកឈ្មោះសិស្សពីឯកសារនេះបានទេ។ សូមពិនិត្យទម្រង់តារាង ឬប្រើជម្រើសបិទភ្ជាប់អត្ថបទ។';
    }
  } catch (err) {
    console.error('Error parsing Excel:', err);
    scanError.value = 'មានបញ្ហាក្នុងការអានឯកសារ Excel/CSV: ' + (err.message || 'សូមពិនិត្យឯកសារឡើងវិញ');
  } finally {
    isScanning.value = false;
  }
};

const toggleImageExpand = () => {
  imageExpanded.value = !imageExpanded.value;
};

const handleImageUpload = (event) => {
  const file = event.target.files?.[0];
  if (!file) return;
  loadImageFile(file);
  event.target.value = '';
};

const handleImageDrop = (e) => {
  isDragging.value = false;
  const file = e.dataTransfer?.files?.[0];
  if (file) {
    loadImageFile(file);
  }
};

const loadImageFile = (file) => {
  const reader = new FileReader();
  reader.onload = (e) => {
    referenceImageUrl.value = e.target?.result || '';
    bulkImportTab.value = 'image';
  };
  reader.readAsDataURL(file);
};

const handleImageFileOcr = async (file) => {
  // Load image as reference image for easy typing / review
  loadImageFile(file);
};

const parseQuickEntry = () => {
  if (!quickEntryText.value.trim()) return;
  scanError.value = '';

  const lines = quickEntryText.value.split('\n');
  const extracted = [];

  for (let rawLine of lines) {
    let line = rawLine.trim();
    if (!line) continue;

    // Strip numbering like "1. ", "១. ", "1) ", "#1 "
    line = line.replace(/^[\d១-៩\.\-\)\s№#]+/, '').trim();
    if (!line) continue;

    // Check for female indicator: ស្រី, ស, F, female
    let gender = 'ប្រុស';
    if (/(^|\s)(ស្រី|ស|female|f)($|\s)/i.test(line)) {
      gender = 'ស្រី';
      line = line.replace(/(^|\s)(ស្រី|ស|female|f)($|\s)/gi, ' ').trim();
    } else if (/(^|\s)(ប្រុស|ប|male|m)($|\s)/i.test(line)) {
      gender = 'ប្រុស';
      line = line.replace(/(^|\s)(ប្រុស|ប|male|m)($|\s)/gi, ' ').trim();
    }

    let name = line.replace(/\s+/g, ' ').trim();

    if (name && name.length >= 2) {
      extracted.push({
        name,
        english_name: '',
        gender,
        grade: bulkTargetClass.value,
        phone: ''
      });
    }
  }

  if (extracted.length > 0) {
    bulkStudents.value = [...bulkStudents.value, ...extracted];
    quickEntryText.value = '';
    scanError.value = '';
  } else {
    scanError.value = 'មិនអាចទាញយកឈ្មោះសិស្សបានទេ។ សូមពិនិត្យមើលទម្រង់ឈ្មោះដែលបានវាយ។';
  }
};

const parsePastedText = () => {
  if (!pasteTextContent.value.trim()) return;
  scanError.value = '';
  
  const lines = pasteTextContent.value.split('\n');
  const extracted = [];

  for (let rawLine of lines) {
    let line = rawLine.trim();
    if (!line) continue;

    line = line.replace(/^[\d១-៩\.\-\)\s]+/, '').trim();
    if (!line) continue;

    let phone = '';
    const phoneMatch = line.match(/(0\d{8,9})/);
    if (phoneMatch) {
      phone = phoneMatch[1];
      line = line.replace(phoneMatch[0], '').trim();
    }

    let gender = 'ប្រុស';
    if (/(ស្រី|female|\bF\b|ស\b)/i.test(line)) {
      gender = 'ស្រី';
      line = line.replace(/(ស្រី|female|\bF\b|ស\b)/gi, '').trim();
    } else if (/(ប្រុស|male|\bM\b|ប\b)/i.test(line)) {
      gender = 'ប្រុស';
      line = line.replace(/(ប្រុស|male|\bM\b|ប\b)/gi, '').trim();
    }

    let name = line;
    let enName = '';
    const parenMatch = line.match(/^([^(]+)\s*\(([^)]+)\)$/);
    if (parenMatch) {
      name = parenMatch[1].trim();
      enName = parenMatch[2].trim();
    } else {
      const parts = line.split(/[,\t]+/).map(p => p.trim()).filter(Boolean);
      if (parts.length >= 2) {
        name = parts[0];
        enName = parts[1];
      }
    }

    name = name.replace(/^[-,]+|[-,]+$/g, '').trim();

    if (name) {
      extracted.push({
        name,
        english_name: enName,
        gender,
        grade: bulkTargetClass.value,
        phone
      });
    }
  }

  if (extracted.length > 0) {
    bulkStudents.value = [...bulkStudents.value, ...extracted];
    pasteTextContent.value = '';
  } else {
    scanError.value = 'មិនអាចទាញយកឈ្មោះសិស្សពីអត្ថបទដែលបានបិទភ្ជាប់ទេ។';
  }
};

const submitBulkStudents = async () => {
  if (bulkStudents.value.length === 0) return;

  const invalid = bulkStudents.value.some(s => !s.name || !s.name.trim());
  if (invalid) {
    alert('សូមបំពេញឈ្មោះសិស្សឱ្យបានគ្រប់ជ្រុងជ្រោយសម្រាប់គ្រប់ជួរទាំងអស់!');
    return;
  }

  isSubmittingBulk.value = true;
  scanError.value = '';

  try {
    const payload = {
      target_class: bulkTargetClass.value,
      students: bulkStudents.value.map(s => ({
        name: s.name.trim(),
        english_name: (s.english_name || '').trim(),
        gender: s.gender,
        grade: s.grade || bulkTargetClass.value,
        phone: (s.phone || '').trim()
      }))
    };

    const res = await axios.post('/api/students/bulk-store', payload);
    if (res.data && res.data.success) {
      alert(res.data.message || `បានបញ្ចូលសិស្សចំនួន ${res.data.count} នាក់ដោយជោគជ័យ!`);
      showBulkModal.value = false;
      bulkStudents.value = [];
      await fetchStudents();
    } else {
      alert(res.data?.message || 'មានបញ្ហាក្នុងការរក្សាទុកទិន្នន័យ។');
    }
  } catch (err) {
    console.error('Bulk store error:', err);
    const msg = err.response?.data?.message || err.message || 'មានបញ្ហាក្នុងការរក្សាទុកសិស្សជាក្រុម!';
    alert(msg);
  } finally {
    isSubmittingBulk.value = false;
  }
};

const handlePrint = () => window.print();
const handleDownload = () => window.location.href = '/students/export';
</script>
