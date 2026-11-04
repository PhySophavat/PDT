<template>
  <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 py-2">
    <!-- Left: Legend or Search -->
    <div class="flex flex-wrap items-center gap-3 text-xs">
      <slot name="left">
        <span class="font-medium text-slate-700">Present: <span class="font-bold text-[#1e40af]">P</span></span>
        <span class="font-medium text-slate-700">Late: <span class="font-bold text-slate-900">L</span></span>
        <span class="font-medium text-slate-700">Absent: <span class="font-bold text-rose-600">A</span></span>
        <span class="font-medium text-slate-700">Holiday: <span class="font-bold text-slate-400">H</span></span>
      </slot>
    </div>

    <!-- Right: Filter Options & Date Dropdown -->
    <div class="flex flex-wrap items-center gap-3 text-xs w-full md:w-auto justify-end">
      <slot name="right">
        <!-- Radio status filters matching reference -->
        <div class="inline-flex items-center gap-3 px-3 py-1.5 bg-slate-50 border border-slate-200/80 rounded-xl">
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
            <input type="radio" name="statusFilter" value="present" checked class="text-blue-600 focus:ring-0">
            <span>Present</span>
          </label>
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
            <input type="radio" name="statusFilter" value="absent" class="text-blue-600 focus:ring-0">
            <span>Absent</span>
          </label>
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
            <input type="radio" name="statusFilter" value="late" class="text-blue-600 focus:ring-0">
            <span>Late</span>
          </label>
        </div>

        <!-- Month / Date Dropdown -->
        <div class="relative">
          <button class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-medium hover:bg-slate-50 transition shadow-2xs">
            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>April 2025</span>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
        </div>
      </slot>
    </div>
  </div>
</template>

<script setup>
</script>
