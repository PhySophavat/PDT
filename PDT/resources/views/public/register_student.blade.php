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

  <!-- Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
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
  </style>
</head>
<body class="bg-gradient-to-br from-[#dce4f7] via-[#e8efff] to-[#d6e0f5] min-h-screen py-6 px-3 sm:px-6 flex flex-col items-center justify-center font-khmer text-slate-800 antialiased">

  <div class="w-full max-w-3xl my-4">

    <!-- Header / Brand Banner -->
    <div class="text-center mb-6">
      <div class="inline-flex items-center gap-2.5 px-4 py-1.5 bg-white/80 backdrop-blur-md rounded-full shadow-xs border border-white mb-3">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-xs font-bold tracking-wider text-blue-700 uppercase">{{ $schoolName }}</span>
      </div>
      @if (session('registration_success'))
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          ប័ណ្ណទទួលពាក្យចុះឈ្មោះចូលរៀន
        </h1>
        <p class="text-sm text-slate-600 mt-1 max-w-md mx-auto">
          ការចុះឈ្មោះរបស់អ្នកត្រូវបានកត់ត្រា និងកំពុងស្ថិតក្នុងដំណាក់កាលត្រួតពិនិត្យ
        </p>
      @else
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          ទម្រង់ចុះឈ្មោះចូលរៀនតាមអនឡាញ
        </h1>
        <p class="text-sm text-slate-600 mt-1 max-w-md mx-auto">
          សូមបំពេញព័ត៌មានខាងក្រោមឱ្យបានត្រឹមត្រូវ ដើម្បីស្នើសុំចុះឈ្មោះចូលរៀនក្នុងឆ្នាំសិក្សាថ្មី
        </p>
      @endif
    </div>

    @if (session('registration_success'))
      <!-- Success Confirmation Screen -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/90 p-6 sm:p-10 text-center transition-all animate-fadeIn relative overflow-hidden">
        
        <!-- Top decorative gradient line -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-blue-600"></div>

        <!-- Success Animation Checkmark -->
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

        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1 mb-2">
          ការចុះឈ្មោះត្រូវបានបញ្ជូនដោយជោគជ័យ!
        </h2>
        <p class="text-slate-600 text-xs sm:text-sm max-w-lg mx-auto mb-6 leading-relaxed">
          សូមអរគុណ! ពាក្យស្នើសុំចូលរៀនរបស់ប្អូនត្រូវបានបញ្ចូលទៅក្នុងប្រព័ន្ធរដ្ឋបាលសាលារៀន ជំនាន់ថ្មី PDT ដោយសុវត្ថិភាព។
        </p>

        <!-- Email Notification Notice Card -->
        <div class="p-4 sm:p-5 bg-gradient-to-r from-emerald-50 via-teal-50/50 to-blue-50/40 border border-emerald-200/90 rounded-2xl max-w-xl mx-auto text-left mb-6 shadow-xs">
          <div class="flex items-start gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white flex items-center justify-center shrink-0 text-xl shadow-md shadow-emerald-600/25 mt-0.5">
              <i class="fa-solid fa-envelope-circle-check"></i>
            </div>
            <div class="flex-1">
              <h4 class="text-xs sm:text-sm font-bold text-emerald-950 mb-1 flex items-center gap-2">
                <span>សារបញ្ជាក់ត្រូវបានផ្ញើទៅកាន់ Email រួចរាល់!</span>
              </h4>
              <p class="text-xs text-slate-700 leading-relaxed">
                ប្រព័ន្ធបានផ្ញើ Email បញ្ជាក់ការទទួលពាក្យទៅកាន់៖ <strong class="text-blue-700 font-bold bg-white/90 px-2 py-0.5 rounded-lg border border-blue-200/70 inline-block mt-0.5">{{ session('registered_email') ?? 'អ៊ីមែលរបស់អ្នក' }}</strong>
              </p>
              <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-info text-blue-500"></i>
                <span>សូមចូលទៅពិនិត្យមើលប្រអប់សំបុត្រ <strong>Inbox</strong> (ឬថត <strong>Spam</strong>) នៃ Email របស់ប្អូន។</span>
              </p>
            </div>
          </div>
        </div>

        <!-- Registered Summary Card -->
        <div class="bg-slate-50/90 border border-slate-200 rounded-2xl max-w-xl mx-auto text-left overflow-hidden mb-6 shadow-xs">
          <!-- Card Header -->
          <div class="bg-slate-100/90 px-4 py-2.5 border-b border-slate-200 flex items-center justify-between text-xs">
            <div class="font-bold text-slate-800 flex items-center gap-2">
              <span class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center text-[11px]">
                <i class="fa-solid fa-address-card"></i>
              </span>
              <span>សេចក្តីសង្ខេបព័ត៌មានសិស្ស</span>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
              <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
              <span>រង់ចាំការត្រួតពិនិត្យ</span>
            </span>
          </div>

          <!-- Card Content Grid -->
          <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="space-y-0.5">
              <span class="text-slate-500 text-[11px] flex items-center gap-1.5">
                <i class="fa-solid fa-user text-slate-400"></i> ឈ្មោះសិស្ស៖
              </span>
              <strong class="text-slate-900 text-sm font-bold block">{{ session('registered_name') ?? '---' }}</strong>
            </div>

            <div class="space-y-0.5">
              <span class="text-slate-500 text-[11px] flex items-center gap-1.5">
                <i class="fa-solid fa-graduation-cap text-slate-400"></i> ថ្នាក់ស្នើសុំចូលរៀន៖
              </span>
              <strong class="text-blue-700 text-sm font-extrabold block">ថ្នាក់ {{ session('registered_grade') ?? '---' }}</strong>
            </div>

            <div class="space-y-0.5">
              <span class="text-slate-500 text-[11px] flex items-center gap-1.5">
                <i class="fa-solid fa-phone text-slate-400"></i> លេខទូរស័ព្ទ៖
              </span>
              <span class="font-bold text-slate-800 text-xs block font-mono">{{ session('registered_phone') ?? '---' }}</span>
            </div>

            <div class="space-y-0.5">
              <span class="text-slate-500 text-[11px] flex items-center gap-1.5">
                <i class="fa-solid fa-envelope text-slate-400"></i> អ៊ីមែលទំនាក់ទំនង៖
              </span>
              <span class="font-bold text-slate-800 text-xs block font-mono break-all">{{ session('registered_email') ?? '---' }}</span>
            </div>
          </div>
        </div>

        <!-- Next Steps Instruction Card -->
        <div class="p-4 sm:p-5 bg-blue-50/70 border border-blue-200/80 rounded-2xl max-w-xl mx-auto text-left text-xs mb-6 space-y-3 shadow-xs">
          <div class="flex items-center gap-2 text-blue-950 font-bold text-xs sm:text-sm">
            <i class="fa-solid fa-list-check text-blue-600"></i>
            <span>ដំណើរការបន្តបន្ទាប់ (Next Steps)៖</span>
          </div>

          <div class="space-y-2.5 text-slate-700 text-xs leading-relaxed">
            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">១</span>
              <p><strong>ការពិនិត្យឯកសារ៖</strong> គណៈគ្រប់គ្រងសាលានឹងធ្វើការត្រួតពិនិត្យផ្ទៀងផ្ទាត់ព័ត៌មាន និងឯកសារដែលប្អូនបានបញ្ជូនមក។</p>
            </div>
            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">២</span>
              <p><strong>ការផ្ដល់គណនីចូលរៀន៖</strong> នៅពេលសាលា <strong>អនុម័តជាផ្លូវការ</strong> ប្រព័ន្ធនឹងផ្ញើ <strong>អត្តលេខសិស្ស (Student ID)</strong> និង <strong>ពាក្យសម្ងាត់បណ្តោះអាសន្ន</strong> ទៅកាន់ Email ខាងលើដោយស្វ័យប្រវត្តិ។</p>
            </div>
            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">៣</span>
              <p><strong>ការចូលប្រើប្រព័ន្ធ៖</strong> ប្អូនអាចយកគណនីនោះទៅ Login ចូលប្រើប្រព័ន្ធសាលា (sala-portal) ដើម្បីមើលកាលវិភាគសិក្សា វត្តមាន និងលទ្ធផលសិក្សាបាន។</p>
            </div>
          </div>
        </div>

        <!-- Safety Lock Note -->
        <div class="p-3.5 bg-slate-100 border border-slate-200/80 rounded-2xl max-w-xl mx-auto text-slate-600 text-[11px] text-center mb-6 flex items-center justify-center gap-2">
          <i class="fa-solid fa-shield-halved text-emerald-600 text-sm"></i>
          <span>ដើម្បីធានាសុវត្ថិភាពទិន្នន័យផ្ទាល់ខ្លួន និងជៀសវាងការចុះឈ្មោះស្ទួន ប្រព័ន្ធបានបញ្ចប់ការចុះឈ្មោះនេះរួចរាល់។</span>
        </div>

        <!-- Close Window Button -->
        <div class="flex items-center justify-center">
          <button 
            type="button" 
            onclick="window.close();" 
            class="px-7 py-3 bg-slate-900 hover:bg-slate-800 active:scale-[0.98] text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-2 cursor-pointer"
          >
            <i class="fa-solid fa-circle-xmark text-sm"></i>
            <span>បិទផ្ទាំងនេះ (Close Window)</span>
          </button>
        </div>
      </div>
    @else
      <!-- Registration Form Card -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
        
        <!-- Form Header Ribbon -->
        <div class="bg-[#1a44e8] px-6 py-4 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-lg">
              <i class="fa-solid fa-id-card"></i>
            </div>
            <div>
              <h3 class="font-bold text-sm sm:text-base">ព័ត៌មានចុះឈ្មោះសិស្ស និងអាណាព្យាបាល</h3>
              <p class="text-[11px] text-blue-100">បំពេញតែម្តង ងាយស្រួល និងរហ័សទាន់ចិត្ត</p>
            </div>
          </div>
          <span class="text-xs bg-white/20 px-2.5 py-1 rounded-full text-white font-mono font-semibold">
            ឆ្នាំ ២០២៦
          </span>
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

        <form action="{{ route('public.register-student.post') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-8">
          @csrf

          <!-- Section 1: Student Information -->
          <div>
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-200">
              <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">១</span>
              <h2 class="text-base font-bold text-slate-800">ព័ត៌មានសិស្ស</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
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
                    name="name_khmer" 
                    value="{{ old('name_khmer') }}" 
                    required 
                    placeholder="ឧ. សុខ សុភក្ត្រ"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  />
                </div>
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
                    name="name_latin" 
                    value="{{ old('name_latin') }}" 
                    required 
                    placeholder="e.g. SOK SOPHEAK"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 uppercase transition"
                  />
                </div>
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
                    name="date_of_birth" 
                    value="{{ old('date_of_birth') }}" 
                    required 
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  />
                </div>
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
                    name="phone" 
                    value="{{ old('phone') }}" 
                    required 
                    placeholder="ឧ. 012 345 678"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  />
                </div>
              </div>

              <!-- Student Email -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  អ៊ីមែលសិស្ស (សម្រាប់ផ្ញើគណនីចូលប្រព័ន្ធ) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-envelope text-xs"></i>
                  </span>
                  <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="ឧ. student@example.com"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border @error('email') border-red-500 @else border-slate-200 @enderror rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  />
                </div>
                @error('email')
                  <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                @enderror
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
                    name="grade" 
                    required 
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
                    name="address" 
                    rows="2" 
                    required 
                    placeholder="ផ្ទះលេខ ផ្លូវ ភូមិ ឃុំ/សង្កាត់ ក្រុង/ស្រុក/ខណ្ឌ ខេត្ត/រាជធានី"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition resize-none"
                  >{{ old('address') }}</textarea>
                </div>
              </div>

              <!-- Photo Upload (optional) -->
              <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  រូបថតសិស្ស <span class="text-slate-400 font-normal">(មិនទាមទារជាចាំបាច់)</span>
                </label>
                <div class="flex items-center gap-4 p-3.5 bg-slate-50 border border-dashed border-slate-300 rounded-2xl hover:border-blue-400 transition">
                  <div id="photo-preview-box" class="w-16 h-16 rounded-xl bg-slate-200 flex items-center justify-center text-slate-400 overflow-hidden flex-shrink-0">
                    <i class="fa-regular fa-image text-2xl"></i>
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
          </div>

          <!-- Section 2: Parent / Guardian Information -->
          <div>
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-200">
              <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">២</span>
              <h2 class="text-base font-bold text-slate-800">ព័ត៌មានអាណាព្យាបាល</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
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
                    name="parent_name" 
                    value="{{ old('parent_name') }}" 
                    required 
                    placeholder="ឈ្មោះពេញរបស់អាណាព្យាបាល"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  />
                </div>
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
                    name="parent_relationship" 
                    required 
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  >
                    <option value="ឪពុក" {{ old('parent_relationship', 'ឪពុក') === 'ឪពុក' ? 'selected' : '' }}>ឪពុក</option>
                    <option value="ម្តាយ" {{ old('parent_relationship') === 'ម្តាយ' ? 'selected' : '' }}>ម្តាយ</option>
                    <option value="អាណាព្យាបាល" {{ old('parent_relationship') === 'អាណាព្យាបាល' ? 'selected' : '' }}>អាណាព្យាបាល</option>
                  </select>
                </div>
              </div>

              <!-- Guardian Occupation -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  មុខរបរ
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-briefcase text-xs"></i>
                  </span>
                  <input 
                    type="text" 
                    name="parent_occupation" 
                    value="{{ old('parent_occupation') }}" 
                    placeholder="ឧ. មន្ត្រីរាជការ, អាជីវករ, កសិករ"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  />
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
                    name="parent_phone" 
                    value="{{ old('parent_phone') }}" 
                    required 
                    placeholder="ឧ. 012 888 999"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Action -->
          <div class="pt-4 border-t border-slate-200">
            <button 
              type="submit" 
              class="w-full py-3.5 px-6 bg-[#1a44e8] hover:bg-[#1538c2] text-white text-sm font-bold rounded-2xl shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-blue-600/40 active:scale-[0.99] transition flex items-center justify-center gap-2"
            >
              <i class="fa-solid fa-paper-plane"></i>
              <span>បញ្ជូនពាក្យចុះឈ្មោះ</span>
            </button>
            <p class="text-[11px] text-center text-slate-400 mt-2.5">
              ព័ត៌មានដែលបានបញ្ជូននឹងត្រូវបានរក្សាទុកដោយសុវត្ថិភាព សម្រាប់តែគោលបំណងរដ្ឋបាលសាលាប៉ុណ្ណោះ។
            </p>
          </div>

        </form>

      </div>
    @endif

    <!-- Footer -->
    <div class="text-center mt-6 text-xs text-slate-500">
      <p>&copy; {{ date('Y') }} {{ $schoolName }}. រក្សាសិទ្ធិគ្រប់យ៉ាង។</p>
    </div>

  </div>

  <script>
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
  </script>
</body>
</html>
