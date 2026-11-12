<template>
  <div class="space-y-6">
    <!-- Page Header (No action button as requested) -->
    <PageHeader 
      title="សំណើ និងមតិយោបល់ (Requests & Feedback)" 
      breadcrumb="សំណើ & មតិយោបល់" 
    />

    <!-- 1. Collapsible Welcome Banner (Slim Toggleable Header) -->
    <div class="rounded-2xl bg-gradient-to-r from-indigo-950 via-indigo-900 to-slate-900 text-white shadow-xs border border-indigo-700/30 overflow-hidden transition-all duration-300">
      <button 
        type="button" 
        @click="showWelcomeNotice = !showWelcomeNotice" 
        class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-white/5 transition cursor-pointer"
        title="ចុចដើម្បីពង្រីក ឬបង្រួមសេចក្តីណែនាំ"
      >
        <div class="flex items-center gap-2.5 min-w-0">
          <div class="w-7 h-7 rounded-lg bg-indigo-500/25 text-amber-300 flex items-center justify-center text-xs shrink-0 border border-white/10">
            <i class="fa-solid fa-bullhorn"></i>
          </div>
          <div class="truncate">
            <h3 class="text-xs sm:text-sm font-bold text-white truncate flex items-center gap-2">
              <span>សេចក្តីណែនាំអំពីប្រអប់ «សំណើ និងមតិយោបល់»</span>
              <span v-if="!showWelcomeNotice" class="text-[10px] font-normal text-indigo-300 hidden sm:inline">(ចុចដើម្បីអាន)</span>
            </h3>
            <p v-if="!showWelcomeNotice" class="text-[10px] text-indigo-300/80 truncate sm:hidden">
              ចុចដើម្បីអានព័ត៌មានលម្អិត...
            </p>
          </div>
        </div>
        <div class="flex items-center gap-1.5 shrink-0 ml-2 text-indigo-300">
          <span class="text-[11px] font-medium hidden sm:inline">{{ showWelcomeNotice ? 'បង្រួម' : 'ពង្រីក' }}</span>
          <div class="w-6 h-6 rounded-lg bg-white/10 flex items-center justify-center text-xs transition-transform duration-200" :class="{ 'rotate-180': showWelcomeNotice }">
            <i class="fa-solid fa-chevron-down text-[10px]"></i>
          </div>
        </div>
      </button>

      <!-- Collapsible Body Content -->
      <div v-show="showWelcomeNotice" class="px-4 pb-4 pt-1 border-t border-white/10 text-xs sm:text-sm text-indigo-100/90 leading-relaxed font-light space-y-1.5 animate-fadeIn">
        <p>
          លោកគ្រូ អ្នកគ្រូ និងប្អូនៗសិស្សានុសិស្ស អាចចែករំលែកមតិយោបល់ សំណូមពរសៀវភៅ និងតំណភ្ជាប់ឯកសារសិក្សាមានប្រយោជន៍ ដើម្បីរួមគ្នាអភិវឌ្ឍបណ្ណាល័យឌីជីថលឱ្យកាន់តែប្រសើរឡើង។
        </p>
        <p class="text-[11px] text-amber-300/90 flex items-center gap-1">
          <i class="fa-solid fa-circle-check text-[10px]"></i>
          <span>រាល់ការចូលរួមរបស់លោកអ្នក គឺមានតម្លៃសម្រាប់យើងទាំងអស់គ្នា!</span>
        </p>
      </div>
    </div>

    <!-- 2. Form សរសេរសំណើ និងមតិយោបល់ (Always Directly Visible Form Card) -->
    <div class="bg-white rounded-3xl p-5 sm:p-7 border border-indigo-100 shadow-md space-y-4">
      <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shadow-xs">
          <i class="fa-solid fa-pen-to-square"></i>
        </div>
        <div>
          <h3 class="font-bold text-slate-900 text-sm sm:text-base">ទម្រង់ផ្ញើសំណើ ឬមតិយោបល់</h3>
          <p class="text-[11px] text-slate-400">សូមបំពេញព័ត៌មានខាងក្រោមដើម្បីផ្ញើផ្ទាល់ជូនគណៈគ្រប់គ្រងសាលា</p>
        </div>
      </div>

      <!-- Success Alert Banner if just submitted -->
      <div v-if="submitSuccessMessage" class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between text-xs text-emerald-800 animate-fadeIn">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
          <span class="font-bold">{{ submitSuccessMessage }}</span>
        </div>
        <button type="button" @click="submitSuccessMessage = ''" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <form @submit.prevent="submitFeedbackRequest" class="space-y-4">
        <!-- Topic / Title -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">
            ចំណងជើង ឬប្រធានបទសំណើ *
          </label>
          <input 
            v-model="form.title" 
            type="text" 
            required 
            placeholder="ឧ. សៀវភៅលំហាត់គណិតវិទ្យាត្រៀមបាក់ឌុប ឬ សំណើបន្ថែមម៉ោងពិសោធន៍ STEM..."
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"
          />
        </div>

        <!-- Message / Note -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">
            ខ្លឹមសារសំណើ ឬមតិយោបល់ *
          </label>
          <textarea 
            v-model="form.note" 
            rows="4"
            required
            placeholder="សូមសរសេរខ្លឹមសារលម្អិតនៃសំណើ ឬមតិយោបល់របស់អ្នកនៅទីនេះ..."
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition leading-relaxed"
          ></textarea>
        </div>

        <!-- Link input (NO file upload as requested) -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
            <span class="flex items-center gap-1.5">
              <i class="fa-solid fa-link text-indigo-500"></i>
              <span>តំណភ្ជាប់ឯកសារ (Link: Google Drive, Telegram, Website, YouTube)</span>
            </span>
            <span class="text-[10px] text-slate-400 font-normal">ស្រេចចិត្ត (បើមាន)</span>
          </label>
          <div class="relative">
            <i class="fa-solid fa-globe absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              v-model="form.file_url" 
              type="url" 
              placeholder="https://drive.google.com/... ឬ https://t.me/..."
              class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition font-mono text-xs"
            />
          </div>
          <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
            <i class="fa-solid fa-circle-info text-indigo-400"></i>
            <span>សូមបិទភ្ជាប់តំណភ្ជាប់ (Link) Google Drive ឬ Telegram ដែលបើកសិទ្ធិមើលជាសាធារណៈ</span>
          </p>
        </div>

        <!-- Action button: Full-width on mobile for easy tapping, auto on desktop -->
        <div class="pt-2">
          <button 
            type="submit" 
            :disabled="isSubmitting"
            class="w-full sm:w-auto sm:ml-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm shadow-indigo-600/25 cursor-pointer disabled:opacity-50"
          >
            <i v-if="isSubmitting" class="fa-solid fa-circle-notch fa-spin text-xs"></i>
            <i v-else class="fa-solid fa-paper-plane text-xs"></i>
            <span>{{ isSubmitting ? 'កំពុងផ្ញើ...' : 'ផ្ញើសំណើ ឬមតិយោបល់' }}</span>
          </button>
        </div>
      </form>
    </div>

    <!-- 3. Admin Only Section: Incoming Requests Inbox (សិស្ស និងគ្រូមិនឃើញផ្នែកនេះទេ) -->
    <div v-if="isAdmin" class="space-y-5 pt-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-slate-200/80 pt-6">
        <div>
          <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
            <i class="fa-solid fa-inbox text-indigo-600"></i>
            <span>ប្រអប់ទទួលសំណើ និងមតិយោបល់ (Admin Inbox)</span>
          </h3>
          <p class="text-xs text-slate-400">សំណើ និងមតិយោបល់ដែលបានផ្ញើមកពីលោកគ្រូ អ្នកគ្រូ និងសិស្សានុសិស្ស</p>
        </div>
      </div>

      <!-- Metrics & Search Controls -->
      <div class="bg-white/90 backdrop-blur-md rounded-2xl p-3 border border-slate-200/70 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 text-xs">
          <button
            type="button"
            @click="roleFilter = 'all'"
            :class="[
              'px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer flex items-center gap-1.5',
              roleFilter === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            ]"
          >
            <span>ទាំងអស់</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="roleFilter === 'all' ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">{{ counts.all }}</span>
          </button>

          <button
            type="button"
            @click="roleFilter = 'student'"
            :class="[
              'px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer flex items-center gap-1.5',
              roleFilter === 'student' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            ]"
          >
            <span>🎓 ពីសិស្សានុសិស្ស</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="roleFilter === 'student' ? 'bg-white/20' : 'bg-emerald-100 text-emerald-800'">{{ counts.students }}</span>
          </button>

          <button
            type="button"
            @click="roleFilter = 'teacher'"
            :class="[
              'px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer flex items-center gap-1.5',
              roleFilter === 'teacher' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            ]"
          >
            <span>👨‍🏫 ពីលោកគ្រូ/អ្នកគ្រូ</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="roleFilter === 'teacher' ? 'bg-white/20' : 'bg-blue-100 text-blue-800'">{{ counts.teachers }}</span>
          </button>

          <button
            type="button"
            @click="roleFilter = 'link'"
            :class="[
              'px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer flex items-center gap-1.5',
              roleFilter === 'link' ? 'bg-violet-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            ]"
          >
            <span>🔗 មានតំណភ្ជាប់ Link</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="roleFilter === 'link' ? 'bg-white/20' : 'bg-violet-100 text-violet-800'">{{ counts.links }}</span>
          </button>
        </div>

        <!-- Search Input -->
        <div class="relative w-full md:w-64">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input 
            v-model="searchQuery"
            type="text"
            placeholder="ស្វែងរកតាមចំណងជើង, ឈ្មោះ..."
            class="w-full pl-8 pr-3 py-1.5 bg-slate-100 focus:bg-white border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition"
          />
        </div>
      </div>

      <!-- Feed List -->
      <div v-if="isLoading" class="py-16 text-center text-slate-400 text-xs">
        <i class="fa-solid fa-circle-notch fa-spin text-2xl text-indigo-600 mb-2"></i>
        <p>កំពុងផ្ទុកបញ្ជីសំណើ និងមតិយោបល់...</p>
      </div>

      <div v-else-if="filteredRequests.length === 0" class="py-16 px-4 text-center bg-white rounded-3xl border border-slate-200/60 shadow-xs space-y-3">
        <div class="w-14 h-14 mx-auto rounded-full bg-slate-50 text-slate-400 flex items-center justify-center text-2xl">
          <i class="fa-solid fa-inbox"></i>
        </div>
        <div class="space-y-1">
          <p class="font-bold text-slate-800 text-sm">មិនទាន់មានសំណើ ឬមតិយោបល់ផ្ញើមកនៅឡើយទេ</p>
          <p class="text-xs text-slate-400">សំណើ និងមតិយោបល់ថ្មីៗពីសិស្ស និងគ្រូនឹងបង្ហាញនៅទីនេះ</p>
        </div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div 
          v-for="req in filteredRequests" 
          :key="req.id"
          class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition space-y-4 flex flex-col justify-between"
        >
          <div class="space-y-3.5">
            <!-- Post Header: Requester avatar, name, role badge, grade, time -->
            <div class="flex items-start justify-between gap-2">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-600 text-white flex items-center justify-center text-sm font-bold shrink-0 shadow-xs">
                  {{ req.requested_by_name?.charAt(0) || 'ស' }}
                </div>
                <div>
                  <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="font-bold text-slate-900 text-xs sm:text-sm">{{ req.requested_by_name }}</span>
                    <span 
                      :class="[
                        'px-2 py-0.5 rounded-lg text-[10px] font-bold border',
                        req.requested_by_role === 'teacher' ? 'bg-blue-50 text-blue-700 border-blue-200' :
                        req.requested_by_role === 'admin' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' :
                        'bg-emerald-50 text-emerald-700 border-emerald-200'
                      ]"
                    >
                      {{ req.requested_by_role === 'teacher' ? 'លោកគ្រូ/អ្នកគ្រូ' : req.requested_by_role === 'admin' ? 'រដ្ឋបាល' : 'សិស្ស' }}
                    </span>
                    <span v-if="req.grade && req.grade !== 'ទូទៅ'" class="px-2 py-0.5 rounded-lg text-[10px] bg-slate-100 text-slate-600 font-semibold">
                      {{ req.grade.includes('ថ្នាក់') ? req.grade : 'ថ្នាក់ទី ' + req.grade }}
                    </span>
                  </div>
                  <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                    <i class="fa-regular fa-clock text-[10px]"></i>
                    <span>{{ req.time_ago }} ({{ req.created_at }})</span>
                  </p>
                </div>
              </div>

            </div>

            <!-- Title & Note -->
            <div>
              <h4 class="font-bold text-slate-900 text-sm leading-snug">{{ req.title }}</h4>
              <p class="text-xs text-slate-600 mt-1.5 leading-relaxed whitespace-pre-line bg-slate-50/70 p-3 rounded-2xl border border-slate-100">
                {{ req.note }}
              </p>
            </div>

            <!-- Attached External Link (Google Drive / Telegram / etc.) -->
            <div v-if="req.file_url" class="p-3 bg-indigo-50/60 rounded-2xl border border-indigo-100/80 flex items-center justify-between gap-3 text-xs">
              <div class="flex items-center gap-2 overflow-hidden">
                <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shrink-0">
                  <i class="fa-solid fa-link"></i>
                </div>
                <div class="truncate">
                  <p class="text-[10px] text-indigo-400 font-semibold">តំណភ្ជាប់ឯកសារ</p>
                  <a 
                    :href="req.file_url" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="font-mono text-indigo-700 hover:text-indigo-900 hover:underline truncate block"
                  >
                    {{ req.file_url }}
                  </a>
                </div>
              </div>
              <a 
                :href="req.file_url" 
                target="_blank" 
                rel="noopener noreferrer" 
                class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[11px] font-bold shrink-0 transition flex items-center gap-1 shadow-xs"
              >
                <span>បើកមើល</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
              </a>
            </div>

            <!-- Admin Feedback / Official Reply (if any) -->
            <div v-if="req.admin_feedback" class="p-3 bg-emerald-50/70 rounded-2xl border border-emerald-200/70 space-y-1">
              <div class="flex items-center gap-1.5 text-emerald-800 text-xs font-bold">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>ការឆ្លើយតបផ្លូវការពីរដ្ឋបាលសាលា</span>
                <span v-if="req.reviewed_by" class="text-[10px] text-emerald-600 font-normal">({{ req.reviewed_by }})</span>
              </div>
              <p class="text-xs text-emerald-900/90 whitespace-pre-line pl-4 border-l-2 border-emerald-400">
                {{ req.admin_feedback }}
              </p>
            </div>
          </div>

          <!-- Post Footer: Action Buttons (Delete & Official Reply) with Safe Spacing & Padding -->
          <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
            <!-- Delete Button (Left side, Red-themed with generous tap target) -->
            <button 
              type="button" 
              @click="deleteRequest(req)" 
              title="លុបសំណើនេះ"
              class="px-3.5 py-2 text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100/80 border border-rose-200/60 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition active:scale-95 cursor-pointer shadow-2xs"
            >
              <i class="fa-regular fa-trash-can text-xs"></i>
              <span>លុប</span>
            </button>

            <!-- Official Reply Button (Right side, Indigo-themed with generous tap target) -->
            <button
              type="button"
              @click="openReplyModal(req)"
              class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs shadow-indigo-600/20"
            >
              <i class="fa-solid fa-reply text-xs"></i>
              <span>{{ req.admin_feedback ? 'កែសម្រួលការឆ្លើយតប' : 'ឆ្លើយតបផ្លូវការ' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Admin Official Reply Modal -->
    <div 
      v-if="showReplyModal" 
      class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 animate-fadeIn"
    >
      <div class="bg-white rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold">
              <i class="fa-solid fa-reply"></i>
            </div>
            <h3 class="font-bold text-slate-900 text-sm">ឆ្លើយតបផ្លូវការទៅកាន់សំណើ</h3>
          </div>
          <button 
            type="button" 
            @click="showReplyModal = false" 
            class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer"
          >
            &times;
          </button>
        </div>

        <form @submit.prevent="submitReply" class="space-y-4">
          <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
            <p class="text-xs text-slate-500 font-semibold">ប្រធានបទ៖</p>
            <p class="text-xs font-bold text-slate-800">{{ replyTargetRequest?.title }}</p>
            <p class="text-[11px] text-slate-400">ពី៖ {{ replyTargetRequest?.requested_by_name }} ({{ replyTargetRequest?.requested_by_role === 'teacher' ? 'គ្រូបង្រៀន' : 'សិស្ស' }})</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ខ្លឹមសារឆ្លើយតបពីរដ្ឋបាលសាលា *</label>
            <textarea 
              v-model="replyMessage" 
              rows="4" 
              required
              placeholder="សរសេរការឆ្លើយតប ឬដំណោះស្រាយពីរដ្ឋបាល..."
              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
            ></textarea>
          </div>

          <div class="pt-2 flex items-center justify-end gap-2">
            <button 
              type="button" 
              @click="showReplyModal = false" 
              class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-50 transition cursor-pointer"
            >
              បោះបង់
            </button>
            <button 
              type="submit" 
              :disabled="isSubmittingReply"
              class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-indigo-500/20 cursor-pointer disabled:opacity-50"
            >
              <i v-if="isSubmittingReply" class="fa-solid fa-circle-notch fa-spin text-xs"></i>
              <span>{{ isSubmittingReply ? 'កំពុងរក្សាទុក...' : 'រក្សាទុកការឆ្លើយតប' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';

const currentUser = ref(window.__INITIAL_USER__ || {});
const userRole = computed(() => {
  const r = (currentUser.value?.role || '').toLowerCase();
  if (r.includes('admin')) return 'admin';
  if (r.includes('teacher') || r.includes('គ្រូ')) return 'teacher';
  return 'student';
});
const isAdmin = computed(() => 
  userRole.value === 'admin' || 
  Boolean(currentUser.value?.is_super_admin) || 
  currentUser.value?.role_khmer === 'អគ្គអ្នកគ្រប់គ្រង' || 
  currentUser.value?.role_khmer === 'អ្នកគ្រប់គ្រង'
);

const requestsList = ref([]);
const counts = ref({ all: 0, students: 0, teachers: 0, links: 0 });
const isLoading = ref(true);
const roleFilter = ref('all'); // all, student, teacher, link
const searchQuery = ref('');

// Collapsible Welcome Notice State (Compact on mobile)
const showWelcomeNotice = ref(false);

// Form State
const isSubmitting = ref(false);
const submitSuccessMessage = ref('');
const form = ref({
  title: '',
  file_url: '',
  note: '',
});

// Admin Reply Modal State
const showReplyModal = ref(false);
const isSubmittingReply = ref(false);
const replyTargetRequest = ref(null);
const replyMessage = ref('');

const filteredRequests = computed(() => {
  let list = requestsList.value;
  if (roleFilter.value === 'student') {
    list = list.filter(r => r.requested_by_role === 'student');
  } else if (roleFilter.value === 'teacher') {
    list = list.filter(r => r.requested_by_role === 'teacher');
  } else if (roleFilter.value === 'link') {
    list = list.filter(r => !!r.file_url);
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim();
    list = list.filter(r => 
      (r.title && r.title.toLowerCase().includes(q)) ||
      (r.requested_by_name && r.requested_by_name.toLowerCase().includes(q)) ||
      (r.note && r.note.toLowerCase().includes(q))
    );
  }
  return list;
});

const fetchRequests = async () => {
  // Only Admin needs to fetch the list of submitted requests
  if (!isAdmin.value) {
    isLoading.value = false;
    return;
  }
  isLoading.value = true;
  try {
    const res = await axios.get('/api/library/requests', { params: { _t: Date.now() } });
    if (res.data) {
      requestsList.value = res.data.requests || [];
      counts.value = res.data.counts || { all: 0, students: 0, teachers: 0, links: 0 };
    }
  } catch (err) {
    console.error('Failed to fetch requests:', err);
  } finally {
    isLoading.value = false;
  }
};

const resetForm = () => {
  form.value = {
    title: '',
    file_url: '',
    note: '',
  };
};

const submitFeedbackRequest = async () => {
  if (!form.value.title?.trim() || !form.value.note?.trim()) {
    alert('សូមបញ្ចូលចំណងជើង និងខ្លឹមសារសំណើ ឬមតិយោបល់');
    return;
  }
  isSubmitting.value = true;
  submitSuccessMessage.value = '';
  try {
    const senderRealName = currentUser.value?.student_name || currentUser.value?.name || '';
    const payload = {
      title: form.value.title.trim(),
      note: form.value.note.trim(),
      file_url: form.value.file_url?.trim() || null,
      requested_by_name: senderRealName !== 'Student' ? senderRealName : '',
      grade: currentUser.value?.student_grade || currentUser.value?.student?.grade || currentUser.value?.grade || (userRole.value === 'teacher' ? 'គ្រូបង្រៀន' : (userRole.value === 'admin' ? 'រដ្ឋបាល' : 'ទូទៅ')),
    };
    const res = await axios.post('/api/library/requests', payload);
    if (res.data?.success) {
      resetForm();
      submitSuccessMessage.value = 'សំណើ ឬមតិយោបល់របស់អ្នកត្រូវបានផ្ញើជូនគណៈគ្រប់គ្រងសាលាដោយជោគជ័យ!';
      if (isAdmin.value) {
        await fetchRequests();
      }
      window.dispatchEvent(new CustomEvent('pdt-data-updated'));
    }
  } catch (err) {
    console.error('Error submitting feedback:', err);
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការផ្ញើសំណើ');
  } finally {
    isSubmitting.value = false;
  }
};

const openReplyModal = (req) => {
  replyTargetRequest.value = req;
  replyMessage.value = req.admin_feedback || '';
  showReplyModal.value = true;
};

const submitReply = async () => {
  if (!replyTargetRequest.value) return;
  isSubmittingReply.value = true;
  try {
    const res = await axios.put(`/api/library/requests/${replyTargetRequest.value.id}/status`, {
      status: 'published',
      admin_feedback: replyMessage.value
    });
    if (res.data?.success) {
      window.dispatchEvent(new CustomEvent('pdt-notif-remove', { detail: { id: `book-request-${replyTargetRequest.value.id}` } }));
      try { localStorage.setItem('pdt_sync_ts', String(Date.now())); } catch (e) {}
      showReplyModal.value = false;
      alert('បានកត់ត្រាការឆ្លើយតបដោយជោគជ័យ!');
      await fetchRequests();
      window.dispatchEvent(new CustomEvent('pdt-data-updated'));
    }
  } catch (err) {
    console.error('Failed to reply:', err);
    alert('មានបញ្ហាក្នុងការឆ្លើយតប');
  } finally {
    isSubmittingReply.value = false;
  }
};

const deleteRequest = async (req) => {
  if (!confirm(`តើអ្នកពិតជាចង់លុបសំណើ «${req.title}» នេះមែនទេ?`)) return;
  try {
    const res = await axios.delete(`/api/library/requests/${req.id}`);
    if (res.data?.success) {
      window.dispatchEvent(new CustomEvent('pdt-notif-remove', { detail: { id: `book-request-${req.id}` } }));
      try { localStorage.setItem('pdt_sync_ts', String(Date.now())); } catch (e) {}
      await fetchRequests();
      window.dispatchEvent(new CustomEvent('pdt-data-updated'));
    }
  } catch (err) {
    console.error('Failed to delete:', err);
    alert('មិនអាចលុបបានទេ');
  }
};

onMounted(() => {
  resetForm();
  fetchRequests();
});
</script>
