<template>
  <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div class="flex items-center gap-4">
      <!-- Cyan Avatar Box -->
      <div class="w-14 h-14 rounded-2xl bg-[#00c5cd] flex items-center justify-center text-white overflow-hidden shadow-xs flex-shrink-0">
        <img v-if="avatar" :src="avatar" alt="Avatar" class="w-full h-full object-cover">
        <svg v-else class="w-8 h-8 text-white/90" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
        </svg>
      </div>
      <div>
        <h2 class="text-lg font-bold text-slate-900 leading-tight">{{ name }}</h2>
        <p v-if="subtitle" class="text-xs text-[#6366f1] font-medium mt-0.5">{{ subtitle }}</p>
        <p v-if="metaId" class="text-xs text-slate-400 mt-0.5 font-mono">ID: {{ metaId }}</p>
      </div>
    </div>

    <!-- Actions (Print & Download icons) -->
    <div class="flex items-center gap-2 self-end sm:self-center">
      <button 
        @click="$emit('print')" 
        class="w-9 h-9 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 flex items-center justify-center transition"
        :title="$t('common.print')"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
      </button>
      <button 
        @click="$emit('download')" 
        class="w-9 h-9 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 flex items-center justify-center transition"
        :title="$t('common.download')"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  name: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  metaId: {
    type: [String, Number],
    default: ''
  },
  avatar: {
    type: String,
    default: ''
  }
});

defineEmits(['print', 'download']);
</script>
