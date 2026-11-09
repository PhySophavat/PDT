@extends('layouts.guest')

@section('title', 'ចូលប្រើប្រព័ន្ធ | portal sala')
@section('meta_robots', 'noindex, nofollow')

@section('content')
<div class="bg-white/95 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-7 sm:p-9 shadow-xl shadow-slate-200/60 space-y-6">

    <!-- Card Top Header -->
    <div class="text-center space-y-2">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-full text-xs font-bold">
            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
            <span>PORTAL LOGIN • portal sala</span>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            ចូលប្រើប្រព័ន្ធ
        </h1>
        <p class="text-xs text-slate-500 max-w-xs mx-auto">
            សូមបញ្ចូលអ៊ីមែល និងពាក្យសម្ងាត់របស់អ្នក ដើម្បីចូលទៅកាន់ផ្ទាំងគ្រប់គ្រងសាលារៀន។
        </p>
    </div>

    <!-- Alert Notifications (Single unified alert box with real-time countdown if locked) -->
    @if (session('success'))
        <div class="flex items-start gap-2.5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-medium animate-in fade-in duration-200">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @elseif (session('lockout_seconds') || session('error') || $errors->any())
        @php
            $lockoutSecs = (int) session('lockout_seconds', 0);
            $errorMessage = session('error') ?: $errors->first();
        @endphp
        <div id="lockout-alert" class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-medium space-y-2 animate-in fade-in duration-200">
            <div class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm mt-0.5 flex-shrink-0"></i>
                <div class="flex-1">
                    <span id="lockout-text">{{ $errorMessage }}</span>
                    @if ($lockoutSecs > 0)
                        <div class="mt-2.5 flex items-center gap-2 pt-2 border-t border-rose-200/80 text-rose-700">
                            <i class="fa-solid fa-clock-rotate-left text-xs animate-spin" style="animation-duration: 3s;"></i>
                            <span>អាចសាកល្បងម្តងទៀតក្នុងរយៈពេល៖ <strong id="countdown-timer" class="font-mono text-sm font-bold text-rose-900 bg-white/90 px-2 py-0.5 rounded-lg border border-rose-200 shadow-2xs">{{ $lockoutSecs }}</strong> វិនាទី</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Login Form -->
    <form id="login-form" action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Email or Student ID Field -->
        <div class="space-y-1.5">
            <label for="email" class="block text-xs font-bold text-slate-700">
                <span>អាសយដ្ឋានអ៊ីមែល ឬ អត្តលេខសិស្ស (ID)</span>
                <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-user text-sm"></i>
                </span>
                <input 
                    id="email" 
                    type="text" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="ឧ. name@gmail.com ឬ STU-2026-004" 
                    required
                    autofocus
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition"
                >
            </div>
        </div>

        <!-- Password Field with Show/Hide Toggle -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-xs font-bold text-slate-700">
                    <span>ពាក្យសម្ងាត់</span>
                    <span class="text-rose-500">*</span>
                </label>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-lock text-sm"></i>
                </span>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    placeholder="••••••••" 
                    value=""
                    required
                    class="w-full pl-10 pr-10 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition"
                >
                <button 
                    type="button" 
                    id="toggle-password" 
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer"
                    title="បង្ហាញ/លាក់ពាក្យសម្ងាត់"
                >
                    <i class="fa-regular fa-eye text-sm" id="toggle-icon"></i>
                </button>
            </div>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between pt-1 text-xs">
            <label class="inline-flex items-center gap-2 cursor-pointer text-slate-600 select-none">
                <input type="checkbox" name="remember" checked class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-0 cursor-pointer">
                <span>ចងចាំគណនីខ្ញុំ</span>
            </label>
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            id="login-submit-btn"
            class="w-full py-3 px-5 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-blue-500/25 transition-all hover:shadow-lg hover:shadow-blue-500/35 active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer"
        >
            <i class="fa-solid fa-arrow-right-to-bracket text-sm" id="btn-icon"></i>
            <span id="btn-text">ចូលប្រើប្រព័ន្ធ</span>
        </button>

        <!-- Live Login Progress Indicator -->
        <div id="login-loading-indicator" class="hidden p-3.5 bg-blue-50/90 border border-blue-200/80 rounded-2xl text-center space-y-1.5 animate-in fade-in duration-200 shadow-2xs">
            <div class="flex items-center justify-center gap-2 text-xs font-bold text-blue-700">
                <i class="fa-solid fa-circle-notch fa-spin text-sm text-blue-600"></i>
                <span>កំពុងផ្ទៀងផ្ទាត់ និងចូលប្រើប្រព័ន្ធ...</span>
            </div>
            <p class="text-[11px] text-slate-500 font-medium leading-relaxed">
                សូមរង់ចាំបន្តិច ប្រព័ន្ធកំពុងផ្ទៀងផ្ទាត់ព័ត៌មាន និងរៀបចំបើកផ្ទាំងគ្រប់គ្រង (Dashboard)...
            </p>
        </div>

        <!-- Request Registration Link -->
        <div class="pt-4 border-t border-slate-100 text-center">
            <a 
                href="{{ route('public.register-student') }}" 
                class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline transition"
            >
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>មិនទាន់មានគណនី? ស្នើសុំចុះឈ្មោះចូលប្រព័ន្ធ</span>
            </a>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    // Password Visibility Toggle
    const toggleBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggle-icon');

    if (toggleBtn && passwordInput && toggleIcon) {
        toggleBtn.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            toggleIcon.className = isPassword ? 'fa-regular fa-eye-slash text-sm text-blue-600' : 'fa-regular fa-eye text-sm';
        });
    }

    // Auto-refresh CSRF token on mobile (Safari / Telegram in-app browser / bfcache)
    const refreshCsrfToken = () => {
        fetch('/csrf-token', { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                if (data && data.csrf_token) {
                    document.querySelectorAll('input[name="_token"]').forEach(el => el.value = data.csrf_token);
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    if (meta) meta.setAttribute('content', data.csrf_token);
                }
            })
            .catch(() => {});
    };

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            refreshCsrfToken();
        }
    });

    window.addEventListener('focus', refreshCsrfToken);
    // Form Submit Loading Feedback
    const loginForm = document.getElementById('login-form');
    const loginSubmitBtn = document.getElementById('login-submit-btn');
    const btnIcon = document.getElementById('btn-icon');
    const btnText = document.getElementById('btn-text');
    const loadingIndicator = document.getElementById('login-loading-indicator');

    function resetSubmitButton() {
        if (loginSubmitBtn) {
            loginSubmitBtn.disabled = false;
            loginSubmitBtn.classList.remove('opacity-80', 'cursor-wait', 'pointer-events-none');
            if (btnIcon) btnIcon.className = 'fa-solid fa-arrow-right-to-bracket text-sm';
            if (btnText) btnText.textContent = 'ចូលប្រើប្រព័ន្ធ';
        }
        if (loadingIndicator) {
            loadingIndicator.classList.add('hidden');
        }
    }

    if (loginForm && loginSubmitBtn) {
        loginForm.addEventListener('submit', function (e) {
            if (loginSubmitBtn.disabled) {
                e.preventDefault();
                return;
            }

            if (!loginForm.checkValidity()) {
                return;
            }

            // Immediately show live loading state & feedback
            loginSubmitBtn.disabled = true;
            loginSubmitBtn.classList.add('opacity-80', 'cursor-wait', 'pointer-events-none');
            if (btnIcon) btnIcon.className = 'fa-solid fa-circle-notch fa-spin text-sm';
            if (btnText) btnText.textContent = 'កំពុងចូលប្រើប្រព័ន្ធ...';

            if (loadingIndicator) {
                loadingIndicator.classList.remove('hidden');
            }
        });
    }

    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            refreshCsrfToken();
            resetSubmitButton();
        }
    });

    // Real-Time Countdown Timer for Rate Limiting / Lockout
    @if (session('lockout_seconds'))
    (function() {
        let timeLeft = {{ (int) session('lockout_seconds') }};
        const countdownEl = document.getElementById('countdown-timer');
        const submitBtn = document.querySelector('button[type="submit"]');
        const lockoutAlert = document.getElementById('lockout-alert');

        if (submitBtn && timeLeft > 0) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-60', 'cursor-not-allowed', 'pointer-events-none');
            const origSubmitHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = `<i class="fa-solid fa-hourglass-half text-sm animate-pulse"></i> <span>សូមរង់ចាំ (${timeLeft} វិនាទី)</span>`;

            const timerInterval = setInterval(() => {
                timeLeft--;
                if (countdownEl) {
                    countdownEl.innerText = timeLeft;
                }
                if (submitBtn) {
                    submitBtn.innerHTML = `<i class="fa-solid fa-hourglass-half text-sm animate-pulse"></i> <span>សូមរង់ចាំ (${timeLeft} វិនាទី)</span>`;
                }

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-60', 'cursor-not-allowed', 'pointer-events-none');
                    submitBtn.innerHTML = origSubmitHtml;
                    if (lockoutAlert) {
                        lockoutAlert.className = 'p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-medium animate-in fade-in duration-300';
                        lockoutAlert.innerHTML = `
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                <span>អ្នកអាចសាកល្បងបញ្ចូលពាក្យសម្ងាត់ដើម្បីចូលម្តងទៀតបានហើយ!</span>
                            </div>
                        `;
                    }
                }
            }, 1000);
        }
    })();
    @endif
</script>
@endpush
