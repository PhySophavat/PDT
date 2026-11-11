<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @php
    $sysSettings = \App\Models\SystemSetting::allSettings();
    $schoolName = $sysSettings['school_name'] ?? 'សាលារៀន ជំនាន់ថ្មី';
    $schoolNameEn = $sysSettings['school_name_en'] ?? 'New Generation School';
  @endphp
  <title>ចុះឈ្មោះសិស្សថ្មី | {{ $schoolName }}</title>

  <!-- Typography & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
              500: '#1a44e8',
              600: '#1538c2',
              700: '#0f2990',
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
    input, select, textarea, button {
      font-family: inherit;
    }
    .input-error {
      border-color: #ef4444 !important;
      background-color: #fef2f2 !important;
      box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
    }
    .input-valid {
      border-color: #10b981 !important;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-[#e6edfa] via-[#eff4ff] to-[#e1ecfb] min-h-screen py-8 px-3 sm:px-6 flex flex-col items-center justify-center font-khmer text-slate-800 antialiased">

  <div class="w-full max-w-3xl my-2">

    <!-- Top Brand & Title -->
    <div class="text-center mb-6">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/90 backdrop-blur-md rounded-full shadow-xs border border-white mb-2.5">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-xs font-bold tracking-wider text-blue-700 uppercase">{{ $schoolName }}</span>
      </div>
      @if (session('registration_success'))
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
          ប័ណ្ណទទួលពាក្យចុះឈ្មោះចូលរៀន
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-md mx-auto">
          ការចុះឈ្មោះរបស់អ្នកត្រូវបានកត់ត្រា និងកំពុងស្ថិតក្នុងដំណាក់កាលត្រួតពិនិត្យ
        </p>
      @else
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
          ទម្រង់ចុះឈ្មោះសិស្សថ្មី 
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-md mx-auto">
          សូមបំពេញព័ត៌មានខាងក្រោមតាមជំហាននីមួយៗ ដើម្បីស្នើសុំចុះឈ្មោះចូលរៀនក្នុងឆ្នាំសិក្សាថ្មី
        </p>
      @endif
    </div>

    @if (session('registration_success'))
      <!-- Success Confirmation Screen -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/90 p-6 sm:p-10 text-center transition-all animate-fadeIn relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-blue-600"></div>

        <div class="relative w-20 h-20 mx-auto mb-4 flex items-center justify-center">
          <div class="absolute inset-0 rounded-full bg-emerald-100 animate-ping opacity-60"></div>
          <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-3xl shadow-lg shadow-emerald-500/30 relative z-10">
            <i class="fa-solid fa-check"></i>
          </div>
        </div>

        <div class="inline-flex items-center gap-1.5 px-3.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold rounded-full uppercase tracking-wider mb-2">
          <i class="fa-solid fa-circle-check text-emerald-600"></i>
          <span>បញ្ជូនពាក្យស្នើសុំជោគជ័យ</span>
        </div>

        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1 mb-2">
          ការចុះឈ្មោះត្រូវបានបញ្ជូនដោយជោគជ័យ!
        </h2>
        <p class="text-xs sm:text-sm text-slate-600 max-w-lg mx-auto mb-6 leading-relaxed">
          សូមអរគុណ! ព័ត៌មានរបស់សិស្ស <strong>{{ session('registered_name') }}</strong> ត្រូវបានរក្សាទុកក្នុងប្រព័ន្ធរួចរាល់។
        </p>

        <!-- Summary Receipt Card -->
        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 sm:p-5 max-w-xl mx-auto text-left mb-6 space-y-3">
          <div class="flex items-center justify-between pb-2.5 border-b border-slate-200 text-xs">
            <span class="text-slate-500 font-medium">លេខកូដពាក្យស្នើសុំ៖</span>
            <span class="font-bold text-blue-700 font-mono text-sm">#REG-{{ session('registered_id') ?? date('Ymd') . '-01' }}</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
            <div>
              <span class="text-slate-500 text-[11px] block">ឈ្មោះសិស្ស៖</span>
              <span class="font-bold text-slate-800 text-xs block">{{ session('registered_name') ?? '---' }}</span>
            </div>
            <div>
              <span class="text-slate-500 text-[11px] block">ថ្នាក់ដែលស្នើសុំ៖</span>
              <span class="font-bold text-blue-700 text-xs block">ថ្នាក់ទី {{ session('registered_grade') ?? '---' }}</span>
            </div>
            <div>
              <span class="text-slate-500 text-[11px] block">លេខទូរស័ព្ទ៖</span>
              <span class="font-bold text-slate-800 text-xs block font-mono">{{ session('registered_phone') ?? '---' }}</span>
            </div>
            <div>
              <span class="text-slate-500 text-[11px] block">អ៊ីមែលទំនាក់ទំនង៖</span>
              <span class="font-bold text-slate-800 text-xs block font-mono break-all">{{ session('registered_email') ?? '---' }}</span>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center justify-center gap-3">
          <a 
            href="{{ route('login') }}" 
            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-2"
          >
            <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
            <span>ត្រឡប់ទៅទំព័រចូលប្រព័ន្ធ</span>
          </a>
          <a 
            href="{{ route('public.register-student') }}" 
            class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-2"
          >
            <i class="fa-solid fa-user-plus text-xs"></i>
            <span>ចុះឈ្មោះសិស្សថ្មីម្នាក់ទៀត</span>
          </a>
        </div>
      </div>
    @else
      <!-- Multi-step Registration Card -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/90 overflow-hidden">
        
        <!-- Top Ribbon -->
        <div class="bg-[#1a44e8] px-6 py-4 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-lg shadow-inner">
              <i class="fa-solid fa-id-card"></i>
            </div>
            <div>
              <h3 class="font-bold text-sm sm:text-base leading-tight">ចុះឈ្មោះសិស្សថ្មី</h3>
              <p class="text-[11px] text-blue-100">បំពេញ ៣ ជំហានងាយៗ រហ័ស និងមានសុវត្ថិភាព</p>
            </div>
          </div>
          <span class="text-xs bg-white/20 px-3 py-1 rounded-full text-white font-semibold">
            ឆ្នាំ ២០២៦
          </span>
        </div>

        <!-- STEPPER PROGRESS BAR -->
        <div class="px-6 sm:px-10 pt-6 pb-2 border-b border-slate-100 bg-slate-50/60">
          <div class="relative flex items-center justify-between max-w-lg mx-auto">
            <!-- Background connecting line -->
            <div class="absolute top-1/2 left-0 right-0 h-1 bg-slate-200 -translate-y-1/2 z-0"></div>
            <!-- Dynamic filled progress line -->
            <div id="step-progress-line" class="absolute top-1/2 left-0 h-1 bg-blue-600 -translate-y-1/2 transition-all duration-300 z-0" style="width: 0%;"></div>

            <!-- Step 1 Button -->
            <div class="step-nav-item relative z-10 flex flex-col items-center cursor-pointer" onclick="goToStep(1)">
              <div id="step-circle-1" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-bold text-xs sm:text-sm bg-blue-600 text-white ring-4 ring-blue-100 transition shadow-sm">
                1
              </div>
              <span id="step-label-1" class="text-[11px] sm:text-xs font-bold text-blue-700 mt-1.5 whitespace-nowrap">ព័ត៌មានសិស្ស</span>
            </div>

            <!-- Step 2 Button -->
            <div class="step-nav-item relative z-10 flex flex-col items-center cursor-pointer" onclick="goToStep(2)">
              <div id="step-circle-2" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-bold text-xs sm:text-sm bg-slate-200 text-slate-500 transition shadow-sm">
                2
              </div>
              <span id="step-label-2" class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-1.5 whitespace-nowrap">អាណាព្យាបាល</span>
            </div>

            <!-- Step 3 Button -->
            <div class="step-nav-item relative z-10 flex flex-col items-center cursor-pointer" onclick="goToStep(3)">
              <div id="step-circle-3" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-bold text-xs sm:text-sm bg-slate-200 text-slate-500 transition shadow-sm">
                3
              </div>
              <span id="step-label-3" class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-1.5 whitespace-nowrap">ផ្ទៀងផ្ទាត់ & បញ្ជូន</span>
            </div>
          </div>
        </div>

        @if ($errors->any())
          <div class="p-4 mx-6 mt-6 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-xs">
            <div class="flex items-center gap-2 font-bold mb-1">
              <i class="fa-solid fa-triangle-exclamation"></i>
              <span>សូមពិនិត្យព័ត៌មានឡើងវិញ៖</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form id="registration-form" action="{{ route('public.register-student.post') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8">
          @csrf

          <!-- Honeypot Field for Spam Bot Protection (Hidden from legitimate users) -->
          <div class="hidden" style="display:none !important;" aria-hidden="true">
            <label for="hp_website_trap">Leave this blank</label>
            <input type="text" name="hp_website_trap" id="hp_website_trap" tabindex="-1" autocomplete="off" value="" />
          </div>

          <!-- ============================================== -->
          <!-- STEP 1: STUDENT INFORMATION                   -->
          <!-- ============================================== -->
          <div id="step-pane-1" class="space-y-5 animate-fade-in">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
              <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">១</span>
                <h2 class="text-base font-bold text-slate-800">ព័ត៌មានផ្ទាល់ខ្លួនសិស្ស</h2>
              </div>
              <span class="text-xs text-slate-400">ជំហាន ១ នៃ ៣</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Khmer Name -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  ឈ្មោះពេញជាភាសាខ្មែរ <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-user text-xs"></i>
                  </span>
                  <input 
                    type="text" 
                    id="name_khmer"
                    name="name_khmer" 
                    value="{{ old('name_khmer') }}" 
                    placeholder="ឧ. សុខ សុភក្ត្រ"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  />
                </div>
                <p id="error-name_khmer" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1 font-medium">
                  <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                  <span>សូមបញ្ចូលឈ្មោះពេញជាភាសាខ្មែរ</span>
                </p>
              </div>

              <!-- Latin Name -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  ឈ្មោះជាអក្សរឡាតាំង <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-signature text-xs"></i>
                  </span>
                  <input 
                    type="text" 
                    id="name_latin"
                    name="name_latin" 
                    value="{{ old('name_latin') }}" 
                    placeholder="e.g. SOK SOPHEAK"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 uppercase transition"
                  />
                </div>
                <p id="error-name_latin" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1 font-medium">
                  <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                  <span>សូមបញ្ចូលឈ្មោះជាអក្សរឡាតាំង (A-Z)</span>
                </p>
              </div>

              <!-- Gender -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  ភេទ <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                  <label class="flex items-center justify-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold cursor-pointer hover:bg-blue-50/50 has-checked:bg-blue-50 has-checked:border-blue-500 has-checked:text-blue-600 transition">
                    <input type="radio" name="gender" value="male" {{ old('gender', 'male') === 'male' ? 'checked' : '' }} class="text-blue-600 focus:ring-0">
                    <span><i class="fa-solid fa-mars text-blue-500 mr-1"></i> ប្រុស</span>
                  </label>
                  <label class="flex items-center justify-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold cursor-pointer hover:bg-pink-50/50 has-checked:bg-pink-50 has-checked:border-pink-500 has-checked:text-pink-600 transition">
                    <input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }} class="text-pink-600 focus:ring-0">
                    <span><i class="fa-solid fa-venus text-pink-500 mr-1"></i> ស្រី</span>
                  </label>
                </div>
              </div>

              <!-- Date of Birth -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  ថ្ងៃខែឆ្នាំកំណើត <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-regular fa-calendar text-xs"></i>
                  </span>
                  <input 
                    type="date" 
                    id="date_of_birth"
                    name="date_of_birth" 
                    value="{{ old('date_of_birth') }}" 
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  />
                </div>
                <p id="error-date_of_birth" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1 font-medium">
                  <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                  <span>សូមជ្រើសរើសថ្ងៃខែឆ្នាំកំណើត</span>
                </p>
              </div>

              <!-- Student Phone -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  លេខទូរស័ព្ទសិស្ស <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-phone text-xs"></i>
                  </span>
                  <input 
                    type="tel" 
                    id="phone"
                    name="phone" 
                    value="{{ old('phone') }}" 
                    placeholder="ឧ. 012 345 678 "
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition font-mono"
                  />
                </div>
                <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1 font-khmer">
                  <i class="fa-solid fa-circle-info text-blue-500 text-[10px]"></i>
                  <span>សូមបញ្ចូលលេខជាភាសាអង់គ្លេស (0-9) ឧ. 012345678 (កុំប្រើលេខខ្មែរ ០-៩)</span>
                </p>
                <p id="error-phone" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1 font-medium">
                  <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                  <span>លេខទូរស័ព្ទមិនត្រឹមត្រូវ (សូមប្រើលេខជាភាសាអង់គ្លេស 0-9 ចាប់ផ្ដើមដោយលេខ 0 មាន 9 ទៅ 10 ខ្ទង់)</span>
                </p>
              </div>

              <!-- Student Email -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  អ៊ីមែលសិស្ស (សម្រាប់ផ្ញើគណនីចូលរៀន) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-envelope text-xs"></i>
                  </span>
                  <input 
                    type="email" 
                    id="email"
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="ឧ. student@example.com"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition font-mono"
                  />
                </div>
                <p id="error-email" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1 font-medium">
                  <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                  <span>សូមបញ្ចូលទម្រង់អ៊ីមែលឱ្យបានត្រឹមត្រូវ (ឧ. name@gmail.com)</span>
                </p>
              </div>

              <!-- Student ID Card (optional) -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                  <span>អត្តលេខសិស្ស (Student ID)</span>
                  <span class="text-[11px] text-slate-400 font-normal">(ប្រសិនបើមាន ឬទុកទំនេរ)</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-id-badge text-xs"></i>
                  </span>
                  <input 
                    type="text" 
                    id="student_id_card"
                    name="student_id_card" 
                    value="{{ old('student_id_card') }}" 
                    placeholder="ឧ. 1234"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition font-mono"
                  />
                </div>
              </div>

              <!-- Requested Grade -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  កម្រិតថ្នាក់ដែលចង់ស្នើសុំចូលរៀន <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-graduation-cap text-xs"></i>
                  </span>
                  <select 
                    id="grade"
                    name="grade" 
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  >
                    <option value="">-- សូមជ្រើសរើសថ្នាក់រៀន --</option>
                    @foreach ($classes as $cl)
                      <option value="{{ $cl }}" {{ old('grade') === $cl ? 'selected' : '' }}>
                        ថ្នាក់ {{ $cl }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <p id="error-grade" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1 font-medium">
                  <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                  <span>សូមជ្រើសរើសកម្រិតថ្នាក់ដែលចង់ចូលរៀន</span>
                </p>
              </div>

              <!-- Current Address -->
              <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  អាសយដ្ឋានបច្ចុប្បន្ន <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute top-3 left-3 text-slate-400">
                    <i class="fa-solid fa-location-dot text-xs"></i>
                  </span>
                  <textarea 
                    id="address"
                    name="address" 
                    rows="2" 
                    placeholder="ផ្ទះលេខ ផ្លូវ ភូមិ ឃុំ/សង្កាត់ ក្រុង/ស្រុក/ខណ្ឌ ខេត្ត/រាជធានី"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition resize-none"
                  >{{ old('address') }}</textarea>
                </div>
                <p id="error-address" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1 font-medium">
                  <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                  <span>សូមបញ្ចូលអាសយដ្ឋានបច្ចុប្បន្ន</span>
                </p>
              </div>

              <!-- Photo Upload -->
              <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  រូបថតសិស្ស <span class="text-slate-400 font-normal">(មិនទាមទារជាចាំបាច់)</span>
                </label>
                <div class="flex items-center gap-4 p-3 bg-slate-50 border border-dashed border-slate-300 rounded-2xl hover:border-blue-400 transition">
                  <div id="photo-preview-box" class="w-14 h-14 rounded-xl bg-slate-200 flex items-center justify-center text-slate-400 overflow-hidden flex-shrink-0 border border-slate-300">
                    <i class="fa-regular fa-image text-xl"></i>
                  </div>
                  <div class="flex-1">
                    <input 
                      type="file" 
                      id="photo-input" 
                      name="photo" 
                      accept="image/*" 
                      class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                    />
                    <p class="text-[11px] text-slate-400 mt-1">ទំហំអតិបរមា 3MB (ប្រភេទ PNG, JPG, JPEG, WebP)</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 1 Navigation Buttons -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end">
              <button 
                type="button" 
                onclick="validateAndGoNext(1)"
                class="py-3 px-6 bg-[#1a44e8] hover:bg-[#1538c2] text-white text-xs font-bold rounded-2xl shadow-md shadow-blue-600/20 active:scale-[0.99] transition flex items-center gap-2 cursor-pointer"
              >
                <span>បន្ទាប់៖ ព័ត៌មានអាណាព្យាបាល</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
              </button>
            </div>
          </div>

          <!-- ============================================== -->
          <!-- STEP 2: GUARDIAN INFORMATION                  -->
          <!-- ============================================== -->
          <div id="step-pane-2" class="space-y-5 hidden animate-fade-in">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
              <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">២</span>
                <h2 class="text-base font-bold text-slate-800">ព័ត៌មានឪពុកម្តាយ ឬអាណាព្យាបាល</h2>
              </div>
              <span class="text-xs text-slate-400">ជំហាន ២ នៃ ៣</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Guardian Name -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  ឈ្មោះឪពុក/ម្តាយ/អាណាព្យាបាល <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-person text-xs"></i>
                  </span>
                  <input 
                    type="text" 
                    id="parent_name"
                    name="parent_name" 
                    value="{{ old('parent_name') }}" 
                    placeholder="ឈ្មោះពេញរបស់អាណាព្យាបាល"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  />
                </div>
                <p id="error-parent_name" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1 font-medium">
                  <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                  <span>សូមបញ្ចូលឈ្មោះអាណាព្យាបាល</span>
                </p>
              </div>

              <!-- Relationship -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  ត្រូវជាអ្វីនឹងសិស្ស <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-people-roof text-xs"></i>
                  </span>
                  <select 
                    id="parent_relationship"
                    name="parent_relationship" 
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  >
                    <option value="ឪពុក" {{ old('parent_relationship', 'ឪពុក') === 'ឪពុក' ? 'selected' : '' }}>ឪពុក</option>
                    <option value="ម្តាយ" {{ old('parent_relationship') === 'ម្តាយ' ? 'selected' : '' }}>ម្តាយ</option>
                    <option value="អាណាព្យាបាល" {{ old('parent_relationship') === 'អាណាព្យាបាល' ? 'selected' : '' }}>អាណាព្យាបាល</option>
                  </select>
                </div>
              </div>

              <!-- Guardian Phone -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  លេខទូរស័ព្ទអាណាព្យាបាល <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-phone-volume text-xs"></i>
                  </span>
                  <input 
                    type="tel" 
                    id="parent_phone"
                    name="parent_phone" 
                    value="{{ old('parent_phone') }}" 
                    placeholder="ឧ. 012 888 999 (លេខ 0-9 មាន 9 ទៅ 10 ខ្ទង់)"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition font-mono"
                  />
                </div>
                <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1 font-khmer">
                  <i class="fa-solid fa-circle-info text-blue-500 text-[10px]"></i>
                  <span>សូមបញ្ចូលលេខជាភាសាអង់គ្លេស (0-9) ឧ. 012888999 (កុំប្រើលេខខ្មែរ ០-៩)</span>
                </p>
                <p id="error-parent_phone" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1 font-medium">
                  <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                  <span>លេខទូរស័ព្ទមិនត្រឹមត្រូវ (សូមប្រើលេខជាភាសាអង់គ្លេស 0-9 ចាប់ផ្ដើមដោយលេខ 0 មាន 9 ទៅ 10 ខ្ទង់)</span>
                </p>
              </div>

              <!-- Guardian Occupation -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  មុខរបរអាណាព្យាបាល <span class="text-slate-400 font-normal">(មិនទាមទារជាចាំបាច់)</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-briefcase text-xs"></i>
                  </span>
                  <input 
                    type="text" 
                    id="parent_occupation"
                    name="parent_occupation" 
                    value="{{ old('parent_occupation') }}" 
                    placeholder="ឧ. មន្ត្រីរាជការ, អាជីវករ, កសិករ"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  />
                </div>
              </div>
            </div>

            <!-- Step 2 Navigation Buttons -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-between gap-3">
              <button 
                type="button" 
                onclick="goToStep(1)"
                class="py-3 px-5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-2xl transition flex items-center gap-2 cursor-pointer"
              >
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>ត្រឡប់ក្រោយ</span>
              </button>
              <button 
                type="button" 
                onclick="validateAndGoNext(2)"
                class="py-3 px-6 bg-[#1a44e8] hover:bg-[#1538c2] text-white text-xs font-bold rounded-2xl shadow-md shadow-blue-600/20 active:scale-[0.99] transition flex items-center gap-2 cursor-pointer"
              >
                <span>បន្ទាប់៖ ផ្ទៀងផ្ទាត់ & បញ្ជូន</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
              </button>
            </div>
          </div>

          <!-- ============================================== -->
          <!-- STEP 3: REVIEW & SUBMIT                       -->
          <!-- ============================================== -->
          <div id="step-pane-3" class="space-y-5 hidden animate-fade-in">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
              <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xs">៣</span>
                <h2 class="text-base font-bold text-slate-800">ផ្ទៀងផ្ទាត់ព័ត៌មាន និងបញ្ជូនពាក្យ</h2>
              </div>
              <span class="text-xs text-slate-400">ជំហាន ៣ នៃ ៣</span>
            </div>

            <div class="p-4 bg-amber-50/70 border border-amber-200/80 rounded-2xl text-xs text-amber-900 flex items-start gap-2.5">
              <i class="fa-solid fa-circle-info text-amber-600 text-sm mt-0.5 shrink-0"></i>
              <p>សូមពិនិត្យផ្ទៀងផ្ទាត់ព័ត៌មានខាងក្រោមឱ្យបានច្បាស់លាស់។ ប្រសិនបើមានចំណុចខុសឆ្គង លោកអ្នកអាចចុចប៊ូតុង <strong>«កែសម្រួល»</strong> ដើម្បីកែឡើងវិញបាន។</p>
            </div>

            <!-- Review Summary Cards -->
            <div class="space-y-4">
              <!-- Student Review Card -->
              <div class="p-4 sm:p-5 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                  <h3 class="text-xs font-bold text-blue-700 uppercase tracking-wide flex items-center gap-1.5">
                    <i class="fa-solid fa-user-graduate"></i>
                    <span>ព័ត៌មានសិស្ស</span>
                  </h3>
                  <button type="button" onclick="goToStep(1)" class="text-[11px] text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1 cursor-pointer">
                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                    <span>កែសម្រួល</span>
                  </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                  <div>
                    <span class="text-slate-400 text-[11px] block">ឈ្មោះពេញ (ខ្មែរ / Latin)៖</span>
                    <span id="review-names" class="font-bold text-slate-800 block text-sm">---</span>
                  </div>
                  <div>
                    <span class="text-slate-400 text-[11px] block">ភេទ / ថ្ងៃខែឆ្នាំកំណើត៖</span>
                    <span id="review-gender-dob" class="font-bold text-slate-800 block">---</span>
                  </div>
                  <div>
                    <span class="text-slate-400 text-[11px] block">លេខទូរស័ព្ទ / អ៊ីមែល៖</span>
                    <span id="review-contact" class="font-bold text-slate-800 block font-mono">---</span>
                  </div>
                  <div>
                    <span class="text-slate-400 text-[11px] block">ថ្នាក់ដែលស្នើសុំចូលរៀន៖</span>
                    <span id="review-grade" class="font-black text-blue-700 block">---</span>
                  </div>
                  <div class="sm:col-span-2">
                    <span class="text-slate-400 text-[11px] block">អាសយដ្ឋានបច្ចុប្បន្ន៖</span>
                    <span id="review-address" class="text-slate-700 block font-medium">---</span>
                  </div>
                </div>
              </div>

              <!-- Guardian Review Card -->
              <div class="p-4 sm:p-5 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                  <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-wide flex items-center gap-1.5">
                    <i class="fa-solid fa-people-roof"></i>
                    <span>ព័ត៌មានអាណាព្យាបាល</span>
                  </h3>
                  <button type="button" onclick="goToStep(2)" class="text-[11px] text-emerald-600 hover:text-emerald-800 font-bold flex items-center gap-1 cursor-pointer">
                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                    <span>កែសម្រួល</span>
                  </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                  <div>
                    <span class="text-slate-400 text-[11px] block">ឈ្មោះអាណាព្យាបាល៖</span>
                    <span id="review-parent-name" class="font-bold text-slate-800 block text-sm">---</span>
                  </div>
                  <div>
                    <span class="text-slate-400 text-[11px] block">ត្រូវជា / មុខរបរ៖</span>
                    <span id="review-parent-rel" class="font-bold text-slate-800 block">---</span>
                  </div>
                  <div>
                    <span class="text-slate-400 text-[11px] block">លេខទូរស័ព្ទទំនាក់ទំនង៖</span>
                    <span id="review-parent-phone" class="font-bold text-slate-800 block font-mono">---</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Agreement Checkbox -->
            <div class="p-3.5 bg-blue-50/50 border border-blue-200/60 rounded-2xl">
              <label class="flex items-start gap-2.5 text-xs text-slate-700 cursor-pointer">
                <input type="checkbox" id="terms_agree" required class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 mt-0.5">
                <span>ខ្ញុំសូមបញ្ជាក់ថាព័ត៌មានដែលបានបំពេញខាងលើទាំងអស់ពិតជាត្រឹមត្រូវពិតប្រាកដមែន ហើយយល់ព្រមគោរពតាមបទបញ្ជាផ្ទៃក្នុងរបស់សាលារៀន។</span>
              </label>
            </div>

            <!-- Step 3 Navigation Buttons -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-between gap-3">
              <button 
                type="button" 
                onclick="goToStep(2)"
                class="py-3 px-5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-2xl transition flex items-center gap-2 cursor-pointer"
              >
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>ត្រឡប់ក្រោយ</span>
              </button>
              <button 
                type="submit" 
                id="submit-btn"
                class="py-3.5 px-7 bg-[#1a44e8] hover:bg-[#1538c2] text-white text-xs font-bold rounded-2xl shadow-lg shadow-blue-600/30 hover:shadow-xl active:scale-[0.99] transition flex items-center gap-2 cursor-pointer"
              >
                <i class="fa-solid fa-paper-plane text-xs"></i>
                <span>បញ្ជូនពាក្យចុះឈ្មោះជាផ្លូវការ</span>
              </button>
            </div>
          </div>

        </form>

      </div>
    @endif

    <!-- Footer -->
    <div class="text-center mt-6 text-xs text-slate-500">
      <p>&copy; {{ date('Y') }} {{ $schoolName }}. រក្សាសិទ្ធិគ្រប់យ៉ាង។</p>
    </div>

  </div>

  <!-- MULTI-STEP LOGIC & FORM VALIDATION SCRIPT -->
  <script>
    let currentStep = 1;

    // Elements
    const stepLine = document.getElementById('step-progress-line');
    const panes = [
      document.getElementById('step-pane-1'),
      document.getElementById('step-pane-2'),
      document.getElementById('step-pane-3')
    ];

    // Real-time Validation Rules
    const validators = {
      name_khmer: (val) => val.trim().length >= 2,
      name_latin: (val) => /^[a-zA-Z\s]{2,60}$/.test(val.trim()),
      date_of_birth: (val) => val.trim() !== '',
      phone: (val) => /^0[0-9]{8,9}$/.test(val.replace(/[\s-]/g, '')),
      email: (val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val.trim()),
      grade: (val) => val.trim() !== '',
      address: (val) => val.trim().length >= 3,
      parent_name: (val) => val.trim().length >= 2,
      parent_phone: (val) => /^0[0-9]{8,9}$/.test(val.replace(/[\s-]/g, ''))
    };

    function validateField(fieldId) {
      const input = document.getElementById(fieldId);
      const errorMsg = document.getElementById(`error-${fieldId}`);
      if (!input || !validators[fieldId]) return true;

      const isValid = validators[fieldId](input.value);
      if (!isValid) {
        input.classList.add('input-error');
        input.classList.remove('input-valid');
        if (errorMsg) errorMsg.classList.remove('hidden');
      } else {
        input.classList.remove('input-error');
        input.classList.add('input-valid');
        if (errorMsg) errorMsg.classList.add('hidden');
      }
      return isValid;
    }

    // Auto-convert Khmer numerals (០-៩) to English digits (0-9)
    function convertKhmerToLatinDigits(str) {
      if (!str) return str;
      const khmerDigits = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
      return str.split('').map(ch => {
        const idx = khmerDigits.indexOf(ch);
        return idx !== -1 ? String(idx) : ch;
      }).join('');
    }

    ['phone', 'parent_phone'].forEach(fieldId => {
      const el = document.getElementById(fieldId);
      if (el) {
        el.addEventListener('input', (e) => {
          const converted = convertKhmerToLatinDigits(e.target.value);
          if (converted !== e.target.value) {
            e.target.value = converted;
          }
        });
      }
    });

    // Attach real-time input listeners
    Object.keys(validators).forEach(fieldId => {
      const el = document.getElementById(fieldId);
      if (el) {
        el.addEventListener('input', () => validateField(fieldId));
        el.addEventListener('blur', () => validateField(fieldId));
      }
    });

    // Step 1 Validation
    function validateStep1() {
      const fields = ['name_khmer', 'name_latin', 'date_of_birth', 'phone', 'email', 'grade', 'address'];
      let allValid = true;
      let firstInvalid = null;

      fields.forEach(field => {
        const ok = validateField(field);
        if (!ok && allValid) {
          allValid = false;
          firstInvalid = document.getElementById(field);
        }
      });

      if (firstInvalid) {
        firstInvalid.focus();
      }
      return allValid;
    }

    // Step 2 Validation
    function validateStep2() {
      const fields = ['parent_name', 'parent_phone'];
      let allValid = true;
      let firstInvalid = null;

      fields.forEach(field => {
        const ok = validateField(field);
        if (!ok && allValid) {
          allValid = false;
          firstInvalid = document.getElementById(field);
        }
      });

      if (firstInvalid) {
        firstInvalid.focus();
      }
      return allValid;
    }

    function validateAndGoNext(fromStep) {
      if (fromStep === 1) {
        if (validateStep1()) {
          goToStep(2);
        }
      } else if (fromStep === 2) {
        if (validateStep2()) {
          updateReviewSummary();
          goToStep(3);
        }
      }
    }

    function goToStep(step) {
      // Prevent skipping ahead without validating previous steps
      if (step === 2 && !validateStep1()) return;
      if (step === 3 && (!validateStep1() || !validateStep2())) return;

      currentStep = step;

      // Update Panes Visibility
      panes.forEach((pane, idx) => {
        if (pane) {
          if (idx + 1 === step) {
            pane.classList.remove('hidden');
          } else {
            pane.classList.add('hidden');
          }
        }
      });

      // Update Stepper Progress Line
      if (stepLine) {
        const progressPercentages = { 1: '0%', 2: '50%', 3: '100%' };
        stepLine.style.width = progressPercentages[step] || '0%';
      }

      // Update Step Circles & Labels
      for (let i = 1; i <= 3; i++) {
        const circle = document.getElementById(`step-circle-${i}`);
        const label = document.getElementById(`step-label-${i}`);

        if (i < step) {
          // Completed step
          circle.className = 'w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-bold text-xs sm:text-sm bg-emerald-600 text-white shadow-sm';
          circle.innerHTML = '<i class="fa-solid fa-check"></i>';
          label.className = 'text-[11px] sm:text-xs font-bold text-emerald-700 mt-1.5 whitespace-nowrap';
        } else if (i === step) {
          // Active step
          circle.className = 'w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-bold text-xs sm:text-sm bg-blue-600 text-white ring-4 ring-blue-100 transition shadow-sm';
          circle.innerHTML = `${i}`;
          label.className = 'text-[11px] sm:text-xs font-bold text-blue-700 mt-1.5 whitespace-nowrap';
        } else {
          // Inactive upcoming step
          circle.className = 'w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-bold text-xs sm:text-sm bg-slate-200 text-slate-500 transition shadow-sm';
          circle.innerHTML = `${i}`;
          label.className = 'text-[11px] sm:text-xs font-semibold text-slate-400 mt-1.5 whitespace-nowrap';
        }
      }

      // Scroll smoothly to top of form
      window.scrollTo({ top: 120, behavior: 'smooth' });
    }

    // Update Step 3 Review Screen with filled values
    function updateReviewSummary() {
      const getVal = (id) => document.getElementById(id)?.value || '---';
      const gender = document.querySelector('input[name="gender"]:checked')?.value === 'female' ? 'ស្រី' : 'ប្រុស';

      const revNames = document.getElementById('review-names');
      if (revNames) revNames.textContent = `${getVal('name_khmer')} (${getVal('name_latin').toUpperCase()})`;

      const revGenderDob = document.getElementById('review-gender-dob');
      if (revGenderDob) revGenderDob.textContent = `${gender} • ${getVal('date_of_birth')}`;

      const revContact = document.getElementById('review-contact');
      if (revContact) revContact.textContent = `${getVal('phone')} • ${getVal('email')}`;

      const revGrade = document.getElementById('review-grade');
      if (revGrade) revGrade.textContent = `ថ្នាក់ទី ${getVal('grade')}`;

      const revAddress = document.getElementById('review-address');
      if (revAddress) revAddress.textContent = getVal('address');

      const revParentName = document.getElementById('review-parent-name');
      if (revParentName) revParentName.textContent = getVal('parent_name');

      const revParentRel = document.getElementById('review-parent-rel');
      if (revParentRel) revParentRel.textContent = `${getVal('parent_relationship')} • ${getVal('parent_occupation') || 'ទូទៅ'}`;

      const revParentPhone = document.getElementById('review-parent-phone');
      if (revParentPhone) revParentPhone.textContent = getVal('parent_phone');
    }

    // Photo Preview
    const photoInput = document.getElementById('photo-input');
    const photoBox = document.getElementById('photo-preview-box');
    if (photoInput && photoBox) {
      photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(evt) {
            photoBox.innerHTML = `<img src="${evt.target.result}" class="w-full h-full object-cover" />`;
          }
          reader.readAsDataURL(file);
        }
      });
    }

    // Form Submit Loading Feedback
    const regForm = document.getElementById('registration-form');
    const submitBtn = document.getElementById('submit-btn');
    if (regForm && submitBtn) {
      regForm.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>កំពុងបញ្ជូនទិន្នន័យ...</span>';
      });
    }
  </script>
</body>
</html>
