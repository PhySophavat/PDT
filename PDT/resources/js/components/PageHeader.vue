<template>
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
    <div>
      <h1 class="text-lg sm:text-2xl font-bold text-slate-900 tracking-tight">{{ title }}</h1>
      <nav class="flex items-center gap-1.5 text-[11px] sm:text-xs text-slate-400 mt-0.5">
        <span>{{ $t('common.home') }}</span>
        <span>&raquo;</span>
        <span class="text-indigo-600 font-medium">{{ breadcrumb || title }}</span>
      </nav>
    </div>

    <div v-if="actionLabel || $slots.extraActions" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
      <slot name="extraActions"></slot>
      <button 
        v-if="actionLabel"
        @click="$emit('action')" 
        class="inline-flex items-center gap-1.5 sm:gap-2 px-4 sm:px-6 py-2 sm:py-2.5 bg-[#1a44e8] hover:bg-[#1538c2] text-white text-xs font-semibold rounded-full shadow-md shadow-blue-600/20 transition-all hover:shadow-lg hover:shadow-blue-600/30 active:scale-95 cursor-pointer"
      >
        <!-- Plus SVG icon -->
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        <span>{{ actionLabel }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  breadcrumb: {
    type: String,
    default: ''
  },
  actionLabel: {
    type: String,
    default: ''
  }
});

defineEmits(['action']);
</script>
