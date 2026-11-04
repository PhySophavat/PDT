<template>
  <div class="space-y-6">
    <!-- Header -->
    <PageHeader 
      :title="$t('examinations.title')" 
      :breadcrumb="$t('nav.exam')" 
      :actionLabel="$t('examinations.addExam')"
      @action="openCreateModal"
    />

    <!-- Sub Tabs -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'exams', label: $t('examinations.examList') },
        { id: 'scores', label: $t('examinations.enterScores') },
        { id: 'matrix', label: '📊 តារាងពិន្ទុរួម (Matrix Sheet)' },
        { id: 'report_cards', label: $t('examinations.reportCard') }
      ]"
    />

    <!-- Entity Banner -->
    <EntityBanner 
      :name="$t('examinations.title')"
      :subtitle="$t('nav.exam') + ' - ការគ្រប់គ្រងសម័យប្រឡង និងលទ្ធផលសិក្សា'"
      metaId="ADM-EXM-BRD"
      @print="handlePrint"
      @download="handleExport"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="$t('examinations.examList')" :value="examsList.length" />
      <StatCard :title="$t('examinations.scores')" :value="totalScoresCount" />
      <StatCard :title="$t('examinations.passRate')" :value="'95.8% ជាប់'" />
      <StatCard :title="$t('classes.totalClasses')" :value="classesList.length || 48" />
    </div>

    <!-- TAB 1: Examination List View -->
    <div v-if="activeTab === 'exams'" class="space-y-4">
      <!-- Filter Bar -->
      <FilterBar>
        <template #left>
          <div class="flex items-center gap-2 text-xs">
            <span class="font-medium text-slate-700">កម្រិតនិទ្ទេស:</span>
            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold">A: 90-100</span>
            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-bold">B: 80-89</span>
            <span class="px-2 py-0.5 rounded bg-cyan-50 text-cyan-700 font-bold">C: 70-79</span>
            <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-bold">D: 60-69</span>
            <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 font-bold">F: &lt;50</span>
          </div>
        </template>

        <template #right>
          <select v-model="examTypeFilter" class="px-3.5 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-medium focus:outline-none">
            <option value="">{{ $t('common.all') }} ប្រភេទប្រឡង</option>
            <option value="ឆមាស">ប្រឡងឆមាស</option>
            <option value="ប្រចាំខែ">ប្រឡងប្រចាំខែ</option>
            <option value="ប្រចាំឆ្នាំ">ប្រឡងប្រចាំឆ្នាំ</option>
          </select>
        </template>
      </FilterBar>

      <!-- Data Table -->
      <DataTable>
        <template #header>
          <tr>
            <th class="py-3 px-4 font-bold text-slate-700">{{ $t('examinations.examName') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700">{{ $t('examinations.examType') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700">{{ $t('students.academicYear') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700">{{ $t('examinations.examDate') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('examinations.semester') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('common.status') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('common.actions') }}</th>
          </tr>
        </template>

        <template #body>
          <tr v-if="filteredExams.length === 0">
            <td colspan="7" class="py-10 text-center text-slate-400">{{ $t('common.noData') }}</td>
          </tr>
          <tr v-for="ex in filteredExams" :key="ex.id" class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3 px-4 font-bold text-slate-800">
              <span class="text-blue-600 block">{{ ex.title }}</span>
              <span class="text-xs text-slate-400 font-normal">បញ្ចូលពិន្ទុបាន: {{ ex.scores_count || 0 }} កំណត់ត្រា</span>
            </td>
            <td class="py-3 px-4 text-slate-600 font-medium">{{ ex.exam_type }}</td>
            <td class="py-3 px-4 font-mono text-slate-600 text-xs">{{ ex.academic_year }}</td>
            <td class="py-3 px-4 text-slate-600 text-xs">{{ ex.start_date || 'N/A' }} ដល់ {{ ex.end_date || 'N/A' }}</td>
            <td class="py-3 px-4 text-center font-semibold text-slate-700">{{ ex.term || `ឆមាសទី ${ex.semester}` }}</td>
            <td class="py-3 px-4 text-center">
              <span :class="[
                'px-2.5 py-1 rounded-full text-[11px] font-bold',
                ex.status === 'active' || ex.status === 'published' || ex.is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'
              ]">
                {{ ex.status === 'active' || ex.is_published ? 'ផ្សាយជាផ្លូវការ' : 'សេចក្តីព្រាង' }}
              </span>
            </td>
            <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
              <button @click="openScoresForExam(ex)" class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition">
                បញ្ចូលពិន្ទុ &rarr;
              </button>
              <button @click="openEditModal(ex)" class="px-2 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-200 transition">
                {{ $t('common.edit') }}
              </button>
              <button @click="handleDelete(ex)" class="px-2 py-1 bg-rose-50 text-rose-600 rounded-lg text-xs font-semibold hover:bg-rose-100 transition">
                {{ $t('common.delete') }}
              </button>
            </td>
          </tr>
        </template>
      </DataTable>
    </div>

    <!-- TAB 2: Enter Scores Interactive Workspace -->
    <div v-else-if="activeTab === 'scores'" class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
        <div>
          <h3 class="font-bold text-slate-800 text-base">បញ្ចូលពិន្ទុប្រឡងសិស្ស (Enter Student Scores)</h3>
          <p class="text-xs text-slate-500">ជ្រើសរើសសម័យប្រឡង ថ្នាក់រៀន និងមុខវិជ្ជាដើម្បីកត់ត្រាពិន្ទុ</p>
        </div>
        <button 
          v-if="scoreSheetStudents.length > 0" 
          @click="submitSaveScores" 
          :disabled="isSavingScores"
          class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition shadow-sm disabled:opacity-50"
        >
          <span v-if="isSavingScores">កំពុងរក្សាទុក...</span>
          <span v-else>💾 រក្សាទុកពិន្ទុទាំងអស់</span>
        </button>
      </div>

      <!-- Selectors Bar -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200/70">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">១. សម័យប្រឡង *</label>
          <select v-model="selectedExamId" @change="loadScoreSheet" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <option value="">-- ជ្រើសរើសការប្រឡង --</option>
            <option v-for="ex in examsList" :key="ex.id" :value="ex.id">{{ ex.title }}</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">២. ថ្នាក់រៀន *</label>
          <select v-model="selectedClassForScores" @change="loadScoreSheet" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <option value="">-- ជ្រើសរើសថ្នាក់ --</option>
            <optgroup v-for="(gClasses, gKey) in groupedClasses" :key="gKey" :label="`-- កម្រិតថ្នាក់ទី ${gKey} --`">
              <option v-for="c in gClasses" :key="c" :value="c">ថ្នាក់ {{ c }}</option>
            </optgroup>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">៣. មុខវិជ្ជាប្រឡង *</label>
          <select v-model="selectedSubjectForScores" @change="loadScoreSheet" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <option value="">-- ជ្រើសរើសមុខវិជ្ជា --</option>
            <option v-for="s in subjectsList" :key="s.id" :value="s.id">{{ s.name }} ({{ s.code }})</option>
          </select>
        </div>
      </div>

      <!-- Student Scores Input Sheet -->
      <div v-if="isLoadingScores" class="py-12 text-center text-slate-400">
        កំពុងផ្ទុកបញ្ជីសិស្ស...
      </div>
      <div v-else-if="scoreSheetStudents.length === 0" class="py-12 text-center text-slate-400">
        សូមជ្រើសរើស សម័យប្រឡង ថ្នាក់រៀន និងមុខវិជ្ជា ដើម្បីបង្ហាញបញ្ជីសិស្សសម្រាប់បញ្ចូលពិន្ទុ។
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="bg-slate-100/80 text-slate-700 uppercase font-bold border-b border-slate-200">
              <th class="py-3 px-4">អត្តលេខ</th>
              <th class="py-3 px-4">ឈ្មោះសិស្ស</th>
              <th class="py-3 px-4">ភេទ</th>
              <th class="py-3 px-4 w-36">ពិន្ទុទទួលបាន (Max: 100)</th>
              <th class="py-3 px-4 text-center">និទ្ទេស</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="st in scoreSheetStudents" :key="st.student_id" class="hover:bg-slate-50">
              <td class="py-3 px-4 font-mono font-bold text-slate-600">{{ st.student_id_card || 'STU-00' }}</td>
              <td class="py-3 px-4 font-bold text-slate-800">{{ st.name }}</td>
              <td class="py-3 px-4 text-slate-600">{{ st.gender === 'female' ? 'ស្រី' : 'ប្រុស' }}</td>
              <td class="py-3 px-4">
                <input 
                  type="number" 
                  min="0" 
                  max="100" 
                  step="0.5"
                  v-model="scoresMap[st.student_id]"
                  placeholder="0 - 100" 
                  class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                />
              </td>
              <td class="py-3 px-4 text-center">
                <span :class="[
                  'px-2 py-0.5 rounded font-bold text-xs',
                  gradeLetterFor(scoresMap[st.student_id]) === 'A' ? 'bg-emerald-100 text-emerald-700' :
                  gradeLetterFor(scoresMap[st.student_id]) === 'B' ? 'bg-blue-100 text-blue-700' :
                  gradeLetterFor(scoresMap[st.student_id]) === 'C' ? 'bg-cyan-100 text-cyan-700' :
                  gradeLetterFor(scoresMap[st.student_id]) === 'D' ? 'bg-amber-100 text-amber-700' :
                  'bg-rose-100 text-rose-700'
                ]">
                  {{ gradeLetterFor(scoresMap[st.student_id]) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 3: Matrix Master Sheet for Homeroom Teachers & Admins -->
    <div v-else-if="activeTab === 'matrix'" class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-6">
      <!-- Matrix Top Bar -->
      <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
        <div>
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span>📊</span>
            តារាងពិន្ទុរួមប្រចាំថ្នាក់ (Class Master Score Sheet)
          </h3>
          <p class="text-xs text-slate-500">
            សម្រាប់គ្រូបន្ទុកថ្នាក់ និងរដ្ឋបាល - បង្ហាញគ្រប់មុខវិជ្ជា សរុប មធ្យមភាគ និទ្ទេស និងចំណាត់ថ្នាក់
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <!-- Template Download -->
          <button 
            type="button"
            @click="downloadMatrixTemplate"
            :disabled="!selectedMatrixClass"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition disabled:opacity-50"
            title="ទាញយកទម្រង់គំរូ Excel សម្រាប់វាយពិន្ទុក្រៅបណ្តាញ"
          >
            📥 គំរូ Excel
          </button>

          <!-- Import Excel/CSV -->
          <button 
            type="button"
            @click="openImportModal"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-xs font-bold transition"
            title="បញ្ចូលទិន្នន័យពី File Excel ឬ CSV"
          >
            📤 បញ្ចូល Excel/CSV
          </button>

          <!-- Export Current Sheet -->
          <button 
            type="button"
            @click="exportMatrixToExcel"
            :disabled="matrixStudents.length === 0"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl text-xs font-bold transition disabled:opacity-50"
            title="ទាញយកតារាងពិន្ទុបច្ចុប្បន្នជា Excel/CSV"
          >
            📊 ទាញយក Excel
          </button>

          <!-- Official Ranking Sheet Button -->
          <a 
            :href="`/examinations/${selectedMatrixExamId}/ranking-sheet?class=${selectedMatrixClass}`" 
            target="_blank"
            :class="[
              'inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition shadow-sm',
              matrixStudents.length === 0 ? 'pointer-events-none opacity-50' : ''
            ]"
            title="មើល និងបោះពុម្ពតារាងចំណាត់ថ្នាក់ប្រចាំខែផ្លូវការ (ទម្រង់ក្រសួង A4)"
          >
            📜 តារាងចំណាត់ថ្នាក់ផ្លូវការ
          </a>

          <!-- Print Matrix Sheet -->
          <button 
            type="button"
            @click="printMatrixSheet"
            :disabled="matrixStudents.length === 0"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-xl text-xs font-bold transition disabled:opacity-50"
          >
            🖨️ បោះពុម្ព
          </button>

          <!-- Save Matrix -->
          <button 
            type="button"
            @click="submitSaveMatrix" 
            :disabled="isSavingMatrix || matrixStudents.length === 0"
            class="inline-flex items-center gap-1.5 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm disabled:opacity-50"
          >
            <span v-if="isSavingMatrix">កំពុងរក្សាទុក...</span>
            <span v-else>💾 រក្សាទុកតារាងពិន្ទុ</span>
          </button>
        </div>
      </div>

      <!-- Selectors & Filter Bar -->
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200/70 items-end">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">១. សម័យប្រឡង *</label>
          <select 
            v-model="selectedMatrixExamId" 
            @change="loadMatrixSheet" 
            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
            <option value="">-- ជ្រើសរើសការប្រឡង --</option>
            <option v-for="ex in examsList" :key="ex.id" :value="ex.id">{{ ex.title }}</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">២. ថ្នាក់រៀន *</label>
          <select 
            v-model="selectedMatrixClass" 
            @change="loadMatrixSheet" 
            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
            <option value="">-- ជ្រើសរើសថ្នាក់ --</option>
            <optgroup v-for="(gClasses, gKey) in groupedClasses" :key="gKey" :label="`-- កម្រិតថ្នាក់ទី ${gKey} --`">
              <option v-for="c in gClasses" :key="c" :value="c">ថ្នាក់ {{ c }}</option>
            </optgroup>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">ស្វែងរកសិស្ស</label>
          <input 
            v-model="matrixSearch" 
            type="text" 
            placeholder="ស្វែងរកតាមឈ្មោះសិស្ស..." 
            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          />
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">ជួរឈរអត្តលេខ</label>
          <button 
            type="button"
            @click="showStudentIdInMatrix = !showStudentIdInMatrix"
            :class="[
              'w-full px-3 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 border',
              showStudentIdInMatrix 
                ? 'bg-blue-50 text-blue-700 border-blue-200' 
                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100'
            ]"
            title="ចុចដើម្បីបិទ/បើកជួរឈរអត្តលេខ"
          >
            <span>{{ showStudentIdInMatrix ? '👁️ កំពុងបង្ហាញអត្តលេខ' : '🙈 លាក់អត្តលេខ (ស្តង់ដារ)' }}</span>
          </button>
        </div>
      </div>

      <!-- Summary Statistics for Class -->
      <div v-if="matrixStats && matrixStudents.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="p-3 bg-blue-50/60 border border-blue-100 rounded-xl">
          <span class="text-[11px] font-bold text-blue-700">ចំនួនសិស្សបានវាយពិន្ទុ</span>
          <div class="text-base font-extrabold text-blue-900 mt-0.5">
            {{ matrixStats.evaluated_count }} / {{ matrixStats.total_students }} នាក់
          </div>
        </div>
        <div class="p-3 bg-purple-50/60 border border-purple-100 rounded-xl">
          <span class="text-[11px] font-bold text-purple-700">មធ្យមភាគប្រចាំថ្នាក់</span>
          <div class="text-base font-extrabold text-purple-900 mt-0.5">
            {{ matrixStats.class_average }} / {{ matrixStats.scale_max || 50 }}
          </div>
        </div>
        <div class="p-3 bg-emerald-50/60 border border-emerald-100 rounded-xl">
          <span class="text-[11px] font-bold text-emerald-700">អត្រាជាប់ (Pass Rate)</span>
          <div class="text-base font-extrabold text-emerald-900 mt-0.5">
            {{ matrixStats.pass_rate }}% ({{ matrixStats.passed_count }} នាក់)
          </div>
        </div>
        <div class="p-3 bg-amber-50/60 border border-amber-100 rounded-xl">
          <span class="text-[11px] font-bold text-amber-700">ពិន្ទុខ្ពស់បំផុត / ទាបបំផុត</span>
          <div class="text-base font-extrabold text-amber-900 mt-0.5">
            {{ matrixStats.highest_average }} / {{ matrixStats.lowest_average }}
          </div>
        </div>
      </div>

      <!-- Interactive Matrix Table -->
      <div v-if="isLoadingMatrix" class="py-12 text-center text-slate-400">
        កំពុងផ្ទុកតារាងពិន្ទុរួម...
      </div>
      <div v-else-if="matrixStudents.length === 0" class="py-10 text-center space-y-3 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
        <div class="w-12 h-12 mx-auto rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl">👥</div>
        <div>
          <p class="font-bold text-slate-800 text-sm">មិនទាន់មានទិន្នន័យសិស្សនៅក្នុងថ្នាក់ {{ selectedMatrixClass }} នៅឡើយទេ</p>
          <p class="text-xs text-slate-500 mt-1">សូមជ្រើសរើសថ្នាក់ដែលមានទិន្នន័យស្រាប់ ឬចុចប៊ូតុងខាងក្រោមដើម្បីបង្ហាញតារាងពិន្ទុពេញ៖</p>
        </div>
        <div class="flex items-center justify-center gap-2 pt-1">
          <button 
            type="button" 
            @click="selectedMatrixClass = '11D'; loadMatrixSheet()" 
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5"
          >
            <span>👉 បង្ហាញថ្នាក់ 11D (សិស្ស ៥២ នាក់ & ពិន្ទុពេញលេញ)</span>
          </button>
          <button 
            type="button" 
            @click="selectedMatrixClass = '10A'; loadMatrixSheet()" 
            class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-xl text-xs font-bold transition"
          >
            <span>បង្ហាញថ្នាក់ 10A</span>
          </button>
        </div>
      </div>
      <div v-else class="overflow-x-auto border border-slate-200 rounded-xl shadow-inner max-h-[calc(100vh-280px)] min-h-[400px] overflow-y-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="sticky top-0 bg-slate-100 z-20 shadow-sm">
            <tr class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200 text-center">
              <th class="py-3 px-3 text-left w-12 sticky left-0 bg-slate-100 z-30">ល.រ</th>
              <th v-if="showStudentIdInMatrix" class="py-3 px-3 text-left sticky left-12 bg-slate-100 z-30">អត្តលេខ</th>
              <th class="py-3 px-4 text-left bg-slate-100 z-30 min-w-36" :class="showStudentIdInMatrix ? 'sticky left-32' : 'sticky left-12'">ឈ្មោះសិស្ស</th>
              <th class="py-3 px-2 w-12">ភេទ</th>
              
              <!-- Dynamic Subjects Header -->
              <th 
                v-for="sub in matrixSubjects" 
                :key="sub.id" 
                class="py-3 px-2 text-center border-l border-slate-200 min-w-24 bg-blue-50/40"
              >
                <div class="font-bold text-slate-800">{{ sub.name }}</div>
                <div class="flex items-center justify-center gap-1 mt-0.5">
                  <span class="text-[10px] text-slate-500 font-mono font-normal">{{ sub.code }}</span>
                  <span v-if="sub.max_score" class="px-1 py-0.2 rounded text-[9px] font-bold bg-blue-100 text-blue-700" title="ពិន្ទុអតិប្បរមា និងក្រេឌីតសម្រាប់ចែក">
                    {{ sub.max_score }}pt ({{ sub.credit }}cr)
                  </span>
                </div>
              </th>

              <!-- Total, Average, Grade, Rank -->
              <th class="py-3 px-3 text-center bg-emerald-50 text-emerald-800 border-l border-slate-200 min-w-20">សរុប</th>
              <th class="py-3 px-3 text-center bg-blue-50 text-blue-800 border-l border-slate-200 min-w-20">មធ្យមភាគ</th>
              <th class="py-3 px-3 text-center bg-purple-50 text-purple-800 border-l border-slate-200 min-w-16">និទ្ទេស</th>
              <th class="py-3 px-3 text-center bg-amber-50 text-amber-800 border-l border-slate-200 min-w-24">ចំណាត់ថ្នាក់</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr 
              v-for="(st, idx) in filteredMatrixStudents" 
              :key="st.student_id" 
              class="hover:bg-slate-50/80 transition-colors"
            >
              <td class="py-2 px-3 font-semibold text-slate-500 text-center sticky left-0 bg-white z-10">{{ idx + 1 }}</td>
              <td v-if="showStudentIdInMatrix" class="py-2 px-3 font-mono font-bold text-slate-600 sticky left-12 bg-white z-10">{{ st.student_id_card }}</td>
              <td class="py-2 px-4 font-bold text-slate-800 bg-white z-10" :class="showStudentIdInMatrix ? 'sticky left-32' : 'sticky left-12'">{{ st.name }}</td>
              <td class="py-2 px-2 text-center text-slate-600">{{ st.gender }}</td>

              <!-- Subject Input Cells -->
              <td 
                v-for="sub in matrixSubjects" 
                :key="sub.id" 
                class="py-1.5 px-2 text-center border-l border-slate-100"
              >
                <input 
                  type="number"
                  min="0"
                  :max="sub.max_score || 100"
                  step="0.5"
                  v-model.number="matrixData[st.student_id][sub.id]"
                  placeholder="-"
                  :class="[
                    'w-16 px-1.5 py-1 text-center font-bold text-xs rounded-lg border transition focus:outline-none focus:ring-2 focus:ring-blue-500/20',
                    matrixData[st.student_id]?.[sub.id] >= 80 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' :
                    matrixData[st.student_id]?.[sub.id] >= 65 ? 'bg-blue-50 text-blue-700 border-blue-200' :
                    matrixData[st.student_id]?.[sub.id] >= 50 ? 'bg-amber-50 text-amber-700 border-amber-200' :
                    matrixData[st.student_id]?.[sub.id] !== undefined && matrixData[st.student_id]?.[sub.id] !== null && matrixData[st.student_id]?.[sub.id] !== '' ? 'bg-rose-50 text-rose-700 border-rose-200' :
                    'bg-slate-50 text-slate-500 border-slate-200'
                  ]"
                />
              </td>

              <!-- Total -->
              <td class="py-2 px-3 text-center font-bold text-emerald-700 border-l border-slate-100 bg-emerald-50/20 font-mono">
                {{ calculateStudentTotal(st.student_id) }}
              </td>

              <!-- Average -->
              <td class="py-2 px-3 text-center font-extrabold text-blue-700 border-l border-slate-100 bg-blue-50/20 font-mono">
                {{ calculateStudentAverage(st.student_id) }}
              </td>

              <!-- Grade -->
              <td class="py-2 px-3 text-center border-l border-slate-100 bg-purple-50/20">
                <span :class="[
                  'px-2 py-0.5 rounded font-bold text-xs inline-block',
                  gradeLetterFor(calculateStudentAverage(st.student_id)) === 'A' ? 'bg-emerald-100 text-emerald-800' :
                  gradeLetterFor(calculateStudentAverage(st.student_id)) === 'B' ? 'bg-blue-100 text-blue-800' :
                  gradeLetterFor(calculateStudentAverage(st.student_id)) === 'C' ? 'bg-cyan-100 text-cyan-800' :
                  gradeLetterFor(calculateStudentAverage(st.student_id)) === 'D' ? 'bg-amber-100 text-amber-800' :
                  gradeLetterFor(calculateStudentAverage(st.student_id)) === 'E' ? 'bg-orange-100 text-orange-800' :
                  calculateStudentAverage(st.student_id) === '-' ? 'text-slate-400' :
                  'bg-rose-100 text-rose-800'
                ]">
                  {{ gradeLetterFor(calculateStudentAverage(st.student_id)) }}
                </span>
              </td>

              <!-- Rank -->
              <td class="py-2 px-3 text-center font-bold border-l border-slate-100 bg-amber-50/20">
                <span v-if="calculateStudentRank(st.student_id) === 1" class="text-amber-600 font-extrabold">🥇 លេខ ១</span>
                <span v-else-if="calculateStudentRank(st.student_id) === 2" class="text-slate-600 font-extrabold">🥈 លេខ ២</span>
                <span v-else-if="calculateStudentRank(st.student_id) === 3" class="text-amber-800 font-extrabold">🥉 លេខ ៣</span>
                <span v-else-if="calculateStudentRank(st.student_id) !== '-'" class="text-slate-700 font-bold">លេខ {{ calculateStudentRank(st.student_id) }}</span>
                <span v-else class="text-slate-400 font-normal">-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div v-else-if="activeTab === 'report_cards'" class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div>
          <h3 class="font-bold text-slate-800 text-base">ព្រឹត្តិបត្រពិន្ទុ និងចំណាត់ថ្នាក់សិស្ស (Report Cards)</h3>
          <p class="text-xs text-slate-500">បោះពុម្ពសៀវភៅតាមដានការសិក្សា និងព្រឹត្តិបត្រពិន្ទុផ្លូវការ</p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">ជ្រើសរើសការប្រឡង</label>
          <select v-model="selectedExamId" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <option value="">-- ជ្រើសរើសសម័យប្រឡង --</option>
            <option v-for="ex in examsList" :key="ex.id" :value="ex.id">{{ ex.title }}</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">ជ្រើសរើសថ្នាក់</label>
          <select v-model="selectedClassForScores" @change="loadScoreSheet" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <option value="">-- ជ្រើសរើសថ្នាក់ --</option>
            <optgroup v-for="(gClasses, gKey) in groupedClasses" :key="gKey" :label="`-- កម្រិតថ្នាក់ទី ${gKey} --`">
              <option v-for="c in gClasses" :key="c" :value="c">ថ្នាក់ {{ c }}</option>
            </optgroup>
          </select>
        </div>
      </div>

      <!-- Quick Action: Official Class Ranking Sheet -->
      <div v-if="selectedExamId && selectedClassForScores" class="p-3 bg-indigo-50/70 border border-indigo-200/60 rounded-xl flex items-center justify-between">
        <div>
          <h4 class="font-bold text-xs text-indigo-950 flex items-center gap-1.5">
            <span>📜</span>
            <span>តារាងចំណាត់ថ្នាក់ប្រចាំខែផ្លូវការ (Official Class Monthly Ranking Sheet)</span>
          </h4>
          <p class="text-[11px] text-indigo-700">បោះពុម្ពតារាងចំណាត់ថ្នាក់សិស្សទាំងអស់ក្នុងថ្នាក់ {{ selectedClassForScores }} ជាទម្រង់ ២ ជួរឈរ (A4 Portrait ដូចទម្រង់ក្រសួង)</p>
        </div>
        <a 
          :href="`/examinations/${selectedExamId}/ranking-sheet?class=${selectedClassForScores}`" 
          target="_blank"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition inline-flex items-center gap-1.5 whitespace-nowrap"
        >
          <span>📜 បើកតារាងចំណាត់ថ្នាក់</span>
          <span>&rarr;</span>
        </a>
      </div>

      <div v-if="scoreSheetStudents.length > 0" class="overflow-x-auto pt-2">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="bg-slate-100 text-slate-700 uppercase font-bold border-b border-slate-200">
              <th class="py-3 px-4">អត្តលេខ</th>
              <th class="py-3 px-4">ឈ្មោះសិស្ស</th>
              <th class="py-3 px-4">ថ្នាក់</th>
              <th class="py-3 px-4 text-right">សកម្មភាព</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="st in scoreSheetStudents" :key="st.student_id" class="hover:bg-slate-50">
              <td class="py-3 px-4 font-mono font-bold text-slate-600">{{ st.student_id_card }}</td>
              <td class="py-3 px-4 font-bold text-slate-800">{{ st.name }}</td>
              <td class="py-3 px-4 font-bold text-blue-600">ថ្នាក់ {{ selectedClassForScores }}</td>
              <td class="py-3 px-4 text-right">
                <a 
                  :href="`/examinations/${selectedExamId}/report-card/${st.student_id}`" 
                  target="_blank" 
                  class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold hover:bg-blue-100 transition"
                >
                  🖨️ បោះពុម្ពព្រឹត្តិបត្រពិន្ទុ
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="py-8 text-center text-slate-400">
        សូមជ្រើសរើសការប្រឡង និងថ្នាក់រៀន ដើម្បីបោះពុម្ពព្រឹត្តិបត្រពិន្ទុ។
      </div>
    </div>

    <!-- Modal: Create Exam Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
            + បង្កើតសម័យប្រឡងថ្មី
          </h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitCreateExam" class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ចំណងជើងការប្រឡង (Title) *</label>
            <input v-model="createForm.title" type="text" required placeholder="ឧ. ការប្រឡងឆមាសទី ១ ឆ្នាំ២០២៥-២០២៦" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ប្រភេទប្រឡង (Exam Type) *</label>
              <select v-model="createForm.exam_type" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="ឆមាស">ប្រឡងឆមាស</option>
                <option value="ប្រចាំខែ">ប្រឡងប្រចាំខែ</option>
                <option value="ប្រចាំឆ្នាំ">ប្រឡងប្រចាំឆ្នាំ</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ឆមាស (Semester) *</label>
              <select v-model.number="createForm.semester" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option :value="1">ឆមាសទី ១</option>
                <option :value="2">ឆមាសទី ២</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ឆ្នាំសិក្សា (Academic Year)</label>
            <select v-model="createForm.academic_year_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
              <option :value="null">-- ជ្រើសរើសឆ្នាំសិក្សា --</option>
              <option v-for="y in academicYearsList" :key="y.id" :value="y.id">{{ y.name }}</option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កាលបរិច្ឆេទចាប់ផ្តើម</label>
              <input v-model="createForm.start_date" type="date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កាលបរិច្ឆេទបញ្ចប់</label>
              <input v-model="createForm.end_date" type="date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <div class="pt-2">
            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
              <input type="checkbox" v-model="createForm.is_published" class="w-4 h-4 text-blue-600 rounded">
              <span>ផ្សាយលទ្ធផលជាសាធារណៈ (Publish Scores)</span>
            </label>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">
              បោះបង់
            </button>
            <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50">
              <span v-if="isSubmitting">កំពុងរក្សាទុក...</span>
              <span v-else>រក្សាទុកការប្រឡង</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal: Edit Exam Modal -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            កែប្រែសម័យប្រឡង
          </h3>
          <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitUpdateExam" class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ចំណងជើងការប្រឡង *</label>
            <input v-model="editForm.title" type="text" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ប្រភេទប្រឡង *</label>
              <select v-model="editForm.exam_type" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="ឆមាស">ប្រឡងឆមាស</option>
                <option value="ប្រចាំខែ">ប្រឡងប្រចាំខែ</option>
                <option value="ប្រចាំឆ្នាំ">ប្រឡងប្រចាំឆ្នាំ</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ឆមាស *</label>
              <select v-model.number="editForm.semester" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option :value="1">ឆមាសទី ១</option>
                <option :value="2">ឆមាសទី ២</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កាលបរិច្ឆេទចាប់ផ្តើម</label>
              <input v-model="editForm.start_date" type="date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កាលបរិច្ឆេទបញ្ចប់</label>
              <input v-model="editForm.end_date" type="date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <div class="pt-2">
            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
              <input type="checkbox" v-model="editForm.is_published" class="w-4 h-4 text-blue-600 rounded">
              <span>ផ្សាយលទ្ធផលជាសាធារណៈ</span>
            </label>
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
    <!-- Modal: Import Excel / CSV Modal -->
    <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span>📤</span>
            បញ្ចូលពិន្ទុតាម File (Import Scores from Excel / CSV)
          </h3>
          <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <div class="p-6 space-y-4">
          <div class="p-3 bg-blue-50 border border-blue-200/60 rounded-xl text-xs text-blue-800 space-y-1">
            <p class="font-bold">💡 របៀបប្រើប្រាស់៖</p>
            <p>១. ទាញយក <strong>«គំរូ Excel»</strong> ដោយចុចប៊ូតុង "📥 គំរូ Excel" ជាមុនសិន។</p>
            <p>២. បំពេញពិន្ទុតាមមុខវិជ្ជានីមួយៗក្នុងកុំព្យូទ័ររបស់អ្នក រួចរក្សាទុកជា File CSV ឬ Excel។</p>
            <p>៣. ជ្រើសរើស File នោះមកបញ្ចូលទីនេះ ប្រព័ន្ធនឹងពិនិត្យផ្ទៀងផ្ទាត់ដោយស្វ័យប្រវត្តិ។</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ជ្រើសរើស File CSV / Text</label>
            <input 
              type="file" 
              accept=".csv, .txt" 
              @change="handleFileUpload" 
              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none"
            />
          </div>

          <!-- Preview of parsed records -->
          <div v-if="importPreviewRows.length > 0" class="space-y-2">
            <div class="flex items-center justify-between text-xs">
              <span class="font-bold text-emerald-700">✓ បានរកឃើញ {{ importPreviewRows.length }} ជួរសិស្ស</span>
              <span class="text-slate-500 font-mono text-[11px]">{{ importPreviewColumns.length }} ជួរឈរមុខវិជ្ជា</span>
            </div>

            <div class="max-h-56 overflow-y-auto border border-slate-200 rounded-xl">
              <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-600 sticky top-0">
                  <tr>
                    <th class="py-2 px-3">អត្តលេខ</th>
                    <th class="py-2 px-3">ឈ្មោះ</th>
                    <th v-for="col in importPreviewColumns.slice(0, 4)" :key="col" class="py-2 px-2 text-center">{{ col }}</th>
                    <th v-if="importPreviewColumns.length > 4" class="py-2 px-2 text-center text-slate-400">...</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="(row, idx) in importPreviewRows.slice(0, 10)" :key="idx" class="hover:bg-slate-50">
                    <td class="py-1.5 px-3 font-mono font-bold text-slate-700">{{ row.student_id_card }}</td>
                    <td class="py-1.5 px-3 text-slate-800">{{ row.name || '-' }}</td>
                    <td v-for="col in importPreviewColumns.slice(0, 4)" :key="col" class="py-1.5 px-2 text-center font-mono font-bold text-blue-600">
                      {{ row.scores[col] !== undefined ? row.scores[col] : '-' }}
                    </td>
                    <td v-if="importPreviewColumns.length > 4" class="py-1.5 px-2 text-center text-slate-400">...</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p v-if="importPreviewRows.length > 10" class="text-[11px] text-slate-400 text-center italic">
              បង្ហាញតែ ១០ ជួរគំរូប៉ុណ្ណោះ (សរុប {{ importPreviewRows.length }} ជួរ)
            </p>
          </div>

          <div v-if="importError" class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-medium">
            {{ importError }}
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button 
              type="button" 
              @click="showImportModal = false" 
              class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200"
            >
              បោះបង់
            </button>
            <button 
              type="button" 
              @click="submitImport" 
              :disabled="isImporting || importPreviewRows.length === 0" 
              class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50"
            >
              <span v-if="isImporting">កំពុងបញ្ចូល...</span>
              <span v-else>អនុវត្ត & រក្សាទុក (Import Now)</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import EntityBanner from '../components/EntityBanner.vue';
import StatCard from '../components/StatCard.vue';
import FilterBar from '../components/FilterBar.vue';
import DataTable from '../components/DataTable.vue';

const { t } = useI18n();

const activeTab = ref('exams');
const examTypeFilter = ref('');

const examsList = ref([]);
const classesList = ref([]);
const subjectsList = ref([]);
const academicYearsList = ref([]);

const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedExam = ref(null);
const isSubmitting = ref(false);

const selectedExamId = ref('');
const selectedClassForScores = ref('');
const selectedSubjectForScores = ref('');
const scoreSheetStudents = ref([]);
const scoresMap = ref({});
const isLoadingScores = ref(false);
const isSavingScores = ref(false);

// Matrix State
const selectedMatrixExamId = ref('');
const selectedMatrixClass = ref('');
const matrixSubjects = ref([]);
const matrixStudents = ref([]);
const matrixData = ref({});
const matrixStats = ref(null);
const isLoadingMatrix = ref(false);
const isSavingMatrix = ref(false);
const matrixSearch = ref('');
const showStudentIdInMatrix = ref(false);

// Import Modal State
const showImportModal = ref(false);
const isImporting = ref(false);
const importPreviewRows = ref([]);
const importPreviewColumns = ref([]);
const importError = ref('');

const createForm = ref({
  title: '',
  exam_type: 'ឆមាស',
  semester: 1,
  academic_year_id: null,
  start_date: '',
  end_date: '',
  is_published: false,
});

const editForm = ref({
  title: '',
  exam_type: 'ឆមាស',
  semester: 1,
  start_date: '',
  end_date: '',
  is_published: false,
});

const fetchExams = async () => {
  try {
    const res = await axios.get('/api/examinations');
    examsList.value = res.data.examinations || [];
    classesList.value = res.data.classes || [];
    subjectsList.value = res.data.subjects || [];
    academicYearsList.value = res.data.academicYears || [];
    if (!selectedExamId.value && examsList.value.length > 0) {
      selectedExamId.value = examsList.value[0].id;
    }
    if (!selectedMatrixExamId.value && examsList.value.length > 0) {
      selectedMatrixExamId.value = examsList.value[0].id;
    }
    if (!selectedClassForScores.value && classesList.value.length > 0) {
      selectedClassForScores.value = classesList.value.includes('11D') ? '11D' : (classesList.value.includes('10A') ? '10A' : classesList.value[0]);
    }
    if (!selectedMatrixClass.value && classesList.value.length > 0) {
      selectedMatrixClass.value = classesList.value.includes('11D') ? '11D' : (classesList.value.includes('10A') ? '10A' : classesList.value[0]);
    }
    if (!selectedSubjectForScores.value && subjectsList.value.length > 0) {
      selectedSubjectForScores.value = subjectsList.value[0].id;
    }
    if (activeTab.value === 'matrix') {
      await loadMatrixSheet();
    }
  } catch (err) {
    console.error('Failed to fetch exams:', err);
  }
};

onMounted(() => {
  fetchExams();
});

watch(activeTab, (tab) => {
  if (tab === 'matrix') {
    loadMatrixSheet();
  } else if (tab === 'scores') {
    loadScoreSheet();
  }
});

watch([selectedMatrixExamId, selectedMatrixClass], () => {
  if (selectedMatrixExamId.value && selectedMatrixClass.value) {
    loadMatrixSheet();
  }
});

watch([selectedExamId, selectedClassForScores, selectedSubjectForScores], () => {
  if (selectedExamId.value && selectedClassForScores.value && selectedSubjectForScores.value) {
    loadScoreSheet();
  }
});

const groupedClasses = computed(() => {
  const groups = {};
  classesList.value.forEach(c => {
    const match = c.match(/^(\d+)/);
    const grade = match ? match[1] : 'ផ្សេងៗ';
    if (!groups[grade]) groups[grade] = [];
    groups[grade].push(c);
  });
  return groups;
});

const filteredExams = computed(() => {
  return examsList.value.filter(ex => {
    return !examTypeFilter.value || ex.exam_type === examTypeFilter.value;
  });
});

const totalScoresCount = computed(() => {
  return examsList.value.reduce((a, ex) => a + (Number(ex.scores_count) || 0), 0);
});

const gradeLetterFor = (score) => {
  if (score === undefined || score === null || score === '' || isNaN(Number(score))) return '-';
  const num = Number(score);
  // Auto-detect Cambodian 50-point vs 100-point scale
  const is50Scale = (matrixStats.value?.scale_max === 50) || (num <= 50.0);
  if (is50Scale) {
    if (num >= 45.0) return 'A';
    if (num >= 40.0) return 'B';
    if (num >= 35.0) return 'C';
    if (num >= 30.0) return 'D';
    if (num >= 25.0) return 'E';
    return 'F';
  } else {
    if (num >= 90.0) return 'A';
    if (num >= 80.0) return 'B';
    if (num >= 70.0) return 'C';
    if (num >= 60.0) return 'D';
    if (num >= 50.0) return 'E';
    return 'F';
  }
};

const openScoresForExam = (ex) => {
  selectedExamId.value = ex.id;
  activeTab.value = 'scores';
  loadScoreSheet();
};

const loadScoreSheet = async () => {
  if (!selectedExamId.value || !selectedClassForScores.value) return;
  isLoadingScores.value = true;
  try {
    const res = await axios.get(`/api/examinations/${selectedExamId.value}/scores`, {
      params: {
        class: selectedClassForScores.value,
        subject_id: selectedSubjectForScores.value,
      }
    });
    scoreSheetStudents.value = res.data.students || [];
    scoresMap.value = {};
    scoreSheetStudents.value.forEach(st => {
      scoresMap.value[st.student_id] = st.score !== null ? st.score : '';
    });
  } catch (err) {
    console.error('Failed to load scores:', err);
  } finally {
    isLoadingScores.value = false;
  }
};

const submitSaveScores = async () => {
  if (!selectedExamId.value || !selectedClassForScores.value || !selectedSubjectForScores.value) {
    alert('សូមជ្រើសរើស សម័យប្រឡង ថ្នាក់រៀន និងមុខវិជ្ជាជាមុនសិន!');
    return;
  }
  isSavingScores.value = true;
  try {
    const res = await axios.post(`/api/examinations/${selectedExamId.value}/scores`, {
      class_name: selectedClassForScores.value,
      course_subject_id: selectedSubjectForScores.value,
      scores: scoresMap.value,
      max_score: 100,
    });
    alert(res.data.message || 'បានរក្សាទុកពិន្ទុដោយជោគជ័យ!');
    await fetchExams();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការរក្សាទុកពិន្ទុ!');
  } finally {
    isSavingScores.value = false;
  }
};

// --- MATRIX SHEET OPERATIONS ---
const loadMatrixSheet = async () => {
  if (!selectedMatrixExamId.value || !selectedMatrixClass.value) return;
  isLoadingMatrix.value = true;
  try {
    const res = await axios.get(`/api/examinations/${selectedMatrixExamId.value}/matrix-scores`, {
      params: { class: selectedMatrixClass.value }
    });
    matrixSubjects.value = res.data.subjects || [];
    matrixStudents.value = res.data.students || [];
    matrixStats.value = res.data.stats || null;

    const dataObj = {};
    matrixStudents.value.forEach(st => {
      dataObj[st.student_id] = {};
      matrixSubjects.value.forEach(sub => {
        dataObj[st.student_id][sub.id] = st.scores[sub.id] !== null && st.scores[sub.id] !== undefined ? st.scores[sub.id] : '';
      });
    });
    matrixData.value = dataObj;
  } catch (err) {
    console.error('Failed to load matrix scores:', err);
  } finally {
    isLoadingMatrix.value = false;
  }
};

const calculateStudentTotal = (studentId) => {
  const scoresObj = matrixData.value[studentId];
  if (!scoresObj) return '-';
  let total = 0;
  let count = 0;
  matrixSubjects.value.forEach(sub => {
    const val = scoresObj[sub.id];
    if (val !== undefined && val !== null && val !== '') {
      total += Number(val);
      count++;
    }
  });
  return count > 0 ? Number(total.toFixed(2)) : '-';
};

const calculateStudentAverage = (studentId) => {
  const scoresObj = matrixData.value[studentId];
  if (!scoresObj) return '-';
  let total = 0;
  let count = 0;
  let totalCredits = 0;
  matrixSubjects.value.forEach(sub => {
    const val = scoresObj[sub.id];
    if (val !== undefined && val !== null && val !== '') {
      total += Number(val);
      count++;
      totalCredits += Number(sub.credit || 1);
    }
  });
  if (count === 0) return '-';
  const divisor = totalCredits > 0 ? totalCredits : count;
  return Number((total / divisor).toFixed(2));
};

const liveRankMap = computed(() => {
  const list = matrixStudents.value.map(st => {
    const avg = calculateStudentAverage(st.student_id);
    return {
      student_id: st.student_id,
      average: avg === '-' ? -1 : Number(avg),
    };
  });
  list.sort((a, b) => b.average - a.average);

  const ranks = {};
  let currentRank = 1;
  list.forEach((item, index) => {
    if (item.average < 0) {
      ranks[item.student_id] = '-';
      return;
    }
    if (index > 0 && item.average < list[index - 1].average) {
      currentRank = index + 1;
    }
    ranks[item.student_id] = currentRank;
  });
  return ranks;
});

const calculateStudentRank = (studentId) => {
  return liveRankMap.value[studentId] ?? '-';
};

const filteredMatrixStudents = computed(() => {
  if (!matrixSearch.value) return matrixStudents.value;
  const s = matrixSearch.value.toLowerCase();
  return matrixStudents.value.filter(st => {
    return (st.name && st.name.toLowerCase().includes(s)) ||
           (st.student_id_card && st.student_id_card.toLowerCase().includes(s));
  });
});

const submitSaveMatrix = async () => {
  if (!selectedMatrixExamId.value || !selectedMatrixClass.value) {
    alert('សូមជ្រើសរើស សម័យប្រឡង និងថ្នាក់រៀនជាមុនសិន!');
    return;
  }
  isSavingMatrix.value = true;
  try {
    const res = await axios.post(`/api/examinations/${selectedMatrixExamId.value}/matrix-scores`, {
      class_name: selectedMatrixClass.value,
      matrix: matrixData.value
    });
    alert(res.data.message || 'បានរក្សាទុកតារាងពិន្ទុរួមដោយជោគជ័យ!');
    await loadMatrixSheet();
    await fetchExams();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការរក្សាទុកតារាងពិន្ទុ!');
  } finally {
    isSavingMatrix.value = false;
  }
};

const exportMatrixToExcel = () => {
  if (matrixStudents.value.length === 0) return;
  const exam = examsList.value.find(e => e.id == selectedMatrixExamId.value);
  const examTitle = exam ? exam.title.replace(/[\/\\?%*:|"<>]/g, '_') : 'Exam';
  
  // Headers WITHOUT 'អត្តលេខ'
  const headers = [
    'ល.រ',
    'ឈ្មោះសិស្ស',
    'ភេទ',
    'ថ្នាក់',
    ...matrixSubjects.value.map(s => `${s.name} (${s.code}${s.max_score ? ` [${s.max_score}pt/${s.credit || 1}cr]` : ''})`),
    'ពិន្ទុសរុប',
    'មធ្យមភាគ',
    'និទ្ទេស',
    'ចំណាត់ថ្នាក់'
  ];

  const rows = matrixStudents.value.map((st, idx) => {
    const scores = matrixSubjects.value.map(s => {
      const val = matrixData.value[st.student_id]?.[s.id];
      return val !== undefined && val !== null ? val : '';
    });
    const total = calculateStudentTotal(st.student_id);
    const avg = calculateStudentAverage(st.student_id);
    const grade = gradeLetterFor(avg);
    const rank = calculateStudentRank(st.student_id);

    return [
      idx + 1,
      `"${st.name}"`,
      `"${st.gender}"`,
      `"${selectedMatrixClass.value}"`,
      ...scores,
      total,
      avg,
      `"${grade}"`,
      rank
    ].join(',');
  });

  const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\n');
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.setAttribute('href', url);
  link.setAttribute('download', `តារាងពិន្ទុរួម_ថ្នាក់${selectedMatrixClass.value}_${examTitle}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const downloadMatrixTemplate = () => {
  if (matrixStudents.value.length === 0) {
    alert('សូមជ្រើសរើស សម័យប្រឡង និងថ្នាក់រៀនជាមុនសិន!');
    return;
  }
  const headers = [
    'ល.រ',
    'ឈ្មោះសិស្ស',
    'ភេទ',
    'ថ្នាក់',
    ...matrixSubjects.value.map(s => s.code || s.id)
  ];

  const rows = matrixStudents.value.map((st, idx) => {
    const emptyScores = matrixSubjects.value.map(() => '');
    return [
      idx + 1,
      `"${st.name}"`,
      `"${st.gender}"`,
      `"${selectedMatrixClass.value}"`,
      ...emptyScores
    ].join(',');
  });

  const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\n');
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.setAttribute('href', url);
  link.setAttribute('download', `គំរូបញ្ចូលពិន្ទុ_ថ្នាក់${selectedMatrixClass.value}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const openImportModal = () => {
  importPreviewRows.value = [];
  importPreviewColumns.value = [];
  importError.value = '';
  showImportModal.value = true;
};

const handleFileUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  importError.value = '';
  importPreviewRows.value = [];
  importPreviewColumns.value = [];

  const reader = new FileReader();
  reader.onload = (evt) => {
    try {
      const text = evt.target.result;
      const lines = text.split(/\r\n|\n/).filter(line => line.trim().length > 0);
      if (lines.length < 2) {
        importError.value = 'File គ្មានទិន្នន័យ ឬខ្វះជួរ Header!';
        return;
      }

      const headerLine = lines[0].replace(/^\uFEFF/, '');
      const rawHeaders = headerLine.split(',').map(h => h.trim().replace(/^["']|["']$/g, ''));
      
      const standardCols = ['ល.រ', 'no', 'student_id_card', 'អត្តលេខ', 'name', 'ឈ្មោះ', 'ឈ្មោះសិស្ស', 'gender', 'ភេទ', 'grade', 'class', 'ថ្នាក់', 'ពិន្ទុសរុប', 'total', 'មធ្យមភាគ', 'average', 'avg', 'និទ្ទេស', 'grade_letter', 'ចំណាត់ថ្នាក់', 'rank'];
      
      const subjectCols = [];
      const colIndexMap = {};

      rawHeaders.forEach((col, idx) => {
        const cleanCol = col.toLowerCase();
        if (!standardCols.includes(cleanCol)) {
          let codeOrId = col;
          const matchCode = col.match(/\(([^)]+)\)/);
          if (matchCode) {
            codeOrId = matchCode[1].trim();
          }
          subjectCols.push(codeOrId);
          colIndexMap[idx] = codeOrId;
        }
      });

      importPreviewColumns.value = subjectCols;

      const parsedRows = [];
      for (let i = 1; i < lines.length; i++) {
        const parts = lines[i].split(',').map(p => p.trim().replace(/^["']|["']$/g, ''));
        if (parts.length < 2) continue;

        let idCard = '';
        let name = '';
        if (parts[0] && parts[0].startsWith('STU-')) {
          idCard = parts[0];
          name = parts[1] || '';
        } else if (parts[1] && parts[1].startsWith('STU-')) {
          idCard = parts[1];
          name = parts[2] || '';
        } else if (isNaN(parts[1]) && parts[1] && parts[1].length > 0) {
          idCard = '';
          name = parts[1];
        } else {
          name = parts[0] || '';
        }

        const scores = {};
        Object.keys(colIndexMap).forEach(colIdx => {
          const subKey = colIndexMap[colIdx];
          const val = parts[colIdx];
          if (val !== undefined && val !== '' && !isNaN(val)) {
            scores[subKey] = Number(val);
          }
        });

        parsedRows.push({
          student_id_card: idCard,
          name: name,
          scores: scores
        });
      }

      importPreviewRows.value = parsedRows;
    } catch (err) {
      console.error(err);
      importError.value = 'មានបញ្ហាក្នុងការអាន File សូមពិនិត្យទម្រង់ CSV ឡើងវិញ!';
    }
  };
  reader.readAsText(file);
};

const submitImport = async () => {
  if (importPreviewRows.value.length === 0 || !selectedMatrixExamId.value || !selectedMatrixClass.value) {
    alert('គ្មានទិន្នន័យសម្រាប់បញ្ចូល!');
    return;
  }
  isImporting.value = true;
  try {
    const res = await axios.post(`/api/examinations/${selectedMatrixExamId.value}/import-scores`, {
      class_name: selectedMatrixClass.value,
      rows: importPreviewRows.value
    });
    alert(res.data.message || 'បានបញ្ចូលទិន្នន័យដោយជោគជ័យ!');
    showImportModal.value = false;
    await loadMatrixSheet();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការបញ្ចូលទិន្នន័យ!');
  } finally {
    isImporting.value = false;
  }
};

const printMatrixSheet = () => window.print();

const openCreateModal = () => {
  createForm.value = {
    title: '',
    exam_type: 'ឆមាស',
    semester: 1,
    academic_year_id: academicYearsList.value[0]?.id || null,
    start_date: new Date().toISOString().slice(0, 10),
    end_date: new Date(Date.now() + 7 * 86400000).toISOString().slice(0, 10),
    is_published: false,
  };
  showCreateModal.value = true;
};

const openEditModal = (ex) => {
  selectedExam.value = ex;
  editForm.value = {
    title: ex.title,
    exam_type: ex.exam_type || 'ឆមាស',
    semester: ex.semester || 1,
    start_date: ex.start_date || '',
    end_date: ex.end_date || '',
    is_published: ex.is_published,
  };
  showEditModal.value = true;
};

const submitCreateExam = async () => {
  isSubmitting.value = true;
  try {
    const res = await axios.post('/api/examinations', createForm.value);
    showCreateModal.value = false;
    alert(res.data.message || 'បានបង្កើតសម័យប្រឡងជោគជ័យ!');
    await fetchExams();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការបង្កើតសម័យប្រឡង!');
  } finally {
    isSubmitting.value = false;
  }
};

const submitUpdateExam = async () => {
  if (!selectedExam.value) return;
  isSubmitting.value = true;
  try {
    const res = await axios.put(`/api/examinations/${selectedExam.value.id}`, editForm.value);
    showEditModal.value = false;
    alert(res.data.message || 'បានកែប្រែសម័យប្រឡងជោគជ័យ!');
    await fetchExams();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការកែប្រែសម័យប្រឡង!');
  } finally {
    isSubmitting.value = false;
  }
};

const handleDelete = async (ex) => {
  if (!confirm(`តើអ្នកពិតជាចង់លុបសម័យប្រឡង "${ex.title}" និងពិន្ទុដែលពាក់ព័ន្ធទាំងអស់មែនទេ?`)) return;
  try {
    const res = await axios.delete(`/api/examinations/${ex.id}`);
    alert(res.data.message || 'បានលុបសម័យប្រឡងដោយជោគជ័យ!');
    await fetchExams();
  } catch (err) {
    alert(err.response?.data?.message || 'មិនអាចលុបសម័យប្រឡងបានទេ!');
  }
};

const handlePrint = () => window.print();

const handleExport = () => {
  const csv = [
    ['ឈ្មោះការប្រឡង', 'ប្រភេទ', 'ឆ្នាំសិក្សា', 'កាលបរិច្ឆេទចាប់ផ្តើម', 'កាលបរិច្ឆេទបញ្ចប់', 'ឆមាស', 'ស្ថានភាព'].join(','),
    ...examsList.value.map(ex => [
      `"${ex.title}"`,
      `"${ex.exam_type}"`,
      `"${ex.academic_year}"`,
      `"${ex.start_date || ''}"`,
      `"${ex.end_date || ''}"`,
      `"${ex.semester}"`,
      `"${ex.status}"`
    ].join(','))
  ].join('\n');

  const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `LMSTICK_Exams_${new Date().toISOString().slice(0, 10)}.csv`;
  a.click();
};
</script>
