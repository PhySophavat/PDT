@extends('layouts.app')

@section('content')
<div class="p-10">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                បញ្ចូលពិន្ទុសិស្ស {{ $student->user->name ?? $student->name ?? 'សិស្សមិនបានកំណត់' }}
            </h1>
            <p class="text-gray-600 mt-2">
                <i class="bi bi-building"></i> ថ្នាក់: <strong>{{ $student->grade }}</strong>
                <span class="mx-2">|</span>
                <i class="bi bi-envelope"></i> {{ $student->user->email ?? 'មិនមាន' }}
            </p>
        </div>

        @if(Auth::user()->hasRole('admin'))
            <a href="{{ route('user.permissions.edit', $student->user->id) }}" 
               class="inline-flex items-center text-white bg-purple-600 px-4 py-2 rounded-lg font-medium hover:bg-purple-700 transition shadow">
                <i class="bi bi-shield-check mr-2"></i> គ្រប់គ្រងសិទ្ធិ
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4 border border-green-300 flex items-center">
            <i class="bi bi-check-circle-fill mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4 border border-red-300">
            <i class="bi bi-exclamation-triangle-fill mr-2"></i>
            <strong>មានបញ្ហា:</strong>
            <ul class="mt-2 ml-6 list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(Auth::user()->hasPermission('add-scores') || Auth::user()->hasRole('teacher') || Auth::user()->hasRole('admin'))
        @php
            $subject = $student->latestSubject;
        @endphp

        <form action="{{ route('subject.store', $student->id) }}" method="POST">
            @csrf
            
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">ឈ្មោះសិស្ស</th>
                                <th class="px-4 py-3 text-center font-semibold">គណិតវិទ្យា</th>
                                <th class="px-4 py-3 text-center font-semibold">ភាសាខ្មែរ</th>
                                <th class="px-4 py-3 text-center font-semibold">ភាសាអង់គ្លេស</th>
                                <th class="px-4 py-3 text-center font-semibold">ប្រវត្តិសាស្ត្រ</th>
                                <th class="px-4 py-3 text-center font-semibold">ភូមិសាស្ត្រ</th>
                                <th class="px-4 py-3 text-center font-semibold">គីមីវិទ្យា</th>
                                <th class="px-4 py-3 text-center font-semibold">រូបវិទ្យា</th>
                                <th class="px-4 py-3 text-center font-semibold">ជីវវិទ្យា</th>
                                <th class="px-4 py-3 text-center font-semibold">សីលធម៌</th>
                                <th class="px-4 py-3 text-center font-semibold">កីឡា</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-gray-50 hover:bg-gray-100 transition">
                                <td class="px-4 py-3 border-b font-semibold text-gray-800">
                                    {{ $student->user->name ?? 'មិនមាន' }}
                                </td>

                                {{-- Math --}}
                                @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('subject-math'))
                                    <td class="px-4 py-3 border-b">
                                        <input type="number" name="math" value="{{ old('math', $subject->math ?? '') }}" min="0" max="100" step="0.01"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                            placeholder="0-100">
                                    </td>
                                @else
                                    <td class="text-center text-gray-400">—</td>
                                @endif

                                {{-- Khmer --}}
                                @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('subject-khmer'))
                                    <td class="px-4 py-3 border-b">
                                        <input type="number" name="khmer" value="{{ old('khmer', $subject->khmer ?? '') }}" min="0" max="100" step="0.01"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                            placeholder="0-100">
                                    </td>
                                @else
                                    <td class="text-center text-gray-400">—</td>
                                @endif

                                {{-- English --}}
                                @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('subject-english'))
                                    <td class="px-4 py-3 border-b">
                                        <input type="number" name="english" value="{{ old('english', $subject->english ?? '') }}" min="0" max="100" step="0.01"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                            placeholder="0-100">
                                    </td>
                                @else
                                    <td class="text-center text-gray-400">—</td>
                                @endif

                                {{-- History --}}
                                @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('subject-history'))
                                    <td class="px-4 py-3 border-b">
                                        <input type="number" name="history" value="{{ old('history', $subject->history ?? '') }}" min="0" max="100" step="0.01"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                            placeholder="0-100">
                                    </td>
                                @else
                                    <td class="text-center text-gray-400">—</td>
                                @endif

                                {{-- Geography --}}
                                @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('subject-geography'))
                                    <td class="px-4 py-3 border-b">
                                        <input type="number" name="geography" value="{{ old('geography', $subject->geography ?? '') }}" min="0" max="100" step="0.01"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                            placeholder="0-100">
                                    </td>
                                @else
                                    <td class="text-center text-gray-400">—</td>
                                @endif

                                {{-- Chemistry --}}
                                @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('subject-chemistry'))
                                    <td class="px-4 py-3 border-b">
                                        <input type="number" name="chemistry" value="{{ old('chemistry', $subject->chemistry ?? '') }}" min="0" max="100" step="0.01"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                            placeholder="0-100">
                                    </td>
                                @else
                                    <td class="text-center text-gray-400">—</td>
                                @endif

                                {{-- Physics --}}
                                @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('subject-physics'))
                                    <td class="px-4 py-3 border-b">
                                        <input type="number" name="physics" value="{{ old('physics', $subject->physics ?? '') }}" min="0" max="100" step="0.01"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                            placeholder="0-100">
                                    </td>
                                @else
                                    <td class="text-center text-gray-400">—</td>
                                @endif

                                {{-- Biology --}}
                                @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('subject-biology'))
                                    <td class="px-4 py-3 border-b">
                                        <input type="number" name="biology" value="{{ old('biology', $subject->biology ?? '') }}" min="0" max="100" step="0.01"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                            placeholder="0-100">
                                    </td>
                                @else
                                    <td class="text-center text-gray-400">—</td>
                                @endif

                                {{-- Morality --}}
                                @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('subject-morality'))
                                    <td class="px-4 py-3 border-b">
                                        <input type="number" name="morality" value="{{ old('morality', $subject->morality ?? '') }}" min="0" max="100" step="0.01"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                            placeholder="0-100">
                                    </td>
                                @else
                                    <td class="text-center text-gray-400">—</td>
                                @endif

                                {{-- Sport --}}
                                @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('subject-sport'))
                                    <td class="px-4 py-3 border-b">
                                        <input type="number" name="sport" value="{{ old('sport', $subject->sport ?? '') }}" min="0" max="100" step="0.01"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                            placeholder="0-100">
                                    </td>
                                @else
                                    <td class="text-center text-gray-400">—</td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" 
                        class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg">
                    <i class="bi bi-save mr-2"></i> រក្សាទុកពិន្ទុ
                </button>
                <a href="{{ route('students.index') }}" 
                   class="inline-flex items-center bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-600 transition shadow-lg">
                    <i class="bi bi-arrow-left mr-2"></i> ត្រលប់ក្រោយ
                </a>
            </div>
        </form>
    @else
        <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 p-6 rounded-lg">
            <div class="flex items-center">
                <i class="bi bi-exclamation-triangle-fill text-3xl mr-4"></i>
                <div>
                    <h3 class="font-bold text-lg mb-2">មិនមានសិទ្ធិចូលប្រើប្រាស់</h3>
                    <p>អ្នកមិនមានសិទ្ធិក្នុងការបញ្ចូលពិន្ទុសិស្សទេ។ សូមទាក់ទងអ្នកគ្រប់គ្រងប្រព័ន្ធ។</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
