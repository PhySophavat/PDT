<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ចុះឈ្មោះគ្រូបង្រៀនថ្មី | LMSTICK School</title>

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
        <span class="text-xs font-bold tracking-wider text-blue-700 uppercase">LMSTICK FACULTY RECRUITMENT</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
        ទម្រង់ចុះឈ្មោះគ្រូបង្រៀនតាមអនឡាញ
      </h1>
      <p class="text-sm text-slate-600 mt-1 max-w-md mx-auto">
        សូមបំពេញព័ត៌មានខាងក្រោមឱ្យបានត្រឹមត្រូវ ដើម្បីស្នើសុំចូលបម្រើការងារបង្រៀនក្នុងសាលា
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
          សូមរង់ចាំការត្រួតពិនិត្យពីសាលា។ ខាងគណៈគ្រប់គ្រងសាលានឹងពិនិត្យប្រវត្តិរូប និងទាក់ទងមកលោកអ្នកតាមរយៈលេខទូរស័ព្ទដែលបានផ្ដល់ជូន ដើម្បីបញ្ជាក់ការបង្រៀន និងផ្ដល់អត្តលេខគ្រូផ្លូវការ។
        </p>

        <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-2xl max-w-md mx-auto text-left text-xs space-y-2 mb-8">
          <div class="flex items-center gap-2 text-slate-700 font-semibold">
            <i class="fa-solid fa-circle-info text-blue-600"></i>
            <span>ចំណាំសម្រាប់គ្រូបង្រៀន៖</span>
          </div>
          <p class="text-slate-500 leading-normal">
            បន្ទាប់ពីសាលាអនុម័តរួច លោកគ្រូ/អ្នកគ្រូ នឹងទទួលបានអត្តលេខសម្គាល់គ្រូ (Teacher ID Card: TCH-2026-XXX) និងកាលវិភាគបង្រៀនក្នុងប្រព័ន្ធ។
          </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
          <a href="{{ route('public.register-teacher') }}" class="w-full sm:w-auto px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-md transition">
            <i class="fa-solid fa-user-plus mr-1.5"></i> ចុះឈ្មោះគ្រូម្នាក់ទៀត
          </a>
          <a href="{{ url('/') }}" class="w-full sm:w-auto px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
            <i class="fa-solid fa-house mr-1.5"></i> ត្រឡប់ទៅទំព័រដើម
          </a>
        </div>
      </div>
    @else
      <!-- Registration Form Card -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
        
        <!-- Form Header Ribbon -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-4 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-lg">
              <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <div>
              <h3 class="font-bold text-sm sm:text-base">ព័ត៌មានចុះឈ្មោះគ្រូបង្រៀន (Faculty Registration)</h3>
              <p class="text-[11px] text-emerald-100">បំពេញតែម្តង ងាយស្រួល និងរហ័សទាន់ចិត្ត</p>
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

        <form action="{{ route('public.register-teacher.post') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-8">
          @csrf

          <!-- Section 1: Teacher Personal Information -->
          <div>
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-200">
              <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">១</span>
              <h2 class="text-base font-bold text-slate-800">ព័ត៌មានផ្ទាល់ខ្លួនគ្រូបង្រៀន (Personal Information)</h2>
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
                    placeholder="ឧ. សុខ ចាន់ដារ៉ា"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                  />
                </div>
              </div>

              <!-- Latin Name -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  ឈ្មោះជាអក្សរឡាតាំង (Latin Name) <span class="text-red-500">*</span>
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
                    placeholder="e.g. SOK CHANDARA"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 uppercase transition"
                  />
                </div>
              </div>

              <!-- Gender -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  ភេទ (Gender) <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                  <label class="flex items-center justify-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold cursor-pointer hover:bg-blue-50/50 has-checked:bg-blue-50 has-checked:border-blue-500 has-checked:text-blue-600 transition">
                    <input type="radio" name="gender" value="male" {{ old('gender', 'male') === 'male' ? 'checked' : '' }} class="text-blue-600 focus:ring-0">
                    <span><i class="fa-solid fa-mars text-blue-500 mr-1"></i> ប្រុស (Male)</span>
                  </label>
                  <label class="flex items-center justify-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold cursor-pointer hover:bg-pink-50/50 has-checked:bg-pink-50 has-checked:border-pink-500 has-checked:text-pink-600 transition">
                    <input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }} class="text-pink-600 focus:ring-0">
                    <span><i class="fa-solid fa-venus text-pink-500 mr-1"></i> ស្រី (Female)</span>
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
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                  />
                </div>
              </div>

              <!-- Phone -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  លេខទូរស័ព្ទ (Phone) <span class="text-red-500">*</span>
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
                    placeholder="ឧ. 012 111 222"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                  />
                </div>
              </div>

              <!-- Current Address -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  អាសយដ្ឋានបច្ចុប្បន្ន <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-location-dot text-xs"></i>
                  </span>
                  <input 
                    type="text" 
                    name="address" 
                    value="{{ old('address') }}" 
                    required 
                    placeholder="ផ្ទះលេខ ផ្លូវ សង្កាត់ ខណ្ឌ រាជធានី/ខេត្ត"
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                  />
                </div>
              </div>

              <!-- Photo Upload (optional) -->
              <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  រូបថតគ្រូបង្រៀន (Teacher Photo) <span class="text-slate-400 font-normal">(មិនទាមទារជាចាំបាច់)</span>
                </label>
                <div class="flex items-center gap-4 p-3.5 bg-slate-50 border border-dashed border-slate-300 rounded-2xl hover:border-emerald-400 transition">
                  <div id="photo-preview-box" class="w-16 h-16 rounded-xl bg-slate-200 flex items-center justify-center text-slate-400 overflow-hidden flex-shrink-0">
                    <i class="fa-regular fa-image text-2xl"></i>
                  </div>
                  <div class="flex-1">
                    <input 
                      type="file" 
                      id="photo-input" 
                      name="photo" 
                      accept="image/*" 
                      class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer"
                    />
                    <p class="text-[11px] text-slate-400 mt-1">ទំហំអតិបរមា 3MB (ប្រភេទ PNG, JPG, JPEG, WebP)</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 2: Specialization & Qualification -->
          <div>
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-200">
              <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">២</span>
              <h2 class="text-base font-bold text-slate-800">ព័ត៌មានជំនាញ និងកម្រិតវប្បធម៌ (Specialization & Qualification)</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
              <!-- Specialization -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  ជំនាញឯកទេសបង្រៀន (Specialization / Subject) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-book-open text-xs"></i>
                  </span>
                  <select 
                    name="specialization" 
                    required 
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                  >
                    <option value="">-- សូមជ្រើសរើសមុខវិជ្ជាឯកទេស --</option>
                    @foreach ($subjects as $sb)
                      <option value="{{ $sb }}" {{ old('specialization') === $sb ? 'selected' : '' }}>
                        {{ $sb }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <!-- Qualification -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                  កម្រិតវប្បធម៌ / សញ្ញាបត្រ (Qualification) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-graduation-cap text-xs"></i>
                  </span>
                  <select 
                    name="qualification" 
                    required 
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                  >
                    <option value="">-- សូមជ្រើសរើសកម្រិតវប្បធម៌ --</option>
                    @foreach ($qualifications as $qual)
                      <option value="{{ $qual }}" {{ old('qualification') === $qual ? 'selected' : '' }}>
                        {{ $qual }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Action -->
          <div class="pt-4 border-t border-slate-200">
            <button 
              type="submit" 
              class="w-full py-3.5 px-6 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-2xl shadow-lg shadow-emerald-600/30 hover:shadow-xl hover:shadow-emerald-600/40 active:scale-[0.99] transition flex items-center justify-center gap-2"
            >
              <i class="fa-solid fa-paper-plane"></i>
              <span>បញ្ជូនពាក្យចុះឈ្មោះ (Submit Teacher Application)</span>
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
      <p>&copy; {{ date('Y') }} LMSTICK School Management System. រក្សាសិទ្ធិគ្រប់យ៉ាង។</p>
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
