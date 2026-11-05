<template>
  <div 
    class="fixed top-5 right-5 z-[99999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none font-khmer px-3 sm:px-0"
    aria-live="polite"
  >
    <transition-group 
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="pointer-events-auto relative overflow-hidden rounded-2xl shadow-xl border p-4 backdrop-blur-md transition-all duration-300 flex items-start gap-3 bg-white/95"
        :class="getToastClasses(toast.type)"
      >
        <!-- Icon -->
        <div 
          class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-sm font-bold shadow-sm"
          :class="getIconContainerClasses(toast.type)"
        >
          <i :class="getIconClass(toast.type)"></i>
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0 pr-1">
          <p class="text-xs font-semibold leading-relaxed break-words" :class="getTextClasses(toast.type)">
            {{ toast.message }}
          </p>
        </div>

        <!-- Dismiss button -->
        <button
          @click="removeToast(toast.id)"
          class="shrink-0 text-slate-400 hover:text-slate-600 p-1 -mr-1 -mt-1 rounded-lg transition-colors cursor-pointer"
          title="បិទ"
        >
          <i class="fa-solid fa-xmark text-xs"></i>
        </button>

        <!-- Shimmer Progress Bar -->
        <div 
          class="absolute bottom-0 left-0 h-0.5 w-full bg-slate-200/50 overflow-hidden"
          v-if="toast.duration > 0"
        >
          <div 
            class="h-full transition-all duration-linear"
            :class="getProgressBarClasses(toast.type)"
            :style="{ 
              animation: `toast-progress ${toast.duration}ms linear forwards` 
            }"
          ></div>
        </div>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { useToast } from '../composables/useToast';

const { toasts, removeToast } = useToast();

const getToastClasses = (type) => {
  switch (type) {
    case 'success':
      return 'border-emerald-200/80 shadow-emerald-500/10';
    case 'error':
      return 'border-rose-200/80 shadow-rose-500/10';
    case 'warning':
      return 'border-amber-200/80 shadow-amber-500/10';
    case 'info':
    default:
      return 'border-blue-200/80 shadow-blue-500/10';
  }
};

const getIconContainerClasses = (type) => {
  switch (type) {
    case 'success':
      return 'bg-emerald-50 text-emerald-600 border border-emerald-100';
    case 'error':
      return 'bg-rose-50 text-rose-600 border border-rose-100';
    case 'warning':
      return 'bg-amber-50 text-amber-600 border border-amber-100';
    case 'info':
    default:
      return 'bg-blue-50 text-blue-600 border border-blue-100';
  }
};

const getIconClass = (type) => {
  switch (type) {
    case 'success':
      return 'fa-solid fa-circle-check';
    case 'error':
      return 'fa-solid fa-circle-xmark';
    case 'warning':
      return 'fa-solid fa-triangle-exclamation';
    case 'info':
    default:
      return 'fa-solid fa-circle-info';
  }
};

const getTextClasses = (type) => {
  switch (type) {
    case 'success':
      return 'text-emerald-950';
    case 'error':
      return 'text-rose-950';
    case 'warning':
      return 'text-amber-950';
    case 'info':
    default:
      return 'text-slate-800';
  }
};

const getProgressBarClasses = (type) => {
  switch (type) {
    case 'success':
      return 'bg-emerald-500';
    case 'error':
      return 'bg-rose-500';
    case 'warning':
      return 'bg-amber-500';
    case 'info':
    default:
      return 'bg-blue-500';
  }
};
</script>

<style scoped>
@keyframes toast-progress {
  from {
    width: 100%;
  }
  to {
    width: 0%;
  }
}
</style>

