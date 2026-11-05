<template>
  <div class="space-y-6 font-khmer">
    <!-- Header -->
    <PageHeader 
      :title="$t('settings.title')" 
      :breadcrumb="$t('nav.websiteSetting')" 
      :actionLabel="$t('common.save')"
      @action="handleSave"
    />

    <!-- Sub Tabs (Dynamic 4 Tabs) -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'general', label: 'ព័ត៌មានគ្រឹះស្ថានសិក្សា' },
        { id: 'academic', label: 'ឆ្នាំសិក្សា និងឆមាសសកម្ម' },
        { id: 'currency', label: 'រូបិយប័ណ្ណគោល' },
        { id: 'security', label: 'តួនាទី និងសិទ្ធិ' }
      ]"
    />

    <!-- Entity Banner -->
    <EntityBanner 
      :name="$t('settings.title')"
      :subtitle="'ប្រព័ន្ធគ្រប់គ្រងសាលារៀន LMSTICK | ការកំណត់រចនាសម្ព័ន្ធ និងទិន្នន័យទូទៅ'"
      metaId="CFG-SYS-2026"
      @print="handlePrint"
      @download="handleBackup"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="$t('common.active') + ' Modules'" :value="18" />
      <StatCard :title="$t('settings.systemVersion')" :value="'v2.4.0 (LMSTICK)'" />
      <StatCard :title="$t('settings.databaseStatus')" :value="'Connected'" />
      <StatCard title="ឆ្នាំសិក្សាបច្ចុប្បន្ន" :value="form.current_academic_year" />
    </div>

    <!-- Tab 1: School Profile & Info -->
    <div v-show="activeTab === 'general'" class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs space-y-6 transition-all">
      <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
        <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
          <i class="fa-solid fa-school"></i>
        </span>
        <div>
          <h2 class="text-sm font-bold text-slate-900">ព័ត៌មានគ្រឹះស្ថានសិក្សា (School Information)</h2>
          <p class="text-[11px] text-slate-500">កំណត់អត្តសញ្ញាណ ព័ត៌មានទំនាក់ទំនង ឡូហ្គោ និងត្រារបស់សាលា</p>
        </div>
      </div>

      <!-- Demo / Dev Mode Switch Card (Radio Switch) -->
      <div class="p-5 bg-gradient-to-r from-indigo-50/90 via-blue-50/70 to-slate-50 border border-blue-200/90 rounded-2xl space-y-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
              <i class="fa-solid fa-flask"></i>
            </span>
            <div>
              <h3 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                <span>របៀបដំណើរការទិន្នន័យ (System Mode / Dev Mode)</span>
                <span :class="[
                  'px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase',
                  form.demo_mode === '1' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700'
                ]">
                  {{ form.demo_mode === '1' ? '⚡ Demo Mode Active' : '🟢 Live Production' }}
                </span>
              </h3>
              <p class="text-[11px] text-slate-500">
                ជ្រើសរើសរបៀបសម្រាប់បង្ហាញទិន្នន័យសាកល្បង (Demo) ពេញលេញគ្រប់ទំព័រ ឬប្រើទិន្នន័យផ្ទាល់ពី Database
              </p>
            </div>
          </div>
        </div>

        <div v-if="modeMessage" class="px-3.5 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-semibold flex items-center gap-2 animate-pulse">
          <i class="fa-solid fa-circle-check text-emerald-600"></i>
          <span>{{ modeMessage }}</span>
        </div>

        <!-- Radio Switch Options -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
          <!-- Option 1: Demo / Dev Mode -->
          <div 
            @click="handleModeChange('1')"
            :class="[
              'flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all select-none',
              form.demo_mode === '1' 
                ? 'border-blue-600 bg-white shadow-xs ring-2 ring-blue-500/20' 
                : 'border-slate-200 bg-white/70 hover:bg-white hover:border-slate-300'
            ]"
          >
            <input 
              type="radio" 
              name="system_mode" 
              value="1" 
              :checked="form.demo_mode === '1'" 
              class="mt-1 text-blue-600 focus:ring-blue-500 pointer-events-none" 
            />
            <div class="flex-1">
              <span class="block text-xs font-bold text-slate-900">
                🚀 របៀបសាកល្បង (Demo / Dev Mode)
              </span>
              <span class="block text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                បង្ហាញទិន្នន័យគំរូពេញលេញ (សិស្ស ២០+ នាក់, គ្រូ ១០+ នាក់, វត្តមាន, ពិន្ទុ, ហិរញ្ញវត្ថុ) សម្រាប់ Demo បានគ្រប់កន្លែង ទោះបីលើ Production ក៏ដោយ។
              </span>
            </div>
          </div>

          <!-- Option 2: Live / Production Mode -->
          <div 
            @click="handleModeChange('0')"
            :class="[
              'flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all select-none',
              form.demo_mode === '0' 
                ? 'border-blue-600 bg-white shadow-xs ring-2 ring-blue-500/20' 
                : 'border-slate-200 bg-white/70 hover:bg-white hover:border-slate-300'
            ]"
          >
            <input 
              type="radio" 
              name="system_mode" 
              value="0" 
              :checked="form.demo_mode === '0'" 
              class="mt-1 text-blue-600 focus:ring-blue-500 pointer-events-none" 
            />
            <div class="flex-1">
              <span class="block text-xs font-bold text-slate-900">
                🔒 របៀបផ្ទាល់ (Live / Production Mode)
              </span>
              <span class="block text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                បង្ហាញតែទិន្នន័យពិតប្រាកដដែលបានបញ្ចូល និងរក្សាទុកក្នុង Database ជាក់ស្តែងប៉ុណ្ណោះ។
              </span>
            </div>
          </div>
        </div>

        <!-- Seeder Quick Action -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pt-3 border-t border-blue-200/60">
          <div class="flex items-center gap-2 text-[11px] text-slate-600">
            <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
              <i class="fa-solid fa-database"></i>
            </span>
            <span>
              <strong>បញ្ចូលទិន្នន័យគំរូកម្ពុជាពេញលេញ៖</strong> សិស្ស ៨៧ នាក់ (11D ៥២ នាក់, 10A ៣៥ នាក់), គ្រូ ១៥ នាក់, ពិន្ទុប្រឡង ៣ សម័យ, កាលវិភាគ
            </span>
          </div>
          <button 
            @click="handleRunSeed" 
            :disabled="isSeeding" 
            type="button" 
            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition flex items-center gap-2 cursor-pointer flex-shrink-0"
          >
            <i :class="isSeeding ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-wand-magic-sparkles'"></i>
            <span>{{ isSeeding ? 'កំពុងបញ្ចូលទិន្នន័យ...' : 'Seed ទិន្នន័យគំរូទៅក្នុង DB' }}</span>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- School Name Khmer -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ឈ្មោះសាលារៀន (ភាសាខ្មែរ) <span class="text-red-500">*</span>
          </label>
          <input 
            type="text" 
            v-model="form.school_name" 
            placeholder="ឧ. សាលារៀន ជំនាន់ថ្មី PDT" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- School Name English -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ឈ្មោះសាលារៀន (English) <span class="text-red-500">*</span>
          </label>
          <input 
            type="text" 
            v-model="form.school_name_en" 
            placeholder="e.g. PDT New Generation School" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Principal Name -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ឈ្មោះនាយកសាលា (Principal Name)
          </label>
          <input 
            type="text" 
            v-model="form.principal_name" 
            placeholder="ឧ. លោក ឈុន វណ្ណា" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Website -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            គេហទំព័រ (Website)
          </label>
          <input 
            type="text" 
            v-model="form.school_website" 
            placeholder="https://school.pdt.edu.kh" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Phone -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            លេខទូរស័ព្ទ (Phone)
          </label>
          <input 
            type="text" 
            v-model="form.school_phone" 
            placeholder="023 880 880 / 012 345 678" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Email -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            អ៊ីមែល (Email)
          </label>
          <input 
            type="email" 
            v-model="form.school_email" 
            placeholder="contact@pdt-school.edu.kh" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Address -->
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            អាសយដ្ឋានសាលា (Address)
          </label>
          <input 
            type="text" 
            v-model="form.school_address" 
            placeholder="មហាវិថីសហព័ន្ធរុស្ស៊ី សង្កាត់ទឹកល្អក់១ ខណ្ឌទួលគោក រាជធានីភ្នំពេញ" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- School Logo Upload -->
        <div class="p-4 bg-slate-50 border border-dashed border-slate-300 rounded-2xl">
          <label class="block text-xs font-semibold text-slate-700 mb-2">
            ឡូហ្គោសាលា (School Logo)
          </label>
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-xl bg-white border border-slate-200 overflow-hidden flex items-center justify-center flex-shrink-0 shadow-2xs">
              <img :src="logoPreview || form.school_logo_url || '/images/logo.png'" class="w-full h-full object-contain p-1" />
            </div>
            <div class="flex-1">
              <input 
                type="file" 
                accept="image/*" 
                @change="handleLogoChange" 
                class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
              />
              <p class="text-[10px] text-slate-400 mt-1">PNG, JPG, SVG (ទំហំសមរម្យ 512x512px)</p>
            </div>
          </div>
        </div>

        <!-- School Stamp / Signature Upload -->
        <div class="p-4 bg-slate-50 border border-dashed border-slate-300 rounded-2xl">
          <label class="block text-xs font-semibold text-slate-700 mb-2">
            ត្រាសាលា និងហត្ថលេខា (Stamp & Signature)
          </label>
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-xl bg-white border border-slate-200 overflow-hidden flex items-center justify-center flex-shrink-0 shadow-2xs">
              <img v-if="stampPreview || form.school_stamp_url" :src="stampPreview || form.school_stamp_url" class="w-full h-full object-contain p-1" />
              <i v-else class="fa-solid fa-stamp text-2xl text-slate-300"></i>
            </div>
            <div class="flex-1">
              <input 
                type="file" 
                accept="image/*" 
                @change="handleStampChange" 
                class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
              />
              <p class="text-[10px] text-slate-400 mt-1">ប្រើសម្រាប់បោះពុម្ពលើកាតសិស្ស និងវិញ្ញាបនបត្រ</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab 2: Academic Year & Active Semester -->
    <div v-show="activeTab === 'academic'" class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs space-y-6 transition-all">
      <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
          <i class="fa-solid fa-calendar-days"></i>
        </span>
        <div>
          <h2 class="text-sm font-bold text-slate-900">ឆ្នាំសិក្សា និងឆមាសសកម្ម (Active Academic Year & Semester)</h2>
          <p class="text-[11px] text-slate-500">កំណត់ឆ្នាំសិក្សាបច្ចុប្បន្ន ឆមាស ម៉ោងសិក្សា និងលក្ខខណ្ឌវត្តមាន</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Current Academic Year Dropdown -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ឆ្នាំសិក្សាបច្ចុប្បន្ន (Active Academic Year) <span class="text-red-500">*</span>
          </label>
          <select 
            v-model="form.current_academic_year" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-bold text-blue-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
            <option v-for="ay in academicYears" :key="ay.id" :value="ay.name">
              ឆ្នាំសិក្សា {{ ay.name }} {{ ay.is_current ? '(សកម្ម)' : '' }}
            </option>
            <option value="2025-2026">ឆ្នាំសិក្សា 2025-2026</option>
            <option value="2026-2027">ឆ្នាំសិក្សា 2026-2027</option>
          </select>
        </div>

        <!-- Current Semester -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ឆមាសសកម្ម (Active Semester) <span class="text-red-500">*</span>
          </label>
          <div class="grid grid-cols-2 gap-3">
            <label class="flex items-center justify-center gap-2 p-2 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-semibold cursor-pointer has-checked:bg-blue-50 has-checked:border-blue-500 has-checked:text-blue-600 transition">
              <input type="radio" v-model="form.current_semester" value="1" class="text-blue-600 focus:ring-0">
              <span>ឆមាសទី ១ (Semester 1)</span>
            </label>
            <label class="flex items-center justify-center gap-2 p-2 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-semibold cursor-pointer has-checked:bg-blue-50 has-checked:border-blue-500 has-checked:text-blue-600 transition">
              <input type="radio" v-model="form.current_semester" value="2" class="text-blue-600 focus:ring-0">
              <span>ឆមាសទី ២ (Semester 2)</span>
            </label>
          </div>
        </div>

        <!-- Academic Start Date -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ថ្ងៃចាប់ផ្តើមឆ្នាំសិក្សា
          </label>
          <input 
            type="date" 
            v-model="form.academic_start_date" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Academic End Date -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ថ្ងៃបញ្ចប់ឆ្នាំសិក្សា
          </label>
          <input 
            type="date" 
            v-model="form.academic_end_date" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Passing Grade % -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ពិន្ទុមធ្យមភាគជាប់ (Passing Grade %)
          </label>
          <div class="relative">
            <input 
              type="number" 
              v-model="form.passing_grade" 
              placeholder="50" 
              min="0" 
              max="100" 
              class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
            >
            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-slate-400 font-bold">%</span>
          </div>
          <p class="text-[10px] text-slate-400 mt-1">ពិន្ទុមធ្យមភាគអប្បបរមាដើម្បីជាប់ការសិក្សា</p>
        </div>

        <!-- Late Threshold in minutes -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            រយៈពេលអនុគ្រោះមុនគិតថាមកយឺត (Late Threshold)
          </label>
          <div class="relative">
            <input 
              type="number" 
              v-model="form.late_threshold_minutes" 
              placeholder="15" 
              min="0" 
              class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
            >
            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-slate-400">នាទី</span>
          </div>
          <p class="text-[10px] text-slate-400 mt-1">លើសពីចំនួននាទីនេះ នឹងត្រូវគណនាជាវត្តមាន "មកយឺត"</p>
        </div>

        <!-- Morning Shift Time -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ម៉ោងចូលរៀនវេនព្រឹក (Morning Shift)
          </label>
          <input 
            type="text" 
            v-model="form.morning_shift_time" 
            placeholder="07:00 - 11:00" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Afternoon Shift Time -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ម៉ោងចូលរៀនវេនរសៀល (Afternoon Shift)
          </label>
          <input 
            type="text" 
            v-model="form.afternoon_shift_time" 
            placeholder="13:00 - 17:00" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>
      </div>
    </div>

    <!-- Tab 3: Currency & Finance -->
    <div v-show="activeTab === 'currency'" class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs space-y-6 transition-all">
      <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
        <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
          <i class="fa-solid fa-coins"></i>
        </span>
        <div>
          <h2 class="text-sm font-bold text-slate-900">រូបិយប័ណ្ណគោល និងហិរញ្ញវត្ថុ (Currency & Finance Setup)</h2>
          <p class="text-[11px] text-slate-500">កំណត់រូបិយប័ណ្ណ អត្រាប្តូរប្រាក់ ទម្រង់វិក្កយបត្រ និងគណនី KHQR</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Base Currency -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            រូបិយប័ណ្ណគោល (Base Currency) <span class="text-red-500">*</span>
          </label>
          <select 
            v-model="form.currency" 
            @change="updateCurrencySymbol"
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
            <option value="USD">USD ($) - ដុល្លារអាមេរិក</option>
            <option value="KHR">KHR (៛) - រៀលខ្មែរ</option>
          </select>
        </div>

        <!-- Currency Symbol -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            និមិត្តសញ្ញារូបិយប័ណ្ណ (Currency Symbol)
          </label>
          <input 
            type="text" 
            v-model="form.currency_symbol" 
            placeholder="$" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Exchange Rate -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            អត្រាប្តូរប្រាក់ (Exchange Rate: 1 USD = ? KHR)
          </label>
          <div class="relative">
            <input 
              type="number" 
              v-model="form.exchange_rate" 
              placeholder="4100" 
              class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
            >
            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-bold text-slate-400">KHR</span>
          </div>
          <p class="text-[10px] text-slate-400 mt-1">ឧទាហរណ៍៖ 1 USD = 4,100 KHR</p>
        </div>

        <!-- Invoice Prefix -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ទម្រង់លេខវិក្កយបត្រ (Invoice Prefix)
          </label>
          <input 
            type="text" 
            v-model="form.invoice_prefix" 
            placeholder="INV-2026-" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-mono font-bold text-blue-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
          <p class="text-[10px] text-slate-400 mt-1">ឧ. INV-2026-0001</p>
        </div>

        <!-- Bank Name -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ឈ្មោះធនាគារសាលា (Bank Name)
          </label>
          <input 
            type="text" 
            v-model="form.bank_name" 
            placeholder="ABA Bank / Canadia Bank" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Account Name -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ឈ្មោះគណនីធនាគារ (Account Name)
          </label>
          <input 
            type="text" 
            v-model="form.bank_account_name" 
            placeholder="PDT SCHOOL CO., LTD" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Account Number -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            លេខគណនីធនាគារ (Account Number)
          </label>
          <input 
            type="text" 
            v-model="form.bank_account_number" 
            placeholder="001 234 567" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-mono font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- KHQR Upload -->
        <div class="p-4 bg-slate-50 border border-dashed border-slate-300 rounded-2xl">
          <label class="block text-xs font-semibold text-slate-700 mb-2">
            រូបភាព Bakong KHQR សាលា (School KHQR Code)
          </label>
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-xl bg-white border border-slate-200 overflow-hidden flex items-center justify-center flex-shrink-0 shadow-2xs">
              <img v-if="khqrPreview || form.khqr_image_url" :src="khqrPreview || form.khqr_image_url" class="w-full h-full object-contain p-1" />
              <i v-else class="fa-solid fa-qrcode text-2xl text-slate-300"></i>
            </div>
            <div class="flex-1">
              <input 
                type="file" 
                accept="image/*" 
                @change="handleKhqrChange" 
                class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
              />
              <p class="text-[10px] text-slate-400 mt-1">រូបភាព KHQR សម្រាប់បង្ហាញលើវិក្កយបត្របង់ថ្លៃសិក្សា</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab 4: Roles, Permissions & System Control -->
    <div v-show="activeTab === 'security'" class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs space-y-6 transition-all">
      <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
        <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">
          <i class="fa-solid fa-shield-halved"></i>
        </span>
        <div>
          <h2 class="text-sm font-bold text-slate-900">តួនាទី សិទ្ធិ និងការគ្រប់គ្រងប្រព័ន្ធ (Roles, Permissions & System Control)</h2>
          <p class="text-[11px] text-slate-500">កំណត់សិទ្ធិតាមតួនាទី បើក/បិទមុខងារស្នូល និងទាញយកទិន្នន័យបម្រុង</p>
        </div>
      </div>

      <!-- Feature Toggles -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Public Registration Toggle -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between">
          <div>
            <h4 class="text-xs font-bold text-slate-800">ការចុះឈ្មោះសិស្សថ្មីតាម Link / QR Code</h4>
            <p class="text-[11px] text-slate-500">បើកឱ្យសិស្ស និងអាណាព្យាបាលអាចចុះឈ្មោះតាមតំណភ្ជាប់សាធារណៈ</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" v-model="form.enable_public_registration" true-value="1" false-value="0" class="sr-only peer">
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
          </label>
        </div>

        <!-- Teacher Score Edit Toggle -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between">
          <div>
            <h4 class="text-xs font-bold text-slate-800">អនុញ្ញាតឱ្យគ្រូបង្រៀនកែប្រែពិន្ទុប្រឡង</h4>
            <p class="text-[11px] text-slate-500">បើក/បិទសិទ្ធិកែប្រែពិន្ទុបន្ទាប់ពីបានរក្សាទុករួច</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" v-model="form.enable_teacher_score_edit" true-value="1" false-value="0" class="sr-only peer">
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
          </label>
        </div>
      </div>

      <!-- Role Permissions Matrix -->
      <div>
        <h4 class="text-xs font-bold text-slate-800 mb-3 flex items-center gap-2">
          <i class="fa-solid fa-user-lock text-slate-500"></i>
          <span>តារាងបែងចែកសិទ្ធិប្រើប្រាស់តាមតួនាទី (Role Permissions Matrix)</span>
        </h4>
        <div class="overflow-x-auto border border-slate-200 rounded-2xl">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
              <tr>
                <th class="py-3 px-4">មុខងារប្រព័ន្ធ</th>
                <th class="py-3 px-3 text-center">អ្នកគ្រប់គ្រង (Admin)</th>
                <th class="py-3 px-3 text-center">គ្រូបង្រៀន (Teacher)</th>
                <th class="py-3 px-3 text-center">គណនេយ្យករ (Accountant)</th>
                <th class="py-3 px-3 text-center">សិស្ស/អាណាព្យាបាល</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-600">
              <tr class="hover:bg-slate-50/50">
                <td class="py-2.5 px-4 font-semibold text-slate-800">គ្រប់គ្រងសិស្សានុសិស្ស</td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" checked disabled class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" checked class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" class="rounded text-blue-600"></td>
              </tr>
              <tr class="hover:bg-slate-50/50">
                <td class="py-2.5 px-4 font-semibold text-slate-800">កត់ត្រា និងគ្រប់គ្រងវត្តមាន</td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" checked disabled class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" checked class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" class="rounded text-blue-600"></td>
              </tr>
              <tr class="hover:bg-slate-50/50">
                <td class="py-2.5 px-4 font-semibold text-slate-800">បញ្ចូលពិន្ទុ និងការប្រឡង</td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" checked disabled class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" checked class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" class="rounded text-blue-600"></td>
              </tr>
              <tr class="hover:bg-slate-50/50">
                <td class="py-2.5 px-4 font-semibold text-slate-800">ហិរញ្ញវត្ថុ វិក្កយបត្រ និងការបង់ប្រាក់</td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" checked disabled class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" checked class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" class="rounded text-blue-600"></td>
              </tr>
              <tr class="hover:bg-slate-50/50">
                <td class="py-2.5 px-4 font-semibold text-slate-800">ទាញយករបាយការណ៍ និងស្ថិតិ</td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" checked disabled class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" checked class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" checked class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" class="rounded text-blue-600"></td>
              </tr>
              <tr class="hover:bg-slate-50/50">
                <td class="py-2.5 px-4 font-semibold text-slate-800">ការកំណត់ប្រព័ន្ធ និងគេហទំព័រ</td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" checked disabled class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" class="rounded text-blue-600"></td>
                <td class="py-2.5 px-3 text-center"><input type="checkbox" class="rounded text-blue-600"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Backup Database Section -->
      <div class="p-5 bg-gradient-to-r from-blue-50/80 to-indigo-50/80 border border-blue-200/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-database text-blue-600"></i>
            <span>ទាញយកទិន្នន័យបម្រុងមូលដ្ឋានទិន្នន័យ (Backup Database)</span>
          </h4>
          <p class="text-[11px] text-slate-500 mt-0.5">
            ទាញយកទិន្នន័យប្រព័ន្ធទាំងអស់ជាឯកសារ SQL Dump ពេញលេញ រួមមានសិស្ស គ្រូ ពិន្ទុ វត្តមាន និងហិរញ្ញវត្ថុ
          </p>
        </div>
        <button 
          @click="handleBackup" 
          type="button"
          class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 transition flex items-center justify-center gap-2 flex-shrink-0"
        >
          <i class="fa-solid fa-download"></i>
          <span>ទាញយកទិន្នន័យបម្រុង (Backup Database)</span>
        </button>
      </div>
    </div>

    <!-- Save Settings Action Footer Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-2xs flex items-center justify-between">
      <span class="text-xs text-slate-500">
        <i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>
        ទិន្នន័យត្រូវបានធ្វើសមកាលកម្មជាមួយ Server
      </span>
      <button 
        @click="handleSave" 
        :disabled="isSaving"
        type="button"
        class="px-8 py-2.5 bg-[#1a44e8] hover:bg-[#1538c2] text-white text-xs font-bold rounded-full shadow-md shadow-blue-600/20 hover:shadow-lg transition flex items-center gap-2 disabled:opacity-50"
      >
        <svg v-if="isSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <i v-else class="fa-solid fa-floppy-disk"></i>
        <span>{{ isSaving ? 'កំពុងរក្សាទុក...' : $t('settings.saveSettings') }}</span>
      </button>
    </div>

  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import EntityBanner from '../components/EntityBanner.vue';
import StatCard from '../components/StatCard.vue';

const { t } = useI18n();

// Tab state (defaults to 'general')
const activeTab = ref('general');
const isSaving = ref(false);

const academicYears = ref([]);

const form = ref({
  // Demo Mode Switch
  demo_mode: localStorage.getItem('pdt_demo_mode') || '1',

  // Tab 1: School Info
  school_name: 'សាលារៀន ជំនាន់ថ្មី PDT',
  school_name_en: 'PDT New Generation School',
  principal_name: 'លោក ឈុន វណ្ណា',
  school_website: 'https://pdt-school.edu.kh',
  school_phone: '023 880 880 / 012 345 678',
  school_email: 'contact@pdt-school.edu.kh',
  school_address: 'មហាវិថីសហព័ន្ធរុស្ស៊ី សង្កាត់ទឹកល្អក់១ ខណ្ឌទួលគោក រាជធានីភ្នំពេញ',
  school_logo_url: '/images/logo.png',
  school_stamp_url: '',

  // Tab 2: Academic Setup
  current_academic_year: '2025-2026',
  current_semester: '1',
  academic_start_date: '2025-10-01',
  academic_end_date: '2026-07-31',
  passing_grade: '50',
  morning_shift_time: '07:00 - 11:00',
  afternoon_shift_time: '13:00 - 17:00',
  late_threshold_minutes: '15',

  // Tab 3: Currency & Finance
  currency: 'USD',
  currency_symbol: '$',
  exchange_rate: '4100',
  invoice_prefix: 'INV-2026-',
  bank_name: 'ABA Bank',
  bank_account_name: 'PDT SCHOOL CO., LTD',
  bank_account_number: '001 234 567',
  khqr_image_url: '',

  // Tab 4: Roles, Permissions & Security
  enable_public_registration: '1',
  enable_teacher_score_edit: '1',
});

// File upload states
const logoFile = ref(null);
const logoPreview = ref('');
const stampFile = ref(null);
const stampPreview = ref('');
const khqrFile = ref(null);
const khqrPreview = ref('');

// Auto save mode state
const isSwitchingMode = ref(false);
const modeMessage = ref('');
const isSeeding = ref(false);

const handleRunSeed = async () => {
  if (!confirm('តើអ្នកពិតជាចង់បញ្ចូលទិន្នន័យគំរូកម្ពុជាពេញលេញ (សិស្ស ៨៧ នាក់, គ្រូ ១៥ នាក់, ពិន្ទុ ២០៨៨ កំណត់ត្រា) ទៅក្នុង Database មែនទេ?')) {
    return;
  }
  isSeeding.value = true;
  try {
    const res = await axios.post('/api/seed');
    alert(res.data.message || 'បាន Seed ទិន្នន័យជោគជ័យ!');
    await fetchSettings();
  } catch (err) {
    console.error('Seed error:', err);
    alert('ការ Seed បានបរាជ័យ៖ ' + (err.response?.data?.message || err.message));
  } finally {
    isSeeding.value = false;
  }
};

const handleModeChange = async (newMode) => {
  form.value.demo_mode = newMode;
  localStorage.setItem('pdt_demo_mode', newMode);
  isSwitchingMode.value = true;
  modeMessage.value = newMode === '1' 
    ? '🚀 បានប្តូរទៅកាន់ Demo Mode (បានរក្សាទុករួចរាល់)' 
    : '🔒 បានប្តូរទៅកាន់ Live Production (បានរក្សាទុករួចរាល់)';

  try {
    const data = new FormData();
    data.append('demo_mode', newMode);
    await axios.post('/api/settings', data);
  } catch (err) {
    console.warn('Auto save demo_mode warning:', err);
  } finally {
    setTimeout(() => {
      modeMessage.value = '';
      isSwitchingMode.value = false;
    }, 2500);
  }
};

const fetchSettings = async () => {
  try {
    const res = await axios.get('/api/settings');
    if (res.data) {
      if (res.data.settings) {
        const s = res.data.settings;
        Object.assign(form.value, s);
        // Sync demo_mode from DB or localStorage
        if (s.demo_mode !== undefined && s.demo_mode !== null) {
          form.value.demo_mode = String(s.demo_mode);
          localStorage.setItem('pdt_demo_mode', form.value.demo_mode);
        } else if (localStorage.getItem('pdt_demo_mode')) {
          form.value.demo_mode = localStorage.getItem('pdt_demo_mode');
        }
      }
      if (res.data.academicYears) {
        academicYears.value = res.data.academicYears;
      }
    }
  } catch (err) {
    console.error('Failed to load settings:', err);
  }
};

onMounted(() => {
  fetchSettings();
});

const handleLogoChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    logoFile.value = file;
    const reader = new FileReader();
    reader.onload = (re) => {
      const base64 = re.target.result;
      logoPreview.value = base64;
      form.value.school_logo_url = base64;
      try {
        localStorage.setItem('pdt_school_logo', base64);
      } catch (err) {
        console.warn('LocalStorage quota:', err);
      }

      // Instantly reflect logo in sidebar top header
      window.dispatchEvent(new CustomEvent('pdt-logo-preview', {
        detail: { logoUrl: base64 }
      }));
    };
    reader.readAsDataURL(file);
  }
};

watch(() => form.value.school_name, (val) => {
  if (val) {
    localStorage.setItem('pdt_school_name', val);
    window.dispatchEvent(new CustomEvent('pdt-settings-saved', {
      detail: {
        school_name: val,
        school_name_en: form.value.school_name_en,
        school_logo_url: logoPreview.value || form.value.school_logo_url
      }
    }));
  }
});

watch(() => form.value.school_name_en, (val) => {
  if (val !== undefined) {
    localStorage.setItem('pdt_school_name_en', val);
    window.dispatchEvent(new CustomEvent('pdt-settings-saved', {
      detail: {
        school_name: form.value.school_name,
        school_name_en: val,
        school_logo_url: logoPreview.value || form.value.school_logo_url
      }
    }));
  }
});

const handleStampChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    stampFile.value = file;
    stampPreview.value = URL.createObjectURL(file);
  }
};

const handleKhqrChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    khqrFile.value = file;
    khqrPreview.value = URL.createObjectURL(file);
  }
};

const updateCurrencySymbol = () => {
  if (form.value.currency === 'USD') {
    form.value.currency_symbol = '$';
  } else if (form.value.currency === 'KHR') {
    form.value.currency_symbol = '៛';
  }
};

const handleSave = async () => {
  isSaving.value = true;
  try {
    const data = new FormData();
    Object.keys(form.value).forEach(k => {
      if (form.value[k] !== null && form.value[k] !== undefined) {
        data.append(k, form.value[k]);
      }
    });

    if (logoFile.value) {
      data.append('school_logo', logoFile.value);
    }
    if (stampFile.value) {
      data.append('school_stamp', stampFile.value);
    }
    if (khqrFile.value) {
      data.append('khqr_image', khqrFile.value);
    }

    const res = await axios.post('/api/settings', data, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    if (res.data && res.data.success) {
      localStorage.setItem('pdt_demo_mode', form.value.demo_mode);
      if (logoPreview.value) {
        try {
          localStorage.setItem('pdt_school_logo', logoPreview.value);
        } catch (e) {}
      }
      if (form.value.school_name) {
        localStorage.setItem('pdt_school_name', form.value.school_name);
      }
      if (form.value.school_name_en) {
        localStorage.setItem('pdt_school_name_en', form.value.school_name_en);
      }
      alert(res.data.message || 'បានរក្សាទុកការកំណត់ប្រព័ន្ធដោយជោគជ័យ!');
      await fetchSettings();

      // Broadcast saved school logo and name to sidebar
      window.dispatchEvent(new CustomEvent('pdt-settings-saved', {
        detail: {
          school_logo_url: res.data.school_logo_url || form.value.school_logo_url,
          school_name: res.data.school_name || form.value.school_name,
          school_name_en: res.data.school_name_en || form.value.school_name_en
        }
      }));
    }
  } catch (err) {
    console.error('Failed to save settings:', err);
    alert('ការរក្សាទុកបានបរាជ័យ សូមព្យាយាមម្តងទៀត។');
  } finally {
    isSaving.value = false;
  }
};

const handleBackup = () => {
  window.location.href = '/api/settings/backup';
};

const handlePrint = () => window.print();
</script>
