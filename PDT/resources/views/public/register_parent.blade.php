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
  <title>ចុះឈ្មោះអាណាព្យាបាលសិស្ស | {{ $schoolName }}</title>

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
        <span class="text-xs font-bold tracking-wider text-emerald-700 uppercase">{{ $schoolName }} • អាណាព្យាបាលសិស្ស</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
        ទម្រង់ចុះឈ្មោះអាណាព្យាបាលសិស្សតាមអនឡាញ
      </h1>
      <p class="text-sm text-slate-600 mt-1 max-w-md mx-auto">
        សូមបំពេញព័ត៌មានខាងក្រោមឱ្យបានត្រឹមត្រូវ ដើម្បីភ្ជាប់ទំនាក់ទំនង និងតាមដានការសិក្សារបស់កូនៗ
      </p>
    </div>

    @if (session('registration_success'))
      <!-- Success Confirmation Screen -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 p-8 sm:p-12 text-center transition-all animate-fadeIn">
        <div class="w-20 h-20 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl mb-5 shadow-inner">
          <i class="fa-solid fa-circle-check"></i>
        </div>

        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full uppercase tracking-wider">
          បញ្ជូនជោគជ័យ
        </span>

        <h2 class="text-2xl font-bold text-slate-900 mt-4 mb-2">
          ការចុះឈ្មោះរបស់អ្នកត្រូវបានបញ្ជូនដោយជោគជ័យ!
        </h2>
        <p class="text-slate-600 text-sm max-w-lg mx-auto mb-8 leading-relaxed">
          សូមរង់ចាំការត្រួតពិនិត្យពីសាលា។ ខាងគណៈគ្រប់គ្រងសាលានឹងផ្ទៀងផ្ទាត់ព័ត៌មាន និងទាក់ទងមកលោកអ្នកតាមរយៈលេខទូរស័ព្ទដែលបានផ្ដល់ជូន ដើម្បីបញ្ជាក់ការភ្ជាប់គណនីអាណាព្យាបាល និងផ្ដល់អត្តលេខផ្លូវការ។
        </p>

        <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-2xl max-w-md mx-auto text-left text-xs space-y-2 mb-8">
          <div class="flex items-center gap-2 text-slate-700 font-semibold">
            <i class="fa-solid fa-circle-info text-emerald-600"></i>
            <span>ចំណាំសម្រាប់អាណាព្យាបាល៖</span>
          </div>
          <p class="text-slate-500 leading-normal">
            បន្ទាប់ពីសាលាអនុម័តរួច លោកអ្នកនឹងអាចប្រើលេខទូរស័ព្ទ ឬអ៊ីមែលដើម្បីតាមដានវត្តមាន ពិន្ទុ និងការបង់ថ្លៃសិក្សារបស់កូនៗក្នុងប្រព័ន្ធបានយ៉ាងងាយស្រួល។
          </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
          <a href="{{ route('public.register-parent') }}" class="w-full sm:w-auto px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-md transition">
            <i class="fa-solid fa-user-plus mr-1.5"></i> ចុះឈ្មោះម្នាក់ទៀត
          </a>
          <a href="{{ url('/') }}" class="w-full sm:w-auto px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
            <i class="fa-solid fa-house mr-1.5"></i> ត្រឡប់ទៅទំព័រដើម
          </a>
        </div>
      </div>
    @else
      <!-- Registration Form Card -->
      <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-xl border border-white/80 p-6 sm:p-10 transition-all">
        
        @if ($errors->any())
          <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-xs flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation text-base mt-0.5"></i>
            <div>
              <p class="font-bold mb-1">សូមត្រួតពិនិត្យកំហុសខាងក្រោម៖</p>
              <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif

        <form action="{{ route('public.register-parent.post') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
          @csrf

          <!-- Section 1: Parent Info -->
          <div>
            <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
              <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">1</span>
              <h2 class="text-sm font-bold text-slate-800">ព័ត៌មានផ្ទាល់ខ្លួនអាណាព្យាបាល</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
              
              <!-- Khmer Name -->
              <div>
                <label class="block font-semibold text-slate-700 mb-1">
                  ឈ្មោះពេញជាភាសាខ្មែរ <span class="text-red-500">*</span>
                </label>
                <input 
                  type="text" 
                  name="name_khmer" 
                  value="{{ old('name_khmer') }}" 
                  required 
                  placeholder="ឧ. សុខ វិបុល" 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                >
              </div>

              <!-- Latin Name -->
              <div>
                <label class="block font-semibold text-slate-700 mb-1">
                  ឈ្មោះជាអក្សរឡាតាំង
                </label>
                <input 
                  type="text" 
                  name="name_latin" 
                  value="{{ old('name_latin') }}" 
                  placeholder="e.g. SOK VIBOL" 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                >
              </div>

              <!-- Gender -->
              <div>
                <label class="block font-semibold text-slate-700 mb-1">
                  ភេទ <span class="text-red-500">*</span>
                </label>
                <select 
                  name="gender" 
                  required 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                >
                  <option value="">-- ជ្រើសរើសភេទ --</option>
                  <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ប្រុស</option>
                  <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>ស្រី</option>
                </select>
              </div>

              <!-- Relationship -->
              <div>
                <label class="block font-semibold text-slate-700 mb-1">
                  ទំនាក់ទំនងត្រូវជា <span class="text-red-500">*</span>
                </label>
                <select 
                  name="relationship" 
                  required 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-medium"
                >
                  <option value="ឪពុក" {{ old('relationship') == 'ឪពុក' ? 'selected' : '' }}>ឪពុក</option>
                  <option value="ម្តាយ" {{ old('relationship') == 'ម្តាយ' ? 'selected' : '' }}>ម្តាយ</option>
                  <option value="អាណាព្យាបាល" {{ old('relationship') == 'អាណាព្យាបាល' ? 'selected' : '' }}>អាណាព្យាបាល</option>
                  <option value="បងប្អូនបង្កើត" {{ old('relationship') == 'បងប្អូនបង្កើត' ? 'selected' : '' }}>បងប្អូនបង្កើត</option>
                </select>
              </div>

              <!-- Phone -->
              <div>
                <label class="block font-semibold text-slate-700 mb-1">
                  លេខទូរស័ព្ទទំនាក់ទំនង <span class="text-red-500">*</span>
                </label>
                <input 
                  type="tel" 
                  id="parent_reg_phone"
                  name="phone" 
                  value="{{ old('phone') }}" 
                  required 
                  placeholder="ឧ. 012 345 678 (លេខ 0-9)" 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-mono"
                >
                <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1 font-khmer">
                  <i class="fa-solid fa-circle-info text-emerald-600 text-[10px]"></i>
                  <span>សូមបញ្ចូលលេខជាភាសាអង់គ្លេស (0-9) កុំប្រើលេខខ្មែរ ០-៩</span>
                </p>
              </div>

              <!-- Email -->
              <div>
                <label class="block font-semibold text-slate-700 mb-1">
                  អ៊ីមែល
                </label>
                <input 
                  type="email" 
                  name="email" 
                  value="{{ old('email') }}" 
                  placeholder="e.g. parent@gmail.com" 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                >
              </div>

              <!-- Occupation -->
              <div class="sm:col-span-2">
                <label class="block font-semibold text-slate-700 mb-1">
                  មុខរបរ / អាជីព
                </label>
                <input 
                  type="text" 
                  name="occupation" 
                  value="{{ old('occupation') }}" 
                  placeholder="ឧ. អាជីវករ, មន្ត្រីរាជការ, វិស្វករ..." 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                >
              </div>

              <!-- Address -->
              <div class="sm:col-span-2">
                <label class="block font-semibold text-slate-700 mb-1">
                  អាសយដ្ឋានបច្ចុប្បន្ន <span class="text-red-500">*</span>
                </label>
                <textarea 
                  name="address" 
                  rows="2" 
                  required 
                  placeholder="ភូមិ/ឃុំ/ស្រុក/ខេត្ត..." 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                >{{ old('address') }}</textarea>
              </div>

            </div>
          </div>

          <!-- Section 2: Child/Student Link -->
          <div>
            <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
              <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">2</span>
              <h2 class="text-sm font-bold text-slate-800">ព័ត៌មានសិស្សដែលត្រូវភ្ជាប់</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
              
              <!-- Child Name -->
              <div>
                <label class="block font-semibold text-slate-700 mb-1">
                  ឈ្មោះសិស្ស (កូន ឬកុមារក្នុងបន្ទុក) <span class="text-red-500">*</span>
                </label>
                <input 
                  type="text" 
                  name="child_name" 
                  value="{{ old('child_name') }}" 
                  required 
                  placeholder="ឧ. សុខ សុភក្ត្រ" 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                >
              </div>

              <!-- Child Grade / Class -->
              <div>
                <label class="block font-semibold text-slate-700 mb-1">
                  ថ្នាក់រៀនបច្ចុប្បន្ន
                </label>
                <input 
                  type="text" 
                  name="child_grade" 
                  value="{{ old('child_grade') }}" 
                  placeholder="ឧ. 7A, 8B, 12A..." 
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                >
              </div>

            </div>
          </div>

          <!-- Section 3: Photo Upload -->
          <div>
            <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
              <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">3</span>
              <h2 class="text-sm font-bold text-slate-800">រូបថតអាណាព្យាបាល (ស្រេចចិត្ត)</h2>
            </div>

            <div class="text-xs">
              <div class="flex items-center gap-4 p-4 bg-slate-50 border border-dashed border-slate-300 rounded-2xl hover:bg-slate-100/70 transition">
                <div class="w-16 h-16 rounded-xl bg-slate-200 flex-shrink-0 flex items-center justify-center text-slate-400 overflow-hidden" id="photoPreview">
                  <i class="fa-solid fa-user text-2xl"></i>
                </div>
                <div class="flex-1">
                  <label class="block font-semibold text-slate-700 mb-1">ជ្រើសរើសរូបថត (JPG, PNG, WebP មិនលើស 3MB)</label>
                  <input 
                    type="file" 
                    name="photo" 
                    accept="image/*" 
                    id="photoInput" 
                    class="block w-full text-slate-500 file:mr-4 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer"
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-4 border-t border-slate-100">
            <button 
              type="submit" 
              class="w-full py-3.5 px-6 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-bold rounded-2xl shadow-lg shadow-emerald-600/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2"
            >
              <i class="fa-solid fa-paper-plane"></i>
              <span>បញ្ជូនទម្រង់ចុះឈ្មោះអាណាព្យាបាល</span>
            </button>
            <p class="text-[11px] text-center text-slate-400 mt-2">
              ទិន្នន័យរបស់អ្នកនឹងត្រូវបញ្ជូនទៅកាន់គណៈគ្រប់គ្រងសាលាដោយសុវត្ថិភាព។
            </p>
          </div>

        </form>
      </div>
    @endif

    <!-- Footer -->
    <div class="text-center text-xs text-slate-400 mt-6 space-y-1">
      <p>© {{ date('Y') }} {{ $schoolName }}. រក្សាសិទ្ធិគ្រប់យ៉ាង។</p>
    </div>

  </div>

  <script>
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    if (photoInput && photoPreview) {
      photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(evt) {
            photoPreview.innerHTML = `<img src="${evt.target.result}" class="w-full h-full object-cover">`;
          };
          reader.readAsDataURL(file);
        }
      });
    }
  </script>
</body>
</html>
