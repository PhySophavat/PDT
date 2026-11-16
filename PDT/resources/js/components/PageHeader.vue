<template>
  <div 
    class="justify-between gap-2.5 sm:gap-4 max-w-full"
    :class="stackOnMobile ? 'flex flex-col sm:flex-row sm:items-center' : 'flex items-center'"
  >
    <div class="min-w-0 flex-1">
      <h1 class="text-base sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight leading-snug truncate">{{ title }}</h1>
      <nav class="flex items-center gap-1.5 text-[11px] sm:text-xs text-slate-400 dark:text-neutral-400 mt-0.5 truncate">
        <span>{{ $t('common.home') }}</span>
        <span>&raquo;</span>
        <span class="text-indigo-600 dark:text-indigo-400 font-medium truncate">{{ breadcrumb || title }}</span>
      </nav>
    </div>

    <div 
      v-if="actionLabel || $slots.extraActions" 
      class="flex items-center gap-1.5 sm:gap-2 max-w-full overflow-x-auto py-0.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden shrink-0 sm:shrink" 
      :class="{ 'w-full sm:w-auto self-start sm:self-center': stackOnMobile }"
    >
      <slot name="extraActions"></slot>
      <button 
        v-if="actionLabel"
        @click="$emit('action')" 
        class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-1.5 sm:py-2.5 bg-[#1a44e8] hover:bg-[#1538c2] text-white text-xs font-semibold rounded-full shadow-md shadow-blue-600/20 transition-all hover:shadow-lg hover:shadow-blue-600/30 active:scale-95 cursor-pointer shrink-0 whitespace-nowrap"
      >
        <!-- Plus SVG icon -->
        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
  },
  stackOnMobile: {
    type: Boolean,
    default: false
  }
});

defineEmits(['action']);
</script>
