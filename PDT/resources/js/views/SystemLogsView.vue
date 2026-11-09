<template>
  <div class="space-y-6 pb-12 font-khmer">
    <!-- Top Header -->
    <PageHeader 
      title="កំណត់ហេតុប្រព័ន្ធ (System Activity Logs)" 
      subtitle="តាមដាន ត្រួតពិនិត្យ វត្តមានចូល-ចេញ និងកត់ត្រារាល់សកម្មភាពរបស់អ្នកប្រើប្រាស់ទូទាំងប្រព័ន្ធ"
    >
      <template #actions>
        <div class="flex items-center gap-2">
          <!-- Live Auto Refresh Toggle -->
          <button 
            type="button"
            @click="toggleAutoRefresh" 
            :class="[
              'px-3 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 border cursor-pointer',
              isAutoRefreshing ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'
            ]"
            :title="isAutoRefreshing ? 'កំពុងបើក Auto-Refresh រៀងរាល់ ៣០ វិនាទី' : 'ចុចដើម្បីបើក Auto-Refresh'"
          >
            <span class="w-2 h-2 rounded-full" :class="isAutoRefreshing ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
            <span>{{ isAutoRefreshing ? 'Auto Refresh: បើក' : 'Auto Refresh' }}</span>
          </button>

          <!-- Refresh Button -->
          <button 
            type="button" 
            @click="fetchLogs(true)" 
            :disabled="isLoading"
            class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer disabled:opacity-60"
          >
            <i class="fa-solid fa-arrows-rotate text-[11px]" :class="{ 'fa-spin': isLoading }"></i>
            <span>ផ្ទុកឡើងវិញ</span>
          </button>
        </div>
      </template>
    </PageHeader>

    <!-- View Mode Switcher Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3 flex-wrap">
      <button 
        type="button" 
        @click="switchViewMode('all')"
        :class="[
          'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer',
          activeViewMode === 'all' 
            ? 'bg-blue-600 text-white shadow-xs' 
            : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'
        ]"
      >
        <i class="fa-solid fa-list-check"></i>
        <span>សកម្មភាពទូទៅ (All Activities)</span>
      </button>

      <button 
        type="button" 
        @click="switchViewMode('sessions')"
        :class="[
          'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer',
          activeViewMode === 'sessions' 
            ? 'bg-indigo-600 text-white shadow-xs' 
            : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'
        ]"
      >
        <i class="fa-solid fa-stopwatch"></i>
        <span>វត្តមានចូល-ចេញ & ម៉ោងប្រើប្រាស់ (User Sessions & Usage)</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeViewMode === 'sessions' ? 'bg-indigo-500 text-white' : 'bg-indigo-100 text-indigo-700'">
          {{ (stats.successful_logins_today || 0) + (stats.logouts_today || 0) }} ថ្ងៃនេះ
        </span>
      </button>
    </div>

    <!-- 1. Summary Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
      <!-- Total Logs -->
      <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
        <div>
          <p class="text-xs font-medium text-slate-500">សកម្មភាពសរុប</p>
          <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mt-1 font-mono">{{ stats.total_logs.toLocaleString() }}</h3>
          <p class="text-[10px] text-slate-400 mt-0.5">កត់ត្រាស្វ័យប្រវត្ត</p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-lg shadow-inner">
          <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
      </div>

      <!-- Activities Today -->
      <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
        <div>
          <p class="text-xs font-medium text-slate-500">សកម្មភាពថ្ងៃនេះ</p>
          <h3 class="text-xl sm:text-2xl font-bold text-emerald-600 mt-1 font-mono">{{ stats.today_logs.toLocaleString() }}</h3>
          <p class="text-[10px] text-slate-400 mt-0.5">អ្នកប្រើសកម្ម: <strong class="text-slate-600">{{ stats.unique_users_today }}</strong> នាក់</p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-lg shadow-inner">
          <i class="fa-solid fa-calendar-day"></i>
        </div>
      </div>

      <!-- Successful Logins Today -->
      <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
        <div>
          <p class="text-xs font-medium text-slate-500">Login ចូលថ្ងៃនេះ</p>
          <h3 class="text-xl sm:text-2xl font-bold text-sky-600 mt-1 font-mono">{{ stats.successful_logins_today.toLocaleString() }}</h3>
          <p class="text-[10px] text-slate-400 mt-0.5">វត្តមានចូលប្រព័ន្ធ</p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center text-lg shadow-inner">
          <i class="fa-solid fa-right-to-bracket"></i>
        </div>
      </div>

      <!-- Logouts Today -->
      <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
        <div>
          <p class="text-xs font-medium text-slate-500">Logout ចេញថ្ងៃនេះ</p>
          <h3 class="text-xl sm:text-2xl font-bold text-purple-600 mt-1 font-mono">{{ (stats.logouts_today || 0).toLocaleString() }}</h3>
          <p class="text-[10px] text-slate-400 mt-0.5">បានកត់ត្រារយៈពេល</p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center text-lg shadow-inner">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </div>
      </div>

      <!-- Failed Logins Today -->
      <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between sm:col-span-2 lg:col-span-1">
        <div>
          <p class="text-xs font-medium text-slate-500">Login បរាជ័យ</p>
          <h3 class="text-xl sm:text-2xl font-bold mt-1 font-mono" :class="stats.failed_logins_today > 0 ? 'text-rose-600' : 'text-slate-700'">
            {{ stats.failed_logins_today.toLocaleString() }}
          </h3>
          <p class="text-[10px] mt-0.5" :class="stats.failed_logins_today > 0 ? 'text-rose-500 font-semibold' : 'text-slate-400'">
            {{ stats.failed_logins_today > 0 ? '⚠️ មានចូលខុស' : 'សុវត្ថិភាពល្អ' }}
          </p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-lg shadow-inner">
          <i class="fa-solid fa-shield-halved"></i>
        </div>
      </div>
    </div>

    <!-- 2. Search, Filter & Controls Panel -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4.5 space-y-3.5">
      <!-- Row 1: Search + Module Dropdown + Date Preset Dropdown -->
      <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
        <!-- Search Keyword -->
        <div class="md:col-span-5 relative">
          <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input 
            v-model="searchQuery" 
            @input="onSearchInput"
            type="text" 
            placeholder="ស្វែងរកតាមឈ្មោះ, IP, ឧបករណ៍, ID ឬការកែប្រែ..." 
            class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          />
          <button 
            v-if="searchQuery" 
            @click="searchQuery = ''; fetchLogs()" 
            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs"
          >
            <i class="fa-solid fa-circle-xmark"></i>
          </button>
        </div>

        <!-- Filter by Module -->
        <div class="md:col-span-4">
          <select 
            v-model="selectedModule" 
            @change="fetchLogs" 
            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
            <option value="">-- គ្រប់ផ្នែកទាំងអស់ (All Modules) --</option>
            <option v-for="m in availableModules" :key="m" :value="m">{{ m }}</option>
          </select>
        </div>

        <!-- Date Presets -->
        <div class="md:col-span-3">
          <select 
            v-model="datePreset" 
            @change="onDatePresetChange" 
            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          >
            <option value="all">📅 គ្រប់ពេលវេលា (All Time)</option>
            <option value="today">📅 ថ្ងៃនេះ (Today)</option>
            <option value="yesterday">📅 ម្សិលមិញ (Yesterday)</option>
            <option value="week">📅 ៧ ថ្ងៃចុងក្រោយ (Last 7 Days)</option>
            <option value="month">📅 ខែនេះ (This Month)</option>
            <option value="custom">📅 កាលបរិច្ឆេទកំណត់ផ្ទាល់ (Custom Range)</option>
          </select>
        </div>
      </div>

      <!-- Custom Date Range Picker (Only visible when Custom is selected) -->
      <div v-if="datePreset === 'custom'" class="flex items-center gap-3 pt-2 border-t border-slate-100 flex-wrap">
        <span class="text-xs text-slate-500 font-semibold">ចន្លោះកាលបរិច្ឆេទ៖</span>
        <div class="flex items-center gap-2">
          <span class="text-xs text-slate-400">ពី:</span>
          <input 
            v-model="customDateFrom" 
            type="date" 
            class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs"
          />
        </div>
        <div class="flex items-center gap-2">
          <span class="text-xs text-slate-400">ដល់:</span>
          <input 
            v-model="customDateTo" 
            type="date" 
            class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs"
          />
        </div>
        <button 
          type="button" 
          @click="fetchLogs" 
          class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold cursor-pointer"
        >
          អនុវត្ត
        </button>
      </div>

      <!-- Row 2: Action Type Filter Pills -->
      <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2 flex-wrap">
        <div class="flex items-center gap-1.5 overflow-x-auto text-xs py-0.5">
          <span class="text-[11px] font-bold text-slate-400 mr-1 whitespace-nowrap">សកម្មភាព៖</span>
          <button 
            type="button"
            @click="setActionFilter('')"
            :class="[
              'px-2.5 py-1 rounded-lg font-semibold transition cursor-pointer text-xs whitespace-nowrap',
              selectedAction === '' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            ]"
          >
            ទាំងអស់
          </button>
          <button 
            type="button"
            @click="setActionFilter('SESSIONS')"
            :class="[
              'px-2.5 py-1 rounded-lg font-semibold transition cursor-pointer text-xs whitespace-nowrap flex items-center gap-1',
              selectedAction === 'SESSIONS' ? 'bg-indigo-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            ]"
          >
            <i class="fa-solid fa-users text-[10px]"></i>
            <span>ចូល-ចេញ (Sessions)</span>
          </button>
          <button 
            type="button"
            @click="setActionFilter('CREATED')"
            :class="[
              'px-2.5 py-1 rounded-lg font-semibold transition cursor-pointer text-xs whitespace-nowrap flex items-center gap-1',
              selectedAction === 'CREATED' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            ]"
          >
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span>CREATED (បង្កើត)</span>
          </button>
          <button 
            type="button"
            @click="setActionFilter('UPDATED')"
            :class="[
              'px-2.5 py-1 rounded-lg font-semibold transition cursor-pointer text-xs whitespace-nowrap flex items-center gap-1',
              selectedAction === 'UPDATED' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            ]"
          >
            <i class="fa-solid fa-pen text-[10px]"></i>
            <span>UPDATED (កែប្រែ)</span>
          </button>
          <button 
            type="button"
            @click="setActionFilter('DELETED')"
            :class="[
              'px-2.5 py-1 rounded-lg font-semibold transition cursor-pointer text-xs whitespace-nowrap flex items-center gap-1',
              selectedAction === 'DELETED' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            ]"
          >
            <i class="fa-solid fa-trash-can text-[10px]"></i>
            <span>DELETED (លុប)</span>
          </button>
          <button 
            type="button"
            @click="setActionFilter('LOGIN')"
            :class="[
              'px-2.5 py-1 rounded-lg font-semibold transition cursor-pointer text-xs whitespace-nowrap flex items-center gap-1',
              selectedAction === 'LOGIN' ? 'bg-sky-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            ]"
          >
            <i class="fa-solid fa-right-to-bracket text-[10px]"></i>
            <span>LOGIN (ចូល)</span>
          </button>
          <button 
            type="button"
            @click="setActionFilter('LOGOUT')"
            :class="[
              'px-2.5 py-1 rounded-lg font-semibold transition cursor-pointer text-xs whitespace-nowrap flex items-center gap-1',
              selectedAction === 'LOGOUT' ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            ]"
          >
            <i class="fa-solid fa-arrow-right-from-bracket text-[10px]"></i>
            <span>LOGOUT (ចេញ)</span>
          </button>
          <button 
            type="button"
            @click="setActionFilter('FAILED_LOGIN')"
            :class="[
              'px-2.5 py-1 rounded-lg font-semibold transition cursor-pointer text-xs whitespace-nowrap flex items-center gap-1',
              selectedAction === 'FAILED_LOGIN' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            ]"
          >
            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
            <span>FAILED_LOGIN</span>
          </button>
        </div>

        <!-- Reset Button -->
        <button 
          v-if="hasActiveFilters" 
          type="button" 
          @click="resetFilters" 
          class="text-xs text-rose-600 hover:text-rose-700 font-semibold flex items-center gap-1 cursor-pointer transition ml-auto"
        >
          <i class="fa-solid fa-rotate-left text-[11px]"></i>
          <span>សម្អាត Filter</span>
        </button>
      </div>
    </div>

    <!-- 3. Logs Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
      <!-- Loading State -->
      <div v-if="isLoading" class="py-16 text-center">
        <div class="w-10 h-10 border-3 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
        <p class="text-xs text-slate-500 font-medium">កំពុងទាញយកកំណត់ហេតុសកម្មភាព...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="logs.length === 0" class="py-16 text-center">
        <div class="w-16 h-16 rounded-3xl bg-slate-50 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3 border border-slate-100 shadow-inner">
          📜
        </div>
        <h4 class="font-bold text-slate-800 text-sm">មិនមានកំណត់ហេតុសកម្មភាពដែលត្រូវនឹងការស្វែងរកទេ</h4>
        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
          សូមសាកល្បងផ្លាស់ប្តូរពាក្យគន្លឹះ កាលបរិច្ឆេទ ឬប្រភេទសកម្មភាពក្នុងការស្វែងរក។
        </p>
        <button 
          v-if="hasActiveFilters" 
          type="button" 
          @click="resetFilters" 
          class="mt-3 px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer"
        >
          សម្អាតការ Filter ទាំងអស់
        </button>
      </div>

      <!-- 3A. Dedicated Sessions Table (When in Sessions View Mode) -->
      <div v-else-if="activeViewMode === 'sessions'" class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-indigo-50/60 border-b border-indigo-100 text-slate-700 font-bold">
              <th class="py-3 px-4 whitespace-nowrap">កាលបរិច្ឆេទ</th>
              <th class="py-3 px-4 whitespace-nowrap">អ្នកប្រើប្រាស់ (User)</th>
              <th class="py-3 px-4 whitespace-nowrap">សកម្មភាព</th>
              <th class="py-3 px-4 whitespace-nowrap text-emerald-700">ម៉ោងចូល (Login)</th>
              <th class="py-3 px-4 whitespace-nowrap text-rose-700">ម៉ោងចេញ (Logout)</th>
              <th class="py-3 px-4 whitespace-nowrap text-purple-700">រយៈពេលប្រើប្រាស់</th>
              <th class="py-3 px-4 whitespace-nowrap">ឧបករណ៍ & IP Address</th>
              <th class="py-3 px-4">សេចក្តីពណ៌នា</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            <tr 
              v-for="log in logs" 
              :key="log.id" 
              class="hover:bg-indigo-50/30 transition-colors"
            >
              <!-- Date & Timestamp -->
              <td class="py-3 px-4 whitespace-nowrap">
                <div class="font-mono text-xs font-semibold text-slate-800">{{ formatDateTime(log.created_at) }}</div>
                <div class="text-[10px] text-slate-400">{{ formatRelativeTime(log.created_at) }}</div>
              </td>

              <!-- User -->
              <td class="py-3 px-4 whitespace-nowrap">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                    {{ (log.display_name || log.user_name || 'U').charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <div class="font-bold text-slate-900 leading-tight">
                      {{ log.display_name || log.user_name || 'ភ្ញៀវ (Guest)' }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                      <span 
                        class="inline-block px-1.5 py-0.2 rounded text-[10px] font-semibold border"
                        :class="getRoleBadgeClass(log.user_role)"
                      >
                        {{ log.user_role || 'User' }}
                      </span>
                      <span v-if="log.student_grade" class="text-[10px] text-slate-500 font-medium">
                        ថ្នាក់ {{ log.student_grade }}
                      </span>
                    </div>
                    <div v-if="log.user_email" class="text-[10px] text-slate-400 font-mono mt-0.5">
                      {{ log.user_email }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Action Badge -->
              <td class="py-3 px-4 whitespace-nowrap">
                <span 
                  class="px-2.5 py-1 rounded-full text-[11px] font-bold border inline-flex items-center gap-1.5"
                  :class="log.action === 'LOGIN' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-purple-50 text-purple-700 border-purple-200'"
                >
                  <i :class="log.action === 'LOGIN' ? 'fa-solid fa-right-to-bracket text-emerald-600' : 'fa-solid fa-arrow-right-from-bracket text-purple-600'" class="text-[11px]"></i>
                  <span>{{ log.action === 'LOGIN' ? 'ចូលប្រព័ន្ធ (LOGIN)' : 'ចាកចេញ (LOGOUT)' }}</span>
                </span>
              </td>

              <!-- Login Time -->
              <td class="py-3 px-4 whitespace-nowrap">
                <div class="inline-flex items-center gap-1.5 font-mono text-xs font-bold text-emerald-700 bg-emerald-50/80 px-2.5 py-1 rounded-lg border border-emerald-200/80">
                  <i class="fa-regular fa-clock text-[10px]"></i>
                  <span>{{ log.session_info?.login_time || (log.action === 'LOGIN' ? formatTimeOnly(log.created_at) : '---') }}</span>
                </div>
              </td>

              <!-- Logout Time -->
              <td class="py-3 px-4 whitespace-nowrap">
                <div v-if="log.action === 'LOGOUT'" class="inline-flex items-center gap-1.5 font-mono text-xs font-bold text-rose-700 bg-rose-50/80 px-2.5 py-1 rounded-lg border border-rose-200/80">
                  <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
                  <span>{{ log.session_info?.logout_time || formatTimeOnly(log.created_at) }}</span>
                </div>
                <div v-else-if="log.action === 'LOGIN'" class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600 bg-emerald-50/60 px-2 py-0.8 rounded-lg border border-emerald-100">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                  <span>កំពុងប្រើប្រាស់</span>
                </div>
                <span v-else class="text-slate-400 font-mono">---</span>
              </td>

              <!-- Usage Duration -->
              <td class="py-3 px-4 whitespace-nowrap">
                <div v-if="log.session_info?.duration" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-800 border border-indigo-200 shadow-2xs">
                  <i class="fa-solid fa-stopwatch text-indigo-600 text-[11px]"></i>
                  <span>{{ log.session_info.duration }}</span>
                </div>
                <div v-else-if="log.action === 'LOGIN'" class="text-[11px] text-slate-500 font-medium flex items-center gap-1">
                  <i class="fa-solid fa-hourglass-half text-amber-500 text-[10px]"></i>
                  <span>រាប់ម៉ោងពេល Logout</span>
                </div>
                <span v-else class="text-slate-400 font-mono">---</span>
              </td>

              <!-- Device & IP -->
              <td class="py-3 px-4 whitespace-nowrap">
                <div class="flex items-center gap-1.5 font-medium text-slate-800">
                  <i :class="getDeviceIcon(log.device_name)" class="text-slate-400 text-xs"></i>
                  <span>{{ log.device_name || 'Unknown Device' }}</span>
                </div>
                <div class="text-[11px] font-mono text-slate-400 mt-0.5 pl-4">
                  {{ log.ip_address || '127.0.0.1' }}
                </div>
              </td>

              <!-- Description -->
              <td class="py-3 px-4 max-w-xs">
                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed" :title="log.description">
                  {{ log.description || '---' }}
                </p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 3B. Standard Logs Table (When in All Activities View Mode) -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200/70 text-slate-600 font-bold">
              <th class="py-3 px-4 whitespace-nowrap">កាលបរិច្ឆេទ & ម៉ោង</th>
              <th class="py-3 px-4 whitespace-nowrap">អ្នកប្រព្រឹត្ត (Actor)</th>
              <th class="py-3 px-4 whitespace-nowrap">ផ្នែក (Module)</th>
              <th class="py-3 px-4 whitespace-nowrap">សកម្មភាព (Action)</th>
              <th class="py-3 px-4 whitespace-nowrap">ឧបករណ៍ & IP Address</th>
              <th class="py-3 px-4">សេចក្តីពណ៌នា</th>
              <th class="py-3 px-4 text-right whitespace-nowrap">ការកែប្រែ</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            <tr 
              v-for="log in logs" 
              :key="log.id" 
              class="hover:bg-slate-50/60 transition-colors"
            >
              <!-- Timestamp -->
              <td class="py-3 px-4 whitespace-nowrap">
                <div class="font-mono text-xs font-semibold text-slate-800">{{ formatDateTime(log.created_at) }}</div>
                <div class="text-[10px] text-slate-400">{{ formatRelativeTime(log.created_at) }}</div>
              </td>

              <!-- Actor -->
              <td class="py-3 px-4 whitespace-nowrap">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-700 text-xs shrink-0 shadow-2xs">
                    {{ (log.display_name || log.user_name || 'U').charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <div class="font-bold text-slate-900 leading-tight">
                      {{ log.display_name || log.user_name || 'ភ្ញៀវ (Guest)' }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                      <span 
                        class="inline-block px-1.5 py-0.2 rounded text-[10px] font-semibold border"
                        :class="getRoleBadgeClass(log.user_role)"
                      >
                        {{ log.user_role || 'User' }}
                      </span>
                      <span v-if="log.student_grade" class="text-[10px] text-slate-500 font-medium">
                        ថ្នាក់ {{ log.student_grade }}
                      </span>
                    </div>
                    <div v-if="log.user_email" class="text-[10px] text-slate-400 font-mono mt-0.5">
                      {{ log.user_email }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Module -->
              <td class="py-3 px-4 whitespace-nowrap">
                <span class="px-2 py-0.8 rounded-lg bg-slate-100 text-slate-800 font-semibold border border-slate-200/60 inline-flex items-center gap-1.5">
                  <i :class="getModuleIcon(log.module)" class="text-slate-500 text-[11px]"></i>
                  <span>{{ log.module }}</span>
                </span>
                <div v-if="log.target_id" class="text-[10px] font-mono text-slate-400 mt-0.5 pl-0.5">
                  ID: #{{ log.target_id }}
                </div>
              </td>

              <!-- Action Badge -->
              <td class="py-3 px-4 whitespace-nowrap">
                <span 
                  class="px-2.5 py-1 rounded-full text-[11px] font-bold border inline-flex items-center gap-1"
                  :class="getActionBadgeClass(log.action)"
                >
                  <i :class="getActionIcon(log.action)" class="text-[10px]"></i>
                  <span>{{ log.action }}</span>
                </span>

                <!-- Duration highlight if available -->
                <div v-if="log.action === 'LOGOUT' && log.session_info?.duration" class="mt-1">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                    <i class="fa-solid fa-stopwatch text-[9px]"></i>
                    <span>ប្រើអស់: {{ log.session_info.duration }}</span>
                  </span>
                </div>
                <div v-else-if="log.action === 'LOGIN'" class="mt-1">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                    <i class="fa-regular fa-clock text-[9px]"></i>
                    <span>ចូលម៉ោង: {{ log.session_info?.login_time || formatTimeOnly(log.created_at) }}</span>
                  </span>
                </div>
              </td>

              <!-- Device & IP -->
              <td class="py-3 px-4 whitespace-nowrap">
                <div class="flex items-center gap-1.5 font-medium text-slate-800">
                  <i :class="getDeviceIcon(log.device_name)" class="text-slate-400 text-xs"></i>
                  <span>{{ log.device_name || 'Unknown Device' }}</span>
                </div>
                <div class="text-[11px] font-mono text-slate-400 mt-0.5 pl-4">
                  {{ log.ip_address || '127.0.0.1' }}
                </div>
              </td>

              <!-- Description -->
              <td class="py-3 px-4 max-w-xs sm:max-w-md">
                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed" :title="log.description">
                  {{ log.description || '---' }}
                </p>
                <div v-if="log.session_info?.login_time && log.session_info?.logout_time" class="mt-1 text-[11px] text-slate-500 font-medium flex items-center gap-2 flex-wrap">
                  <span class="text-emerald-700 font-semibold"><i class="fa-regular fa-clock mr-1"></i>ចូល: {{ log.session_info.login_time }}</span>
                  <span>➔</span>
                  <span class="text-rose-700 font-semibold"><i class="fa-solid fa-arrow-right-from-bracket mr-1"></i>ចេញ: {{ log.session_info.logout_time }}</span>
                  <span class="text-purple-800 font-bold bg-purple-50 px-1.5 py-0.2 rounded border border-purple-100">រយៈពេល: {{ log.session_info.duration }}</span>
                </div>
              </td>

              <!-- View Changes Button -->
              <td class="py-3 px-4 text-right whitespace-nowrap">
                <button 
                  v-if="hasDiff(log)" 
                  type="button" 
                  @click="openDiffModal(log)" 
                  class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 cursor-pointer shadow-2xs"
                  title="ចុចដើម្បីពិនិត្យទិន្នន័យចាស់ និងទិន្នន័យថ្មី"
                >
                  <i class="fa-solid fa-code-compare text-[11px]"></i>
                  <span>មើលការកែប្រែ</span>
                </button>
                <span v-else class="text-slate-300 text-xs font-mono">---</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Table Footer / Pagination -->
      <div v-if="logs.length > 0" class="px-5 py-3.5 bg-slate-50/70 border-t border-slate-200/70 flex items-center justify-between text-xs flex-wrap gap-3">
        <div class="text-slate-500 font-medium">
          បង្ហាញពី <strong class="text-slate-800">{{ pagination.from || 0 }}</strong> ដល់ <strong class="text-slate-800">{{ pagination.to || 0 }}</strong> នៃសរុប <strong class="text-slate-800">{{ pagination.total.toLocaleString() }}</strong> សកម្មភាព
        </div>

        <div class="flex items-center gap-1.5">
          <button 
            type="button"
            :disabled="pagination.current_page <= 1"
            @click="changePage(pagination.current_page - 1)"
            class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-100 transition disabled:opacity-40 disabled:cursor-not-allowed font-medium"
          >
            &larr; ថយក្រោយ
          </button>

          <span class="px-3 py-1 bg-white border border-slate-200 rounded-lg font-bold text-slate-800">
            ទំព័រ {{ pagination.current_page }} / {{ pagination.last_page }}
          </span>

          <button 
            type="button"
            :disabled="pagination.current_page >= pagination.last_page"
            @click="changePage(pagination.current_page + 1)"
            class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-100 transition disabled:opacity-40 disabled:cursor-not-allowed font-medium"
          >
            បន្ទាប់ &rarr;
          </button>
        </div>
      </div>
    </div>

    <!-- 4. Diff Changes Modal (Modal ប្រៀបធៀបទិន្នន័យចាស់ និងថ្មី) -->
    <div 
      v-if="showModal && activeLog" 
      class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/70 backdrop-blur-xs overflow-y-auto"
      @click.self="showModal = false"
    >
      <div class="bg-white rounded-3xl w-full max-w-3xl shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150 my-auto">
        <!-- Modal Header -->
        <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
          <div class="flex items-center gap-3">
            <span 
              class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg border shadow-xs"
              :class="getActionBadgeClass(activeLog.action)"
            >
              <i :class="getActionIcon(activeLog.action)"></i>
            </span>
            <div>
              <div class="flex items-center gap-2">
                <h3 class="font-bold text-slate-900 text-sm sm:text-base">
                  កំណត់ត្រាការកែប្រែទិន្នន័យ
                </h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold border" :class="getActionBadgeClass(activeLog.action)">
                  {{ activeLog.action }}
                </span>
              </div>
              <p class="text-xs text-slate-400 mt-0.5">
                ផ្នែក៖ <strong class="text-slate-700">{{ activeLog.module }}</strong> • កាលបរិច្ឆេទ៖ <span class="font-mono">{{ formatDateTime(activeLog.created_at) }}</span>
              </p>
            </div>
          </div>

          <button 
            type="button" 
            @click="showModal = false" 
            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer text-lg font-bold"
          >
            &times;
          </button>
        </div>

        <!-- Meta Summary Strip -->
        <div class="px-6 py-3 bg-slate-50/40 border-b border-slate-100 flex items-center justify-between text-xs text-slate-600 flex-wrap gap-2">
          <div class="flex items-center gap-2">
            <i class="fa-solid fa-user-circle text-slate-400"></i>
            <span>អ្នកប្រព្រឹត្ត៖ <strong>{{ activeLog.display_name || activeLog.user_name }}</strong> ({{ activeLog.user_role }})</span>
          </div>
          <div class="flex items-center gap-3 font-mono text-[11px] text-slate-500">
            <span><i class="fa-solid fa-laptop text-slate-400 mr-1"></i>{{ activeLog.device_name }}</span>
            <span><i class="fa-solid fa-network-wired text-slate-400 mr-1"></i>{{ activeLog.ip_address }}</span>
          </div>
        </div>

        <!-- Modal Body: Diff List -->
        <div class="p-6 max-h-[65vh] overflow-y-auto space-y-4 custom-scrollbar">
          <div v-if="activeLog.description" class="p-3 bg-blue-50/60 rounded-xl border border-blue-100 text-xs text-blue-900">
            <i class="fa-solid fa-circle-info text-blue-500 mr-1.5"></i>
            <span>{{ activeLog.description }}</span>
          </div>

          <!-- Empty diff guard -->
          <div v-if="diffFields.length === 0" class="py-8 text-center text-slate-400 text-xs">
            មិនមានទិន្នន័យវាលដែលត្រូវប្រៀបធៀបឡើយ។
          </div>

          <!-- Diff comparison entries -->
          <div v-else class="space-y-3">
            <div 
              v-for="field in diffFields" 
              :key="field.name" 
              class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs"
            >
              <!-- Field Name Header -->
              <div class="px-4 py-2 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <span class="font-mono font-bold text-xs text-slate-800">
                  <i class="fa-solid fa-tag text-slate-400 mr-1.5 text-[10px]"></i>
                  {{ field.name }}
                </span>
                <span class="text-[10px] px-2 py-0.5 rounded-md font-bold uppercase" :class="field.isNew ? 'bg-emerald-100 text-emerald-800' : (field.isDeleted ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800')">
                  {{ field.isNew ? 'បង្កើតថ្មី (Created)' : (field.isDeleted ? 'បានលុប (Deleted)' : 'បានផ្លាស់ប្តូរ (Changed)') }}
                </span>
              </div>

              <!-- Side-by-Side Values -->
              <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-200 text-xs">
                <!-- Old Value -->
                <div class="p-3 bg-rose-50/50">
                  <div class="text-[10px] font-bold text-rose-600 mb-1 flex items-center gap-1">
                    <i class="fa-solid fa-minus"></i>
                    <span>ទិន្នន័យចាស់ (Old Value)</span>
                  </div>
                  <pre class="font-mono text-xs text-rose-950 whitespace-pre-wrap break-all bg-white/70 p-2.5 rounded-xl border border-rose-200/60 max-h-48 overflow-y-auto">{{ formatValue(field.old) }}</pre>
                </div>

                <!-- New Value -->
                <div class="p-3 bg-emerald-50/50">
                  <div class="text-[10px] font-bold text-emerald-600 mb-1 flex items-center gap-1">
                    <i class="fa-solid fa-plus"></i>
                    <span>ទិន្នន័យថ្មី (New Value)</span>
                  </div>
                  <pre class="font-mono text-xs text-emerald-950 whitespace-pre-wrap break-all bg-white/70 p-2.5 rounded-xl border border-emerald-200/60 max-h-48 overflow-y-auto">{{ formatValue(field.new) }}</pre>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3.5 border-t border-slate-100 flex items-center justify-between bg-slate-50/70">
          <span class="text-xs text-slate-400">
            កំណត់ហេតុប្រព័ន្ធជាទិន្នន័យ Read-Only មិនអាចកែប្រែ ឬលុបបានឡើយ
          </span>
          <button 
            type="button" 
            @click="showModal = false" 
            class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-xl text-xs font-bold transition cursor-pointer"
          >
            បិទផ្ទាំង
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';

// State
const isLoading = ref(true);
const logs = ref([]);
const stats = ref({
  total_logs: 0,
  today_logs: 0,
  successful_logins_today: 0,
  logouts_today: 0,
  failed_logins_today: 0,
  unique_users_today: 0,
});
const availableModules = ref([]);
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 20,
  total: 0,
  from: 0,
  to: 0,
});

// View Mode ('all' | 'sessions')
const activeViewMode = ref('all');

// Filters
const searchQuery = ref('');
const selectedModule = ref('');
const selectedAction = ref('');
const datePreset = ref('all');
const customDateFrom = ref('');
const customDateTo = ref('');

// Auto Refresh
const isAutoRefreshing = ref(false);
let autoRefreshTimer = null;

// Diff Modal State
const showModal = ref(false);
const activeLog = ref(null);

const hasActiveFilters = computed(() => {
  return searchQuery.value !== '' || selectedModule.value !== '' || selectedAction.value !== '' || datePreset.value !== 'all';
});

// Fetch Logs from Server
const fetchLogs = async (silent = false) => {
  if (!silent) isLoading.value = true;
  try {
    const params = {
      page: pagination.value.current_page,
      search: searchQuery.value.trim() || undefined,
      module: selectedModule.value || undefined,
      action: selectedAction.value || undefined,
      date_preset: datePreset.value,
      date_from: datePreset.value === 'custom' ? customDateFrom.value : undefined,
      date_to: datePreset.value === 'custom' ? customDateTo.value : undefined,
    };

    const res = await axios.get('/api/system-logs', { params });
    if (res.data && res.data.success) {
      logs.value = res.data.logs || [];
      if (res.data.stats) {
        stats.value = res.data.stats;
      }
      if (res.data.filters?.modules) {
        availableModules.value = res.data.filters.modules;
      }
      if (res.data.pagination) {
        pagination.value = res.data.pagination;
      }
    }
  } catch (err) {
    console.error('Failed to load system activity logs:', err);
  } finally {
    isLoading.value = false;
  }
};

// Search with slight debounce
let searchDebounceTimeout = null;
const onSearchInput = () => {
  clearTimeout(searchDebounceTimeout);
  searchDebounceTimeout = setTimeout(() => {
    pagination.value.current_page = 1;
    fetchLogs();
  }, 350);
};

const switchViewMode = (mode) => {
  activeViewMode.value = mode;
  pagination.value.current_page = 1;
  if (mode === 'sessions') {
    selectedAction.value = 'SESSIONS';
    selectedModule.value = '';
  } else {
    if (selectedAction.value === 'SESSIONS') {
      selectedAction.value = '';
    }
  }
  fetchLogs();
};

const setActionFilter = (act) => {
  selectedAction.value = act;
  if (act === 'SESSIONS') {
    activeViewMode.value = 'sessions';
  } else if (activeViewMode.value === 'sessions' && act !== 'LOGIN' && act !== 'LOGOUT') {
    activeViewMode.value = 'all';
  }
  pagination.value.current_page = 1;
  fetchLogs();
};

const onDatePresetChange = () => {
  pagination.value.current_page = 1;
  if (datePreset.value !== 'custom') {
    fetchLogs();
  }
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedModule.value = '';
  selectedAction.value = '';
  datePreset.value = 'all';
  customDateFrom.value = '';
  customDateTo.value = '';
  pagination.value.current_page = 1;
  fetchLogs();
};

const changePage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return;
  pagination.value.current_page = page;
  fetchLogs();
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const toggleAutoRefresh = () => {
  isAutoRefreshing.value = !isAutoRefreshing.value;
  if (isAutoRefreshing.value) {
    autoRefreshTimer = setInterval(() => {
      fetchLogs(true);
    }, 30000);
  } else {
    clearInterval(autoRefreshTimer);
    autoRefreshTimer = null;
  }
};

// Modal Diff Processing
const hasDiff = (log) => {
  if (!log.payload) return false;
  const p = log.payload;
  return (p.old_values && Object.keys(p.old_values).length > 0) || (p.new_values && Object.keys(p.new_values).length > 0);
};

const openDiffModal = (log) => {
  activeLog.value = log;
  showModal.value = true;
};

const diffFields = computed(() => {
  if (!activeLog.value || !activeLog.value.payload) return [];
  const p = activeLog.value.payload;
  const oldVals = p.old_values || {};
  const newVals = p.new_values || {};

  const allKeys = Array.from(new Set([...Object.keys(oldVals), ...Object.keys(newVals)]));
  return allKeys.map(key => {
    const hasOld = key in oldVals;
    const hasNew = key in newVals;
    return {
      name: key,
      old: oldVals[key] ?? null,
      new: newVals[key] ?? null,
      isNew: !hasOld && hasNew,
      isDeleted: hasOld && !hasNew,
      isChanged: hasOld && hasNew,
    };
  });
});

const formatValue = (val) => {
  if (val === null || val === undefined) return '(ទទេ / NULL)';
  if (typeof val === 'boolean') return val ? 'true (បាទ/ចាស)' : 'false (ទេ)';
  if (typeof val === 'object') return JSON.stringify(val, null, 2);
  return String(val);
};

// Helpers for Icons and Badges
const formatDateTime = (dt) => {
  if (!dt) return '---';
  const d = new Date(dt);
  return d.toLocaleString('sv-SE', { timeZone: 'Asia/Phnom_Penh' });
};

const formatTimeOnly = (dt) => {
  if (!dt) return '---';
  const d = new Date(dt);
  return d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Phnom_Penh' });
};

const formatRelativeTime = (dt) => {
  if (!dt) return '';
  const now = Date.now();
  const past = new Date(dt).getTime();
  const diffSec = Math.floor((now - past) / 1000);

  if (diffSec < 60) return 'ទើបតែមុននេះ';
  if (diffSec < 3600) return `${Math.floor(diffSec / 60)} នាទីមុន`;
  if (diffSec < 86400) return `${Math.floor(diffSec / 3600)} ម៉ោងមុន`;
  return `${Math.floor(diffSec / 86400)} ថ្ងៃមុន`;
};

const getActionBadgeClass = (action) => {
  switch (action) {
    case 'CREATED':
      return 'bg-emerald-50 text-emerald-700 border-emerald-200';
    case 'UPDATED':
      return 'bg-blue-50 text-blue-700 border-blue-200';
    case 'DELETED':
      return 'bg-rose-50 text-rose-700 border-rose-200';
    case 'LOGIN':
      return 'bg-sky-50 text-sky-700 border-sky-200';
    case 'FAILED_LOGIN':
      return 'bg-amber-50 text-amber-800 border-amber-200';
    case 'LOGOUT':
      return 'bg-purple-50 text-purple-700 border-purple-200';
    default:
      return 'bg-slate-50 text-slate-700 border-slate-200';
  }
};

const getActionIcon = (action) => {
  switch (action) {
    case 'CREATED':
      return 'fa-solid fa-plus-circle';
    case 'UPDATED':
      return 'fa-solid fa-pen-to-square';
    case 'DELETED':
      return 'fa-solid fa-trash-can';
    case 'LOGIN':
      return 'fa-solid fa-right-to-bracket';
    case 'FAILED_LOGIN':
      return 'fa-solid fa-triangle-exclamation';
    case 'LOGOUT':
      return 'fa-solid fa-arrow-right-from-bracket';
    default:
      return 'fa-solid fa-circle-dot';
  }
};

const getRoleBadgeClass = (role) => {
  const r = (role || '').toLowerCase();
  if (r.includes('super')) return 'bg-purple-50 text-purple-700 border-purple-200';
  if (r.includes('admin')) return 'bg-blue-50 text-blue-700 border-blue-200';
  if (r.includes('គ្រូ') || r.includes('teacher')) return 'bg-emerald-50 text-emerald-700 border-emerald-200';
  if (r.includes('សិស្ស') || r.includes('student')) return 'bg-amber-50 text-amber-700 border-amber-200';
  return 'bg-slate-50 text-slate-600 border-slate-200';
};

const getModuleIcon = (module) => {
  const m = module || '';
  if (m.includes('បណ្ណាល័យ') || m.includes('សៀវភៅ')) return 'fa-solid fa-book';
  if (m.includes('វត្តមាន')) return 'fa-solid fa-calendar-check';
  if (m.includes('សិស្ស')) return 'fa-solid fa-graduation-cap';
  if (m.includes('គ្រូ')) return 'fa-solid fa-user-tie';
  if (m.includes('ប្រឡង') || m.includes('ពិន្ទុ')) return 'fa-solid fa-file-pen';
  if (m.includes('ថ្នាក់')) return 'fa-solid fa-school';
  if (m.includes('ហិរញ្ញវត្ថុ') || m.includes('ប្រាក់')) return 'fa-solid fa-coins';
  if (m.includes('កំណត់') || m.includes('ប្រព័ន្ធ')) return 'fa-solid fa-gear';
  if (m.includes('ចូល')) return 'fa-solid fa-key';
  return 'fa-solid fa-layer-group';
};

const getDeviceIcon = (dev) => {
  const d = (dev || '').toLowerCase();
  if (d.includes('iphone') || d.includes('ios') || d.includes('android')) return 'fa-solid fa-mobile-screen';
  if (d.includes('mac') || d.includes('apple')) return 'fa-brands fa-apple';
  if (d.includes('windows')) return 'fa-brands fa-windows';
  if (d.includes('linux')) return 'fa-brands fa-linux';
  return 'fa-solid fa-desktop';
};

onMounted(() => {
  fetchLogs();
});

onUnmounted(() => {
  if (autoRefreshTimer) {
    clearInterval(autoRefreshTimer);
  }
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
  height: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 9999px;
}
</style>

