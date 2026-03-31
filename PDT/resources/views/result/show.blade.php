@extends('layouts.app')

@section('content')
<div class="container app-content-shell--plain mx-auto p-6">
    <div class="flex items-center justify-between p-4">
        <h1 class="text-xl font-bold text-gray-800">បញ្ជីសិស្ស</h1>
        <a href="{{ route('rank.index', ['className' => $className, 'search' => $search]) }}"
           class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition font-semibold">
            ចំណាត់ថ្នាក់
        </a>
    </div>

    <div class="px-4 pb-4">
        <form method="GET" action="{{ route('result.show', $className) }}" class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <label for="result-search" class="text-sm font-medium text-gray-700">ស្វែងរកសិស្ស</label>
                <input
                    id="result-search"
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="ស្វែងរកតាមឈ្មោះសិស្ស"
                    class="w-full rounded border border-gray-300 px-3 py-2 text-sm sm:w-72"
                >
            </div>
            <div class="flex gap-2">
                <button
                    type="submit"
                    class="rounded bg-slate-700 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    ស្វែងរក
                </button>
                @if($search !== '')
                    <a
                        href="{{ route('result.show', $className) }}"
                        class="rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    >
                        សម្អាត
                    </a>
                @endif
            </div>
        </form>

        @if($search !== '')
            <p class="mt-3 text-sm text-gray-600">
                បង្ហាញលទ្ធផល <strong>{{ $students->count() }}</strong> សម្រាប់ "<strong>{{ $search }}</strong>"។
            </p>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-4 border-b text-left">ល.រ</th>
                    <th class="py-2 px-4 border-b text-left">ឈ្មោះ</th>
                    <th class="py-2 px-4 border-b text-left">ថ្នាក់</th>
                    @foreach($subjectLabels as $label)
                        <th class="py-2 px-4 border-b text-left">{{ $label }}</th>
                    @endforeach
                    <th class="py-2 px-4 border-b text-left">មេគុណ</th>
                    <th class="py-2 px-4 border-b text-left">សរុប</th>
                    <th class="py-2 px-4 border-b text-left">មធ្យមភាគ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $student)
                    @php
                        $subject = $student->latestSubject;
                        $total = $subject ? $subject->totalScore() : 0;
                        $coefficient = $subject ? $subject->coefficientValue() : 1;
                        $average = $subject ? $subject->averageScore() : 0;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="border-b py-2 px-4">{{ $index + 1 }}</td>
                        <td class="border-b py-2 px-4">{{ $student->user->name ?? 'N/A' }}</td>
                        <td class="border-b py-2 px-4">{{ $student->grade }}</td>
                        @foreach(array_keys($subjectLabels) as $field)
                            <td class="border-b py-2 px-4">{{ $subject ? ($subject->{$field} ?? '--') : '--' }}</td>
                        @endforeach
                        <td class="border-b py-2 px-4 font-semibold">{{ $coefficient }}</td>
                        <td class="border-b py-2 px-4 font-bold">{{ number_format($total, 2) }}</td>
                        <td class="border-b py-2 px-4 font-bold">{{ number_format($average, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($subjectLabels) + 6 }}" class="py-4 px-4 text-center text-gray-500">
                            បញ្ជីឈ្មោះសិស្សថ្នាក់ {{ $className }} មិនមានទេ។
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
