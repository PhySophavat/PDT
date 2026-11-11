<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    @php
      $sysSettings = \App\Models\SystemSetting::allSettings();
      $schoolName = $sysSettings['school_name'] ?? 'សាលារៀន ជំនាន់ថ្មី';
      $schoolNameEn = $sysSettings['school_name_en'] ?? 'New Generation School';
      $logoPath = $sysSettings['school_logo'] ?? '';
      $logoUrl = $logoPath ? (str_starts_with($logoPath, 'http') || str_starts_with($logoPath, 'data:') ? $logoPath : asset('storage/' . ltrim($logoPath, '/'))) : asset('images/logo.png');
    @endphp

    <title>ផ្ទៀងផ្ទាត់លេខកូដ OTP | portal sala - {{ $schoolName }}</title>
    <link rel="icon" type="image/png" href="{{ $logoUrl }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        khmer: ['"Kantumruy Pro"', '"Plus Jakarta Sans"', 'sans-serif'],
                        sans: ['"Plus Jakarta Sans"', '"Kantumruy Pro"', 'sans-serif'],
                    },
                    boxShadow: {
                        'glow': '0 0 40px -10px rgba(59, 130, 246, 0.3)',
                        'card': '0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 0 1px 1px rgba(15, 23, 42, 0.05)',
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Kantumruy Pro', 'Plus Jakarta Sans', sans-serif; }
        input, button, select, textarea { font-family: inherit; }
        .otp-box {
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .otp-box:focus {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-slate-900 font-khmer text-slate-800 antialiased selection:bg-blue-600 selection:text-white min-h-screen">

    <div class="min-h-screen flex flex-col lg:flex-row">

        <!-- ======================================================== -->
        <!-- LEFT PANEL: Educational Showcase (Desktop)               -->
        <!-- ======================================================== -->
        <div class="hidden lg:flex lg:w-1/2 xl:w-7/12 relative overflow-hidden bg-gradient-to-br from-slate-950 via-[#071324] to-[#0c1f3c] text-white p-12 xl:p-16 flex-col justify-between select-none">
            
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#1e3a8a_1px,transparent_1px)] [background-size:24px_24px] opacity-20 pointer-events-none"></div>

            <!-- Brand Header -->
            <div class="relative z-10 flex items-center gap-3.5">
                @if(!empty($logoUrl))
                    <img src="{{ $logoUrl }}" alt="Logo" class="w-12 h-12 object-contain rounded-2xl bg-white/10 p-1.5 backdrop-blur-md border border-white/15 shadow-glow" onerror="this.onerror=null; this.src='/images/logo.png';">
                @else
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-glow border border-white/20">
                        <i class="fa-solid fa-graduation-cap text-xl"></i>
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black tracking-tight text-white font-sans">portal sala</h1>
                        <span class="px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-300 text-[10px] font-bold border border-blue-400/30">2026</span>
                    </div>
                    <p class="text-xs text-blue-200/80 font-medium">{{ $schoolName }} • {{ $schoolNameEn }}</p>
                </div>
            </div>

            <!-- Center Content -->
            <div class="relative z-10 my-auto py-8 space-y-7 max-w-xl">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 border border-blue-400/20 text-blue-300 text-xs font-bold">
                    <i class="fa-solid fa-shield-halved text-amber-400"></i>
                    <span>ការផ្ទៀងផ្ទាត់សុវត្ថិភាព • Two-Factor OTP</span>
                </div>

                <div class="space-y-3">
                    <h2 class="text-3xl xl:text-4xl font-extrabold text-white leading-tight">
                        ផ្ទៀងផ្ទាត់អត្តសញ្ញាណ <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-300 to-amber-300">
                            តាមរយៈលេខកូដ OTP ៦ ខ្ទង់
                        </span>
                    </h2>
                    <p class="text-sm text-slate-300 font-medium leading-relaxed">
                        លេខកូដ OTP ៦ ខ្ទង់ ត្រូវបានផ្ញើទៅកាន់អ៊ីមែលរបស់អ្នករួចរាល់ហើយ។ សូមពិនិត្យប្រអប់សំបុត្រ (Inbox) ឬក្នុងថតសារឥតបានការ (Spam/Junk) រួចវាយបញ្ចូលលេខកូដដើម្បីបន្ត។
                    </p>
                </div>

                <!-- Security Checklist -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-white/[0.04] border border-white/10">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-stopwatch text-sm"></i>
                        </div>
                        <span class="text-xs text-slate-300">សុពលភាពត្រឹម ៣ នាទី (Expires in 3 mins)</span>
                    </div>
                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-white/[0.04] border border-white/10">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                        </div>
                        <span class="text-xs text-slate-300">អនុញ្ញាតឱ្យវាយខុសអតិបរមា ៥ ដង (Max 5 attempts)</span>
                    </div>
                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-white/[0.04] border border-white/10">
                        <div class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-rotate text-sm"></i>
                        </div>
                        <span class="text-xs text-slate-300">អាចស្នើសុំកូដថ្មីម្តងទៀតក្រោយ ៦០ វិនាទី (Resend in 60s)</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Left -->
            <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                <span>© 2026 portal sala • សាលារៀន ជំនាន់ថ្មី</span>
                <span class="text-emerald-400 font-bold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Brevo SMTP Secure Delivery
                </span>
            </div>

        </div>

        <!-- ======================================================== -->
        <!-- RIGHT PANEL: Polished Compact OTP Form Card              -->
        <!-- ======================================================== -->
        <div class="w-full lg:w-1/2 xl:w-5/12 flex-1 flex flex-col justify-between bg-slate-50 min-h-screen relative overflow-y-auto">
            
            <!-- Mobile Top Bar -->
            <div class="lg:hidden w-full px-5 py-4 bg-slate-900 border-b border-slate-800 flex items-center justify-between text-white shrink-0">
                <a href="{{ route('login') }}" class="flex items-center gap-2.5">
                    @if(!empty($logoUrl))
                        <img src="{{ $logoUrl }}" alt="Logo" class="w-8 h-8 object-contain rounded-xl" onerror="this.onerror=null; this.src='/images/logo.png';">
                    @else
                        <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center text-white text-xs">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    @endif
                    <div>
                        <span class="text-base font-black font-sans leading-tight">portal sala</span>
                        <span class="block text-[9px] text-slate-400 leading-tight">{{ $schoolName }}</span>
                    </div>
                </a>
                <a href="{{ route('login') }}" class="text-xs font-bold text-blue-400 hover:text-blue-300 flex items-center gap-1">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>ចូលប្រព័ន្ធ</span>
                </a>
            </div>

            <!-- Main Interactive Card Container -->
            <div class="flex-1 flex items-center justify-center p-4 sm:p-8 lg:p-10">
                <div class="w-full max-w-md space-y-4 sm:space-y-5">

                    <!-- Header & Compact Email Chip -->
                    <div class="space-y-2 text-center sm:text-left">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100 text-xs font-bold">
                            <i class="fa-solid fa-shield-halved text-blue-600 text-xs"></i>
                            <span>ផ្ទៀងផ្ទាត់សុវត្ថិភាព • VERIFY OTP</span>
                        </div>
                        
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            វាយបញ្ចូលលេខកូដ OTP
                        </h2>

                        <!-- Interactive Email Destination Chip -->
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs text-xs text-slate-600 max-w-full">
                            <i class="fa-solid fa-envelope text-blue-600 shrink-0"></i>
                            <span class="truncate font-semibold text-slate-800">{{ $email }}</span>
                            <a href="{{ route('password.request') }}" class="text-blue-600 hover:text-blue-800 font-bold hover:underline shrink-0 text-[11px] ml-1">
                                កែប្រែ
                            </a>
                        </div>
                    </div>

                    <!-- Clean Compact Status Toast (if any) -->
                    @if (session('status'))
                        <div class="p-3 bg-emerald-50 border border-emerald-200/90 text-emerald-800 rounded-2xl text-xs font-medium flex items-center gap-2.5 animate-in fade-in duration-200 shadow-2xs">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                            <div class="leading-relaxed flex-1">
                                <span>លេខកូដ ៦ ខ្ទង់ ត្រូវបានផ្ញើរួចរាល់! សូមពិនិត្យប្រអប់សំបុត្រ ឬ Spam/Junk។</span>
                            </div>
                        </div>
                    @elseif (session('error') || $errors->any())
                        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-medium flex items-start gap-2.5 animate-in fade-in duration-200 shadow-2xs">
                            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base mt-0.5 shrink-0"></i>
                            <span class="leading-relaxed flex-1">{{ session('error') ?: $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- Elevated White Form Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-5 sm:p-7 space-y-5">
                        
                        <form id="otp-form" action="{{ route('password.otp.verify') }}" method="POST" class="space-y-5">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <input type="hidden" name="otp" id="otp-full" value="">

                            <!-- 6 Split OTP Input Boxes -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-slate-700">លេខកូដ OTP ៦ ខ្ទង់ <span class="text-rose-500">*</span></span>
                                    <span class="text-[11px] text-slate-400 font-medium">សុពលភាព ៣ នាទី</span>
                                </div>
                                
                                <div class="flex items-center justify-between gap-1.5 sm:gap-2.5" id="otp-boxes-container">
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-box w-11 h-14 sm:w-13 sm:h-16 text-center font-mono text-2xl sm:text-3xl font-black text-slate-900 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 focus:outline-none shadow-2xs" autofocus autocomplete="one-time-code">
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-box w-11 h-14 sm:w-13 sm:h-16 text-center font-mono text-2xl sm:text-3xl font-black text-slate-900 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 focus:outline-none shadow-2xs">
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-box w-11 h-14 sm:w-13 sm:h-16 text-center font-mono text-2xl sm:text-3xl font-black text-slate-900 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 focus:outline-none shadow-2xs">
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-box w-11 h-14 sm:w-13 sm:h-16 text-center font-mono text-2xl sm:text-3xl font-black text-slate-900 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 focus:outline-none shadow-2xs">
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-box w-11 h-14 sm:w-13 sm:h-16 text-center font-mono text-2xl sm:text-3xl font-black text-slate-900 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 focus:outline-none shadow-2xs">
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-box w-11 h-14 sm:w-13 sm:h-16 text-center font-mono text-2xl sm:text-3xl font-black text-slate-900 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 focus:outline-none shadow-2xs">
                                </div>

                                <div class="flex items-center justify-center gap-1.5 text-[11px] text-slate-400 pt-0.5">
                                    <i class="fa-regular fa-clipboard text-slate-400 text-xs"></i>
                                    <span>អាចចម្លង (Copy) រួចបិទភ្ជាប់ (Paste) លេខ ៦ ខ្ទង់ដោយផ្ទាល់</span>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button 
                                type="submit" 
                                id="otp-submit-btn"
                                class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs sm:text-sm shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/35 active:scale-[0.99] transition-all flex items-center justify-center gap-2.5 cursor-pointer"
                            >
                                <i class="fa-solid fa-circle-check text-sm" id="btn-icon"></i>
                                <span id="btn-text">ផ្ទៀងផ្ទាត់លេខកូដ OTP</span>
                            </button>

                            <!-- Live Feedback Indicator -->
                            <div id="otp-loading-indicator" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-2xl text-center space-y-1 animate-in fade-in duration-200">
                                <div class="flex items-center justify-center gap-2 text-xs font-bold text-blue-700">
                                    <i class="fa-solid fa-circle-notch fa-spin text-sm text-blue-600"></i>
                                    <span>កំពុងផ្ទៀងផ្ទាត់លេខកូដ...</span>
                                </div>
                                <p class="text-[11px] text-slate-500">
                                    សូមរង់ចាំបន្តិច ប្រព័ន្ធកំពុងផ្ទៀងផ្ទាត់សុពលភាព...
                                </p>
                            </div>

                        </form>

                        <!-- Sleek Resend & Navigation Footer -->
                        <div class="pt-4 border-t border-slate-100 space-y-2.5 text-center">
                            <div class="text-xs text-slate-500 flex items-center justify-center gap-1.5 flex-wrap">
                                <span>មិនទាន់ទទួលបានកូដ?</span>
                                <form action="{{ route('password.email') }}" method="POST" id="resend-form" class="inline">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $email }}">
                                    <button 
                                        type="submit" 
                                        id="resend-btn" 
                                        disabled
                                        class="font-bold text-slate-400 cursor-not-allowed transition inline-flex items-center gap-1 disabled:opacity-75"
                                    >
                                        <i class="fa-solid fa-rotate text-[10px]" id="resend-icon"></i>
                                        <span id="resend-text">ផ្ញើម្តងទៀត (60s)</span>
                                    </button>
                                </form>
                            </div>

                            <div>
                                <a 
                                    href="{{ route('password.request') }}" 
                                    class="text-[11px] font-semibold text-slate-400 hover:text-blue-600 inline-flex items-center gap-1 transition"
                                >
                                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                                    <span>ប្តូរអាសយដ្ឋានអ៊ីមែលផ្សេង</span>
                                </a>
                            </div>
                        </div>

                    </div>

                    <!-- Security Notice Footer -->
                    <div class="text-center text-[11px] text-slate-400 flex items-center justify-center gap-3">
                        <span class="inline-flex items-center gap-1">
                            <i class="fa-solid fa-lock text-[10px]"></i>
                            <span>256-bit SSL Encrypted</span>
                        </span>
                        <span>•</span>
                        <span>វាយខុសអតិបរមា ៥ ដង</span>
                    </div>

                </div>
            </div>

            <!-- Footer -->
            <footer class="w-full py-4 px-6 border-t border-slate-200/80 bg-white/70 backdrop-blur-md text-center text-xs text-slate-500 space-y-0.5 shrink-0">
                <p class="font-medium text-slate-600">
                    © 2026 portal sala • សាលារៀន ជំនាន់ថ្មី.
                </p>
                <p class="text-[11px] text-slate-400">
                    Developed by <span class="font-bold text-slate-700">Phy Sophavat</span>
                </p>
            </footer>

        </div>

    </div>

    <script>
        // 6-digit OTP Input Boxes Logic (Auto-Advance, Backspace, Paste, Dynamic Highlight)
        const boxes = document.querySelectorAll('.otp-box');
        const fullOtpInput = document.getElementById('otp-full');
        const otpForm = document.getElementById('otp-form');

        function updateFullOtp() {
            let full = '';
            boxes.forEach(b => {
                const v = b.value;
                full += v;
                if (v) {
                    b.classList.remove('bg-slate-50', 'border-slate-200', 'text-slate-900');
                    b.classList.add('bg-blue-50/50', 'border-blue-500', 'text-blue-700');
                } else {
                    b.classList.remove('bg-blue-50/50', 'border-blue-500', 'text-blue-700');
                    b.classList.add('bg-slate-50', 'border-slate-200', 'text-slate-900');
                }
            });
            fullOtpInput.value = full;
        }

        boxes.forEach((box, idx) => {
            box.addEventListener('input', (e) => {
                const val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val ? val[0] : '';
                updateFullOtp();

                if (val && idx < boxes.length - 1) {
                    boxes[idx + 1].focus();
                }

                // If all 6 digits entered, auto-submit
                if (fullOtpInput.value.length === 6) {
                    setTimeout(() => {
                        otpForm.requestSubmit();
                    }, 200);
                }
            });

            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !box.value && idx > 0) {
                    boxes[idx - 1].focus();
                }
            });

            // Handle Paste of complete 6-digit OTP
            box.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                if (pasteData) {
                    const digits = pasteData.slice(0, 6).split('');
                    digits.forEach((d, i) => {
                        if (boxes[i]) boxes[i].value = d;
                    });
                    updateFullOtp();
                    const nextIdx = Math.min(digits.length, boxes.length - 1);
                    boxes[nextIdx].focus();

                    if (fullOtpInput.value.length === 6) {
                        setTimeout(() => {
                            otpForm.requestSubmit();
                        }, 200);
                    }
                }
            });
        });

        // Form Submit Loading Feedback
        const otpSubmitBtn = document.getElementById('otp-submit-btn');
        const btnIcon = document.getElementById('btn-icon');
        const btnText = document.getElementById('btn-text');
        const loadingIndicator = document.getElementById('otp-loading-indicator');

        if (otpForm && otpSubmitBtn) {
            otpForm.addEventListener('submit', function (e) {
                updateFullOtp();
                if (fullOtpInput.value.length < 6) {
                    e.preventDefault();
                    alert('សូមវាយបញ្ចូលលេខកូដ OTP ឱ្យគ្រប់ទាំង ៦ ខ្ទង់!');
                    boxes[fullOtpInput.value.length].focus();
                    return;
                }

                otpSubmitBtn.disabled = true;
                otpSubmitBtn.classList.add('opacity-80', 'cursor-wait', 'pointer-events-none');
                if (btnIcon) btnIcon.className = 'fa-solid fa-circle-notch fa-spin text-sm';
                if (btnText) btnText.textContent = 'កំពុងផ្ទៀងផ្ទាត់...';

                if (loadingIndicator) {
                    loadingIndicator.classList.remove('hidden');
                }
            });
        }

        // Resend OTP Countdown 60s Timer (Security Best Practice)
        const resendBtn = document.getElementById('resend-btn');
        const resendText = document.getElementById('resend-text');
        const resendIcon = document.getElementById('resend-icon');
        let resendSeconds = 60;

        const countdownTimer = setInterval(() => {
            resendSeconds--;
            if (resendSeconds > 0) {
                resendText.textContent = `ផ្ញើម្តងទៀត (${resendSeconds}s)`;
            } else {
                clearInterval(countdownTimer);
                resendBtn.disabled = false;
                resendBtn.classList.remove('text-slate-400', 'cursor-not-allowed');
                resendBtn.classList.add('text-blue-600', 'hover:text-blue-800', 'cursor-pointer', 'underline');
                if (resendIcon) resendIcon.classList.add('animate-spin');
                resendText.textContent = 'ផ្ញើលេខកូដម្តងទៀត';
            }
        }, 1000);
    </script>
</body>
</html>
