<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @php
    $sysSettings = \App\Models\SystemSetting::allSettings();
    $schoolName = $sysSettings['school_name'] ?? 'សាលារៀន ជំនាន់ថ្មី';
  @endphp
  <title>ចុះឈ្មោះអាណាព្យាបាលសិស្ស | {{ $schoolName }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
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
          }
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Kantumruy Pro', 'Plus Jakarta Sans', sans-serif; }
    input, select, textarea, button { font-family: inherit; }
    .input-field {
      @apply w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl
             focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-sm;
    }
    .label-text { @apply block text-xs font-semibold text-slate-700 mb-1; }
  </style>
</head>
<body class="bg-gradient-to-br from-[#dce4f7] via-[#e8efff] to-[#d6e0f5] min-h-screen py-6 px-3 sm:px-6 flex flex-col items-center font-khmer text-slate-800 antialiased">

  <div class="w-full max-w-3xl my-4">

    <!-- Header -->
    <div class="text-center mb-6">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/80 backdrop-blur-md rounded-full shadow-sm border border-white mb-3">
        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
        <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">{{ $schoolName }} • ចុះឈ្មោះអាណាព្យាបាល</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">ទម្រង់ចុះឈ្មោះអាណាព្យាបាលសិស្ស</h1>
      <p class="text-sm text-slate-600 mt-1 max-w-md mx-auto">
        បំពេញព័ត៌មានខាងក្រោម ដើម្បីភ្ជាប់គណនីអាណាព្យាបាលជាមួយប្រវត្តិការសិក្សារបស់កូន
      </p>
    </div>

    @if (session('registration_success'))
      <!-- Success Screen -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 p-8 sm:p-12 text-center">
        <div class="w-20 h-20 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl mb-5 shadow-inner">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full uppercase tracking-wider">បញ្ជូនជោគជ័យ</span>
        <h2 class="text-2xl font-bold text-slate-900 mt-4 mb-2">ការចុះឈ្មោះរបស់អ្នកត្រូវបានបញ្ជូនដោយជោគជ័យ!</h2>
        <p class="text-slate-600 text-sm max-w-lg mx-auto mb-8 leading-relaxed">
          សូមរង់ចាំការត្រួតពិនិត្យពីសាលា។ ខាងគណៈគ្រប់គ្រងនឹងទាក់ទងមកលោកអ្នកតាមរយៈលេខទូរស័ព្ទដែលបានផ្ដល់ ដើម្បីបញ្ជាក់ការភ្ជាប់គណនីអាណាព្យាបាល។
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
          <a href="{{ route('public.register-parent') }}" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-md transition">
            <i class="fa-solid fa-user-plus mr-1.5"></i> ចុះឈ្មោះម្នាក់ទៀត
          </a>
          <a href="{{ url('/') }}" class="w-full sm:w-auto px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
            <i class="fa-solid fa-house mr-1.5"></i> ទំព័រដើម
          </a>
        </div>
      </div>

    @else
      <!-- Registration Form -->
      <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-xl border border-white/80 p-5 sm:p-8 space-y-7">

        @if ($errors->any())
          <div class="p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-xs flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation text-base mt-0.5"></i>
            <div>
              <p class="font-bold mb-1">សូមត្រួតពិនិត្យ៖</p>
              <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif

        <form action="{{ route('public.register-parent.post') }}" method="POST" enctype="multipart/form-data" class="space-y-7">
          @csrf
          <input type="hidden" name="student_id" id="student_id_hidden">

          <!-- ════════ STEP 1: Student Lookup ════════ -->
          <div>
            <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
              <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">1</span>
              <div>
                <h2 class="text-sm font-bold text-slate-800">ស្វែងរកព័ត៌មានសិស្ស</h2>
                <p class="text-[11px] text-slate-500">បញ្ចូលអត្តលេខ (ID) របស់កូន ដែលមានក្នុងប័ណ្ណសិស្ស</p>
              </div>
            </div>

            <!-- ID Card Lookup Box -->
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl space-y-3">
              <label class="label-text text-blue-800">
                <i class="fa-solid fa-id-card mr-1 text-blue-600"></i>
                អត្តលេខសិស្ស (ID Card) <span class="text-red-500">*</span>
              </label>
              <div class="flex gap-2">
                <input
                  type="text"
                  id="id_card_input"
                  name="student_id_card"
                  value="{{ old('student_id_card') }}"
                  required
                  placeholder="ឧ. 1234, 4567, 7890..."
                  class="flex-1 px-3.5 py-2.5 bg-white border border-blue-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition text-sm font-mono font-bold"
                >
                <button
                  type="button"
                  id="lookup_btn"
                  onclick="lookupStudent()"
                  class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow transition flex items-center gap-2 cursor-pointer whitespace-nowrap"
                >
                  <i class="fa-solid fa-magnifying-glass" id="lookup_icon"></i>
                  <span id="lookup_text">ស្វែងរក</span>
                </button>
              </div>

              <!-- Lookup Result -->
              <div id="lookup_result" class="hidden mt-2 p-3 rounded-xl border text-xs font-medium"></div>

              <!-- Student Found Card -->
              <div id="student_found_card" class="hidden mt-3 p-4 bg-white rounded-2xl border-2 border-emerald-300 shadow-sm">
                <div class="flex items-start gap-3">
                  <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 flex-shrink-0">
                    <i class="fa-solid fa-graduation-cap text-lg"></i>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                      <span class="font-bold text-slate-900 text-sm" id="found_name">-</span>
                      <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold" id="found_grade">-</span>
                      <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold">✓ រកឃើញ</span>
                    </div>
                    <p class="text-slate-500 text-xs mt-1" id="found_detail">-</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ════════ STEP 2: Parent / Guardian Info ════════ -->
          <div>
            <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
              <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">2</span>
              <div>
                <h2 class="text-sm font-bold text-slate-800">ព័ត៌មានអាណាព្យាបាល</h2>
                <p class="text-[11px] text-slate-500">បំពេញព័ត៌មានអ្នកដែលចុះឈ្មោះ — ឪពុក, ម្តាយ, ឬអាណាព្យាបាល</p>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

              <!-- Relationship (Who is registering) -->
              <div class="sm:col-span-2">
                <label class="label-text">
                  <i class="fa-solid fa-people-roof mr-1 text-slate-500"></i>
                  អ្នកចុះឈ្មោះជា <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                  @foreach([
                    ['value' => 'ឪពុក',       'icon' => 'fa-person', 'color' => 'blue',   'label' => '👨 ឪពុក'],
                    ['value' => 'ម្តាយ',       'icon' => 'fa-person-dress', 'color' => 'pink', 'label' => '👩 ម្តាយ'],
                    ['value' => 'អាណាព្យាបាល', 'icon' => 'fa-user-shield', 'color' => 'purple', 'label' => '🛡 អាណាព្យាបាល'],
                    ['value' => 'បងប្អូន',     'icon' => 'fa-user-group', 'color' => 'orange', 'label' => '👥 បងប្អូន'],
                  ] as $rel)
                    <label class="relative cursor-pointer">
                      <input type="radio" name="relationship" value="{{ $rel['value'] }}"
                             {{ old('relationship', 'ឪពុក') == $rel['value'] ? 'checked' : '' }}
                             class="peer sr-only" required>
                      <div class="p-3 text-center rounded-xl border-2 border-slate-200 bg-slate-50 font-semibold text-xs text-slate-600
                                  peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 hover:border-blue-300 transition select-none">
                        {{ $rel['label'] }}
                      </div>
                    </label>
                  @endforeach
                </div>
              </div>

              <!-- Khmer Name -->
              <div>
                <label class="label-text">ឈ្មោះអ្នកចុះឈ្មោះ (ភាសាខ្មែរ) <span class="text-red-500">*</span></label>
                <input type="text" name="name_khmer" id="reg_name_khmer" value="{{ old('name_khmer') }}" required placeholder="ឧ. ណុំ ចន្ទា" class="input-field">
              </div>

              <!-- Latin Name -->
              <div>
                <label class="label-text">ឈ្មោះ (អក្សរឡាតាំង)</label>
                <input type="text" name="name_latin" id="reg_name_latin" value="{{ old('name_latin') }}" placeholder="e.g. NOM CHANDA" class="input-field uppercase">
              </div>

              <!-- Gender -->
              <div>
                <label class="label-text">ភេទ <span class="text-red-500">*</span></label>
                <select name="gender" required class="input-field">
                  <option value="">-- ជ្រើសរើស --</option>
                  <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ប្រុស</option>
                  <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>ស្រី</option>
                </select>
              </div>

              <!-- Phone -->
              <div>
                <label class="label-text">លេខទូរស័ព្ទទំនាក់ទំនង <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="012 345 678" class="input-field font-mono">
                <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                  <i class="fa-solid fa-circle-info text-blue-400 text-[10px]"></i>
                  បញ្ចូលលេខជាភាសាអង់គ្លេស (0-9)
                </p>
              </div>

              <!-- Email -->
              <div>
                <label class="label-text">អ៊ីមែល</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. parent@gmail.com" class="input-field">
              </div>

              <!-- Occupation -->
              <div>
                <label class="label-text">មុខរបរ / អាជីព</label>
                <input type="text" name="occupation" value="{{ old('occupation') }}" placeholder="ឧ. អាជីវករ, មន្ត្រី, គ្រូ..." class="input-field">
              </div>

              <!-- Address -->
              <div class="sm:col-span-2">
                <label class="label-text">អាសយដ្ឋានបច្ចុប្បន្ន <span class="text-red-500">*</span></label>
                <textarea name="address" id="reg_address" rows="2" required placeholder="ភូមិ / ឃុំ-សង្កាត់ / ស្រុក-ខណ្ឌ / ខេត្ត-រាជធានី" class="input-field resize-none">{{ old('address') }}</textarea>
              </div>

            </div>
          </div>

          <!-- ════════ STEP 3: Father / Mother / Guardian Names ════════ -->
          <div>
            <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
              <span class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold">3</span>
              <div>
                <h2 class="text-sm font-bold text-slate-800">ព័ត៌មានឪពុក / ម្តាយ / អាណាព្យាបាល</h2>
                <p class="text-[11px] text-slate-500">ជួយបំពេញតាមលទ្ធភាព — ព័ត៌មាននេះប្រើដើម្បីភ្ជាប់ទំនាក់ទំនងជាមួយប្រវត្តិសិស្ស</p>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

              <!-- Father Name -->
              <div>
                <label class="label-text">
                  <span class="text-blue-500 mr-1">👨</span> ឈ្មោះឪពុក
                </label>
                <input type="text" name="father_name" id="father_name" value="{{ old('father_name') }}" placeholder="ឧ. ណុំ សុខ" class="input-field">
              </div>

              <!-- Father Phone -->
              <div>
                <label class="label-text">
                  <span class="text-blue-500 mr-1">📞</span> លេខទូរស័ព្ទឪពុក
                </label>
                <input type="tel" name="father_phone" id="father_phone" value="{{ old('father_phone') }}" placeholder="012 345 678" class="input-field font-mono">
              </div>

              <!-- Mother Name -->
              <div>
                <label class="label-text">
                  <span class="text-pink-500 mr-1">👩</span> ឈ្មោះម្តាយ
                </label>
                <input type="text" name="mother_name" id="mother_name" value="{{ old('mother_name') }}" placeholder="ឧ. សុខ ចន្ទា" class="input-field">
              </div>

              <!-- Mother Phone -->
              <div>
                <label class="label-text">
                  <span class="text-pink-500 mr-1">📞</span> លេខទូរស័ព្ទម្តាយ
                </label>
                <input type="tel" name="mother_phone" id="mother_phone" value="{{ old('mother_phone') }}" placeholder="012 345 678" class="input-field font-mono">
              </div>

              <!-- Guardian Name -->
              <div>
                <label class="label-text">
                  <span class="text-purple-500 mr-1">🛡</span> ឈ្មោះអាណាព្យាបាល <span class="text-[10px] text-slate-400">(បើខុសពីឪពុក/ម្តាយ)</span>
                </label>
                <input type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name') }}" placeholder="ឧ. ណុំ វិបុល" class="input-field">
              </div>

              <!-- Guardian Phone -->
              <div>
                <label class="label-text">
                  <span class="text-purple-500 mr-1">📞</span> លេខទូរស័ព្ទអាណាព្យាបាល
                </label>
                <input type="tel" name="guardian_phone" id="guardian_phone" value="{{ old('guardian_phone') }}" placeholder="012 345 678" class="input-field font-mono">
              </div>

            </div>
          </div>

          <!-- ════════ STEP 4: Photo ════════ -->
          <div>
            <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
              <span class="w-7 h-7 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center text-xs font-bold">4</span>
              <h2 class="text-sm font-bold text-slate-800">រូបថតអាណាព្យាបាល <span class="text-[11px] font-normal text-slate-400">(ស្រេចចិត្ត)</span></h2>
            </div>
            <div class="flex items-center gap-4 p-4 bg-slate-50 border border-dashed border-slate-300 rounded-2xl hover:bg-slate-100/70 transition">
              <div class="w-16 h-16 rounded-xl bg-slate-200 flex-shrink-0 flex items-center justify-center text-slate-400 overflow-hidden" id="photoPreview">
                <i class="fa-solid fa-user text-2xl"></i>
              </div>
              <div class="flex-1">
                <label class="block text-xs font-semibold text-slate-700 mb-1">ជ្រើសរើសរូបថត (JPG, PNG, WebP — មិនលើស 3MB)</label>
                <input type="file" name="photo" accept="image/*" id="photoInput"
                  class="block w-full text-slate-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
              </div>
            </div>
          </div>

          <!-- Submit -->
          <div class="pt-4 border-t border-slate-100">
            <button type="submit"
              class="w-full py-3.5 px-6 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-bold rounded-2xl shadow-lg shadow-blue-600/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
              <i class="fa-solid fa-paper-plane"></i>
              <span>បញ្ជូនទម្រង់ចុះឈ្មោះអាណាព្យាបាល</span>
            </button>
            <p class="text-[11px] text-center text-slate-400 mt-2">
              ព័ត៌មានរបស់អ្នកនឹងត្រូវបញ្ជូនទៅគណៈគ្រប់គ្រងសាលាដោយសុវត្ថិភាព
            </p>
          </div>

        </form>
      </div>
    @endif

    <div class="text-center text-xs text-slate-400 mt-6">
      <p>© {{ date('Y') }} {{ $schoolName }} • ប្រព័ន្ធគ្រប់គ្រងសាលា Portal Sala</p>
    </div>

  </div>

  <script>
    // ── Photo preview ──
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    if (photoInput && photoPreview) {
      photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = evt => {
            photoPreview.innerHTML = `<img src="${evt.target.result}" class="w-full h-full object-cover">`;
          };
          reader.readAsDataURL(file);
        }
      });
    }

    // ── Student ID Card Lookup ──
    async function lookupStudent() {
      const idCard = document.getElementById('id_card_input').value.trim();
      if (!idCard) {
        showResult('danger', 'សូមបញ្ចូលអត្តលេខសិស្សជាមុន');
        return;
      }

      const btn = document.getElementById('lookup_btn');
      const icon = document.getElementById('lookup_icon');
      const text = document.getElementById('lookup_text');
      btn.disabled = true;
      icon.className = 'fa-solid fa-spinner fa-spin';
      text.textContent = 'កំពុងស្វែងរក...';

      try {
        const res = await fetch(`/api/public/student-by-id-card?id_card=${encodeURIComponent(idCard)}`, {
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await res.json();

        if (data.found) {
          // Set hidden student_id
          document.getElementById('student_id_hidden').value = data.student_id;

          // Show found card
          document.getElementById('found_name').textContent = data.name_khmer || data.name_latin || '-';
          document.getElementById('found_grade').textContent = data.grade ? 'ថ្នាក់ ' + data.grade : '-';
          document.getElementById('found_detail').textContent =
            [data.name_latin, data.gender === 'female' ? 'ស្រី' : 'ប្រុស'].filter(Boolean).join(' · ');
          document.getElementById('student_found_card').classList.remove('hidden');

          // Auto-fill parent fields from student record
          autoFill('father_name',  data.father_name);
          autoFill('father_phone', data.father_phone);
          autoFill('mother_name',  data.mother_name);
          autoFill('mother_phone', data.mother_phone);
          autoFill('guardian_name',  data.parent_name);
          autoFill('guardian_phone', data.parent_phone);
          autoFill('reg_address',   data.address);

          showResult('success', `✓ រកឃើញ: <strong>${data.name_khmer || data.name_latin}</strong> — ថ្នាក់ ${data.grade || '-'}`);
        } else {
          document.getElementById('student_id_hidden').value = '';
          document.getElementById('student_found_card').classList.add('hidden');
          showResult('danger', `✗ ${data.message || 'រកមិនឃើញសិស្ស'} — សូមពិនិត្យអត្តលេខ ឬទាក់ទងសាលា`);
        }
      } catch (e) {
        showResult('danger', 'មានបញ្ហាក្នុងការស្វែងរក — សូមព្យាយាមម្ដងទៀត');
      } finally {
        btn.disabled = false;
        icon.className = 'fa-solid fa-magnifying-glass';
        text.textContent = 'ស្វែងរក';
      }
    }

    function autoFill(id, value) {
      const el = document.getElementById(id);
      if (el && !el.value && value) {
        el.value = value;
        el.classList.add('bg-green-50', 'border-green-300');
        setTimeout(() => el.classList.remove('bg-green-50', 'border-green-300'), 2000);
      }
    }

    function showResult(type, msg) {
      const el = document.getElementById('lookup_result');
      el.classList.remove('hidden', 'bg-emerald-50', 'border-emerald-300', 'text-emerald-800', 'bg-red-50', 'border-red-300', 'text-red-800');
      if (type === 'success') {
        el.classList.add('bg-emerald-50', 'border-emerald-300', 'text-emerald-800');
      } else {
        el.classList.add('bg-red-50', 'border-red-300', 'text-red-800');
      }
      el.innerHTML = msg;
    }

    // Allow Enter key in ID card input to trigger lookup
    document.getElementById('id_card_input')?.addEventListener('keydown', e => {
      if (e.key === 'Enter') { e.preventDefault(); lookupStudent(); }
    });
  </script>
</body>
</html>
