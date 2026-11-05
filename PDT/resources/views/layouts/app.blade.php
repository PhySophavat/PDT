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
      $sysSettings = \App\Models\SystemSetting::pluck('value', 'key');
      $logoPath = $sysSettings['school_logo'] ?? '';
      $logoUrl = $logoPath ? (str_starts_with($logoPath, 'http') || str_starts_with($logoPath, 'data:') ? $logoPath : asset('storage/' . ltrim($logoPath, '/'))) : '';
    @endphp
    window.__INITIAL_SETTINGS__ = {
      school_logo_url: "{{ $logoUrl }}",
      school_name: "{{ $sysSettings['school_name'] ?? 'សាលារៀន ជំនាន់ថ្មី PDT' }}",
      school_name_en: "{{ $sysSettings['school_name_en'] ?? 'PDT New Generation School' }}"
    };
  </script>
</head>
<body class="bg-[#dce4f7] min-h-screen p-2 sm:p-4 lg:p-6 flex items-center justify-center antialiased text-slate-800">
  <!-- Vue.js 3 Master Single Page Application Mount Point -->
  <div id="app" class="w-full flex items-center justify-center">
    <!-- Instant Loading Indicator while JS Chunks Mount -->
    <div class="flex flex-col items-center justify-center min-h-[50vh] gap-3 text-slate-500 font-khmer p-6">
      <div class="w-14 h-14 rounded-2xl bg-white shadow-xl shadow-blue-500/10 border border-slate-200/80 flex items-center justify-center text-blue-600 text-2xl animate-pulse">
        <i class="fa-solid fa-school"></i>
      </div>
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 mt-1">
        <div class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></div>
        <span>កំពុងដំណើរការប្រព័ន្ធ...</span>
      </div>
    </div>
  </div>

  @stack('scripts')
</body>
</html>
