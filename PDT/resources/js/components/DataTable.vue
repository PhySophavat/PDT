<template>
  <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
          <slot name="header" />
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          <slot name="body" />
        </tbody>
      </table>
    </div>
    
    <div v-if="$slots.footer" class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
</script>
