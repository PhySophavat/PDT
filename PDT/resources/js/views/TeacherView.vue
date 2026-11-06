<template>
  <div class="space-y-6 font-khmer">
    <!-- Header with Extra Actions Slot for Link / QR Code -->
    <PageHeader 
      :title="t('teachers.title')" 
      :breadcrumb="t('nav.teachers')" 
      :actionLabel="t('teachers.addTeacher')"
      @action="openCreateModal"
    >
      <template #extraActions>
        <button 
          @click="openQrModal" 
          type="button"
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-full shadow-md shadow-emerald-600/20 transition-all hover:shadow-lg hover:shadow-emerald-600/30 active:scale-95"
        >
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
          </svg>
          <span>🔗 Link / QR ចុះឈ្មោះគ្រូ</span>
        </button>
      </template>
    </PageHeader>

    <!-- Sub Tabs including Pending Status with Badge -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'all', label: t('common.all') },
        { id: 'pending', label: 'រង់ចាំអនុម័ត', badge: pendingCount, badgeColor: 'bg-amber-100 text-amber-800' },
        { id: 'active', label: t('common.active') },
        { id: 'leave', label: t('teachers.onLeave') }
      ]"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="t('teachers.totalTeachers')" :value="teachers.length" />
      <StatCard :title="t('common.active')" :value="activeCount" />
      <StatCard title="រង់ចាំអនុម័ត" :value="pendingCount" />
      <StatCard :title="t('teachers.onLeave')" :value="leaveCount" />
    </div>

    <!-- Filter Bar -->
    <FilterBar>
      <template #left>
        <input 
          v-model="searchQuery"
          type="text" 
          :placeholder="t('common.searchPlaceholder')" 
          class="px-3.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs w-64 focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-khmer"
        />
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
      </template>
    </FilterBar>

    <!-- Data Table -->
    <DataTable :loading="isLoading" :empty="filteredTeachers.length === 0" :colSpan="7" loadingText="កំពុងទាញយកទិន្នន័យគ្រូបង្រៀន...">
      <template #header>
        <tr>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('teachers.teacherId') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('teachers.name') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('teachers.specialization') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('teachers.qualification') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('students.phone') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ t('common.status') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ t('common.actions') }}</th>
        </tr>
      </template>

      <template #body>
        <tr v-if="filteredTeachers.length === 0">
          <td colspan="7" class="py-10 text-center text-slate-400 font-khmer">{{ t('common.noData') }}</td>
        </tr>
        <tr v-for="teacher in filteredTeachers" :key="teacher.id" class="hover:bg-slate-50/70 transition-colors">
          <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ teacher.idCard }}</td>
          <td class="py-3 px-4 font-semibold text-slate-800">
            <div class="flex items-center gap-2.5">
              <div v-if="teacher.photo" class="w-8 h-8 rounded-full overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0">
                <img :src="teacher.photo" alt="" class="w-full h-full object-cover">
              </div>
              <div v-else class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                {{ (teacher.name || 'គ').charAt(0) }}
              </div>
              <div>
                <p class="leading-tight">{{ teacher.name }}</p>
                <p v-if="teacher.name_latin" class="text-[10px] text-slate-400 font-sans tracking-wide uppercase">{{ teacher.name_latin }}</p>
              </div>
            </div>
          </td>
          <td class="py-3 px-4 font-medium text-slate-700">{{ teacher.specialization }}</td>
          <td class="py-3 px-4 text-slate-600 text-xs">{{ teacher.qualification }}</td>
          <td class="py-3 px-4 text-slate-600 font-mono text-xs">{{ teacher.phone }}</td>
          <td class="py-3 px-4 text-center">
            <!-- Pending Status Badge -->
            <span 
              v-if="teacher.status === 'pending'"
              class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 inline-flex items-center gap-1"
            >
              <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
              រង់ចាំអនុម័ត
            </span>
            <!-- Active Status Badge -->
            <span 
              v-else-if="teacher.status === 'active' || teacher.status === 'fulltime' || teacher.status === 'parttime'"
              class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700"
            >
              {{ t('common.active') }}
            </span>
            <!-- On Leave Badge -->
            <span 
              v-else-if="teacher.status === 'leave'"
              class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700"
            >
              {{ t('teachers.onLeave') }}
            </span>
            <!-- Other Status Badge -->
            <span v-else class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
              {{ teacher.status }}
            </span>
          </td>
          <td class="py-3 px-4 text-right space-x-1">
            <!-- Action buttons for Pending Teachers -->
            <template v-if="teacher.status === 'pending'">
              <button 
                @click="openApproveModal(teacher)"
                class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs transition inline-flex items-center gap-1"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>អនុម័ត (Approve)</span>
              </button>
            </template>
            <!-- Normal Active Teacher Action buttons -->
            <template v-else>
              <button @click="openProfileModal(teacher)" class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs hover:bg-slate-200 transition">
                {{ t('attendance.tabs.profile') }}
              </button>
              <button @click="openEditModal(teacher)" class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition">
                {{ t('common.edit') }}
              </button>
              <button @click="confirmDeleteTeacher(teacher)" class="px-2 py-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg text-xs transition" title="លុប">
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
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 p-6 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
              </svg>
            </div>
            <div>
              <h3 class="font-bold text-base">Link / QR Code ចុះឈ្មោះគ្រូបង្រៀន</h3>
              <p class="text-xs text-emerald-100">សម្រាប់ចែករំលែកទៅកាន់លោកគ្រូ/អ្នកគ្រូ</p>
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
          
          <!-- QR Code Display -->
          <div class="p-6 bg-slate-50 border border-slate-200/80 rounded-2xl inline-block mx-auto shadow-inner text-center">
            <div class="text-xs font-bold text-slate-800 mb-2 uppercase tracking-wide">
              LMSTICK FACULTY - ស្កេនដើម្បីចុះឈ្មោះ
            </div>
            <div class="w-48 h-48 mx-auto bg-white p-2 rounded-xl border border-slate-200 shadow-xs flex items-center justify-center">
              <img v-if="qrDataUrl" :src="qrDataUrl" alt="QR Code Registration" class="w-full h-full object-contain" />
              <div v-else class="text-slate-400 text-xs">កំពុងបង្កើត QR Code...</div>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">ប្រើទូរស័ព្ទដៃដើម្បីស្កេនបំពេញទម្រង់ចុះឈ្មោះ</p>
          </div>

          <!-- Shareable URL Input with Copy Button -->
          <div class="text-left space-y-1.5">
            <label class="text-xs font-semibold text-slate-700">តំណភ្ជាប់ទម្រង់ចុះឈ្មោះគ្រូ (Teacher Registration Link):</label>
            <div class="flex items-center gap-2">
              <input 
                type="text" 
                readonly 
                :value="publicRegistrationUrl" 
                class="flex-1 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-emerald-700 select-all focus:outline-none"
              />
              <button 
                @click="copyLink"
                type="button"
                :class="[
                  'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5',
                  isCopied 
                    ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' 
                    : 'bg-emerald-700 hover:bg-emerald-800 text-white shadow-md shadow-emerald-700/20'
                ]"
              >
                <svg v-if="!isCopied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10a2 2 0 00-2 2v3a2 2 0 002 2h8a2 2 0 002-2v-3a2 2 0 00-2-2z"/>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ isCopied ? 'បានចម្លង! (Copied)' : 'ចម្លង Link' }}</span>
              </button>
            </div>
            <p class="text-[11px] text-slate-400">អ្នកអាចផ្ញើតំណភ្ជាប់នេះតាមរយៈ Telegram, Messenger ឬបណ្តាញសង្គមផ្សេងៗ។</p>
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

    <!-- Modal 2: Admin Quick Create Teacher Modal (Fix for "+ បន្ថែមគ្រូបង្រៀន") -->
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
              <h3 class="font-bold text-base">បន្ថែមគ្រូបង្រៀនថ្មី (Add New Teacher)</h3>
              <p class="text-xs text-blue-100">បញ្ចូលព័ត៌មានគ្រូផ្ទាល់ និងបង្កើតគណនីស្វ័យប្រវត្តិ</p>
            </div>
          </div>
          <button @click="showCreateModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">✕</button>
        </div>

        <form @submit.prevent="submitCreateTeacher" class="p-6 space-y-4 text-xs text-slate-700">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះពេញជាភាសាខ្មែរ <span class="text-red-500">*</span></label>
              <input type="text" v-model="createForm.name_khmer" required placeholder="ឧ. សុខ ចាន់ដារ៉ា" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះជាអក្សរឡាតាំង (Latin Name)</label>
              <input type="text" v-model="createForm.name_latin" placeholder="e.g. SOK CHANDARA" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ភេទ <span class="text-red-500">*</span></label>
              <select v-model="createForm.gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="male">ប្រុស (Male)</option>
                <option value="female">ស្រី (Female)</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">លេខទូរស័ព្ទ</label>
              <input type="tel" v-model="createForm.phone" placeholder="012 111 222" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ជំនាញឯកទេស <span class="text-red-500">*</span></label>
              <select v-model="createForm.specialization" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-bold text-slate-800">
                <option value="គណិតវិទ្យា">គណិតវិទ្យា</option>
                <option value="ភាសាខ្មែរ">ភាសាខ្មែរ</option>
                <option value="រូបវិទ្យា">រូបវិទ្យា</option>
                <option value="គីមីវិទ្យា">គីមីវិទ្យា</option>
                <option value="ជីវវិទ្យា">ជីវវិទ្យា</option>
                <option value="ភាសាអង់គ្លេស">ភាសាអង់គ្លេស</option>
                <option value="ប្រវត្តិវិទ្យា">ប្រវត្តិវិទ្យា</option>
                <option value="ភូមិវិទ្យា">ភូមិវិទ្យា</option>
                <option value="ព័ត៌មានវិទ្យា (ICT)">ព័ត៌មានវិទ្យា (ICT)</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">កម្រិតវប្បធម៌</label>
              <select v-model="createForm.qualification" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="បរិញ្ញាបត្រ">បរិញ្ញាបត្រ (Bachelor)</option>
                <option value="បរិញ្ញាបត្រជាន់ខ្ពស់">បរិញ្ញាបត្រជាន់ខ្ពស់ (Master)</option>
                <option value="បណ្ឌិត">បណ្ឌិត (PhD)</option>
                <option value="គរុកោសល្យមធ្យម">គរុកោសល្យមធ្យម</option>
              </select>
            </div>
            <div class="sm:col-span-2">
              <label class="block font-semibold mb-1">ថ្នាក់ដែលត្រូវចាត់តាំងបង្រៀន (ឧ. 7A, 7B)</label>
              <input type="text" v-model="createForm.class_assigned" placeholder="ឧ. 7A, 7B, 8A" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <div class="p-5 bg-slate-50 border-t border-slate-200 -mx-6 -mb-6 mt-4 flex items-center justify-end gap-3">
            <button @click="showCreateModal = false" type="button" class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl font-semibold transition">បោះបង់</button>
            <button :disabled="isSubmittingCreate" type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md shadow-blue-600/30 transition flex items-center gap-2 disabled:opacity-50">
              <i v-if="!isSubmittingCreate" class="fa-solid fa-check"></i>
              <span>{{ isSubmittingCreate ? 'កំពុងបន្ថែម...' : 'បន្ថែមគ្រូបង្រៀន' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal 3: Admin Review & Approve Teacher Modal -->
    <div 
      v-if="showApproveModal && selectedTeacher" 
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
              <h3 class="font-bold text-base">ត្រួតពិនិត្យ និងអនុម័តគ្រូបង្រៀន</h3>
              <p class="text-xs text-emerald-100">ពិនិត្យព័ត៌មាន បញ្ជាក់ជំនាញ និងផ្ដល់អត្តលេខគ្រូផ្លូវការ</p>
            </div>
          </div>
          <button @click="showApproveModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">✕</button>
        </div>

        <!-- Approve Body -->
        <div class="p-6 space-y-5 text-xs text-slate-700">
          <div class="flex items-center gap-4 p-4 bg-slate-50 border border-slate-200/80 rounded-2xl">
            <div v-if="selectedTeacher.photo" class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-200 border border-slate-300 flex-shrink-0">
              <img :src="selectedTeacher.photo" alt="" class="w-full h-full object-cover">
            </div>
            <div v-else class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xl flex-shrink-0">
              {{ (selectedTeacher.name || 'គ').charAt(0) }}
            </div>
            <div>
              <h4 class="text-base font-bold text-slate-900 leading-tight">{{ selectedTeacher.name }}</h4>
              <p v-if="selectedTeacher.name_latin" class="text-slate-500 font-sans tracking-wider uppercase font-semibold">
                {{ selectedTeacher.name_latin }}
              </p>
              <div class="flex flex-wrap items-center gap-2 mt-1.5 text-[11px] text-slate-500">
                <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-semibold">
                  ភេទ៖ {{ selectedTeacher.gender === 'male' || selectedTeacher.gender === 'ប្រុស' ? 'ប្រុស' : 'ស្រី' }}
                </span>
                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 font-semibold">
                  ឯកទេស៖ {{ selectedTeacher.specialization }}
                </span>
              </div>
            </div>
          </div>

          <div class="p-4 bg-blue-50/70 border border-blue-200 rounded-2xl space-y-4">
            <h5 class="font-bold text-blue-900 flex items-center gap-2">
              <i class="fa-solid fa-id-card-clip text-blue-700"></i>
              <span>ការកំណត់អត្តលេខគ្រូ និងការចាត់តាំងបង្រៀន</span>
            </h5>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block font-semibold mb-1">អត្តលេខគ្រូស្វ័យប្រវត្តិ (Teacher ID) <span class="text-red-500">*</span></label>
                <input type="text" v-model="approveForm.teacher_id_card" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl font-mono font-bold text-emerald-700 focus:outline-none">
              </div>
              <div>
                <label class="block font-semibold mb-1">មុខវិជ្ជាឯកទេស</label>
                <input type="text" v-model="approveForm.specialization" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl font-semibold text-slate-800 focus:outline-none">
              </div>
              <div class="sm:col-span-2">
                <label class="block font-semibold mb-1">ថ្នាក់ដែលចាត់តាំងបង្រៀន (ឧ. 7A, 7B, 8A)</label>
                <input type="text" v-model="approveForm.class_assigned" placeholder="ឧ. 7A, 7B" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl font-medium text-slate-800 focus:outline-none">
              </div>
            </div>
          </div>

          <p class="text-[11px] text-slate-500 italic">
            * ចំណាំ៖ នៅពេលចុច "យល់ព្រមអនុម័ត" គ្រូបង្រៀននឹងត្រូវបានប្តូរស្ថានភាពទៅ "សកម្ម" ហើយគណនីប្រើប្រាស់ប្រព័ន្ធនឹងត្រូវបានបង្កើតជូនដោយស្វ័យប្រវត្តិ។
          </p>
        </div>

        <div class="p-5 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-3">
          <button @click="showApproveModal = false" type="button" class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl font-semibold transition">បោះបង់</button>
          <button @click="submitApproval" :disabled="isSubmittingApproval" type="button" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md shadow-emerald-600/30 transition flex items-center gap-2 disabled:opacity-50">
            <svg v-if="isSubmittingApproval" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <i v-else class="fa-solid fa-check"></i>
            <span>{{ isSubmittingApproval ? 'កំពុងអនុម័ត...' : 'យល់ព្រមអនុម័ត (Approve & Enroll)' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal 4: Teacher Profile Modal -->
    <div 
      v-if="showProfileModal && selectedTeacher" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs font-khmer transition-opacity"
      @click.self="showProfileModal = false"
    >
      <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-blue-700 to-indigo-700 p-6 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-2xl">
              <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <div>
              <h3 class="font-bold text-base">ប្រវត្តិរូបគ្រូបង្រៀន (Teacher Profile)</h3>
              <p class="text-xs text-blue-100">ព័ត៌មានលម្អិត និងកាលវិភាគបង្រៀន</p>
            </div>
          </div>
          <button @click="showProfileModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">✕</button>
        </div>

        <div class="p-6 space-y-5 text-xs text-slate-700">
          <div class="flex items-center gap-4 p-4 bg-slate-50 border border-slate-200/80 rounded-2xl">
            <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 overflow-hidden shadow-xs flex-shrink-0">
              <img v-if="selectedTeacher.photo" :src="selectedTeacher.photo" class="w-full h-full object-cover">
              <i v-else class="fa-solid fa-user-tie text-3xl text-emerald-600"></i>
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <h4 class="font-bold text-base text-slate-900">{{ selectedTeacher.name }}</h4>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                  {{ selectedTeacher.specialization || 'គ្រូបង្រៀន' }}
                </span>
              </div>
              <p v-if="selectedTeacher.name_latin" class="text-xs text-slate-500 font-mono uppercase">{{ selectedTeacher.name_latin }}</p>
              <div class="mt-1 font-mono text-xs font-bold text-blue-600">{{ selectedTeacher.idCard }}</div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
            <div>
              <span class="text-slate-400 block text-[11px]">ភេទ:</span>
              <span class="font-semibold text-slate-800">{{ selectedTeacher.gender === 'male' || selectedTeacher.gender === 'ប្រុស' ? 'ប្រុស (Male)' : 'ស្រី (Female)' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">ថ្ងៃខែឆ្នាំកំណើត:</span>
              <span class="font-semibold text-slate-800">{{ selectedTeacher.date_of_birth || '-' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">កម្រិតវប្បធម៌:</span>
              <span class="font-semibold text-slate-800">{{ selectedTeacher.qualification || 'បរិញ្ញាបត្រ' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">លេខទូរស័ព្ទ:</span>
              <span class="font-semibold text-slate-800 font-mono">{{ selectedTeacher.phone || '-' }}</span>
            </div>
            <div class="col-span-2">
              <span class="text-slate-400 block text-[11px]">ថ្នាក់ដែលត្រូវចាត់តាំង:</span>
              <span class="font-bold text-blue-700">{{ selectedTeacher.class_assigned || 'មិនទាន់ចាត់តាំង' }}</span>
            </div>
            <div class="col-span-2">
              <span class="text-slate-400 block text-[11px]">អាសយដ្ឋានបច្ចុប្បន្ន:</span>
              <span class="font-semibold text-slate-800">{{ selectedTeacher.address || '-' }}</span>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button 
              @click="showProfileModal = false; openEditModal(selectedTeacher)" 
              type="button" 
              class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition"
            >
              កែប្រែ
            </button>
            <button 
              @click="showProfileModal = false" 
              type="button" 
              class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-semibold transition"
            >
              បិទ
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal 5: Edit Teacher Modal -->
    <div 
      v-if="showEditModal && selectedTeacher" 
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
              <h3 class="font-bold text-base">កែប្រែព័ត៌មានគ្រូបង្រៀន (Edit Teacher)</h3>
              <p class="text-xs text-blue-100">{{ selectedTeacher.name }} ({{ selectedTeacher.idCard }})</p>
            </div>
          </div>
          <button @click="showEditModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">✕</button>
        </div>

        <form @submit.prevent="submitEditTeacher" class="p-6 space-y-4 text-xs text-slate-700">
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
              <label class="block font-semibold mb-1">លេខទូរស័ព្ទ</label>
              <input type="tel" v-model="editForm.phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ជំនាញឯកទេស <span class="text-red-500">*</span></label>
              <input type="text" v-model="editForm.specialization" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-bold">
            </div>
            <div>
              <label class="block font-semibold mb-1">កម្រិតវប្បធម៌</label>
              <select v-model="editForm.qualification" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                <option value="បរិញ្ញាបត្រ">បរិញ្ញាបត្រ (Bachelor)</option>
                <option value="បរិញ្ញាបត្រជាន់ខ្ពស់">បរិញ្ញាបត្រជាន់ខ្ពស់ (Master)</option>
                <option value="បណ្ឌិត">បណ្ឌិត (PhD)</option>
                <option value="គរុកោសល្យមធ្យម">គរុកោសល្យមធ្យម</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">ស្ថានភាព</label>
              <select v-model="editForm.status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                <option value="active">សកម្ម (Active)</option>
                <option value="fulltime">ពេញម៉ោង (Full-Time)</option>
                <option value="parttime">ក្រៅម៉ោង (Part-Time)</option>
                <option value="leave">ច្បាប់ឈប់សម្រាក (On Leave)</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">ថ្នាក់ដែលត្រូវចាត់តាំង</label>
              <input type="text" v-model="editForm.class_assigned" placeholder="ឧ. 7A, 7B" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
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

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onActivated, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import QRCode from 'qrcode';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import StatCard from '../components/StatCard.vue';
import FilterBar from '../components/FilterBar.vue';
import DataTable from '../components/DataTable.vue';

const { t } = useI18n();
const router = useRouter();
const route = useRoute();

const activeTab = ref(route.query?.tab || 'all');
watch(() => route.query?.tab, (tab) => {
  if (tab) activeTab.value = tab;
});
const searchQuery = ref('');
const genderFilter = ref('all');

const teachers = ref([]);
const pendingCount = ref(0);
const activeCount = ref(0);
const leaveCount = ref(0);
const isLoading = ref(true);

// Hydrate from localStorage cache to show data immediately
try {
  const cachedTeachers = localStorage.getItem('pdt_cached_teachers');
  if (cachedTeachers) {
    const d = JSON.parse(cachedTeachers);
    if (d.teachers && d.teachers.length) {
      teachers.value = d.teachers;
      isLoading.value = false;
    }
    if (d.counts) {
      pendingCount.value = d.counts.pending || 0;
      activeCount.value = d.counts.active || 0;
      leaveCount.value = d.counts.leave || 0;
    }
  }
} catch (e) {}

// QR Code Modal State
const showQrModal = ref(false);
const qrDataUrl = ref('');
const isCopied = ref(false);
const publicRegistrationUrl = ref('');

// Create Teacher Modal State
const showCreateModal = ref(false);
const isSubmittingCreate = ref(false);
const createForm = ref({
  name_khmer: '',
  name_latin: '',
  gender: 'male',
  phone: '',
  specialization: 'គណិតវិទ្យា',
  qualification: 'បរិញ្ញាបត្រ',
  class_assigned: '7A, 7B',
});

// Approve Modal State
const showApproveModal = ref(false);
const selectedTeacher = ref(null);
const approveForm = ref({
  teacher_id_card: '',
  specialization: '',
  qualification: '',
  class_assigned: '',
});
const isSubmittingApproval = ref(false);

const fetchTeachers = async () => {
  const currentMode = localStorage.getItem('pdt_demo_mode') || '0';
  if (teachers.value.length === 0) {
    isLoading.value = true;
  }
  try {
    const res = await axios.get('/api/teachers', {
      params: { demo_mode: currentMode, _t: Date.now() }
    });
    if (res.data) {
      if (res.data.teachers) {
        teachers.value = res.data.teachers;
        try {
          localStorage.setItem('pdt_cached_teachers', JSON.stringify(res.data));
        } catch (e) {}
      }
      if (res.data.counts) {
        pendingCount.value = res.data.counts.pending || 0;
        activeCount.value = res.data.counts.active || 0;
        leaveCount.value = res.data.counts.leave || 0;
      } else {
        pendingCount.value = teachers.value.filter(tc => tc.status === 'pending').length;
        activeCount.value = teachers.value.filter(tc => tc.status === 'active' || tc.status === 'fulltime' || tc.status === 'parttime').length;
        leaveCount.value = teachers.value.filter(tc => tc.status === 'leave').length;
      }
    }
  } catch (e) {
    console.error('Failed to load teachers:', e);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchTeachers();
  publicRegistrationUrl.value = window.location.origin + '/register-teacher';
});

onActivated(() => {
  fetchTeachers();
});

const filteredTeachers = computed(() => {
  const result = teachers.value.filter(tc => {
    let matchesTab = true;
    if (activeTab.value === 'all') {
      matchesTab = true;
    } else if (activeTab.value === 'pending') {
      matchesTab = tc.status === 'pending';
    } else if (activeTab.value === 'active') {
      matchesTab = tc.status === 'active' || tc.status === 'fulltime' || tc.status === 'parttime';
    } else if (activeTab.value === 'leave') {
      matchesTab = tc.status === 'leave';
    }

    const s = searchQuery.value.toLowerCase().trim();
    const matchesSearch = !s || 
      (tc.name && tc.name.toLowerCase().includes(s)) ||
      (tc.name_latin && tc.name_latin.toLowerCase().includes(s)) ||
      (tc.idCard && tc.idCard.toLowerCase().includes(s)) ||
      (tc.phone && tc.phone.includes(s)) ||
      (tc.specialization && tc.specialization.toLowerCase().includes(s));

    const matchesGender = genderFilter.value === 'all' || 
      (genderFilter.value === 'male' && (tc.gender === 'male' || tc.gender === 'ប្រុស')) ||
      (genderFilter.value === 'female' && (tc.gender === 'female' || tc.gender === 'ស្រី'));

    return matchesTab && matchesSearch && matchesGender;
  });

  // Sort strictly by Khmer Alphabetical Order (អក្ខរក្រមខ្មែរ)
  return result.sort((a, b) => {
    const nameA = a.name_khmer || a.name || '';
    const nameB = b.name_khmer || b.name || '';
    return nameA.localeCompare(nameB, 'km');
  });
});

// QR Code Operations
const openQrModal = async () => {
  publicRegistrationUrl.value = window.location.origin + '/register-teacher';
  showQrModal.value = true;
  isCopied.value = false;
  try {
    qrDataUrl.value = await QRCode.toDataURL(publicRegistrationUrl.value, {
      width: 280,
      margin: 2,
      color: {
        dark: '#059669', // Emerald
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
      <title>បោះពុម្ព QR Code ចុះឈ្មោះគ្រូបង្រៀន - LMSTICK</title>
      <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600;700&display=swap" rel="stylesheet">
      <style>
        body { font-family: 'Kantumruy Pro', sans-serif; text-align: center; padding: 40px 20px; color: #1e293b; }
        .poster { max-width: 500px; margin: 0 auto; border: 3px solid #059669; border-radius: 24px; padding: 40px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .school-title { font-size: 26px; font-weight: 700; color: #059669; margin-bottom: 6px; }
        .subtitle { font-size: 16px; color: #475569; margin-bottom: 25px; }
        .qr-box { display: inline-block; padding: 15px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 20px; margin-bottom: 20px; }
        .qr-box img { width: 240px; height: 240px; display: block; }
        .instructions { font-size: 14px; color: #334155; margin-bottom: 15px; line-height: 1.6; }
        .url-text { font-size: 12px; font-family: monospace; background: #e2e8f0; padding: 8px 14px; border-radius: 10px; display: inline-block; word-break: break-all; }
        @media print { body { padding: 0; } .poster { border-color: #000; box-shadow: none; } }
      </style>
    </head>
    <body>
      <div class="poster">
        <div class="school-title">LMSTICK SCHOOL</div>
        <div class="subtitle">ទម្រង់ចុះឈ្មោះគ្រូបង្រៀនតាមអនឡាញ (Faculty Recruitment)</div>
        <div class="qr-box"><img src="${qrDataUrl.value}" alt="QR Code" /></div>
        <div class="instructions">សូមស្កេន QR Code ដើម្បីបំពេញព័ត៌មានចុះឈ្មោះចូលបម្រើការងារបង្រៀន</div>
        <div class="url-text">${publicRegistrationUrl.value}</div>
      </div>
      <script>window.onload = function() { window.print(); };<\/script>
    </body>
    </html>
  `);
  printWindow.document.close();
};

// Create Teacher Operations (Fix for "+ បន្ថែមគ្រូបង្រៀន")
const openCreateModal = () => {
  createForm.value = {
    name_khmer: '',
    name_latin: '',
    gender: 'male',
    phone: '',
    specialization: 'គណិតវិទ្យា',
    qualification: 'បរិញ្ញាបត្រ',
    class_assigned: '7A, 7B',
  };
  showCreateModal.value = true;
};

const submitCreateTeacher = async () => {
  isSubmittingCreate.value = true;
  try {
    const res = await axios.post('/api/teachers', createForm.value);
    if (res.data && res.data.success) {
      alert(res.data.message || 'បានបន្ថែមគ្រូបង្រៀនដោយជោគជ័យ!');
      showCreateModal.value = false;
      await fetchTeachers();
    }
  } catch (err) {
    console.error('Failed to create teacher:', err);
    alert(err.response?.data?.message || 'ការបន្ថែមគ្រូបង្រៀនបានបរាជ័យ');
  } finally {
    isSubmittingCreate.value = false;
  }
};

// Approval Operations
const openApproveModal = (teacher) => {
  selectedTeacher.value = teacher;
  const year = new Date().getFullYear();
  const nextNum = String(teacher.id || 1).padStart(3, '0');
  const generatedId = `TCH-${year}-${nextNum}`;

  approveForm.value = {
    teacher_id_card: generatedId,
    specialization: teacher.specialization || 'គណិតវិទ្យា',
    qualification: teacher.qualification || 'បរិញ្ញាបត្រ',
    class_assigned: teacher.class_assigned !== 'មិនទាន់ចាត់តាំង' ? teacher.class_assigned : '7A, 7B',
  };
  showApproveModal.value = true;
};

const submitApproval = async () => {
  if (!selectedTeacher.value) return;

  isSubmittingApproval.value = true;
  try {
    const res = await axios.post(`/api/teachers/${selectedTeacher.value.id}/approve`, approveForm.value);
    if (res.data && res.data.success) {
      alert(res.data.message || 'បានអនុម័តគ្រូបង្រៀនដោយជោគជ័យ!');
      showApproveModal.value = false;
      await fetchTeachers();
    }
  } catch (err) {
    console.error('Teacher approval failed:', err);
    alert(err.response?.data?.message || 'ការអនុម័តបានបរាជ័យ');
  } finally {
    isSubmittingApproval.value = false;
  }
};

const handlePrint = () => window.print();
const handleDownload = () => window.location.href = '/api/settings/backup';

// Profile Modal
const showProfileModal = ref(false);
const openProfileModal = (teacher) => {
  selectedTeacher.value = teacher;
  showProfileModal.value = true;
};

// Edit Teacher
const showEditModal = ref(false);
const isSubmittingEdit = ref(false);
const editForm = ref({
  name_khmer: '',
  name_latin: '',
  gender: 'male',
  specialization: '',
  qualification: 'បរិញ្ញាបត្រ',
  phone: '',
  class_assigned: '',
  address: '',
  status: 'active',
});

const openEditModal = (teacher) => {
  selectedTeacher.value = teacher;
  editForm.value = {
    name_khmer: teacher.name_khmer || teacher.name,
    name_latin: teacher.name_latin || '',
    gender: teacher.gender === 'female' || teacher.gender === 'ស្រី' ? 'female' : 'male',
    specialization: teacher.specialization || '',
    qualification: teacher.qualification || 'បរិញ្ញាបត្រ',
    phone: teacher.phone !== '-' ? teacher.phone : '',
    class_assigned: teacher.class_assigned !== 'មិនទាន់ចាត់តាំង' ? teacher.class_assigned : '',
    address: teacher.address || '',
    status: teacher.status || 'active',
  };
  showEditModal.value = true;
};

const submitEditTeacher = async () => {
  if (!selectedTeacher.value) return;
  isSubmittingEdit.value = true;
  try {
    const res = await axios.put(`/api/teachers/${selectedTeacher.value.id}`, editForm.value);
    if (res.data && res.data.success) {
      alert(res.data.message);
      showEditModal.value = false;
      await fetchTeachers();
    }
  } catch (err) {
    console.error('Error editing teacher:', err);
    alert('មានបញ្ហាក្នុងការកែប្រែព័ត៌មានគ្រូបង្រៀន។');
  } finally {
    isSubmittingEdit.value = false;
  }
};

// Delete Teacher
const confirmDeleteTeacher = async (teacher) => {
  if (!confirm(`តើអ្នកពិតជាចង់លុបគ្រូបង្រៀន "${teacher.name}" (អត្តលេខ: ${teacher.idCard}) មែនទេ?`)) return;
  try {
    const res = await axios.delete(`/api/teachers/${teacher.id}`);
    if (res.data && res.data.success) {
      alert(res.data.message);
      await fetchTeachers();
    }
  } catch (err) {
    console.error('Delete teacher failed:', err);
    alert('មិនអាចលុបទិន្នន័យបានទេ។');
  }
};
</script>
