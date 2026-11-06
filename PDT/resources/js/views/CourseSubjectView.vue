<template>
  <div class="space-y-6">
    <!-- Header -->
    <PageHeader 
      :title="$t('subjects.title')" 
      :breadcrumb="$t('nav.inventory')" 
      :actionLabel="$t('subjects.addSubject')"
      @action="openCreateModal"
    />

    <!-- Sub Tabs -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'all', label: `ទាំងអស់ (${subjectsList.length})` },
        { id: 'core', label: '⭐ មុខវិជ្ជាគោល' },
        { id: 'science', label: '🔬 វិទ្យាសាស្ត្រពិត' },
        { id: 'social', label: '🌍 វិទ្យាសាស្ត្រសង្គម' },
        { id: 'languages', label: '🗣️ ភាសា' },
        { id: 'elective', label: '🎨 មុខវិជ្ជាជ្រើសរើស' }
      ]"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="$t('subjects.totalSubjects')" :value="subjectsList.length" />
      <StatCard title="ក្រេឌីតសម្រាប់ចែកសរុប" :value="totalCreditsDivisor" />
      <StatCard title="ម៉ោងបង្រៀនសរុប/សប្តាហ៍" :value="totalHours" />
      <StatCard :title="$t('classes.gradeLevel')" :value="'៧ ដល់ ១២'" />
    </div>

    <!-- Filter Bar -->
    <FilterBar>
      <template #left>
        <input 
          v-model="searchQuery"
          type="text"
          :placeholder="`${$t('common.search')} ${$t('subjects.title')} (ឈ្មោះ, កូដ)...`"
          class="px-3.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs w-64 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
        />
      </template>
      <template #right>
        <span class="text-xs text-slate-500">{{ $t('subjects.totalSubjects') }}: <strong>{{ filteredSubjects.length }}</strong></span>
      </template>
    </FilterBar>

    <!-- Data Table -->
    <DataTable :loading="isLoading" :empty="filteredSubjects.length === 0" :colSpan="9" loadingText="កំពុងទាញយកទិន្នន័យមុខវិជ្ជា...">
      <template #header>
        <tr>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('subjects.subjectCode') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('subjects.khmerTitle') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">ប្រភេទមុខវិជ្ជា</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">ពិន្ទុអតិប្បរមា</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">ក្រេឌីត (ចែក)</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">ម៉ោងបង្រៀន</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">ការកំណត់តាមថ្នាក់</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('common.status') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('common.actions') }}</th>
        </tr>
      </template>

      <template #body>
        <tr v-if="filteredSubjects.length === 0">
          <td colspan="9" class="py-10 text-center text-slate-400">{{ $t('common.noData') }}</td>
        </tr>
        <tr v-for="s in filteredSubjects" :key="s.id || s.code" class="hover:bg-slate-50/70 transition-colors">
          <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ s.code }}</td>
          <td class="py-3 px-4 font-bold text-slate-900">
            <div>{{ s.nameKh || s.name }}</div>
            <div class="text-[11px] text-slate-400 font-normal truncate max-w-[200px]" :title="s.nameEn || s.description || ''">
              {{ s.nameEn || s.description || '-' }}
            </div>
          </td>
          <td class="py-3 px-4">
            <span :class="[
              'px-2.5 py-1 rounded-lg text-[11px] font-bold inline-flex items-center gap-1.5',
              s.type === 'science' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
              s.type === 'languages' ? 'bg-purple-50 text-purple-700 border border-purple-200' :
              s.type === 'social' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
              s.type === 'core' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
              s.type === 'elective' ? 'bg-teal-50 text-teal-700 border border-teal-200' :
              'bg-slate-100 text-slate-700 border border-slate-200'
            ]">
              <span class="w-1.5 h-1.5 rounded-full" :class="[
                s.type === 'science' ? 'bg-blue-500' :
                s.type === 'languages' ? 'bg-purple-500' :
                s.type === 'social' ? 'bg-amber-500' :
                s.type === 'core' ? 'bg-emerald-500' :
                s.type === 'elective' ? 'bg-teal-500' :
                'bg-slate-400'
              ]"></span>
              {{ s.typeLabel || 'ទូទៅ' }}
            </span>
          </td>
          
          <!-- Max Score Column -->
          <td class="py-3 px-4 text-center">
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-xs">
              {{ s.max_score || 50 }} ពិន្ទុ
            </span>
          </td>

          <!-- Credit / Divisor Column -->
          <td class="py-3 px-4 text-center">
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200 shadow-xs" title="ក្រេឌីត / មេគុណសម្រាប់ចែកមធ្យមភាគ">
              {{ s.credit || 1 }} ក្រេឌីត
            </span>
          </td>

          <!-- Teaching Hours Column -->
          <td class="py-3 px-4 text-center font-semibold text-slate-700 text-xs">
            {{ s.credit_hours || 3 }} ម៉ោង/សប្តាហ៍
          </td>

          <!-- Class Configs Column -->
          <td class="py-3 px-4 text-center">
            <button 
              type="button" 
              @click="openClassConfigModal(s)"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 transition shadow-xs group"
              title="ចុចដើម្បីកំណត់ពិន្ទុអតិប្បរមា និងក្រេឌីតតាមថ្នាក់នីមួយៗ"
            >
              <span>⚙️</span>
              <span>កំណត់តាមថ្នាក់</span>
              <span class="px-1.5 py-0.2 rounded-full bg-amber-200/80 text-[10px] text-amber-900 font-extrabold group-hover:bg-amber-300">
                {{ Object.keys(s.class_configs || {}).length || 6 }}
              </span>
            </button>
          </td>

          <!-- Status -->
          <td class="py-3 px-4 text-center">
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
              {{ $t('common.active') }}
            </span>
          </td>

          <!-- Actions -->
          <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
            <button @click="openEditModal(s)" class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition">
              {{ $t('common.edit') }}
            </button>
            <button @click="handleDelete(s)" class="px-2 py-1 bg-rose-50 text-rose-600 rounded-lg text-xs font-semibold hover:bg-rose-100 transition">
              {{ $t('common.delete') }}
            </button>
          </td>
        </tr>
      </template>
    </DataTable>

    <!-- Modal 1: Create Subject Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
            + បន្ថែមមុខវិជ្ជាថ្មី
          </h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitCreateSubject" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កូដមុខវិជ្ជា *</label>
              <input v-model="createForm.code" type="text" required placeholder="ឧ. MTH-01" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 uppercase">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ម៉ោងសិក្សា/សប្តាហ៍ *</label>
              <input v-model.number="createForm.credit_hours" type="number" min="1" max="15" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ឈ្មោះមុខវិជ្ជា (ភាសាខ្មែរ) *</label>
            <input v-model="createForm.name" type="text" required placeholder="ឧ. គណិតវិទ្យា" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ឈ្មោះមុខវិជ្ជា (អង់គ្លេស / ការពិពណ៌នា)</label>
            <input v-model="createForm.description" type="text" placeholder="ឧ. Mathematics" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <!-- Max Score and Credit Divisor (New) -->
          <div class="p-3.5 bg-blue-50/50 border border-blue-100 rounded-xl space-y-3">
            <div class="text-xs font-bold text-blue-900 flex items-center justify-between">
              <span>🎯 ពិន្ទុអតិប្បរមា និងក្រេឌីត (ស្តង់ដារក្រសួង MoEYS)</span>
              <span class="text-[11px] font-semibold text-blue-700 bg-blue-100/70 px-2 py-0.5 rounded-md">១ ក្រេឌីត = ៥០ ពិន្ទុ</span>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">ពិន្ទុអតិប្បរមាទូទៅ (Max Score) *</label>
                <div class="flex items-center gap-1">
                  <input 
                    v-model.number="createForm.max_score" 
                    @input="onMaxScoreChange(createForm)"
                    type="number" 
                    min="1" 
                    max="1000" 
                    step="1" 
                    required 
                    placeholder="50, 75 ឬ 100" 
                    class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                  >
                  <span class="text-xs text-slate-500 font-bold">pt</span>
                </div>
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">ក្រេឌីត / មេគុណ (Credit) *</label>
                <div class="flex items-center gap-1">
                  <input 
                    v-model.number="createForm.credit" 
                    @input="onCreditChange(createForm)"
                    type="number" 
                    min="0.1" 
                    max="20" 
                    step="0.5" 
                    required 
                    placeholder="1, 1.5 ឬ 2" 
                    class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                  >
                  <span class="text-xs text-slate-500 font-bold">cr</span>
                </div>
              </div>
            </div>
            <!-- Quick Presets -->
            <div class="flex items-center gap-2 pt-1 border-t border-blue-100/60">
              <span class="text-[10px] font-bold text-slate-500">គំរូស្តង់ដារក្រសួង:</span>
              <button 
                type="button" 
                @click="createForm.credit = 1; createForm.max_score = 50"
                class="px-2 py-0.5 rounded text-[11px] font-semibold bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 transition"
              >១ ក្រេឌីត (៥០ pt)</button>
              <button 
                type="button" 
                @click="createForm.credit = 1.5; createForm.max_score = 75"
                class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 border border-amber-200 hover:bg-amber-100 text-amber-800 transition"
              >១.៥ ក្រេឌីត (៧៥ pt)</button>
              <button 
                type="button" 
                @click="createForm.credit = 2; createForm.max_score = 100"
                class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 border border-blue-200 hover:bg-blue-100 text-blue-800 transition"
              >២ ក្រេឌីត (១០០ pt)</button>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ប្រភេទមុខវិជ្ជា *</label>
              <select v-model="createForm.subject_type" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="core">⭐ មុខវិជ្ជាគោល / ស្នូល</option>
                <option value="science">🔬 វិទ្យាសាស្ត្រពិត</option>
                <option value="social">🌍 វិទ្យាសាស្ត្រសង្គម</option>
                <option value="languages">🗣️ ភាសាបរទេស / ភាសា</option>
                <option value="elective">🎨 មុខវិជ្ជាជ្រើសរើស / បំណិន</option>
                <option value="general">📚 ទូទៅ</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កម្រិតថ្នាក់ដែលត្រូវរៀន</label>
              <select v-model="createForm.grade_level" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="7-12">ថ្នាក់ទី ៧ ដល់ ១២ (ទូទៅ)</option>
                <option value="7-9">អនុវិទ្យាល័យ (៧ - ៩)</option>
                <option value="10-12">វិទ្យាល័យ (១០ - ១២)</option>
              </select>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">
              បោះបង់
            </button>
            <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50">
              <span v-if="isSubmitting">កំពុងរក្សាទុក...</span>
              <span v-else>រក្សាទុកមុខវិជ្ជា</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal 2: Edit Subject Modal -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            កែប្រែមុខវិជ្ជា {{ selectedSubject?.name }}
          </h3>
          <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitUpdateSubject" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កូដមុខវិជ្ជា *</label>
              <input v-model="editForm.code" type="text" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 uppercase">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ម៉ោងសិក្សា/សប្តាហ៍ *</label>
              <input v-model.number="editForm.credit_hours" type="number" min="1" max="15" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ឈ្មោះមុខវិជ្ជា (ភាសាខ្មែរ) *</label>
            <input v-model="editForm.name" type="text" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ឈ្មោះមុខវិជ្ជា (អង់គ្លេស / ការពិពណ៌នា)</label>
            <input v-model="editForm.description" type="text" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <!-- Max Score and Credit Divisor (New) -->
          <div class="p-3.5 bg-amber-50/50 border border-amber-100 rounded-xl space-y-3">
            <div class="text-xs font-bold text-amber-900 flex items-center justify-between">
              <span>🎯 ពិន្ទុអតិប្បរមា និងក្រេឌីត (ទូទៅ)</span>
              <button 
                type="button" 
                @click="showEditModal = false; openClassConfigModal(selectedSubject)"
                class="text-[11px] text-blue-600 hover:text-blue-800 underline font-bold"
              >
                ⚙️ កំណត់តាមថ្នាក់ដោយឡែក &rarr;
              </button>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">ពិន្ទុអតិប្បរមាទូទៅ (Max Score) *</label>
                <div class="flex items-center gap-1">
                  <input 
                    v-model.number="editForm.max_score" 
                    @input="onMaxScoreChange(editForm)"
                    type="number" 
                    min="1" 
                    max="1000" 
                    step="1" 
                    required 
                    class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                  >
                  <span class="text-xs text-slate-500 font-bold">pt</span>
                </div>
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">ក្រេឌីត / មេគុណ (Credit) *</label>
                <div class="flex items-center gap-1">
                  <input 
                    v-model.number="editForm.credit" 
                    @input="onCreditChange(editForm)"
                    type="number" 
                    min="0.1" 
                    max="20" 
                    step="0.5" 
                    required 
                    class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                  >
                  <span class="text-xs text-slate-500 font-bold">cr</span>
                </div>
              </div>
            </div>
            <!-- Quick Presets -->
            <div class="flex items-center gap-2 pt-1 border-t border-amber-100/60">
              <span class="text-[10px] font-bold text-slate-500">គំរូស្តង់ដារក្រសួង:</span>
              <button 
                type="button" 
                @click="editForm.credit = 1; editForm.max_score = 50"
                class="px-2 py-0.5 rounded text-[11px] font-semibold bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 transition"
              >១ ក្រេឌីត (៥០ pt)</button>
              <button 
                type="button" 
                @click="editForm.credit = 1.5; editForm.max_score = 75"
                class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100/80 border border-amber-200 hover:bg-amber-200 text-amber-800 transition"
              >១.៥ ក្រេឌីត (៧៥ pt)</button>
              <button 
                type="button" 
                @click="editForm.credit = 2; editForm.max_score = 100"
                class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 border border-blue-200 hover:bg-blue-100 text-blue-800 transition"
              >២ ក្រេឌីត (១០០ pt)</button>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ប្រភេទមុខវិជ្ជា *</label>
              <select v-model="editForm.subject_type" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="core">⭐ មុខវិជ្ជាគោល / ស្នូល</option>
                <option value="science">🔬 វិទ្យាសាស្ត្រពិត</option>
                <option value="social">🌍 វិទ្យាសាស្ត្រសង្គម</option>
                <option value="languages">🗣️ ភាសាបរទេស / ភាសា</option>
                <option value="elective">🎨 មុខវិជ្ជាជ្រើសរើស / បំណិន</option>
                <option value="general">📚 ទូទៅ</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កម្រិតថ្នាក់ដែលត្រូវរៀន</label>
              <select v-model="editForm.grade_level" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="7-12">ថ្នាក់ទី ៧ ដល់ ១២ (ទូទៅ)</option>
                <option value="7-9">អនុវិទ្យាល័យ (៧ - ៩)</option>
                <option value="10-12">វិទ្យាល័យ (១០ - ១២)</option>
              </select>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">
              បោះបង់
            </button>
            <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50">
              <span v-if="isSubmitting">កំពុងរក្សាទុក...</span>
              <span v-else>រក្សាទុកការកែប្រែ</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal 3: Class-Specific Max Score & Credit Configuration Modal -->
    <div v-if="showClassConfigModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] shadow-2xl border border-slate-100 flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 flex-shrink-0">
          <div>
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-amber-500"></span>
              កំណត់ពិន្ទុអតិប្បរមា និងក្រេឌីតតាមថ្នាក់៖ <span class="text-blue-600 font-extrabold">{{ selectedSubjectForConfig?.name }}</span> ({{ selectedSubjectForConfig?.code }})
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
              ពិន្ទុអតិប្បរមា និងក្រេឌីត (មេគុណសម្រាប់ចែកមធ្យមភាគ) អាចកំណត់ខុសៗគ្នាទៅតាមកម្រិតថ្នាក់នីមួយៗ
            </p>
          </div>
          <button @click="showClassConfigModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <!-- Presets Bar -->
        <div class="px-6 py-3 bg-amber-50/50 border-b border-amber-100 flex flex-wrap items-center justify-between gap-2 flex-shrink-0">
          <div class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
            <span>⚡ កំណត់រហ័សលើគ្រប់ថ្នាក់:</span>
          </div>
          <div class="flex flex-wrap items-center gap-1.5">
            <button 
              type="button" 
              @click="applyPreset(50, 1)"
              class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-semibold shadow-xs"
            >
              50pt (1cr)
            </button>
            <button 
              type="button" 
              @click="applyPreset(75, 1.5)"
              class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-semibold shadow-xs"
            >
              75pt (1.5cr)
            </button>
            <button 
              type="button" 
              @click="applyPreset(100, 2)"
              class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-semibold shadow-xs"
            >
              100pt (2cr)
            </button>
            <button 
              type="button" 
              @click="applySciencePreset"
              class="px-2.5 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg text-xs font-bold shadow-xs"
              title="កំណត់តាមស្តង់ដារវិទ្យាសាស្ត្រពិត (ថ្នាក់ 11-12 ឡើង 75-100)"
            >
              🔬 វិទ្យាសាស្ត្រពិត
            </button>
            <button 
              type="button" 
              @click="applySocialPreset"
              class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-lg text-xs font-bold shadow-xs"
              title="កំណត់តាមស្តង់ដារវិទ្យាសាស្ត្រសង្គម"
            >
              🌍 វិទ្យាសាស្ត្រសង្គម
            </button>
          </div>
        </div>

        <!-- Grade / Class Table -->
        <div class="p-6 overflow-y-auto space-y-3 flex-1">
          <div class="text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
            <span>តារាងកំណត់តាមកម្រិតថ្នាក់ (ថ្នាក់ទី ៧ ដល់ ១២) និងបន្ទប់ជាក់ស្តែង</span>
            <span class="text-slate-400 font-normal">គោល: {{ selectedSubjectForConfig?.max_score || 50 }} pt | {{ selectedSubjectForConfig?.credit || 1 }} cr</span>
          </div>

          <div class="border border-slate-200 rounded-xl overflow-hidden shadow-xs">
            <table class="w-full text-left text-xs border-collapse">
              <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                <tr>
                  <th class="py-2.5 px-4 w-44">កម្រិតថ្នាក់ / បន្ទប់រៀន</th>
                  <th class="py-2.5 px-4">ពិន្ទុអតិប្បរមា</th>
                  <th class="py-2.5 px-4">ក្រេឌីតចែក</th>
                  <th class="py-2.5 px-4 text-center">ជ្រើសរើសរហ័ស</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="item in availableGrades" :key="item.key" class="hover:bg-slate-50/50">
                  <td class="py-2.5 px-4 font-bold text-slate-800">
                    <div class="flex items-center gap-2">
                      <span class="w-2 h-2 rounded-full" :class="item.key >= 10 || item.key.startsWith('10') || item.key.startsWith('11') || item.key.startsWith('12') ? 'bg-indigo-500' : 'bg-emerald-500'"></span>
                      <span>{{ item.label }}</span>
                    </div>
                  </td>
                  <td class="py-2.5 px-4">
                    <div class="flex items-center gap-1.5">
                      <input 
                        v-model.number="classConfigForm[item.key].max_score" 
                        @input="onMaxScoreChange(classConfigForm[item.key])"
                        type="number" 
                        min="1" 
                        max="1000" 
                        step="1"
                        required
                        class="w-24 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                      />
                      <span class="text-slate-400 font-medium">ពិន្ទុ</span>
                    </div>
                  </td>
                  <td class="py-2.5 px-4">
                    <div class="flex items-center gap-1.5">
                      <input 
                        v-model.number="classConfigForm[item.key].credit" 
                        @input="onCreditChange(classConfigForm[item.key])"
                        type="number" 
                        min="0.1" 
                        max="20" 
                        step="0.5"
                        required
                        class="w-24 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20"
                      />
                      <span class="text-slate-400 font-medium">ក្រេឌីត</span>
                    </div>
                  </td>
                  <td class="py-2.5 px-4 text-center">
                    <div class="flex items-center justify-center gap-1">
                      <button 
                        type="button" 
                        @click="classConfigForm[item.key].max_score = 50; classConfigForm[item.key].credit = 1"
                        class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700"
                        title="50 ពិន្ទុ / 1 ក្រេឌីត"
                      >50</button>
                      <button 
                        type="button" 
                        @click="classConfigForm[item.key].max_score = 75; classConfigForm[item.key].credit = 1.5"
                        class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 hover:bg-amber-200 text-amber-800"
                        title="75 ពិន្ទុ / 1.5 ក្រេឌីត"
                      >75</button>
                      <button 
                        type="button" 
                        @click="classConfigForm[item.key].max_score = 100; classConfigForm[item.key].credit = 2"
                        class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 hover:bg-blue-200 text-blue-800"
                        title="100 ពិន្ទុ / 2 ក្រេឌីត"
                      >100</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3.5 border-t border-slate-100 flex items-center justify-between bg-slate-50/50 flex-shrink-0">
          <button type="button" @click="showClassConfigModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">
            បោះបង់
          </button>
          <button 
            type="button" 
            @click="submitSaveClassConfigs" 
            :disabled="isSavingConfigs" 
            class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50 flex items-center gap-1.5 shadow-sm"
          >
            <span v-if="isSavingConfigs">កំពុងរក្សាទុក...</span>
            <span v-else>💾 រក្សាទុកការកំណត់</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onActivated } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import StatCard from '../components/StatCard.vue';
import FilterBar from '../components/FilterBar.vue';
import DataTable from '../components/DataTable.vue';

const { t } = useI18n();

const activeTab = ref('all');
const searchQuery = ref('');
const subjectsList = ref([]);
const isLoading = ref(true);

// Hydrate from cache
try {
  const cached = localStorage.getItem('pdt_cached_subjects');
  if (cached) {
    const d = JSON.parse(cached);
    if (d.subjects && d.subjects.length) {
      subjectsList.value = d.subjects;
      isLoading.value = false;
    }
  }
} catch (e) {}

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showClassConfigModal = ref(false);
const selectedSubject = ref(null);
const selectedSubjectForConfig = ref(null);
const isSubmitting = ref(false);
const isSavingConfigs = ref(false);

const classConfigForm = ref({});

const availableGrades = [
  { key: '7', label: 'ថ្នាក់ទី ៧ (អនុវិទ្យាល័យ)' },
  { key: '8', label: 'ថ្នាក់ទី ៨ (អនុវិទ្យាល័យ)' },
  { key: '9', label: 'ថ្នាក់ទី ៩ (អនុវិទ្យាល័យ)' },
  { key: '10', label: 'ថ្នាក់ទី ១០ (វិទ្យាល័យ)' },
  { key: '11', label: 'ថ្នាក់ទី ១១ (វិទ្យាល័យ)' },
  { key: '12', label: 'ថ្នាក់ទី ១២ (វិទ្យាល័យ)' },
  { key: '11D', label: 'បន្ទប់ 11D (ជាក់ស្តែង)' },
  { key: '10A', label: 'បន្ទប់ 10A (ជាក់ស្តែង)' },
];

const createForm = ref({
  code: '',
  name: '',
  subject_type: 'core',
  description: '',
  max_score: 50,
  credit: 1,
  credit_hours: 3,
  grade_level: '7-12',
});

const editForm = ref({
  code: '',
  name: '',
  subject_type: 'core',
  description: '',
  max_score: 50,
  credit: 1,
  credit_hours: 3,
  grade_level: '7-12',
});

// Cambodian MoEYS Standard: 1 Credit = 50 Maximum Points
const onCreditChange = (targetObj) => {
  if (!targetObj) return;
  const cr = Number(targetObj.credit);
  if (!isNaN(cr) && cr > 0) {
    targetObj.max_score = Math.round(cr * 50 * 100) / 100;
  }
};

const onMaxScoreChange = (targetObj) => {
  if (!targetObj) return;
  const max = Number(targetObj.max_score);
  if (!isNaN(max) && max > 0) {
    targetObj.credit = Math.round((max / 50) * 100) / 100;
  }
};

const fetchSubjects = async () => {
  if (subjectsList.value.length === 0) {
    isLoading.value = true;
  }
  try {
    const res = await axios.get('/api/course-subjects', { params: { _t: Date.now() } });
    subjectsList.value = res.data.subjects || [];
    try {
      localStorage.setItem('pdt_cached_subjects', JSON.stringify(res.data));
    } catch (e) {}
  } catch (err) {
    console.error('Failed to fetch subjects:', err);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchSubjects();
});

onActivated(() => {
  fetchSubjects();
});

const filteredSubjects = computed(() => {
  return subjectsList.value.filter(s => {
    const matchesTab = activeTab.value === 'all' || s.type === activeTab.value || s.subject_type === activeTab.value;
    const q = searchQuery.value.toLowerCase();
    const matchesSearch = !q || 
      (s.nameKh && s.nameKh.toLowerCase().includes(q)) || 
      (s.name && s.name.toLowerCase().includes(q)) || 
      (s.nameEn && s.nameEn.toLowerCase().includes(q)) || 
      s.code.toLowerCase().includes(q);
    return matchesTab && matchesSearch;
  });
});

const totalHours = computed(() => subjectsList.value.reduce((a, s) => a + (Number(s.credit_hours) || 0), 0));
const totalCreditsDivisor = computed(() => subjectsList.value.reduce((a, s) => a + (Number(s.credit) || 1), 0));

const openCreateModal = () => {
  createForm.value = {
    code: '',
    name: '',
    subject_type: 'core',
    description: '',
    max_score: 50,
    credit: 1,
    credit_hours: 3,
    grade_level: '7-12',
  };
  showCreateModal.value = true;
};

const openEditModal = (s) => {
  selectedSubject.value = s;
  editForm.value = {
    code: s.code,
    name: s.nameKh || s.name,
    subject_type: s.subject_type || s.type || 'core',
    description: s.nameEn || s.description || '',
    max_score: Number(s.max_score) || 50,
    credit: Number(s.credit) || 1,
    credit_hours: Number(s.credit_hours) || 3,
    grade_level: s.grade_level || '7-12',
  };
  showEditModal.value = true;
};

const openClassConfigModal = (s) => {
  selectedSubjectForConfig.value = s;
  const existing = s.class_configs || {};
  const form = {};

  availableGrades.forEach(g => {
    if (existing[g.key]) {
      form[g.key] = {
        max_score: Number(existing[g.key].max_score) || (Number(s.max_score) || 50),
        credit: Number(existing[g.key].credit) || (Number(s.credit) || 1)
      };
    } else {
      // Default to subject's general max_score and credit
      form[g.key] = {
        max_score: Number(s.max_score) || 50,
        credit: Number(s.credit) || 1
      };
    }
  });

  classConfigForm.value = form;
  showClassConfigModal.value = true;
};

const applyPreset = (maxScore, credit) => {
  availableGrades.forEach(g => {
    if (classConfigForm.value[g.key]) {
      classConfigForm.value[g.key].max_score = maxScore;
      classConfigForm.value[g.key].credit = credit;
    }
  });
};

const applySciencePreset = () => {
  const code = (selectedSubjectForConfig.value?.code || '').toUpperCase();
  const isMajorScience = code.includes('MTH') || code.includes('PHY') || code.includes('CHM') || code.includes('BIO');
  
  availableGrades.forEach(g => {
    if (classConfigForm.value[g.key]) {
      if (['11', '12', '11D'].includes(g.key)) {
        // High School Science Track: Science subjects 75-100pt, others 50pt
        classConfigForm.value[g.key].max_score = isMajorScience ? (code.includes('MTH') ? 100 : 75) : 50;
        classConfigForm.value[g.key].credit = isMajorScience ? (code.includes('MTH') ? 2 : 1.5) : 1;
      } else {
        // Grades 7-10
        classConfigForm.value[g.key].max_score = code.includes('MTH') || code.includes('KHM') ? 100 : 50;
        classConfigForm.value[g.key].credit = code.includes('MTH') || code.includes('KHM') ? 2 : 1;
      }
    }
  });
};

const applySocialPreset = () => {
  const code = (selectedSubjectForConfig.value?.code || '').toUpperCase();
  const isMajorSocial = code.includes('KHM') || code.includes('HIS') || code.includes('GEO') || code.includes('ETH');
  
  availableGrades.forEach(g => {
    if (classConfigForm.value[g.key]) {
      if (['11', '12', '11D'].includes(g.key)) {
        // High School Social Science Track: Khmer 100, Social 75, Sciences 50
        classConfigForm.value[g.key].max_score = code.includes('KHM') ? 100 : (isMajorSocial ? 75 : 50);
        classConfigForm.value[g.key].credit = code.includes('KHM') ? 2 : (isMajorSocial ? 1.5 : 1);
      } else {
        // Grades 7-10
        classConfigForm.value[g.key].max_score = code.includes('MTH') || code.includes('KHM') ? 100 : 50;
        classConfigForm.value[g.key].credit = code.includes('MTH') || code.includes('KHM') ? 2 : 1;
      }
    }
  });
};

const submitSaveClassConfigs = async () => {
  if (!selectedSubjectForConfig.value) return;
  isSavingConfigs.value = true;
  try {
    const res = await axios.post(`/api/course-subjects/${selectedSubjectForConfig.value.id}/class-configs`, {
      class_configs: classConfigForm.value
    });
    showClassConfigModal.value = false;
    alert(res.data.message || 'បានរក្សាទុកការកំណត់ពិន្ទុ និងក្រេឌីតតាមថ្នាក់ដោយជោគជ័យ!');
    await fetchSubjects();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការរក្សាទុកការកំណត់!');
  } finally {
    isSavingConfigs.value = false;
  }
};

const submitCreateSubject = async () => {
  isSubmitting.value = true;
  try {
    const res = await axios.post('/api/course-subjects', createForm.value);
    showCreateModal.value = false;
    alert(res.data.message || 'បានបង្កើតមុខវិជ្ជាថ្មីជោគជ័យ!');
    await fetchSubjects();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការបង្កើតមុខវិជ្ជា!');
  } finally {
    isSubmitting.value = false;
  }
};

const submitUpdateSubject = async () => {
  if (!selectedSubject.value) return;
  isSubmitting.value = true;
  try {
    const res = await axios.put(`/api/course-subjects/${selectedSubject.value.id}`, editForm.value);
    showEditModal.value = false;
    alert(res.data.message || 'បានកែប្រែព័ត៌មានមុខវិជ្ជាជោគជ័យ!');
    await fetchSubjects();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការកែប្រែមុខវិជ្ជា!');
  } finally {
    isSubmitting.value = false;
  }
};

const handleDelete = async (s) => {
  if (!confirm(`តើអ្នកពិតជាចង់លុបមុខវិជ្ជា "${s.nameKh || s.name}" នេះមែនទេ?`)) return;
  try {
    const res = await axios.delete(`/api/course-subjects/${s.id}`);
    alert(res.data.message || 'បានលុបមុខវិជ្ជាដោយជោគជ័យ!');
    await fetchSubjects();
  } catch (err) {
    alert(err.response?.data?.message || 'មិនអាចលុបមុខវិជ្ជានេះបានទេ!');
  }
};

const handlePrint = () => window.print();

const handleExport = () => {
  const csv = [
    ['កូដ', 'ឈ្មោះខ្មែរ', 'ឈ្មោះអង់គ្លេស', 'ប្រភេទមុខវិជ្ជា', 'ពិន្ទុអតិប្បរមា', 'ក្រេឌីតចែក', 'ម៉ោងសិក្សា/សប្តាហ៍', 'កម្រិតថ្នាក់'].join(','),
    ...subjectsList.value.map(s => [
      `"${s.code}"`,
      `"${s.nameKh || s.name}"`,
      `"${s.nameEn || s.description || ''}"`,
      `"${s.typeLabel || s.type || ''}"`,
      `"${s.max_score || 50}"`,
      `"${s.credit || 1}"`,
      `"${s.credit_hours || 3}"`,
      `"${s.grade_level || ''}"`
    ].join(','))
  ].join('\n');

  const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `Subjects_${new Date().toISOString().slice(0, 10)}.csv`;
  a.click();
};
</script>
