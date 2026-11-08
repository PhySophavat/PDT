<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'sala-portal')</title>
  
  <!-- Typography matching Attendance Reference -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  @php
    $cachedSettings = \Illuminate\Support\Facades\Cache::remember('portal_app_layout_settings', 300, function () {
        $sysSettings = \App\Models\SystemSetting::allSettings();
        $logoPath = $sysSettings['school_logo'] ?? '';
        $logoUrl = $logoPath ? (str_starts_with($logoPath, 'http') || str_starts_with($logoPath, 'data:') ? $logoPath : (str_starts_with($logoPath, 'images/') ? asset($logoPath) : asset('storage/' . ltrim($logoPath, '/')))) : asset('images/logo.png');
        $activeYear = $sysSettings['current_academic_year'] ?? (\App\Models\AcademicYear::where('is_current', true)->value('name') ?: '2025-2026');
        return [
            'logoUrl' => $logoUrl,
            'school_name' => $sysSettings['school_name'] ?? 'សាលារៀន ជំនាន់ថ្មី',
            'school_name_en' => $sysSettings['school_name_en'] ?? 'New Generation School',
            'activeYear' => $activeYear,
        ];
    });
    $logoUrl = $cachedSettings['logoUrl'];
    $authUser = Auth::user();
    $roleName = 'Student';
    $roleKhmer = 'សិស្សានុសិស្ស';
    $isSuperAdmin = false;
    $studentGrade = null;
    $studentId = null;

    if ($authUser) {
        $isSuperAdmin = $authUser->isSuperAdmin();
        if ($isSuperAdmin) {
            $roleName = 'Super Admin';
            $roleKhmer = 'អគ្គអ្នកគ្រប់គ្រង';
        } elseif ($authUser->hasRole('admin') || strtolower($authUser->role ?? '') === 'admin') {
            $roleName = 'Admin';
            $roleKhmer = 'អ្នកគ្រប់គ្រង';
        } elseif ($authUser->hasRole('teacher') || strtolower($authUser->role ?? '') === 'teacher') {
            $roleName = 'Teacher';
            $roleKhmer = 'គ្រូបង្រៀន';
        } else {
            $roleName = 'Student';
            $roleKhmer = 'សិស្សានុសិស្ស';
            $studentProfile = $authUser->student;
            $studentGrade = $studentProfile?->grade;
            $studentId = $studentProfile?->id;
        }
    }
    $initialUserJson = json_encode([
        'id' => $authUser?->id,
        'name' => $authUser?->name ?? 'portal sala',
        'role' => $roleName,
        'role_khmer' => $roleKhmer,
        'is_super_admin' => $isSuperAdmin,
        'email' => $authUser?->email ?? 'admin@school.edu.kh',
        'avatar' => $authUser?->avatar ?? '',
        'student_grade' => $studentGrade,
        'student_id' => $studentId,
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $initialSettingsJson = json_encode([
        'school_logo_url' => $cachedSettings['logoUrl'],
        'school_name' => $cachedSettings['school_name'],
        'school_name_en' => $cachedSettings['school_name_en'],
        'current_academic_year' => $cachedSettings['activeYear']
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
  @endphp
  <link id="app-favicon" rel="icon" type="image/png" href="{{ $logoUrl }}">
  
  <!-- Progressive Web App (PWA) Meta & Links -->
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#2563eb">
  <meta name="application-name" content="{{ $cachedSettings['school_name'] }}">
  <meta name="apple-mobile-web-app-title" content="{{ $cachedSettings['school_name'] }}">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="apple-touch-icon" href="/images/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="192x192" href="/images/icon-192.png">
  <link rel="icon" type="image/png" sizes="512x512" href="/images/icon-512.png">
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <script>
    window.__INITIAL_USER__ = {!! $initialUserJson !!};
    window.__INITIAL_SETTINGS__ = {!! $initialSettingsJson !!};
  </script>
</head>
<body class="bg-white sm:bg-[#dce4f7] min-h-screen p-0 sm:p-3 lg:p-6 flex items-center justify-center antialiased text-slate-800">
  <!-- Vue.js 3 Master Single Page Application Mount Point -->
  <div id="app" class="w-full h-full min-h-screen flex items-center justify-center">
    <!-- Instant Loading Indicator while JS Chunks Mount -->
    <div class="flex flex-col items-center justify-center min-h-[50vh] gap-3 text-slate-500 font-khmer p-6">
      <div class="w-16 h-16 rounded-2xl bg-white shadow-xl shadow-blue-500/10 border border-slate-200/80 flex items-center justify-center p-2 animate-pulse">
        <img src="/images/logo.png" alt="MoEYS Logo" class="w-full h-full object-contain">
      </div>
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 mt-1">
        <div class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></div>
        <span>កំពុងដំណើរការប្រព័ន្ធ...</span>
      </div>
    </div>
  </div>

  @include('partials.pwa-prompt')
  @stack('scripts')
</body>
</html>
