<template>
  <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden relative font-khmer">
    <!-- Top Progress Bar (Visible whenever loading is true) -->
    <div v-if="loading" class="absolute top-0 left-0 right-0 h-1 bg-blue-100/70 z-20 overflow-hidden">
      <div class="pdt-indeterminate-bar h-full bg-gradient-to-r from-blue-500 via-indigo-600 to-blue-500 rounded-full"></div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
          <slot name="header" />
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          <!-- State 1: When actively loading and data is empty -->
          <tr v-if="loading && empty">
            <td :colspan="colSpan" class="py-14 text-center font-khmer bg-slate-50/30">
              <div class="flex flex-col items-center justify-center gap-3">
                <div class="relative w-9 h-9">
                  <div class="w-9 h-9 rounded-full border-3 border-blue-100"></div>
                  <div class="absolute inset-0 w-9 h-9 rounded-full border-3 border-blue-600 border-t-transparent animate-spin"></div>
                </div>
                <div class="space-y-1">
                  <p class="text-xs font-bold text-slate-700">{{ loadingText }}</p>
                  <p class="text-[11px] text-slate-400">សូមរង់ចាំបន្តិច ប្រព័ន្ធកំពុងទាញយកទិន្នន័យពី Server...</p>
                </div>
              </div>
            </td>
          </tr>

          <!-- State 2: Slot body rendered when not in initial empty loading state -->
          <slot v-else name="body" />
        </tbody>
      </table>
    </div>
    
    <div v-if="$slots.footer" class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  loading: {
    type: Boolean,
    default: false
  },
  loadingText: {
    type: String,
    default: 'កំពុងទាញយកទិន្នន័យ...'
  },
  empty: {
    type: Boolean,
    default: false
  },
  colSpan: {
    type: [Number, String],
    default: 8
  }
});
</script>

<style scoped>
.pdt-indeterminate-bar {
  position: absolute;
  width: 45%;
  animation: pdt-indeterminate 1.2s infinite ease-in-out;
}
@keyframes pdt-indeterminate {
  0% {
    left: -45%;
  }
  100% {
    left: 100%;
  }
}
</style>
