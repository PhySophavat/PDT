<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'LMSTICK - School Management System')</title>
  
  <!-- Typography matching Attendance Reference -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <script>
    window.__INITIAL_USER__ = {
      name: "{{ Auth::user()?->name ?? 'Stevne Zone' }}",
      role: "{{ Auth::user()?->roles?->first()?->name ?? 'Admin' }}",
      email: "{{ Auth::user()?->email ?? 'admin@example.com' }}",
      avatar: "{{ Auth::user()?->avatar ?? '' }}"
    };
    @php
      $logoPath = \App\Models\SystemSetting::get('school_logo');
      $logoUrl = $logoPath ? (str_starts_with($logoPath, 'http') || str_starts_with($logoPath, 'data:') ? $logoPath : asset('storage/' . ltrim($logoPath, '/'))) : '';
    @endphp
    window.__INITIAL_SETTINGS__ = {
      school_logo_url: "{{ $logoUrl }}",
      school_name: "{{ \App\Models\SystemSetting::get('school_name', 'សាលារៀន ជំនាន់ថ្មី PDT') }}",
      school_name_en: "{{ \App\Models\SystemSetting::get('school_name_en', 'PDT New Generation School') }}"
    };
  </script>
</head>
<body class="bg-[#dce4f7] min-h-screen p-2 sm:p-4 lg:p-6 flex items-center justify-center antialiased text-slate-800">
  <!-- Vue.js 3 Master Single Page Application Mount Point -->
  <div id="app" class="w-full flex items-center justify-center">
    @hasSection('content')
      <!-- Fallback for non-JS / search engines -->
      <noscript>
        @yield('content')
      </noscript>
    @endif
  </div>

  @stack('scripts')
</body>
</html>
