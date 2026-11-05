<template>
  <div class="space-y-6 font-khmer">
    <!-- Header with Extra Actions Slot for Link / QR Code -->
    <PageHeader 
      :title="t('parents.title')" 
      :breadcrumb="t('nav.messaging')" 
      :actionLabel="t('parents.registerParent')"
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
          <span>🔗 Link / QR ចុះឈ្មោះអាណាព្យាបាល</span>
        </button>
      </template>
    </PageHeader>

    <!-- Sub Tabs including Pending Status with Badge -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'all', label: t('common.all') },
        { id: 'pending', label: 'រង់ចាំអនុម័ត', badge: pendingCount, badgeColor: 'bg-amber-100 text-amber-800' },
        { id: 'active', label: t('common.active') }
      ]"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="t('parents.registeredParents')" :value="parents.length" />
      <StatCard :title="t('common.active')" :value="activeCount" />
      <StatCard title="រង់ចាំអនុម័ត" :value="pendingCount" />
      <StatCard :title="t('parents.linkedChildren')" :value="totalChildrenCount" />
    </div>

    <!-- Filter Bar -->
    <FilterBar>
      <template #left>
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="`${t('common.search')} ឈ្មោះអាណាព្យាបាល, កូន, លេខទូរស័ព្ទ...`"
          class="px-3.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs w-72 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 font-khmer"
        />
      </template>

      <template #right>
        <div class="inline-flex items-center gap-3 px-3.5 py-1.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs">
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
            <input type="radio" v-model="relationFilter" value="all" class="text-emerald-600 focus:ring-0">
            <span>{{ t('common.all') }}</span>
          </label>
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
            <input type="radio" v-model="relationFilter" value="father" class="text-emerald-600 focus:ring-0">
            <span>ឪពុក</span>
          </label>
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
            <input type="radio" v-model="relationFilter" value="mother" class="text-emerald-600 focus:ring-0">
            <span>ម្តាយ</span>
          </label>
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
            <input type="radio" v-model="relationFilter" value="guardian" class="text-emerald-600 focus:ring-0">
            <span>អាណាព្យាបាល</span>
          </label>
        </div>
      </template>
    </FilterBar>

    <!-- Data Table -->
    <DataTable>
      <template #header>
        <tr>
          <th class="py-3 px-4 font-bold text-slate-700">អត្តលេខ</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('parents.parentName') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">ទំនាក់ទំនង</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('students.phone') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ t('parents.occupation') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">ព័ត៌មានកូនៗ (Children)</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ t('common.status') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ t('common.actions') }}</th>
        </tr>
      </template>

      <template #body>
        <tr v-if="filteredParents.length === 0">
          <td colspan="8" class="py-10 text-center text-slate-400 font-khmer">{{ t('common.noData') }}</td>
        </tr>
        <tr v-for="p in filteredParents" :key="p.id" class="hover:bg-slate-50/70 transition-colors">
          <!-- Parent ID -->
          <td class="py-3 px-4 font-mono font-bold text-emerald-600 text-xs">
            {{ p.idCard }}
          </td>

          <!-- Parent Name -->
          <td class="py-3 px-4">
            <div class="font-bold text-slate-900">{{ p.name }}</div>
            <div v-if="p.name_latin" class="text-[11px] text-slate-400 font-mono uppercase">{{ p.name_latin }}</div>
          </td>

          <!-- Relationship -->
          <td class="py-3 px-4">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
              {{ p.relationship || 'អាណាព្យាបាល' }}
            </span>
          </td>

          <!-- Phone & Email -->
          <td class="py-3 px-4">
            <div class="text-slate-700 font-mono text-xs font-semibold">{{ p.phone }}</div>
            <div v-if="p.email && p.email !== '-'" class="text-[11px] text-slate-400">{{ p.email }}</div>
          </td>

          <!-- Occupation -->
          <td class="py-3 px-4 text-slate-600 text-xs">
            {{ p.occupation }}
          </td>

          <!-- Children -->
          <td class="py-3 px-4">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
              <i class="fa-solid fa-graduation-cap text-[10px]"></i>
              {{ p.children }}
            </span>
          </td>

          <!-- Status Badge -->
          <td class="py-3 px-4 text-center">
            <span 
              v-if="p.status === 'pending'" 
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 animate-pulse border border-amber-300"
            >
              <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
              <span>រង់ចាំអនុម័ត</span>
            </span>
            <span 
              v-else 
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800"
            >
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              <span>សកម្ម</span>
            </span>
          </td>

          <!-- Actions -->
          <td class="py-3 px-4 text-right space-x-1.5">
            <!-- Review & Approve button for pending status -->
            <button 
              v-if="p.status === 'pending'" 
              @click="openApproveModal(p)"
              type="button"
              class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-xs transition inline-flex items-center gap-1"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
              <span>អនុម័ត</span>
            </button>

            <button 
              @click="openProfileModal(p)" 
              class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs hover:bg-slate-200 transition"
            >
              {{ t('attendance.tabs.profile') }}
            </button>
            <button 
              @click="openEditModal(p)" 
              class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition"
            >
              {{ t('common.edit') }}
            </button>
            <button 
              @click="confirmDeleteParent(p)" 
              class="px-2 py-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg text-xs transition" 
              title="លុប"
            >
              <i class="fa-solid fa-trash-can"></i>
            </button>
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
              <h3 class="font-bold text-base">Link / QR Code ចុះឈ្មោះអាណាព្យាបាល</h3>
              <p class="text-xs text-emerald-100">សម្រាប់ចែករំលែកទៅកាន់មាតាបិតា ឬអាណាព្យាបាលសិស្ស</p>
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
              LMSTICK PARENT - ស្កេនដើម្បីចុះឈ្មោះ
            </div>
            <div class="w-48 h-48 mx-auto bg-white p-2 rounded-xl border border-slate-200 shadow-xs flex items-center justify-center">
              <img v-if="qrDataUrl" :src="qrDataUrl" alt="QR Code Parent Registration" class="w-full h-full object-contain" />
              <div v-else class="text-slate-400 text-xs">កំពុងបង្កើត QR Code...</div>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">ប្រើទូរស័ព្ទដៃដើម្បីស្កេនបំពេញទម្រង់ចុះឈ្មោះ</p>
          </div>

          <!-- Shareable URL Input with Copy Button -->
          <div class="text-left space-y-1.5">
            <label class="text-xs font-semibold text-slate-700">តំណភ្ជាប់ទម្រង់ចុះឈ្មោះ (Parent Registration Link):</label>
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

    <!-- Modal 2: Admin Quick Create Parent Modal -->
    <div 
      v-if="showCreateModal" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs font-khmer transition-opacity"
      @click.self="showCreateModal = false"
    >
      <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 p-5 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
              <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
              <h3 class="font-bold text-base">បន្ថែមអាណាព្យាបាលថ្មី (Add New Parent)</h3>
              <p class="text-xs text-emerald-100">បញ្ចូលព័ត៌មានមាតាបិតាផ្ទាល់ និងភ្ជាប់ជាមួយសិស្ស</p>
            </div>
          </div>
          <button @click="showCreateModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">✕</button>
        </div>

        <form @submit.prevent="submitCreateParent" class="p-6 space-y-4 text-xs text-slate-700">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះពេញជាភាសាខ្មែរ <span class="text-red-500">*</span></label>
              <input type="text" v-model="createForm.name_khmer" required placeholder="ឧ. សុខ វិបុល" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះជាអក្សរឡាតាំង (Latin Name)</label>
              <input type="text" v-model="createForm.name_latin" placeholder="e.g. SOK VIBOL" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ភេទ <span class="text-red-500">*</span></label>
              <select v-model="createForm.gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                <option value="male">ប្រុស (Male)</option>
                <option value="female">ស្រី (Female)</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">ទំនាក់ទំនងត្រូវជា <span class="text-red-500">*</span></label>
              <select v-model="createForm.relationship" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 font-medium">
                <option value="ឪពុក">ឪពុក (Father)</option>
                <option value="ម្តាយ">ម្តាយ (Mother)</option>
                <option value="អាណាព្យាបាល">អាណាព្យាបាល (Guardian)</option>
                <option value="បងប្អូនបង្កើត">បងប្អូនបង្កើត (Sibling)</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">លេខទូរស័ព្ទ <span class="text-red-500">*</span></label>
              <input type="tel" v-model="createForm.phone" required placeholder="012 345 678" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">អ៊ីមែល (Email)</label>
              <input type="email" v-model="createForm.email" placeholder="parent@example.com" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">មុខរបរ (Occupation)</label>
              <input type="text" v-model="createForm.occupation" placeholder="ឧ. អាជីវករ, មន្ត្រីរាជការ..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះសិស្ស (កូន) <span class="text-red-500">*</span></label>
              <input type="text" v-model="createForm.child_name" required placeholder="ឧ. សុខ សុភក្ត្រ" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 font-bold text-slate-800">
            </div>
            <div class="sm:col-span-2">
              <label class="block font-semibold mb-1">ថ្នាក់រៀនរបស់សិស្ស (ឧ. 7A, 8B)</label>
              <input type="text" v-model="createForm.child_grade" placeholder="ឧ. 7A, 8B" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div class="sm:col-span-2">
              <label class="block font-semibold mb-1">អាសយដ្ឋានបច្ចុប្បន្ន</label>
              <textarea v-model="createForm.address" rows="2" placeholder="ភូមិ/ឃុំ/ស្រុក/ខេត្ត..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20"></textarea>
            </div>
          </div>

          <div class="p-5 bg-slate-50 border-t border-slate-200 -mx-6 -mb-6 mt-4 flex items-center justify-end gap-3">
            <button @click="showCreateModal = false" type="button" class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl font-semibold transition">បោះបង់</button>
            <button :disabled="isSubmittingCreate" type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md shadow-emerald-600/30 transition flex items-center gap-2 disabled:opacity-50">
              <i v-if="!isSubmittingCreate" class="fa-solid fa-check"></i>
              <span>{{ isSubmittingCreate ? 'កំពុងបន្ថែម...' : 'បន្ថែមអាណាព្យាបាល' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal 3: Admin Review & Approve Parent Modal -->
    <div 
      v-if="showApproveModal && selectedParent" 
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
              <h3 class="font-bold text-base">ត្រួតពិនិត្យ និងអនុម័តអាណាព្យាបាល</h3>
              <p class="text-xs text-emerald-100">ពិនិត្យព័ត៌មាន បញ្ជាក់ការភ្ជាប់កូន និងផ្ដល់អត្តលេខផ្លូវការ</p>
            </div>
          </div>
          <button @click="showApproveModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">✕</button>
        </div>

        <!-- Approve Body -->
        <div class="p-6 space-y-5 text-xs text-slate-700">
          
          <!-- Parent Profile Summary -->
          <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 overflow-hidden shadow-xs flex-shrink-0">
              <img v-if="selectedParent.photo" :src="selectedParent.photo" class="w-full h-full object-cover">
              <i v-else class="fa-solid fa-user-tie text-2xl text-slate-400"></i>
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <h4 class="font-bold text-sm text-slate-900">{{ selectedParent.name }}</h4>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                  {{ selectedParent.relationship || 'អាណាព្យាបាល' }}
                </span>
              </div>
              <p v-if="selectedParent.name_latin" class="text-[11px] text-slate-500 font-mono">{{ selectedParent.name_latin }}</p>
              <div class="flex items-center gap-3 text-slate-500 mt-1">
                <span><i class="fa-solid fa-phone text-[10px] mr-1"></i> {{ selectedParent.phone }}</span>
                <span><i class="fa-solid fa-briefcase text-[10px] mr-1"></i> {{ selectedParent.occupation }}</span>
              </div>
            </div>
          </div>

          <!-- Child Info Note -->
          <div class="p-3.5 bg-blue-50/80 border border-blue-200 rounded-xl text-blue-800">
            <div class="font-bold flex items-center gap-1.5 mb-1">
              <i class="fa-solid fa-child text-blue-600"></i>
              <span>ព័ត៌មានសិស្សស្នើសុំភ្ជាប់៖</span>
            </div>
            <p class="text-slate-600">
              ឈ្មោះសិស្ស: <strong class="text-blue-900">{{ selectedParent.child_name || 'មិនបានបញ្ជាក់' }}</strong> 
              <span v-if="selectedParent.child_grade"> (ថ្នាក់: <strong>{{ selectedParent.child_grade }}</strong>)</span>
            </p>
          </div>

          <!-- Official Assignment Form -->
          <div class="space-y-3 pt-2">
            <div>
              <label class="block font-semibold mb-1 text-slate-800">
                អត្តលេខអាណាព្យាបាលផ្លូវការ (Parent ID Card) <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                v-model="approveForm.parent_id_card" 
                placeholder="ឧ. PAR-2026-001" 
                class="w-full px-3.5 py-2.5 bg-emerald-50/50 border border-emerald-300 rounded-xl font-mono font-bold text-emerald-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
              >
              <p class="text-[11px] text-slate-400 mt-1">ប្រព័ន្ធបានបង្កើតអត្តលេខស្វ័យប្រវត្តិតាមលំដាប់លំដោយ។ អ្នកអាចកែប្រែបាន។</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold mb-1">ទំនាក់ទំនងផ្លូវការ</label>
                <select v-model="approveForm.relationship" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                  <option value="ឪពុក">ឪពុក</option>
                  <option value="ម្តាយ">ម្តាយ</option>
                  <option value="អាណាព្យាបាល">អាណាព្យាបាល</option>
                  <option value="បងប្អូនបង្កើត">បងប្អូនបង្កើត</option>
                </select>
              </div>
              <div>
                <label class="block font-semibold mb-1">បញ្ជាក់ថ្នាក់រៀនរបស់កូន</label>
                <input type="text" v-model="approveForm.child_grade" placeholder="ឧ. 7A" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
              </div>
            </div>
          </div>

          <!-- Approve Buttons -->
          <div class="p-5 bg-slate-50 border-t border-slate-200 -mx-6 -mb-6 mt-4 flex items-center justify-end gap-3">
            <button @click="showApproveModal = false" type="button" class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl font-semibold transition">បោះបង់</button>
            <button 
              :disabled="isSubmittingApproval" 
              @click="submitApproveParent" 
              type="button" 
              class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md shadow-emerald-600/30 transition flex items-center gap-2 disabled:opacity-50"
            >
              <svg v-if="!isSubmittingApproval" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
              <span>{{ isSubmittingApproval ? 'កំពុងអនុម័ត...' : 'បញ្ជាក់ការអនុម័ត' }}</span>
            </button>
          </div>

        </div>
      </div>
    </div>

    <!-- Modal 4: Parent Profile Modal -->
    <div 
      v-if="showProfileModal && selectedParent" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs font-khmer transition-opacity"
      @click.self="showProfileModal = false"
    >
      <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-emerald-700 to-teal-800 p-6 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-2xl">
              <i class="fa-solid fa-user-shield"></i>
            </div>
            <div>
              <h3 class="font-bold text-base">ប្រវត្តិរូបអាណាព្យាបាល (Parent Profile)</h3>
              <p class="text-xs text-emerald-100">ព័ត៌មានលម្អិត និងកូនៗដែលបានភ្ជាប់</p>
            </div>
          </div>
          <button @click="showProfileModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">✕</button>
        </div>

        <div class="p-6 space-y-5 text-xs text-slate-700">
          <div class="flex items-center gap-4 p-4 bg-slate-50 border border-slate-200/80 rounded-2xl">
            <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 overflow-hidden shadow-xs flex-shrink-0">
              <img v-if="selectedParent.photo" :src="selectedParent.photo" class="w-full h-full object-cover">
              <i v-else class="fa-solid fa-user-tie text-3xl text-emerald-600"></i>
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <h4 class="font-bold text-base text-slate-900">{{ selectedParent.name }}</h4>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                  {{ selectedParent.relationship || 'អាណាព្យាបាល' }}
                </span>
              </div>
              <p v-if="selectedParent.name_latin" class="text-xs text-slate-500 font-mono uppercase">{{ selectedParent.name_latin }}</p>
              <div class="mt-1 font-mono text-xs font-bold text-emerald-600">{{ selectedParent.idCard }}</div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
            <div>
              <span class="text-slate-400 block text-[11px]">ភេទ:</span>
              <span class="font-semibold text-slate-800">{{ selectedParent.gender === 'male' || selectedParent.gender === 'ប្រុស' ? 'ប្រុស (Male)' : 'ស្រី (Female)' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">លេខទូរស័ព្ទ:</span>
              <span class="font-semibold text-slate-800 font-mono">{{ selectedParent.phone || '-' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">អ៊ីមែល:</span>
              <span class="font-semibold text-slate-800">{{ selectedParent.email || '-' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">មុខរបរ:</span>
              <span class="font-semibold text-slate-800">{{ selectedParent.occupation || '-' }}</span>
            </div>
            <div class="col-span-2">
              <span class="text-slate-400 block text-[11px]">កូន/សិស្សក្នុងបន្ទុក:</span>
              <span class="font-bold text-emerald-700 text-sm">{{ selectedParent.children || '-' }}</span>
            </div>
            <div class="col-span-2">
              <span class="text-slate-400 block text-[11px]">អាសយដ្ឋានបច្ចុប្បន្ន:</span>
              <span class="font-semibold text-slate-800">{{ selectedParent.address || '-' }}</span>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button 
              @click="showProfileModal = false; openEditModal(selectedParent)" 
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

    <!-- Modal 5: Edit Parent Modal -->
    <div 
      v-if="showEditModal && selectedParent" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs font-khmer transition-opacity"
      @click.self="showEditModal = false"
    >
      <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 p-5 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
              <i class="fa-solid fa-user-pen"></i>
            </div>
            <div>
              <h3 class="font-bold text-base">កែប្រែព័ត៌មានអាណាព្យាបាល (Edit Parent)</h3>
              <p class="text-xs text-emerald-100">{{ selectedParent.name }} ({{ selectedParent.idCard }})</p>
            </div>
          </div>
          <button @click="showEditModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">✕</button>
        </div>

        <form @submit.prevent="submitEditParent" class="p-6 space-y-4 text-xs text-slate-700">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះពេញជាភាសាខ្មែរ <span class="text-red-500">*</span></label>
              <input type="text" v-model="editForm.name_khmer" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះជាអក្សរឡាតាំង (Latin Name)</label>
              <input type="text" v-model="editForm.name_latin" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">ភេទ <span class="text-red-500">*</span></label>
              <select v-model="editForm.gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                <option value="male">ប្រុស (Male)</option>
                <option value="female">ស្រី (Female)</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">ទំនាក់ទំនងត្រូវជា <span class="text-red-500">*</span></label>
              <select v-model="editForm.relationship" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                <option value="ឪពុក">ឪពុក</option>
                <option value="ម្តាយ">ម្តាយ</option>
                <option value="អាណាព្យាបាល">អាណាព្យាបាល</option>
                <option value="បងប្អូនបង្កើត">បងប្អូនបង្កើត</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">លេខទូរស័ព្ទ <span class="text-red-500">*</span></label>
              <input type="tel" v-model="editForm.phone" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div>
              <label class="block font-semibold mb-1">អ៊ីមែល (Email)</label>
              <input type="email" v-model="editForm.email" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
            </div>
            <div>
              <label class="block font-semibold mb-1">មុខរបរ (Occupation)</label>
              <input type="text" v-model="editForm.occupation" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
            </div>
            <div>
              <label class="block font-semibold mb-1">ស្ថានភាព</label>
              <select v-model="editForm.status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                <option value="active">សកម្ម (Active)</option>
                <option value="pending">រង់ចាំអនុម័ត (Pending)</option>
                <option value="inactive">អសកម្ម (Inactive)</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">ឈ្មោះកូន/សិស្ស</label>
              <input type="text" v-model="editForm.child_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
            </div>
            <div>
              <label class="block font-semibold mb-1">ថ្នាក់រៀនរបស់កូន</label>
              <input type="text" v-model="editForm.child_grade" placeholder="ឧ. 7A" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
            </div>
            <div class="sm:col-span-2">
              <label class="block font-semibold mb-1">អាសយដ្ឋានបច្ចុប្បន្ន</label>
              <input type="text" v-model="editForm.address" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
            </div>
          </div>

          <div class="p-5 bg-slate-50 border-t border-slate-200 -mx-6 -mb-6 mt-4 flex items-center justify-end gap-3">
            <button @click="showEditModal = false" type="button" class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl font-semibold transition">បោះបង់</button>
            <button :disabled="isSubmittingEdit" type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md shadow-emerald-600/30 transition flex items-center gap-2 disabled:opacity-50">
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
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import axios from 'axios';
import QRCode from 'qrcode';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import StatCard from '../components/StatCard.vue';
import FilterBar from '../components/FilterBar.vue';
import DataTable from '../components/DataTable.vue';

const { t } = useI18n();
const router = useRouter();

const activeTab = ref('all');
const searchQuery = ref('');
const relationFilter = ref('all');

const parents = ref([]);
const pendingCount = ref(0);
const activeCount = ref(0);

// QR Code Modal State
const showQrModal = ref(false);
const qrDataUrl = ref('');
const isCopied = ref(false);
const publicRegistrationUrl = ref('');

// Create Parent Modal State
const showCreateModal = ref(false);
const isSubmittingCreate = ref(false);
const createForm = ref({
  name_khmer: '',
  name_latin: '',
  gender: 'male',
  relationship: 'ឪពុក',
  phone: '',
  email: '',
  occupation: '',
  address: '',
  child_name: '',
  child_grade: '',
});

// Approve Modal State
const showApproveModal = ref(false);
const selectedParent = ref(null);
const approveForm = ref({
  parent_id_card: '',
  relationship: '',
  child_grade: '',
});
const isSubmittingApproval = ref(false);

const fetchParents = async () => {
  try {
    const res = await axios.get('/api/parents');
    if (res.data) {
      if (res.data.parents) {
        parents.value = res.data.parents;
      }
      if (res.data.counts) {
        pendingCount.value = res.data.counts.pending || 0;
        activeCount.value = res.data.counts.active || 0;
      } else {
        pendingCount.value = parents.value.filter(p => p.status === 'pending').length;
        activeCount.value = parents.value.filter(p => p.status === 'active').length;
      }
    }
  } catch (e) {
    console.error('Failed to load parents:', e);
  }
};

onMounted(() => {
  fetchParents();
  publicRegistrationUrl.value = window.location.origin + '/register-parent';
});

const totalChildrenCount = computed(() => {
  return parents.value.reduce((acc, p) => acc + (p.children_count || 1), 0);
});

const filteredParents = computed(() => {
  return parents.value.filter(p => {
    let matchesTab = true;
    if (activeTab.value === 'all') {
      matchesTab = true;
    } else if (activeTab.value === 'pending') {
      matchesTab = p.status === 'pending';
    } else if (activeTab.value === 'active') {
      matchesTab = p.status === 'active';
    }

    const q = searchQuery.value.toLowerCase().trim();
    const matchesSearch = !q || 
      (p.name && p.name.toLowerCase().includes(q)) ||
      (p.name_latin && p.name_latin.toLowerCase().includes(q)) ||
      (p.idCard && p.idCard.toLowerCase().includes(q)) ||
      (p.phone && p.phone.includes(q)) ||
      (p.email && p.email.toLowerCase().includes(q)) ||
      (p.occupation && p.occupation.toLowerCase().includes(q)) ||
      (p.children && p.children.toLowerCase().includes(q)) ||
      (p.child_name && p.child_name.toLowerCase().includes(q));

    let matchesRelation = true;
    if (relationFilter.value === 'father') {
      matchesRelation = p.relationship && p.relationship.includes('ឪពុក');
    } else if (relationFilter.value === 'mother') {
      matchesRelation = p.relationship && p.relationship.includes('ម្តាយ');
    } else if (relationFilter.value === 'guardian') {
      matchesRelation = p.relationship && (p.relationship.includes('អាណាព្យាបាល') || p.relationship.includes('បងប្អូន'));
    }

    return matchesTab && matchesSearch && matchesRelation;
  });
});

// QR Code Operations
const openQrModal = async () => {
  publicRegistrationUrl.value = window.location.origin + '/register-parent';
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
      <title>បោះពុម្ព QR Code ចុះឈ្មោះអាណាព្យាបាល</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600;700&display=swap" rel="stylesheet">
      <style>
        body {
          font-family: 'Kantumruy Pro', sans-serif;
          display: flex;
          align-items: center;
          justify-content: center;
          min-height: 100vh;
          margin: 0;
          background: #f8fafc;
        }
        .card {
          border: 2px dashed #059669;
          padding: 40px;
          text-align: center;
          border-radius: 24px;
          background: white;
          max-width: 420px;
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        h2 { margin: 0 0 8px 0; color: #0f172a; font-size: 20px; }
        p { color: #64748b; font-size: 13px; margin: 0 0 24px 0; }
        img { width: 220px; height: 220px; border-radius: 12px; }
        .url {
          margin-top: 20px;
          padding: 10px 14px;
          background: #ecfdf5;
          border-radius: 10px;
          font-size: 11px;
          font-family: monospace;
          color: #047857;
          word-break: break-all;
        }
      </style>
    </head>
    <body>
      <div class="card">
        <h2>ទម្រង់ចុះឈ្មោះអាណាព្យាបាលតាមអនឡាញ</h2>
        <p>ស្កេន QR Code ខាងក្រោម ដើម្បីបំពេញព័ត៌មានភ្ជាប់គណនី</p>
        <img src="${qrDataUrl.value}" alt="QR Code" />
        <div class="url">${publicRegistrationUrl.value}</div>
      </div>
      <script>
        window.onload = function() {
          window.print();
        };
      <\/script>
    </body>
    </html>
  `);
  printWindow.document.close();
};

// Create Parent Operations
const openCreateModal = () => {
  createForm.value = {
    name_khmer: '',
    name_latin: '',
    gender: 'male',
    relationship: 'ឪពុក',
    phone: '',
    email: '',
    occupation: '',
    address: '',
    child_name: '',
    child_grade: '',
  };
  showCreateModal.value = true;
};

const submitCreateParent = async () => {
  isSubmittingCreate.value = true;
  try {
    const res = await axios.post('/api/parents', createForm.value);
    if (res.data && res.data.success) {
      alert(res.data.message);
      showCreateModal.value = false;
      await fetchParents();
    }
  } catch (err) {
    console.error('Error creating parent:', err);
    alert('មានបញ្ហាក្នុងការបង្កើតអាណាព្យាបាល សូមពិនិត្យព័ត៌មានឡើងវិញ។');
  } finally {
    isSubmittingCreate.value = false;
  }
};

// Approve Parent Operations
const openApproveModal = (p) => {
  selectedParent.value = p;
  const year = new Date().getFullYear();
  let nextId = `PAR-${year}-${String(parents.value.length + 1).padStart(3, '0')}`;
  
  approveForm.value = {
    parent_id_card: p.idCard && p.idCard !== 'PAR-រង់ចាំ' ? p.idCard : nextId,
    relationship: p.relationship || 'អាណាព្យាបាល',
    child_grade: p.child_grade || '',
  };
  showApproveModal.value = true;
};

const submitApproveParent = async () => {
  if (!selectedParent.value) return;
  isSubmittingApproval.value = true;
  try {
    const res = await axios.post(`/api/parents/${selectedParent.value.id}/approve`, approveForm.value);
    if (res.data && res.data.success) {
      alert(res.data.message);
      showApproveModal.value = false;
      await fetchParents();
    }
  } catch (err) {
    console.error('Approval failed:', err);
    const msg = err.response?.data?.message || 'ការអនុម័តមិនជោគជ័យ សូមព្យាយាមម្តងទៀត។';
    alert(msg);
  } finally {
    isSubmittingApproval.value = false;
  }
};

const handlePrint = () => window.print();
const handleDownload = () => alert(t('common.download'));

// Profile Modal
const showProfileModal = ref(false);
const openProfileModal = (p) => {
  selectedParent.value = p;
  showProfileModal.value = true;
};

// Edit Parent
const showEditModal = ref(false);
const isSubmittingEdit = ref(false);
const editForm = ref({
  name_khmer: '',
  name_latin: '',
  gender: 'male',
  relationship: 'ឪពុក',
  phone: '',
  email: '',
  occupation: '',
  address: '',
  child_name: '',
  child_grade: '',
  status: 'active',
});

const openEditModal = (p) => {
  selectedParent.value = p;
  editForm.value = {
    name_khmer: p.name_khmer || p.name,
    name_latin: p.name_latin || '',
    gender: p.gender === 'female' || p.gender === 'ស្រី' ? 'female' : 'male',
    relationship: p.relationship || 'អាណាព្យាបាល',
    phone: p.phone !== '-' ? p.phone : '',
    email: p.email !== '-' ? p.email : '',
    occupation: p.occupation || '',
    address: p.address !== '-' ? p.address : '',
    child_name: p.child_name || '',
    child_grade: p.child_grade || '',
    status: p.status || 'active',
  };
  showEditModal.value = true;
};

const submitEditParent = async () => {
  if (!selectedParent.value) return;
  isSubmittingEdit.value = true;
  try {
    const res = await axios.put(`/api/parents/${selectedParent.value.id}`, editForm.value);
    if (res.data && res.data.success) {
      alert(res.data.message);
      showEditModal.value = false;
      await fetchParents();
    }
  } catch (err) {
    console.error('Error editing parent:', err);
    alert('មានបញ្ហាក្នុងការកែប្រែព័ត៌មានអាណាព្យាបាល។');
  } finally {
    isSubmittingEdit.value = false;
  }
};

// Delete Parent
const confirmDeleteParent = async (p) => {
  if (!confirm(`តើអ្នកពិតជាចង់លុបអាណាព្យាបាល "${p.name}" (អត្តលេខ: ${p.idCard}) មែនទេ?`)) return;
  try {
    const res = await axios.delete(`/api/parents/${p.id}`);
    if (res.data && res.data.success) {
      alert(res.data.message);
      await fetchParents();
    }
  } catch (err) {
    console.error('Delete parent failed:', err);
    alert('មិនអាចលុបទិន្នន័យបានទេ។');
  }
};
</script>
