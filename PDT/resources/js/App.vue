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
        <span>ផ្ទុកទំព័រឡើងវិញ (Reload)</span>
      </button>
    </div>
    <router-view v-else />
  </AdminLayout>
</template>

<script setup>
import { ref, onErrorCaptured } from 'vue';
import AdminLayout from './components/AdminLayout.vue';

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
