<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="index, follow">

    @php
      $sysSettings = \App\Models\SystemSetting::allSettings();
      $schoolName = $sysSettings['school_name'] ?? 'សាលារៀន ជំនាន់ថ្មី';
      $schoolNameEn = $sysSettings['school_name_en'] ?? 'New Generation School';
      $logoPath = $sysSettings['school_logo'] ?? '';
      $logoUrl = $logoPath ? (str_starts_with($logoPath, 'http') || str_starts_with($logoPath, 'data:') ? $logoPath : asset('storage/' . ltrim($logoPath, '/'))) : asset('images/logo.png');
      $ogImage = asset('images/og-preview-v2.jpg');
      $pageTitle = $schoolName . ' | ប្រព័ន្ធគ្រប់គ្រងសាលារៀន និងបណ្ណាល័យឌីជីថល';
      $pageDesc = 'ប្រព័ន្ធគ្រប់គ្រងសាលារៀន និងបណ្ណាល័យឌីជីថល ' . $schoolName . ' - វត្តមាន ពិន្ទុ កាលវិភាគ និងបណ្ណាល័យអេឡិចត្រូនិច។';
    @endphp

    <title>ចូលប្រើប្រព័ន្ធ | {{ $schoolName }}</title>
    <link rel="icon" type="image/png" href="{{ $logoUrl }}">
    <link rel="canonical" href="{{ url('/portal') }}">

    <!-- Open Graph / Facebook / Telegram Sharing -->
    <meta property="og:locale" content="km_KH">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $schoolName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:url" content="{{ url('/portal') }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:secure_url" content="{{ $ogImage }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1024">
    <meta property="og:image:height" content="572">
    <meta property="og:image:alt" content="{{ $schoolName }}">

    <!-- Twitter / X Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

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
                    colors: {
                        navy: {
                            800: '#0b192c',
                            900: '#071322',
                            950: '#030a13',
                        },
                        edu: {
                            blue: '#1d4ed8',
                            indigo: '#4338ca',
                            gold: '#f59e0b',
                        }
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
        body {
            font-family: 'Kantumruy Pro', 'Plus Jakarta Sans', sans-serif;
        }
        input, button, select, textarea {
            font-family: inherit;
        }
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .animate-float {
            animation: float-slow 4s ease-in-out infinite;
        }
        .animate-float-delayed {
            animation: float-slow 5s ease-in-out 2s infinite;
        }
    </style>
</head>
<body class="bg-slate-900 font-khmer text-slate-800 antialiased selection:bg-blue-600 selection:text-white min-h-screen">

    <div class="min-h-screen flex flex-col lg:flex-row">

        <!-- ======================================================== -->
        <!-- LEFT PANEL: Educational Showcase & Graphic (Desktop)     -->
        <!-- ======================================================== -->
        <div class="hidden lg:flex lg:w-1/2 xl:w-7/12 relative overflow-hidden bg-gradient-to-br from-slate-950 via-[#071324] to-[#0c1f3c] text-white p-12 xl:p-16 flex-col justify-between select-none">
            
            <!-- Ambient Background Glow & Decorative Elements -->
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#1e3a8a_1px,transparent_1px)] [background-size:24px_24px] opacity-20 pointer-events-none"></div>

            <!-- Top Left: Brand Header -->
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

            <!-- Center Content: Hero Graphic & Slogan -->
            <div class="relative z-10 my-auto py-8 space-y-8 max-w-xl">
                
                <!-- Slogan Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-gradient-to-r from-blue-500/10 via-indigo-500/10 to-amber-500/10 border border-blue-400/20 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    <span class="text-xs font-bold text-blue-200 tracking-wide">Next-Generation School Management Portal</span>
                </div>

                <!-- Main Welcoming Slogan -->
                <div class="space-y-3">
                    <h2 class="text-3xl xl:text-4xl font-extrabold text-white leading-tight tracking-tight">
                        អនាគតនៃការអប់រំឌីជីថល <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-300 to-amber-300">
                            ប្រកបដោយគុណភាព និងឧត្តមភាព
                        </span>
                    </h2>
                    <p class="text-sm text-slate-300 font-medium leading-relaxed">
                        គ្រប់គ្រងការសិក្សា បណ្ណាល័យឌីជីថល វត្តមាន និងពិន្ទុប្រឡងរបស់សិស្សានុសិស្សបានរហ័ស ងាយស្រួល និងសុវត្ថិភាពខ្ពស់ នៅក្នុងប្រព័ន្ធតែមួយ។
                    </p>
                </div>

                <!-- 3 Modern Floating Feature Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-2">
                    <!-- Feature 1 -->
                    <div class="p-4 rounded-2xl bg-white/[0.04] backdrop-blur-md border border-white/10 hover:border-blue-400/40 transition-all hover:-translate-y-1 group">
                        <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-base mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-book-open-reader"></i>
                        </div>
                        <h3 class="text-xs font-bold text-white mb-1">បណ្ណាល័យ MoEYS</h3>
                        <p class="text-[11px] text-slate-400 leading-snug">សៀវភៅពុម្ព និងឯកសារស្រាវជ្រាវគ្រប់កម្រិតថ្នាក់</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-4 rounded-2xl bg-white/[0.04] backdrop-blur-md border border-white/10 hover:border-indigo-400/40 transition-all hover:-translate-y-1 group">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-base mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <h3 class="text-xs font-bold text-white mb-1">តាមដានវត្តមាន & ពិន្ទុ</h3>
                        <p class="text-[11px] text-slate-400 leading-snug">ទិន្នន័យជាក់ស្ដែង Real-Time ជូនអាណាព្យាបាល</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-4 rounded-2xl bg-white/[0.04] backdrop-blur-md border border-white/10 hover:border-amber-400/40 transition-all hover:-translate-y-1 group">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-base mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h3 class="text-xs font-bold text-white mb-1">សុវត្ថិភាពទិន្នន័យ</h3>
                        <p class="text-[11px] text-slate-400 leading-snug">ការពារឯកជនភាព និងទិន្នន័យតាមស្តង់ដារខ្ពស់</p>
                    </div>
                </div>

            </div>

            <!-- Bottom Left: Quote & Info -->
            <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-quote-left text-blue-400 text-sm"></i>
                    <span class="italic text-slate-300">«ការអប់រំគឺជាស្ពានឆ្ពោះទៅកាន់អនាគតដ៏ភ្លឺស្វាង»</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 text-emerald-400 font-bold text-[11px]">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        ប្រព័ន្ធដំណើរការប្រក្រតី
                    </span>
                </div>
            </div>

        </div>

        <!-- ======================================================== -->
        <!-- RIGHT PANEL: Login Form & Card                           -->
        <!-- ======================================================== -->
        <div class="w-full lg:w-1/2 xl:w-5/12 flex-1 flex flex-col justify-between bg-slate-50 min-h-screen relative overflow-y-auto">
            
            <!-- Mobile Top Bar Branding (Visible only on mobile/tablet) -->
            <div class="lg:hidden w-full px-6 py-5 bg-slate-900 border-b border-slate-800 flex items-center justify-between text-white">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    @if(!empty($logoUrl))
                        <img src="{{ $logoUrl }}" alt="Logo" class="w-9 h-9 object-contain rounded-xl" onerror="this.onerror=null; this.src='/images/logo.png';">
                    @else
                        <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    @endif
                    <div>
                        <span class="text-lg font-black font-sans tracking-tight">portal sala</span>
                        <span class="block text-[10px] text-slate-400 leading-tight">{{ $schoolName }}</span>
                    </div>
                </a>
                <a href="{{ url('/register-student') }}" class="text-xs font-bold text-blue-400 hover:text-blue-300 flex items-center gap-1">
                    <span>ចុះឈ្មោះ</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Login Container -->
            <div class="flex-1 flex items-center justify-center p-6 sm:p-10 lg:p-12">
                <div class="w-full max-w-md space-y-6">

                    <!-- Form Header -->
                    <div class="space-y-2 text-center sm:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100 text-xs font-bold">
                            <i class="fa-solid fa-shield-cat text-blue-600"></i>
                            <span>ច្រកចូលសុវត្ថិភាព • SECURE LOGIN</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            ចូលប្រើប្រព័ន្ធ
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium leading-relaxed">
                            សូមបញ្ចូលអាសយដ្ឋានអ៊ីមែល និងពាក្យសម្ងាត់របស់អ្នក ដើម្បីចូលទៅកាន់ផ្ទាំងគ្រប់គ្រង។
                        </p>
                    </div>

                    <!-- Alert Notifications -->
                    @if (session('success'))
                        <div class="flex items-start gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-medium animate-in fade-in duration-200 shadow-xs">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-base mt-0.5 shrink-0"></i>
                            <div class="leading-relaxed">{{ session('success') }}</div>
                        </div>
                    @elseif (session('lockout_seconds') || session('error') || $errors->any())
                        @php
                            $lockoutSecs = (int) session('lockout_seconds', 0);
                            $errorMessage = session('error') ?: $errors->first();
                        @endphp
                        <div id="lockout-alert" class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-medium space-y-2.5 animate-in fade-in duration-200 shadow-xs">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base mt-0.5 shrink-0"></i>
                                <div class="flex-1">
                                    <span id="lockout-text" class="leading-relaxed">{{ $errorMessage }}</span>
                                    @if ($lockoutSecs > 0)
                                        <div class="mt-2.5 flex items-center gap-2 pt-2.5 border-t border-rose-200 text-rose-700">
                                            <i class="fa-solid fa-clock-rotate-left text-xs animate-spin" style="animation-duration: 3s;"></i>
                                            <span>អាចសាកល្បងម្តងទៀតក្នុងរយៈពេល៖ <strong id="countdown-timer" class="font-mono text-sm font-bold text-rose-900 bg-white px-2 py-0.5 rounded-lg border border-rose-200 shadow-2xs">{{ $lockoutSecs }}</strong> វិនាទី</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Login Form Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 sm:p-8 space-y-5">
                        
                        <form id="login-form" action="{{ url('/portal') }}" method="POST" class="space-y-4">
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

                            <!-- Password Field with Show/Hide Toggle -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="password" class="block text-xs font-bold text-slate-700">
                                        <span>ពាក្យសម្ងាត់</span>
                                        <span class="text-rose-500">*</span>
                                    </label>
                                </div>
                                <div class="relative group">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                        <i class="fa-solid fa-lock text-sm"></i>
                                    </span>
                                    <input 
                                        id="password" 
                                        type="password" 
                                        name="password" 
                                        placeholder="បញ្ចូលពាក្យសម្ងាត់យ៉ាងតិច ៨ ខ្ទង់" 
                                        required
                                        autocomplete="current-password"
                                        class="w-full pl-10 pr-11 py-3 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/15 focus:border-blue-600 transition-all duration-200"
                                    >
                                    <button 
                                        type="button" 
                                        id="toggle-password" 
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none transition cursor-pointer"
                                        title="បង្ហាញ/លាក់ពាក្យសម្ងាត់"
                                    >
                                        <i class="fa-regular fa-eye text-sm" id="toggle-icon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Me & Forgot Password Row -->
                            <div class="flex items-center justify-between pt-1">
                                <label class="inline-flex items-center gap-2 cursor-pointer select-none text-xs text-slate-600 hover:text-slate-900 transition">
                                    <input 
                                        type="checkbox" 
                                        name="remember" 
                                        checked 
                                        class="w-4 h-4 rounded-lg text-blue-600 border-slate-300 focus:ring-2 focus:ring-blue-500/20 cursor-pointer"
                                    >
                                    <span class="font-medium">ចងចាំគណនីខ្ញុំ</span>
                                </label>

                                <a 
                                    href="{{ route('password.request') }}" 
                                    class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline transition"
                                >
                                    ភ្លេចពាក្យសម្ងាត់?
                                </a>
                            </div>

                            <!-- Submit Button -->
                            <button 
                                type="submit" 
                                id="login-submit-btn"
                                class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs sm:text-sm shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/35 active:scale-[0.99] transition-all flex items-center justify-center gap-2.5 cursor-pointer mt-2"
                            >
                                <i class="fa-solid fa-arrow-right-to-bracket text-sm" id="btn-icon"></i>
                                <span id="btn-text">ចូលប្រើប្រព័ន្ធ</span>
                            </button>

                            <!-- Live Login Progress Indicator -->
                            <div id="login-loading-indicator" class="hidden p-3.5 bg-blue-50 border border-blue-200 rounded-2xl text-center space-y-1 animate-in fade-in duration-200">
                                <div class="flex items-center justify-center gap-2 text-xs font-bold text-blue-700">
                                    <i class="fa-solid fa-circle-notch fa-spin text-sm text-blue-600"></i>
                                    <span>កំពុងផ្ទៀងផ្ទាត់ និងចូលប្រើប្រព័ន្ធ...</span>
                                </div>
                                <p class="text-[11px] text-slate-500">
                                    សូមរង់ចាំបន្តិច ប្រព័ន្ធកំពុងផ្ទៀងផ្ទាត់ និងរៀបចំផ្ទាំងគ្រប់គ្រង (Dashboard)...
                                </p>
                            </div>

                        </form>

                        <!-- Student Registration Link Divider -->
                        <div class="pt-4 border-t border-slate-100 text-center">
                            <p class="text-xs text-slate-500">
                                មិនទាន់មានគណនីសិស្ស? 
                                <a 
                                    href="{{ route('public.register-student') }}" 
                                    class="font-bold text-blue-600 hover:text-blue-800 hover:underline ml-1 inline-flex items-center gap-1 transition"
                                >
                                    <span>ស្នើសុំចុះឈ្មោះចូលរៀន</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </p>
                        </div>

                    </div>

                    <!-- Security & Privacy Assurance Notice -->
                   

                </div>
            </div>

            <!-- Footer & Branding -->
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

    <!-- Forgot Password Modal Notification -->
    <div id="forgot-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-sm w-full space-y-4 shadow-2xl border border-slate-200 animate-in zoom-in-95 duration-150">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mx-auto border border-amber-100">
                <i class="fa-solid fa-key"></i>
            </div>
            <div class="text-center space-y-1.5">
                <h3 class="text-base font-bold text-slate-800">ភ្លេចពាក្យសម្ងាត់?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    ដើម្បីការពារសុវត្ថិភាពគណនី សូមទំនាក់ទំនងមកកាន់ការិយាល័យរដ្ឋបាលសាលា ឬ Admin ប្រព័ន្ធផ្ទាល់ ដើម្បីកំណត់ពាក្យសម្ងាត់ថ្មីជូនអ្នកឡើងវិញ។
                </p>
            </div>
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-600 space-y-1">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-phone text-blue-600 text-xs"></i>
                    <span class="font-semibold">ផ្នែកជំនួយបច្ចេកទេស៖ 012 345 678</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-envelope text-blue-600 text-xs"></i>
                    <span class="font-semibold">អ៊ីមែល៖ support@portalsala.edu.kh</span>
                </div>
            </div>
            <button 
                type="button" 
                onclick="closeForgotPasswordNotice()" 
                class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition cursor-pointer"
            >
                យល់ព្រម
            </button>
        </div>
    </div>

    <!-- Scripts -->
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

        // Forgot Password Modal
        function showForgotPasswordNotice() {
            const modal = document.getElementById('forgot-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }
        function closeForgotPasswordNotice() {
            const modal = document.getElementById('forgot-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
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
                            lockoutAlert.className = 'p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-medium animate-in fade-in duration-300';
                            lockoutAlert.innerHTML = `
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
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
</body>
</html>
