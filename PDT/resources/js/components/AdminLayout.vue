<template>
  <div class="h-screen w-screen bg-[#f8fafc] flex font-khmer antialiased text-slate-800 overflow-hidden">
    <!-- Master App Canvas Container (100% Full Screen Edge-to-Edge) -->
    <div class="w-full h-full bg-white flex overflow-hidden">
      
      <!-- 1. LEFT SIDEBAR (LMSTICK Brand) -->
      <aside 
        :class="[
          'flex-shrink-0 bg-white border-r border-slate-100 flex flex-col justify-between transition-all duration-300 z-30',
          sidebarOpen ? 'w-64 translate-x-0' : '-translate-x-full md:translate-x-0 md:w-20'
        ]"
      >
        <!-- Logo Header -->
        <div class="h-20 px-4 md:px-5 flex items-center justify-between border-b border-slate-50">
          <router-link to="/attendance" class="flex items-center gap-3 group min-w-0">
            <!-- Custom School Logo (if uploaded) -->
            <div 
              v-if="schoolLogo" 
              class="w-10 h-10 rounded-xl overflow-hidden border border-slate-100 bg-white flex items-center justify-center p-1 shadow-xs group-hover:scale-105 transition-transform flex-shrink-0"
            >
              <img :src="schoolLogo" alt="School Logo" class="w-full h-full object-contain" />
            </div>

            <!-- Default Brand Mark Circle (Blue & Navy) when no logo uploaded -->
            <div 
              v-else 
              class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#1e3a8a] to-[#2563eb] flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform flex-shrink-0"
            >
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" opacity="0.3"/>
                <path d="M12 4a8 8 0 0 1 8 8h-8V4z" fill="#ffffff"/>
              </svg>
            </div>

            <div v-if="sidebarOpen" class="flex flex-col truncate">
              <span class="text-base font-extrabold tracking-tight text-[#1e293b] leading-tight truncate">
                {{ schoolName || 'LMSTICK' }}
              </span>
              <span v-if="schoolNameEn" class="text-[10px] text-slate-400 font-medium truncate leading-none mt-0.5">
                {{ schoolNameEn }}
              </span>
            </div>
          </router-link>
          
          <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-50 transition" title="Toggle Menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>
        </div>

        <!-- Navigation Menu -->
        <div class="flex-1 overflow-y-auto px-3 py-4 space-y-1 custom-scrollbar">
          <template v-for="item in menuItems" :key="item.path || item.id">
            <!-- Dropdown Menu Item (e.g. វត្តមាន: សិស្ស & គ្រូ) -->
            <div v-if="item.children" class="space-y-1">
              <button 
                type="button"
                @click="toggleDropdown(item.id)"
                :class="[
                  'w-full flex items-center rounded-xl text-[13px] font-medium transition-all group cursor-pointer',
                  sidebarOpen ? 'justify-between px-3.5 py-2.5' : 'justify-center p-2.5',
                  isChildActive(item) 
                    ? 'bg-[#e5ecfc] text-[#1e40af] font-semibold shadow-xs' 
                    : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50'
                ]"
                :title="!sidebarOpen ? t(item.i18nKey) : ''"
              >
                <div class="flex items-center gap-3 truncate">
                  <component 
                    :is="item.icon" 
                    :class="[
                      'w-5 h-5 flex-shrink-0 transition-colors',
                      isChildActive(item) ? 'text-[#1e40af]' : 'text-slate-400 group-hover:text-slate-600'
                    ]"
                  />
                  <span v-if="sidebarOpen" class="truncate">{{ t(item.i18nKey) }}</span>
                </div>
                <svg 
                  v-if="sidebarOpen" 
                  class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 flex-shrink-0"
                  :class="{ 'rotate-180 text-blue-600': openDropdowns.includes(item.id) }"
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <!-- Dropdown Children Sub-items -->
              <div 
                v-if="sidebarOpen && openDropdowns.includes(item.id)" 
                class="pl-4 pr-1 py-1 space-y-1 border-l-2 border-blue-100 ml-4.5 transition-all"
              >
                <router-link
                  v-for="sub in item.children"
                  :key="sub.path"
                  :to="sub.path"
                  :class="[
                    'flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all',
                    isActive(sub.path)
                      ? 'bg-blue-50 text-blue-700 font-bold shadow-xs'
                      : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'
                  ]"
                >
                  <component 
                    :is="sub.icon" 
                    :class="[
                      'w-4 h-4 flex-shrink-0 transition-colors',
                      isActive(sub.path) ? 'text-blue-600' : 'text-slate-400'
                    ]"
                  />
                  <span class="truncate">{{ currentLocale === 'km' ? sub.nameKm : sub.nameEn }}</span>
                </router-link>
              </div>
            </div>

            <!-- Standard Direct Navigation Link -->
            <router-link 
              v-else
              :to="item.path"
              :class="[
                'flex items-center rounded-xl text-[13px] font-medium transition-all group',
                sidebarOpen ? 'justify-between px-3.5 py-2.5' : 'justify-center p-2.5',
                isActive(item.path) 
                  ? 'bg-[#e5ecfc] text-[#1e40af] font-semibold shadow-xs' 
                  : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50'
              ]"
              :title="!sidebarOpen ? t(item.i18nKey) : ''"
            >
              <div class="flex items-center gap-3 truncate">
                <component 
                  :is="item.icon" 
                  :class="[
                    'w-5 h-5 flex-shrink-0 transition-colors',
                    isActive(item.path) ? 'text-[#1e40af]' : 'text-slate-400 group-hover:text-slate-600'
                  ]"
                />
                <span v-if="sidebarOpen" class="truncate">{{ t(item.i18nKey) }}</span>
              </div>
            </router-link>
          </template>
        </div>

        <!-- Bottom Logout Button -->
        <div class="p-3 border-t border-slate-50">
          <a 
            href="/logout" 
            onclick="event.preventDefault(); document.getElementById('spa-logout-form').submit();"
            :class="[
              'w-full flex items-center rounded-xl text-[13px] font-medium text-slate-600 hover:text-rose-600 hover:bg-rose-50/60 transition group',
              sidebarOpen ? 'justify-between px-3.5 py-2.5' : 'justify-center p-2.5'
            ]"
            :title="!sidebarOpen ? t('common.logout') : ''"
          >
            <div class="flex items-center gap-2.5">
              <svg class="w-4 h-4 flex-shrink-0 text-slate-400 group-hover:text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              <span v-if="sidebarOpen">{{ t('common.logout') }}</span>
            </div>
            <div v-if="sidebarOpen" class="w-5 h-5 rounded-full border border-slate-200 flex items-center justify-center text-slate-400 group-hover:border-rose-300 group-hover:text-rose-600 transition">
              <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </div>
          </a>
          <form id="spa-logout-form" action="/logout" method="POST" class="hidden">
            <input type="hidden" name="_token" :value="csrfToken">
          </form>
        </div>
      </aside>

      <!-- 2. MAIN CONTENT AREA -->
      <main class="flex-1 flex flex-col min-w-0 bg-[#fbfcff] overflow-hidden">
        <!-- Top Navbar -->
        <header class="h-20 px-6 sm:px-8 flex items-center justify-between border-b border-slate-100 bg-white/80 backdrop-blur-md flex-shrink-0">
          <div class="flex items-center gap-3">
            <!-- Toggle Sidebar Button in Navbar -->
            <button 
              @click="sidebarOpen = !sidebarOpen" 
              class="text-slate-500 hover:text-slate-800 p-2 rounded-xl hover:bg-slate-100 transition flex items-center justify-center" 
              title="បង្រួម / ពង្រីកម៉ឺនុយ (Toggle Sidebar)"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>

            <!-- Search Bar (Attendance Reference Style) -->
            <div class="relative w-64 lg:w-80">
              <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </span>
              <input 
                type="text" 
                :placeholder="t('common.searchPlaceholder')" 
                class="w-full pl-10 pr-4 py-2 bg-[#f2f5fb] border-none rounded-xl text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition font-khmer"
              />
            </div>
          </div>

          <!-- Right: Fullscreen, Language Switcher, Profile & Badges -->
          <div class="flex items-center gap-3 sm:gap-4">
            <!-- Fullscreen Toggle Button -->
            <button 
              @click="toggleFullscreen" 
              class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 hover:text-blue-600 hover:bg-blue-50 flex items-center justify-center transition" 
              :title="isFullscreen ? 'ចាកចេញពីពេញអេក្រង់ (Exit Fullscreen)' : 'ពង្រីកពេញអេក្រង់ (Fullscreen)'"
            >
              <svg v-if="!isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4v4H5m10-4v4h4M9 20v-4H5m10 4v-4h4"/>
              </svg>
            </button>

            <!-- Language Switcher (Khmer & English) -->
            <div class="relative flex items-center bg-slate-100/80 p-1 rounded-xl text-xs font-semibold">
              <button 
                @click="switchLanguage('km')" 
                :class="[
                  'px-2.5 py-1 rounded-lg transition-all flex items-center gap-1.5',
                  currentLocale === 'km' 
                    ? 'bg-white text-blue-700 shadow-xs font-bold' 
                    : 'text-slate-500 hover:text-slate-800'
                ]"
                title="ប្តូរជាភាសាខ្មែរ"
              >
                <span>🇰🇭</span>
                <span>ខ្មែរ</span>
              </button>
              <button 
                @click="switchLanguage('en')" 
                :class="[
                  'px-2.5 py-1 rounded-lg transition-all flex items-center gap-1.5',
                  currentLocale === 'en' 
                    ? 'bg-white text-blue-700 shadow-xs font-bold' 
                    : 'text-slate-500 hover:text-slate-800'
                ]"
                title="Switch to English"
              >
                <span>🇬🇧</span>
                <span>EN</span>
              </button>
            </div>

            <!-- User Profile (Stevne Zone / Admin) -->
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-slate-200 ring-2 ring-blue-500/20 overflow-hidden flex-shrink-0">
                <img 
                  :src="user.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop&crop=faces'" 
                  alt="User Avatar" 
                  class="w-full h-full object-cover"
                />
              </div>
              <div class="text-right hidden sm:block">
                <h4 class="text-xs font-bold text-slate-900 leading-tight">{{ user.name || 'Stevne Zone' }}</h4>
                <p class="text-[10px] text-slate-400 leading-tight">{{ currentLocale === 'km' ? 'អ្នកគ្រប់គ្រង (Admin)' : 'Admin' }}</p>
              </div>
            </div>

            <!-- Cyan Message Badge -->
            <button class="relative w-8 h-8 rounded-full bg-[#e8f7fa] text-[#0891b2] flex items-center justify-center hover:bg-[#d6f0f5] transition" title="Messages">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-[#00bcd4] text-white text-[9px] font-bold flex items-center justify-center ring-2 ring-white">4</span>
            </button>

            <!-- Red Notification Bell Badge -->
            <button class="relative w-8 h-8 rounded-full bg-[#fef2f2] text-[#ef4444] flex items-center justify-center hover:bg-[#fee2e2] transition" title="Notifications">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
              </svg>
              <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-[#ef4444] text-white text-[9px] font-bold flex items-center justify-center ring-2 ring-white">8</span>
            </button>
          </div>
        </header>

        <!-- View Body Content (Scrollable Full Height) -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6">
          <slot />
        </div>
      </main>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import { setLocale } from '../i18n';
import { 
  LayoutDashboard, 
  Settings, 
  GraduationCap, 
  CalendarCheck, 
  FileText, 
  Bell, 
  BookOpen, 
  Package, 
  MessageSquare, 
  BookMarked, 
  BadgePercent, 
  ShieldCheck, 
  Building, 
  Receipt, 
  KeyRound,
  Users
} from 'lucide-vue-next';

const { t, locale } = useI18n();
const route = useRoute();
const sidebarOpen = ref(true);
const isFullscreen = ref(false);
const openDropdowns = ref(['attendance']);

// School Logo and Brand Header State
const schoolLogo = ref(localStorage.getItem('pdt_school_logo') || '');
const schoolName = ref(localStorage.getItem('pdt_school_name') || 'LMSTICK');
const schoolNameEn = ref(localStorage.getItem('pdt_school_name_en') || '');

const loadSchoolSettings = async () => {
  try {
    const res = await axios.get('/api/settings');
    if (res.data && res.data.settings) {
      const s = res.data.settings;
      if (s.school_logo_url) {
        schoolLogo.value = s.school_logo_url;
        localStorage.setItem('pdt_school_logo', s.school_logo_url);
      }
      if (s.school_name) {
        schoolName.value = s.school_name;
        localStorage.setItem('pdt_school_name', s.school_name);
      }
      if (s.school_name_en) {
        schoolNameEn.value = s.school_name_en;
        localStorage.setItem('pdt_school_name_en', s.school_name_en);
      }
    }
  } catch (err) {
    console.debug('School settings not loaded for sidebar header:', err);
  }
};

const toggleDropdown = (id) => {
  if (!sidebarOpen.value) {
    sidebarOpen.value = true;
    if (!openDropdowns.value.includes(id)) {
      openDropdowns.value.push(id);
    }
    return;
  }
  const idx = openDropdowns.value.indexOf(id);
  if (idx > -1) {
    openDropdowns.value.splice(idx, 1);
  } else {
    openDropdowns.value.push(id);
  }
};

const isChildActive = (item) => {
  if (!item.children) return false;
  return item.children.some(child => isActive(child.path));
};

watch(() => route.path, (newPath) => {
  if (newPath && (newPath.includes('attendance') || newPath.includes('teacher-attendance'))) {
    if (!openDropdowns.value.includes('attendance')) {
      openDropdowns.value.push('attendance');
    }
  }
}, { immediate: true });

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen().then(() => {
      isFullscreen.value = true;
    }).catch(err => {
      console.warn('Fullscreen request failed:', err);
    });
  } else {
    document.exitFullscreen().then(() => {
      isFullscreen.value = false;
    }).catch(err => {
      console.warn('Exit fullscreen failed:', err);
    });
  }
};

onMounted(() => {
  document.addEventListener('fullscreenchange', () => {
    isFullscreen.value = !!document.fullscreenElement;
  });

  loadSchoolSettings();

  // Listen for instant logo file selection from SettingView
  window.addEventListener('pdt-logo-preview', (e) => {
    if (e.detail?.logoUrl) {
      schoolLogo.value = e.detail.logoUrl;
    }
  });

  // Listen for saved school settings broadcast
  window.addEventListener('pdt-settings-saved', (e) => {
    if (e.detail?.school_logo_url) {
      schoolLogo.value = e.detail.school_logo_url;
      localStorage.setItem('pdt_school_logo', e.detail.school_logo_url);
    }
    if (e.detail?.school_name) {
      schoolName.value = e.detail.school_name;
      localStorage.setItem('pdt_school_name', e.detail.school_name);
    }
    if (e.detail?.school_name_en) {
      schoolNameEn.value = e.detail.school_name_en;
      localStorage.setItem('pdt_school_name_en', e.detail.school_name_en);
    }
  });
});

const currentLocale = computed(() => locale.value);

const switchLanguage = (lang) => {
  setLocale(lang);
};

const user = ref({
  name: window.__INITIAL_USER__?.name || 'Stevne Zone',
  role: window.__INITIAL_USER__?.role || 'Admin',
  avatar: window.__INITIAL_USER__?.avatar || ''
});

const csrfToken = computed(() => {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
});

// Menu items with i18n keys
const menuItems = [
  { i18nKey: 'nav.dashboard', path: '/dashboard', icon: LayoutDashboard },
  { 
    id: 'attendance',
    i18nKey: 'nav.attendance', 
    icon: CalendarCheck,
    children: [
      { 
        nameKm: 'វត្តមានសិស្ស', 
        nameEn: 'Student Attendance', 
        path: '/attendance', 
        icon: GraduationCap 
      },
      { 
        nameKm: 'វត្តមានគ្រូបង្រៀន', 
        nameEn: 'Teacher Attendance', 
        path: '/teacher-attendance', 
        icon: Users 
      },
    ]
  },
  { i18nKey: 'nav.hostel', path: '/students', icon: GraduationCap },
  { i18nKey: 'nav.teachers', path: '/teachers', icon: Users },
  { i18nKey: 'nav.academics', path: '/classes', icon: Building },
  { i18nKey: 'nav.academicYears', path: '/academic-years', icon: CalendarCheck },
  { i18nKey: 'nav.inventory', path: '/course-subjects', icon: Package },
  { i18nKey: 'nav.homework', path: '/timetables', icon: BookMarked },
  { i18nKey: 'nav.exam', path: '/examinations', icon: FileText },
  { i18nKey: 'nav.messaging', path: '/parents', icon: MessageSquare },
  { i18nKey: 'nav.feesCollection', path: '/finance', icon: BadgePercent },
  { i18nKey: 'nav.library', path: '/library', icon: BookOpen },
  { i18nKey: 'nav.notice', path: '/announcements', icon: Bell },
  { i18nKey: 'nav.reports', path: '/reports', icon: FileText },
  { i18nKey: 'nav.rolesPermissions', path: '/roles', icon: ShieldCheck },
  { i18nKey: 'nav.websiteSetting', path: '/settings', icon: Settings },
];

const isActive = (path) => {
  if (!path) return false;
  if (path === '/attendance') {
    return route.path === '/attendance' || route.path === '/attendance/';
  }
  if (path === '/teacher-attendance') {
    return route.path.startsWith('/teacher-attendance') || route.path.startsWith('/attendance/teachers');
  }
  if (path === '/dashboard' && (route.path === '/dashboard' || route.path === '/')) return true;
  return route.path === path || (path !== '/dashboard' && route.path.startsWith(path));
};
</script>

<style>
.font-khmer {
  font-family: 'Kantumruy Pro', 'Plus Jakarta Sans', sans-serif;
}
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
</style>
