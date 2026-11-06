<template>
  <div class="bg-white rounded-2xl p-1 sm:p-1.5 border border-slate-100 shadow-xs flex overflow-x-auto max-w-full [scrollbar-width:none] [&::-webkit-scrollbar]:hidden items-center gap-1">
    <button 
      v-for="tab in tabs" 
      :key="tab.id"
      @click="$emit('update:modelValue', tab.id)"
      :class="[
        'px-3.5 py-1.5 sm:px-5 sm:py-2 rounded-xl text-xs font-semibold transition-all inline-flex items-center gap-1.5 sm:gap-2 whitespace-nowrap flex-shrink-0 cursor-pointer',
        modelValue === tab.id 
          ? 'bg-[#254beb] text-white shadow-md shadow-blue-500/20' 
          : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50'
      ]"
    >
      <span>{{ tab.label }}</span>
      <span 
        v-if="tab.badge !== undefined && tab.badge !== null && tab.badge > 0" 
        :class="[
          'px-2 py-0.5 rounded-full text-[10px] font-bold',
          modelValue === tab.id 
            ? 'bg-white/25 text-white' 
            : (tab.badgeColor || 'bg-amber-100 text-amber-800')
        ]"
      >
        {{ tab.badge }}
      </span>
    </button>
  </div>
</template>

<script setup>
defineProps({
  tabs: {
    type: Array,
    required: true
  },
  modelValue: {
    type: [String, Number],
    required: true
  }
});

defineEmits(['update:modelValue']);
</script>
