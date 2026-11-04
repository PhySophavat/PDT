<template>
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ title }}</h1>
      <nav class="flex items-center gap-1.5 text-xs text-slate-400 mt-1">
        <span>{{ $t('common.home') }}</span>
        <span>&raquo;</span>
        <span class="text-indigo-600 font-medium">{{ breadcrumb || title }}</span>
      </nav>
    </div>

    <div v-if="actionLabel || $slots.extraActions" class="flex flex-wrap items-center gap-2">
      <slot name="extraActions"></slot>
      <button 
        v-if="actionLabel"
        @click="$emit('action')" 
        class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1a44e8] hover:bg-[#1538c2] text-white text-xs font-semibold rounded-full shadow-md shadow-blue-600/20 transition-all hover:shadow-lg hover:shadow-blue-600/30"
      >
        <!-- Plus SVG icon -->
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        <!-- actionLabel must NOT include '+' prefix - it is added by the SVG icon above -->
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
