<template>
  <div class="h-screen w-screen bg-[#f8fafc] flex font-khmer antialiased text-slate-800 overflow-hidden relative">
    <!-- Mobile Backdrop Overlay when sidebar is expanded -->
    <div 
      v-if="sidebarOpen" 
      @click="sidebarOpen = false" 
      class="fixed inset-0 bg-slate-900/40 backdrop-blur-2xs z-40 md:hidden transition-opacity"
    ></div>

    <!-- Master App Canvas Container (100% Full Screen Edge-to-Edge) -->
    <div class="w-full h-full bg-white flex overflow-hidden">
      
      <!-- 1. LEFT SIDEBAR (Shows Icon Rail when collapsed, Full Sidebar when open) -->
      <aside 
        :class="[
          'bg-white border-r border-slate-100 flex flex-col justify-between transition-all duration-300 flex-shrink-0',
          sidebarOpen 
            ? 'fixed md:static inset-y-0 left-0 w-72 shadow-2xl md:shadow-none z-50 md:z-30 translate-x-0' 
            : 'fixed md:static inset-y-0 left-0 -translate-x-full md:translate-x-0 w-16 lg:w-20 z-50 md:z-30'
        ]"
      >
        <!-- Logo Header -->
        <div class="h-16 sm:h-20 px-2 sm:px-4 md:px-5 flex items-center justify-between border-b border-slate-50">
          <router-link to="/attendance" @click="handleNavClick" class="flex items-center gap-3 group min-w-0" :class="{ 'mx-auto': !sidebarOpen }">
            <!-- School Brand Logo -->
            <div 
              class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl overflow-hidden border border-slate-100 bg-white flex items-center justify-center p-1 shadow-xs group-hover:scale-105 transition-transform flex-shrink-0"
            >
              <img :src="schoolLogo || '/images/logo.png'" alt="School Logo" class="w-full h-full object-contain" @error="schoolLogo = '/images/logo.png'" />
            </div>

            <div v-if="sidebarOpen" class="flex flex-col truncate">
              <span class="text-base font-extrabold tracking-tight text-[#1e293b] leading-tight truncate">
                {{ currentLocale === 'en' ? (schoolNameEn || 'New Generation School PDT') : (schoolName || 'សាលារៀន ជំនាន់ថ្មី') }}
              </span>
              <span class="text-[10px] text-slate-400 font-medium truncate leading-none mt-0.5">
                {{ schoolNameEn || 'New Generation School' }}
              </span>
            </div>
          </router-link>

          <!-- Close button on Mobile when Sidebar is open -->
          <button 
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="md:hidden w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition flex-shrink-0 ml-2 cursor-pointer"
            title="បិទម៉ឺនុយ"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>

          <!-- Collapse button on Desktop when Sidebar is open -->
          <button 
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="hidden md:flex w-7 h-7 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-700 items-center justify-center transition flex-shrink-0 ml-1 cursor-pointer"
            title="បង្រួមម៉ឺនុយ"
          >
            <i class="fa-solid fa-chevron-left text-xs"></i>
          </button>
        </div>

        <!-- Navigation Menu (Always shows icons) -->
        <div class="flex-1 overflow-y-auto px-1.5 sm:px-3 py-4 space-y-1 custom-scrollbar">
          <template v-for="item in visibleMenuItems" :key="item.path || item.id">
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
                :title="!sidebarOpen ? getMenuLabel(item) : ''"
              >
                <div class="flex items-center gap-3 truncate">
                  <component 
                    :is="item.icon" 
                    :class="[
                      'w-5 h-5 flex-shrink-0 transition-colors',
                      isChildActive(item) ? 'text-[#1e40af]' : 'text-slate-400 group-hover:text-slate-600'
                    ]"
                  />
                  <span v-if="sidebarOpen" class="truncate">{{ getMenuLabel(item) }}</span>
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
                  @click="handleNavClick"
                  :class="[
                    'flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all',
                    isActive(sub.path)
                      ? 'bg-blue-50 text-blue-700 font-semibold shadow-xs'
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
              @click="handleNavClick"
              :class="[
                'flex items-center rounded-xl text-[13px] font-medium transition-all group',
                sidebarOpen ? 'justify-between px-3.5 py-2.5' : 'justify-center p-2.5 relative',
                isActive(item.path) 
                  ? 'bg-[#e5ecfc] text-[#1e40af] font-semibold shadow-xs' 
                  : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50'
              ]"
              :title="!sidebarOpen ? getMenuLabel(item) : ''"
            >
              <div class="flex items-center gap-3 truncate">
                <component 
                  :is="item.icon" 
                  :class="[
                    'w-5 h-5 flex-shrink-0 transition-colors',
                    isActive(item.path) ? 'text-[#1e40af]' : 'text-slate-400 group-hover:text-slate-600'
                  ]"
                />
                <span v-if="sidebarOpen" class="truncate">{{ getMenuLabel(item) }}</span>
              </div>
              <!-- Announcement Badge on 'ដំណឹង និងសេចក្តីជូនដំណឹង' -->
              <span 
                v-if="sidebarOpen && item.path === '/announcements' && unreadAnnouncementCount > 0" 
                class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white shadow-2xs shrink-0"
              >
                {{ unreadAnnouncementCount }}
              </span>
              <span 
                v-else-if="!sidebarOpen && item.path === '/announcements' && unreadAnnouncementCount > 0" 
                class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"
              ></span>
            </router-link>

            <!-- Subtle horizontal divider below "សំណើ និងមតិយោបល់" -->
            <div 
              v-if="item.path === '/requests' || item.dividerAfter" 
              class="my-2 border-t border-slate-200/80 mx-1"
            ></div>
          </template>
        </div>

        <!-- Bottom Logout Button -->
        <div class="p-3 border-t border-slate-50">
          <button 
            type="button"
            @click="promptLogout"
            :class="[
              'w-full flex items-center rounded-xl text-[13px] font-medium text-slate-600 hover:bg-red-50 hover:text-red-600 active:bg-red-100 transition-colors group cursor-pointer',
              sidebarOpen ? 'justify-start px-3.5 py-2.5 gap-2.5' : 'justify-center p-2.5'
            ]"
            :title="!sidebarOpen ? t('common.logout') : ''"
          >
            <div class="flex items-center gap-2.5">
              <svg class="w-4 h-4 flex-shrink-0 text-slate-400 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              <span v-if="sidebarOpen">{{ t('common.logout') }}</span>
            </div>
          </button>
          <form id="spa-logout-form" action="/logout" method="POST" class="hidden">
            <input type="hidden" name="_token" :value="csrfToken">
          </form>
        </div>
      </aside>

      <!-- 2. MAIN CONTENT AREA -->
      <main class="flex-1 flex flex-col min-w-0 bg-[#fbfcff] overflow-hidden">
        <!-- Top Navbar: Clean Frosted Glass Style -->
        <header class="relative z-40 h-16 px-4 py-3 sm:px-6 md:px-8 flex items-center justify-between border-b border-slate-100 bg-white/90 backdrop-blur-md flex-shrink-0 transition-all shadow-2xs">
          <!-- Left Section: Menu Toggle, Brand / School Logo -->
          <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
            <!-- Sidebar / Menu Toggle Button (Desktop & Laptop only, hidden on mobile) -->
            <button 
              type="button" 
              @click="sidebarOpen = !sidebarOpen" 
              class="hidden md:flex w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 items-center justify-center transition shadow-2xs cursor-pointer shrink-0"
              :title="sidebarOpen ? 'បង្រួមម៉ឺនុយ' : 'ពង្រីកម៉ឺនុយ (Menu)'"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>

            <!-- Brand Title with School Logo (Visible on all screens) -->
            <div class="flex items-center gap-2.5 min-w-0">
              <div class="w-8 h-8 rounded-xl overflow-hidden border border-slate-100 bg-white p-0.5 shadow-2xs flex-shrink-0 flex items-center justify-center">
                <img :src="schoolLogo || '/images/logo.png'" alt="Logo" class="w-full h-full object-contain" />
              </div>
              <span class="text-xs sm:text-sm font-bold text-slate-800 truncate max-w-[200px] sm:max-w-none leading-tight font-khmer">
                {{ currentLocale === 'en' ? (schoolNameEn || 'New Generation School PDT') : (schoolName || 'សាលារៀន ជំនាន់ថ្មី PDT') }}
              </span>
            </div>
          </div>

          <!-- Right: Cleanly Aligned Actions Group (Only Notification Bell) -->
          <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <!-- Notification Bell Button with subtle unread red dot -->
            <div class="relative z-50" ref="notificationDropdownRef">
              <button 
                type="button"
                @click.stop="toggleNotificationDropdown"
                class="relative w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition shadow-2xs cursor-pointer"
                title="ការជូនដំណឹង"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span 
                  v-if="unreadCount > 0" 
                  class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-black flex items-center justify-center ring-2 ring-white shadow-xs"
                >
                  {{ unreadCount > 99 ? '99+' : unreadCount }}
                </span>
              </button>

              <!-- Notifications Dropdown Menu -->
              <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 translate-y-1 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0 scale-100"
                leave-to-class="opacity-0 translate-y-1 scale-95"
              >
                <div 
                  v-if="showNotificationDropdown" 
                  class="absolute right-0 top-11 sm:top-12 z-[999] w-80 sm:w-96 rounded-2xl bg-white shadow-2xl border border-slate-200/90 overflow-hidden font-khmer animate-in fade-in"
                  @click.stop
                >
                  <!-- Header -->
                  <div class="px-4 py-3 bg-gradient-to-r from-slate-50 to-rose-50/40 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-bell"></i>
                      </div>
                      <div>
                        <h4 class="text-xs font-bold text-slate-800 leading-tight">ការជូនដំណឹង</h4>
                        <p class="text-[10px] text-slate-400">
                          {{ isStudentUser ? 'ដំណឹង និងស្ថានភាពសុំច្បាប់' : 'សំណើរង់ចាំការអនុម័ត និងដំណឹង' }}
                        </p>
                      </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                      <button 
                        v-if="unreadCount > 0"
                        type="button" 
                        @click.stop="markAllAsRead" 
                        class="text-[10px] text-blue-600 hover:text-blue-800 font-medium hover:underline mr-1 cursor-pointer"
                        title="កំណត់ថាមើលរួចទាំងអស់"
                      >
                        មើលរួចទាំងអស់
                      </button>
                      <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="unreadCount > 0 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-500'">
                        {{ unreadCount > 0 ? unreadCount + ' ថ្មី' : pendingTotalCount + ' ដំណឹង' }}
                      </span>
                      <button 
                        type="button" 
                        @click="fetchPendingNotifications" 
                        class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition text-[11px] cursor-pointer" 
                        title="ផ្ទុកឡើងវិញ"
                      >
                        <i class="fa-solid fa-arrows-rotate" :class="{ 'fa-spin': isLoadingNotifications }"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Filter Pills: ទាំងអស់ | សេចក្ដីជូនដំណឹង | សិស្សថ្មី | គ្រូថ្មី | សុំច្បាប់ -->
                  <div class="p-2 border-b border-slate-100 bg-slate-50/50 flex items-center gap-1 overflow-x-auto text-[11px]">
                    <button
                      type="button"
                      @click="notificationFilter = 'all'"
                      :class="[
                        'px-2.5 py-1 rounded-lg transition font-medium whitespace-nowrap cursor-pointer',
                        notificationFilter === 'all' ? 'bg-white text-blue-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'
                      ]"
                    >
                      ទាំងអស់ ({{ pendingTotalCount }})
                    </button>
                    <button
                      type="button"
                      @click="notificationFilter = 'announcement'"
                      :class="[
                        'px-2.5 py-1 rounded-lg transition font-medium whitespace-nowrap cursor-pointer',
                        notificationFilter === 'announcement' ? 'bg-white text-indigo-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'
                      ]"
                    >
                      សេចក្ដីជូនដំណឹង ({{ pendingCounts.announcements || 0 }})
                    </button>
                    <button
                      v-if="!isStudentUser"
                      type="button"
                      @click="notificationFilter = 'student'"
                      :class="[
                        'px-2.5 py-1 rounded-lg transition font-medium whitespace-nowrap cursor-pointer',
                        notificationFilter === 'student' ? 'bg-white text-emerald-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'
                      ]"
                    >
                      សិស្សថ្មី ({{ pendingCounts.students || 0 }})
                    </button>
                    <button
                      v-if="!isStudentUser"
                      type="button"
                      @click="notificationFilter = 'teacher'"
                      :class="[
                        'px-2.5 py-1 rounded-lg transition font-medium whitespace-nowrap cursor-pointer',
                        notificationFilter === 'teacher' ? 'bg-white text-blue-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'
                      ]"
                    >
                      គ្រូថ្មី ({{ pendingCounts.teachers || 0 }})
                    </button>
                    <button
                      type="button"
                      @click="notificationFilter = 'leave'"
                      :class="[
                        'px-2.5 py-1 rounded-lg transition font-medium whitespace-nowrap cursor-pointer',
                        notificationFilter === 'leave' ? 'bg-white text-amber-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'
                      ]"
                    >
                      សុំច្បាប់ ({{ pendingCounts.leaves || 0 }})
                    </button>
                    <button
                      type="button"
                      @click="notificationFilter = 'book_request'"
                      :class="[
                        'px-2.5 py-1 rounded-lg transition font-medium whitespace-nowrap cursor-pointer',
                        notificationFilter === 'book_request' ? 'bg-white text-indigo-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'
                      ]"
                    >
                      សំណើ/មតិ ({{ pendingCounts.book_requests || 0 }})
                    </button>
                  </div>

                  <!-- Notifications Items List -->
                  <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
                    <div v-if="isLoadingNotifications && pendingNotificationItems.length === 0" class="py-8 text-center text-xs text-slate-400">
                      <i class="fa-solid fa-circle-notch fa-spin text-base text-blue-500 mb-2"></i>
                      <p>កំពុងផ្ទុកការជូនដំណឹង...</p>
                    </div>

                    <div v-else-if="filteredNotificationItems.length === 0" class="py-8 px-4 text-center space-y-2">
                      <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto text-base">
                        <i class="fa-solid fa-check"></i>
                      </div>
                      <p class="text-xs font-bold text-slate-700">
                        {{ isStudentUser ? 'ពុំមានការជូនដំណឹងថ្មីឡើយ' : 'ពុំមានសំណើរង់ចាំការអនុម័តឡើយ' }}
                      </p>
                      <p class="text-[10px] text-slate-400">
                        {{ isStudentUser ? 'រាល់ដំណឹង និងការអនុម័តច្បាប់នឹងបង្ហាញនៅទីនេះ' : 'រាល់ការចុះឈ្មោះ និងសំណើសុំច្បាប់ត្រូវបានអនុម័តរួចរាល់' }}
                      </p>
                    </div>

                    <div 
                      v-else
                      v-for="item in filteredNotificationItems" 
                      :key="item.id"
                      class="border-b border-slate-50 last:border-0 transition"
                      :class="{ 'bg-slate-50/60': expandedNotificationId === item.id }"
                    >
                      <!-- Main Clickable Header Row (Click to Expand / Read) -->
                      <div 
                        @click="toggleNotificationExpand(item)"
                        class="p-3 hover:bg-slate-50/80 transition flex items-start gap-2.5 cursor-pointer group relative select-none"
                      >
                        <!-- Type Icon Badge -->
                        <div 
                          class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-xs border shadow-xs"
                          :class="item.bg_color"
                        >
                          <i :class="item.icon"></i>
                        </div>

                        <!-- Details Summary -->
                        <div class="flex-1 min-w-0 pr-3">
                          <div class="flex items-center justify-between gap-1 mb-0.5">
                            <span 
                              class="text-[9px] font-bold px-1.5 py-0.2 rounded"
                              :class="[
                                item.type === 'announcement' ? 'bg-indigo-100 text-indigo-800' :
                                (item.leave_status === 'approved' ? 'bg-emerald-100 text-emerald-800' :
                                (item.leave_status === 'rejected' ? 'bg-rose-100 text-rose-800' :
                                (item.type === 'student' ? 'bg-emerald-100 text-emerald-800' :
                                (item.type === 'teacher' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800'))))
                              ]"
                            >
                              {{ item.type_label }}
                            </span>
                            <span class="text-[10px] text-slate-400 shrink-0">{{ item.time }}</span>
                          </div>
                          <h5 class="text-xs font-bold text-slate-800 group-hover:text-blue-600 transition leading-snug truncate">
                            {{ item.title }}
                          </h5>
                          <p class="text-[11px] text-slate-500 truncate mt-0.5" v-if="expandedNotificationId !== item.id">
                            {{ item.subtitle }}
                          </p>
                        </div>

                        <!-- Unread Indicator Dot -->
                        <span 
                          v-if="!readNotificationIds.includes(item.id)" 
                          class="absolute top-3.5 right-7 w-2 h-2 rounded-full bg-blue-500 ring-2 ring-white"
                          title="មិនទាន់មើល"
                        ></span>

                        <!-- Rotating Dropdown / Chevron Indicator -->
                        <div class="shrink-0 text-slate-300 group-hover:text-blue-500 transition text-xs mt-2">
                          <i 
                            class="fa-solid fa-chevron-right transition-transform duration-200"
                            :class="{ 'rotate-90 text-blue-600 font-bold': expandedNotificationId === item.id }"
                          ></i>
                        </div>
                      </div>

                      <!-- Expandable Accordion Body (Read Notification Content Directly) -->
                      <transition
                        enter-active-class="transition-all duration-200 ease-out overflow-hidden"
                        enter-from-class="opacity-0 max-h-0"
                        enter-to-class="opacity-100 max-h-96"
                        leave-active-class="transition-all duration-150 ease-in overflow-hidden"
                        leave-from-class="opacity-100 max-h-96"
                        leave-to-class="opacity-0 max-h-0"
                      >
                        <div 
                          v-if="expandedNotificationId === item.id"
                          class="px-3.5 pb-3.5 pt-0.5"
                        >
                          <div class="p-3 rounded-xl bg-white border border-slate-200/90 shadow-2xs space-y-2.5 text-xs text-slate-700">
                            <!-- 1. Announcement Content -->
                            <template v-if="item.type === 'announcement'">
                              <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-[10px] text-slate-400">
                                  <span class="font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full">
                                    <i class="fa-solid fa-bullhorn mr-1 text-[9px]"></i>
                                    {{ item.target_audience ? 'ជូនចំពោះ៖ ' + item.target_audience : 'ជូនចំពោះ៖ ទាំងអស់គ្នា' }}
                                  </span>
                                  <span>{{ item.time }}</span>
                                </div>
                                <div class="text-[12px] text-slate-800 leading-relaxed font-normal whitespace-pre-line bg-slate-50/70 p-2.5 rounded-lg border border-slate-100 select-text">
                                  {{ item.full_content || item.subtitle || item.title }}
                                </div>
                              </div>
                              <div class="flex items-center justify-end pt-1">
                                <button 
                                  type="button" 
                                  @click.stop="goToNotificationLink(item.link, item.id)"
                                  class="text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1 cursor-pointer"
                                >
                                  <span>បើកមើលទំព័រពេញលេញ</span>
                                  <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                </button>
                              </div>
                            </template>

                            <!-- 2. Leave Request / Status Content -->
                            <template v-else-if="item.type === 'leave'">
                              <div class="space-y-2 text-[11px]">
                                <div v-if="item.dates_formatted" class="flex items-center justify-between py-0.5 border-b border-slate-100">
                                  <span class="text-slate-400">កាលបរិច្ឆេទសុំច្បាប់៖</span>
                                  <span class="font-bold text-slate-800">{{ item.dates_formatted }} ({{ item.days_text || (item.days_count ? item.days_count + ' ថ្ងៃ' : '1 ថ្ងៃ') }})</span>
                                </div>
                                <div v-if="item.class_name" class="flex items-center justify-between py-0.5 border-b border-slate-100">
                                  <span class="text-slate-400">ថ្នាក់រៀន៖</span>
                                  <span class="font-bold text-indigo-600">ថ្នាក់ {{ item.class_name }}</span>
                                </div>
                                <div v-if="item.reason" class="pt-0.5">
                                  <span class="text-slate-400 block text-[10px] mb-1">មូលហេតុសុំច្បាប់៖</span>
                                  <div class="p-2 bg-slate-50 rounded-lg border border-slate-100 text-slate-700 leading-relaxed select-text">
                                    {{ item.reason }}
                                  </div>
                                </div>
                                <div v-if="item.rejection_reason" class="pt-0.5">
                                  <span class="text-rose-600 font-bold block text-[10px] mb-1">មូលហេតុនៃការបដិសេធ៖</span>
                                  <div class="p-2 bg-rose-50 rounded-lg border border-rose-100 text-rose-800 leading-relaxed select-text">
                                    {{ item.rejection_reason }}
                                  </div>
                                </div>
                              </div>
                              <div class="flex items-center justify-end pt-1">
                                <button 
                                  type="button" 
                                  @click.stop="goToNotificationLink(item.link, item.id)"
                                  class="text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1 cursor-pointer"
                                >
                                  <span>{{ isStudentUser ? 'មើលប្រវត្តិវត្តមាន និងច្បាប់' : 'ពិនិត្យ និងអនុម័តសំណើ' }}</span>
                                  <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                </button>
                              </div>
                            </template>

                            <!-- 3. Student / Teacher Registration Content -->
                            <template v-else>
                              <div class="text-[11px] text-slate-700 bg-slate-50 p-2.5 rounded-lg border border-slate-100 leading-relaxed select-text">
                                {{ item.subtitle }}
                              </div>
                              <div class="flex items-center justify-end pt-1">
                                <button 
                                  type="button" 
                                  @click.stop="goToNotificationLink(item.link, item.id)"
                                  class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[11px] font-bold shadow-xs inline-flex items-center gap-1 cursor-pointer transition"
                                >
                                  <span>ពិនិត្យសំណើនេះ</span>
                                  <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                </button>
                              </div>
                            </template>
                          </div>
                        </div>
                      </transition>
                    </div>
                  </div>

                  <!-- Footer Shortcuts -->
                  <div class="p-2.5 bg-slate-50/90 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-medium">
                    <template v-if="isStudentUser">
                      <button 
                        type="button" 
                        @click="goToNotificationLink('/announcements')"
                        class="hover:text-blue-700 hover:underline cursor-pointer"
                      >
                        សេចក្ដីជូនដំណឹង
                      </button>
                      <span>•</span>
                      <button 
                        type="button" 
                        @click="goToNotificationLink('/attendance')"
                        class="hover:text-blue-700 hover:underline cursor-pointer"
                      >
                        វត្តមានរបស់ខ្ញុំ
                      </button>
                      <span>•</span>
                      <button 
                        type="button" 
                        @click="goToNotificationLink('/attendance?tab=leaves')"
                        class="hover:text-blue-700 hover:underline cursor-pointer"
                      >
                        សំណើសុំច្បាប់
                      </button>
                    </template>
                    <template v-else>
                      <button 
                        type="button" 
                        @click="goToNotificationLink('/announcements')"
                        class="hover:text-blue-700 hover:underline cursor-pointer"
                      >
                        សេចក្ដីជូនដំណឹង
                      </button>
                      <span>•</span>
                      <button 
                        type="button" 
                        @click="goToNotificationLink('/students?tab=pending')"
                        class="hover:text-blue-700 hover:underline cursor-pointer"
                      >
                        សិស្សរង់ចាំ
                      </button>
                      <span>•</span>
                      <button 
                        type="button" 
                        @click="goToNotificationLink('/teachers?tab=pending')"
                        class="hover:text-blue-700 hover:underline cursor-pointer"
                      >
                        គ្រូរង់ចាំ
                      </button>
                      <span>•</span>
                      <button 
                        type="button" 
                        @click="goToNotificationLink('/attendance?tab=leaves')"
                        class="hover:text-blue-700 hover:underline cursor-pointer"
                      >
                        សុំច្បាប់រង់ចាំ
                      </button>
                    </template>
                  </div>
                </div>
              </transition>
            </div>
          </div>
        </header>

        <!-- View Body Content (Scrollable Full Height with bottom padding for mobile bar) -->
        <div class="flex-1 overflow-y-auto p-3 sm:p-6 lg:p-8 pb-24 md:pb-8 space-y-4 sm:space-y-6">
          <slot />
        </div>
      </main>

    </div>

    <!-- 3. MOBILE BOTTOM NAVIGATION BAR (Sticky thumb-friendly navigation on phones) -->
    <nav 
      class="md:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200/90 px-3 py-1.5 z-40 flex items-center justify-around shadow-[0_-4px_25px_rgba(0,0,0,0.06)]"
      style="padding-bottom: max(0.375rem, env(safe-area-inset-bottom));"
      aria-label="Mobile Bottom Navigation"
    >
      <!-- 1. Dashboard -->
      <router-link 
        to="/dashboard" 
        @click="sidebarOpen = false"
        :class="[
          'flex flex-col items-center justify-center py-1 px-2 rounded-xl transition text-[10px] font-medium min-w-[56px]',
          isActive('/dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800'
        ]"
      >
        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>{{ currentLocale === 'km' ? 'ផ្ទាំងដើម' : 'Home' }}</span>
      </router-link>

      <!-- 2. Attendance (Clipboard-Check Icon) -->
      <router-link 
        to="/attendance" 
        @click="sidebarOpen = false"
        :class="[
          'flex flex-col items-center justify-center py-1 px-2 rounded-xl transition text-[10px] font-medium min-w-[56px]',
          isActive('/attendance') || isActive('/teacher-attendance') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800'
        ]"
      >
        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        <span>{{ currentLocale === 'km' ? 'វត្តមាន' : 'Attendance' }}</span>
      </router-link>

      <!-- 3. Timetables (for Students: Calendar-Clock Icon) OR Students (for Admin / Teacher) -->
      <router-link 
        v-if="isStudentUser"
        to="/timetables" 
        @click="sidebarOpen = false"
        :class="[
          'flex flex-col items-center justify-center py-1 px-2 rounded-xl transition text-[10px] font-medium min-w-[56px]',
          isActive('/timetables') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800'
        ]"
      >
        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ currentLocale === 'km' ? 'កាលវិភាគ' : 'Timetable' }}</span>
      </router-link>

      <router-link 
        v-else
        to="/students" 
        @click="sidebarOpen = false"
        :class="[
          'flex flex-col items-center justify-center py-1 px-2 rounded-xl transition text-[10px] font-medium min-w-[56px]',
          isActive('/students') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800'
        ]"
      >
        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
        </svg>
        <span>{{ currentLocale === 'km' ? 'សិស្ស' : 'Students' }}</span>
      </router-link>

      <!-- 4. Teachers (for Admin / Teacher) OR Library (for Students) -->
      <router-link 
        v-if="isStudentUser"
        to="/library" 
        @click="sidebarOpen = false"
        :class="[
          'flex flex-col items-center justify-center py-1 px-2 rounded-xl transition text-[10px] font-medium min-w-[56px]',
          isActive('/library') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800'
        ]"
      >
        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <span>{{ currentLocale === 'km' ? 'បណ្ណាល័យ' : 'Library' }}</span>
      </router-link>

      <router-link 
        v-else
        to="/teachers" 
        @click="sidebarOpen = false"
        :class="[
          'flex flex-col items-center justify-center py-1 px-2 rounded-xl transition text-[10px] font-medium min-w-[56px]',
          isActive('/teachers') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800'
        ]"
      >
        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span>{{ currentLocale === 'km' ? 'គ្រូ' : 'Teachers' }}</span>
      </router-link>

      <!-- 5. All Menu (Opens full drawer) -->
      <button 
        type="button" 
        @click="sidebarOpen = !sidebarOpen" 
        :class="[
          'flex flex-col items-center justify-center py-1 px-2 rounded-xl transition text-[10px] font-medium min-w-[56px] cursor-pointer',
          sidebarOpen ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800'
        ]"
      >
        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <span>{{ currentLocale === 'km' ? 'មុខងារ' : 'Modules' }}</span>
      </button>
    </nav>

    <!-- Logout Confirmation Modal (Teleported to body for guaranteed top-level z-index on mobile & desktop) -->
    <Teleport to="body">
      <div 
        v-if="showLogoutModal" 
        class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs font-khmer"
        @click.self="showLogoutModal = false"
      >
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-sm w-full p-6 text-center space-y-5 animate-in fade-in zoom-in-95 duration-200">
          <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl shadow-sm border border-rose-100">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
          </div>

          <div class="space-y-1.5">
            <h3 class="text-base font-bold text-slate-900">បញ្ជាក់ការចាកចេញ</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
              តើលោកអ្នកពិតជាចង់ចាកចេញពីប្រព័ន្ធគ្រប់គ្រងសាលារៀនមែនទេ?
            </p>
          </div>

          <div class="grid grid-cols-2 gap-3 pt-2">
            <button
              type="button"
              @click="showLogoutModal = false"
              :disabled="isLoggingOut"
              class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer"
            >
              បោះបង់
            </button>
            <button
              type="button"
              @click="executeLogout"
              :disabled="isLoggingOut"
              class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-md shadow-rose-600/20 cursor-pointer disabled:opacity-50"
            >
              <i v-if="isLoggingOut" class="fa-solid fa-circle-notch fa-spin"></i>
              <span>{{ isLoggingOut ? 'កំពុងចាកចេញ...' : 'ចាកចេញ' }}</span>
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import { setLocale } from '../i18n';
import { 
  LayoutDashboard, 
  Settings, 
  SlidersHorizontal,
  GraduationCap, 
  CalendarCheck, 
  FileText, 
  Bell, 
  BookOpen, 
  BookmarkPlus,
  Package, 
  MessageSquare, 
  BookMarked, 
  BadgePercent, 
  ShieldCheck, 
  ShieldAlert,
  Building, 
  Receipt, 
  KeyRound,
  Users,
  User,
  UserRound,
  LockKeyhole,
  Languages,
  ChevronRight
} from 'lucide-vue-next';
import { updateFavicon } from '../utils/favicon';

const { t, locale } = useI18n();
const route = useRoute();
const router = useRouter();
const sidebarOpen = ref(true);
const isFullscreen = ref(false);
const openDropdowns = ref(['attendance', 'account_settings']);

// Notification System (Pending Students, Teachers, and Leave Requests)
const showNotificationDropdown = ref(false);
const notificationDropdownRef = ref(null);
const isLoadingNotifications = ref(false);
const notificationFilter = ref('all');
const pendingTotalCount = ref(0);
const pendingCounts = ref({ announcements: 0, students: 0, teachers: 0, leaves: 0, book_requests: 0 });
const pendingNotificationItems = ref([]);
const getStorageUserKey = () => {
  return window.__INITIAL_USER__?.id || window.__INITIAL_USER__?.name || 'user';
};

const getInitialReadIds = () => {
  try {
    const raw = localStorage.getItem(`pdt_read_notifs_${getStorageUserKey()}`);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
};

const readNotificationIds = ref(getInitialReadIds());
const expandedNotificationId = ref(null);

const toggleNotificationExpand = (item) => {
  if (expandedNotificationId.value === item.id) {
    expandedNotificationId.value = null;
  } else {
    expandedNotificationId.value = item.id;
    markItemAsRead(item.id);
  }
};

const loadReadNotificationIds = () => {
  readNotificationIds.value = getInitialReadIds();
};

const markAllAsRead = () => {
  if (pendingNotificationItems.value.length === 0) return;
  const ids = pendingNotificationItems.value.map(i => i.id);
  const combined = Array.from(new Set([...readNotificationIds.value, ...ids]));
  readNotificationIds.value = combined;
  try {
    localStorage.setItem(`pdt_read_notifs_${getStorageUserKey()}`, JSON.stringify(combined));
  } catch (e) {
    console.warn('LocalStorage error:', e);
  }
};

const markItemAsRead = (id) => {
  if (!id) return;
  if (!readNotificationIds.value.includes(id)) {
    const combined = [...readNotificationIds.value, id];
    readNotificationIds.value = combined;
    try {
      localStorage.setItem(`pdt_read_notifs_${getStorageUserKey()}`, JSON.stringify(combined));
    } catch (e) {
      console.warn('LocalStorage error:', e);
    }
  }
};

const unreadCount = computed(() => {
  return pendingNotificationItems.value.filter(item => !readNotificationIds.value.includes(item.id)).length;
});

const unreadAnnouncementCount = computed(() => {
  return pendingNotificationItems.value.filter(
    item => item.type === 'announcement' && !readNotificationIds.value.includes(item.id)
  ).length;
});

// Sync PWA App Badge on mobile home screen / desktop taskbar
watch(unreadCount, (count) => {
  if (typeof navigator !== 'undefined' && 'setAppBadge' in navigator) {
    if (count > 0) {
      navigator.setAppBadge(count).catch(() => {});
    } else {
      navigator.clearAppBadge().catch(() => {});
    }
  }
}, { immediate: true });

const filteredNotificationItems = computed(() => {
  if (notificationFilter.value === 'all') return pendingNotificationItems.value;
  return pendingNotificationItems.value.filter(n => n.type === notificationFilter.value);
});

const toggleNotificationDropdown = async () => {
  showNotificationDropdown.value = !showNotificationDropdown.value;
  if (showNotificationDropdown.value) {
    markAllAsRead();
    await fetchPendingNotifications();
    markAllAsRead();
  }
};

const goToNotificationLink = (link, itemId = null) => {
  if (itemId) {
    markItemAsRead(itemId);
  }
  markAllAsRead();
  showNotificationDropdown.value = false;
  router.push(link);
  window.dispatchEvent(new CustomEvent('pdt-data-updated'));
};

watch(() => route.path, (newPath) => {
  if (newPath === '/announcements') {
    const annIds = pendingNotificationItems.value
      .filter(item => item.type === 'announcement')
      .map(item => item.id);
    if (annIds.length > 0) {
      const combined = Array.from(new Set([...readNotificationIds.value, ...annIds]));
      readNotificationIds.value = combined;
      try {
        localStorage.setItem(`pdt_read_notifs_${getStorageUserKey()}`, JSON.stringify(combined));
      } catch (e) {}
    }
  }
});

let isFetchingPendingNotifs = false;
let lastPendingCount = -1;

const fetchPendingNotifications = async (force = false) => {
  // Sleep / Pause polling if tab is in the background or screen is locked (Saves 80-90% server resources)
  if (!force && typeof document !== 'undefined' && document.visibilityState !== 'visible') {
    return;
  }
  if (isFetchingPendingNotifs) return;
  isFetchingPendingNotifs = true;
  isLoadingNotifications.value = true;
  try {
    const res = await axios.get('/api/notifications/pending');
    if (res.data) {
      const newTotal = res.data.total_count || 0;
      pendingTotalCount.value = newTotal;
      pendingCounts.value = res.data.counts || { announcements: 0, students: 0, teachers: 0, leaves: 0 };
      pendingNotificationItems.value = res.data.notifications || [];

      // If dropdown is currently open when new items arrive, mark them as read
      if (showNotificationDropdown.value) {
        markAllAsRead();
      }

      // If pending count changed (e.g. new student registered), alert active views to refresh
      if (lastPendingCount !== -1 && newTotal !== lastPendingCount) {
        window.dispatchEvent(new CustomEvent('pdt-data-updated'));
      }
      lastPendingCount = newTotal;
    }
  } catch (err) {
    console.error('Failed to fetch pending notifications:', err);
  } finally {
    isFetchingPendingNotifs = false;
    isLoadingNotifications.value = false;
  }
};

const handleDocClick = (e) => {
  if (notificationDropdownRef.value && !notificationDropdownRef.value.contains(e.target)) {
    showNotificationDropdown.value = false;
    expandedNotificationId.value = null;
  }
};

const handleKeydown = (e) => {
  if (e.key === 'Escape') {
    showNotificationDropdown.value = false;
  }
};

// Logout Confirmation Modal State & Handlers
const showLogoutModal = ref(false);
const isLoggingOut = ref(false);

const promptLogout = () => {
  showLogoutModal.value = true;
};

const executeLogout = async () => {
  if (isLoggingOut.value) return;
  isLoggingOut.value = true;

  try {
    // 1. Clear all client-side cached data in localStorage and sessionStorage
    try {
      const keysToRemove = [];
      for (let i = 0; i < localStorage.length; i++) {
        const k = localStorage.key(i);
        if (k && (k.startsWith('sala_portal_') || k.startsWith('pdt_') || k.startsWith('chunk_retry_') || k.startsWith('sp_'))) {
          keysToRemove.push(k);
        }
      }
      keysToRemove.forEach(k => localStorage.removeItem(k));
      sessionStorage.clear();
    } catch (e) {}

    // 2. Clear PWA app badge
    if (typeof navigator !== 'undefined' && 'clearAppBadge' in navigator) {
      navigator.clearAppBadge().catch(() => {});
    }

    // 3. Clear Service Worker caches
    if (typeof navigator !== 'undefined' && 'serviceWorker' in navigator && navigator.serviceWorker.controller) {
      try {
        navigator.serviceWorker.controller.postMessage({ type: 'CLEAR_CACHE' });
      } catch (e) {}
    }
    if (typeof caches !== 'undefined') {
      try {
        const cacheNames = await caches.keys();
        await Promise.all(cacheNames.map(name => caches.delete(name)));
      } catch (e) {}
    }

    // 4. Submit standard logout form for atomic, 100% reliable session termination and redirect
    let form = document.getElementById('spa-logout-form');
    if (!form) {
      form = document.createElement('form');
      form.id = 'spa-logout-form';
      form.method = 'POST';
      form.action = '/logout';
      form.style.display = 'none';
      const tokenInput = document.createElement('input');
      tokenInput.type = 'hidden';
      tokenInput.name = '_token';
      tokenInput.value = csrfToken.value;
      form.appendChild(tokenInput);
      document.body.appendChild(form);
    }
    form.submit();
    return;
  } catch (err) {}

  // Fallback: direct navigation to logout endpoint
  window.location.href = '/logout';
};

// School Logo and Brand Header State
const schoolLogo = ref(
  localStorage.getItem('pdt_school_logo') || 
  window.__INITIAL_SETTINGS__?.school_logo_url || 
  '/images/logo.png'
);
const schoolName = ref(
  localStorage.getItem('pdt_school_name') || 
  window.__INITIAL_SETTINGS__?.school_name || 
  'សាលារៀន ជំនាន់ថ្មី'
);
const schoolNameEn = ref(
  localStorage.getItem('pdt_school_name_en') || 
  window.__INITIAL_SETTINGS__?.school_name_en || 
  'New Generation School'
);

const loadSchoolSettings = async () => {
  if (window.__INITIAL_SETTINGS__?.school_name) {
    if (window.__INITIAL_SETTINGS__.school_logo_url) {
      schoolLogo.value = window.__INITIAL_SETTINGS__.school_logo_url;
      updateFavicon(window.__INITIAL_SETTINGS__.school_logo_url);
    }
    schoolName.value = window.__INITIAL_SETTINGS__.school_name;
    schoolNameEn.value = window.__INITIAL_SETTINGS__.school_name_en;
    return;
  }

  try {
    const res = await axios.get('/api/settings');
    if (res.data && res.data.settings) {
      const s = res.data.settings;
      if (s.school_logo_url) {
        schoolLogo.value = s.school_logo_url;
        localStorage.setItem('pdt_school_logo', s.school_logo_url);
        updateFavicon(s.school_logo_url);
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

const handleNavClick = () => {
  if (typeof window !== 'undefined' && window.innerWidth < 768) {
    sidebarOpen.value = false;
  }
};

watch(() => route.path, (newPath) => {
  if (newPath && (newPath.includes('attendance') || newPath.includes('teacher-attendance'))) {
    if (!openDropdowns.value.includes('attendance')) {
      openDropdowns.value.push('attendance');
    }
  }
  if (newPath && (newPath === '/profile' || newPath === '/security' || newPath === '/language' || newPath.startsWith('/settings/'))) {
    if (!openDropdowns.value.includes('account_settings')) {
      openDropdowns.value.push('account_settings');
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
  if (typeof window !== 'undefined') {
    if (window.innerWidth < 768) {
      sidebarOpen.value = false;
    } else {
      sidebarOpen.value = true;
    }
  }

  document.addEventListener('fullscreenchange', () => {
    isFullscreen.value = !!document.fullscreenElement;
  });

  if (schoolLogo.value) {
    updateFavicon(schoolLogo.value);
  }

  loadSchoolSettings();

  // Listen for instant logo file selection from SettingView
  window.addEventListener('pdt-logo-preview', (e) => {
    if (e.detail?.logoUrl) {
      schoolLogo.value = e.detail.logoUrl;
      updateFavicon(e.detail.logoUrl);
    }
  });

  // Listen for saved school settings broadcast
  window.addEventListener('pdt-settings-saved', (e) => {
    if (e.detail?.school_logo_url) {
      schoolLogo.value = e.detail.school_logo_url;
      localStorage.setItem('pdt_school_logo', e.detail.school_logo_url);
      updateFavicon(e.detail.school_logo_url);
    }
    if (e.detail?.school_name) {
      schoolName.value = e.detail.school_name;
      localStorage.setItem('pdt_school_name', e.detail.school_name);
    }
    if (e.detail?.school_name_en) {
      schoolNameEn.value = e.detail.school_name_en;
      localStorage.setItem('pdt_school_name_en', e.detail.school_name_en);
    }
    if (e.detail?.current_academic_year) {
      localStorage.setItem('pdt_current_academic_year', e.detail.current_academic_year);
    }
  });

  // Listen for user avatar updates
  window.addEventListener('pdt-user-avatar-updated', (e) => {
    if (e.detail?.avatar) {
      user.value.avatar = e.detail.avatar;
      if (window.__INITIAL_USER__) {
        window.__INITIAL_USER__.avatar = e.detail.avatar;
      }
    }
  });

  // Listen for storage events from other tabs/windows
  window.addEventListener('storage', (e) => {
    if (e.key === 'pdt_school_logo' && e.newValue) {
      schoolLogo.value = e.newValue;
      updateFavicon(e.newValue);
    }
    if (e.key === 'pdt_school_name' && e.newValue) {
      schoolName.value = e.newValue;
    }
    if (e.key === 'pdt_school_name_en' && e.newValue) {
      schoolNameEn.value = e.newValue;
    }
  });

  document.addEventListener('click', handleDocClick);
  document.addEventListener('keydown', handleKeydown);
  loadReadNotificationIds();
  fetchPendingNotifications(true);

  // Poll every 60 seconds (optimized from 30s)
  const notifInterval = setInterval(() => {
    fetchPendingNotifications();
  }, 60000);

  // Tab Visibility Detection: Auto-fetch immediately when user unlocks screen or returns to tab
  const handleVisibilityChange = () => {
    if (document.visibilityState === 'visible') {
      fetchPendingNotifications(true);
    }
  };
  document.addEventListener('visibilitychange', handleVisibilityChange);

  window.addEventListener('pdt-data-updated', () => fetchPendingNotifications(true));

  onUnmounted(() => {
    document.removeEventListener('click', handleDocClick);
    document.removeEventListener('keydown', handleKeydown);
    document.removeEventListener('visibilitychange', handleVisibilityChange);
    clearInterval(notifInterval);
    window.removeEventListener('pdt-data-updated', () => fetchPendingNotifications(true));
  });
});

const currentLocale = computed(() => locale.value);

const switchLanguage = (lang) => {
  setLocale(lang);
};

const user = ref({
  name: window.__INITIAL_USER__?.name || 'Stevne Zone',
  role: window.__INITIAL_USER__?.role || 'Student',
  role_khmer: window.__INITIAL_USER__?.role_khmer || '',
  is_super_admin: Boolean(window.__INITIAL_USER__?.is_super_admin),
  avatar: window.__INITIAL_USER__?.avatar || '',
  student_grade: window.__INITIAL_USER__?.student_grade || '',
  student_id: window.__INITIAL_USER__?.student_id || null,
});

const csrfToken = computed(() => {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
});

// Menu items with i18n keys and role access definitions
const menuItems = [
  { 
    i18nKey: 'nav.dashboard', 
    path: '/dashboard', 
    icon: LayoutDashboard,
    roles: ['Super Admin', 'Admin', 'Teacher', 'Student']
  },
  { 
    id: 'attendance',
    i18nKey: 'nav.attendance', 
    icon: CalendarCheck,
    roles: ['Super Admin', 'Admin', 'Teacher', 'Student'],
    children: [
      { 
        nameKm: 'វត្តមានសិស្ស', 
        nameEn: 'Student Attendance', 
        path: '/attendance', 
        icon: GraduationCap,
        roles: ['Super Admin', 'Admin', 'Teacher', 'Student']
      },
      { 
        nameKm: 'វត្តមានគ្រូបង្រៀន', 
        nameEn: 'Teacher Attendance', 
        path: '/teacher-attendance', 
        icon: Users,
        roles: ['Super Admin', 'Admin']
      },
    ]
  },
  { i18nKey: 'nav.hostel', path: '/students', icon: GraduationCap, roles: ['Super Admin', 'Admin', 'Teacher'] },
  { i18nKey: 'nav.teachers', path: '/teachers', icon: Users, roles: ['Super Admin', 'Admin'] },
  { i18nKey: 'nav.academics', path: '/classes', icon: Building, roles: ['Super Admin', 'Admin', 'Teacher'] },
  { i18nKey: 'nav.academicYears', path: '/academic-years', icon: CalendarCheck, roles: ['Super Admin', 'Admin'] },
  { i18nKey: 'nav.inventory', path: '/course-subjects', icon: Package, roles: ['Super Admin', 'Admin'] },
  { i18nKey: 'nav.homework', path: '/timetables', icon: BookMarked, roles: ['Super Admin', 'Admin', 'Teacher', 'Student'] },
  { i18nKey: 'nav.exam', path: '/examinations', icon: FileText, roles: ['Super Admin', 'Admin', 'Teacher', 'Student'] },
  { i18nKey: 'nav.messaging', path: '/parents', icon: MessageSquare, roles: ['Super Admin', 'Admin'] },
  { i18nKey: 'nav.feesCollection', path: '/finance', icon: BadgePercent, roles: ['Super Admin', 'Admin'] },
  { i18nKey: 'nav.library', path: '/library', icon: BookOpen, roles: ['Super Admin', 'Admin', 'Teacher', 'Student'] },
  { nameKm: 'សំណើ និងមតិយោបល់', nameEn: 'Requests & Feedback', path: '/requests', icon: BookmarkPlus, roles: ['Super Admin', 'Admin', 'Teacher', 'Student'], dividerAfter: true },
  { 
    id: 'account_settings',
    nameKm: 'គណនី និងការកំណត់', 
    nameEn: 'Account & Settings', 
    icon: UserRound,
    roles: ['Super Admin', 'Admin', 'Teacher', 'Student'],
    children: [
      { 
        nameKm: 'ព័ត៌មានផ្ទាល់ខ្លួន', 
        nameEn: 'Personal Info', 
        path: '/profile', 
        icon: UserRound,
        roles: ['Super Admin', 'Admin', 'Teacher', 'Student']
      },
      { 
        nameKm: 'សុវត្ថិភាព', 
        nameEn: 'Security', 
        path: '/security', 
        icon: ShieldCheck,
        roles: ['Super Admin', 'Admin', 'Teacher', 'Student']
      },
      { 
        nameKm: 'ភាសា', 
        nameEn: 'Language', 
        path: '/language', 
        icon: Languages,
        roles: ['Super Admin', 'Admin', 'Teacher', 'Student']
      },
    ]
  },
  { i18nKey: 'nav.notice', path: '/announcements', icon: Bell, roles: ['Super Admin', 'Admin', 'Teacher'] },
  { i18nKey: 'nav.reports', path: '/reports', icon: FileText, roles: ['Super Admin', 'Admin', 'Teacher'] },
  { i18nKey: 'nav.rolesPermissions', path: '/roles', icon: ShieldCheck, roles: ['Super Admin', 'Admin'] },
  { i18nKey: 'nav.systemLogs', path: '/system-logs', icon: ShieldAlert, superAdminOnly: true, roles: ['Super Admin'] },
  { i18nKey: 'nav.websiteSetting', path: '/settings', icon: Settings, roles: ['Super Admin', 'Admin'] },
];

const userRole = computed(() => {
  if (user.value.is_super_admin) return 'Super Admin';
  return user.value.role || 'Admin';
});

const displayRoleName = computed(() => {
  const isKm = currentLocale.value === 'km';
  if (user.value.role_khmer && isKm) {
    return user.value.role_khmer;
  }
  if (user.value.is_super_admin) {
    return isKm ? 'អគ្គអ្នកគ្រប់គ្រង' : 'Super Admin';
  }
  const r = (user.value.role || '').toLowerCase();
  if (r.includes('super_admin') || r.includes('superadmin')) {
    return isKm ? 'អគ្គអ្នកគ្រប់គ្រង' : 'Super Admin';
  }
  if (r.includes('teacher') || r.includes('គ្រូ')) {
    return isKm ? 'គ្រូបង្រៀន' : 'Teacher';
  }
  if (r.includes('student') || r.includes('សិស្ស')) {
    return isKm ? 'សិស្សានុសិស្ស' : 'Student';
  }
  if (r.includes('admin')) {
    return isKm ? 'អ្នកគ្រប់គ្រង' : 'Admin';
  }
  return isKm ? 'សិស្សានុសិស្ស' : 'Student';
});

const isStudentUser = computed(() => {
  if (user.value.is_super_admin) return false;
  const r = (user.value.role || '').toLowerCase();
  const rKm = user.value.role_khmer || '';
  return r.includes('student') || rKm.includes('សិស្ស') || userRole.value === 'Student';
});

const getMenuLabel = (item) => {
  if (item.nameKm && item.nameEn) {
    return currentLocale.value === 'km' ? item.nameKm : item.nameEn;
  }
  if (item.path === '/timetables' && isStudentUser.value) {
    return currentLocale.value === 'km' ? 'កាលវិភាគសិក្សា' : 'Class Timetable';
  }
  return item.i18nKey ? t(item.i18nKey) : (item.name || '');
};

const isItemVisible = (item) => {
  if (item.superAdminOnly && !user.value.is_super_admin) {
    return false;
  }
  if (item.roles && !item.roles.includes(userRole.value)) {
    return false;
  }
  return true;
};

const visibleMenuItems = computed(() => {
  return menuItems
    .filter(isItemVisible)
    .map(item => {
      if (item.children) {
        const filteredChildren = item.children.filter(child => !child.roles || child.roles.includes(userRole.value));
        // For Students or any role with only 1 accessible child (e.g. Student Attendance),
        // flatten it into a direct link without a dropdown accordion!
        if (item.id === 'attendance' && (isStudentUser.value || filteredChildren.length <= 1)) {
          const directChild = filteredChildren[0] || item.children[0];
          return {
            ...item,
            path: directChild.path || '/attendance',
            children: null
          };
        }
        return {
          ...item,
          children: filteredChildren
        };
      }
      return item;
    });
});

const isActive = (path) => {
  if (!path) return false;
  if (path === '/requests') {
    return route.path === '/requests' || route.path.startsWith('/requests');
  }
  if (path === '/library') {
    return route.path === '/library' || route.path.startsWith('/library');
  }
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
