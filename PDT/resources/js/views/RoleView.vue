<template>
  <div class="space-y-6 font-khmer">
    <!-- Header -->
    <PageHeader 
      title="តួនាទី និងសិទ្ធិប្រើប្រាស់ប្រព័ន្ធ" 
      breadcrumb="សន្តិសុខ និងការកំណត់សិទ្ធិ" 
      actionLabel="💾 រក្សាទុកការផ្លាស់ប្តូរ"
      @action="handleSave"
    />

    <!-- Sub Tabs (Dynamic 3 Tabs) -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'matrix', label: 'តារាងបែងចែកសិទ្ធិ' },
        { id: 'controls', label: 'ការគ្រប់គ្រងសុវត្ថិភាពប្រព័ន្ធ' },
        { id: 'roles', label: 'បញ្ជីតួនាទី និងអ្នកប្រើប្រាស់' }
      ]"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard title="តួនាទីសរុប" :value="rolesList.length" />
      <StatCard title="មុខងារប្រព័ន្ធ" :value="systemModules.length" />
      <StatCard title="អ្នកប្រើប្រាស់សកម្ម" :value="totalActiveUsers" />
      <StatCard title="កម្រិតសុវត្ថិភាព" value="RBAC Protected" />
    </div>

    <!-- Alert Success / Info Message -->
    <div v-if="successMessage" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center justify-between animate-fade-in shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
        <span>{{ successMessage }}</span>
      </div>
      <button @click="successMessage = ''" class="text-emerald-600 hover:text-emerald-800">✕</button>
    </div>

    <!-- TAB 1: Permissions Matrix -->
    <div v-show="activeTab === 'matrix'" class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-base shadow-2xs">
            <i class="fa-solid fa-shield-halved"></i>
          </span>
          <div>
            <h2 class="text-sm font-bold text-slate-900">តារាងបែងចែកសិទ្ធិប្រើប្រាស់តាមតួនាទី</h2>
            <p class="text-[11px] text-slate-500">កំណត់សិទ្ធិមើល បន្ថែម និងកែប្រែទិន្នន័យសម្រាប់គ្រូបង្រៀន គណនេយ្យករ និងសិស្ស/អាណាព្យាបាល</p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button 
            type="button" 
            @click="resetToDefaults"
            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer"
          >
            🔄 កំណត់ឡើងវិញ
          </button>
          <button 
            type="button" 
            @click="handleSave"
            :disabled="isSaving"
            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition flex items-center gap-2 cursor-pointer"
          >
            <i v-if="isSaving" class="fa-solid fa-spinner fa-spin"></i>
            <i v-else class="fa-solid fa-floppy-disk"></i>
            <span>{{ isSaving ? 'កំពុងរក្សាទុក...' : 'រក្សាទុកសិទ្ធិ' }}</span>
          </button>
        </div>
      </div>

      <!-- Matrix Table -->
      <div class="overflow-x-auto border border-slate-200/90 rounded-2xl">
        <table class="w-full text-left text-xs border-collapse">
          <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
            <tr>
              <th class="py-3.5 px-4 font-bold text-slate-800">មុខងារប្រព័ន្ធ</th>
              <th class="py-3.5 px-3 text-center w-36">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 font-bold text-[11px]">
                  <i class="fa-solid fa-crown text-[10px]"></i>
                  អ្នកគ្រប់គ្រង
                </span>
              </th>
              <th class="py-3.5 px-3 text-center w-36">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-800 font-bold text-[11px]">
                  <i class="fa-solid fa-chalkboard-user text-[10px]"></i>
                  គ្រូបង្រៀន
                </span>
              </th>
              <th class="py-3.5 px-3 text-center w-36">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-bold text-[11px]">
                  <i class="fa-solid fa-calculator text-[10px]"></i>
                  គណនេយ្យករ
                </span>
              </th>
              <th class="py-3.5 px-3 text-center w-36">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 font-bold text-[11px]">
                  <i class="fa-solid fa-users text-[10px]"></i>
                  សិស្ស / អាណាព្យាបាល
                </span>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="mod in systemModules" :key="mod.key" class="hover:bg-slate-50/70 transition-colors">
              <td class="py-3 px-4 font-medium text-slate-800">
                <div class="flex items-center gap-2.5">
                  <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs flex-shrink-0">
                    <i :class="mod.icon"></i>
                  </span>
                  <div>
                    <span class="font-bold text-slate-900 block">{{ mod.name }}</span>
                    <span class="text-[10px] text-slate-400 font-normal">{{ mod.desc }}</span>
                  </div>
                </div>
              </td>
              <!-- Admin: Always checked and disabled (Master Access) -->
              <td class="py-3 px-3 text-center">
                <input 
                  type="checkbox" 
                  checked 
                  disabled 
                  class="w-4 h-4 rounded text-blue-600 bg-slate-100 border-slate-300 cursor-not-allowed"
                  title="អ្នកគ្រប់គ្រងមានសិទ្ធិពេញលេញគ្រប់មុខងារទាំងអស់"
                />
              </td>
              <!-- Teacher -->
              <td class="py-3 px-3 text-center">
                <input 
                  type="checkbox" 
                  :checked="hasPermission('teacher', mod.key)" 
                  @change="togglePermission('teacher', mod.key)"
                  class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 cursor-pointer"
                />
              </td>
              <!-- Accountant -->
              <td class="py-3 px-3 text-center">
                <input 
                  type="checkbox" 
                  :checked="hasPermission('accountant', mod.key)" 
                  @change="togglePermission('accountant', mod.key)"
                  class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer"
                />
              </td>
              <!-- Student / Parent -->
              <td class="py-3 px-3 text-center">
                <input 
                  type="checkbox" 
                  :checked="hasPermission('student_parent', mod.key)" 
                  @change="togglePermission('student_parent', mod.key)"
                  class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500 border-slate-300 cursor-pointer"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl flex items-center justify-between text-xs text-slate-600">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-circle-info text-blue-600"></i>
          <span>រាល់ការកែប្រែសិទ្ធិនឹងមានប្រសិទ្ធភាពភ្លាមៗបន្ទាប់ពីចុច <strong>រក្សាទុកសិទ្ធិ</strong>។</span>
        </div>
        <button 
          @click="handleSave" 
          :disabled="isSaving" 
          type="button" 
          class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-xl font-bold shadow-xs transition"
        >
          {{ isSaving ? 'កំពុងរក្សាទុក...' : 'រក្សាទុកសិទ្ធិ' }}
        </button>
      </div>
    </div>

    <!-- TAB 2: System Security Controls -->
    <div v-show="activeTab === 'controls'" class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs space-y-6">
      <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
        <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base shadow-2xs">
          <i class="fa-solid fa-sliders"></i>
        </span>
        <div>
          <h2 class="text-sm font-bold text-slate-900">ការគ្រប់គ្រងសុវត្ថិភាព និងការអនុញ្ញាតប្រព័ន្ធ</h2>
          <p class="text-[11px] text-slate-500">បើក ឬបិទមុខងារចុះឈ្មោះសាធារណៈ សិទ្ធិកែប្រែពិន្ទុ និងការស្នើសុំច្បាប់</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- 1. Public Student Registration -->
        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between gap-4">
          <div>
            <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
              <span>ការចុះឈ្មោះសិស្សថ្មីតាម Link / QR Code</span>
              <span :class="controls.enable_public_registration === '1' ? 'text-emerald-600' : 'text-slate-400'">
                ● {{ controls.enable_public_registration === '1' ? 'បើកដំណើរការ' : 'បានបិទ' }}
              </span>
            </h4>
            <p class="text-[11px] text-slate-500 mt-1">អនុញ្ញាតឱ្យសិស្ស និងអាណាព្យាបាលចុះឈ្មោះចូលរៀនតាមរយៈតំណភ្ជាប់សាធារណៈ</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
            <input 
              type="checkbox" 
              v-model="controls.enable_public_registration" 
              true-value="1" 
              false-value="0" 
              class="sr-only peer"
            >
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
          </label>
        </div>

        <!-- 2. Teacher Score Editing Permission -->
        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between gap-4">
          <div>
            <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
              <span>អនុញ្ញាតឱ្យគ្រូបង្រៀនកែប្រែពិន្ទុប្រឡង</span>
              <span :class="controls.enable_teacher_score_edit === '1' ? 'text-blue-600' : 'text-slate-400'">
                ● {{ controls.enable_teacher_score_edit === '1' ? 'អនុញ្ញាត' : 'ចាក់សោ' }}
              </span>
            </h4>
            <p class="text-[11px] text-slate-500 mt-1">បើក ឬបិទសិទ្ធិរបស់លោកគ្រូអ្នកគ្រូក្នុងការកែប្រែពិន្ទុបន្ទាប់ពីបាន Submit រួច</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
            <input 
              type="checkbox" 
              v-model="controls.enable_teacher_score_edit" 
              true-value="1" 
              false-value="0" 
              class="sr-only peer"
            >
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
          </label>
        </div>

        <!-- 3. Public Teacher Registration -->
        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between gap-4">
          <div>
            <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
              <span>ការចុះឈ្មោះគ្រូបង្រៀនជាសាធារណៈ</span>
              <span :class="controls.enable_public_teacher_registration === '1' ? 'text-emerald-600' : 'text-slate-400'">
                ● {{ controls.enable_public_teacher_registration === '1' ? 'បើកដំណើរការ' : 'បានបិទ' }}
              </span>
            </h4>
            <p class="text-[11px] text-slate-500 mt-1">អនុញ្ញាតឱ្យគ្រូបង្រៀនថ្មីបំពេញពាក្យសុំបង្រៀនតាម Link / QR Code ដោយស្វ័យប្រវត្តិ</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
            <input 
              type="checkbox" 
              v-model="controls.enable_public_teacher_registration" 
              true-value="1" 
              false-value="0" 
              class="sr-only peer"
            >
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
          </label>
        </div>

        <!-- 4. Public Leave Request -->
        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between gap-4">
          <div>
            <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
              <span>ការស្នើសុំច្បាប់សិស្សជាសាធារណៈ</span>
              <span :class="controls.enable_public_leave_request === '1' ? 'text-emerald-600' : 'text-slate-400'">
                ● {{ controls.enable_public_leave_request === '1' ? 'បើកដំណើរការ' : 'បានបិទ' }}
              </span>
            </h4>
            <p class="text-[11px] text-slate-500 mt-1">អនុញ្ញាតឱ្យអាណាព្យាបាលសុំច្បាប់ឱ្យសិស្សតាម Online Form ដោយមិនបាច់ Login</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
            <input 
              type="checkbox" 
              v-model="controls.enable_public_leave_request" 
              true-value="1" 
              false-value="0" 
              class="sr-only peer"
            >
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
          </label>
        </div>
      </div>

      <div class="flex items-center justify-end pt-2">
        <button 
          @click="handleSave" 
          :disabled="isSaving" 
          type="button" 
          class="px-7 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition flex items-center gap-2 cursor-pointer"
        >
          <i v-if="isSaving" class="fa-solid fa-spinner fa-spin"></i>
          <i v-else class="fa-solid fa-floppy-disk"></i>
          <span>{{ isSaving ? 'កំពុងរក្សាទុក...' : 'រក្សាទុកការកំណត់សុវត្ថិភាព' }}</span>
        </button>
      </div>
    </div>

    <!-- TAB 3: Roles Directory -->
    <div v-show="activeTab === 'roles'" class="space-y-4">
      <FilterBar>
        <template #left>
          <span class="font-medium text-slate-700 text-xs">Access Policy: <span class="font-bold text-[#1e40af]">Role-Based (RBAC)</span></span>
          <span class="font-medium text-slate-700 text-xs ml-2">Guard: <span class="font-bold text-emerald-600">Web Session</span></span>
        </template>

        <template #right>
          <div class="relative w-64">
            <input 
              type="text" 
              v-model="searchQuery" 
              :placeholder="t('common.searchPlaceholder')" 
              class="w-full px-3.5 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-khmer"
            />
          </div>
        </template>
      </FilterBar>

      <DataTable :loading="false" :empty="filteredRoles.length === 0" :colSpan="7">
        <template #header>
          <tr>
            <th class="py-3 px-4 font-bold text-slate-700">Role ID</th>
            <th class="py-3 px-4 font-bold text-slate-700">ឈ្មោះតួនាទី</th>
            <th class="py-3 px-4 font-bold text-slate-700">ឈ្មោះសម្គាល់</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-center">អ្នកប្រើប្រាស់</th>
            <th class="py-3 px-4 font-bold text-slate-700">សិទ្ធិសំខាន់ៗ</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ t('common.status') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ t('common.actions') }}</th>
          </tr>
        </template>

        <template #body>
          <tr v-for="role in filteredRoles" :key="role.id" class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3 px-4 font-mono font-bold text-blue-600">ROL-00{{ role.id }}</td>
            <td class="py-3 px-4 font-bold text-slate-900 capitalize">{{ role.name }}</td>
            <td class="py-3 px-4 text-slate-700 font-semibold">{{ role.displayName }}</td>
            <td class="py-3 px-4 text-center font-bold text-slate-800">
              <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                {{ role.userCount }} នាក់
              </span>
            </td>
            <td class="py-3 px-4 text-slate-500 text-xs">
              <div class="flex flex-wrap gap-1 max-w-xs">
                <span 
                  v-for="p in getRoleSummaryPermissions(role.name)" 
                  :key="p"
                  class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md font-medium text-[10px]"
                >
                  {{ p }}
                </span>
              </div>
            </td>
            <td class="py-3 px-4 text-center">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
                Active
              </span>
            </td>
            <td class="py-3 px-4 text-right">
              <button 
                @click="activeTab = 'matrix'" 
                class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition cursor-pointer"
              >
                កែប្រែសិទ្ធិ
              </button>
            </td>
          </tr>
        </template>
      </DataTable>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import StatCard from '../components/StatCard.vue';
import FilterBar from '../components/FilterBar.vue';
import DataTable from '../components/DataTable.vue';

const { t } = useI18n();

const activeTab = ref('matrix');
const searchQuery = ref('');
const isSaving = ref(false);
const successMessage = ref('');

// 9 System Modules Definition
const systemModules = [
  { key: 'students', name: 'គ្រប់គ្រងសិស្សានុសិស្ស', desc: 'មើល បន្ថែម កែប្រែសិស្ស & OCR', icon: 'fa-solid fa-graduation-cap' },
  { key: 'attendance', name: 'កត់ត្រា និងគ្រប់គ្រងវត្តមាន', desc: 'កត់ត្រាវត្តមានសិស្ស និងលោកគ្រូអ្នកគ្រូ', icon: 'fa-solid fa-calendar-check' },
  { key: 'examinations', name: 'បញ្ចូលពិន្ទុ និងការប្រឡង', desc: 'បញ្ចូលពិន្ទុ តារាងពិន្ទុរួម និងសៀវភៅតាមដាន', icon: 'fa-solid fa-clipboard-check' },
  { key: 'classes_timetable', name: 'ថ្នាក់រៀន និងកាលវិភាគបង្រៀន', desc: 'គ្រប់គ្រងថ្នាក់រៀន បន្ទប់ និងកាលវិភាគប្រចាំសប្តាហ៍', icon: 'fa-solid fa-school' },
  { key: 'finance', name: 'ហិរញ្ញវត្ថុ វិក្កយបត្រ និងការបង់ប្រាក់', desc: 'បង្កើតវិក្កយបត្រ បង់ថ្លៃសិក្សា និងចំណាយសាលា', icon: 'fa-solid fa-file-invoice-dollar' },
  { key: 'library', name: 'គ្រប់គ្រងបណ្ណាល័យ', desc: 'កាតាឡុកសៀវភៅ និងការខ្ចី-សងសៀវភៅ', icon: 'fa-solid fa-book-open' },
  { key: 'announcements', name: 'ដំណឹង និងសេចក្ដីជូនដំណឹង', desc: 'ផ្សាយដំណឹងទូទាំងសាលា និងផ្ញើសារ', icon: 'fa-solid fa-bullhorn' },
  { key: 'reports', name: 'ទាញយករបាយការណ៍ និងស្ថិតិ', desc: 'របាយការណ៍លទ្ធផលសិក្សា វត្តមាន និងហិរញ្ញវត្ថុ', icon: 'fa-solid fa-chart-pie' },
  { key: 'settings', name: 'ការកំណត់ប្រព័ន្ធ និងគេហទំព័រ', desc: 'ព័ត៌មានសាលា រូបិយប័ណ្ណ និងទិន្នន័យបម្រុង', icon: 'fa-solid fa-gear' },
];

// Reactive Permissions Matrix State
const permissions = ref({
  admin: ['students', 'attendance', 'examinations', 'classes_timetable', 'finance', 'library', 'announcements', 'reports', 'settings'],
  teacher: ['attendance', 'examinations', 'library', 'reports'],
  accountant: ['finance', 'reports'],
  student_parent: ['attendance', 'examinations', 'library'],
});

// Reactive Security Controls State
const controls = ref({
  enable_public_registration: '1',
  enable_teacher_score_edit: '1',
  enable_public_teacher_registration: '1',
  enable_public_leave_request: '1',
});

// Roles List
const rolesList = ref([
  { id: 1, name: 'admin', displayName: 'អ្នកគ្រប់គ្រងប្រព័ន្ធ', userCount: 2 },
  { id: 2, name: 'teacher', displayName: 'លោកគ្រូ-អ្នកគ្រូ', userCount: 15 },
  { id: 3, name: 'accountant', displayName: 'គណនេយ្យករ', userCount: 2 },
  { id: 4, name: 'student', displayName: 'សិស្សានុសិស្ស', userCount: 87 },
  { id: 5, name: 'parent', displayName: 'មាតាបិតា-អាណាព្យាបាល', userCount: 35 },
]);

const totalActiveUsers = computed(() => {
  return rolesList.value.reduce((acc, r) => acc + (r.userCount || 0), 0);
});

const filteredRoles = computed(() => {
  if (!searchQuery.value) return rolesList.value;
  const q = searchQuery.value.toLowerCase().trim();
  return rolesList.value.filter(r => 
    r.name.toLowerCase().includes(q) || 
    r.displayName.toLowerCase().includes(q)
  );
});

// Check if a role has permission for a module
const hasPermission = (roleKey, moduleKey) => {
  const rolePerms = permissions.value[roleKey] || [];
  return rolePerms.includes(moduleKey);
};

// Toggle permission for a role
const togglePermission = (roleKey, moduleKey) => {
  if (!permissions.value[roleKey]) {
    permissions.value[roleKey] = [];
  }
  const idx = permissions.value[roleKey].indexOf(moduleKey);
  if (idx > -1) {
    permissions.value[roleKey].splice(idx, 1);
  } else {
    permissions.value[roleKey].push(moduleKey);
  }
};

const getRoleSummaryPermissions = (roleName) => {
  let key = roleName;
  if (roleName === 'student' || roleName === 'parent') key = 'student_parent';
  const perms = permissions.value[key] || [];
  return perms.map(k => {
    const mod = systemModules.find(m => m.key === k);
    return mod ? mod.name : k;
  });
};

const resetToDefaults = () => {
  permissions.value = {
    admin: ['students', 'attendance', 'examinations', 'classes_timetable', 'finance', 'library', 'announcements', 'reports', 'settings'],
    teacher: ['attendance', 'examinations', 'library', 'reports'],
    accountant: ['finance', 'reports'],
    student_parent: ['attendance', 'examinations', 'library'],
  };
  controls.value = {
    enable_public_registration: '1',
    enable_teacher_score_edit: '1',
    enable_public_teacher_registration: '1',
    enable_public_leave_request: '1',
  };
  successMessage.value = 'បានកំណត់សិទ្ធិ និងសុវត្ថិភាពឡើងវិញតាមលំនាំដើម!';
};

// Fetch Roles & Saved Permissions
const fetchRolesData = async () => {
  try {
    const res = await axios.get('/api/roles');
    if (res.data) {
      if (res.data.roles && res.data.roles.length > 0) {
        rolesList.value = res.data.roles;
      }
      if (res.data.permissions) {
        permissions.value = res.data.permissions;
      }
      if (res.data.controls) {
        Object.assign(controls.value, res.data.controls);
      }
    }
  } catch (err) {
    console.error('Failed to load roles data:', err);
  }
};

// Save Permissions & Controls to Backend
const handleSave = async () => {
  isSaving.value = true;
  successMessage.value = '';
  try {
    const res = await axios.post('/api/roles/permissions', {
      permissions: permissions.value,
      controls: controls.value,
    });
    successMessage.value = res.data?.message || 'បានរក្សាទុកសិទ្ធិ និងការគ្រប់គ្រងប្រព័ន្ធដោយជោគជ័យ!';
    setTimeout(() => {
      successMessage.value = '';
    }, 4000);
  } catch (err) {
    console.error('Save permissions error:', err);
    alert('ការរក្សាទុកបានបរាជ័យ៖ ' + (err.response?.data?.message || err.message));
  } finally {
    isSaving.value = false;
  }
};

onMounted(() => {
  fetchRolesData();
});

const handlePrint = () => window.print();
const handleDownload = () => window.location.href = '/api/settings/backup';
</script>
