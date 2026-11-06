<template>
  <div class="space-y-6 font-khmer">
    <!-- Header -->
    <PageHeader 
      :title="$t('settings.title')" 
      :breadcrumb="$t('nav.websiteSetting')" 
      :actionLabel="$t('common.save')"
      @action="handleSave"
    />

    <!-- Sub Tabs (Dynamic 5 Tabs) -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'general', label: 'ព័ត៌មានគ្រឹះស្ថានសិក្សា' },
        { id: 'academic', label: 'ឆ្នាំសិក្សា និងឆមាសសកម្ម' },
        { id: 'currency', label: 'រូបិយប័ណ្ណគោល' },
        { id: 'backup', label: 'ទិន្នន័យបម្រុង' },
        { id: 'mobile_app', label: '📱 កម្មវិធីទូរស័ព្ទ (App)' }
      ]"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="$t('common.active') + ' Modules'" :value="18" />
      <StatCard :title="$t('settings.systemVersion')" :value="'v2.4.0'" />
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
          <h2 class="text-sm font-bold text-slate-900">ព័ត៌មានគ្រឹះស្ថានសិក្សា</h2>
          <p class="text-[11px] text-slate-500">កំណត់អត្តសញ្ញាណ ព័ត៌មានទំនាក់ទំនង ឡូហ្គោ និងត្រារបស់សាលា</p>
        </div>
      </div>

      <!-- Quick PWA Install Callout Banner -->
      <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50/60 border border-blue-200/80 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg flex-shrink-0 shadow-md shadow-blue-500/20">
            <i class="fa-solid fa-mobile-screen-button"></i>
          </div>
          <div>
            <h4 class="text-xs font-bold text-slate-800">ដំឡើងកម្មវិធីសាលារៀនលើទូរស័ព្ទ (School Mobile App)</h4>
            <p class="text-[11px] text-slate-500">លោកអ្នកអាចដំឡើង App លើអេក្រង់ទូរស័ព្ទ ដើម្បីបើកលឿន ពេញអេក្រង់ និងមិនបាត់ Session</p>
          </div>
        </div>
        <button 
          type="button" 
          @click="openPwaModal"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 flex-shrink-0 cursor-pointer shadow-sm active:scale-95"
        >
          <i class="fa-solid fa-download"></i>
          <span>ដំឡើង App ឥឡូវនេះ</span>
        </button>
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
            placeholder="ឧ. សាលារៀន ជំនាន់ថ្មី" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- School Name English -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ឈ្មោះសាលារៀន <span class="text-red-500">*</span>
          </label>
          <input 
            type="text" 
            v-model="form.school_name_en" 
            placeholder="e.g. New Generation School" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Principal Name -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ឈ្មោះនាយកសាលា
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
            គេហទំព័រ
          </label>
          <input 
            type="text" 
            v-model="form.school_website" 
            placeholder="https://school.edu.kh" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Phone -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            លេខទូរស័ព្ទ
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
            អ៊ីមែល
          </label>
          <input 
            type="email" 
            v-model="form.school_email" 
            placeholder="contact@school.edu.kh" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Address -->
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            អាសយដ្ឋានសាលា
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
            ឡូហ្គោសាលា
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
            ត្រាសាលា និងហត្ថលេខា
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
          <h2 class="text-sm font-bold text-slate-900">ឆ្នាំសិក្សា និងឆមាសសកម្ម</h2>
          <p class="text-[11px] text-slate-500">កំណត់ឆ្នាំសិក្សាបច្ចុប្បន្ន ឆមាស ម៉ោងសិក្សា និងលក្ខខណ្ឌវត្តមាន</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Current Academic Year Dropdown -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            ឆ្នាំសិក្សាបច្ចុប្បន្ន <span class="text-red-500">*</span>
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
            ឆមាសសកម្ម <span class="text-red-500">*</span>
          </label>
          <div class="grid grid-cols-2 gap-3">
            <label class="flex items-center justify-center gap-2 p-2 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-semibold cursor-pointer has-checked:bg-blue-50 has-checked:border-blue-500 has-checked:text-blue-600 transition">
              <input type="radio" v-model="form.current_semester" value="1" class="text-blue-600 focus:ring-0">
              <span>ឆមាសទី ១</span>
            </label>
            <label class="flex items-center justify-center gap-2 p-2 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-semibold cursor-pointer has-checked:bg-blue-50 has-checked:border-blue-500 has-checked:text-blue-600 transition">
              <input type="radio" v-model="form.current_semester" value="2" class="text-blue-600 focus:ring-0">
              <span>ឆមាសទី ២</span>
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
            រយៈពេលអនុគ្រោះមុនគិតថាមកយឺត
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
            ម៉ោងចូលរៀនវេនព្រឹក
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
            ម៉ោងចូលរៀនវេនរសៀល
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
          <h2 class="text-sm font-bold text-slate-900">រូបិយប័ណ្ណគោល និងហិរញ្ញវត្ថុ</h2>
          <p class="text-[11px] text-slate-500">កំណត់រូបិយប័ណ្ណ អត្រាប្តូរប្រាក់ ទម្រង់វិក្កយបត្រ និងគណនី KHQR</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Base Currency -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            រូបិយប័ណ្ណគោល <span class="text-red-500">*</span>
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
            និមិត្តសញ្ញារូបិយប័ណ្ណ
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
            ទម្រង់លេខវិក្កយបត្រ
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
            ឈ្មោះធនាគារសាលា
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
            ឈ្មោះគណនីធនាគារ
          </label>
          <input 
            type="text" 
            v-model="form.bank_account_name" 
            placeholder="SCHOOL ACCOUNT" 
            class="w-full px-3.5 py-2.5 bg-[#f8faff] border border-slate-200 rounded-xl text-xs font-medium uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
        </div>

        <!-- Account Number -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            លេខគណនីធនាគារ
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
            រូបភាព Bakong KHQR សាលា
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

    <!-- Tab 4: Database Backup & Security Navigation -->
    <div v-show="activeTab === 'backup'" class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs space-y-6 transition-all">
      <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
        <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
          <i class="fa-solid fa-database"></i>
        </span>
        <div>
          <h2 class="text-sm font-bold text-slate-900">ទិន្នន័យបម្រុង និងសុវត្ថិភាពមូលដ្ឋានទិន្នន័យ</h2>
          <p class="text-[11px] text-slate-500">ទាញយកទិន្នន័យប្រព័ន្ធបម្រុងទុកការពារហានិភ័យ និងតំណភ្ជាប់ទៅកាន់ការកំណត់សិទ្ធិ</p>
        </div>
      </div>

      <!-- Backup Database Section -->
      <div class="p-5 sm:p-6 bg-gradient-to-r from-blue-50/90 via-indigo-50/70 to-slate-50 border border-blue-200/80 rounded-2xl space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
          <div class="flex items-center gap-3.5">
            <span class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-base shadow-xs flex-shrink-0">
              <i class="fa-solid fa-cloud-arrow-down"></i>
            </span>
            <div>
              <h4 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                <span>ទាញយកទិន្នន័យបម្រុងមូលដ្ឋានទិន្នន័យ</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">Backup</span>
              </h4>
              <p class="text-[11px] text-slate-500 mt-0.5">
                ទាញយកទិន្នន័យប្រព័ន្ធទាំងអស់ជាឯកសារ Excel (.xlsx) គ្រប់សន្លឹក (សិស្ស គ្រូ ថ្នាក់ មុខវិជ្ជា ពិន្ទុ វត្តមាន ហិរញ្ញវត្ថុ) ឬជា SQL Dump ពេញលេញ
              </p>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-2.5">
            <!-- Download Excel Button -->
            <button 
              @click="handleBackupExcel" 
              :disabled="isExportingExcel"
              type="button"
              class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2 flex-shrink-0 cursor-pointer"
            >
              <i v-if="isExportingExcel" class="fa-solid fa-spinner fa-spin"></i>
              <i v-else class="fa-solid fa-file-excel"></i>
              <span>{{ isExportingExcel ? 'កំពុងបង្កើត Excel...' : 'ទាញយក Excel (.xlsx)' }}</span>
            </button>

            <!-- Download SQL Button -->
            <button 
              @click="handleBackup" 
              type="button"
              class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 transition flex items-center justify-center gap-2 flex-shrink-0 cursor-pointer"
            >
              <i class="fa-solid fa-database"></i>
              <span>ទាញយក SQL Dump</span>
            </button>
          </div>
        </div>

        <!-- Sheet Badges Info -->
        <div class="pt-3 border-t border-blue-100/80 flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
          <span class="font-bold text-slate-700 flex items-center gap-1">
            <i class="fa-solid fa-table-cells text-emerald-600"></i>
            <span>សន្លឹកទិន្នន័យក្នុង Excel:</span>
          </span>
          <span class="px-2 py-0.5 bg-white border border-slate-200 rounded-md font-medium text-slate-700">🧑‍🎓 សិស្ស</span>
          <span class="px-2 py-0.5 bg-white border border-slate-200 rounded-md font-medium text-slate-700">👨‍🏫 គ្រូបង្រៀន</span>
          <span class="px-2 py-0.5 bg-white border border-slate-200 rounded-md font-medium text-slate-700">🏫 ថ្នាក់រៀន</span>
          <span class="px-2 py-0.5 bg-white border border-slate-200 rounded-md font-medium text-slate-700">📚 មុខវិជ្ជា</span>
          <span class="px-2 py-0.5 bg-white border border-slate-200 rounded-md font-medium text-slate-700">📝 ពិន្ទុប្រឡង</span>
          <span class="px-2 py-0.5 bg-white border border-slate-200 rounded-md font-medium text-slate-700">📋 វត្តមាន</span>
          <span class="px-2 py-0.5 bg-white border border-slate-200 rounded-md font-medium text-slate-700">💵 ហិរញ្ញវត្ថុ</span>
        </div>
      </div>
    </div>

    <!-- Tab 5: Mobile App & PWA Settings -->
    <div v-show="activeTab === 'mobile_app'" class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs space-y-6 transition-all">
      <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
        <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
          <i class="fa-solid fa-mobile-screen-button"></i>
        </span>
        <div>
          <h2 class="text-sm font-bold text-slate-900">កម្មវិធីទូរស័ព្ទដៃ (Progressive Web App - PWA)</h2>
          <p class="text-[11px] text-slate-500">ដំឡើង និងគ្រប់គ្រងការប្រើប្រាស់លើទូរស័ព្ទដៃដូច Native App ពិតៗ</p>
        </div>
      </div>

      <!-- Hero Banner with Direct Install Button -->
      <div class="p-5 sm:p-6 bg-gradient-to-tr from-blue-600 via-indigo-600 to-blue-700 text-white rounded-3xl shadow-xl shadow-blue-500/20 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white p-2 flex items-center justify-center shadow-lg flex-shrink-0">
            <img :src="form.school_logo_url || '/images/logo.png'" alt="App Logo" class="w-full h-full object-contain" />
          </div>
          <div class="space-y-1">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-bold">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
              <span>PWA Ready • v2.4.0</span>
            </div>
            <h3 class="text-base sm:text-lg font-extrabold tracking-tight">{{ form.school_name || 'សាលារៀន ជំនាន់ថ្មី' }}</h3>
            <p class="text-xs text-blue-100 max-w-md">
              ដំឡើង App លើអេក្រង់ទូរស័ព្ទដៃ ដើម្បីបើកប្រើបានលឿន ពេញអេក្រង់ មិនបាត់ Session និងមិនបាច់វាយបញ្ចូល Password ម្ដងទៀតឡើយ!
            </p>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
          <button 
            type="button" 
            @click="openPwaModal"
            class="w-full sm:w-auto px-6 py-3 bg-white text-blue-700 hover:bg-blue-50 rounded-2xl font-bold text-xs shadow-lg transition flex items-center justify-center gap-2 active:scale-95 cursor-pointer"
          >
            <i class="fa-solid fa-download text-sm"></i>
            <span>ដំឡើង App លើទូរស័ព្ទឥឡូវនេះ</span>
          </button>
        </div>
      </div>

      <!-- Feature Grid: 3 Benefits -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/70 space-y-1.5">
          <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold">
            <i class="fa-solid fa-bolt"></i>
          </div>
          <h4 class="text-xs font-bold text-slate-800">ល្បឿនលឿនរហ័ស</h4>
          <p class="text-[11px] text-slate-500 leading-relaxed">
            ប្រើប្រាស់បច្ចេកវិទ្យា Service Worker Caching បើកទំព័រភ្លាមៗដោយមិនបាច់រង់ចាំយូរ។
          </p>
        </div>

        <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/70 space-y-1.5">
          <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-bold">
            <i class="fa-solid fa-mobile-screen"></i>
          </div>
          <h4 class="text-xs font-bold text-slate-800">ពេញអេក្រង់ទូរស័ព្ទ</h4>
          <p class="text-[11px] text-slate-500 leading-relaxed">
            គ្មានរបារ URL ខាងលើរំខានភ្នែក មាន Bottom Navigation ស្រួលចុចដោយមេដៃដូច App ពិតៗ។
          </p>
        </div>

        <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/70 space-y-1.5">
          <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">
            <i class="fa-solid fa-shield-halved"></i>
          </div>
          <h4 class="text-xs font-bold text-slate-800">រក្សាការ Login ទុក ១ ឆ្នាំ</h4>
          <p class="text-[11px] text-slate-500 leading-relaxed">
            មិនបារម្ភរឿង Page Expired ឬទាមទារ Password ញឹកញាប់ទៀតឡើយ Login តែម្តងប្រើបានរហូត។
          </p>
        </div>
      </div>

      <!-- Two Detailed Step-by-Step Cards (iOS & Android) + QR Code -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-2">
        <!-- iOS Guide -->
        <div class="p-5 rounded-2xl border border-slate-200/90 bg-white space-y-3">
          <div class="flex items-center gap-2 text-xs font-bold text-slate-900 border-b border-slate-100 pb-2.5">
            <i class="fa-brands fa-apple text-lg text-slate-900"></i>
            <span>សម្រាប់ iPhone / iPad (Safari)</span>
          </div>
          <ol class="space-y-2.5 text-xs text-slate-600">
            <li class="flex items-start gap-2">
              <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">1</span>
              <span>បើក Safari ចូលគេហទំព័រ <strong class="text-blue-600">sala-portal.vercel.app</strong></span>
            </li>
            <li class="flex items-start gap-2">
              <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">2</span>
              <span>ចុចប៊ូតុង <strong>Share</strong> (សញ្ញាព្រួញឡើងលើ 📤 នៅខាងក្រោម)</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">3</span>
              <span>អូសចុះក្រោម រួចចុច <strong>"Add to Home Screen" (បន្ថែមទៅអេក្រង់ដើម)</strong></span>
            </li>
            <li class="flex items-start gap-2">
              <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">4</span>
              <span>ចុច <strong>"Add"</strong> នៅជ្រុងខាងស្តាំលើ ជាការស្រេច!</span>
            </li>
          </ol>
        </div>

        <!-- Android Guide -->
        <div class="p-5 rounded-2xl border border-slate-200/90 bg-white space-y-3">
          <div class="flex items-center gap-2 text-xs font-bold text-slate-900 border-b border-slate-100 pb-2.5">
            <i class="fa-brands fa-android text-lg text-emerald-600"></i>
            <span>សម្រាប់ Android (Google Chrome)</span>
          </div>
          <ol class="space-y-2.5 text-xs text-slate-600">
            <li class="flex items-start gap-2">
              <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">1</span>
              <span>បើក Google Chrome ចូល <strong class="text-blue-600">sala-portal.vercel.app</strong></span>
            </li>
            <li class="flex items-start gap-2">
              <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">2</span>
              <span>ចុចសញ្ញាចុច ៣ (⋮) នៅជ្រុងខាងស្តាំលើនៃ Chrome</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">3</span>
              <span>ជ្រើសរើស <strong>"Install app"</strong> ឬ <strong>"Add to Home screen"</strong></span>
            </li>
            <li class="flex items-start gap-2">
              <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">4</span>
              <span>ចុច <strong>"Install" (ដំឡើង)</strong> ជាការស្រេច!</span>
            </li>
          </ol>
        </div>

        <!-- Scan to Install on Mobile QR Code -->
        <div class="p-5 rounded-2xl border border-slate-200/90 bg-white space-y-3 text-center flex flex-col items-center justify-between">
          <div class="w-full flex items-center justify-center gap-2 text-xs font-bold text-slate-900 border-b border-slate-100 pb-2.5">
            <i class="fa-solid fa-qrcode text-base text-blue-600"></i>
            <span>ស្កេនដើម្បីបើកលើទូរស័ព្ទ</span>
          </div>
          <div class="w-36 h-36 p-2 bg-slate-50 border border-slate-200/80 rounded-2xl shadow-2xs flex items-center justify-center">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=https://sala-portal.vercel.app" alt="App QR Code" class="w-full h-full object-contain" />
          </div>
          <p class="text-[11px] text-slate-500">
            ស្កេនដោយកាមេរ៉ាទូរស័ព្ទដៃ ដើម្បីបើកគេហទំព័រ និងដំឡើង App លើទូរស័ព្ទភ្លាមៗ។
          </p>
        </div>
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
import * as XLSX from 'xlsx';
import { updateFavicon } from '../utils/favicon';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import StatCard from '../components/StatCard.vue';

const { t } = useI18n();

// Tab state (defaults to 'general')
const activeTab = ref('general');
const isSaving = ref(false);

const academicYears = ref([]);

const openPwaModal = () => {
  localStorage.removeItem('pwa_dismissed_until');
  if (typeof window !== 'undefined' && typeof window.pwaOpenModal === 'function') {
    window.pwaOpenModal();
  }
};

const form = ref({
  // Demo Mode Switch
  demo_mode: localStorage.getItem('pdt_demo_mode') || '0',

  // Tab 1: School Info
  school_name: 'សាលារៀន ជំនាន់ថ្មី',
  school_name_en: 'New Generation School',
  principal_name: 'លោក ឈុន វណ្ណា',
  school_website: 'https://school.edu.kh',
  school_phone: '023 880 880 / 012 345 678',
  school_email: 'contact@school.edu.kh',
  school_address: 'មហាវិថីសហព័ន្ធរុស្ស៊ី សង្កាត់ទឹកល្អក់១ ខណ្ឌទួលគោក រាជធានីភ្នំពេញ',
  school_logo_url: '/images/logo.png',
  school_stamp_url: '',

  // Tab 2: Academic Setup
  current_academic_year: localStorage.getItem('pdt_current_academic_year') || window.__INITIAL_SETTINGS__?.current_academic_year || '2026-2027',
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
  bank_account_name: 'SCHOOL ACCOUNT',
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

const isPurging = ref(false);
const handlePurgeDemoData = async () => {
  const confirmText = prompt('តើអ្នកពិតជាចង់លុបទិន្នន័យគំរូ ទាំងអស់មែនទេ?\nសកម្មភាពនេះនឹងលុបសិស្ស គ្រូ ពិន្ទុ វត្តមាន កាលវិភាគ និងវិក្កយបត្រទាំងអស់ពី Database ដើម្បីឱ្យប្រព័ន្ធស្អាតសម្រាប់ Production។\n\nសូមវាយពាក្យ "DELETE" ដើម្បីបញ្ជាក់ការលុប៖');
  if (confirmText !== 'DELETE') {
    if (confirmText !== null) alert('ពាក្យបញ្ជាក់មិនត្រឹមត្រូវ! ការលុបទិន្នន័យត្រូវបានបោះបង់។');
    return;
  }
  isPurging.value = true;
  try {
    const res = await axios.post('/api/settings/reset-demo-data');
    localStorage.setItem('pdt_demo_mode', '0');
    form.value.demo_mode = '0';
    alert(res.data.message || 'បានសម្អាតទិន្នន័យ Demo ដោយជោគជ័យ!');
    window.location.reload();
  } catch (err) {
    console.error('Purge error:', err);
    alert('ការលុបបានបរាជ័យ៖ ' + (err.response?.data?.message || err.message));
  } finally {
    isPurging.value = false;
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
        if (s.school_logo_url) {
          updateFavicon(s.school_logo_url);
        }
        if (s.current_academic_year) {
          localStorage.setItem('pdt_current_academic_year', s.current_academic_year);
        }
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

      // Instantly reflect logo in sidebar top header & browser favicon
      updateFavicon(base64);
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
      if (form.value.current_academic_year) {
        localStorage.setItem('pdt_current_academic_year', form.value.current_academic_year);
      }
      alert(res.data.message || 'បានរក្សាទុកការកំណត់ប្រព័ន្ធដោយជោគជ័យ!');
      await fetchSettings();

      // Broadcast saved school logo, name and academic year to sidebar and components
      window.dispatchEvent(new CustomEvent('pdt-settings-saved', {
        detail: {
          school_logo_url: res.data.school_logo_url || form.value.school_logo_url,
          school_name: res.data.school_name || form.value.school_name,
          school_name_en: res.data.school_name_en || form.value.school_name_en,
          current_academic_year: form.value.current_academic_year
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

const isExportingExcel = ref(false);
const handleBackupExcel = async () => {
  isExportingExcel.value = true;
  try {
    const res = await axios.get('/api/settings/backup-excel');
    if (res.data && res.data.success && res.data.data) {
      const wb = XLSX.utils.book_new();
      const allSheets = res.data.data;
      
      let sheetCount = 0;
      Object.keys(allSheets).forEach(sheetName => {
        const rows = allSheets[sheetName];
        if (rows && rows.length > 0) {
          const ws = XLSX.utils.json_to_sheet(rows);
          // Auto calculate column widths for nice padding
          const colWidths = Object.keys(rows[0] || {}).map(key => {
            const maxValLen = Math.max(...rows.map(r => String(r[key] ?? '').length));
            const colLen = Math.max(key.length, maxValLen);
            return { wch: Math.min(Math.max(colLen + 4, 14), 45) };
          });
          ws['!cols'] = colWidths;
          // Excel sheet names cannot exceed 31 chars
          const safeName = sheetName.substring(0, 31);
          XLSX.utils.book_append_sheet(wb, ws, safeName);
          sheetCount++;
        }
      });

      if (sheetCount === 0) {
        const ws = XLSX.utils.aoa_to_sheet([['ទិន្នន័យទទេ']]);
        XLSX.utils.book_append_sheet(wb, ws, 'Info');
      }

      const today = new Date().toISOString().split('T')[0];
      const filename = `PDT_School_Database_Backup_${today}.xlsx`;
      XLSX.writeFile(wb, filename);
      alert('បានបង្កើត និងទាញយកឯកសារ Excel (' + filename + ') ដោយជោគជ័យ!');
    } else {
      throw new Error('No data received from server');
    }
  } catch (err) {
    console.error('Backup Excel error:', err);
    alert('ការទាញយក Excel បរាជ័យ៖ ' + (err.response?.data?.message || err.message));
  } finally {
    isExportingExcel.value = false;
  }
};

const handleBackup = () => {
  window.location.href = '/api/settings/backup';
};

const handlePrint = () => window.print();
</script>
