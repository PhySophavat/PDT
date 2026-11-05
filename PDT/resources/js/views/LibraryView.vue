<template>
  <div class="space-y-6">
    <!-- Header -->
    <PageHeader 
      :title="$t('library.title')" 
      :breadcrumb="$t('nav.library')" 
      :actionLabel="$t('library.addBook')"
      @action="openAddBookModal"
    />

    <!-- Sub Tabs -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'books', label: $t('library.bookCatalog') + ` (${booksList.length})` },
        { id: 'borrows', label: $t('library.borrows') + ` (${borrowsList.length})` }
      ]"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="$t('library.totalBooks')" :value="stats.total" />
      <StatCard :title="$t('library.available')" :value="stats.available" />
      <StatCard :title="$t('library.activeBorrows')" :value="stats.borrows" />
      <StatCard :title="$t('library.overdueReturns')" :value="stats.overdue" />
    </div>

    <!-- Filter Bar -->
    <FilterBar>
      <template #left>
        <input 
          v-model="searchQuery" 
          type="text" 
          :placeholder="`${$t('common.search')} ${$t('library.bookTitle')}, ISBN, ${$t('library.author')}...`" 
          class="px-3.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs w-64 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
        />
      </template>

      <template #right>
        <button @click="openBorrowModal" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-1.5">
          <span>+ កត់ត្រាការខ្ចីសៀវភៅ</span>
        </button>
      </template>
    </FilterBar>

    <!-- TAB 1: Books Catalog -->
    <DataTable v-if="activeTab === 'books'" :loading="isLoading" :empty="filteredBooks.length === 0" :colSpan="8" loadingText="កំពុងទាញយកទិន្នន័យសៀវភៅ...">
      <template #header>
        <tr>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('library.isbn') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('library.bookTitle') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('library.author') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('library.category') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('library.quantity') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('library.available') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('common.status') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('common.actions') }}</th>
        </tr>
      </template>

      <template #body>
        <tr v-if="filteredBooks.length === 0">
          <td colspan="8" class="py-10 text-center text-slate-400">{{ $t('common.noData') }}</td>
        </tr>
        <tr v-for="b in filteredBooks" :key="b.id" class="hover:bg-slate-50/70 transition-colors">
          <td class="py-3 px-4 font-mono font-bold text-slate-500">{{ b.isbn || 'N/A' }}</td>
          <td class="py-3 px-4 font-bold text-slate-900">{{ b.title }}</td>
          <td class="py-3 px-4 text-slate-600">{{ b.author }}</td>
          <td class="py-3 px-4 text-slate-500">{{ b.category }}</td>
          <td class="py-3 px-4 text-center font-semibold text-slate-700">{{ b.quantity }} ក្បាល</td>
          <td class="py-3 px-4 text-center font-bold text-emerald-600">{{ b.available }} ក្បាល</td>
          <td class="py-3 px-4 text-center">
            <span :class="[
              'px-2.5 py-1 rounded-full text-[11px] font-bold',
              b.available > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'
            ]">
              {{ b.available > 0 ? $t('library.available') : $t('library.outOfStock') }}
            </span>
          </td>
          <td class="py-3 px-4 text-right">
            <button 
              v-if="b.available > 0"
              @click="quickBorrowForBook(b)" 
              class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition"
            >
              ឱ្យខ្ចី &rarr;
            </button>
            <span v-else class="text-xs text-rose-500 font-medium">អស់ស្តុក</span>
          </td>
        </tr>
      </template>
    </DataTable>

    <!-- TAB 2: Borrows Records -->
    <DataTable v-else :loading="isLoading" :empty="filteredBorrows.length === 0" :colSpan="6" loadingText="កំពុងទាញយកកំណត់ត្រាខ្ចីសៀវភៅ...">
      <template #header>
        <tr>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('library.bookTitle') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('library.borrower') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('library.borrowDate') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('library.dueDate') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('common.status') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('common.actions') }}</th>
        </tr>
      </template>

      <template #body>
        <tr v-if="filteredBorrows.length === 0">
          <td colspan="6" class="py-10 text-center text-slate-400">{{ $t('common.noData') }}</td>
        </tr>
        <tr v-for="br in filteredBorrows" :key="br.id" class="hover:bg-slate-50/70 transition-colors">
          <td class="py-3 px-4 font-bold text-slate-900">{{ br.book }}</td>
          <td class="py-3 px-4 font-semibold text-slate-800">{{ br.student }}</td>
          <td class="py-3 px-4 text-slate-600">{{ br.borrow_date }}</td>
          <td class="py-3 px-4 text-slate-600">{{ br.due_date }}</td>
          <td class="py-3 px-4 text-center">
            <span :class="[
              'px-2.5 py-1 rounded-full text-[11px] font-bold',
              br.status === 'Returned' ? 'bg-emerald-100 text-emerald-700' : 
              (br.status === 'Overdue' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700')
            ]">
              {{ br.status === 'Returned' ? 'បានសងរួច' : (br.status === 'Overdue' ? 'ហួសកំណត់' : 'កំពុងខ្ចី') }}
            </span>
          </td>
          <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
            <!-- Edit Button for Borrow Record -->
            <button 
              @click="openEditBorrowModal(br)"
              class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-200 transition"
              title="កែសម្រួលព័ត៌មាន ឬពន្យារពេលសង"
            >
              ✏️ កែប្រែ
            </button>
            <button 
              v-if="br.status !== 'Returned'" 
              @click="handleReturn(br)" 
              class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold hover:bg-emerald-100 transition"
            >
              ✓ ទទួលសៀវភៅសង
            </button>
            <span v-else class="text-xs text-slate-400">បានសងរួចរាល់</span>
          </td>
        </tr>
      </template>
    </DataTable>

    <!-- Modal 1: Add Book Modal -->
    <div v-if="showAddBookModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
            + បន្ថែមសៀវភៅថ្មីចូលបណ្ណាល័យ
          </h3>
          <button @click="showAddBookModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitAddBook" class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ចំណងជើងសៀវភៅ *</label>
            <input v-model="bookForm.title" type="text" required placeholder="ឧ. គណិតវិទ្យា ថ្នាក់ទី១១" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">អ្នកនិពន្ធ / ចងក្រង *</label>
              <input v-model="bookForm.author" type="text" required placeholder="ឧ. ក្រសួងអប់រំ" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កូដ ISBN</label>
              <input v-model="bookForm.isbn" type="text" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ប្រភេទសៀវភៅ</label>
              <select v-model="bookForm.book_category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option v-for="c in categoriesList" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ចំនួនសរុប (ក្បាល) *</label>
              <input v-model.number="bookForm.quantity" type="number" min="1" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button type="button" @click="showAddBookModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">
              បោះបង់
            </button>
            <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50">
              <span v-if="isSubmitting">កំពុងរក្សាទុក...</span>
              <span v-else>រក្សាទុកសៀវភៅ</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal 2: Searchable Borrow Book Modal (សរសេរដោយដៃ / ស្វែងរកងាយស្រួល) -->
    <div v-if="showBorrowModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
            + កត់ត្រាការខ្ចីសៀវភៅ (Borrow Book)
          </h3>
          <button @click="showBorrowModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitBorrowBook" class="p-6 space-y-4">
          <!-- 1. Searchable Book Input -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block text-xs font-bold text-slate-700">១. ជ្រើសរើស ឬវាយឈ្មោះសៀវភៅ *</label>
              <span v-if="selectedBorrowBook" class="text-[11px] font-bold text-emerald-600">
                នៅសល់: {{ selectedBorrowBook.available }} ក្បាល
              </span>
            </div>

            <!-- Selected Book Pill -->
            <div v-if="selectedBorrowBook" class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between">
              <div class="flex items-center gap-2 truncate">
                <span class="text-base">📖</span>
                <div>
                  <h4 class="text-xs font-bold text-emerald-950 truncate">{{ selectedBorrowBook.title }}</h4>
                  <p class="text-[10px] text-emerald-700">{{ selectedBorrowBook.author }} &bull; ISBN: {{ selectedBorrowBook.isbn || 'N/A' }}</p>
                </div>
              </div>
              <button type="button" @click="clearSelectedBook" class="text-xs font-bold text-rose-600 hover:text-rose-800 px-2 py-1 rounded hover:bg-rose-50 transition" title="ជ្រើសរើសសៀវភៅផ្សេង">
                ✕ ប្តូរ
              </button>
            </div>

            <!-- Search Book Field (Type by hand) -->
            <div v-else class="relative">
              <input 
                v-model="bookSearchQuery" 
                type="text" 
                required
                placeholder="វាយឈ្មោះសៀវភៅ, អ្នកនិពន្ធ ឬ ISBN ដោយដៃ..." 
                @focus="showBookSuggestions = true"
                class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
              />
              <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>

              <!-- Auto-suggest Dropdown -->
              <div v-if="showBookSuggestions && filteredBooksForBorrow.length > 0" class="absolute z-20 left-0 right-0 mt-1 max-h-48 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-lg divide-y divide-slate-100 custom-scrollbar">
                <div 
                  v-for="b in filteredBooksForBorrow" 
                  :key="b.id"
                  @click="selectBook(b)"
                  class="p-2.5 hover:bg-blue-50 cursor-pointer flex items-center justify-between transition"
                >
                  <div class="truncate pr-2">
                    <p class="text-xs font-bold text-slate-800 truncate">{{ b.title }}</p>
                    <p class="text-[10px] text-slate-500">{{ b.author }} &bull; {{ b.category }}</p>
                  </div>
                  <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0" :class="b.available > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">
                    នៅសល់ {{ b.available }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- 2. Searchable Student Input -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block text-xs font-bold text-slate-700">២. ជ្រើសរើស ឬវាយឈ្មោះសិស្សដែលខ្ចី *</label>
              <div class="flex items-center gap-1">
                <span class="text-[10px] text-slate-400">ថ្នាក់:</span>
                <select v-model="studentGradeFilter" class="text-[10px] py-0.5 px-1 bg-slate-100 border border-slate-200 rounded font-semibold focus:outline-none">
                  <option value="">គ្រប់ថ្នាក់</option>
                  <option v-for="g in uniqueStudentGrades" :key="g" :value="g">{{ g }}</option>
                </select>
              </div>
            </div>

            <!-- Selected Student Pill -->
            <div v-if="selectedBorrowStudent" class="p-2.5 bg-blue-50 border border-blue-200 rounded-xl flex items-center justify-between">
              <div class="flex items-center gap-2 truncate">
                <span class="text-base">👤</span>
                <div>
                  <h4 class="text-xs font-bold text-blue-950 truncate">{{ selectedBorrowStudent.name }}</h4>
                  <p class="text-[10px] text-blue-700">អត្តលេខ: {{ selectedBorrowStudent.student_id_card || 'N/A' }} &bull; ថ្នាក់: {{ selectedBorrowStudent.grade }}</p>
                </div>
              </div>
              <button type="button" @click="clearSelectedStudent" class="text-xs font-bold text-rose-600 hover:text-rose-800 px-2 py-1 rounded hover:bg-rose-50 transition" title="ជ្រើសរើសសិស្សផ្សេង">
                ✕ ប្តូរ
              </button>
            </div>

            <!-- Search Student Field (Type by hand) -->
            <div v-else class="relative">
              <input 
                v-model="studentSearchQuery" 
                type="text" 
                required
                placeholder="វាយឈ្មោះសិស្ស ឬអត្តលេខដោយដៃ..." 
                @focus="showStudentSuggestions = true"
                class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
              />
              <span class="absolute left-3 top-2.5 text-slate-400 text-xs">👤</span>

              <!-- Auto-suggest Dropdown -->
              <div v-if="showStudentSuggestions && filteredStudentsForBorrow.length > 0" class="absolute z-20 left-0 right-0 mt-1 max-h-48 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-lg divide-y divide-slate-100 custom-scrollbar">
                <div 
                  v-for="s in filteredStudentsForBorrow" 
                  :key="s.id"
                  @click="selectStudent(s)"
                  class="p-2.5 hover:bg-blue-50 cursor-pointer flex items-center justify-between transition"
                >
                  <div class="truncate pr-2">
                    <p class="text-xs font-bold text-slate-800 truncate">{{ s.name }}</p>
                    <p class="text-[10px] text-slate-500">អត្តលេខ: {{ s.student_id_card || 'N/A' }}</p>
                  </div>
                  <span class="text-[10px] font-bold px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full flex-shrink-0">
                    ថ្នាក់ {{ s.grade }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. Dates with Quick Duration Buttons -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ថ្ងៃខ្ចី *</label>
              <input v-model="borrowForm.borrow_date" type="date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ថ្ងៃត្រូវសងត្រឡប់ *</label>
              <input v-model="borrowForm.due_date" type="date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <!-- Quick Duration Selector -->
          <div class="flex items-center gap-2 pt-0.5">
            <span class="text-[11px] text-slate-500 font-medium">កំណត់រហ័ស:</span>
            <button type="button" @click="setDueDateDays(7)" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[10px] font-bold transition">
              + ៧ ថ្ងៃ (១ សប្តាហ៍)
            </button>
            <button type="button" @click="setDueDateDays(14)" class="px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-[10px] font-bold transition">
              + ១៤ ថ្ងៃ (២ សប្តាហ៍)
            </button>
            <button type="button" @click="setDueDateDays(30)" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[10px] font-bold transition">
              + ៣០ ថ្ងៃ (១ ខែ)
            </button>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button type="button" @click="showBorrowModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">
              បោះបង់
            </button>
            <button type="submit" :disabled="isSubmitting || !borrowForm.book_id || !borrowForm.student_id" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition disabled:opacity-50">
              <span v-if="isSubmitting">កំពុងកត់ត្រា...</span>
              <span v-else>កត់ត្រាការខ្ចី</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal 3: Edit Borrow Record Modal (កែសម្រួលព័ត៌មានការខ្ចី) -->
    <div v-if="showEditBorrowModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            កែសម្រួលព័ត៌មានការខ្ចីសៀវភៅ
          </h3>
          <button @click="showEditBorrowModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitUpdateBorrow" class="p-6 space-y-4">
          <!-- Book Selection -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">សៀវភៅដែលខ្ចី *</label>
            <select v-model="editBorrowForm.book_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
              <option v-for="b in booksList" :key="b.id" :value="b.id">
                {{ b.title }} ({{ b.author }})
              </option>
            </select>
          </div>

          <!-- Student Selection -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">សិស្សដែលខ្ចី *</label>
            <select v-model="editBorrowForm.student_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
              <option v-for="s in studentsList" :key="s.id" :value="s.id">
                {{ s.name }} ({{ s.student_id_card }}) - ថ្នាក់ {{ s.grade }}
              </option>
            </select>
          </div>

          <!-- Dates -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ថ្ងៃខ្ចី *</label>
              <input v-model="editBorrowForm.borrow_date" type="date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ថ្ងៃត្រូវសងត្រឡប់ *</label>
              <input v-model="editBorrowForm.due_date" type="date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <!-- Quick Extend Due Date Buttons -->
          <div class="flex items-center gap-2 pt-0.5">
            <span class="text-[11px] text-slate-500 font-medium">ពន្យារពេលរហ័ស:</span>
            <button type="button" @click="extendDueDate(7)" class="px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-[10px] font-bold transition">
              + ៧ ថ្ងៃ
            </button>
            <button type="button" @click="extendDueDate(14)" class="px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-[10px] font-bold transition">
              + ១៤ ថ្ងៃ
            </button>
            <button type="button" @click="extendDueDate(30)" class="px-2 py-1 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg text-[10px] font-bold transition">
              + ៣០ ថ្ងៃ
            </button>
          </div>

          <!-- Status Selector -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ស្ថានភាព *</label>
            <select v-model="editBorrowForm.status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
              <option value="borrowed">កំពុងខ្ចី (Borrowed)</option>
              <option value="returned">បានសងរួច (Returned)</option>
              <option value="overdue">ហួសកំណត់ (Overdue)</option>
            </select>
          </div>

          <div class="flex items-center justify-between pt-3 border-t border-slate-100">
            <button type="button" @click="deleteBorrowRecord" class="px-3 py-2 bg-rose-50 text-rose-600 rounded-xl text-xs font-bold hover:bg-rose-100 transition flex items-center gap-1">
              🗑️ លុបកំណត់ត្រានេះ
            </button>
            <div class="flex items-center gap-2">
              <button type="button" @click="showEditBorrowModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">
                បោះបង់
              </button>
              <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50">
                <span v-if="isSubmitting">កំពុងរក្សាទុក...</span>
                <span v-else>រក្សាទុកការកែប្រែ</span>
              </button>
            </div>
          </div>
        </form>
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

const activeTab = ref('books');
const searchQuery = ref('');
const isLoading = ref(true);

const stats = ref({
  total: 0,
  available: 0,
  borrows: 0,
  overdue: 0,
});

const booksList = ref([]);
const borrowsList = ref([]);
const categoriesList = ref([]);
const studentsList = ref([]);

// Hydrate from localStorage
try {
  const cached = localStorage.getItem('pdt_cached_library');
  if (cached) {
    const d = JSON.parse(cached);
    if (d.books && d.books.length) {
      booksList.value = d.books;
      isLoading.value = false;
    }
    if (d.stats) stats.value = d.stats;
    if (d.borrows) borrowsList.value = d.borrows;
    if (d.categories) categoriesList.value = d.categories;
    if (d.students) studentsList.value = d.students;
  }
} catch (e) {}

const showAddBookModal = ref(false);
const showBorrowModal = ref(false);
const showEditBorrowModal = ref(false);
const isSubmitting = ref(false);

const bookForm = ref({
  title: '',
  author: '',
  isbn: '',
  book_category_id: null,
  quantity: 5,
  publisher: '',
});

const borrowForm = ref({
  book_id: '',
  student_id: '',
  borrow_date: new Date().toISOString().slice(0, 10),
  due_date: new Date(Date.now() + 14 * 86400000).toISOString().slice(0, 10),
});

const editBorrowForm = ref({
  id: null,
  book_id: '',
  student_id: '',
  borrow_date: '',
  due_date: '',
  status: 'borrowed',
});

// Searchable combobox states for Borrow Modal
const bookSearchQuery = ref('');
const showBookSuggestions = ref(false);
const studentSearchQuery = ref('');
const showStudentSuggestions = ref(false);
const studentGradeFilter = ref('');

const selectedBorrowBook = computed(() => {
  return booksList.value.find(b => b.id === borrowForm.value.book_id) || null;
});

const selectedBorrowStudent = computed(() => {
  return studentsList.value.find(s => s.id === borrowForm.value.student_id) || null;
});

const uniqueStudentGrades = computed(() => {
  const grades = studentsList.value.map(s => s.grade).filter(Boolean);
  return [...new Set(grades)].sort();
});

const filteredBooksForBorrow = computed(() => {
  const q = bookSearchQuery.value.trim().toLowerCase();
  return booksList.value.filter(b => {
    if (b.available < 1 && (!selectedBorrowBook.value || selectedBorrowBook.value.id !== b.id)) return false;
    if (!q) return true;
    return b.title.toLowerCase().includes(q) || 
           b.author.toLowerCase().includes(q) || 
           (b.isbn && b.isbn.toLowerCase().includes(q));
  }).slice(0, 10);
});

const filteredStudentsForBorrow = computed(() => {
  const q = studentSearchQuery.value.trim().toLowerCase();
  const grade = studentGradeFilter.value;
  const list = studentsList.value.filter(s => {
    if (grade && s.grade !== grade) return false;
    if (!q) return true;
    return s.name.toLowerCase().includes(q) || 
           (s.student_id_card && s.student_id_card.toLowerCase().includes(q)) ||
           (s.grade && s.grade.toLowerCase().includes(q));
  });

  // Sort strictly by Khmer Alphabetical Order (អក្ខរក្រមខ្មែរ)
  return list.sort((a, b) => (a.name || '').localeCompare(b.name || '', 'km')).slice(0, 15);
});

const selectBook = (book) => {
  borrowForm.value.book_id = book.id;
  bookSearchQuery.value = book.title;
  showBookSuggestions.value = false;
};

const clearSelectedBook = () => {
  borrowForm.value.book_id = '';
  bookSearchQuery.value = '';
  showBookSuggestions.value = true;
};

const selectStudent = (student) => {
  borrowForm.value.student_id = student.id;
  studentSearchQuery.value = student.name;
  showStudentSuggestions.value = false;
};

const clearSelectedStudent = () => {
  borrowForm.value.student_id = '';
  studentSearchQuery.value = '';
  showStudentSuggestions.value = true;
};

const setDueDateDays = (days) => {
  const base = borrowForm.value.borrow_date ? new Date(borrowForm.value.borrow_date) : new Date();
  const target = new Date(base.getTime() + days * 86400000);
  borrowForm.value.due_date = target.toISOString().slice(0, 10);
};

const extendDueDate = (days) => {
  const base = editBorrowForm.value.due_date ? new Date(editBorrowForm.value.due_date) : new Date();
  const target = new Date(base.getTime() + days * 86400000);
  editBorrowForm.value.due_date = target.toISOString().slice(0, 10);
};

const fetchLibraryData = async () => {
  if (booksList.value.length === 0) {
    isLoading.value = true;
  }
  try {
    const res = await axios.get('/api/library', { params: { _t: Date.now() } });
    if (res.data) {
      stats.value = res.data.stats || { total: 0, available: 0, borrows: 0, overdue: 0 };
      booksList.value = res.data.books || [];
      borrowsList.value = res.data.borrows || [];
      categoriesList.value = res.data.categories || [];
      studentsList.value = [...(res.data.students || [])].sort((a, b) => (a.name || '').localeCompare(b.name || '', 'km'));
      try {
        localStorage.setItem('pdt_cached_library', JSON.stringify(res.data));
      } catch (e) {}
    }
  } catch (err) {
    console.error('Failed to fetch library:', err);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchLibraryData();
});

onActivated(() => {
  fetchLibraryData();
});

const filteredBooks = computed(() => {
  if (!searchQuery.value) return booksList.value;
  const q = searchQuery.value.toLowerCase();
  return booksList.value.filter(b => 
    b.title.toLowerCase().includes(q) || 
    b.author.toLowerCase().includes(q) || 
    (b.isbn && b.isbn.toLowerCase().includes(q))
  );
});

const filteredBorrows = computed(() => {
  if (!searchQuery.value) return borrowsList.value;
  const q = searchQuery.value.toLowerCase();
  return borrowsList.value.filter(br => 
    br.book.toLowerCase().includes(q) || 
    br.student.toLowerCase().includes(q)
  );
});

const openAddBookModal = () => {
  bookForm.value = {
    title: '',
    author: '',
    isbn: `978-99950-${Math.floor(100 + Math.random() * 900)}`,
    book_category_id: categoriesList.value[0]?.id || null,
    quantity: 5,
    publisher: 'ក្រសួងអប់រំ យុវជន និងកីឡា',
  };
  showAddBookModal.value = true;
};

const openBorrowModal = () => {
  const firstAvailBook = booksList.value.find(b => b.available > 0) || booksList.value[0];
  borrowForm.value = {
    book_id: firstAvailBook?.id || '',
    student_id: studentsList.value[0]?.id || '',
    borrow_date: new Date().toISOString().slice(0, 10),
    due_date: new Date(Date.now() + 14 * 86400000).toISOString().slice(0, 10),
  };
  bookSearchQuery.value = firstAvailBook?.title || '';
  studentSearchQuery.value = studentsList.value[0]?.name || '';
  showBookSuggestions.value = false;
  showStudentSuggestions.value = false;
  showBorrowModal.value = true;
};

const quickBorrowForBook = (book) => {
  borrowForm.value = {
    book_id: book.id,
    student_id: studentsList.value[0]?.id || '',
    borrow_date: new Date().toISOString().slice(0, 10),
    due_date: new Date(Date.now() + 14 * 86400000).toISOString().slice(0, 10),
  };
  bookSearchQuery.value = book.title;
  studentSearchQuery.value = studentsList.value[0]?.name || '';
  showBookSuggestions.value = false;
  showStudentSuggestions.value = false;
  showBorrowModal.value = true;
};

const openEditBorrowModal = (borrow) => {
  editBorrowForm.value = {
    id: borrow.id,
    book_id: borrow.book_id,
    student_id: borrow.student_id,
    borrow_date: borrow.borrow_date,
    due_date: borrow.due_date,
    status: borrow.status_raw || (borrow.status === 'Returned' ? 'returned' : 'borrowed'),
  };
  showEditBorrowModal.value = true;
};

const submitAddBook = async () => {
  isSubmitting.value = true;
  try {
    const res = await axios.post('/api/library/books', bookForm.value);
    showAddBookModal.value = false;
    alert(res.data.message || 'បានបន្ថែមសៀវភៅដោយជោគជ័យ!');
    await fetchLibraryData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការបន្ថែមសៀវភៅ!');
  } finally {
    isSubmitting.value = false;
  }
};

const submitBorrowBook = async () => {
  isSubmitting.value = true;
  try {
    const res = await axios.post('/api/library/borrow', borrowForm.value);
    showBorrowModal.value = false;
    alert(res.data.message || 'បានកត់ត្រាការខ្ចីសៀវភៅដោយជោគជ័យ!');
    await fetchLibraryData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការកត់ត្រាការខ្ចី!');
  } finally {
    isSubmitting.value = false;
  }
};

const submitUpdateBorrow = async () => {
  if (!editBorrowForm.value.id) return;
  isSubmitting.value = true;
  try {
    const res = await axios.put(`/api/library/borrow/${editBorrowForm.value.id}`, editBorrowForm.value);
    showEditBorrowModal.value = false;
    alert(res.data.message || 'បានកែសម្រួលព័ត៌មានការខ្ចីដោយជោគជ័យ!');
    await fetchLibraryData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការកែសម្រួលការខ្ចី!');
  } finally {
    isSubmitting.value = false;
  }
};

const deleteBorrowRecord = async () => {
  if (!editBorrowForm.value.id) return;
  if (!confirm('តើអ្នកពិតជាចង់លុបកំណត់ត្រាការខ្ចីនេះមែនទេ?')) return;
  isSubmitting.value = true;
  try {
    const res = await axios.delete(`/api/library/borrow/${editBorrowForm.value.id}`);
    showEditBorrowModal.value = false;
    alert(res.data.message || 'បានលុបកំណត់ត្រាដោយជោគជ័យ!');
    await fetchLibraryData();
  } catch (err) {
    alert(err.response?.data?.message || 'មិនអាចលុបកំណត់ត្រានេះបានទេ!');
  } finally {
    isSubmitting.value = false;
  }
};

const handleReturn = async (borrow) => {
  if (!confirm(`តើអ្នកពិតជាបានទទួលសៀវភៅ "${borrow.book}" ត្រឡប់មកវិញហើយមែនទេ?`)) return;
  try {
    const res = await axios.post(`/api/library/return/${borrow.id}`);
    alert(res.data.message || 'បានកត់ត្រាការសងសៀវភៅដោយជោគជ័យ!');
    await fetchLibraryData();
  } catch (err) {
    alert(err.response?.data?.message || 'មិនអាចកត់ត្រាការសងសៀវភៅបានទេ!');
  }
};

const handlePrint = () => window.print();

const handleExport = () => {
  const csv = [
    ['ISBN', 'ចំណងជើងសៀវភៅ', 'អ្នកនិពន្ធ', 'ប្រភេទ', 'ចំនួនសរុប', 'នៅសល់'].join(','),
    ...booksList.value.map(b => [
      `"${b.isbn || ''}"`,
      `"${b.title}"`,
      `"${b.author}"`,
      `"${b.category}"`,
      `"${b.quantity}"`,
      `"${b.available}"`
    ].join(','))
  ].join('\n');

  const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `LMSTICK_Library_${new Date().toISOString().slice(0, 10)}.csv`;
  a.click();
};
</script>
