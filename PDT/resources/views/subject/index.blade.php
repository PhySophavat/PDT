@extends('layouts.app')

@section('content')



hi
{{-- <div class="p-10">
    <h1 class="text-2xl font-bold mb-2">
     Scores for {{ $student->user->name ?? 'Unknown Student' }}
</h1>

    <p class="text-gray-600 mb-6">Grade: {{ $student->grade }}</p>

  

    <form action="{{ route('subject.store', $student->id) }}" method="POST">
        @csrf
        <table class="min-w-full border text-left mb-4">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">Subject</th>
                    <th class="px-4 py-2 border">Math</th>
                     <th class="px-4 py-2 border">Khmer</th>
                      <th class="px-4 py-2 border">English</th>
                       <th class="px-4 py-2 border">History</th>
                        <th class="px-4 py-2 border">Geography</th>
                    
                </tr>
            </thead>
            <tbody>
              
            </tbody>
        </table>

        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">Save Scores</button>
        <a href="{{ route('students.index') }}" class="ml-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">← Back</a>
    </form>
</div> --}}
@endsection
