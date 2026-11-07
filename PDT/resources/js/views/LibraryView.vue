<template>
  <div class="space-y-6">
    <!-- Header -->
    <PageHeader 
      title="បណ្ណាល័យឌីជីថល និងសៀវភៅពុម្ព (Digital Library)" 
      :breadcrumb="$t('nav.library')" 
      :actionLabel="canManageLibrary ? '+ បន្ថែមសៀវភៅថ្មី' : ''"
      @action="canManageLibrary ? openAddBookModal() : null"
    >
      <template #extraActions>
        <button 
          v-if="canManageLibrary"
          type="button" 
          @click="showManageSubjectsModal = true"
          class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs"
          title="គ្រប់គ្រងមុខវិជ្ជាសិក្សាជាតិ"
        >
          <i class="fa-solid fa-graduation-cap text-indigo-600"></i>
          <span class="hidden sm:inline">គ្រប់គ្រងមុខវិជ្ជា ({{ subjectsList.length }})</span>
        </button>
      </template>
    </PageHeader>

    <!-- Sub Tabs (Filtered by Role) -->
    <TabNav 
      v-if="availableTabs.length > 1"
      v-model="activeTab" 
      :tabs="availableTabs"
    />

    <!-- Compact Modern Hero Banner -->
    <div class="py-5 px-6 bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 text-white rounded-2xl shadow-md border border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-xl flex-shrink-0 shadow-inner">
          📚
        </div>
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <h4 class="font-bold text-sm sm:text-base tracking-tight text-white">បណ្ណាល័យអេឡិចត្រូនិកជាតិ ថ្នាក់ទី៧–១២</h4>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-400 text-emerald-950">អានបាន ២៤/៧</span>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-white/15 text-blue-100 border border-white/20">MoEYS E-Library</span>
          </div>
          <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
            បណ្តុំសៀវភៅពុម្ពផ្លូវការគ្រប់មុខវិជ្ជាជាតិ សៀវភៅជំនួយស្មារតី កំនែមេរៀន និងឯកសារត្រៀមប្រឡងឌីប្លូម & បាក់ឌុប អាន និងរក្សាទុកក្នុងទូរស័ព្ទដៃបានភ្លាមៗ។
          </p>
        </div>
      </div>
      <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto justify-end">
        <button 
          v-if="activeTab !== 'elibrary'"
          type="button" 
          @click="activeTab = 'elibrary'"
          class="px-4 py-2 rounded-xl bg-white text-indigo-900 hover:bg-slate-100 text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer"
        >
          <i class="fa-solid fa-book-open text-rose-500"></i>
          <span>ចូលអានសៀវភៅ PDF</span>
        </button>
      </div>
    </div>

    <!-- TAB 1: Digital E-Library (Grades 7–12) -->
    <div v-if="activeTab === 'elibrary'" class="space-y-4">
      
      <!-- Sleek 3-Row Dedicated Control Bar (Never Clipped) -->
      <div class="bg-white/80 backdrop-blur-md border border-slate-200/60 rounded-2xl p-4 shadow-sm space-y-3">
        <!-- Row 1: Search, Level dropdown & Saved/Recent Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <!-- Search input -->
          <div class="relative w-full sm:w-80 shrink-0">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="ស្វែងរកចំណងជើង, អ្នកនិពន្ធ, មុខវិជ្ជា..." 
              class="w-full pl-8 pr-3 py-1.5 bg-slate-100/70 hover:bg-slate-100 focus:bg-white border border-slate-200/80 rounded-xl text-xs placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"
            />
          </div>

          <!-- Level & Tab View Filters -->
          <div class="flex items-center gap-2 shrink-0 justify-end flex-wrap">
            <select 
              v-model="selectedLevel" 
              class="px-2.5 py-1.5 bg-slate-100/70 border border-slate-200/80 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
            >
              <option value="">គ្រប់កម្រិត</option>
              <option value="basic">មូលដ្ឋាន</option>
              <option value="advanced">កម្រិតខ្ពស់</option>
              <option value="អក្សរសិល្ប៍">អក្សរសិល្ប៍</option>
            </select>

            <div class="flex items-center gap-1 bg-slate-100/80 p-0.5 rounded-xl border border-slate-200/70">
              <button 
                type="button"
                @click="subFilter = 'all'"
                :class="[
                  'px-2 py-1 rounded-lg text-xs font-semibold transition cursor-pointer',
                  subFilter === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
                ]"
                title="សៀវភៅទាំងអស់"
              >
                ទាំងអស់
              </button>
              <button 
                type="button"
                @click="subFilter = 'bookmarks'"
                :class="[
                  'px-2 py-1 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1',
                  subFilter === 'bookmarks' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
                ]"
                title="សៀវភៅបានរក្សាទុក"
              >
                <i class="fa-solid fa-star text-[10px]"></i>
                <span>({{ bookmarkedIds.length }})</span>
              </button>
              <button 
                type="button"
                @click="subFilter = 'recent'"
                :class="[
                  'px-2 py-1 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1',
                  subFilter === 'recent' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
                ]"
                title="ទើបអានរួច"
              >
                <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
                <span>({{ recentlyViewedIds.length }})</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Row 2: Dedicated Grade Pills Row (Never Clipped) -->
        <div class="pt-2.5 border-t border-slate-100/90 flex items-center justify-between gap-2 flex-wrap">
          <div class="flex items-center gap-2 overflow-x-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] text-xs py-0.5">
            <span class="text-[11px] font-bold text-slate-500 whitespace-nowrap flex items-center gap-1 mr-1 shrink-0">
              <i class="fa-solid fa-layer-group text-indigo-600"></i>
              <span>កម្រិតថ្នាក់៖</span>
            </span>

            <button 
              type="button"
              @click="selectGrade('')"
              :class="[
                'px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer flex items-center gap-1.5 shrink-0',
                selectedGrade === '' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
              ]"
            >
              <span>គ្រប់ថ្នាក់ទាំងអស់</span>
              <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="selectedGrade === '' ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">{{ eLibraryBooks.length }}</span>
            </button>

            <button 
              v-for="g in ['7', '8', '9', '10', '11', '12']" 
              :key="g"
              type="button"
              @click="selectGrade(g)"
              :class="[
                'px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer flex items-center gap-1.5 shrink-0',
                selectedGrade === g ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
              ]"
            >
              <span>ថ្នាក់ទី {{ g }}</span>
              <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="selectedGrade === g ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">
                {{ countForGrade(g) }}
              </span>
            </button>
          </div>
        </div>

        <!-- Row 3: Subject Filter Pills (Exact order: [ទាំងអស់] -> [អក្សរសិល្ប៍ (8)] -> ...) -->
        <div class="pt-2.5 border-t border-slate-100/90 flex items-center justify-between gap-2">
          <div class="flex-1 min-w-0 flex items-center gap-1.5">
            <span class="text-[11px] font-bold text-slate-400 whitespace-nowrap flex items-center gap-1 mr-1 shrink-0">
              <i class="fa-solid fa-book-bookmark text-slate-400"></i>
              <span>មុខវិជ្ជា៖</span>
            </span>

            <!-- Scroll Left Arrow (Desktop Helper) -->
            <button 
              type="button" 
              @click="scrollSubjects('left')" 
              class="hidden sm:flex w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 items-center justify-center shrink-0 cursor-pointer text-[10px] transition shadow-2xs"
              title="រមូរទៅឆ្វេង (Scroll Left)"
            >
              <i class="fa-solid fa-chevron-left"></i>
            </button>

            <!-- Scrollable Subjects Container -->
            <div 
              ref="subjectScrollRef" 
              @wheel="onSubjectWheel"
              class="flex-1 min-w-0 flex items-center gap-1.5 overflow-x-auto scroll-smooth [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] text-xs py-0.5"
            >
              <button 
                type="button"
                @click="selectSubject('')"
                :class="[
                  'px-2.5 py-1 rounded-lg font-medium transition whitespace-nowrap cursor-pointer text-xs shrink-0',
                  selectedSubjectId === '' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                ]"
              >
                ទាំងអស់
              </button>

              <button 
                v-for="sub in sortedSubjectsList" 
                :key="sub.id"
                type="button"
                @click="selectSubject(sub.id)"
                :class="[
                  'px-2.5 py-1 rounded-lg font-medium transition whitespace-nowrap cursor-pointer text-xs flex items-center gap-1.5 shrink-0',
                  selectedSubjectId === sub.id ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                ]"
              >
                <span>{{ sub.name }}</span>
                <span v-if="countForSubject(sub.id) > 0" class="text-[10px] opacity-75">({{ countForSubject(sub.id) }})</span>
              </button>
            </div>

            <!-- Scroll Right Arrow (Desktop Helper) -->
            <button 
              type="button" 
              @click="scrollSubjects('right')" 
              class="hidden sm:flex w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 items-center justify-center shrink-0 cursor-pointer text-[10px] transition shadow-2xs"
              title="រមូរទៅស្តាំ (Scroll Right)"
            >
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>

          <span class="text-xs text-slate-400 font-medium shrink-0 hidden sm:inline-block pl-2 border-l border-slate-100">
            បង្ហាញ <strong>{{ filteredELibraryBooks.length }}</strong> ក្បាល
          </span>
        </div>
      </div>

      <!-- Book Grid -->
      <div v-if="filteredELibraryBooks.length === 0" class="py-16 text-center bg-white rounded-3xl border border-slate-200">
        <div class="w-16 h-16 rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
          📚
        </div>
        <h4 class="font-bold text-slate-700 text-sm">រកមិនឃើញសៀវភៅដែលត្រូវនឹងការស្វែងរកទេ</h4>
        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
          សូមសាកល្បងផ្លាស់ប្តូរការជ្រើសរើសកម្រិតថ្នាក់ មុខវិជ្ជា ឬពាក្យគន្លឹះស្វែងរក។
        </p>
        <button 
          type="button" 
          @click="resetFilters"
          class="mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer"
        >
          កំណត់ឡើងវិញ (Reset Filters)
        </button>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
        <div 
          v-for="b in filteredELibraryBooks" 
          :key="b.id"
          class="bg-white rounded-2xl border border-slate-200/70 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col group relative"
        >
          <!-- Card Header / Cover with Glassmorphic Badges -->
          <div class="h-44 rounded-t-2xl p-5 flex flex-col justify-between relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-blue-900 text-white shadow-inner">
            <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full bg-indigo-500/10 blur-2xl pointer-events-none"></div>
            
            <!-- Top Badges & Bookmark Toggle -->
            <div class="flex items-center justify-between z-10">
              <div class="flex items-center gap-1.5 flex-wrap">
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md text-white border border-white/20">
                  {{ isLitBook(b) || b.grade === 'ទូទៅ' ? 'ទូទៅគ្រប់ថ្នាក់' : `ថ្នាក់ទី ${b.grade || '11'}` }}
                </span>
                <span v-if="isLitBook(b) || b.level === 'អក្សរសិល្ប៍'" class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-400 text-purple-950 shadow-xs">
                  អក្សរសិល្ប៍
                </span>
                <span v-else-if="b.level === 'advanced'" class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-400 text-amber-950 shadow-xs">
                  កម្រិតខ្ពស់
                </span>
                <span v-else class="px-2.5 py-1 rounded-full text-xs font-medium bg-white/15 backdrop-blur-md text-slate-100 border border-white/10">
                  មូលដ្ឋាន
                </span>
              </div>

              <!-- Bookmark Star -->
              <button 
                type="button"
                @click.stop="toggleBookmark(b.id)"
                class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/25 backdrop-blur-md text-white flex items-center justify-center text-xs transition cursor-pointer"
                :title="isBookmarked(b.id) ? 'ដកចេញពីសៀវភៅចំណាំ' : 'រក្សាទុកក្នុងសៀវភៅចំណាំ'"
              >
                <i :class="isBookmarked(b.id) ? 'fa-solid fa-star text-amber-300' : 'fa-regular fa-star text-white/80'"></i>
              </button>
            </div>

            <!-- Book Title -->
            <div class="z-10 mt-auto">
              <h4 class="font-bold text-sm text-white line-clamp-2 leading-relaxed drop-shadow-xs" :title="b.title">
                {{ b.title }}
              </h4>
            </div>
          </div>

          <!-- Card Body -->
          <div class="p-4 flex-1 flex flex-col justify-between space-y-3.5">
            <!-- Subject Pill & ISBN row -->
            <div class="flex items-center justify-between gap-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 truncate max-w-[65%]">
                {{ b.subject_name || 'សៀវភៅពុម្ព' }}
              </span>
              <span class="text-xs text-slate-400 font-mono truncate text-right max-w-[35%]" :title="b.isbn">
                {{ b.isbn || 'MoEYS' }}
              </span>
            </div>

            <!-- Metadata List -->
            <div class="space-y-1.5 text-xs text-slate-600">
              <div class="flex items-center justify-between gap-2">
                <span class="text-slate-400 text-[11px] shrink-0 flex items-center gap-1">
                  <i class="fa-solid fa-user-pen text-slate-400 text-[10px]"></i>
                  <span>អ្នកនិពន្ធ៖</span>
                </span>
                <span class="font-medium truncate text-slate-800 text-right">{{ b.author || 'ក្រសួងអប់រំ យុវជន និងកីឡា' }}</span>
              </div>
              <div class="flex items-center justify-between gap-2">
                <span class="text-slate-400 text-[11px] shrink-0 flex items-center gap-1">
                  <i class="fa-solid fa-calendar-check text-slate-400 text-[10px]"></i>
                  <span>បោះពុម្ព៖</span>
                </span>
                <span class="font-medium text-slate-700 truncate text-right">{{ b.edition || 'លើកទី១' }} ({{ b.publication_year || 2024 }})</span>
              </div>
              <div class="flex items-center justify-between text-[11px] pt-1.5 border-t border-slate-100/90">
                <span class="text-slate-400 flex items-center gap-1">
                  <i class="fa-solid fa-eye text-slate-400 text-[10px]"></i>
                  <span>{{ b.views_count || 0 }} ដង</span>
                </span>
                <span v-if="getLastPage(b.id)" class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-md">
                  📖 ទំព័រទី {{ getLastPage(b.id) }}
                </span>
              </div>
            </div>

            <!-- Actions Row -->
            <div class="pt-2 border-t border-slate-100/90 flex items-center gap-1.5">
              <button 
                type="button" 
                @click="openPdfReader(b)"
                class="flex-1 py-2 px-3 bg-indigo-600 hover:bg-indigo-700 active:scale-98 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer"
              >
                <i class="fa-solid fa-book-open-reader text-xs"></i>
                <span>{{ getLastPage(b.id) ? 'បន្តអាន' : 'អានសៀវភៅ' }}</span>
              </button>

              <a 
                :href="getPdfViewUrl(b.external_pdf_url)" 
                target="_blank"
                class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition shrink-0"
                title="បើកមើលក្នុង Tab ថ្មី (ពេញអេក្រង់)"
              >
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
              </a>

              <a 
                v-if="b.allow_download !== false || canManageLibrary"
                :href="getPdfDownloadUrl(b.external_pdf_url)" 
                target="_blank"
                download
                class="w-8 h-8 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center justify-center text-xs transition shrink-0"
                title="Save in phone (ទាញយកសៀវភៅ PDF ទុកក្នុងទូរស័ព្ទ)"
              >
                <i class="fa-solid fa-download"></i>
              </a>

              <!-- Admin Edit Button -->
              <button 
                v-if="canManageLibrary"
                type="button" 
                @click="openEditBookModal(b)" 
                class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-500 hover:text-slate-800 border border-slate-200 flex items-center justify-center text-xs transition shrink-0 cursor-pointer"
                title="កែប្រែព័ត៌មានសៀវភៅ"
              >
                <i class="fa-solid fa-pen"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 2: Analytics & Admin Dashboard -->
    <div v-else-if="activeTab === 'analytics'" class="space-y-6">
      <!-- 4 Stats Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard title="សៀវភៅ PDF ឌីជីថល" :value="eLibraryBooks.length" />
        <StatCard title="មុខវិជ្ជាជាតិសរុប" :value="subjectsList.length" />
        <StatCard title="សៀវភៅក្នុងស្តុកសរុប" :value="stats.total || booksList.length" />
        <StatCard title="តំណភ្ជាប់មានបញ្ហា (Broken)" :value="stats.broken_links || brokenBooks.length" />
      </div>

      <!-- Textbooks Count by Grade (Grades 7–12 Distribution) -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h4 class="font-bold text-slate-800 text-sm">ការបែងចែកសៀវភៅតាមកម្រិតថ្នាក់ (Grades 7–12 Distribution)</h4>
            <p class="text-xs text-slate-400">ចំនួនសៀវភៅពុម្ព និងឯកសារមេរៀននីមួយៗតាមថ្នាក់</p>
          </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
          <div 
            v-for="g in ['7', '8', '9', '10', '11', '12']" 
            :key="g"
            class="p-4 bg-gradient-to-b from-slate-50 to-blue-50/40 rounded-2xl border border-slate-200 text-center space-y-1 hover:border-blue-300 transition"
          >
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-bold text-sm flex items-center justify-center mx-auto shadow-xs">
              {{ g }}
            </div>
            <div class="text-xs font-bold text-slate-700 mt-2">ថ្នាក់ទី {{ g }}</div>
            <div class="text-lg font-extrabold text-blue-700">{{ countForGrade(g) }} <span class="text-xs font-normal text-slate-400">ក្បាល</span></div>
            <button 
              type="button" 
              @click="filterByGradeFromDashboard(g)"
              class="text-[11px] text-blue-600 hover:underline font-semibold block mx-auto cursor-pointer"
            >
              មើលសៀវភៅ &rarr;
            </button>
          </div>
        </div>
      </div>

      <!-- Two Column: Most Viewed & Broken Links -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Most Viewed Textbooks -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-3">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
              <i class="fa-solid fa-fire text-amber-500"></i>
              <span>សៀវភៅដែលមានអ្នកអានច្រើនជាងគេ (Most Viewed)</span>
            </h4>
          </div>

          <div class="divide-y divide-slate-100">
            <div 
              v-for="(b, idx) in mostViewedBooks" 
              :key="b.id"
              class="py-2.5 flex items-center justify-between gap-3 hover:bg-slate-50 rounded-xl px-2 transition"
            >
              <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-6 h-6 rounded-full font-bold text-xs flex items-center justify-center" :class="idx < 3 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600'">
                  {{ idx + 1 }}
                </span>
                <div class="min-w-0">
                  <h5 class="text-xs font-bold text-slate-800 truncate">{{ b.title }}</h5>
                  <p class="text-[11px] text-slate-400">ថ្នាក់ទី {{ b.grade }} • {{ b.subject_name }}</p>
                </div>
              </div>
              <div class="flex items-center gap-2 flex-shrink-0">
                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">
                  {{ b.views_count || 0 }} អាន
                </span>
                <button 
                  type="button" 
                  @click="openPdfReader(b)"
                  class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold cursor-pointer"
                >
                  អាន
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Broken Links Monitor -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-3">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
              <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
              <span>ត្រួតពិនិត្យតំណភ្ជាប់ខូច (Broken Links Monitor)</span>
            </h4>
            <span class="text-xs px-2 py-0.5 rounded-full font-bold" :class="brokenBooks.length > 0 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'">
              {{ brokenBooks.length }} មានបញ្ហា
            </span>
          </div>

          <div v-if="brokenBooks.length === 0" class="py-8 text-center text-slate-400 text-xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-2xl mb-1.5 block"></i>
            <span>គ្រប់តំណភ្ជាប់សៀវភៅទាំងអស់ដំណើរការល្អឥតខ្ចោះ គ្មានតំណខូចឡើយ!</span>
          </div>

          <div v-else class="divide-y divide-slate-100">
            <div 
              v-for="b in brokenBooks" 
              :key="b.id"
              class="py-2.5 flex items-center justify-between gap-3"
            >
              <div class="min-w-0">
                <h5 class="text-xs font-bold text-slate-800 truncate">{{ b.title }}</h5>
                <p class="text-[10px] text-rose-500 font-mono truncate">{{ b.external_pdf_url || 'គ្មានតំណភ្ជាប់ URL' }}</p>
              </div>
              <div class="flex items-center gap-1.5 flex-shrink-0">
                <button 
                  type="button" 
                  @click="openEditBookModal(b)"
                  class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold cursor-pointer"
                >
                  កែសម្រួល URL
                </button>
                <button 
                  type="button" 
                  @click="toggleBrokenLinkStatus(b)"
                  class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs cursor-pointer"
                  title="សម្គាល់ថាបានជួសជុល"
                >
                  ✓ ជួសជុលរួច
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 3: Books Catalog (Physical / Table View) -->
    <div v-else-if="activeTab === 'books'" class="space-y-4">
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
          <button @click="openBorrowModal" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-1.5 cursor-pointer">
            <span>+ កត់ត្រាការខ្ចីសៀវភៅ</span>
          </button>
        </template>
      </FilterBar>

      <DataTable :loading="isLoading" :empty="filteredBooks.length === 0" :colSpan="9" loadingText="កំពុងទាញយកទិន្នន័យសៀវភៅ...">
        <template #header>
          <tr>
            <th class="py-3 px-4 font-bold text-slate-700">{{ $t('library.isbn') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700">{{ $t('library.bookTitle') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-center">ថ្នាក់</th>
            <th class="py-3 px-4 font-bold text-slate-700">មុខវិជ្ជា</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('library.totalCopies') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('library.availableCopies') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-center">PDF E-Book</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('common.actions') }}</th>
          </tr>
        </template>

        <template #body>
          <tr v-if="filteredBooks.length === 0">
            <td colspan="9" class="py-10 text-center text-slate-400">{{ $t('common.noData') }}</td>
          </tr>
          <tr v-for="b in filteredBooks" :key="b.id" class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ b.isbn || '---' }}</td>
            <td class="py-3 px-4 font-bold text-slate-900">
              <div class="flex items-center gap-2">
                <span>{{ b.title }}</span>
                <span v-if="b.level === 'advanced'" class="px-1.5 py-0.5 rounded text-[10px] bg-amber-100 text-amber-800 font-bold">កម្រិតខ្ពស់</span>
              </div>
              <div class="text-[11px] text-slate-400 font-normal">{{ b.author }} • {{ b.edition }}</div>
            </td>
            <td class="py-3 px-4 text-center">
              <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                ថ្នាក់ទី {{ b.grade || '---' }}
              </span>
            </td>
            <td class="py-3 px-4 text-slate-600 text-xs font-medium">{{ b.subject_name || 'ទូទៅ' }}</td>
            <td class="py-3 px-4 text-center font-semibold text-slate-700">{{ b.quantity }}</td>
            <td class="py-3 px-4 text-center font-bold text-emerald-600">{{ b.available }}</td>
            <td class="py-3 px-4 text-center">
              <button 
                v-if="b.external_pdf_url" 
                type="button" 
                @click="openPdfReader(b)" 
                class="px-2.5 py-1 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white rounded-lg text-xs font-bold shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer"
                title="ចុចអាន PDF អនឡាញ"
              >
                <i class="fa-solid fa-book-open"></i>
                <span>អាន PDF</span>
              </button>
              <button 
                v-else
                type="button"
                @click="openAddPdfQuick(b)"
                class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-medium transition inline-flex items-center gap-1 cursor-pointer"
                title="ភ្ជាប់តំណ PDF ចូលសៀវភៅនេះ"
              >
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>ដាក់ PDF</span>
              </button>
            </td>
            <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
              <button 
                type="button"
                @click="openEditBookModal(b)"
                class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-medium transition cursor-pointer"
                title="កែសម្រួល"
              >
                ✏️
              </button>
              <button 
                v-if="b.available > 0"
                @click="quickBorrowForBook(b)" 
                class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition cursor-pointer"
              >
                ឱ្យខ្ចី &rarr;
              </button>
              <span v-else class="text-xs text-rose-500 font-medium">អស់ស្តុក</span>
            </td>
          </tr>
        </template>
      </DataTable>
    </div>

    <!-- TAB 4: Borrows Records -->
    <div v-else-if="activeTab === 'borrows'" class="space-y-4">
      <DataTable :loading="isLoading" :empty="filteredBorrows.length === 0" :colSpan="6" loadingText="កំពុងទាញយកកំណត់ត្រាខ្ចីសៀវភៅ...">
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
              <button 
                @click="openEditBorrowModal(br)"
                class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-200 transition cursor-pointer"
                title="កែសម្រួលព័ត៌មាន ឬពន្យារពេលសង"
              >
                ✏️ កែប្រែ
              </button>
              <button 
                v-if="br.status !== 'Returned'" 
                @click="handleReturn(br)" 
                class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold hover:bg-emerald-100 transition cursor-pointer"
              >
                ✓ ទទួលសៀវភៅសង
              </button>
              <span v-else class="text-xs text-slate-400">បានសងរួចរាល់</span>
            </td>
          </tr>
        </template>
      </DataTable>
    </div>

    <!-- MODAL 1: Add / Edit Textbook Modal -->
    <div v-if="showAddBookModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
      <div class="bg-white rounded-3xl w-full max-w-xl shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150 my-auto">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
            {{ bookForm.id ? '✏️ កែសម្រួលព័ត៌មានសៀវភៅពុម្ព' : '+ បន្ថែមសៀវភៅពុម្ពថ្មីចូលបណ្ណាល័យ' }}
          </h3>
          <button @click="showAddBookModal = false" class="text-slate-400 hover:text-slate-600 text-xl cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitAddBook" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto custom-scrollbar">
          <!-- Title -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ចំណងជើងសៀវភៅ *</label>
            <input v-model="bookForm.title" type="text" required placeholder="ឧ. គណិតវិទ្យា ថ្នាក់ទី១១ (កម្រិតខ្ពស់)" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <!-- Grade & Subject -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កម្រិតថ្នាក់ (Grade) *</label>
              <select v-model="bookForm.grade" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="7">ថ្នាក់ទី ៧ (Grade 7)</option>
                <option value="8">ថ្នាក់ទី ៨ (Grade 8)</option>
                <option value="9">ថ្នាក់ទី ៩ (Grade 9)</option>
                <option value="10">ថ្នាក់ទី ១០ (Grade 10)</option>
                <option value="11">ថ្នាក់ទី ១១ (Grade 11)</option>
                <option value="12">ថ្នាក់ទី ១២ (Grade 12)</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">មុខវិជ្ជាជាតិ (Subject) *</label>
              <select v-model="bookForm.course_subject_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option :value="null">-- ជ្រើសរើសមុខវិជ្ជា --</option>
                <option v-for="s in subjectsList" :key="s.id" :value="s.id">{{ s.name }} ({{ s.code }})</option>
              </select>
            </div>
          </div>

          <!-- Level & Edition -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កម្រិតសិក្សា (Level)</label>
              <select v-model="bookForm.level" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="basic">កម្រិតមូលដ្ឋាន (Basic)</option>
                <option value="advanced">កម្រិតខ្ពស់ (Advanced)</option>
                <option value="general">ទូទៅ / ឯកសារយោង</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ការបោះពុម្ព (Edition)</label>
              <input v-model="bookForm.edition" type="text" placeholder="ឧ. កំណែទម្រង់ថ្មី ២០២៤" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <!-- Author & ISBN -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">អ្នកនិពន្ធ / ចងក្រង *</label>
              <input v-model="bookForm.author" type="text" required placeholder="ឧ. ក្រសួងអប់រំ យុវជន និងកីឡា" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កូដ ISBN</label>
              <input v-model="bookForm.isbn" type="text" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <!-- Year & Quantity -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ឆ្នាំបោះពុម្ព</label>
              <input v-model.number="bookForm.publication_year" type="number" placeholder="2024" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ចំនួនក្នុងស្តុក (ក្បាល)</label>
              <input v-model.number="bookForm.quantity" type="number" min="1" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <!-- External PDF URL with Auto-Extract -->
          <div class="p-3.5 bg-blue-50/70 rounded-2xl border border-blue-200/80 space-y-2.5">
            <div class="flex items-center justify-between">
              <label class="block text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <i class="fa-solid fa-file-pdf text-rose-500 text-sm"></i>
                <span>តំណភ្ជាប់ PDF សៀវភៅ (Auto-Extract MoEYS / Google Drive)</span>
              </label>
              <span v-if="isExtractingPdf" class="text-[11px] text-blue-700 font-bold flex items-center gap-1">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <span>កំពុងស្រង់យកតំណ...</span>
              </span>
            </div>

            <div class="relative">
              <input 
                v-model="bookForm.external_pdf_url" 
                type="text" 
                @paste="handlePdfUrlPaste"
                @blur="handlePdfUrlBlur"
                placeholder="បិទភ្ជាប់ (Paste) តំណ MoEYS Google Sites ឬ Google Drive..." 
                class="w-full pl-8 pr-24 py-2 bg-white border border-slate-300 rounded-xl text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20"
              >
              <i class="fa-solid fa-link absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
              <button 
                type="button" 
                @click="extractPdfFromUrl(bookForm.external_pdf_url)" 
                :disabled="isExtractingPdf || !bookForm.external_pdf_url"
                class="absolute right-1 top-1 px-3 py-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg text-[11px] font-bold shadow-xs transition disabled:opacity-40 cursor-pointer"
              >
                ស្រង់តំណ
              </button>
            </div>

            <p v-if="pdfExtractMessage" class="text-[11px]" :class="pdfExtractMessage.includes('✓') ? 'text-emerald-700 font-medium' : 'text-slate-600'">
              {{ pdfExtractMessage }}
            </p>

            <div class="flex items-center gap-3 pt-1 text-xs">
              <label class="flex items-center gap-1.5 cursor-pointer">
                <input v-model="bookForm.allow_download" type="checkbox" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                <span class="text-slate-700 font-medium">អនុញ្ញាតឱ្យសិស្សទាញយក (Download PDF)</span>
              </label>
            </div>
          </div>

          <!-- Action buttons -->
          <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
            <button 
              v-if="bookForm.id" 
              type="button" 
              @click="deleteBookById(bookForm.id)" 
              class="px-4 py-2 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl text-xs font-bold transition cursor-pointer"
            >
              🗑️ លុបសៀវភៅនេះ
            </button>
            <div class="flex items-center gap-2 ml-auto">
              <button 
                type="button" 
                @click="showAddBookModal = false" 
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer"
              >
                បោះបង់
              </button>
              <button 
                type="submit" 
                :disabled="isSubmitting"
                class="px-5 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition disabled:opacity-50 cursor-pointer"
              >
                {{ isSubmitting ? 'កំពុងរក្សាទុក...' : (bookForm.id ? 'រក្សាទុកការកែប្រែ' : 'បញ្ចូលសៀវភៅ') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL 2: Manage Subjects Modal (Admin) -->
    <div v-if="showManageSubjectsModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
      <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150 my-auto">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <i class="fa-solid fa-graduation-cap text-indigo-600"></i>
            <span>គ្រប់គ្រងមុខវិជ្ជាសិក្សាជាតិ (MoEYS Subjects Management)</span>
          </h3>
          <button @click="showManageSubjectsModal = false" class="text-slate-400 hover:text-slate-600 text-xl cursor-pointer">&times;</button>
        </div>

        <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto custom-scrollbar">
          <!-- Add Subject Form -->
          <form @submit.prevent="submitSaveSubject" class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
            <h4 class="text-xs font-bold text-slate-700">
              {{ editingSubject.id ? '✏️ កែប្រែមុខវិជ្ជា៖ ' + editingSubject.name : '+ បន្ថែមមុខវិជ្ជាថ្មី' }}
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
              <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-0.5">ឈ្មោះមុខវិជ្ជា *</label>
                <input v-model="subjectForm.name" type="text" required placeholder="ឧ. ផែនដីវិទ្យា" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500/20">
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-0.5">កូដមុខវិជ្ជា *</label>
                <input v-model="subjectForm.code" type="text" required placeholder="ឧ. EES-01" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-mono focus:ring-2 focus:ring-blue-500/20">
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-0.5">កម្រិតថ្នាក់</label>
                <input v-model="subjectForm.grade_level" type="text" placeholder="7-12" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500/20">
              </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-1">
              <button 
                v-if="editingSubject.id" 
                type="button" 
                @click="resetSubjectForm" 
                class="px-3 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-semibold"
              >
                បោះបង់កែប្រែ
              </button>
              <button 
                type="submit" 
                class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs cursor-pointer"
              >
                {{ editingSubject.id ? 'រក្សាទុកការកែប្រែ' : '+ បន្ថែមមុខវិជ្ជា' }}
              </button>
            </div>
          </form>

          <!-- Subjects List Table -->
          <div class="border border-slate-200 rounded-2xl overflow-hidden">
            <table class="w-full text-left text-xs">
              <thead class="bg-slate-100 text-slate-700 font-bold">
                <tr>
                  <th class="py-2.5 px-3">កូដ</th>
                  <th class="py-2.5 px-3">ឈ្មោះមុខវិជ្ជា</th>
                  <th class="py-2.5 px-3 text-center">កម្រិត</th>
                  <th class="py-2.5 px-3 text-center">សៀវភៅ</th>
                  <th class="py-2.5 px-3 text-right">សកម្មភាព</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="sub in subjectsList" :key="sub.id" class="hover:bg-slate-50">
                  <td class="py-2.5 px-3 font-mono text-blue-700 font-bold">{{ sub.code }}</td>
                  <td class="py-2.5 px-3 font-semibold text-slate-800">{{ sub.name }}</td>
                  <td class="py-2.5 px-3 text-center text-slate-500">{{ sub.grade_level || '7-12' }}</td>
                  <td class="py-2.5 px-3 text-center font-bold text-slate-700">{{ countForSubject(sub.id) }}</td>
                  <td class="py-2.5 px-3 text-right space-x-1.5">
                    <button 
                      type="button" 
                      @click="editSubjectItem(sub)" 
                      class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-semibold cursor-pointer"
                    >
                      កែប្រែ
                    </button>
                    <button 
                      type="button" 
                      @click="deleteSubjectItem(sub.id, sub.name)" 
                      class="px-2 py-0.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded text-[11px] font-semibold cursor-pointer"
                    >
                      លុប
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL 3: Borrow Book Modal -->
    <div v-if="showBorrowModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
            + កត់ត្រាការឱ្យខ្ចីសៀវភៅ
          </h3>
          <button @click="showBorrowModal = false" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitBorrowBook" class="p-6 space-y-4">
          <!-- Book Selector with Combobox -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ជ្រើសរើសសៀវភៅ *</label>
            <div class="relative">
              <input 
                v-model="bookSearchQuery" 
                type="text" 
                placeholder="ស្វែងរកតាមចំណងជើងសៀវភៅ, ISBN..." 
                @focus="showBookSuggestions = true"
                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"
              />
              <div v-if="showBookSuggestions" class="absolute left-0 right-0 top-11 bg-white border border-slate-200 rounded-xl shadow-lg z-30 max-h-48 overflow-y-auto custom-scrollbar">
                <div 
                  v-for="b in filteredBooksForBorrow" 
                  :key="b.id"
                  @click="selectBook(b)"
                  class="px-3 py-2 hover:bg-blue-50 text-xs cursor-pointer flex items-center justify-between"
                >
                  <span class="font-semibold text-slate-800">{{ b.title }} (ថ្នាក់ទី {{ b.grade || '---' }})</span>
                  <span class="text-emerald-600 font-bold">នៅសល់ {{ b.available }} ក្បាល</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Student Selector with Combobox -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ជ្រើសរើសសិស្សខ្ចី *</label>
            <div class="relative">
              <input 
                v-model="studentSearchQuery" 
                type="text" 
                placeholder="ស្វែងរកតាមឈ្មោះសិស្ស, អត្តលេខ..." 
                @focus="showStudentSuggestions = true"
                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"
              />
              <div v-if="showStudentSuggestions" class="absolute left-0 right-0 top-11 bg-white border border-slate-200 rounded-xl shadow-lg z-30 max-h-48 overflow-y-auto custom-scrollbar">
                <div 
                  v-for="s in filteredStudentsForBorrow" 
                  :key="s.id"
                  @click="selectStudent(s)"
                  class="px-3 py-2 hover:bg-blue-50 text-xs cursor-pointer flex items-center justify-between"
                >
                  <span class="font-semibold text-slate-800">{{ s.name }}</span>
                  <span class="text-slate-400 font-mono">{{ s.student_id_card }} (ថ្នាក់ {{ s.grade }})</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Dates -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">កាលបរិច្ឆេទខ្ចី *</label>
              <input v-model="borrowForm.borrow_date" type="date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ថ្ងៃកំណត់សង *</label>
              <input v-model="borrowForm.due_date" type="date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <div class="pt-2 flex items-center justify-end gap-2">
            <button type="button" @click="showBorrowModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold">បោះបង់</button>
            <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm">
              {{ isSubmitting ? 'កំពុងកត់ត្រា...' : 'កត់ត្រាការឱ្យខ្ចី' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL 4: Fullscreen/Large PDF Reader Modal with Enhanced Toolbar & Mobile Support -->
    <div 
      v-if="showPdfReaderModal" 
      class="fixed inset-0 z-50 flex items-center justify-center p-0 sm:p-4 bg-slate-950/80 backdrop-blur-md overflow-hidden"
      @click.self="showPdfReaderModal = false"
    >
      <div class="bg-white sm:rounded-3xl rounded-none w-full max-w-5xl h-full sm:h-[92vh] shadow-2xl border border-slate-200 flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Reader Header -->
        <div class="px-3.5 sm:px-5 py-2.5 sm:py-3.5 border-b border-slate-100 flex items-center justify-between bg-white/95 backdrop-blur-sm z-10 shrink-0">
          <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
            <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center text-sm flex-shrink-0">
              <i class="fa-solid fa-book-open-reader"></i>
            </span>
            <div class="min-w-0">
              <div class="flex items-center gap-2">
                <h3 class="font-bold text-slate-800 text-xs sm:text-sm truncate max-w-[170px] sm:max-w-md">{{ currentPdfTitle }}</h3>
                <span class="px-1.5 py-0.2 rounded text-[10px] bg-blue-50 text-blue-700 font-bold hidden sm:inline">ថ្នាក់ទី {{ currentBook?.grade || '---' }}</span>
              </div>
              <p class="text-[10px] text-slate-400 font-mono truncate hidden sm:block">{{ currentPdfUrl }}</p>
            </div>
          </div>

          <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
            <!-- Bookmark Toggle inside Reader -->
            <button 
              type="button" 
              @click="toggleBookmark(currentBook?.id)"
              class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-xs transition cursor-pointer"
              :title="isBookmarked(currentBook?.id) ? 'ដកចំណាំ' : 'ចំណាំសៀវភៅនេះ'"
            >
              <i :class="isBookmarked(currentBook?.id) ? 'fa-solid fa-star text-amber-500' : 'fa-regular fa-star text-slate-400'"></i>
            </button>

            <!-- Save in phone / Download button -->
            <a 
              v-if="currentBook?.allow_download !== false && currentPdfDownloadUrl"
              :href="currentPdfDownloadUrl" 
              target="_blank" 
              download
              class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border border-emerald-200 cursor-pointer"
              title="ទាញយកសៀវភៅ PDF ទុកក្នុងទូរស័ព្ទ / កុំព្យូទ័រ"
            >
              <i class="fa-solid fa-download text-emerald-600"></i>
              <span class="hidden sm:inline">Save in phone</span>
            </a>

            <!-- Open Native / Fullscreen View -->
            <a 
              :href="currentPdfViewUrl" 
              target="_blank" 
              class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border border-blue-200 cursor-pointer"
              title="បើកមើលក្នុងផ្ទាំងថ្មីពេញអេក្រង់ (អូសអានបានរលូន 60fps)"
            >
              <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
              <span class="hidden sm:inline">ពេញអេក្រង់</span>
            </a>

            <!-- Close Button -->
            <button 
              type="button" 
              @click="showPdfReaderModal = false" 
              class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-lg font-bold transition cursor-pointer"
              title="បិទផ្ទាំងអាន"
            >
              &times;
            </button>
          </div>
        </div>

        <!-- Page Bookmark Toolbar (Remember Last Reading Page) -->
        <div class="px-3 sm:px-5 py-2 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-xs shrink-0 flex-wrap gap-2">
          <div class="flex items-center gap-2">
            <span class="text-slate-600 font-semibold flex items-center gap-1">
              <i class="fa-solid fa-bookmark text-blue-600"></i>
              <span>ទំព័រដែលកំពុងអាន៖</span>
            </span>
            <input 
              v-model.number="currentPageInput" 
              type="number" 
              min="1" 
              class="w-16 px-2 py-0.5 bg-white border border-slate-300 rounded-lg text-xs text-center font-bold"
            />
            <button 
              type="button" 
              @click="saveCurrentPageBookmark" 
              class="px-2.5 py-0.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[11px] font-bold cursor-pointer"
            >
              ចំណាំទំព័រនេះ
            </button>
            <span v-if="pageSavedMessage" class="text-emerald-600 font-bold text-[11px] animate-fade-in">
              {{ pageSavedMessage }}
            </span>
          </div>

          <div class="flex items-center gap-2">
            <button 
              type="button" 
              @click="toggleReportBrokenLink" 
              class="text-[11px] text-slate-400 hover:text-rose-600 flex items-center gap-1 cursor-pointer"
              title="រាយការណ៍ថាឯកសារ PDF នេះបើកមិនចេញ"
            >
              <i class="fa-solid fa-flag"></i>
              <span>រាយការណ៍តំណខូច</span>
            </button>
          </div>
        </div>

        <!-- Mobile Scroll Tip Bar -->
        <div class="sm:hidden px-3 py-1.5 bg-gradient-to-r from-blue-900 to-indigo-900 text-white flex items-center justify-between text-[11px] shrink-0 border-b border-indigo-800">
          <div class="flex items-center gap-1.5 text-blue-100">
            <i class="fa-solid fa-circle-info text-amber-400"></i>
            <span>អូសអានមិនទៅលើទូរស័ព្ទ?</span>
          </div>
          <a 
            :href="currentPdfViewUrl" 
            target="_blank" 
            class="text-amber-300 font-bold underline flex items-center gap-1"
          >
            អានពេញអេក្រង់ &rarr;
          </a>
        </div>

        <!-- Reader Iframe Body -->
        <div class="flex-1 bg-slate-950 w-full min-h-0 relative overflow-auto" style="-webkit-overflow-scrolling: touch;">
          <iframe 
            :src="readerIframeUrl || currentPdfUrl" 
            class="w-full h-full border-0 block" 
            allow="autoplay; fullscreen"
            style="-webkit-overflow-scrolling: touch; touch-action: auto;"
          ></iframe>
        </div>

        <!-- Mobile Bottom Action Bar (Fixed footer for phone users) -->
        <div class="sm:hidden px-3 py-2 bg-slate-900 text-white flex items-center justify-between gap-2 shrink-0 border-t border-slate-800" style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom));">
          <a 
            v-if="currentBook?.allow_download !== false && currentPdfDownloadUrl"
            :href="currentPdfDownloadUrl" 
            target="_blank" 
            download
            class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold text-center flex items-center justify-center gap-1.5 transition shadow-sm active:scale-95 cursor-pointer"
          >
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Save in Phone</span>
          </a>
          <a 
            :href="currentPdfViewUrl" 
            target="_blank" 
            class="flex-1 py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold text-center flex items-center justify-center gap-1.5 transition shadow-sm active:scale-95 cursor-pointer"
          >
            <i class="fa-solid fa-mobile-screen"></i>
            <span>អានពេញអេក្រង់ &rarr;</span>
          </a>
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

// Current User & Role Permissions
const currentUser = ref(window.__INITIAL_USER__ || {});
const userRole = computed(() => {
  const r = (currentUser.value?.role || '').toLowerCase();
  if (r.includes('admin')) return 'admin';
  if (r.includes('teacher') || r.includes('គ្រូ')) return 'teacher';
  return 'student';
});

const isAdmin = computed(() => userRole.value === 'admin');
const isTeacher = computed(() => userRole.value === 'teacher');
const isStudent = computed(() => userRole.value === 'student');
const canManageLibrary = computed(() => isAdmin.value);
const canBorrow = computed(() => isAdmin.value || isTeacher.value);

const activeTab = ref('elibrary');

const availableTabs = computed(() => {
  const tabs = [
    { id: 'elibrary', label: `📖 បណ្ណាល័យឌីជីថល ថ្នាក់ទី៧-១២ (${eLibraryBooks.value.length})` }
  ];
  if (isAdmin.value) {
    tabs.push({ id: 'analytics', label: `📊 ស្ថិតិ & ផ្ទាំងគ្រប់គ្រង (${stats.value.digital_books || eLibraryBooks.value.length})` });
  }
  if (canBorrow.value) {
    tabs.push({ id: 'books', label: `កាតាឡុកសៀវភៅក្នុងស្តុក (${booksList.value.length})` });
    tabs.push({ id: 'borrows', label: `កំណត់ត្រាខ្ចី-សង (${borrowsList.value.length})` });
  }
  return tabs;
});
const searchQuery = ref('');
const selectedGrade = ref('');
const selectedSubjectId = ref('');
const selectedLevel = ref('');
const subFilter = ref('all'); // all, bookmarks, recent
const isLoading = ref(true);

const stats = ref({
  total: 0,
  available: 0,
  borrows: 0,
  overdue: 0,
  digital_books: 0,
  total_subjects: 0,
  broken_links: 0,
  books_by_grade: {},
});

const booksList = ref([]);
const subjectsList = ref([]);
const borrowsList = ref([]);
const categoriesList = ref([]);
const studentsList = ref([]);

// LocalStorage Reading Memory
const bookmarkedIds = ref([]);
const recentlyViewedIds = ref([]);
const readingPagesMap = ref({});

// Load user bookmarks & reading progress from localStorage
const loadLocalStorageData = () => {
  try {
    const savedBookmarks = localStorage.getItem('pdt_library_bookmarks');
    if (savedBookmarks) bookmarkedIds.value = JSON.parse(savedBookmarks);

    const savedRecent = localStorage.getItem('pdt_library_recent');
    if (savedRecent) recentlyViewedIds.value = JSON.parse(savedRecent);

    const savedPages = localStorage.getItem('pdt_library_pages');
    if (savedPages) readingPagesMap.value = JSON.parse(savedPages);
  } catch (e) {
    console.error('Failed to load localStorage data:', e);
  }
};

const toggleBookmark = (bookId) => {
  if (!bookId) return;
  const idx = bookmarkedIds.value.indexOf(bookId);
  if (idx > -1) {
    bookmarkedIds.value.splice(idx, 1);
  } else {
    bookmarkedIds.value.push(bookId);
  }
  try {
    localStorage.setItem('pdt_library_bookmarks', JSON.stringify(bookmarkedIds.value));
  } catch (e) {}
};

const isBookmarked = (bookId) => bookmarkedIds.value.includes(bookId);

const recordRecentlyViewed = (bookId) => {
  if (!bookId) return;
  recentlyViewedIds.value = [bookId, ...recentlyViewedIds.value.filter(id => id !== bookId)].slice(0, 20);
  try {
    localStorage.setItem('pdt_library_recent', JSON.stringify(recentlyViewedIds.value));
  } catch (e) {}
};

const getLastPage = (bookId) => readingPagesMap.value[bookId] || null;

// Modal States
const showAddBookModal = ref(false);
const showManageSubjectsModal = ref(false);
const showBorrowModal = ref(false);
const showEditBorrowModal = ref(false);
const showPdfReaderModal = ref(false);
const currentBook = ref(null);
const currentPdfUrl = ref('');
const currentPdfTitle = ref('');
const currentPageInput = ref(1);
const pageSavedMessage = ref('');
const isExtractingPdf = ref(false);
const extractedPdfLinks = ref([]);
const pdfExtractMessage = ref('');
const isSubmitting = ref(false);

const bookForm = ref({
  id: null,
  title: '',
  author: '',
  isbn: '',
  grade: '11',
  course_subject_id: null,
  level: 'basic',
  edition: 'បោះពុម្ពលើកទី១',
  publication_year: 2024,
  book_category_id: null,
  quantity: 10,
  publisher: 'ក្រសួងអប់រំ យុវជន និងកីឡា',
  external_pdf_url: '',
  allow_download: true,
  is_broken_link: false,
});

const subjectForm = ref({
  id: null,
  name: '',
  code: '',
  grade_level: '7-12',
  description: '',
});
const editingSubject = ref({ id: null });

const borrowForm = ref({
  book_id: '',
  student_id: '',
  borrow_date: new Date().toISOString().slice(0, 10),
  due_date: new Date(Date.now() + 14 * 86400000).toISOString().slice(0, 10),
});

// Searchable Comboboxes
const bookSearchQuery = ref('');
const showBookSuggestions = ref(false);
const studentSearchQuery = ref('');
const showStudentSuggestions = ref(false);

// Filter and Computed Logic
const eLibraryBooks = computed(() => {
  return booksList.value.filter(b => b.external_pdf_url && b.external_pdf_url.trim() !== '');
});

const brokenBooks = computed(() => {
  return booksList.value.filter(b => b.is_broken_link || !b.external_pdf_url);
});

const mostViewedBooks = computed(() => {
  return [...booksList.value]
    .filter(b => b.external_pdf_url)
    .sort((a, b) => (b.views_count || 0) - (a.views_count || 0))
    .slice(0, 8);
});

const normalizeGrade = (g) => {
  if (!g) return '';
  const s = String(g).trim();
  const map = { '៧': '7', '៨': '8', '៩': '9', '១០': '10', '១១': '11', '១២': '12' };
  return map[s] || s;
};

const countForGrade = (grade) => {
  const target = normalizeGrade(grade);
  if (selectedSubjectId.value && !isLiteratureSubject(selectedSubjectId.value)) {
    return eLibraryBooks.value.filter(b => 
      normalizeGrade(b.grade) === target && 
      (String(b.course_subject_id) === String(selectedSubjectId.value) || b.subject_name === getSubjectName(selectedSubjectId.value))
    ).length;
  }
  return eLibraryBooks.value.filter(b => normalizeGrade(b.grade) === target).length;
};

const getSubjectName = (subjectId) => {
  const s = subjectsList.value.find(sub => String(sub.id) === String(subjectId));
  return s ? s.name : '';
};

const countForSubject = (subjectId) => {
  if (isLiteratureSubject(subjectId)) {
    return eLibraryBooks.value.filter(b => isLitBook(b)).length;
  }
  const subName = getSubjectName(subjectId);
  if (selectedGrade.value) {
    const target = normalizeGrade(selectedGrade.value);
    return eLibraryBooks.value.filter(b => 
      normalizeGrade(b.grade) === target && 
      (String(b.course_subject_id) === String(subjectId) || (subName && b.subject_name === subName))
    ).length;
  }
  return eLibraryBooks.value.filter(b => 
    String(b.course_subject_id) === String(subjectId) || (subName && b.subject_name === subName)
  ).length;
};

// Sort subjects so "អក្សរសិល្ប៍" is ALWAYS the very first subject right after "ទាំងអស់"
const sortedSubjectsList = computed(() => {
  return [...subjectsList.value].sort((a, b) => {
    const isLitA = a.name === 'អក្សរសិល្ប៍' || a.code === 'LIT-01';
    const isLitB = b.name === 'អក្សរសិល្ប៍' || b.code === 'LIT-01';
    if (isLitA) return -1;
    if (isLitB) return 1;
    const isKhA = a.name === 'អក្សរសាស្ត្រខ្មែរ' || a.code === 'KHM-01';
    const isKhB = b.name === 'អក្សរសាស្ត្រខ្មែរ' || b.code === 'KHM-01';
    if (isKhA) return -1;
    if (isKhB) return 1;
    const isMathA = a.name === 'គណិតវិទ្យា' || a.code === 'MTH-01';
    const isMathB = b.name === 'គណិតវិទ្យា' || b.code === 'MTH-01';
    if (isMathA) return -1;
    if (isMathB) return 1;
    return 0;
  });
});

const isLiteratureSubject = (subjId) => {
  if (!subjId) return false;
  const s = subjectsList.value.find(sub => String(sub.id) === String(subjId));
  return s ? (s.name === 'អក្សរសិល្ប៍' || s.code === 'LIT-01') : false;
};

const isLitBook = (b) => {
  if (!b) return false;
  return b.grade === 'ទូទៅ' || b.level === 'អក្សរសិល្ប៍' || (b.subject_name && b.subject_name === 'អក្សរសិល្ប៍');
};

const selectSubject = (subId) => {
  selectedSubjectId.value = subId;
  if (!subId) return;

  // Grade-independent: when user clicks [អក្សរសិល្ប៍ (8)], display all 8 books by clearing grade filter
  if (isLiteratureSubject(subId)) {
    selectedGrade.value = '';
    return;
  }

  // If currently selected grade has no books for this subject, auto-clear grade so subject books display
  if (selectedGrade.value) {
    const target = normalizeGrade(selectedGrade.value);
    const subName = getSubjectName(subId);
    const hasBooks = eLibraryBooks.value.some(b => 
      normalizeGrade(b.grade) === target && 
      (String(b.course_subject_id) === String(subId) || (subName && b.subject_name === subName))
    );
    if (!hasBooks) {
      selectedGrade.value = '';
    }
  }
};

const selectGrade = (grade) => {
  selectedGrade.value = grade;
  if (!grade) return;

  // If user picks a specific grade while viewing literature, reset subject so grade textbooks show
  if (isLiteratureSubject(selectedSubjectId.value)) {
    selectedSubjectId.value = '';
    return;
  }

  // If currently selected subject has no books for this grade, auto-reset subject to 'ទាំងអស់'
  if (selectedSubjectId.value) {
    const target = normalizeGrade(grade);
    const subName = getSubjectName(selectedSubjectId.value);
    const hasBooks = eLibraryBooks.value.some(b => 
      normalizeGrade(b.grade) === target && 
      (String(b.course_subject_id) === String(selectedSubjectId.value) || (subName && b.subject_name === subName))
    );
    if (!hasBooks) {
      selectedSubjectId.value = '';
    }
  }
};

const filterByGradeFromDashboard = (grade) => {
  selectedGrade.value = normalizeGrade(grade);
  activeTab.value = 'elibrary';
};

const resetFilters = () => {
  selectedGrade.value = '';
  selectedSubjectId.value = '';
  selectedLevel.value = '';
  searchQuery.value = '';
  subFilter.value = 'all';
};

const filteredELibraryBooks = computed(() => {
  let list = eLibraryBooks.value;

  // Sub filter
  if (subFilter.value === 'bookmarks') {
    list = list.filter(b => bookmarkedIds.value.includes(b.id));
  } else if (subFilter.value === 'recent') {
    list = list.filter(b => recentlyViewedIds.value.includes(b.id));
  }

  // Subject filter
  if (selectedSubjectId.value) {
    if (isLiteratureSubject(selectedSubjectId.value)) {
      list = list.filter(b => isLitBook(b));
    } else {
      const subName = getSubjectName(selectedSubjectId.value);
      list = list.filter(b => 
        String(b.course_subject_id) === String(selectedSubjectId.value) || 
        (subName && b.subject_name === subName)
      );
    }
  }

  // Grade filter (handles both Khmer digits and standard digits; grade-independent for literature)
  if (selectedGrade.value) {
    const target = normalizeGrade(selectedGrade.value);
    const isViewingLit = selectedSubjectId.value && isLiteratureSubject(selectedSubjectId.value);
    if (!isViewingLit) {
      list = list.filter(b => normalizeGrade(b.grade) === target);
    }
  }

  // Level filter
  if (selectedLevel.value) {
    list = list.filter(b => b.level === selectedLevel.value);
  }

  // Search query
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase().trim();
    list = list.filter(b => 
      (b.title && b.title.toLowerCase().includes(q)) || 
      (b.author && b.author.toLowerCase().includes(q)) || 
      (b.subject_name && b.subject_name.toLowerCase().includes(q)) || 
      (b.isbn && b.isbn.toLowerCase().includes(q))
    );
  }

  return list;
});

const filteredBooks = computed(() => {
  if (!searchQuery.value) return booksList.value;
  const q = searchQuery.value.toLowerCase().trim();
  return booksList.value.filter(b => 
    b.title.toLowerCase().includes(q) || 
    b.author.toLowerCase().includes(q) || 
    (b.subject_name && b.subject_name.toLowerCase().includes(q)) ||
    (b.isbn && b.isbn.toLowerCase().includes(q))
  );
});

const filteredBorrows = computed(() => {
  if (!searchQuery.value) return borrowsList.value;
  const q = searchQuery.value.toLowerCase().trim();
  return borrowsList.value.filter(br => 
    br.book.toLowerCase().includes(q) || 
    br.student.toLowerCase().includes(q)
  );
});

const filteredBooksForBorrow = computed(() => {
  const q = bookSearchQuery.value.trim().toLowerCase();
  return booksList.value.filter(b => {
    if (b.available < 1) return false;
    if (!q) return true;
    return b.title.toLowerCase().includes(q) || (b.isbn && b.isbn.toLowerCase().includes(q));
  }).slice(0, 10);
});

const filteredStudentsForBorrow = computed(() => {
  const q = studentSearchQuery.value.trim().toLowerCase();
  return studentsList.value.filter(s => {
    if (!q) return true;
    return s.name.toLowerCase().includes(q) || (s.student_id_card && s.student_id_card.toLowerCase().includes(q));
  }).slice(0, 15);
});

const selectBook = (book) => {
  borrowForm.value.book_id = book.id;
  bookSearchQuery.value = book.title;
  showBookSuggestions.value = false;
};

const selectStudent = (student) => {
  borrowForm.value.student_id = student.id;
  studentSearchQuery.value = student.name;
  showStudentSuggestions.value = false;
};

// URL Helpers for Google Drive Preview & Download
const readerIframeUrl = computed(() => {
  if (!currentPdfUrl.value) return '';
  const url = currentPdfUrl.value;
  if (url.includes('drive.google.com')) {
    if (url.includes('/view')) return url.replace('/view', '/preview');
    if (url.includes('/edit')) return url.replace(/\/edit.*$/, '/preview');
    if (!url.includes('/preview')) return `${url.replace(/\/+$/, '')}/preview`;
    return url;
  }
  // For external direct PDFs (e.g., SalaDigital), embed via Google Drive viewer
  return `https://drive.google.com/viewerng/viewer?embedded=true&url=${encodeURIComponent(url)}`;
});

const currentPdfFileId = computed(() => {
  if (!currentPdfUrl.value) return '';
  const m = currentPdfUrl.value.match(/\/file\/d\/([a-zA-Z0-9_-]{20,})/i) || currentPdfUrl.value.match(/id=([a-zA-Z0-9_-]{20,})/i);
  return m ? m[1] : '';
});

const currentPdfViewUrl = computed(() => {
  if (!currentPdfUrl.value) return '#';
  if (currentPdfFileId.value) {
    return `https://drive.google.com/file/d/${currentPdfFileId.value}/view`;
  }
  return currentPdfUrl.value;
});

const currentPdfDownloadUrl = computed(() => {
  if (!currentPdfUrl.value) return '';
  if (currentPdfFileId.value) {
    return `https://drive.google.com/uc?export=download&id=${currentPdfFileId.value}`;
  }
  return currentPdfUrl.value;
});

const getPdfDownloadUrl = (url) => {
  if (!url) return '#';
  const m = url.match(/\/file\/d\/([a-zA-Z0-9_-]{20,})/i) || url.match(/id=([a-zA-Z0-9_-]{20,})/i);
  return m ? `https://drive.google.com/uc?export=download&id=${m[1]}` : url;
};

const getPdfViewUrl = (url) => {
  if (!url) return '#';
  const m = url.match(/\/file\/d\/([a-zA-Z0-9_-]{20,})/i) || url.match(/id=([a-zA-Z0-9_-]{20,})/i);
  return m ? `https://drive.google.com/file/d/${m[1]}/view` : url;
};

const subjectScrollRef = ref(null);

const scrollSubjects = (direction) => {
  if (subjectScrollRef.value) {
    const amount = direction === 'left' ? -220 : 220;
    subjectScrollRef.value.scrollBy({ left: amount, behavior: 'smooth' });
  }
};

const onSubjectWheel = (e) => {
  if (subjectScrollRef.value && Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
    e.preventDefault();
    subjectScrollRef.value.scrollLeft += e.deltaY;
  }
};

// Fetch Library Data
const fetchLibraryData = async () => {
  if (booksList.value.length === 0) {
    isLoading.value = true;
  }
  try {
    const res = await axios.get('/api/library', { params: { _t: Date.now() } });
    if (res.data) {
      if (res.data.user) {
        currentUser.value = { ...currentUser.value, ...res.data.user };
      }
      stats.value = res.data.stats || stats.value;
      booksList.value = res.data.books || [];
      subjectsList.value = res.data.subjects || [];
      borrowsList.value = res.data.borrows || [];
      categoriesList.value = res.data.categories || [];
      studentsList.value = [...(res.data.students || [])].sort((a, b) => (a.name || '').localeCompare(b.name || '', 'km'));

      // If current activeTab is not permitted for this role, default to elibrary
      if (!availableTabs.value.some(t => t.id === activeTab.value)) {
        activeTab.value = 'elibrary';
      }
    }
  } catch (err) {
    console.error('Failed to fetch library:', err);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  loadLocalStorageData();
  fetchLibraryData();
});

onActivated(() => {
  fetchLibraryData();
});

// PDF Reader Actions
const openPdfReader = async (book) => {
  if (!book?.external_pdf_url) return;
  currentBook.value = book;
  currentPdfUrl.value = book.external_pdf_url;
  currentPdfTitle.value = book.title;
  currentPageInput.value = getLastPage(book.id) || 1;
  pageSavedMessage.value = '';
  showPdfReaderModal.value = true;

  recordRecentlyViewed(book.id);

  // Increment backend views count
  try {
    await axios.post(`/api/library/books/${book.id}/view`);
    book.views_count = (book.views_count || 0) + 1;
  } catch (e) {}
};

const saveCurrentPageBookmark = () => {
  if (!currentBook.value?.id || !currentPageInput.value) return;
  readingPagesMap.value[currentBook.value.id] = currentPageInput.value;
  try {
    localStorage.setItem('pdt_library_pages', JSON.stringify(readingPagesMap.value));
    pageSavedMessage.value = `✓ បានចំណាំទំព័រទី ${currentPageInput.value} រួចរាល់!`;
    setTimeout(() => { pageSavedMessage.value = ''; }, 3000);
  } catch (e) {}
};

const toggleReportBrokenLink = async () => {
  if (!currentBook.value?.id) return;
  if (!confirm('តើលោកអ្នកពិតជាចង់រាយការណ៍ថាតំណភ្ជាប់ PDF នេះខូច ឬមើលមិនចេញមែនទេ?')) return;
  try {
    const res = await axios.post(`/api/library/books/${currentBook.value.id}/broken-link`, { is_broken: true });
    alert(res.data.message || 'បានរាយការណ៍ដោយជោគជ័យ!');
    currentBook.value.is_broken_link = true;
    await fetchLibraryData();
  } catch (e) {
    alert('មានបញ្ហាក្នុងការរាយការណ៍');
  }
};

const toggleBrokenLinkStatus = async (book) => {
  try {
    const res = await axios.post(`/api/library/books/${book.id}/broken-link`, { is_broken: !book.is_broken_link });
    book.is_broken_link = res.data.is_broken_link;
    await fetchLibraryData();
  } catch (e) {}
};

// Book Form Actions
const openAddBookModal = () => {
  bookForm.value = {
    id: null,
    title: '',
    author: 'ក្រសួងអប់រំ យុវជន និងកីឡា',
    isbn: `978-99950-${Math.floor(10 + Math.random() * 90)}-${Math.floor(10 + Math.random() * 90)}`,
    grade: selectedGrade.value || '11',
    course_subject_id: selectedSubjectId.value || subjectsList.value[0]?.id || null,
    level: 'basic',
    edition: 'បោះពុម្ពលើកទី១',
    publication_year: 2024,
    book_category_id: categoriesList.value[0]?.id || null,
    quantity: 10,
    publisher: 'ក្រសួងអប់រំ យុវជន និងកីឡា',
    external_pdf_url: '',
    allow_download: true,
    is_broken_link: false,
  };
  extractedPdfLinks.value = [];
  pdfExtractMessage.value = '';
  showAddBookModal.value = true;
};

const openEditBookModal = (b) => {
  bookForm.value = {
    id: b.id,
    title: b.title,
    author: b.author,
    isbn: b.isbn || '',
    grade: String(b.grade || '11'),
    course_subject_id: b.course_subject_id,
    level: b.level || 'basic',
    edition: b.edition || '',
    publication_year: b.publication_year || 2024,
    book_category_id: b.category_id || categoriesList.value[0]?.id || null,
    quantity: b.quantity || 10,
    publisher: b.publisher || 'ក្រសួងអប់រំ យុវជន និងកីឡា',
    external_pdf_url: b.external_pdf_url || '',
    allow_download: b.allow_download !== false,
    is_broken_link: Boolean(b.is_broken_link),
  };
  extractedPdfLinks.value = [];
  pdfExtractMessage.value = '';
  showAddBookModal.value = true;
};

const openAddPdfQuick = (b) => {
  openEditBookModal(b);
};

const handlePdfUrlPaste = (event) => {
  const clipboardData = event.clipboardData || window.clipboardData;
  const pastedText = clipboardData ? clipboardData.getData('text') : '';
  if (pastedText && pastedText.trim().startsWith('http')) {
    setTimeout(() => {
      extractPdfFromUrl(bookForm.value.external_pdf_url || pastedText.trim());
    }, 50);
  }
};

const handlePdfUrlBlur = () => {
  if (bookForm.value.external_pdf_url && bookForm.value.external_pdf_url.trim().startsWith('http')) {
    extractPdfFromUrl(bookForm.value.external_pdf_url.trim());
  }
};

const extractPdfFromUrl = async (url) => {
  if (!url || !url.trim().startsWith('http')) return;
  isExtractingPdf.value = true;
  pdfExtractMessage.value = 'កំពុងទាញយក និងស្រង់តំណភ្ជាប់ PDF ស្វ័យប្រវត្តិ...';

  try {
    const res = await axios.post('/api/library/extract-pdf', { url: url.trim() });
    if (res.data.success) {
      bookForm.value.external_pdf_url = res.data.preview_url;
      extractedPdfLinks.value = res.data.all_links || [];
      pdfExtractMessage.value = '✓ បានស្រង់យកតំណភ្ជាប់ Google Drive Preview ដោយជោគជ័យ!';
    } else {
      pdfExtractMessage.value = res.data.message || 'រកមិនឃើញឯកសារ PDF ទេ';
    }
  } catch (err) {
    const driveMatch = url.match(/drive\.google\.com\/(?:file\/d\/|open\?id=)([a-zA-Z0-9_-]{25,})/i);
    if (driveMatch) {
      bookForm.value.external_pdf_url = `https://drive.google.com/file/d/${driveMatch[1]}/preview`;
      pdfExtractMessage.value = '✓ បានបំលែងទៅជា Google Drive Preview ដោយជោគជ័យ!';
    } else {
      pdfExtractMessage.value = 'មិនអាចស្រង់តំណភ្ជាប់ PDF បានទេ សូមពិនិត្យមើល URL';
    }
  } finally {
    isExtractingPdf.value = false;
  }
};

const submitAddBook = async () => {
  isSubmitting.value = true;
  try {
    const res = await axios.post('/api/library/books', bookForm.value);
    showAddBookModal.value = false;
    alert(res.data.message || 'បានរក្សាទុកសៀវភៅដោយជោគជ័យ!');
    await fetchLibraryData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការរក្សាទុកសៀវភៅ!');
  } finally {
    isSubmitting.value = false;
  }
};

const deleteBookById = async (bookId) => {
  if (!confirm('តើលោកអ្នកពិតជាចង់លុបសៀវភៅនេះចេញពីបណ្ណាល័យមែនទេ?')) return;
  try {
    const res = await axios.delete(`/api/library/books/${bookId}`);
    showAddBookModal.value = false;
    alert(res.data.message || 'បានលុបសៀវភៅដោយជោគជ័យ!');
    await fetchLibraryData();
  } catch (err) {
    alert('មិនអាចលុបសៀវភៅបានទេ');
  }
};

// Subject Management Actions
const editSubjectItem = (sub) => {
  editingSubject.value = { ...sub };
  subjectForm.value = {
    id: sub.id,
    name: sub.name,
    code: sub.code,
    grade_level: sub.grade_level || '7-12',
    description: sub.description || '',
  };
};

const resetSubjectForm = () => {
  editingSubject.value = { id: null };
  subjectForm.value = { id: null, name: '', code: '', grade_level: '7-12', description: '' };
};

const submitSaveSubject = async () => {
  try {
    if (editingSubject.value.id) {
      const res = await axios.put(`/api/library/subjects/${editingSubject.value.id}`, subjectForm.value);
      alert(res.data.message || 'បានកែប្រែមុខវិជ្ជាដោយជោគជ័យ!');
    } else {
      const res = await axios.post('/api/library/subjects', subjectForm.value);
      alert(res.data.message || 'បានបន្ថែមមុខវិជ្ជាដោយជោគជ័យ!');
    }
    resetSubjectForm();
    await fetchLibraryData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការរក្សាទុកមុខវិជ្ជា!');
  }
};

const deleteSubjectItem = async (id, name) => {
  if (!confirm(`តើលោកអ្នកពិតជាចង់លុបមុខវិជ្ជា "${name}" មែនទេ?`)) return;
  try {
    const res = await axios.delete(`/api/library/subjects/${id}`);
    alert(res.data.message || 'បានលុបមុខវិជ្ជាដោយជោគជ័យ!');
    await fetchLibraryData();
  } catch (err) {
    alert('មិនអាចលុបមុខវិជ្ជានេះបានទេ');
  }
};

// Borrow Actions
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

const handleReturn = async (borrow) => {
  if (!confirm(`តើអ្នកពិតជាបានទទួលសៀវភៅ "${borrow.book}" ត្រឡប់មកវិញហើយមែនទេ?`)) return;
  try {
    const res = await axios.post(`/api/library/return/${borrow.id}`);
    alert(res.data.message || 'បានកត់ត្រាការសងសៀវភៅដោយជោគជ័យ!');
    await fetchLibraryData();
  } catch (err) {
    alert('មិនអាចកត់ត្រាការសងសៀវភៅបានទេ!');
  }
};
</script>
