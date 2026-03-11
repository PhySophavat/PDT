@extends('layouts.app')

@section('content')
{{-- <div class="p-10">
    <h1 class="text-2xl font-bold mb-4">Scores for {{ $student->user->name ?? 'Unknown' }}</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <table class="min-w-full border text-left mb-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">Subject</th>
                <th class="px-4 py-2 border">Score</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subjects = ['math', 'khmer', 'english', 'history', 'geography'];
            @endphp
            @foreach($subjects as $subject)
                <tr>
                    <td class="px-4 py-2 border font-semibold">{{ ucfirst($subject) }}</td>
                    <td class="px-4 py-2 border">{{ $student->subject->$subject ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('subject.create', $student->id) }}" class="text-blue-600 hover:underline">Add/Edit Scores</a>
</div> --}}
<table class="min-w-full mt-4 border border-gray-300 bg-white rounded-lg">
    <thead class="bg-gray-100">
        <tr>
            <th class="py-2 px-4 border">Student ID</th>
            <th class="py-2 px-4 border">Name</th>
            <th class="py-2 px-4 border">Math</th>
            <th class="py-2 px-4 border">Khmer</th>
            <th class="py-2 px-4 border">English</th>
            <th class="py-2 px-4 border">History</th>
            <th class="py-2 px-4 border">Geography</th>
            <th class="py-2 px-4 border">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($subjects as $subject)
            <tr>
                <td class="border px-4 py-2">{{ $subject->student->id ?? $subject->student_id }}</td>
                <td class="border px-4 py-2">{{ $subject->student->user->name ?? 'N/A' }}</td>
                <td class="border px-4 py-2">{{ $subject->math }}</td>
                <td class="border px-4 py-2">{{ $subject->khmer }}</td>
                <td class="border px-4 py-2">{{ $subject->english }}</td>
                <td class="border px-4 py-2">{{ $subject->history }}</td>
                <td class="border px-4 py-2">{{ $subject->geography }}</td>
                <td class="border px-4 py-2 text-center">
                    <a href="{{ route('subject.edit', $subject) }}" class="text-blue-600 hover:underline">Edit</a> |
                    <form action="{{ route('subject.destroy', $subject) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Delete this record?')">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="py-2 px-4 text-center">No records found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
