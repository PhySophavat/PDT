<template>
  <div class="space-y-6">
    <!-- Header -->
    <PageHeader 
      :title="$t('classes.title')" 
      :breadcrumb="$t('classes.classList')" 
      :actionLabel="$t('classes.addClass')"
      @action="openCreateModal"
    />

    <!-- Sub Tabs -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'all', label: $t('classes.classList') },
        { id: 'grades', label: `${$t('classes.gradeLevel')} (7-12)` },
        { id: 'promote', label: $t('classes.promoteStudents') },
        { id: 'transfers', label: $t('classes.transferStudent') }
      ]"
    />

    <!-- Entity Banner -->
    <EntityBanner 
      :name="$t('classes.title')"
      :subtitle="$t('nav.academics') + ' - គ្រប់គ្រងថ្នាក់រៀន បន្ទប់សិក្សា និងការផ្ទេរថ្នាក់'"
      metaId="ADM-CLS-DIR"
      @print="handlePrint"
      @download="handleExport"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="$t('classes.totalClasses')" :value="classesList.length" />
      <StatCard :title="$t('classes.totalCapacity')" :value="totalCapacity" />
      <StatCard :title="$t('classes.enrolled')" :value="totalEnrolled" />
      <StatCard :title="$t('classes.activeRooms')" :value="activeRoomsCount" />
    </div>

    <!-- Filter Bar (For Tab 'all') -->
    <FilterBar v-if="activeTab === 'all' || activeTab === 'grades'">
      <template #left>
        <div class="flex items-center gap-2">
          <input 
            v-model="searchQuery"
            type="text" 
            :placeholder="`${$t('common.search')} ${$t('classes.className')} (7A, 8B, 12C)...`" 
            class="px-3.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs w-60 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          />
        </div>
      </template>

      <template #right>
        <div class="inline-flex items-center gap-3 px-3.5 py-1.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs">
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
            <input type="radio" v-model="gradeFilter" value="all" class="text-blue-600 focus:ring-0">
            <span>{{ $t('common.all') }}</span>
          </label>
          <label v-for="g in [7, 8, 9, 10, 11, 12]" :key="g" class="flex items-center gap-1 cursor-pointer text-slate-600">
            <input type="radio" v-model="gradeFilter" :value="g.toString()" class="text-blue-600 focus:ring-0">
            <span>ថ្នាក់ទី{{ g }}</span>
          </label>
        </div>

        <select v-model="yearFilter" class="px-3.5 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-medium focus:outline-none">
          <option value="">{{ $t('common.all') }} ឆ្នាំសិក្សា</option>
          <option v-for="y in academicYearsList" :key="y.id" :value="y.id">{{ y.name }}</option>
        </select>
      </template>
    </FilterBar>

    <!-- Tab 1: All Classes Table View -->
    <DataTable v-if="activeTab === 'all'">
      <template #header>
        <tr>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('classes.className') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('classes.gradeLevel') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('classes.classTeacher') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('classes.roomNumber') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('classes.capacity') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('classes.enrolled') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('common.actions') }}</th>
        </tr>
      </template>

      <template #body>
        <tr v-if="filteredClasses.length === 0">
          <td colspan="7" class="py-10 text-center text-slate-400">{{ $t('common.noData') }}</td>
        </tr>
        <tr v-for="c in filteredClasses" :key="c.id || c.name" class="hover:bg-slate-50/70 transition-colors">
          <td class="py-3 px-4 font-bold text-blue-600">
            <span class="inline-flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-blue-500"></span>
              ថ្នាក់ {{ c.name }}
            </span>
          </td>
          <td class="py-3 px-4 font-medium text-slate-700">{{ $t('classes.gradeLevel') }} {{ c.grade }}</td>
          <td class="py-3 px-4 text-slate-600 font-medium">
            <span v-if="c.teacher" class="text-slate-800">{{ c.teacher }}</span>
            <span v-else class="text-slate-400 italic">មិនទាន់ចាត់តាំង</span>
          </td>
          <td class="py-3 px-4 font-mono text-slate-600">{{ c.room || 'បន្ទប់ ' + c.name }}</td>
          <td class="py-3 px-4 text-center font-semibold text-slate-600">{{ c.capacity }} នាក់</td>
          <td class="py-3 px-4 text-center">
            <span :class="[
              'px-2.5 py-0.5 rounded-full text-xs font-bold',
              c.enrolled >= c.capacity ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600'
            ]">
              {{ c.enrolled }} នាក់
            </span>
          </td>
          <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
            <button @click="router.push(`/students?grade=${c.name}`)" class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-medium hover:bg-slate-200 transition">
              {{ $t('students.title') }}
            </button>
            <button @click="openEditModal(c)" class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition">
              {{ $t('common.edit') }}
            </button>
            <button @click="handleDelete(c)" class="px-2 py-1 bg-rose-50 text-rose-600 rounded-lg text-xs font-semibold hover:bg-rose-100 transition">
              {{ $t('common.delete') }}
            </button>
          </td>
        </tr>
      </template>
    </DataTable>

    <!-- Tab 2: Grouped by Grade Levels (7 to 12) -->
    <div v-else-if="activeTab === 'grades'" class="space-y-6">
      <div v-for="g in [7, 8, 9, 10, 11, 12]" :key="g" class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-lg">
              {{ g }}
            </div>
            <div>
              <h3 class="font-bold text-slate-800 text-base">កម្រិតថ្នាក់ទី {{ g }}</h3>
              <p class="text-xs text-slate-500">មានចំនួន {{ classesByGrade(g).length }} ថ្នាក់សរុប</p>
            </div>
          </div>
          <button @click="openCreateForGrade(g)" class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-xl text-xs font-semibold hover:bg-blue-100 transition">
            + បន្ថែមថ្នាក់ទី {{ g }}
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
          <div 
            v-for="c in classesByGrade(g)" 
            :key="c.id || c.name"
            class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-blue-50/40 hover:border-blue-200 transition-all flex flex-col justify-between"
          >
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="font-bold text-blue-700 text-lg">ថ្នាក់ {{ c.name }}</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-600">
                  {{ c.enrolled }}/{{ c.capacity }} នាក់
                </span>
              </div>
              <p class="text-xs text-slate-600 mb-1">
                <strong>គ្រូបន្ទុក:</strong> {{ c.teacher || 'មិនទាន់ចាត់តាំង' }}
              </p>
              <p class="text-xs text-slate-500 font-mono">
                {{ c.room || 'បន្ទប់ ' + c.name }}
              </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs">
              <button @click="openEditModal(c)" class="text-blue-600 font-medium hover:underline">
                {{ $t('common.edit') }}
              </button>
              <button @click="router.push(`/students?grade=${c.name}`)" class="text-slate-600 font-medium hover:text-slate-900">
                មើលសិស្ស &rarr;
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab 3: Promote Students Form -->
    <div v-else-if="activeTab === 'promote'" class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm max-w-2xl mx-auto">
      <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
          🎓
        </div>
        <div>
          <h3 class="font-bold text-slate-800 text-lg">ឡើងថ្នាក់សិស្សស្វ័យប្រវត្តិ (Promote Students)</h3>
          <p class="text-xs text-slate-500">ផ្ទេរសិស្សទាំងអស់ពីថ្នាក់ចាស់ ទៅថ្នាក់ថ្មីក្នុងឆ្នាំសិក្សាថ្មី</p>
        </div>
      </div>

      <form @submit.prevent="submitPromote" class="space-y-5">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">ថ្នាក់បច្ចុប្បន្ន (Current Class) *</label>
          <select v-model="promoteForm.current_class" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <option value="">-- សូមជ្រើសរើសថ្នាក់បច្ចុប្បន្ន --</option>
            <option v-for="c in classesList" :key="c.id" :value="c.name">
              ថ្នាក់ {{ c.name }} (មានសិស្ស {{ c.enrolled }} នាក់)
            </option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">ថ្នាក់បន្ទាប់ដែលត្រូវឡើង (Next Target Class) *</label>
          <select v-model="promoteForm.next_class" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <option value="">-- សូមជ្រើសរើសថ្នាក់គោលដៅ --</option>
            <option v-for="c in classesList" :key="c.id" :value="c.name">
              ថ្នាក់ {{ c.name }}
            </option>
          </select>
        </div>

        <div class="p-4 bg-amber-50 border border-amber-200/80 rounded-xl text-xs text-amber-800">
          ⚠️ <strong>ចំណាំ:</strong> សិស្សទាំងអស់ដែលកំពុងស្ថិតក្នុងថ្នាក់បច្ចុប្បន្ន នឹងត្រូវផ្លាស់ប្តូរទៅកាន់ថ្នាក់ថ្មីដោយស្វ័យប្រវត្តិក្នុងមូលដ្ឋានទិន្នន័យ។
        </div>

        <div class="pt-2">
          <button 
            type="submit" 
            :disabled="isPromoting"
            class="w-full py-2.5 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 transition shadow-sm disabled:opacity-50"
          >
            <span v-if="isPromoting">កំពុងដំណើរការឡើងថ្នាក់...</span>
            <span v-else>អនុវត្តការឡើងថ្នាក់ &rarr;</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Tab 4: Transfer Single Student -->
    <div v-else-if="activeTab === 'transfers'" class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm max-w-2xl mx-auto">
      <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
          🔄
        </div>
        <div>
          <h3 class="font-bold text-slate-800 text-lg">ផ្ទេរថ្នាក់សិស្សបុគ្គល (Transfer Student)</h3>
          <p class="text-xs text-slate-500">ប្តូរថ្នាក់រៀនសម្រាប់សិស្សម្នាក់ៗយ៉ាងឆាប់រហ័ស</p>
        </div>
      </div>

      <form @submit.prevent="submitTransfer" class="space-y-5">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">ជ្រើសរើសសិស្សដែលត្រូវផ្ទេរ *</label>
          <select v-model="transferForm.student_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <option value="">-- សូមជ្រើសរើសសិស្ស --</option>
            <option v-for="s in studentsList" :key="s.id" :value="s.id">
              {{ s.name }} ({{ s.student_id_card || 'អត្តលេខ' }}) - ថ្នាក់បច្ចុប្បន្ន: {{ s.grade || 'គ្មាន' }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">ថ្នាក់ថ្មីដែលត្រូវចូលរៀន *</label>
          <select v-model="transferForm.target_class" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <option value="">-- សូមជ្រើសរើសថ្នាក់គោលដៅ --</option>
            <option v-for="c in classesList" :key="c.id" :value="c.name">
              ថ្នាក់ {{ c.name }} (បន្ទប់: {{ c.room }})
            </option>
          </select>
        </div>

        <div class="pt-2">
          <button 
            type="submit" 
            :disabled="isTransferring"
            class="w-full py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition shadow-sm disabled:opacity-50"
          >
            <span v-if="isTransferring">កំពុងផ្ទេរថ្នាក់...</span>
            <span v-else>រក្សាទុកការផ្ទេរថ្នាក់ &rarr;</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Modal 1: Create Class Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
            + បង្កើតថ្នាក់រៀនថ្មី
          </h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitCreateClass" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កម្រិតថ្នាក់ (Grade) *</label>
              <select v-model="createForm.grade" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option v-for="g in [7, 8, 9, 10, 11, 12]" :key="g" :value="g.toString()">ថ្នាក់ទី {{ g }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">បន្ទប់/ផ្នែក (Section) *</label>
              <select v-model="createForm.section" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option v-for="sec in ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']" :key="sec" :value="sec">បន្ទប់ {{ sec }}</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ឈ្មោះបន្ទប់សិក្សា</label>
              <input v-model="createForm.room_number" type="text" placeholder="ឧ. Building A - Room 101" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ចំណុះសិស្សអតិបរមា</label>
              <input v-model.number="createForm.capacity" type="number" min="1" max="100" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">គ្រូបន្ទុកថ្នាក់ (Class Teacher)</label>
            <select v-model="createForm.teacher_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
              <option :value="null">-- មិនទាន់ចាត់តាំង --</option>
              <option v-for="t in teachersList" :key="t.id" :value="t.id">{{ t.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ឆ្នាំសិក្សា (Academic Year)</label>
            <select v-model="createForm.academic_year_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
              <option :value="null">-- ឆ្នាំសិក្សាបច្ចុប្បន្ន --</option>
              <option v-for="y in academicYearsList" :key="y.id" :value="y.id">{{ y.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ការពិពណ៌នាបន្ថែម</label>
            <textarea v-model="createForm.description" rows="2" placeholder="ព័ត៌មានបន្ថែមអំពីថ្នាក់រៀន..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">
              បោះបង់
            </button>
            <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50">
              <span v-if="isSubmitting">កំពុងរក្សាទុក...</span>
              <span v-else>រក្សាទុកថ្នាក់រៀន</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal 2: Edit Class Modal -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            កែប្រែព័ត៌មានថ្នាក់ {{ selectedClass?.name }}
          </h3>
          <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitUpdateClass" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">បន្ទប់សិក្សា</label>
              <input v-model="editForm.room_number" type="text" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ចំណុះសិស្ស</label>
              <input v-model.number="editForm.capacity" type="number" min="1" max="100" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">គ្រូបន្ទុកថ្នាក់ (Class Teacher)</label>
            <select v-model="editForm.teacher_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
              <option :value="null">-- មិនទាន់ចាត់តាំង --</option>
              <option v-for="t in teachersList" :key="t.id" :value="t.id">{{ t.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ឆ្នាំសិក្សា</label>
            <select v-model="editForm.academic_year_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
              <option :value="null">-- ឆ្នាំសិក្សាបច្ចុប្បន្ន --</option>
              <option v-for="y in academicYearsList" :key="y.id" :value="y.id">{{ y.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ការពិពណ៌នា</label>
            <textarea v-model="editForm.description" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"></textarea>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import axios from 'axios';
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
const gradeFilter = ref('all');
const yearFilter = ref('');

const classesList = ref([]);
const teachersList = ref([]);
const academicYearsList = ref([]);
const studentsList = ref([]);

const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedClass = ref(null);
const isSubmitting = ref(false);
const isPromoting = ref(false);
const isTransferring = ref(false);

const createForm = ref({
  grade: '7',
  section: 'A',
  room_number: '',
  capacity: 40,
  teacher_id: null,
  academic_year_id: null,
  description: '',
});

const editForm = ref({
  room_number: '',
  capacity: 40,
  teacher_id: null,
  academic_year_id: null,
  description: '',
});

const promoteForm = ref({
  current_class: '',
  next_class: '',
});

const transferForm = ref({
  student_id: '',
  target_class: '',
});

const fetchClassesData = async () => {
  try {
    const res = await axios.get('/api/classes');
    classesList.value = res.data.classes || [];
    teachersList.value = res.data.teachers || [];
    academicYearsList.value = res.data.academicYears || [];
    studentsList.value = res.data.students || [];
  } catch (err) {
    console.error('Failed to fetch classes:', err);
  }
};

onMounted(() => {
  fetchClassesData();
});

const filteredClasses = computed(() => {
  return classesList.value.filter(c => {
    const matchesSearch = !searchQuery.value || 
      c.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (c.teacher && c.teacher.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
      (c.room && c.room.toLowerCase().includes(searchQuery.value.toLowerCase()));
    const matchesGrade = gradeFilter.value === 'all' || c.grade.toString() === gradeFilter.value;
    const matchesYear = !yearFilter.value || c.academic_year_id === yearFilter.value;
    return matchesSearch && matchesGrade && matchesYear;
  });
});

const classesByGrade = (grade) => {
  return classesList.value.filter(c => c.grade.toString() === grade.toString());
};

const totalCapacity = computed(() => classesList.value.reduce((a, c) => a + (Number(c.capacity) || 0), 0));
const totalEnrolled = computed(() => classesList.value.reduce((a, c) => a + (Number(c.enrolled) || 0), 0));
const activeRoomsCount = computed(() => classesList.value.filter(c => !!c.room).length || classesList.value.length);

const openCreateModal = () => {
  createForm.value = {
    grade: '7',
    section: 'A',
    room_number: 'Building A - Room 101',
    capacity: 40,
    teacher_id: null,
    academic_year_id: null,
    description: '',
  };
  showCreateModal.value = true;
};

const openCreateForGrade = (grade) => {
  createForm.value = {
    grade: grade.toString(),
    section: 'A',
    room_number: `Building ${grade > 9 ? 'C' : 'A'} - Room ${grade}01`,
    capacity: 40,
    teacher_id: null,
    academic_year_id: null,
    description: '',
  };
  showCreateModal.value = true;
};

const openEditModal = (c) => {
  selectedClass.value = c;
  editForm.value = {
    room_number: c.room || '',
    capacity: c.capacity || 40,
    teacher_id: c.teacher_id || null,
    academic_year_id: c.academic_year_id || null,
    description: c.description || '',
  };
  showEditModal.value = true;
};

const submitCreateClass = async () => {
  isSubmitting.value = true;
  try {
    const res = await axios.post('/api/classes', createForm.value);
    showCreateModal.value = false;
    alert(res.data.message || 'បានបង្កើតថ្នាក់ជោគជ័យ!');
    await fetchClassesData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការបង្កើតថ្នាក់!');
  } finally {
    isSubmitting.value = false;
  }
};

const submitUpdateClass = async () => {
  if (!selectedClass.value) return;
  isSubmitting.value = true;
  try {
    const res = await axios.put(`/api/classes/${selectedClass.value.id}`, editForm.value);
    showEditModal.value = false;
    alert(res.data.message || 'បានកែប្រែព័ត៌មានថ្នាក់ជោគជ័យ!');
    await fetchClassesData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការកែប្រែថ្នាក់!');
  } finally {
    isSubmitting.value = false;
  }
};

const handleDelete = async (c) => {
  if (!confirm(`តើអ្នកប្រាកដជាចង់លុបថ្នាក់ ${c.name} នេះមែនទេ?`)) return;
  try {
    const res = await axios.delete(`/api/classes/${c.id}`);
    alert(res.data.message || 'បានលុបថ្នាក់ដោយជោគជ័យ!');
    await fetchClassesData();
  } catch (err) {
    alert(err.response?.data?.message || 'មិនអាចលុបថ្នាក់នេះបានទេ!');
  }
};

const submitPromote = async () => {
  if (!promoteForm.value.current_class || !promoteForm.value.next_class) return;
  if (promoteForm.value.current_class === promoteForm.value.next_class) {
    alert('ថ្នាក់បច្ចុប្បន្ន និងថ្នាក់ថ្មីមិនអាចដូចគ្នាបានទេ!');
    return;
  }
  isPromoting.value = true;
  try {
    const res = await axios.post('/api/classes/promote', promoteForm.value);
    alert(res.data.message || 'បានឡើងថ្នាក់សិស្សដោយជោគជ័យ!');
    promoteForm.value.current_class = '';
    promoteForm.value.next_class = '';
    await fetchClassesData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការឡើងថ្នាក់!');
  } finally {
    isPromoting.value = false;
  }
};

const submitTransfer = async () => {
  if (!transferForm.value.student_id || !transferForm.value.target_class) return;
  isTransferring.value = true;
  try {
    const res = await axios.post('/api/classes/transfer', transferForm.value);
    alert(res.data.message || 'បានផ្ទេរថ្នាក់សិស្សដោយជោគជ័យ!');
    transferForm.value.student_id = '';
    transferForm.value.target_class = '';
    await fetchClassesData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការផ្ទេរថ្នាក់!');
  } finally {
    isTransferring.value = false;
  }
};

const handlePrint = () => window.print();

const handleExport = () => {
  const csv = [
    ['ឈ្មោះថ្នាក់', 'កម្រិត', 'គ្រូបន្ទុក', 'បន្ទប់', 'ចំណុះ', 'សិស្សកំពុងរៀន'].join(','),
    ...classesList.value.map(c => [
      `"${c.name}"`,
      `"${c.grade}"`,
      `"${c.teacher || ''}"`,
      `"${c.room || ''}"`,
      `"${c.capacity}"`,
      `"${c.enrolled}"`
    ].join(','))
  ].join('\n');

  const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `LMSTICK_Classes_${new Date().toISOString().slice(0, 10)}.csv`;
  a.click();
};
</script>
