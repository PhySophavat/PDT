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
  <title>ទម្រង់សុំច្បាប់លោកគ្រូ-អ្នកគ្រូ | {{ $schoolName }}</title>

  <!-- SEO & Social Open Graph Tags -->
  <meta name="description" content="ទម្រង់សុំច្បាប់លោកគ្រូ-អ្នកគ្រូអនឡាញ នៃ{{ $schoolName }} ងាយស្រួល ឆាប់រហ័ស និងមានសុវត្ថិភាព។">
  <meta property="og:locale" content="km_KH">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="{{ $schoolName }}">
  <meta property="og:title" content="ទម្រង់សុំច្បាប់លោកគ្រូ-អ្នកគ្រូ | {{ $schoolName }}">
  <meta property="og:description" content="ទម្រង់សុំច្បាប់លោកគ្រូ-អ្នកគ្រូអនឡាញ នៃ{{ $schoolName }} ងាយស្រួល ឆាប់រហ័ស និងមានសុវត្ថិភាព។">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:image" content="{{ asset('images/og-preview.jpg') }}">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="ទម្រង់សុំច្បាប់លោកគ្រូ-អ្នកគ្រូ | {{ $schoolName }}">
  <meta name="twitter:description" content="ទម្រង់សុំច្បាប់លោកគ្រូ-អ្នកគ្រូអនឡាញ នៃ{{ $schoolName }} ងាយស្រួល ឆាប់រហ័ស និងមានសុវត្ថិភាព។">
  <meta name="twitter:image" content="{{ asset('images/og-preview.jpg') }}">

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
              50: '#ecfdf5',
              100: '#d1fae5',
              500: '#10b981',
              600: '#059669',
              700: '#047857',
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
<body class="bg-gradient-to-br from-[#d1fae5] via-[#f0fdf4] to-[#e2e8f0] min-h-screen py-8 px-3 sm:px-6 flex flex-col items-center justify-center font-khmer text-slate-800 antialiased">

  <div class="w-full max-w-2xl my-4">

    <!-- Header / Brand Banner -->
    <div class="text-center mb-6">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/80 backdrop-blur-md rounded-full shadow-xs border border-white mb-3">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-pulse"></span>
        <span class="text-xs font-bold tracking-wider text-emerald-700 uppercase">{{ $schoolName }} • សុំច្បាប់គ្រូបង្រៀន</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
        ទម្រង់សុំច្បាប់ឈប់សម្រាករបស់គ្រូបង្រៀន
      </h1>
      <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-md mx-auto">
        សូមបំពេញព័ត៌មានខាងក្រោមដើម្បីស្នើសុំច្បាប់ឈប់សម្រាកទៅកាន់គណៈគ្រប់គ្រងសាលា
      </p>
    </div>

    @if (session('leave_success'))
      @php $succ = session('leave_success'); @endphp
      <!-- Success Confirmation Screen -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 p-8 sm:p-10 text-center transition-all">
        <div class="w-20 h-20 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-4xl mb-5 shadow-inner">
          <i class="fa-solid fa-circle-check"></i>
        </div>

        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full uppercase tracking-wider">
          បានបញ្ជូនដោយជោគជ័យ
        </span>

        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mt-4 mb-2">
          សំណើសុំច្បាប់ត្រូវបានបញ្ជូនទៅគណៈគ្រប់គ្រងសាលារួចរាល់!
        </h2>
        <p class="text-slate-600 text-xs sm:text-sm max-w-md mx-auto mb-6 leading-relaxed">
          គណៈគ្រប់គ្រងសាលានឹងពិនិត្យ និងអនុម័តសំណើសុំច្បាប់របស់លោកគ្រូ-អ្នកគ្រូក្នុងប្រព័ន្ធ។
        </p>

        <!-- Summary Card -->
        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 max-w-md mx-auto text-left text-xs space-y-2.5 mb-8">
          <div class="flex justify-between border-b border-slate-200 pb-2">
            <span class="text-slate-500">ឈ្មោះគ្រូបង្រៀន៖</span>
            <span class="font-bold text-slate-900">{{ $succ['teacher_name'] ?? '-' }}</span>
          </div>
          <div class="flex justify-between border-b border-slate-200 pb-2">
            <span class="text-slate-500">មុខវិជ្ជាឯកទេស៖</span>
            <span class="font-bold text-slate-700">{{ $succ['specialization'] ?? '-' }}</span>
          </div>
          <div class="flex justify-between border-b border-slate-200 pb-2">
            <span class="text-slate-500">ប្រភេទច្បាប់៖</span>
            <span class="font-bold text-emerald-700">{{ $succ['leave_type'] ?? '-' }}</span>
          </div>
          <div class="flex justify-between border-b border-slate-200 pb-2">
            <span class="text-slate-500">រយៈពេលសុំច្បាប់៖</span>
            <span class="font-bold text-slate-900">{{ $succ['start_date'] ?? '-' }} ដល់ {{ $succ['end_date'] ?? '-' }} ({{ $succ['days_count'] ?? 1 }} ថ្ងៃ)</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">ស្ថានភាព៖</span>
            <span class="inline-flex items-center gap-1 font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full text-[11px]">
              <i class="fa-solid fa-clock"></i> កំពុងរង់ចាំការអនុម័ត
            </span>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
          <a href="{{ route('public.teacher-leave-request') }}" class="w-full sm:w-auto px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md transition">
            <i class="fa-solid fa-plus mr-1"></i> ស្នើសុំច្បាប់បន្ថែម
          </a>
          <a href="{{ url('/') }}" class="w-full sm:w-auto px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
            <i class="fa-solid fa-house mr-1"></i> ទំព័រដើម
          </a>
        </div>
      </div>
    @else
      <!-- Form Screen -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 p-6 sm:p-8 transition-all">
        
        <form action="{{ route('public.teacher-leave-request.post') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
          @csrf

          @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs">
              <div class="font-bold flex items-center gap-1.5 mb-1">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>សូមពិនិត្យព័ត៌មានដែលបានបញ្ចូល៖</span>
              </div>
              <ul class="list-disc list-inside space-y-0.5 pl-2">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <!-- Teacher Selection -->
          <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            <div class="sm:col-span-7">
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                ១. ជ្រើសរើសឈ្មោះគ្រូបង្រៀន <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <select 
                  id="teacher_select" 
                  name="teacher_id" 
                  required
                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                >
                  <option value="">-- សូមជ្រើសរើសឈ្មោះលោកគ្រូ-អ្នកគ្រូ --</option>
                  @foreach ($teachers as $t)
                    <option 
                      value="{{ $t->id }}" 
                      data-name="{{ $t->user->name ?? $t->name }}"
                      data-code="{{ $t->teacher_code ?? 'TCH-' . $t->id }}" 
                      data-spec="{{ $t->subject_specialty ?? $t->qualification ?? '' }}"
                      data-phone="{{ $t->user->phone ?? $t->phone ?? '' }}"
                      {{ old('teacher_id') == $t->id ? 'selected' : '' }}
                    >
                      {{ $t->user->name ?? $t->name }} ({{ $t->subject_specialty ?? 'គ្រូបង្រៀន' }})
                    </option>
                  @endforeach
                </select>
              </div>
              <input 
                type="hidden" 
                id="teacher_name_input" 
                name="teacher_name" 
                value="{{ old('teacher_name') }}"
                required
              />
              <input 
                type="hidden" 
                id="specialization_input" 
                name="specialization" 
                value="{{ old('specialization') }}"
              />
            </div>

            <div class="sm:col-span-5">
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                អត្តលេខគ្រូ
              </label>
              <input 
                type="text" 
                id="teacher_code_display" 
                name="teacher_code"
                readonly 
                placeholder="TCH-2026-..." 
                value="{{ old('teacher_code') }}"
                class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-600 cursor-not-allowed"
              />
            </div>
          </div>

          <!-- Leave Dates -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                ២. កាលបរិច្ឆេទចាប់ផ្ដើម <span class="text-rose-500">*</span>
              </label>
              <input 
                type="date" 
                id="start_date" 
                name="start_date" 
                value="{{ old('start_date', date('Y-m-d')) }}" 
                required 
                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
              />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                កាលបរិច្ឆេទបញ្ចប់ <span class="text-rose-500">*</span>
              </label>
              <input 
                type="date" 
                id="end_date" 
                name="end_date" 
                value="{{ old('end_date', date('Y-m-d')) }}" 
                required 
                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
              />
            </div>
            <div class="sm:col-span-2 text-right">
              <span id="days_count_badge" class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-full text-xs font-bold">
                <i class="fa-regular fa-clock"></i> ចំនួនសរុប៖ <span id="days_count_text">១ ថ្ងៃ</span>
              </span>
            </div>
          </div>

          <!-- Leave Type & Phone -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                ៣. ប្រភេទច្បាប់ឈប់សម្រាក <span class="text-rose-500">*</span>
              </label>
              <select 
                name="leave_type" 
                required 
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
              >
                <option value="ឈឺ" {{ old('leave_type') == 'ឈឺ' ? 'selected' : '' }}>🤒 ឈឺ (មានវេជ្ជបញ្ជា/សម្រាកព្យាបាល)</option>
                <option value="ធុរៈចាំបាច់" {{ old('leave_type', 'ធុរៈចាំបាច់') == 'ធុរៈចាំបាច់' ? 'selected' : '' }}>🏠 ធុរៈផ្ទាល់ខ្លួន/គ្រួសារចាំបាច់</option>
                <option value="បេសកកម្ម/បណ្តុះបណ្តាល" {{ old('leave_type') == 'បេសកកម្ម/បណ្តុះបណ្តាល' ? 'selected' : '' }}>📚 បេសកកម្ម ឬវគ្គបណ្តុះបណ្តាល</option>
                <option value="សម្រាលកូន/លំហែមាតុភាព" {{ old('leave_type') == 'សម្រាលកូន/លំហែមាតុភាព' ? 'selected' : '' }}>👶 លំហែមាតុភាព</option>
                <option value="ផ្សេងៗ" {{ old('leave_type') == 'ផ្សេងៗ' ? 'selected' : '' }}>📝 ផ្សេងៗ</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                លេខទូរស័ព្ទទំនាក់ទំនង <span class="text-rose-500">*</span>
              </label>
              <input 
                type="text" 
                id="phone_input" 
                name="phone" 
                value="{{ old('phone') }}" 
                required
                placeholder="012 345 678" 
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
              />
            </div>
          </div>

          <!-- Reason Textarea -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              ៤. មូលហេតុសុំច្បាប់លម្អិត <span class="text-rose-500">*</span>
            </label>
            <textarea 
              name="reason" 
              rows="3" 
              required 
              placeholder="សូមបញ្ជាក់មូលហេតុលម្អិត (ឧ. មានធុរៈបន្ទាន់ក្នុងគ្រួសារ, ផ្តាសាយក្តៅខ្លួន, ត្រូវចូលរួមសិក្ខាសាលា...)" 
              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white rounded-xl text-xs font-khmer focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
            >{{ old('reason') }}</textarea>
          </div>

          <!-- Attachment Upload -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              ៥. ឯកសារភ្ជាប់ (លិខិតពេទ្យ ឬលិខិតអញ្ជើញបេសកកម្ម - បើមាន)
            </label>
            <div class="border-2 border-dashed border-slate-300 hover:border-emerald-400 rounded-2xl p-4 text-center bg-slate-50/60 relative cursor-pointer">
              <input 
                type="file" 
                name="attachment" 
                accept=".jpg,.jpeg,.png,.webp,.pdf" 
                id="attachment_file" 
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
              />
              <div class="flex items-center justify-center gap-2 text-xs text-slate-600">
                <i class="fa-solid fa-paperclip text-emerald-600 text-base"></i>
                <span id="file_label" class="font-semibold text-emerald-700">ចុចដើម្បីជ្រើសរើសឯកសារ (JPG, PNG, PDF)</span>
                <span class="text-[11px] text-slate-400">(អតិបរមា 5MB)</span>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-3">
            <button 
              type="submit" 
              class="w-full py-3.5 px-6 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white rounded-2xl text-xs sm:text-sm font-bold shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2 active:scale-98 cursor-pointer"
            >
              <i class="fa-solid fa-paper-plane"></i>
              <span>បញ្ជូនសំណើសុំច្បាប់</span>
            </button>
          </div>

        </form>
      </div>
    @endif

    <div class="text-center mt-6 text-xs text-slate-400">
      &copy; {{ date('Y') }} {{ $schoolName }}. រក្សាសិទ្ធិគ្រប់យ៉ាង។
    </div>

  </div>

  <script>
    const teacherSelect = document.getElementById('teacher_select');
    const teacherNameInput = document.getElementById('teacher_name_input');
    const specializationInput = document.getElementById('specialization_input');
    const teacherCodeDisplay = document.getElementById('teacher_code_display');
    const phoneInput = document.getElementById('phone_input');

    if (teacherSelect) {
      teacherSelect.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt && opt.value) {
          teacherNameInput.value = opt.getAttribute('data-name') || '';
          specializationInput.value = opt.getAttribute('data-spec') || '';
          teacherCodeDisplay.value = opt.getAttribute('data-code') || '';
          if (!phoneInput.value) {
            phoneInput.value = opt.getAttribute('data-phone') || '';
          }
        } else {
          teacherNameInput.value = '';
          specializationInput.value = '';
          teacherCodeDisplay.value = '';
        }
      });
      // Trigger initial if pre-selected
      if (teacherSelect.value) {
        teacherSelect.dispatchEvent(new Event('change'));
      }
    }

    // Days calculation
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const daysCountText = document.getElementById('days_count_text');

    function updateDaysCount() {
      if (startDateInput && endDateInput && daysCountText) {
        const s = new Date(startDateInput.value);
        const e = new Date(endDateInput.value);
        if (!isNaN(s) && !isNaN(e) && e >= s) {
          const diffDays = Math.round((e - s) / (1000 * 60 * 60 * 24)) + 1;
          daysCountText.textContent = diffDays + ' ថ្ងៃ';
        } else {
          daysCountText.textContent = '១ ថ្ងៃ';
        }
      }
    }

    if (startDateInput && endDateInput) {
      startDateInput.addEventListener('change', function() {
        if (endDateInput.value < startDateInput.value) {
          endDateInput.value = startDateInput.value;
        }
        updateDaysCount();
      });
      endDateInput.addEventListener('change', updateDaysCount);
    }

    // Attachment file name update
    const fileInput = document.getElementById('attachment_file');
    const fileLabel = document.getElementById('file_label');
    if (fileInput && fileLabel) {
      fileInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
          fileLabel.textContent = 'ឯកសារ៖ ' + this.files[0].name;
        }
      });
    }
  </script>
</body>
</html>

