<!-- PWA Install Guide Modal & Floating Trigger -->
<div id="pwa-install-container" class="hidden">
  <!-- Floating Install Pill for Mobile -->
  <aside 
    id="pwa-floating-btn" 
    aria-label="ការដំឡើងកម្មវិធីលើទូរស័ព្ទ"
    class="fixed bottom-20 md:bottom-5 right-4 z-[9990] flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white pl-3.5 pr-2 py-2 rounded-full shadow-2xl shadow-blue-500/40 border border-white/20 text-xs font-semibold backdrop-blur-md cursor-pointer transition-all hover:scale-105 active:scale-95 animate-bounce"
  >
    <div class="flex items-center gap-2" onclick="window.pwaOpenModal()">
      <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
      <i class="fa-solid fa-mobile-screen-button text-sm"></i>
      <span>ដំឡើង App លើទូរស័ព្ទ</span>
    </div>
    <button 
      type="button" 
      onclick="window.pwaDismiss(event)" 
      class="ml-1 w-5 h-5 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white/80 hover:text-white transition-colors"
      title="បិទ"
    >
      <i class="fa-solid fa-xmark text-[10px]"></i>
    </button>
  </aside>

  <!-- PWA Install Modal Overlay -->
  <div 
    id="pwa-modal-backdrop" 
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9995] hidden opacity-0 transition-opacity duration-200 flex items-end sm:items-center justify-center p-3 sm:p-4"
    onclick="if(event.target === this) window.pwaCloseModal()"
  >
    <div 
      id="pwa-modal-card" 
      class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 transform transition-all duration-200 scale-95 translate-y-4 space-y-5 text-slate-800"
    >
      <!-- Modal Header -->
      <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/20 p-2">
            <img src="/images/icon-192.png" alt="App Icon" class="w-full h-full object-contain rounded-xl" onerror="this.src='/images/logo.png'">
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">ដំឡើងកម្មវិធីសាលារៀន</h3>
            <p class="text-xs text-slate-500">ប្រើដូចកម្មវិធី App ពិតៗ លឿន និងងាយស្រួល</p>
          </div>
        </div>
        <button 
          type="button" 
          onclick="window.pwaCloseModal()" 
          class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition-colors"
        >
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>
      </div>

      <!-- In-App Browser Warning (Telegram / Facebook) -->
      <div id="pwa-inapp-alert" class="hidden p-3.5 bg-amber-50 border border-amber-200 rounded-2xl text-xs text-amber-900 space-y-1">
        <div class="font-bold flex items-center gap-1.5 text-amber-800">
          <i class="fa-solid fa-triangle-exclamation text-sm"></i>
          <span>អ្នកកំពុងបើកក្នុង Telegram / Facebook</span>
        </div>
        <p class="text-[11px] leading-relaxed text-amber-700">
          ដើម្បីដំណើរការរលូន ១០០% សូមចុចសញ្ញា <strong>⋯</strong> ឬ <strong>Share</strong> (នៅជ្រុងខាងលើ) ហើយជ្រើសរើស <strong>"Open in Safari"</strong> ឬ <strong>"Open in Chrome"</strong>។
        </p>
      </div>

      <!-- Android Native Install Button (if available) -->
      <div id="pwa-android-direct" class="hidden">
        <button 
          type="button" 
          onclick="window.pwaTriggerInstall()"
          class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-2xl font-bold text-sm shadow-xl shadow-blue-500/30 flex items-center justify-center gap-2 active:scale-95 transition-transform"
        >
          <i class="fa-solid fa-download"></i>
          <span>ចុចដើម្បីដំឡើង App ឥឡូវនេះ (Install)</span>
        </button>
      </div>

      <!-- iOS Safari Instructions -->
      <div id="pwa-ios-instructions" class="space-y-3 bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
        <div class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
          <i class="fa-brands fa-apple text-slate-900 text-sm"></i>
          <span>របៀបដំឡើងលើ iPhone / iPad (Safari)៖</span>
        </div>
        <ol class="space-y-2 text-xs text-slate-600 pl-1">
          <li class="flex items-start gap-2">
            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">1</span>
            <span>ចុចប៊ូតុង <strong>Share</strong> <i class="fa-solid fa-arrow-up-from-bracket text-blue-600 mx-1"></i> នៅរបារខាងក្រោមនៃ Safari</span>
          </li>
          <li class="flex items-start gap-2">
            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">2</span>
            <span>អូសចុះក្រោមបន្តិច រួចចុច <strong>"Add to Home Screen" (បន្ថែមទៅអេក្រង់ដើម)</strong> <i class="fa-regular fa-square-plus text-emerald-600 mx-1"></i></span>
          </li>
          <li class="flex items-start gap-2">
            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">3</span>
            <span>ចុចប៊ូតុង <strong>"Add" (បន្ថែម)</strong> នៅជ្រុងខាងស្តាំលើ ជាការស្រេច!</span>
          </li>
        </ol>
      </div>

      <!-- Android Chrome Manual Instructions (if native prompt unavailable) -->
      <div id="pwa-android-manual" class="space-y-3 bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
        <div class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
          <i class="fa-brands fa-android text-emerald-600 text-sm"></i>
          <span>របៀបដំឡើងលើ Android (Google Chrome)៖</span>
        </div>
        <ol class="space-y-2 text-xs text-slate-600 pl-1">
          <li class="flex items-start gap-2">
            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">1</span>
            <span>ចុចសញ្ញាចុច ៣ <i class="fa-solid fa-ellipsis-vertical text-slate-800 mx-1 font-bold"></i> នៅជ្រុងខាងស្តាំលើនៃ Chrome</span>
          </li>
          <li class="flex items-start gap-2">
            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">2</span>
            <span>ជ្រើសរើសយក <strong>"Install app"</strong> ឬ <strong>"Add to Home screen" (បន្ថែមទៅអេក្រង់ដើម)</strong></span>
          </li>
          <li class="flex items-start gap-2">
            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">3</span>
            <span>ចុច <strong>"Install" (ដំឡើង)</strong></span>
          </li>
        </ol>
      </div>

      <!-- Benefits Footer -->
      <div class="grid grid-cols-3 gap-2 text-center pt-1 border-t border-slate-100 text-[11px] text-slate-500">
        <div class="p-2 rounded-xl bg-slate-50">
          <i class="fa-solid fa-bolt text-amber-500 mb-1 block text-sm"></i>
          <span>បើកលឿនរហ័ស</span>
        </div>
        <div class="p-2 rounded-xl bg-slate-50">
          <i class="fa-solid fa-bell text-blue-500 mb-1 block text-sm"></i>
          <span>ពេញអេក្រង់</span>
        </div>
        <div class="p-2 rounded-xl bg-slate-50">
          <i class="fa-solid fa-lock text-emerald-500 mb-1 block text-sm"></i>
          <span>មិនបាត់ Session</span>
        </div>
      </div>

      <div class="text-center">
        <button 
          type="button" 
          onclick="window.pwaCloseModal()" 
          class="text-xs text-slate-400 hover:text-slate-600 underline font-medium"
        >
          បិទផ្ទាំងនេះ
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  // Service Worker Registration
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
      navigator.serviceWorker.register('/sw.js').catch(function() {});
    });
  }

  // Detect standalone (already installed as PWA)
  var isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                     window.navigator.standalone === true || 
                     document.referrer.includes('android-app://');

  if (isStandalone) {
    // Already installed! Don't show prompts
    return;
  }

  // Dismissed check
  var dismissedUntil = localStorage.getItem('pwa_dismissed_until');
  if (dismissedUntil && Number(dismissedUntil) > Date.now()) {
    return;
  }

  var ua = navigator.userAgent || '';
  var isIOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream;
  var isAndroid = /Android/.test(ua);
  var isInApp = /Telegram|FB_IAB|FBAN|FBAV|Instagram|Line/i.test(ua);
  var isMobile = isIOS || isAndroid || window.innerWidth <= 768;

  if (!isMobile) {
    // Only show install helper on mobile devices
    return;
  }

  var deferredPrompt = null;
  window.addEventListener('beforeinstallprompt', function(e) {
    e.preventDefault();
    deferredPrompt = e;
    var directBtn = document.getElementById('pwa-android-direct');
    if (directBtn) directBtn.classList.remove('hidden');
    var manualAndroid = document.getElementById('pwa-android-manual');
    if (manualAndroid) manualAndroid.classList.add('hidden');
  });

  // Reveal the container
  var container = document.getElementById('pwa-install-container');
  if (container) {
    container.classList.remove('hidden');
  }

  // Adjust modal instructions based on OS
  var iosBox = document.getElementById('pwa-ios-instructions');
  var androidBox = document.getElementById('pwa-android-manual');
  var inAppAlert = document.getElementById('pwa-inapp-alert');

  if (isInApp && inAppAlert) {
    inAppAlert.classList.remove('hidden');
  }

  if (isIOS) {
    if (iosBox) iosBox.classList.remove('hidden');
    if (androidBox) androidBox.classList.add('hidden');
  } else {
    if (iosBox) iosBox.classList.add('hidden');
    if (androidBox) androidBox.classList.remove('hidden');
  }

  // Global Helpers
  window.pwaOpenModal = function() {
    var backdrop = document.getElementById('pwa-modal-backdrop');
    var card = document.getElementById('pwa-modal-card');
    if (backdrop && card) {
      backdrop.classList.remove('hidden');
      setTimeout(function() {
        backdrop.classList.remove('opacity-0');
        card.classList.remove('scale-95', 'translate-y-4');
        card.classList.add('scale-100', 'translate-y-0');
      }, 10);
    }
  };

  window.pwaCloseModal = function() {
    var backdrop = document.getElementById('pwa-modal-backdrop');
    var card = document.getElementById('pwa-modal-card');
    if (backdrop && card) {
      backdrop.classList.add('opacity-0');
      card.classList.add('scale-95', 'translate-y-4');
      card.classList.remove('scale-100', 'translate-y-0');
      setTimeout(function() {
        backdrop.classList.add('hidden');
      }, 200);
    }
  };

  window.pwaDismiss = function(e) {
    if (e) e.stopPropagation();
    var pill = document.getElementById('pwa-floating-btn');
    if (pill) pill.classList.add('hidden');
    window.pwaCloseModal();
    // Dismiss for 3 days
    localStorage.setItem('pwa_dismissed_until', String(Date.now() + (3 * 24 * 60 * 60 * 1000)));
  };

  window.pwaTriggerInstall = function() {
    if (deferredPrompt) {
      deferredPrompt.prompt();
      deferredPrompt.userChoice.then(function(choiceResult) {
        if (choiceResult.outcome === 'accepted') {
          window.pwaDismiss();
        }
        deferredPrompt = null;
      });
    }
  };
})();
</script>
