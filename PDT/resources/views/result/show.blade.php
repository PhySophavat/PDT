@extends('layouts.app')
@section('content')
<div class="container mx-auto p-6 bg-white rounded-lg shadow-md">
<div class="flex items-center justify-between p-4 ">
  <h1 class="text-xl font-bold text-gray-800">បញ្ជីសិស្ស</h1>
  <a href="{{ route('rank.index', $className) }}" 
     class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition font-semibold">
    ចំណាត់ថ្នាក់
  </a>
</div>


<table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
    <thead class="bg-gray-100">
        <tr>
            <th class="py-2 px-4 border-b text-left">ល.រ</th>
            <th class="py-2 px-4 border-b text-left">ឈ្មោះ</th>
            <th class="py-2 px-4 border-b text-left">ថ្នាក់</th>
            <th class="py-2 px-4 border-b text-left">គណិតវិទ្យា</th>
            <th class="py-2 px-4 border-b text-left">ភាសាខ្មែរ</th>
            <th class="py-2 px-4 border-b text-left">ភាសាអង់គ្លេស</th>
            <th class="py-2 px-4 border-b text-left">ប្រវត្តិវិទ្យា</th>
            <th class="py-2 px-4 border-b text-left">ភូមិវិទ្យា</th>
            <th class="py-2 px-4 border-b text-left">សរុប</th>
            <th class="py-2 px-4 border-b text-left">មធ្យមភាគ</th>

        </tr>
    </thead>
    <tbody>
        @php
            // Calculate totals for all students and sort by total descending
            $studentsWithTotals = $students->map(function($student) {
                $math = $student->subjects->sum('math');
                $khmer = $student->subjects->sum('khmer');
                $english = $student->subjects->sum('english');
                $history = $student->subjects->sum('history');
                $geography = $student->subjects->sum('geography');
                $total = $math + $khmer + $english + $history + $geography;
                
                return [
                    'student' => $student,
                    'math' => $math,
                    'khmer' => $khmer,
                    'english' => $english,
                    'history' => $history,
                    'geography' => $geography,
                    'total' => $total,
                ];
            })->sortByDesc('total')->values();
            
            $rank = 1;
        @endphp
        
        @forelse($studentsWithTotals as $data)
            @php
                $student = $data['student'];
                $total = $data['total'];
                $countSubjects = 5;
                $average = $countSubjects ? round($total / $countSubjects, 2) : 0;
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="border-b py-2 px-4">{{ $student->id }}</td>
                <td class="border-b py-2 px-4">{{ $student->user->name ?? 'N/A' }}</td>
                <td class="border-b py-2 px-4">{{ $student->grade }}</td>
                <td class="border-b py-2 px-4">{{ $data['math'] }}</td>
                <td class="border-b py-2 px-4">{{ $data['khmer'] }}</td>
                <td class="border-b py-2 px-4">{{ $data['english'] }}</td>
                <td class="border-b py-2 px-4">{{ $data['history'] }}</td>
                <td class="border-b py-2 px-4">{{ $data['geography'] }}</td>
                <td class="border-b py-2 px-4 font-bold">{{ $total }}</td>
                <td class="border-b py-2 px-4 font-bold">{{ $average }}</td>
               
            </tr>
        @empty
            <tr>
                <td colspan="11" class="py-4 px-4 text-center text-gray-500"> បញ្ជីឈ្មោះសិស្សថ្នាក់ {{ $className }} មិនមានទេ។</td>
            </tr>
        @endforelse
    </tbody>
</table>
</div>
@endsection