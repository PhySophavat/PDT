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
      <a 
        href="{{ route('public.leave-request') }}" 
        target="_blank"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-full shadow-md shadow-indigo-600/20 transition-all hover:shadow-lg hover:shadow-indigo-600/30"
      >
        <i class="fa-solid fa-link"></i>
        <span>🔗 តំណភ្ជាប់សុំច្បាប់</span>
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

  <!-- 4 Stat Cards -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
      <div class="text-xs font-bold text-slate-500">វត្តមានសរុប</div>
      <div class="text-2xl font-extrabold text-indigo-600 mt-1">{{ $presentCount }}</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
      <div class="text-xs font-bold text-slate-500">យឺតសរុប</div>
      <div class="text-2xl font-extrabold text-amber-500 mt-1">{{ $lateCount }}</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
      <div class="text-xs font-bold text-slate-500">អវត្តមានសរុប</div>
      <div class="text-2xl font-extrabold text-rose-600 mt-1">{{ $absentCount }}</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
      <div class="text-xs font-bold text-slate-500">ច្បាប់សរុប</div>
      <div class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $excusedCount }}</div>
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
@endsection

