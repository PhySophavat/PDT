@extends('layouts.app')

@section('title', 'sala-portal')

@section('content')
<div class="space-y-6 font-khmer w-full">
  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">វត្តមានសិស្សានុសិស្ស</h1>
      <nav class="flex items-center gap-1.5 text-xs text-slate-400 mt-1">
        <span>ទំព័រដើម</span>
        <span>&raquo;</span>
        <span class="text-indigo-600 font-medium">វត្តមាន</span>
      </nav>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      <button 
        type="button"
        onclick="openLeaveRequestModal()" 
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-semibold rounded-full shadow-md shadow-blue-600/20 transition-all hover:shadow-lg hover:shadow-blue-600/30 cursor-pointer"
      >
        <i class="fa-solid fa-file-pen"></i>
        <span>ស្នើសុំច្បាប់</span>
      </button>

      <a 
        href="{{ route('public.leave-request') }}" 
        target="_blank"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-full shadow-md shadow-indigo-600/20 transition-all hover:shadow-lg hover:shadow-indigo-600/30"
      >
        <i class="fa-solid fa-link"></i>
        <span>តំណភ្ជាប់សុំច្បាប់</span>
      </a>

      <button 
        onclick="window.print()" 
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-full shadow-md transition-all"
      >
        <i class="fa-solid fa-print"></i>
        <span>បោះពុម្ព</span>
      </button>
    </div>
  </div>

  <!-- Entity Banner -->
  <div class="bg-gradient-to-r from-blue-700 via-indigo-600 to-indigo-700 text-white rounded-2xl p-6 shadow-md flex flex-wrap items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-bold">ព័ត៌មានលម្អិតសិស្ស និងវត្តមាន</h2>
      <p class="text-xs text-indigo-100 mt-0.5">ថ្នាក់រៀន: ថ្នាក់ {{ $selectedClass }} | កាលបរិច្ឆេទ: {{ $selectedDate }}</p>
    </div>
    <span class="text-xs px-3 py-1 bg-white/20 rounded-full font-mono font-bold">ADM-ATT-SYS</span>
  </div>

  <!-- 4 Stat Cards with Distinct Icons & Soft Background Badges -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
      <div>
        <div class="text-xs font-bold text-slate-500">វត្តមានសរុប</div>
        <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $presentCount }}</div>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-check"></i>
      </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
      <div>
        <div class="text-xs font-bold text-slate-500">យឺតសរុប</div>
        <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $lateCount }}</div>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-regular fa-clock"></i>
      </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
      <div>
        <div class="text-xs font-bold text-slate-500">អវត្តមានសរុប</div>
        <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $absentCount }}</div>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-circle-xmark"></i>
      </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
      <div>
        <div class="text-xs font-bold text-slate-500">ច្បាប់/សម្រាកសរុប</div>
        <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $excusedCount }}</div>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-regular fa-calendar-check"></i>
      </div>
    </div>
  </div>

  <!-- Advanced Filter Bar -->
  <form method="GET" action="{{ route('attendance.index') }}" class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
      <div>
        <label class="block font-bold text-slate-700 mb-1">ថ្នាក់រៀន:</label>
        <select name="class" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-indigo-700 font-bold rounded-xl focus:bg-white focus:outline-none">
          @foreach ($classes as $c)
            <option value="{{ $c }}" {{ $selectedClass == $c ? 'selected' : '' }}>ថ្នាក់ {{ $c }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block font-bold text-slate-700 mb-1">ឆ្នាំសិក្សា:</label>
        <select name="academic_year_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-700 font-semibold rounded-xl focus:bg-white focus:outline-none">
          @foreach ($academicYears as $y)
            <option value="{{ $y->id }}">{{ $y->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block font-bold text-slate-700 mb-1">ខែ:</label>
        <select name="month" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-700 font-semibold rounded-xl focus:bg-white focus:outline-none">
          <option value="">-- គ្រប់ខែ --</option>
          <option value="1">មករា</option>
          <option value="2">កុម្ភៈ</option>
          <option value="3">មីនា</option>
          <option value="4">មេសា</option>
          <option value="5">ឧសភា</option>
          <option value="6">មិថុនា</option>
          <option value="7">កក្កដា</option>
          <option value="8">សីហា</option>
          <option value="9">កញ្ញា</option>
          <option value="10">តុលា</option>
          <option value="11">វិច្ឆិកា</option>
          <option value="12">ធ្នូ</option>
        </select>
      </div>

      <div>
        <label class="block font-bold text-slate-700 mb-1">កាលបរិច្ឆេទ:</label>
        <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-700 font-semibold rounded-xl focus:bg-white focus:outline-none" />
      </div>
    </div>
  </form>

  <!-- Daily Attendance Sheet Table -->
  <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <div>
        <h3 class="font-bold text-slate-800 text-base">បញ្ជីវត្តមានសិស្សប្រចាំថ្ងៃ</h3>
        <p class="text-xs text-slate-500">ស្រង់វត្តមានសិស្សថ្នាក់ {{ $selectedClass }} សម្រាប់ថ្ងៃទី {{ $selectedDate }}</p>
      </div>
    </div>

    @if ($students->isEmpty())
      <div class="py-12 text-center text-slate-400 text-xs">
        ពុំទាន់មានទិន្នន័យសិស្សក្នុងថ្នាក់នេះឡើយ។
      </div>
    @else
      <div class="overflow-x-auto border border-slate-200/80 rounded-2xl">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="bg-slate-100/90 text-slate-700 uppercase font-bold border-b border-slate-200">
              <th class="py-3 px-4">អត្តលេខ</th>
              <th class="py-3 px-4">ឈ្មោះសិស្ស</th>
              <th class="py-3 px-4">ភេទ</th>
              <th class="py-3 px-4 text-center">ស្ថានភាពវត្តមាន</th>
              <th class="py-3 px-4">សម្គាល់</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach ($students as $st)
              @php $att = $attendances->get($st->id); @endphp
              <tr class="hover:bg-slate-50">
                <td class="py-3 px-4 font-mono font-bold text-slate-600">{{ $st->student_id_card }}</td>
                <td class="py-3 px-4 font-bold text-slate-800">{{ $st->display_name }}</td>
                <td class="py-3 px-4 text-slate-600">{{ $st->gender == 'female' ? 'ស្រី' : 'ប្រុស' }}</td>
                <td class="py-3 px-4 text-center">
                  <span class="px-3 py-1 rounded-full text-xs font-bold
                    {{ ($att?->status == 'present' || !$att) ? 'bg-emerald-100 text-emerald-800' : '' }}
                    {{ $att?->status == 'late' ? 'bg-amber-100 text-amber-800' : '' }}
                    {{ $att?->status == 'absent' ? 'bg-rose-100 text-rose-800' : '' }}
                    {{ $att?->status == 'excused' ? 'bg-blue-100 text-blue-800' : '' }}
                  ">
                    {{ $att?->status == 'late' ? 'យឺត' : ($att?->status == 'absent' ? 'អវត្តមាន' : ($att?->status == 'excused' ? 'ច្បាប់' : 'វត្តមាន')) }}
                  </span>
                </td>
                <td class="py-3 px-4 text-slate-500 font-khmer">{{ $att?->remark ?: '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>

<!-- Modal: In-App Student Leave Request Modal (Blade Fallback) -->
<div 
  id="bladeLeaveModal" 
  class="fixed inset-0 z-50 hidden items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs font-khmer transition-opacity"
  onclick="if(event.target === this) closeLeaveRequestModal()"
>
  <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
    <!-- 4. Sticky Modal Header -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 p-4 sm:p-5 text-white flex items-center justify-between flex-shrink-0 sticky top-0 z-20 shadow-xs">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl shadow-inner">
          <i class="fa-solid fa-file-pen text-lg"></i>
        </div>
        <div>
          <h3 class="font-bold text-base leading-tight">ទម្រង់ស្នើសុំច្បាប់ឈប់សម្រាក</h3>
          <p class="text-xs text-indigo-100 mt-0.5">បំពេញព័ត៌មានដើម្បីផ្ញើសំណើជូនលោកគ្រូ/អ្នកគ្រូ</p>
        </div>
      </div>
      <button 
        onclick="closeLeaveRequestModal()" 
        type="button"
        class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition cursor-pointer"
      >
        ✕
      </button>
    </div>

    <!-- Scrollable Form Body -->
    <form id="bladeLeaveForm" onsubmit="handleBladeLeaveSubmit(event)" class="flex flex-col flex-1 min-h-0 overflow-hidden">
      @csrf
      <div class="p-4 sm:p-6 space-y-4 max-h-[75vh] overflow-y-auto text-xs flex-1">
        
        <!-- Pre-filled User Information Card -->
        @php
          $authUser = Auth::user();
          $studentProfile = $authUser?->student ?? \App\Models\Student::where('email', $authUser?->email)->first();
          $prefillName = $studentProfile?->name_khmer ?: ($studentProfile?->name_latin ?: ($authUser?->name ?? ''));
          $prefillClass = $studentProfile?->grade ?? ($selectedClass ?? '7A');
          $prefillIdCard = $studentProfile?->student_id_card ?? '';
          $prefillPhone = $studentProfile?->phone ?? ($authUser?->phone ?? '');
        @endphp
        <div class="p-4 bg-gradient-to-br from-indigo-50/80 via-blue-50/50 to-slate-50 rounded-2xl border border-indigo-100/80 shadow-2xs">
          <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-700 flex items-center gap-1.5">
              <i class="fa-solid fa-circle-check text-emerald-500"></i>
              ព័ត៌មានសិស្ស (បំពេញស្វ័យប្រវត្តិ)
            </span>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800">
              ថ្នាក់ {{ $prefillClass }}
            </span>
          </div>
          <div class="grid grid-cols-2 gap-3 text-slate-700">
            <div>
              <span class="text-[11px] text-slate-400 block">ឈ្មោះសិស្ស:</span>
              <span class="font-bold text-slate-900 text-sm">{{ $prefillName }}</span>
            </div>
            @if ($prefillIdCard)
              <div>
                <span class="text-[11px] text-slate-400 block">អត្តលេខ:</span>
                <span class="font-mono font-bold text-indigo-600">{{ $prefillIdCard }}</span>
              </div>
            @endif
          </div>
        </div>

        <input type="hidden" name="class_name" value="{{ $prefillClass }}" />
        <input type="hidden" name="student_id" value="{{ $studentProfile?->id ?? '' }}" />
        <input type="hidden" name="student_name" value="{{ $prefillName }}" />
        <input type="hidden" name="leave_type" id="bladeLeaveTypeInput" value="ច្បាប់" />
        <input type="hidden" name="session" id="bladeSessionInput" value="full_day" />
        <input type="hidden" name="selected_periods" id="bladeSelectedPeriodsInput" value="1" />

        <!-- Duration / Session Selector (Full Day vs By Hours) -->
        <div>
          <label class="block font-bold text-slate-700 mb-1.5 text-xs">
            រយៈពេលសុំច្បាប់ <span class="text-rose-500">*</span>
          </label>
          <div class="grid grid-cols-2 gap-2.5">
            <button
              type="button"
              onclick="selectBladeSession(this, 'full_day')"
              id="bladeBtnFullDay"
              class="blade-session-btn py-2.5 px-3 rounded-2xl text-center border text-xs transition cursor-pointer active:scale-95 flex items-center justify-center gap-2.5 bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-600/25 ring-2 ring-indigo-600/30 font-bold"
            >
              <i class="fa-solid fa-calendar-day text-base"></i>
              <div class="text-left">
                <div class="leading-tight text-xs sm:text-sm">ពេញមួយថ្ងៃ</div>
                <div class="text-[10px] opacity-80">១ ថ្ងៃ ឬច្រើនថ្ងៃ</div>
              </div>
            </button>
            <button
              type="button"
              onclick="selectBladeSession(this, 'hourly')"
              id="bladeBtnHourly"
              class="blade-session-btn py-2.5 px-3 rounded-2xl text-center border text-xs transition cursor-pointer active:scale-95 flex items-center justify-center gap-2.5 bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100 font-medium"
            >
              <i class="fa-solid fa-clock text-base"></i>
              <div class="text-left">
                <div class="leading-tight text-xs sm:text-sm">សុំជាម៉ោង</div>
                <div class="text-[10px] opacity-80">ជ្រើសរើសម៉ោងសិក្សា</div>
              </div>
            </button>
          </div>
        </div>

        <!-- Date Selection Box (Khmer Format DD/MM/YYYY) -->
        <div class="space-y-3 bg-slate-50/80 p-3.5 rounded-2xl border border-slate-200/80">
          <!-- Full Day Date Range (From & To) -->
          <div id="bladeFullDayDateBlock" class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1 text-xs">
                ពីថ្ងៃទី <span class="text-rose-500">*</span>
              </label>
              <div 
                class="relative group cursor-pointer"
                onclick="openBladeDatePicker('bladeStartDate')"
              >
                <input 
                  type="date" 
                  id="bladeStartDate"
                  name="start_date" 
                  value="{{ date('Y-m-d') }}"
                  required
                  onchange="calcBladeDays()"
                  onclick="event.stopPropagation(); openBladeDatePicker('bladeStartDate')"
                  class="custom-date-trigger opacity-0 absolute inset-0 w-full h-full cursor-pointer z-20"
                />
                <div class="w-full px-3 py-2 bg-white border border-slate-200 group-hover:border-indigo-500 group-hover:ring-2 group-hover:ring-indigo-500/20 rounded-xl font-bold text-slate-800 text-xs flex items-center justify-between shadow-2xs transition pointer-events-none">
                  <div class="flex items-center gap-1.5 overflow-hidden">
                    <i class="fa-regular fa-calendar-days text-indigo-600 text-xs flex-shrink-0"></i>
                    <span id="bladeStartDateDisplay" class="tracking-wide text-xs truncate">{{ date('d/m/Y') }}</span>
                  </div>
                  <span class="text-[9px] text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded font-semibold flex-shrink-0">ថ្ងៃ/ខែ/ឆ្នាំ</span>
                </div>
              </div>
              <span id="bladeStartDateKhmerText" class="text-[10px] text-slate-500 mt-1 block font-medium truncate">
                ថ្ងៃទី {{ date('d') }} ខែ {{ date('m') }} ឆ្នាំ {{ date('Y') }}
              </span>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1 text-xs">
                ដល់ថ្ងៃទី <span class="text-rose-500">*</span>
              </label>
              <div 
                class="relative group cursor-pointer"
                onclick="openBladeDatePicker('bladeEndDate')"
              >
                <input 
                  type="date" 
                  id="bladeEndDate"
                  name="end_date" 
                  value="{{ date('Y-m-d') }}"
                  required
                  onchange="calcBladeDays()"
                  onclick="event.stopPropagation(); openBladeDatePicker('bladeEndDate')"
                  class="custom-date-trigger opacity-0 absolute inset-0 w-full h-full cursor-pointer z-20"
                />
                <div class="w-full px-3 py-2 bg-white border border-slate-200 group-hover:border-indigo-500 group-hover:ring-2 group-hover:ring-indigo-500/20 rounded-xl font-bold text-slate-800 text-xs flex items-center justify-between shadow-2xs transition pointer-events-none">
                  <div class="flex items-center gap-1.5 overflow-hidden">
                    <i class="fa-regular fa-calendar-days text-indigo-600 text-xs flex-shrink-0"></i>
                    <span id="bladeEndDateDisplay" class="tracking-wide text-xs truncate">{{ date('d/m/Y') }}</span>
                  </div>
                  <span class="text-[9px] text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded font-semibold flex-shrink-0">ថ្ងៃ/ខែ/ឆ្នាំ</span>
                </div>
              </div>
              <span id="bladeEndDateKhmerText" class="text-[10px] text-slate-500 mt-1 block font-medium truncate">
                ថ្ងៃទី {{ date('d') }} ខែ {{ date('m') }} ឆ្នាំ {{ date('Y') }}
              </span>
            </div>
          </div>

          <!-- Hourly Single Date Block (hidden by default) -->
          <div id="bladeHourlyDateBlock" class="hidden">
            <label class="block font-bold text-slate-700 mb-1 text-xs">
              កាលបរិច្ឆេទសុំច្បាប់ <span class="text-rose-500">*</span>
            </label>
            <div 
              class="relative group cursor-pointer"
              onclick="openBladeDatePicker('bladeHourlyDate')"
            >
              <input 
                type="date" 
                id="bladeHourlyDate"
                value="{{ date('Y-m-d') }}"
                onchange="syncBladeHourlyDate(this.value)"
                onclick="event.stopPropagation(); openBladeDatePicker('bladeHourlyDate')"
                class="custom-date-trigger opacity-0 absolute inset-0 w-full h-full cursor-pointer z-20"
              />
              <div class="w-full px-3.5 py-2.5 bg-white border border-slate-200 group-hover:border-indigo-500 group-hover:ring-2 group-hover:ring-indigo-500/20 rounded-xl font-bold text-slate-800 text-xs flex items-center justify-between shadow-2xs transition pointer-events-none">
                <div class="flex items-center gap-2">
                  <i class="fa-regular fa-calendar-days text-indigo-600 text-sm"></i>
                  <span id="bladeHourlyDateDisplay" class="tracking-wide text-xs sm:text-sm text-slate-900">{{ date('d/m/Y') }}</span>
                </div>
                <span class="text-[10px] text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded font-semibold">ថ្ងៃ/ខែ/ឆ្នាំ</span>
              </div>
            </div>
            <span id="bladeHourlyDateKhmerText" class="text-[10px] text-slate-500 mt-1 block font-medium">
              ថ្ងៃទី {{ date('d') }} ខែ {{ date('m') }} ឆ្នាំ {{ date('Y') }}
            </span>
          </div>

          <!-- Hourly Period Selection Buttons (07:00 - 11:00) -->
          <div id="bladeHourlyPeriodsBlock" class="hidden p-3 bg-indigo-50/70 rounded-2xl border border-indigo-100 space-y-2.5">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-indigo-950 flex items-center gap-1.5">
                <i class="fa-solid fa-graduation-cap text-indigo-600"></i>
                ជ្រើសរើសម៉ោងសិក្សាដែលត្រូវសុំច្បាប់ (០៧:០០ - ១១:០០)៖
              </span>
              <span id="bladeHourlyBadgeCounter" class="text-[11px] font-bold text-indigo-700 bg-white px-2.5 py-0.5 rounded-full border border-indigo-200 shadow-2xs">
                1 ម៉ោង
              </span>
            </div>

            <div class="grid grid-cols-2 gap-2">
              <button
                type="button"
                onclick="toggleBladePeriod('1')"
                id="bladePeriodBtn_1"
                class="blade-period-btn p-2.5 rounded-xl border text-left transition cursor-pointer flex items-center justify-between active:scale-95 bg-indigo-600 text-white border-indigo-600 shadow-xs"
              >
                <div>
                  <div class="font-bold text-xs">ម៉ោងទី ១</div>
                  <div class="text-[10px] font-mono opacity-80 mt-0.5">07:00 - 07:50</div>
                </div>
                <div id="bladePeriodCheck_1" class="w-5 h-5 rounded-full flex items-center justify-center text-xs flex-shrink-0 bg-white text-indigo-600">
                  <i class="fa-solid fa-check text-[10px]"></i>
                </div>
              </button>

              <button
                type="button"
                onclick="toggleBladePeriod('2')"
                id="bladePeriodBtn_2"
                class="blade-period-btn p-2.5 rounded-xl border text-left transition cursor-pointer flex items-center justify-between active:scale-95 bg-white border-indigo-100 text-slate-700 hover:bg-indigo-50/50"
              >
                <div>
                  <div class="font-bold text-xs">ម៉ោងទី ២</div>
                  <div class="text-[10px] font-mono opacity-80 mt-0.5">07:55 - 08:45</div>
                </div>
                <div id="bladePeriodCheck_2" class="w-5 h-5 rounded-full flex items-center justify-center text-xs flex-shrink-0 bg-slate-100 text-slate-400">
                </div>
              </button>

              <button
                type="button"
                onclick="toggleBladePeriod('3')"
                id="bladePeriodBtn_3"
                class="blade-period-btn p-2.5 rounded-xl border text-left transition cursor-pointer flex items-center justify-between active:scale-95 bg-white border-indigo-100 text-slate-700 hover:bg-indigo-50/50"
              >
                <div>
                  <div class="font-bold text-xs">ម៉ោងទី ៣</div>
                  <div class="text-[10px] font-mono opacity-80 mt-0.5">09:15 - 10:05</div>
                </div>
                <div id="bladePeriodCheck_3" class="w-5 h-5 rounded-full flex items-center justify-center text-xs flex-shrink-0 bg-slate-100 text-slate-400">
                </div>
              </button>

              <button
                type="button"
                onclick="toggleBladePeriod('4')"
                id="bladePeriodBtn_4"
                class="blade-period-btn p-2.5 rounded-xl border text-left transition cursor-pointer flex items-center justify-between active:scale-95 bg-white border-indigo-100 text-slate-700 hover:bg-indigo-50/50"
              >
                <div>
                  <div class="font-bold text-xs">ម៉ោងទី ៤</div>
                  <div class="text-[10px] font-mono opacity-80 mt-0.5">10:10 - 11:00</div>
                </div>
                <div id="bladePeriodCheck_4" class="w-5 h-5 rounded-full flex items-center justify-center text-xs flex-shrink-0 bg-slate-100 text-slate-400">
                </div>
              </button>
            </div>
          </div>

          <!-- Date Confirmation & Calculated Total Badge -->
          <div class="bg-indigo-50/80 border border-indigo-100 px-3.5 py-2.5 rounded-xl flex items-center justify-between text-indigo-900">
            <div class="flex items-center gap-2.5 overflow-hidden">
              <div id="bladeConfirmationIcon" class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm flex-shrink-0 shadow-2xs">
                <i class="fa-regular fa-calendar-days"></i>
              </div>
              <div class="overflow-hidden">
                <div id="bladeDatePreview" class="text-xs font-bold leading-tight truncate">
                  សុំច្បាប់៖ {{ date('d/m/Y') }}
                </div>
                <div id="bladeSessionPreview" class="text-[10px] text-indigo-700 mt-0.5 truncate font-medium">
                  ពេញមួយថ្ងៃ
                </div>
              </div>
            </div>
            <span id="bladeDaysBadge" class="font-bold text-xs bg-indigo-600 text-white px-3 py-1 rounded-full shadow-xs flex-shrink-0 ml-2">
              1 ថ្ងៃ
            </span>
          </div>
        </div>

        <!-- Phone Number -->
        <div>
          <label class="block font-bold text-slate-700 mb-1 text-xs">
            លេខទូរស័ព្ទអាណាព្យាបាល / សិស្ស <span class="text-rose-500">*</span>
          </label>
          <input 
            type="tel" 
            name="phone"
            value="{{ $prefillPhone }}"
            placeholder="ឧទាហរណ៍៖ 012 345 678" 
            required
            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
          />
        </div>

        <!-- Reason / Details -->
        <div>
          <label class="block font-bold text-slate-700 mb-1 text-xs">
            មូលហេតុនៃការសុំច្បាប់ <span class="text-rose-500">*</span>
          </label>
          <textarea 
            name="reason" 
            id="bladeReason"
            rows="3" 
            required
            placeholder="សូមបញ្ជាក់មូលហេតុលម្អិត..."
            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-normal text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 resize-none"
          ></textarea>
        </div>

        <!-- 3. Attachment: Custom Khmer Dropzone -->
        <div>
          <label class="block font-bold text-slate-700 mb-1.5 text-xs">
            ឯកសារភ្ជាប់ (បើមាន៖ លិខិតវេជ្ជបញ្ជា ឬលិខិតសុំច្បាប់)
          </label>

          <input 
            type="file" 
            id="bladeFileInput"
            name="attachment" 
            accept="image/jpeg,image/png,image/webp,application/pdf"
            onchange="handleBladeFileInput(event)"
            class="hidden"
          />

          <!-- Empty State: Modern Dashed Upload Dropzone -->
          <div 
            id="bladeDropzoneEmpty"
            onclick="document.getElementById('bladeFileInput').click()"
            class="border-2 border-dashed border-indigo-200 hover:border-indigo-500 rounded-2xl p-4 sm:p-5 bg-indigo-50/40 hover:bg-indigo-50/70 text-center cursor-pointer transition flex flex-col items-center justify-center gap-2 group"
          >
            <div class="w-11 h-11 rounded-2xl bg-indigo-100 group-hover:bg-indigo-200 text-indigo-600 flex items-center justify-center text-xl transition shadow-xs">
              <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
            <div>
              <div class="text-xs font-bold text-indigo-950 group-hover:text-indigo-700">
                ចុចទីនេះដើម្បីជ្រើសរើសរូបភាព ឬថតរូបលិខិតសុំច្បាប់
              </div>
              <div class="text-[10px] text-slate-500 mt-0.5">
                គាំទ្រឯកសារ JPG, PNG ឬ PDF (អតិបរមា 5MB)
              </div>
            </div>
          </div>

          <!-- File Selected Preview Card with Remove Button -->
          <div 
            id="bladeDropzonePreview"
            class="hidden border border-indigo-200 rounded-2xl p-3 bg-white shadow-2xs flex items-center justify-between gap-3"
          >
            <div class="flex items-center gap-3 overflow-hidden">
              <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                <img id="bladeImgPreview" src="" alt="Preview" class="hidden w-full h-full object-cover" />
                <i id="bladePdfPreview" class="fa-solid fa-file-pdf text-red-500 text-2xl"></i>
              </div>
              <div class="min-w-0">
                <div id="bladeFileName" class="text-xs font-bold text-slate-800 truncate">
                  filename.jpg
                </div>
                <div id="bladeFileSize" class="text-[10px] text-slate-400 font-mono mt-0.5">
                  0 KB
                </div>
              </div>
            </div>

            <button 
              type="button" 
              onclick="removeBladeSelectedFile()" 
              class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-xs font-semibold flex items-center gap-1 transition flex-shrink-0 cursor-pointer active:scale-95"
            >
              <i class="fa-solid fa-trash-can text-[11px]"></i>
              <span>លុបចេញ</span>
            </button>
          </div>
        </div>

      </div>

      <!-- 4. Sticky Full-Width Footer Action Buttons (1:2 ratio) -->
      <div class="p-4 bg-slate-50 border-t border-slate-100 flex-shrink-0 sticky bottom-0 z-20">
        <div class="grid grid-cols-3 gap-3 w-full">
          <button 
            type="button" 
            onclick="closeLeaveRequestModal()"
            class="col-span-1 px-3 py-3 rounded-2xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold transition text-xs sm:text-sm text-center cursor-pointer active:scale-95"
          >
            បោះបង់
          </button>
          <button 
            type="submit" 
            id="bladeSubmitBtn"
            class="col-span-2 px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-2xl font-bold shadow-md shadow-blue-600/25 transition text-xs sm:text-sm flex items-center justify-center gap-2 disabled:opacity-50 cursor-pointer active:scale-95"
          >
            <span>✓ បញ្ជូនសំណើសុំច្បាប់</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  function openLeaveRequestModal() {
    const modal = document.getElementById('bladeLeaveModal');
    if (modal) {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }
  }

  function closeLeaveRequestModal() {
    const modal = document.getElementById('bladeLeaveModal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
    removeBladeSelectedFile();
  }

  const bladeMorningPeriods = [
    { id: '1', name: 'ម៉ោងទី ១', time: '07:00 - 07:50' },
    { id: '2', name: 'ម៉ោងទី ២', time: '07:55 - 08:45' },
    { id: '3', name: 'ម៉ោងទី ៣', time: '09:15 - 10:05' },
    { id: '4', name: 'ម៉ោងទី ៤', time: '10:10 - 11:00' },
  ];

  let selectedBladePeriods = ['1'];
  let currentBladeSession = 'full_day';

  const kmMonthsList = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
  function toKmDigits(num) {
    const kmNums = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
    return String(num).replace(/[0-9]/g, w => kmNums[+w]);
  }
  function formatKmDateString(dStr) {
    if (!dStr) return '';
    const parts = dStr.split('-');
    if (parts.length === 3) {
      const y = parseInt(parts[0], 10);
      const m = parseInt(parts[1], 10);
      const d = parseInt(parts[2], 10);
      return `ថ្ងៃទី ${toKmDigits(String(d).padStart(2, '0'))} ខែ${kmMonthsList[m - 1] || m} ឆ្នាំ${toKmDigits(y)}`;
    }
    return dStr;
  }
  function formatDmy(dStr) {
    if (!dStr) return '';
    const parts = dStr.split('-');
    return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : dStr;
  }

  function getBladePeriodLabels() {
    const selected = bladeMorningPeriods.filter(p => selectedBladePeriods.includes(p.id));
    if (selected.length === 0) return 'ម៉ោងទី ១';
    const names = selected.map(p => p.name).join(', ');
    const firstTime = selected[0].time.split(' - ')[0];
    const lastTime = selected[selected.length - 1].time.split(' - ')[1];
    return `${names} (${firstTime} - ${lastTime})`;
  }

  function selectBladeSession(btn, val) {
    currentBladeSession = val;
    document.getElementById('bladeSessionInput').value = val;

    const btnFull = document.getElementById('bladeBtnFullDay');
    const btnHour = document.getElementById('bladeBtnHourly');
    const fullBlock = document.getElementById('bladeFullDayDateBlock');
    const hourDateBlock = document.getElementById('bladeHourlyDateBlock');
    const hourPeriodsBlock = document.getElementById('bladeHourlyPeriodsBlock');
    const iconContainer = document.getElementById('bladeConfirmationIcon');

    if (val === 'full_day') {
      btnFull.className = 'blade-session-btn py-2.5 px-3 rounded-2xl text-center border text-xs transition cursor-pointer active:scale-95 flex items-center justify-center gap-2.5 bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-600/25 ring-2 ring-indigo-600/30 font-bold';
      btnHour.className = 'blade-session-btn py-2.5 px-3 rounded-2xl text-center border text-xs transition cursor-pointer active:scale-95 flex items-center justify-center gap-2.5 bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100 font-medium';
      fullBlock.classList.remove('hidden');
      hourDateBlock.classList.add('hidden');
      hourPeriodsBlock.classList.add('hidden');
      iconContainer.innerHTML = '<i class="fa-regular fa-calendar-days"></i>';
    } else {
      btnHour.className = 'blade-session-btn py-2.5 px-3 rounded-2xl text-center border text-xs transition cursor-pointer active:scale-95 flex items-center justify-center gap-2.5 bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-600/25 ring-2 ring-indigo-600/30 font-bold';
      btnFull.className = 'blade-session-btn py-2.5 px-3 rounded-2xl text-center border text-xs transition cursor-pointer active:scale-95 flex items-center justify-center gap-2.5 bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100 font-medium';
      fullBlock.classList.add('hidden');
      hourDateBlock.classList.remove('hidden');
      hourPeriodsBlock.classList.remove('hidden');
      iconContainer.innerHTML = '<i class="fa-regular fa-clock"></i>';
    }

    calcBladeDays();
  }

  function syncBladeHourlyDate(val) {
    document.getElementById('bladeStartDate').value = val;
    document.getElementById('bladeEndDate').value = val;
    calcBladeDays();
  }

  function toggleBladePeriod(pId) {
    const idx = selectedBladePeriods.indexOf(pId);
    if (idx > -1) {
      if (selectedBladePeriods.length > 1) {
        selectedBladePeriods.splice(idx, 1);
      }
    } else {
      selectedBladePeriods.push(pId);
      selectedBladePeriods.sort();
    }

    document.getElementById('bladeSelectedPeriodsInput').value = selectedBladePeriods.join(',');
    document.getElementById('bladeHourlyBadgeCounter').innerText = selectedBladePeriods.length + ' ម៉ោង';

    bladeMorningPeriods.forEach(p => {
      const btn = document.getElementById(`bladePeriodBtn_${p.id}`);
      const check = document.getElementById(`bladePeriodCheck_${p.id}`);
      if (selectedBladePeriods.includes(p.id)) {
        btn.className = 'blade-period-btn p-2.5 rounded-xl border text-left transition cursor-pointer flex items-center justify-between active:scale-95 bg-indigo-600 text-white border-indigo-600 shadow-xs';
        check.className = 'w-5 h-5 rounded-full flex items-center justify-center text-xs flex-shrink-0 bg-white text-indigo-600';
        check.innerHTML = '<i class="fa-solid fa-check text-[10px]"></i>';
      } else {
        btn.className = 'blade-period-btn p-2.5 rounded-xl border text-left transition cursor-pointer flex items-center justify-between active:scale-95 bg-white border-indigo-100 text-slate-700 hover:bg-indigo-50/50';
        check.className = 'w-5 h-5 rounded-full flex items-center justify-center text-xs flex-shrink-0 bg-slate-100 text-slate-400';
        check.innerHTML = '';
      }
    });

    calcBladeDays();
  }

  function calcBladeDays() {
    const startStr = document.getElementById('bladeStartDate').value;
    const endStr = document.getElementById('bladeEndDate').value;
    if (!startStr || !endStr) return;

    // Update displays
    document.getElementById('bladeStartDateDisplay').innerText = formatDmy(startStr);
    document.getElementById('bladeStartDateKhmerText').innerText = formatKmDateString(startStr);
    document.getElementById('bladeEndDateDisplay').innerText = formatDmy(endStr);
    document.getElementById('bladeEndDateKhmerText').innerText = formatKmDateString(endStr);
    document.getElementById('bladeHourlyDateDisplay').innerText = formatDmy(startStr);
    document.getElementById('bladeHourlyDateKhmerText').innerText = formatKmDateString(startStr);

    if (currentBladeSession === 'hourly') {
      document.getElementById('bladeDaysBadge').innerText = selectedBladePeriods.length + ' ម៉ោង';
      document.getElementById('bladeDatePreview').innerText = `សុំច្បាប់៖ ${formatDmy(startStr)}`;
      document.getElementById('bladeSessionPreview').innerText = `សុំជាម៉ោង៖ ${getBladePeriodLabels()}`;
      return;
    }

    const start = new Date(startStr);
    const end = new Date(endStr);
    if (isNaN(start.getTime()) || isNaN(end.getTime()) || end < start) {
      document.getElementById('bladeDaysBadge').innerText = '1 ថ្ងៃ';
      document.getElementById('bladeDatePreview').innerText = `សុំច្បាប់៖ ${formatDmy(startStr)}`;
      document.getElementById('bladeSessionPreview').innerText = 'ពេញមួយថ្ងៃ';
      return;
    }

    const diffTime = Math.abs(end - start);
    let days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    document.getElementById('bladeDaysBadge').innerText = days + ' ថ្ងៃ';

    const dateText = startStr === endStr ? `សុំច្បាប់៖ ${formatDmy(startStr)}` : `សុំច្បាប់៖ ${formatDmy(startStr)} ដល់ ${formatDmy(endStr)}`;
    document.getElementById('bladeDatePreview').innerText = dateText;
    document.getElementById('bladeSessionPreview').innerText = 'ពេញមួយថ្ងៃ';
  }

  function handleBladeFileInput(e) {
    const file = e.target.files?.[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
      alert('ឯកសារភ្ជាប់មិនត្រូវលើសពី 5MB ឡើយ!');
      e.target.value = '';
      return;
    }

    document.getElementById('bladeFileName').innerText = file.name;
    const sizeStr = file.size < 1024 * 1024 ? (file.size / 1024).toFixed(1) + ' KB' : (file.size / (1024 * 1024)).toFixed(2) + ' MB';
    document.getElementById('bladeFileSize').innerText = sizeStr;

    const img = document.getElementById('bladeImgPreview');
    const pdf = document.getElementById('bladePdfPreview');

    if (file.type.startsWith('image/')) {
      img.src = URL.createObjectURL(file);
      img.classList.remove('hidden');
      pdf.classList.add('hidden');
    } else {
      img.classList.add('hidden');
      pdf.classList.remove('hidden');
    }

    document.getElementById('bladeDropzoneEmpty').classList.add('hidden');
    document.getElementById('bladeDropzonePreview').classList.remove('hidden');
  }

  function removeBladeSelectedFile() {
    const fileInput = document.getElementById('bladeFileInput');
    if (fileInput) fileInput.value = '';
    const img = document.getElementById('bladeImgPreview');
    if (img && img.src) URL.revokeObjectURL(img.src);

    const empty = document.getElementById('bladeDropzoneEmpty');
    const preview = document.getElementById('bladeDropzonePreview');
    if (empty) empty.classList.remove('hidden');
    if (preview) preview.classList.add('hidden');
  }

  async function handleBladeLeaveSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const submitBtn = document.getElementById('bladeSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.innerText = 'កំពុងបញ្ជូន...';

    try {
      const formData = new FormData(form);
      const isHourly = currentBladeSession === 'hourly';
      if (isHourly) {
        const hDate = document.getElementById('bladeHourlyDate').value;
        formData.set('start_date', hDate);
        formData.set('end_date', hDate);
        formData.set('days_count', (selectedBladePeriods.length * 0.25).toFixed(2));
      }
      formData.set('leave_type', 'ច្បាប់');
      const sessionNote = isHourly ? ` [សុំច្បាប់ជាម៉ោង៖ ${getBladePeriodLabels()}]` : ' [ពេញមួយថ្ងៃ]';
      const reasonVal = (document.getElementById('bladeReason')?.value || '').trim();
      formData.set('reason', reasonVal + sessionNote);

      const res = await fetch('/api/leave-requests', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: formData
      });
      const data = await res.json();
      if (res.ok && data.success) {
        alert(data.message || 'សំណើសុំច្បាប់ត្រូវបានបញ្ជូនដោយជោគជ័យ!');
        closeLeaveRequestModal();
        window.location.reload();
      } else {
        alert(data.message || 'មានបញ្ហាក្នុងការបញ្ជូនសំណើសុំច្បាប់!');
      }
    } catch (err) {
      alert('មានបញ្ហាក្នុងការបញ្ជូនសំណើសុំច្បាប់ សូមព្យាយាមម្តងទៀត!');
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerText = '✓ បញ្ជូនសំណើសុំច្បាប់';
    }
  }

  function openBladeDatePicker(id) {
    const el = document.getElementById(id);
    if (!el) return;
    try {
      if (typeof el.showPicker === 'function') {
        el.showPicker();
      } else {
        el.focus();
      }
    } catch (e) {
      el.focus();
    }
  }
</script>

<style>
.custom-date-trigger {
  cursor: pointer;
}
.custom-date-trigger::-webkit-calendar-picker-indicator {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
  opacity: 0;
  cursor: pointer;
}
</style>
@endsection

