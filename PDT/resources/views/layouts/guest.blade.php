<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
      $sysSettings = \App\Models\SystemSetting::allSettings();
      $schoolName = $sysSettings['school_name'] ?? 'សាលារៀន ជំនាន់ថ្មី';
      $schoolNameEn = $sysSettings['school_name_en'] ?? 'New Generation School';
      $logoPath = $sysSettings['school_logo'] ?? '';
      $logoUrl = $logoPath ? (str_starts_with($logoPath, 'http') || str_starts_with($logoPath, 'data:') ? $logoPath : asset('storage/' . ltrim($logoPath, '/'))) : asset('images/logo.png');
      $ogImage = asset('images/og-preview.jpg') . '?v=' . (file_exists(public_path('images/og-preview.jpg')) ? filemtime(public_path('images/og-preview.jpg')) : '20261110');
      $currentUrl = url()->current();
      $defaultDesc = 'ប្រព័ន្ធគ្រប់គ្រងសាលារៀន និងបណ្ណាល័យឌីជីថលកម្ពុជា ' . $schoolName . ' ផ្តល់ជូនសៀវភៅសិក្សាគោល MoEYS ពីថ្នាក់ទី ៧ ដល់ទី ១២ ការគ្រប់គ្រងវត្តមាន ពិន្ទុ និងតារាងចំណាត់ថ្នាក់សិស្ស។';
    @endphp
    <title>@yield('title', $schoolName . ' | ប្រព័ន្ធគ្រប់គ្រងសាលារៀន និងបណ្ណាល័យឌីជីថល')</title>
    <link rel="icon" type="image/png" href="{{ $logoUrl }}">
    <link rel="canonical" href="{{ $currentUrl }}">

    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', $defaultDesc)">
    <meta name="keywords" content="@yield('meta_keywords', 'សាលារៀនជំនាន់ថ្មី, ប្រព័ន្ធគ្រប់គ្រងសាលារៀន, បណ្ណាល័យឌីជីថល, ពិន្ទុប្រឡង, តារាងចំណាត់ថ្នាក់, សៀវភៅពុម្ព MoEYS, New Generation School, Digital Library Cambodia')">
    <meta name="author" content="{{ $schoolName }}">
    <meta name="robots" content="@yield('meta_robots', 'index, follow, max-image-preview:large, max-snippet:-1')">

    <!-- Open Graph / Facebook / Telegram Sharing -->
    <meta property="og:locale" content="km_KH">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $schoolName }}">
    <meta property="og:title" content="@yield('og_title', View::getSection('title', $schoolName . ' | ប្រព័ន្ធគ្រប់គ្រងសាលារៀន និងបណ្ណាល័យឌីជីថល'))">
    <meta property="og:description" content="@yield('og_description', View::getSection('meta_description', $defaultDesc))">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:image" content="@yield('og_image', $ogImage)">
    <meta property="og:image:secure_url" content="@yield('og_image', $ogImage)">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $schoolName }}">

    <!-- Twitter / X Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', View::getSection('title', $schoolName . ' | ប្រព័ន្ធគ្រប់គ្រងសាលារៀន និងបណ្ណាល័យឌីជីថល'))">
    <meta name="twitter:description" content="@yield('og_description', View::getSection('meta_description', $defaultDesc))">
    <meta name="twitter:image" content="@yield('og_image', $ogImage)">

    <!-- Schema.org JSON-LD Structured Data for Google Search -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "EducationalOrganization",
      "name": "{{ $schoolName }}",
      "alternateName": "{{ $schoolNameEn }}",
      "url": "{{ url('/') }}",
      "logo": "{{ $logoUrl }}",
      "description": "{{ $defaultDesc }}",
      "address": {
        "@@type": "PostalAddress",
        "addressCountry": "KH"
      }
    }
    </script>

    <!-- Progressive Web App (PWA) Meta & Links -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="application-name" content="សាលារៀន">
    <meta name="apple-mobile-web-app-title" content="សាលារៀន">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/images/icon-192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/images/icon-512.png">

    <!-- Google Fonts: Kantumruy Pro & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

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
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
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
    </style>
</head>
<body class="bg-gradient-to-br from-slate-100 via-blue-50/40 to-indigo-50/50 min-h-screen flex flex-col justify-between font-khmer text-slate-800 antialiased selection:bg-blue-600 selection:text-white relative overflow-x-hidden">

    <!-- Ambient Gradient Blobs in Background -->
    <div class="fixed -top-32 -left-32 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="fixed -bottom-32 -right-32 w-96 h-96 bg-indigo-400/15 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Header Bar -->
    <header class="w-full px-6 py-5 flex items-center justify-between max-w-6xl mx-auto z-10">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            @if(!empty($logoUrl))
                <img src="{{ $logoUrl }}" alt="Logo" class="w-10 h-10 object-contain rounded-2xl shadow-xs group-hover:scale-105 transition-transform flex-shrink-0" onerror="this.onerror=null; this.src='/images/logo.png';">
            @else
                <!-- Brand Mark Circle -->
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#1e3a8a] to-[#2563eb] flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                    <i class="fa-solid fa-graduation-cap text-lg"></i>
                </div>
            @endif
            <div>
                <span class="text-xl font-extrabold tracking-tight text-slate-900 group-hover:text-blue-700 transition">portal sala</span>
                <span class="block text-[11px] font-semibold text-slate-400 leading-tight">សាលារៀន ជំនាន់ថ្មី</span>
            </div>
        </a>

        <!-- Public links / help -->
        <div class="flex items-center gap-3 text-xs">
            <a href="{{ url('/leave-request') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/80 hover:bg-white text-indigo-700 border border-slate-200/80 rounded-xl font-semibold shadow-xs transition hover:shadow-sm">
                <i class="fa-solid fa-file-pen text-[11px]"></i>
                <span>ទម្រង់សុំច្បាប់</span>
            </a>
        </div>
    </header>

    <!-- Main Content Shell -->
    <main class="w-full max-w-md mx-auto px-4 py-8 z-10 my-auto">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="w-full text-center py-6 text-xs text-slate-400 z-10 space-y-1">
        <p>© {{ date('Y') }} portal sala • សាលារៀន ជំនាន់ថ្មី. រក្សាសិទ្ធិគ្រប់យ៉ាង។</p>
        <p class="text-[11px] text-slate-500 font-medium">Developed by <span class="font-bold text-slate-700">Phy Sophavat</span></p>
    </footer>

    @include('partials.pwa-prompt')
    @stack('scripts')
</body>
</html>
