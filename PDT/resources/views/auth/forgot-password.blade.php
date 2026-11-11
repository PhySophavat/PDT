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

    <title>ភ្លេចពាក្យសម្ងាត់ | portal sala - {{ $schoolName }}</title>
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
    </style>
</head>
<body class="bg-slate-900 font-khmer text-slate-800 antialiased selection:bg-blue-600 selection:text-white min-h-screen">

    <div class="min-h-screen flex flex-col lg:flex-row">

        <!-- LEFT PANEL: Educational Showcase (Desktop) -->
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
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-400/20 text-amber-300 text-xs font-bold">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>ប្រព័ន្ធសុវត្ថិភាពការពារគណនី • Account Security</span>
                </div>

                <div class="space-y-3">
                    <h2 class="text-3xl xl:text-4xl font-extrabold text-white leading-tight">
                        ស្រោចស្រង់ និងកំណត់ <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-300 to-amber-300">
                            ពាក្យសម្ងាត់ថ្មីដោយសុវត្ថិភាព
                        </span>
                    </h2>
                    <p class="text-sm text-slate-300 font-medium leading-relaxed">
                        តំណភ្ជាប់ និងលេខកូដ OTP កំណត់ពាក្យសម្ងាត់ថ្មីនឹងត្រូវផ្ញើទៅកាន់អ៊ីមែលផ្លូវការរបស់អ្នកដោយផ្ទាល់។ កូដនីមួយៗត្រូវបានបង្កើតឡើងដោយកូដសុវត្ថិភាពខ្ពស់ មានសុពលភាពត្រឹមតែ ៣ នាទី និងអាចប្រើប្រាស់បានតែម្តងគត់។
                    </p>
                </div>

                <!-- 3 Steps Visual Guide -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white/[0.04] border border-white/10">
                        <div class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-xs shrink-0">១</div>
                        <div>
                            <h4 class="text-xs font-bold text-white">បញ្ចូលអ៊ីមែល ឬអត្តលេខសិស្ស</h4>
                            <p class="text-[11px] text-slate-400">ប្រព័ន្ធនឹងស្វែងរកគណនីរបស់អ្នកនៅក្នុងមូលដ្ឋានទិន្នន័យ</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white/[0.04] border border-white/10">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs shrink-0">២</div>
                        <div>
                            <h4 class="text-xs font-bold text-white">ពិនិត្យមើលប្រអប់សំបុត្រ (Inbox / Spam)</h4>
                            <p class="text-[11px] text-slate-400">បើកសារដែលមានចំណងជើងថា «🔒 សំណើកំណត់ពាក្យសម្ងាត់ថ្មី»</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white/[0.04] border border-white/10">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-xs shrink-0">៣</div>
                        <div>
                            <h4 class="text-xs font-bold text-white">កំណត់ និងបញ្ជាក់ពាក្យសម្ងាត់ថ្មី</h4>
                            <p class="text-[11px] text-slate-400">បញ្ចូលពាក្យសម្ងាត់ថ្មីយ៉ាងតិច ៨ ខ្ទង់ រួចចូលប្រើប្រព័ន្ធភ្លាមៗ</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Left -->
            <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                <span>ផ្នែកជំនួយបច្ចេកទេសសាលា៖ 012 345 678</span>
                <span class="text-emerald-400 font-bold">256-bit SSL Protected</span>
            </div>

        </div>

        <!-- RIGHT PANEL: Form Card -->
        <div class="w-full lg:w-1/2 xl:w-5/12 flex-1 flex flex-col justify-between bg-slate-50 min-h-screen relative overflow-y-auto">
            
            <!-- Mobile Top Bar -->
            <div class="lg:hidden w-full px-6 py-5 bg-slate-900 border-b border-slate-800 flex items-center justify-between text-white">
                <a href="{{ route('login') }}" class="flex items-center gap-3">
                    @if(!empty($logoUrl))
                        <img src="{{ $logoUrl }}" alt="Logo" class="w-9 h-9 object-contain rounded-xl" onerror="this.onerror=null; this.src='/images/logo.png';">
                    @else
                        <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    @endif
                    <div>
                        <span class="text-lg font-black font-sans">portal sala</span>
                        <span class="block text-[10px] text-slate-400 leading-tight">{{ $schoolName }}</span>
                    </div>
                </a>
                <a href="{{ route('login') }}" class="text-xs font-bold text-blue-400 hover:text-blue-300 flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left text-[11px]"></i>
                    <span>ចូលប្រព័ន្ធ</span>
                </a>
            </div>

            <!-- Form Content -->
            <div class="flex-1 flex items-center justify-center p-6 sm:p-10 lg:p-12">
                <div class="w-full max-w-md space-y-6">

                    <!-- Header -->
                    <div class="space-y-2 text-center sm:text-left">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 transition mb-1 group">
                            <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                            <span>ត្រឡប់ទៅកាន់ទំព័រចូលប្រព័ន្ធ (Back to Login)</span>
                        </a>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            ភ្លេចពាក្យសម្ងាត់?
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium leading-relaxed">
                            សូមបញ្ចូលអាសយដ្ឋានអ៊ីមែលរបស់អ្នក។ យើងនឹងផ្ញើតំណភ្ជាប់ដើម្បីបង្កើតពាក្យសម្ងាត់ថ្មីទៅកាន់អ៊ីមែលរបស់អ្នក។
                        </p>
                    </div>

                    <!-- Alert Notifications -->
                    @if (session('status'))
                        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-medium space-y-1.5 animate-in fade-in duration-200 shadow-xs">
                            <div class="flex items-center gap-2 font-bold text-emerald-700">
                                <i class="fa-solid fa-circle-check text-base text-emerald-600"></i>
                                <span>សារជូនដំណឹងត្រូវបានផ្ញើ!</span>
                            </div>
                            <p class="leading-relaxed pl-6">{{ session('status') }}</p>
                        </div>
                    @elseif (session('error') || $errors->any())
                        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-medium space-y-1 animate-in fade-in duration-200 shadow-xs">
                            <div class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base mt-0.5 shrink-0"></i>
                                <span class="leading-relaxed">{{ session('error') ?: $errors->first() }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Card Form -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 sm:p-8 space-y-5">
                        
                        <form id="forgot-form" action="{{ route('password.email') }}" method="POST" class="space-y-4">
                            @csrf

                            <!-- Email Field (Only Email) -->
                            <div class="space-y-1.5">
                                <label for="email" class="block text-xs font-bold text-slate-700">
                                    <span>អាសយដ្ឋានអ៊ីមែល (Email)</span>
                                    <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative group">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                        <i class="fa-solid fa-envelope text-sm"></i>
                                    </span>
                                    <input 
                                        id="email" 
                                        type="email" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        placeholder="ឧ. name@example.com" 
                                        required
                                        autofocus
                                        autocomplete="email"
                                        class="w-full pl-10 pr-4 py-3 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/15 focus:border-blue-600 transition-all duration-200"
                                    >
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button 
                                type="submit" 
                                id="forgot-submit-btn"
                                class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs sm:text-sm shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/35 active:scale-[0.99] transition-all flex items-center justify-center gap-2.5 cursor-pointer mt-2"
                            >
                                <i class="fa-solid fa-paper-plane text-sm" id="btn-icon"></i>
                                <span id="btn-text">ផ្ញើតំណភ្ជាប់កំណត់ពាក្យសម្ងាត់</span>
                            </button>

                            <!-- Live Feedback -->
                            <div id="forgot-loading-indicator" class="hidden p-3.5 bg-blue-50 border border-blue-200 rounded-2xl text-center space-y-1 animate-in fade-in duration-200">
                                <div class="flex items-center justify-center gap-2 text-xs font-bold text-blue-700">
                                    <i class="fa-solid fa-circle-notch fa-spin text-sm text-blue-600"></i>
                                    <span>កំពុងផ្ញើតំណភ្ជាប់ទៅកាន់អ៊ីមែល...</span>
                                </div>
                                <p class="text-[11px] text-slate-500">
                                    សូមរង់ចាំបន្តិច ប្រព័ន្ធកំពុងផ្ទៀងផ្ទាត់ និងបញ្ជូនសារតាមរយៈ Secure SMTP...
                                </p>
                            </div>

                        </form>

                        <div class="pt-4 border-t border-slate-100 text-center">
                            <a 
                                href="{{ route('login') }}" 
                                class="text-xs font-bold text-slate-600 hover:text-blue-600 inline-flex items-center gap-1.5 transition"
                            >
                                <i class="fa-solid fa-arrow-left text-xs"></i>
                                <span>ត្រឡប់ទៅទំព័រចូលប្រព័ន្ធ</span>
                            </a>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Footer -->
            <footer class="w-full py-5 px-6 border-t border-slate-200/80 bg-white/70 backdrop-blur-md text-center text-xs text-slate-500 space-y-1 shrink-0">
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
        const forgotForm = document.getElementById('forgot-form');
        const forgotSubmitBtn = document.getElementById('forgot-submit-btn');
        const btnIcon = document.getElementById('btn-icon');
        const btnText = document.getElementById('btn-text');
        const loadingIndicator = document.getElementById('forgot-loading-indicator');

        if (forgotForm && forgotSubmitBtn) {
            forgotForm.addEventListener('submit', function (e) {
                if (forgotSubmitBtn.disabled) {
                    e.preventDefault();
                    return;
                }
                if (!forgotForm.checkValidity()) {
                    return;
                }

                forgotSubmitBtn.disabled = true;
                forgotSubmitBtn.classList.add('opacity-80', 'cursor-wait', 'pointer-events-none');
                if (btnIcon) btnIcon.className = 'fa-solid fa-circle-notch fa-spin text-sm';
                if (btnText) btnText.textContent = 'កំពុងផ្ញើសារ...';

                if (loadingIndicator) {
                    loadingIndicator.classList.remove('hidden');
                }
            });
        }
    </script>
</body>
</html>

