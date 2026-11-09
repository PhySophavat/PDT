<template>
  <AdminLayout>
    <div v-if="error" class="p-8 max-w-xl mx-auto my-12 bg-white rounded-2xl border border-rose-200 shadow-lg text-center font-khmer space-y-4">
      <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl font-bold">
        <i class="fa-solid fa-triangle-exclamation"></i>
      </div>
      <h3 class="text-base font-bold text-slate-800">មានបញ្ហាបច្ចេកទេសក្នុងការបង្ហាញទំព័រនេះ</h3>
      <p class="text-xs text-slate-500">{{ error.message || 'សូមព្យាយាមផ្ទុកទំព័រឡើងវិញ' }}</p>
      <button 
        @click="reloadPage" 
        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition inline-flex items-center gap-2 cursor-pointer shadow-md shadow-blue-500/20"
      >
        <i class="fa-solid fa-arrow-rotate-right"></i>
        <span>ផ្ទុកទំព័រឡើងវិញ</span>
      </button>
    </div>
    <router-view v-else v-slot="{ Component }">
      <keep-alive>
        <component :is="Component" />
      </keep-alive>
    </router-view>
  </AdminLayout>
  <!-- Global Modern Floating Toast Notifications (No more blocking browser alert dialogs!) -->
  <ToastContainer />

  <!-- Floating Live Update Banner when new version is ready -->
  <transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0 translate-y-6 scale-95"
    enter-to-class="opacity-100 translate-y-0 scale-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100 translate-y-0 scale-100"
    leave-to-class="opacity-0 translate-y-6 scale-95"
  >
    <div 
      v-if="isUpdateAvailable"
      class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[99999] bg-slate-900/95 text-white backdrop-blur-md px-4 py-2.5 rounded-2xl shadow-2xl border border-indigo-500/40 flex items-center gap-3 font-khmer text-xs"
    >
      <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping shrink-0"></div>
      <div class="flex items-center gap-1.5">
        <span class="font-bold text-indigo-300">🎉 កំណែអាប់ដេតថ្មី!</span>
        <span class="text-slate-300 hidden sm:inline">ប្រព័ន្ធទើបតែទទួលបានមុខងារថ្មីៗ។</span>
      </div>
      <button 
        type="button" 
        @click="applyUpdateNow" 
        :disabled="isUpdatingNow"
        class="ml-2 px-3 py-1 bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-600 hover:to-blue-700 active:scale-95 text-white rounded-xl font-bold transition flex items-center gap-1.5 shadow-md shadow-indigo-500/30 cursor-pointer text-xs"
      >
        <i class="fa-solid fa-arrows-rotate" :class="{ 'fa-spin': isUpdatingNow }"></i>
        <span>{{ isUpdatingNow ? 'កំពុងអាប់ដេត...' : 'អាប់ដេតឥឡូវនេះ' }}</span>
      </button>
    </div>
  </transition>
</template>

<script setup>
import { ref, onErrorCaptured } from 'vue';
import AdminLayout from './components/AdminLayout.vue';
import ToastContainer from './components/ToastContainer.vue';
import { isUpdateAvailable, isUpdatingNow, applyUpdateNow } from './services/autoUpdater';

const error = ref(null);

onErrorCaptured((err) => {
  console.error('App caught runtime error:', err);
  error.value = err;
  return false;
});

const reloadPage = () => {
  error.value = null;
  window.location.reload();
};
</script>
