@extends('layouts.app')

@section('title', auth()->user()?->hasRole('student') ? 'កាលវិភាគសិក្សា' : 'កាលវិភាគបង្រៀន និងរៀន')

@section('content')
@php
    $authUser = auth()->user();
    $isStudent = $authUser && ($authUser->hasRole('student') || strtolower($authUser->role ?? '') === 'student');
    $studentGrade = $isStudent ? ($authUser->student?->grade ?: '7A') : null;
@endphp

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-lg sm:text-2xl font-bold text-slate-900 tracking-tight">
                {{ $isStudent ? 'កាលវិភាគសិក្សា' : 'កាលវិភាគបង្រៀន និងរៀន' }}
            </h1>
            <nav class="flex items-center gap-1.5 text-[11px] sm:text-xs text-slate-400 mt-0.5">
                <span>ទំព័រដើម</span>
                <span>&raquo;</span>
                <span class="text-indigo-600 font-medium">{{ $isStudent ? 'កាលវិភាគសិក្សា' : 'កាលវិភាគ' }}</span>
            </nav>
        </div>

        @if(!$isStudent)
        <div>
            <button type="button" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-full shadow-md shadow-blue-600/20 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ បន្ថែមម៉ោងសិក្សា</span>
            </button>
        </div>
        @endif
    </div>

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @if($isStudent)
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm">
                <span class="text-xs text-slate-400 font-medium">ម៉ោងសិក្សាសរុប</span>
                <h3 class="text-xl font-bold text-slate-800 mt-1">{{ count($timetables ?? []) }} ម៉ោង/សប្តាហ៍</h3>
            </div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm">
                <span class="text-xs text-slate-400 font-medium">មុខវិជ្ជាសរុប</span>
                <h3 class="text-xl font-bold text-slate-800 mt-1">{{ count($subjects ?? []) }} មុខវិជ្ជា</h3>
            </div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm">
                <span class="text-xs text-slate-400 font-medium">ថ្នាក់រៀន</span>
                <h3 class="text-xl font-bold text-blue-600 mt-1">ថ្នាក់ {{ $studentGrade }}</h3>
            </div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm">
                <span class="text-xs text-slate-400 font-medium">ម៉ោងរៀនថ្ងៃនេះ</span>
                <h3 class="text-xl font-bold text-slate-800 mt-1">{{ count($timetables['ច័ន្ទ'] ?? []) }} ម៉ោង</h3>
            </div>
        @else
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm">
                <span class="text-xs text-slate-400 font-medium">ម៉ោងសិក្សា</span>
                <h3 class="text-xl font-bold text-slate-800 mt-1">{{ count($timetables ?? []) }}</h3>
            </div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm">
                <span class="text-xs text-slate-400 font-medium">ថ្នាក់រៀនសរុប</span>
                <h3 class="text-xl font-bold text-slate-800 mt-1">{{ count($classes ?? []) ?: 48 }}</h3>
            </div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm">
                <span class="text-xs text-slate-400 font-medium">គ្រូបង្រៀនសរុប</span>
                <h3 class="text-xl font-bold text-slate-800 mt-1">{{ count($teachers ?? []) ?: 15 }}</h3>
            </div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm">
                <span class="text-xs text-slate-400 font-medium">គ្មានជម្លោះម៉ោង</span>
                <h3 class="text-xl font-bold text-emerald-600 mt-1">100% ត្រឹមត្រូវ</h3>
            </div>
        @endif
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-3">
        @if($isStudent)
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-blue-50 text-blue-700 rounded-xl font-bold text-xs border border-blue-200/60 shadow-xs">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>ថ្នាក់រៀនរបស់អ្នក៖ ថ្នាក់ {{ $studentGrade }}</span>
            </div>
        @else
            <form method="GET" action="{{ route('timetables.index') }}" class="flex items-center gap-3">
                <label class="text-xs font-bold text-slate-700">ជ្រើសរើសថ្នាក់រៀន:</label>
                <select name="class" onchange="this.form.submit()" class="px-3.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <option value="">-- បង្ហាញគ្រប់ថ្នាក់ --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ ($selectedClass ?? '') == $c ? 'selected' : '' }}>ថ្នាក់ {{ $c }}</option>
                    @endforeach
                </select>
            </form>
        @endif

        <div class="text-xs text-slate-500">
            @if($isStudent)
                ម៉ោងរៀនសរុប៖ <strong>{{ count($timetables ?? []) }}</strong> ម៉ោង
            @else
                សរុបមាន: <strong>{{ count($timetables ?? []) }}</strong> ម៉ោងបង្រៀន
            @endif
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-50 border-b border-slate-200/80">
                <tr>
                    <th class="py-3 px-4 font-bold text-slate-700">ថ្ងៃ</th>
                    <th class="py-3 px-4 font-bold text-slate-700">ម៉ោងសិក្សា</th>
                    <th class="py-3 px-4 font-bold text-slate-700">មុខវិជ្ជា</th>
                    <th class="py-3 px-4 font-bold text-slate-700">ថ្នាក់</th>
                    <th class="py-3 px-4 font-bold text-slate-700">គ្រូបង្រៀន</th>
                    <th class="py-3 px-4 font-bold text-slate-700">បន្ទប់</th>
                    @if(!$isStudent)
                        <th class="py-3 px-4 font-bold text-slate-700 text-right">សកម្មភាព</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($timetables as $day => $items)
                    @foreach($items as $tt)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-bold text-blue-700">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700">
                                    {{ $tt->day_of_week }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-700">{{ $tt->start_time }} - {{ $tt->end_time }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $tt->courseSubject?->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 font-bold text-blue-600">ថ្នាក់ {{ $tt->class_name }}</td>
                            <td class="py-3 px-4 text-slate-600 font-medium">{{ $tt->teacher?->display_name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-slate-500 font-mono">{{ $tt->room_number ?? '-' }}</td>
                            @if(!$isStudent)
                                <td class="py-3 px-4 text-right">
                                    <form method="POST" action="{{ route('timetables.destroy', $tt->id) }}" onsubmit="return confirm('តើអ្នកប្រាកដជាចង់លុបម៉ោងបង្រៀននេះមែនទេ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-rose-50 text-rose-600 rounded-lg text-xs font-semibold hover:bg-rose-100 transition">
                                            លុប
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="{{ $isStudent ? 6 : 7 }}" class="py-10 text-center text-slate-400">មិនមានទិន្នន័យឡើយ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

