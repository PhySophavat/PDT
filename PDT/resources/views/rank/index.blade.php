@extends('layouts.app')

@section('content')
<div class="container app-content-shell--plain mx-auto p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2 md:mb-0">សម្រាប់ថ្នាក់ {{ $className }}</h1>
        <a href="{{ url()->previous() }}" class="text-sm text-blue-600 hover:underline flex items-center gap-1">
            ← ត្រឡប់
        </a>
    </div>

    <form method="GET" action="{{ route('rank.index', $className) }}" class="mb-6">
        <label for="month" class="font-medium mr-2">ជ្រើសរើសខែ:</label>
        <select id="month" name="month" onchange="this.form.submit()" class="border px-3 py-1 rounded">
            <option value="0" {{ $selectedMonth === 0 ? 'selected' : '' }}>ទាំងអស់</option>
            @foreach($monthOptions as $monthValue => $monthLabel)
                <option value="{{ $monthValue }}" {{ $selectedMonth === $monthValue ? 'selected' : '' }}>
                    {{ $monthLabel }}
                </option>
            @endforeach
        </select>
    </form>


    <!-- Two-column responsive grid with equal height cards -->
    <div class=" gap-6">
        <!-- Card 1: Student List / Rank Table -->
         <div class="">
            <h2 class="text-xl font-semibold mb-4 border-b pb-2">តារាងសិស្ស</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="py-2 px-3 text-left font-semibold uppercase tracking-wider">ល.រ</th>
                            <th class="py-2 px-3 text-left font-semibold uppercase tracking-wider">ឈ្មោះ</th>
                            <th class="py-2 px-3 text-left font-semibold uppercase tracking-wider">មេគុណ</th>
                            <th class="py-2 px-3 text-left font-semibold uppercase tracking-wider">សរុប</th>
                            <th class="py-2 px-3 text-left font-semibold uppercase tracking-wider">មធ្យមភាគ</th>
                            <th class="py-2 px-3 text-left font-semibold uppercase tracking-wider">ចំណាត់ថ្នាក់</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rankedStudents as $row)
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="py-2 px-3 border-b">{{ $row['student']->id }}</td>
                                <td class="py-2 px-3 border-b">{{ $row['student']->user->name ?? 'N/A' }}</td>
                                <td class="py-2 px-3 border-b">{{ number_format($row['coefficient'], 2) }}</td>
                                <td class="py-2 px-3 border-b font-semibold text-blue-600">{{ number_format($row['total'], 2) }}</td>
                                <td class="py-2 px-3 border-b">{{ number_format($row['average'], 2) }}</td>
                                <td class="py-2 px-3 border-b">{{ $row['rank'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 px-3 text-center text-gray-500">មិនមានសិស្សសម្រាប់ថ្នាក់ {{ $className }} ទេ។</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    

        <!-- Card 2: Extra Info / Summary -->
       <div class=" p-4 flex flex-col">
           <h2 class="text-xl font-semibold mb-4">ព័ត៌មានបន្ថែម</h2>
           <div class="space-y-2 flex-1">
               <p>
                   <strong>ខែដែលបានជ្រើស:</strong>
                   {{ $selectedMonth > 0 ? ($monthOptions[$selectedMonth] ?? 'មិនស្គាល់') : 'ទាំងអស់' }}
               </p>
               <p><strong>ចំនួនសិស្សសរុប:</strong> {{ count($rankedStudents) }}</p>
               @if(!empty($rankedStudents))
                   <p><strong>សិស្សថ្នាក់ 1:</strong> {{ $rankedStudents[0]['student']->user->name ?? 'N/A' }}</p>
                   <p><strong>សិស្សថ្នាក់ចុងក្រោយ:</strong> {{ $rankedStudents[count($rankedStudents)-1]['student']->user->name ?? 'N/A' }}</p>
               @else
                   <p>គ្មានទិន្នន័យសិស្ស</p>
               @endif
           </div>
       </div>
    </div>
</div>
@endsection
