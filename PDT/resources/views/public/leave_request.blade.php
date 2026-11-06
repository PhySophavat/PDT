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
  <title>ទម្រង់សុំច្បាប់សិស្ស | {{ $schoolName }}</title>

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
              500: '#4f46e5',
              600: '#4338ca',
              700: '#3730a3',
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
<body class="bg-gradient-to-br from-[#e0e7ff] via-[#f1f5f9] to-[#e2e8f0] min-h-screen py-8 px-3 sm:px-6 flex flex-col items-center justify-center font-khmer text-slate-800 antialiased">

  <div class="w-full max-w-2xl my-4">

    <!-- Header / Brand Banner -->
    <div class="text-center mb-6">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/80 backdrop-blur-md rounded-full shadow-xs border border-white mb-3">
        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-pulse"></span>
        <span class="text-xs font-bold tracking-wider text-indigo-700 uppercase">{{ $schoolName }} • ប្រព័ន្ធសុំច្បាប់អនឡាញ</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
        ទម្រង់សុំច្បាប់ឈប់សម្រាករបស់សិស្ស
      </h1>
      <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-md mx-auto">
        សូមបំពេញព័ត៌មានខាងក្រោមដើម្បីស្នើសុំច្បាប់ឈប់សម្រាកទៅកាន់លោកគ្រូ អ្នកគ្រូ និងគណៈគ្រប់គ្រងសាលា
      </p>
    </div>

    @if (session('leave_success'))
      @php $succ = session('leave_success'); @endphp
      <!-- Success Confirmation Screen -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 p-8 sm:p-10 text-center transition-all">
        <div class="w-20 h-20 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-4xl mb-5 shadow-inner">
          <i class="fa-solid fa-circle-check"></i>
        </div>

        <h2 class="text-2xl font-bold text-slate-900 mb-2">
          បានបញ្ជូនសំណើសុំច្បាប់រួចរាល់!
        </h2>
        <p class="text-sm text-slate-600 mb-6">
          {{ is_array($succ) ? $succ['message'] : $succ }}
        </p>

        @if (is_array($succ))
          <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 mb-6 text-left text-xs sm:text-sm space-y-2.5 max-w-md mx-auto">
            <div class="flex justify-between py-1 border-b border-slate-200/60">
              <span class="text-slate-500">ឈ្មោះសិស្ស:</span>
              <span class="font-bold text-slate-800">{{ $succ['student_name'] }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-200/60">
              <span class="text-slate-500">ថ្នាក់រៀន:</span>
              <span class="font-bold text-indigo-600">ថ្នាក់ {{ $succ['class_name'] }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-200/60">
              <span class="text-slate-500">កាលបរិច្ឆេទសុំច្បាប់:</span>
              <span class="font-bold text-slate-800">{{ $succ['dates'] }}</span>
            </div>
            <div class="flex justify-between py-1">
              <span class="text-slate-500">ប្រភេទច្បាប់:</span>
              <span class="font-bold text-slate-800">{{ $succ['leave_type'] }}</span>
            </div>
          </div>
        @endif

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
          <a href="{{ route('public.leave-request') }}" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/30 transition">
            <i class="fa-solid fa-plus mr-1.5"></i> បញ្ជូនសំណើថ្មីផ្សេងទៀត
          </a>
        </div>
      </div>
    @else
      <!-- Leave Request Form Card -->
      <form action="{{ route('public.leave-request.post') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
        @csrf

        <!-- Top Accent Bar -->
        <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-blue-600 px-6 py-4 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl shadow-inner">
              <i class="fa-regular fa-calendar-check"></i>
            </div>
            <div>
              <h3 class="font-bold text-base leading-tight">ពាក្យសុំច្បាប់ឈប់សម្រាក</h3>
              <p class="text-[11px] text-indigo-100">បំពេញព័ត៌មានឱ្យបានគ្រប់ជ្រុងជ្រោយខាងក្រោម</p>
            </div>
          </div>
          <span class="text-xs px-2.5 py-1 bg-white/20 rounded-full font-semibold">អនឡាញ</span>
        </div>

        <div class="p-6 sm:p-8 space-y-5">

          <!-- Errors Banner -->
          @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-700 space-y-1">
              <div class="font-bold flex items-center gap-1.5">
                <i class="fa-solid fa-circle-exclamation"></i> សូមពិនិត្យកំហុសខាងក្រោម៖
              </div>
              <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <!-- Class Selection -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              ១. ជ្រើសរើសថ្នាក់រៀន <span class="text-rose-500">*</span>
            </label>
            <select 
              id="class_select" 
              name="class_name" 
              required 
              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white rounded-xl text-xs font-bold text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
            >
              <option value="">-- សូមជ្រើសរើសថ្នាក់រៀន --</option>
              @foreach ($classes as $c)
                <option value="{{ $c }}" {{ (old('class_name', $selectedClass) == $c) ? 'selected' : '' }}>
                  ថ្នាក់ {{ $c }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Student Selection or Input -->
          <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            <div class="sm:col-span-7">
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                ២. ឈ្មោះសិស្ស <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <select 
                  id="student_select" 
                  name="student_id" 
                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                >
                  <option value="">-- ជ្រើសរើសសិស្សក្នុងថ្នាក់ --</option>
                  @foreach ($students as $st)
                    <option value="{{ $st['id'] }}" data-code="{{ $st['student_code'] }}" {{ old('student_id') == $st['id'] ? 'selected' : '' }}>
                      {{ $st['name'] }} {{ $st['student_code'] ? "({$st['student_code']})" : '' }}
                    </option>
                  @endforeach
                </select>
              </div>
              <input 
                type="hidden" 
                id="student_name_input" 
                name="student_name" 
                value="{{ old('student_name') }}"
                required
              />
              <p class="text-[11px] text-slate-400 mt-1">💡 ជ្រើសរើសថ្នាក់រៀនខាងលើ បញ្ជីឈ្មោះសិស្សនឹងបង្ហាញនៅទីនេះ</p>
            </div>

            <div class="sm:col-span-5">
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                អត្តលេខសិស្ស
              </label>
              <input 
                type="text" 
                id="student_code_display" 
                readonly 
                placeholder="STU-2026-..." 
                class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-600 cursor-not-allowed"
              />
            </div>
          </div>

          <!-- Leave Dates -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                ៣. កាលបរិច្ឆេទចាប់ផ្ដើម <span class="text-rose-500">*</span>
              </label>
              <input 
                type="date" 
                id="start_date" 
                name="start_date" 
                value="{{ old('start_date', date('Y-m-d')) }}" 
                required 
                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
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
                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
              />
            </div>
            <div class="sm:col-span-2 text-right">
              <span id="days_count_badge" class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-full text-xs font-bold">
                <i class="fa-regular fa-clock"></i> ចំនួនសរុប៖ <span id="days_count_text">១ ថ្ងៃ</span>
              </span>
            </div>
          </div>

          <!-- Leave Type & Phone -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                ៤. ប្រភេទច្បាប់ឈប់សម្រាក <span class="text-rose-500">*</span>
              </label>
              <select 
                name="leave_type" 
                required 
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
              >
                <option value="ឈឺ (Sick)" {{ old('leave_type') == 'ឈឺ (Sick)' ? 'selected' : '' }}>🤒 ឈឺ</option>
                <option value="ធុរៈចាំបាច់ (Personal)" {{ old('leave_type') == 'ធុរៈចាំបាច់ (Personal)' ? 'selected' : '' }}>🏠 ធុរៈចាំបាច់ក្នុងគ្រួសារ</option>
                <option value="ផ្សេងៗ (Other)" {{ old('leave_type') == 'ផ្សេងៗ (Other)' ? 'selected' : '' }}>📝 ផ្សេងៗ</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                លេខទូរស័ព្ទអាណាព្យាបាល / សិស្ស
              </label>
              <input 
                type="text" 
                id="phone_input" 
                name="phone" 
                value="{{ old('phone') }}" 
                placeholder="012 345 678" 
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
              />
            </div>
          </div>

          <!-- Reason Textarea -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              ៥. មូលហេតុសុំច្បាប់លម្អិត <span class="text-rose-500">*</span>
            </label>
            <textarea 
              name="reason" 
              rows="3" 
              required 
              placeholder="សូមបញ្ជាក់មូលហេតុលម្អិត (ឧ. ផ្ដាសាយ ក្ដៅខ្លួន ឬត្រូវទៅខេត្តជាមួយគ្រួសារ...)" 
              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white rounded-xl text-xs font-khmer focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
            >{{ old('reason') }}</textarea>
          </div>

          <!-- Attachment Upload -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              ៦. ឯកសារភ្ជាប់ (លិខិតពេទ្យ ឬលិខិតសុំច្បាប់ពីអាណាព្យាបាល - បើមាន)
            </label>
            <div class="border-2 border-dashed border-slate-300 hover:border-indigo-400 rounded-2xl p-4 text-center bg-slate-50/60 relative cursor-pointer">
              <input 
                type="file" 
                name="attachment" 
                accept=".jpg,.jpeg,.png,.webp,.pdf" 
                id="attachment_file" 
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
              />
              <div class="flex items-center justify-center gap-2 text-xs text-slate-600">
                <i class="fa-solid fa-paperclip text-indigo-600 text-base"></i>
                <span id="file_label" class="font-semibold text-indigo-700">ចុចដើម្បីជ្រើសរើសឯកសារ (JPG, PNG, PDF)</span>
                <span class="text-[11px] text-slate-400">(អតិបរមា 5MB)</span>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-3">
            <button 
              type="submit" 
              class="w-full py-3.5 px-6 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-2xl text-xs sm:text-sm font-bold shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2 active:scale-98 cursor-pointer"
            >
              <i class="fa-solid fa-paper-plane"></i>
              <span>បញ្ជូនសំណើសុំច្បាប់</span>
            </button>
          </div>

        </div>
      </form>
    @endif

    <div class="text-center mt-6 text-xs text-slate-400">
      &copy; {{ date('Y') }} {{ $schoolName }}. រក្សាសិទ្ធិគ្រប់យ៉ាង។
    </div>

  </div>

  <!-- Dynamic Script for Class & Student sync -->
  <script>
    const classSelect = document.getElementById('class_select');
    const studentSelect = document.getElementById('student_select');
    const studentNameInput = document.getElementById('student_name_input');
    const studentCodeDisplay = document.getElementById('student_code_display');
    const phoneInput = document.getElementById('phone_input');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const daysCountText = document.getElementById('days_count_text');
    const attachmentFile = document.getElementById('attachment_file');
    const fileLabel = document.getElementById('file_label');

    // Calculate days count
    function calculateDays() {
      if (!startDateInput || !endDateInput || !daysCountText) return;
      const start = new Date(startDateInput.value);
      const end = new Date(endDateInput.value);
      if (start && end && !isNaN(start.getTime()) && !isNaN(end.getTime())) {
        if (end < start) {
          endDateInput.value = startDateInput.value;
        }
        const diffTime = Math.abs(new Date(endDateInput.value) - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        daysCountText.textContent = diffDays + ' ថ្ងៃ';
      }
    }

    if (startDateInput && endDateInput) {
      startDateInput.addEventListener('change', calculateDays);
      endDateInput.addEventListener('change', calculateDays);
      calculateDays();
    }

    // Attachment file name display
    if (attachmentFile && fileLabel) {
      attachmentFile.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
          fileLabel.textContent = '📄 ' + e.target.files[0].name;
        }
      });
    }

    // Student select handler
    if (studentSelect) {
      studentSelect.addEventListener('change', function() {
        const selectedOpt = studentSelect.options[studentSelect.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
          studentNameInput.value = selectedOpt.textContent.split('(')[0].trim();
          studentCodeDisplay.value = selectedOpt.getAttribute('data-code') || '';
          const phone = selectedOpt.getAttribute('data-phone');
          if (phone && phoneInput && !phoneInput.value) {
            phoneInput.value = phone;
          }
        } else {
          studentNameInput.value = '';
          studentCodeDisplay.value = '';
        }
      });
    }

    // Class select handler to fetch students
    if (classSelect) {
      classSelect.addEventListener('change', function() {
        const cls = classSelect.value;
        if (!cls) return;

        studentSelect.innerHTML = '<option value="">កំពុងទាញយកឈ្មោះសិស្ស...</option>';

        fetch('/api/public/students-by-class?class=' + encodeURIComponent(cls))
          .then(res => res.json())
          .then(data => {
            studentSelect.innerHTML = '<option value="">-- ជ្រើសរើសសិស្សក្នុងថ្នាក់ --</option>';
            if (data.students && data.students.length > 0) {
              data.students.forEach(st => {
                const opt = document.createElement('option');
                opt.value = st.id;
                opt.textContent = st.name + (st.student_code ? ` (${st.student_code})` : '');
                opt.setAttribute('data-code', st.student_code || '');
                opt.setAttribute('data-phone', st.phone || '');
                studentSelect.appendChild(opt);
              });
            } else {
              studentSelect.innerHTML = '<option value="">(ពុំទាន់មានទិន្នន័យសិស្សក្នុងថ្នាក់នេះ)</option>';
            }
          })
          .catch(err => {
            console.error(err);
            studentSelect.innerHTML = '<option value="">-- មានបញ្ហាក្នុងការទាញយកទិន្នន័យ --</option>';
          });
      });

      // If class is already selected on page load, trigger change to set initial student name
      if (classSelect.value && studentSelect.value) {
        const selectedOpt = studentSelect.options[studentSelect.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
          studentNameInput.value = selectedOpt.textContent.split('(')[0].trim();
          studentCodeDisplay.value = selectedOpt.getAttribute('data-code') || '';
        }
      }
    }
  </script>
</body>
</html>

