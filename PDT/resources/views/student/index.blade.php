@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 bg-white rounded-lg shadow-md">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 ">
            គ្រប់គ្រងសិស្ស
        </h1>

        @if(Auth::user()->hasRole('admin'))
            <a href="{{ route('student.create') }}" 
               class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition font-semibold">
                + បន្ថែមសិស្ស
            </a>
        @endif
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 border border-green-300 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-green-700"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Student Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 border-b font-medium text-gray-700 text-left">ល.រ</th>
                    <th class="px-4 py-3 border-b font-medium text-gray-700 text-left">ឈ្មោះ</th>
                    <th class="px-4 py-3 border-b font-medium text-gray-700 text-left">អ៊ីម៉ែល</th>
                    <th class="px-4 py-3 border-b font-medium text-gray-700 text-left">ថ្នាក់</th>
                    <th class="px-4 py-3 border-b font-medium text-gray-700 text-left">លេខទូរស័ព្ទ</th>
                    <th class="px-4 py-3 border-b font-medium text-gray-700 text-center">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $student)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2 border-b">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 border-b font-medium">{{ $student->user->name ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">{{ $student->user->email ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">{{ $student->grade ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">{{ $student->phone ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">
                            <div class="flex flex-wrap justify-center gap-2">
                                @if(Auth::user()->hasRole('admin'))
                                    <a href="{{ route('user.permissions.edit', $student->user->id) }}" 
                                       class=" text-sky-600 font-medium hover:underline mr2">
                                        ផ្តល់សិទ្ធិ
                                    </a>
                                @endif

                                @if(Auth::user()->hasPermission('add-scores') || Auth::user()->hasRole('teacher'))
                                    <a href="{{ route('subject.create', $student->id) }}" 
                                       class="text-blue-600 font-medium hover:underline mr2">
                                        បញ្ចូលពិន្ទុ
                                    </a>
                                @endif

                                @if(Auth::user()->hasRole('admin'))
                                    <a href="{{ route('student.edit', $student) }}" 
                                       class=" text-green-600 font-medium hover:underline mr2">
                                        កែប្រែ
                                    </a>

                                    <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline"
                                          onsubmit="return confirm('តើអ្នកចង់លុបសិស្ស {{ $student->user->name }} មែនទេ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class=" text-red-600 font-medium hover:underline mr2">
                                            លុប
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            <i class="bi bi-inbox text-3xl mb-2 block"></i>
                            មិនមានសិស្សនៅឡើយទេ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($students->count() > 0)
        <div class="mt-4 text-gray-600 text-sm">
            សរុប: <strong>{{ $students->count() }}</strong> នាក់
        </div>
    @endif
</div>
@endsection
